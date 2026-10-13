<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CharacteristicNature;
use App\Http\Controllers\Controller;
use App\Models\Characteristic;
use App\Models\CharacteristicConsumable;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicProfile;
use App\Models\CharacteristicSpell;
use App\Models\Scrapping\ScrappingEntityMapping;
use App\Models\Scrapping\ScrappingEntityMappingTarget;
use App\Models\Type\ItemType;
use App\Services\Characteristic\Admin\CharacteristicShowPayloadBuilder;
use App\Services\Characteristic\CharacteristicColorCssGenerator;
use App\Services\Characteristic\Conversion\ConversionFormulaGenerator;
use App\Services\Characteristic\Conversion\ConversionFunctionRegistry;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Formula\FormulaConfigDecoder;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Norms\NormsGridGenerator;
use App\Services\Media\EntityImageMediaService;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Config\ScrappingMappingService;
use Database\Seeders\Data\CharacteristicPaletteResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Administration des caractéristiques (nouvelle structure : characteristics + characteristic_creature/object/spell).
 */
class CharacteristicController extends Controller
{
    /**
     * La migration `2026_03_27_160000_add_icon_false_to_characteristics_table` ajoute la colonne.
     * Tant qu’elle n’est pas passée, on n’inclut pas `icon_false` dans les requêtes SQL.
     */
    private function characteristicsTableHasIconFalseColumn(): bool
    {
        return Schema::hasColumn('characteristics', 'icon_false');
    }

    private function characteristicsTableHasValueOverridesColumn(): bool
    {
        return Schema::hasColumn('characteristics', 'value_overrides');
    }

    private function characteristicsTableHasHideWhenEmptyColumn(): bool
    {
        return Schema::hasColumn('characteristics', 'hide_when_empty');
    }

    private function characteristicsTableHasHideWhenFalseColumn(): bool
    {
        return Schema::hasColumn('characteristics', 'hide_when_false');
    }

    private function characteristicsTableHasStatusColumn(): bool
    {
        return Schema::hasColumn('characteristics', 'status');
    }

    /**
     * Les selects de palettes envoient une chaîne vide ; {@see Rule::in} ne l’accepte pas. Normalise en null.
     */
    private function normalizeEmptyTailwindPaletteFields(Request $request): void
    {
        foreach (['color'] as $key) {
            if ($request->has($key) && $request->input($key) === '') {
                $request->merge([$key => null]);
            }
        }
        $vos = $request->input('value_overrides');
        if (! is_array($vos)) {
            return;
        }
        $normalized = [];
        foreach ($vos as $entry) {
            if (! is_array($entry)) {
                $normalized[] = $entry;

                continue;
            }
            if (array_key_exists('color', $entry) && $entry['color'] === '') {
                $entry['color'] = null;
            }
            $normalized[] = $entry;
        }
        $request->merge(['value_overrides' => $normalized]);
    }

    /** Entités possibles + '*' = toutes les entités du groupe (défaut). */
    private const ENTITIES = ['*', 'monster', 'class', 'npc', 'item', 'panoply', 'spell', 'consumable', 'breed', 'specialization'];

    /** Entités par groupe (pour la création). */
    private const ENTITIES_BY_GROUP = [
        'creature' => ['*', 'monster', 'class', 'npc'],
        'object' => ['*', 'item', 'panoply'],
        'spell' => ['*', 'spell'],
        'consumable' => ['*', 'consumable'],
        'profile' => ['*', 'breed', 'specialization', 'monster'],
    ];

    /** Entités DofusDB proposées dans la modal mapping par groupe (objet → chemins). */
    private const DOFUSDB_ENTITIES_BY_GROUP = [
        'creature' => ['monster', 'breed'],
        'object' => ['item', 'panoply'],
        'spell' => ['spell'],
        'consumable' => ['consumable'],
        'profile' => [],
    ];

    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly CharacteristicFormulaService $formulaService,
        private readonly ConversionFormulaGenerator $conversionFormulaGenerator,
        private readonly ConversionFunctionRegistry $conversionFunctionRegistry,
        private readonly CharacteristicColorCssGenerator $colorCssGenerator,
        private readonly CharacteristicShowPayloadBuilder $showPayloadBuilder,
        private readonly ScrappingMappingService $scrappingMappingService,
        private readonly ConfigLoader $configLoader,
        private readonly NormsGridGenerator $normsGridGenerator,
    ) {}

    /**
     * Types d'équipement (item_types) pour multiselect admin : restriction des caractéristiques objet.
     *
     * @return list<array{id: int, name: string, dofusdb_type_id: int}>
     */
    private function itemTypesForAdminSelect(): array
    {
        return ItemType::query()
            ->orderBy('name')
            ->get(['id', 'name', 'dofusdb_type_id'])
            ->map(fn (ItemType $t): array => [
                'id' => (int) $t->id,
                'name' => (string) $t->name,
                'dofusdb_type_id' => (int) $t->dofusdb_type_id,
            ])
            ->all();
    }

    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/characteristics/Index', array_merge(
            $this->adminCharacteristicFormProps(),
            [
                'characteristicsByGroup' => $this->buildCharacteristicsByGroup(),
                'selected' => null,
                'itemTypes' => [],
            ]
        ));
    }

    /**
     * Options communes admin (natures, intrinsèques créature, entités par groupe).
     *
     * @return array<string, mixed>
     */
    private function adminCharacteristicFormProps(): array
    {
        return [
            'entitiesByGroup' => self::ENTITIES_BY_GROUP,
            'groups' => array_keys(self::ENTITIES_BY_GROUP),
            'natures' => array_map(
                static fn (CharacteristicNature $n): array => ['value' => $n->value, 'label' => $n->label()],
                CharacteristicNature::cases()
            ),
            'intrinsicCharacteristicOptions' => $this->intrinsicCharacteristicOptionsForAdmin(),
        ];
    }

    /**
     * Caractéristiques créature intrinsèques (lien bonus_malus / buff_debuff).
     *
     * @return list<array{id: int, key: string, name: string}>
     */
    private function intrinsicCharacteristicOptionsForAdmin(): array
    {
        if (! Schema::hasColumn('characteristics', 'nature')) {
            return [];
        }

        return Characteristic::query()
            ->where('group', 'creature')
            ->where('nature', CharacteristicNature::Intrinsic->value)
            ->whereNull('linked_to_characteristic_id')
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get(['id', 'key', 'name'])
            ->map(fn (Characteristic $c): array => [
                'id' => (int) $c->id,
                'key' => $c->key,
                'name' => $c->name ?? $c->key,
            ])
            ->all();
    }

    /**
     * Formulaire de création d'une caractéristique (nouvelle, liée ou copie).
     */
    public function create(Request $request): InertiaResponse
    {
        $characteristicsByGroup = $this->buildCharacteristicsByGroup();

        $defaultEntityRow = [
            'entity' => '*',
            'db_column' => null,
            'min' => null,
            'max' => null,
            'formula' => null,
            'formula_display' => null,
            'default_value' => null,
            'conversion_formula' => null,
            'conversion_function' => null,
            'conversion_dofus_sample' => null,
            'conversion_krosmoz_sample' => null,
            'conversion_sample_rows' => null,
            'forgemagie_max' => 0,
            'base_price_per_unit' => null,
            'rune_price_per_unit' => null,
            'norms_grid' => null,
            'norms_conditions' => null,
            'norms_description' => null,
            'norms_help_section_id' => null,
            'item_type_ids' => [],
        ];
        $entitiesTemplate = [];
        foreach (self::ENTITIES_BY_GROUP as $group => $entities) {
            $entitiesTemplate[$group] = array_map(fn (string $entity) => array_merge($defaultEntityRow, ['entity' => $entity]), $entities);
        }

        // Caractéristiques éditables (non liées) pour "Lier à" et "Copier depuis"
        $characteristicsForLinkOrCopy = Characteristic::whereNull('linked_to_characteristic_id')
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get(['id', 'key', 'name', 'group'])
            ->map(fn (Characteristic $c) => [
                'id' => $c->id,
                'key' => $c->key,
                'name' => $c->name ?? $c->key,
                'group' => $c->group ?? $this->inferPrimaryGroup($c),
            ])
            ->values()
            ->all();

        // Préremplissage "Copier depuis" : charge la caractéristique source et envoie ses données au front
        $copyFromKey = $request->query('copy_from', '');
        $copyFromCharacteristic = null;
        if (is_string($copyFromKey) && $copyFromKey !== '') {
            $source = Characteristic::where('key', $copyFromKey)->whereNull('linked_to_characteristic_id')->first();
            if ($source !== null) {
                $copyFromCharacteristic = $this->buildSelectedPayloadForCopy($source);
            }
        }

        return Inertia::render('Admin/characteristics/Index', array_merge(
            $this->adminCharacteristicFormProps(),
            [
                'characteristicsByGroup' => $characteristicsByGroup,
                'selected' => null,
                'createMode' => true,
                'entitiesTemplate' => $entitiesTemplate,
                'characteristicsForLinkOrCopy' => $characteristicsForLinkOrCopy,
                'copyFromCharacteristic' => $copyFromCharacteristic,
                'conversionFunctionOptions' => $this->conversionFunctionRegistry->options(),
                'itemTypes' => $this->itemTypesForAdminSelect(),
            ]
        ));
    }

    /**
     * Construit le payload "selected" d'une caractéristique pour préremplir le formulaire "Copier depuis".
     *
     * @return array<string, mixed>
     */
    private function buildSelectedPayloadForCopy(Characteristic $characteristic): array
    {
        $group = $characteristic->group ?? $this->inferPrimaryGroup($characteristic);
        $entitiesForGroup = self::ENTITIES_BY_GROUP[$group] ?? ['*'];
        $entities = [];
        foreach (CharacteristicCreature::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
            $entities[] = $this->groupRowToEntity($row, $characteristic);
        }
        foreach (CharacteristicObject::with('allowedItemTypes')->where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
            $entities[] = $this->groupRowToEntity($row, $characteristic);
        }
        foreach (CharacteristicSpell::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
            $entities[] = $this->groupRowToEntity($row, $characteristic);
        }
        foreach (CharacteristicConsumable::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
            $entities[] = $this->groupRowToEntity($row, $characteristic);
        }
        foreach (CharacteristicProfile::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
            $entities[] = $this->groupRowToEntity($row, $characteristic);
        }
        $entities = array_values(array_filter($entities, fn (array $ent): bool => in_array($ent['entity'] ?? '', $entitiesForGroup, true)));

        $characteristic->loadMissing('intrinsicCharacteristic');
        $nature = $characteristic->natureEnum();

        return [
            'key' => $characteristic->key,
            'name' => $characteristic->name,
            'short_name' => $characteristic->short_name,
            'descriptions' => $characteristic->descriptions,
            'helper' => $characteristic->helper,
            'icon' => $characteristic->icon,
            'icon_false' => $this->characteristicsTableHasIconFalseColumn() ? $characteristic->icon_false : null,
            'color' => $characteristic->color,
            'value_overrides' => $this->characteristicsTableHasValueOverridesColumn() ? $characteristic->value_overrides : null,
            'type' => $characteristic->type,
            'status' => $this->characteristicsTableHasStatusColumn() ? $characteristic->status : Characteristic::STATUS_A_VALIDER,
            'unit' => $characteristic->unit,
            'sort_order' => $characteristic->sort_order,
            'group' => $group,
            'nature' => $nature?->value,
            'intrinsic_characteristic_id' => $characteristic->intrinsic_characteristic_id,
            'entities' => $entities,
        ];
    }

    /**
     * Enregistre une nouvelle caractéristique (table générale + lignes du groupe choisi).
     * La clé est normalisée : si elle ne se termine pas par _creature, _object ou _spell,
     * le suffixe correspondant au groupe choisi est ajouté automatiquement (ex. life_dice → life_dice_creature).
     */
    public function store(Request $request): RedirectResponse
    {
        $createMode = $request->input('create_mode', 'new');
        if ($createMode === 'link') {
            return $this->storeLink($request);
        }

        if (is_array($request->input('entities'))) {
            $request->merge([
                'entities' => $this->normalizeNormsHelpSectionIdsInEntities($request->input('entities')),
            ]);
        }

        $this->normalizeEmptyTailwindPaletteFields($request);

        $data = $request->validate([
            'key' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/'],
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'helper' => 'nullable|string',
            'icon' => 'nullable|string|max:64',
            'icon_false' => 'nullable|string|max:64',
            'color' => ['nullable', 'string', 'max:32', Rule::in(CharacteristicPaletteResolver::ALLOWED_PALETTES)],
            'type' => 'required|in:int,string,array,bool',
            'status' => ['nullable', 'string', Rule::in(Characteristic::STATUSES)],
            'unit' => 'nullable|string|max:64',
            'sort_order' => 'nullable|integer',
            'group' => 'required|in:creature,object,spell,consumable,profile',
            'nature' => ['nullable', 'string', Rule::in(CharacteristicNature::values())],
            'intrinsic_characteristic_id' => 'nullable|integer|exists:characteristics,id',
            'value_overrides' => 'nullable|array',
            'value_overrides.*.value' => 'required',
            'value_overrides.*.icon' => 'nullable|string|max:64',
            'value_overrides.*.color' => ['nullable', 'string', 'max:32', Rule::in(CharacteristicPaletteResolver::ALLOWED_PALETTES)],
            'value_overrides.*.subtitle' => 'nullable|string|max:500',
            'hide_when_empty' => 'nullable|boolean',
            'hide_when_false' => 'nullable|boolean',
            'entities' => 'array',
            'entities.*.entity' => 'required|string|max:32',
            'entities.*.db_column' => 'nullable|string|max:64',
            'entities.*.min' => 'nullable|string|max:512',
            'entities.*.max' => 'nullable|string|max:512',
            'entities.*.formula' => 'nullable|string',
            'entities.*.formula_display' => 'nullable|string',
            'entities.*.default_value' => 'nullable|string|max:512',
            'entities.*.conversion_formula' => 'nullable|string',
            'entities.*.conversion_function' => 'nullable|string|max:64',
            'entities.*.forgemagie_max' => 'nullable|integer',
            'entities.*.base_price_per_unit' => 'nullable|numeric',
            'entities.*.rune_price_per_unit' => 'nullable|numeric',
            'entities.*.item_type_ids' => 'nullable|array',
            'entities.*.item_type_ids.*' => 'integer|exists:item_types,id',
            'entities.*.norms_help_section_id' => [
                'nullable',
                'integer',
                Rule::exists('sections', 'id')->whereNull('deleted_at'),
            ],
        ]);

        $key = $this->normalizeCharacteristicKey(trim($data['key']), $data['group']);
        Validator::make(
            ['key' => $key],
            ['key' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/', 'unique:characteristics,key']]
        )->validate();

        $allowedEntities = self::ENTITIES_BY_GROUP[$data['group']];
        $entities = array_filter($data['entities'] ?? [], function ($ent) use ($allowedEntities) {
            return in_array($ent['entity'] ?? '', $allowedEntities, true);
        });

        $createPayload = [
            'key' => $key,
            'name' => $data['name'],
            'short_name' => $data['short_name'] ?? null,
            'helper' => $data['helper'] ?? null,
            'descriptions' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? null,
            'type' => $data['type'],
            'unit' => $data['unit'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'group' => $data['group'],
        ];
        if ($this->characteristicsTableHasStatusColumn()) {
            $createPayload['status'] = $data['status'] ?? Characteristic::STATUS_A_VALIDER;
        }
        if ($this->characteristicsTableHasIconFalseColumn()) {
            $createPayload['icon_false'] = $data['icon_false'] ?? null;
        }
        if ($this->characteristicsTableHasValueOverridesColumn()) {
            $createPayload['value_overrides'] = $this->parseValueOverridesInput($data['value_overrides'] ?? null);
        }
        if ($this->characteristicsTableHasHideWhenEmptyColumn()) {
            $createPayload['hide_when_empty'] = (bool) ($data['hide_when_empty'] ?? false);
        }
        if ($this->characteristicsTableHasHideWhenFalseColumn()) {
            $createPayload['hide_when_false'] = (bool) ($data['hide_when_false'] ?? false);
        }
        if (Schema::hasColumn('characteristics', 'nature')) {
            $createPayload['nature'] = $data['nature'] ?? null;
            $createPayload['intrinsic_characteristic_id'] = $data['intrinsic_characteristic_id'] ?? null;
        }

        $characteristic = Characteristic::create($createPayload);

        foreach ($entities as $ent) {
            $this->updateGroupRow($characteristic->id, $data['group'], $ent['entity'], $ent);
        }

        $this->getter->clearCache();
        $this->colorCssGenerator->generate();

        return redirect()
            ->route('admin.characteristics.show', $characteristic->key)
            ->with('success', 'Caractéristique créée.');
    }

    /**
     * Crée une caractéristique liée (pas de lignes de groupe, config = maître).
     */
    private function storeLink(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'linked_to_characteristic_id' => ['required', 'integer', 'exists:characteristics,id'],
            'group' => 'required|in:creature,object,spell,consumable,profile',
            'key' => ['nullable', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/'],
        ]);

        $master = Characteristic::findOrFail($data['linked_to_characteristic_id']);
        if ($master->linked_to_characteristic_id !== null) {
            return redirect()->back()->withErrors(['linked_to_characteristic_id' => 'La caractéristique source ne peut pas être elle-même une liée.'])->withInput();
        }

        $key = isset($data['key']) && trim($data['key']) !== ''
            ? $this->normalizeCharacteristicKey(trim($data['key']), $data['group'])
            : $this->deriveLinkedKey($master->key, $data['group']);

        Validator::make(
            ['key' => $key],
            ['key' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/', 'unique:characteristics,key']]
        )->validate();

        $linkPayload = [
            'key' => $key,
            'name' => null,
            'short_name' => null,
            'helper' => null,
            'descriptions' => null,
            'icon' => null,
            'color' => null,
            'type' => $master->type,
            'unit' => null,
            'sort_order' => $master->sort_order,
            'group' => $data['group'],
            'linked_to_characteristic_id' => $master->id,
        ];
        if ($this->characteristicsTableHasStatusColumn()) {
            $linkPayload['status'] = Characteristic::STATUS_A_VALIDER;
        }
        $characteristic = Characteristic::create($linkPayload);

        $this->getter->clearCache();
        $this->colorCssGenerator->generate();

        return redirect()
            ->route('admin.characteristics.show', $characteristic->key)
            ->with('success', 'Caractéristique liée créée. Les paramètres sont ceux de la caractéristique maître.');
    }

    /**
     * Dérive la clé d'une caractéristique liée à partir de la clé maître et du groupe (ex. level_creature + object → level_object).
     */
    private function deriveLinkedKey(string $masterKey, string $group): string
    {
        $suffix = '_'.$group;
        foreach (['_creature', '_object', '_spell', '_consumable', '_profile'] as $s) {
            if (str_ends_with($masterKey, $s)) {
                return substr($masterKey, 0, -strlen($s)).$suffix;
            }
        }

        return $masterKey.$suffix;
    }

    public function show(string $characteristic_key): InertiaResponse
    {
        $characteristic = Characteristic::where('key', $characteristic_key)->first();
        if ($characteristic === null) {
            session()->flash('error', 'Caractéristique introuvable.');

            return Inertia::render('Admin/characteristics/Index', array_merge(
                $this->adminCharacteristicFormProps(),
                [
                    'characteristicsByGroup' => $this->buildCharacteristicsByGroup(),
                    'selected' => null,
                    'itemTypes' => $this->itemTypesForAdminSelect(),
                ]
            ))->with('error', 'Caractéristique introuvable.');
        }

        $payload = $this->showPayloadBuilder->build($characteristic);

        return Inertia::render('Admin/characteristics/Index', array_merge(
            $this->adminCharacteristicFormProps(),
            [
                'characteristicsByGroup' => $this->buildCharacteristicsByGroup(),
                'selected' => $payload['selected'],
                'characteristicsForConvertToLinked' => $payload['characteristicsForConvertToLinked'],
                'scrappingMappingsUsingThis' => $payload['scrappingMappingsUsingThis'],
                'conversionFunctionOptions' => $this->conversionFunctionRegistry->options(),
                'itemTypes' => $this->itemTypesForAdminSelect(),
            ]
        ));
    }

    /**
     * Supprime une caractéristique (et ses lignes de groupe par cascade).
     * Une maître ne peut pas être supprimée tant qu'elle a des caractéristiques liées.
     */
    public function destroy(string $characteristic_key): RedirectResponse
    {
        $characteristic = Characteristic::where('key', $characteristic_key)->withCount('linkedCharacteristics')->first();
        if ($characteristic === null) {
            return redirect()->route('admin.characteristics.index')->with('error', 'Caractéristique introuvable.');
        }
        if ($characteristic->linked_characteristics_count > 0) {
            return redirect()->route('admin.characteristics.index')
                ->with('error', 'Impossible de supprimer une caractéristique maître tant que des caractéristiques y sont liées. Supprimez d\'abord les liées.');
        }
        $characteristic->delete();
        $this->getter->clearCache();
        $this->colorCssGenerator->generate();

        return redirect()->route('admin.characteristics.index')->with('success', 'Caractéristique supprimée.');
    }

    /**
     * Modal mapping : sans entity → liste des entités DofusDB pour le groupe ; avec entity → chemins (from_path) depuis le JSON.
     *
     * @see docs/features/scrapping/README.md
     */
    public function scrappingMappingOptions(Request $request, string $characteristic_key): JsonResponse
    {
        $characteristic = Characteristic::where('key', $characteristic_key)->firstOrFail();
        $group = $this->inferPrimaryGroup($characteristic);
        $entity = (string) $request->query('entity', '');
        $entityIds = self::DOFUSDB_ENTITIES_BY_GROUP[$group] ?? [];

        if ($entity === '') {
            $entities = [];
            foreach ($entityIds as $e) {
                $entities[] = [
                    'id' => $e,
                    'label' => $this->configLoader->getEntityLabel('dofusdb', $e),
                ];
            }

            return response()->json(['entities' => $entities]);
        }

        if (! in_array($entity, $entityIds, true)) {
            return response()->json(['message' => 'Entité invalide pour ce groupe.'], 422);
        }
        try {
            $paths = $this->configLoader->getEntityMappingEntriesFromFile('dofusdb', $entity);
        } catch (\Throwable) {
            return response()->json(['message' => 'Config entité introuvable.'], 422);
        }

        return response()->json(['paths' => $paths]);
    }

    /**
     * Crée (ou met à jour) une règle de mapping depuis un chemin DofusDB et la lie à cette caractéristique.
     * Les targets et formatters viennent du JSON d'entité.
     *
     * @see docs/features/scrapping/README.md
     */
    public function storeScrappingMapping(Request $request, string $characteristic_key): JsonResponse
    {
        $characteristic = Characteristic::where('key', $characteristic_key)->firstOrFail();
        $validated = $request->validate([
            'entity' => 'required|string|max:64',
            'from_path' => 'required|string|max:256',
            'from_key' => 'nullable|string|max:64',
        ]);
        $group = $this->inferPrimaryGroup($characteristic);
        $entityIds = self::DOFUSDB_ENTITIES_BY_GROUP[$group] ?? [];
        if (! in_array($validated['entity'], $entityIds, true)) {
            return response()->json(['message' => 'Entité invalide pour ce groupe.'], 422);
        }
        try {
            $entries = $this->configLoader->getEntityMappingEntriesFromFile('dofusdb', $validated['entity']);
        } catch (\Throwable) {
            return response()->json(['message' => 'Config entité introuvable.'], 422);
        }
        $fromKey = isset($validated['from_key']) && $validated['from_key'] !== '' ? (string) $validated['from_key'] : null;
        $entry = null;
        foreach ($entries as $e) {
            if ($e['path'] !== $validated['from_path']) {
                continue;
            }
            if ($fromKey !== null) {
                if (($e['key'] ?? '') === $fromKey) {
                    $entry = $e;
                    break;
                }

                continue;
            }
            $entry = $e;
            break;
        }
        if ($entry === null) {
            return response()->json(['message' => 'Chemin (ou clé) non trouvé dans la config de l\'entité.'], 422);
        }
        if ($entry['targets'] === []) {
            return response()->json(['message' => 'Ce chemin n\'a pas de cible Krosmoz définie dans la config.'], 422);
        }

        // Contrainte unique (source, entity, mapping_key) : réutiliser la règle existante ou créer
        $mapping = ScrappingEntityMapping::where('source', 'dofusdb')
            ->where('entity', $validated['entity'])
            ->where('mapping_key', $entry['key'])
            ->first();

        if ($mapping !== null) {
            $mapping->update([
                'from_path' => $entry['path'],
                'from_lang_aware' => $entry['langAware'],
                'formatters' => $entry['formatters'],
                'characteristic_id' => $characteristic->id,
            ]);
            $mapping->targets()->delete();
            $mapping->characteristics()->syncWithoutDetaching([$characteristic->id]);
        } else {
            $maxSort = (int) ScrappingEntityMapping::where('source', 'dofusdb')
                ->where('entity', $validated['entity'])
                ->max('sort_order');
            $mapping = ScrappingEntityMapping::create([
                'source' => 'dofusdb',
                'entity' => $validated['entity'],
                'mapping_key' => $entry['key'],
                'from_path' => $entry['path'],
                'from_lang_aware' => $entry['langAware'],
                'characteristic_id' => $characteristic->id,
                'formatters' => $entry['formatters'],
                'sort_order' => $maxSort + 1,
            ]);
            $mapping->characteristics()->attach($characteristic->id);
        }

        foreach ($entry['targets'] as $i => $t) {
            ScrappingEntityMappingTarget::create([
                'scrapping_entity_mapping_id' => $mapping->id,
                'target_model' => $t['model'],
                'target_field' => $t['field'],
                'sort_order' => $i,
            ]);
        }

        $mapping->load('targets');

        return response()->json([
            'success' => true,
            'message' => 'Mapping enregistré.',
            'mapping' => $mapping->toSummaryArray(),
        ]);
    }

    /**
     * Délie une règle de mapping de cette caractéristique (characteristic_id = null).
     *
     * @see docs/features/scrapping/README.md
     */
    public function unlinkScrappingMapping(Request $request, string $characteristic_key): JsonResponse
    {
        $characteristic = Characteristic::where('key', $characteristic_key)->firstOrFail();
        $validated = $request->validate(['mapping_id' => 'required|integer|exists:scrapping_entity_mappings,id']);
        $mapping = ScrappingEntityMapping::findOrFail($validated['mapping_id']);
        $linkedViaPivot = $mapping->characteristics()->where('characteristics.id', $characteristic->id)->exists();
        $linkedViaColumn = $mapping->characteristic_id === (int) $characteristic->id;
        if (! $linkedViaPivot && ! $linkedViaColumn) {
            return response()->json(['message' => 'Cette règle n\'est pas liée à cette caractéristique.'], 422);
        }
        $mapping->characteristics()->detach($characteristic->id);
        if ($linkedViaColumn) {
            $mapping->update(['characteristic_id' => null]);
        }

        return response()->json(['success' => true, 'message' => 'Règle déliée.']);
    }

    /**
     * Infère le groupe principal de la caractéristique (creature, object ou spell) selon les tables qui ont des lignes.
     */
    private function inferPrimaryGroup(Characteristic $characteristic): string
    {
        if (is_string($characteristic->group) && $characteristic->group !== ''
            && in_array($characteristic->group, array_keys(self::ENTITIES_BY_GROUP), true)) {
            return $characteristic->group;
        }
        if (CharacteristicCreature::where('characteristic_id', $characteristic->id)->exists()) {
            return 'creature';
        }
        if (CharacteristicObject::where('characteristic_id', $characteristic->id)->exists()) {
            return 'object';
        }
        if (CharacteristicSpell::where('characteristic_id', $characteristic->id)->exists()) {
            return 'spell';
        }
        if (CharacteristicConsumable::where('characteristic_id', $characteristic->id)->exists()) {
            return 'consumable';
        }
        if (CharacteristicProfile::where('characteristic_id', $characteristic->id)->exists()) {
            return 'profile';
        }

        return 'creature';
    }

    public function formulaPreview(Request $request): JsonResponse
    {
        $characteristicId = $request->query('characteristic_id', '');
        $entity = $request->query('entity', 'resource');
        $variable = $request->query('variable', 'level');
        $formulaOverride = $request->query('formula');

        $def = $this->getter->getDefinition($characteristicId, $entity);
        $formula = $formulaOverride !== null && $formulaOverride !== ''
            ? $formulaOverride
            : ($def['formula'] ?? $def['conversion_formula'] ?? null);
        if ($formula === null || $formula === '') {
            return response()->json(['points' => []]);
        }

        $decoded = FormulaConfigDecoder::decode($formula);
        $axisVar = $variable;
        if ($decoded['type'] === 'table') {
            $axisVar = $decoded['characteristic'];
        }

        $limits = $this->getter->getLimitsByField($axisVar, $entity);
        if ($limits === null) {
            $limits = ['min' => 1, 'max' => 20];
        }
        $min = $limits['min'];
        $max = min($limits['max'], $min + 50);

        $defaults = $this->buildDefaultVariablesForPreview($formula, $decoded, $axisVar, $entity);

        $range = $this->formulaService->evaluateForVariableRange($formula, $axisVar, $min, $max, $defaults);
        $points = [];
        foreach ($range as $x => $y) {
            $points[] = ['x' => $x, 'y' => (int) round($y, 0)];
        }

        return response()->json(['points' => $points]);
    }

    /**
     * Suggère une formule de conversion à partir des échantillons ou des paires (d, k).
     * Body : soit (conversion_dofus_sample + conversion_krosmoz_sample), soit (pairs : [{d, k}, ...]), et curve_type (table|linear|power|shifted_power).
     */
    public function suggestConversionFormula(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conversion_dofus_sample' => 'nullable|array',
            'conversion_dofus_sample.*' => 'numeric',
            'conversion_krosmoz_sample' => 'nullable|array',
            'conversion_krosmoz_sample.*' => 'numeric',
            'pairs' => 'nullable|array',
            'pairs.*.d' => 'required_with:pairs|numeric',
            'pairs.*.k' => 'required_with:pairs|numeric',
            'curve_type' => 'required|in:table,linear,power,shifted_power,exponential,log,polynomial2',
        ]);
        $curveType = $validated['curve_type'];

        if (! empty($validated['pairs'])) {
            $pairs = array_map(function (array $p): array {
                return ['d' => (float) $p['d'], 'k' => (float) $p['k']];
            }, $validated['pairs']);
            $suggestions = $this->conversionFormulaGenerator->suggestFormulasFromPairs($pairs);
        } else {
            $dofusSample = $this->normalizeSampleKeys($validated['conversion_dofus_sample'] ?? []);
            $krosmozSample = $this->normalizeSampleKeys($validated['conversion_krosmoz_sample'] ?? []);
            if ($dofusSample === [] || $krosmozSample === []) {
                return response()->json(
                    ['message' => 'Fournissez soit des paires (d, k), soit conversion_dofus_sample et conversion_krosmoz_sample.'],
                    422
                );
            }
            $suggestions = $this->conversionFormulaGenerator->suggestFormulas($dofusSample, $krosmozSample);
        }

        if ($curveType === 'table') {
            return response()->json(['formula' => $suggestions['table']]);
        }
        $result = $suggestions[$curveType] ?? null;
        if ($result === null) {
            return response()->json(
                ['message' => "Ajustement {$curveType} impossible avec les données fournies."],
                422
            );
        }

        return response()->json([
            'formula' => $result['formula'],
            'r2' => $result['r2'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $sample
     * @return array<int|string, float>
     */
    private function normalizeSampleKeys(array $sample): array
    {
        $out = [];
        foreach ($sample as $k => $v) {
            $key = is_numeric($k) ? (int) $k : $k;
            $out[$key] = (float) $v;
        }

        return $out;
    }

    /**
     * Normalise les échantillons pour la sauvegarde (null ou tableau clé → valeur numérique).
     *
     * @return array<int|string, float>|null
     */
    private function normalizeSampleForSave(mixed $value): ?array
    {
        if ($value === null || $value === []) {
            return null;
        }
        if (! is_array($value)) {
            return null;
        }
        $out = [];
        foreach ($value as $k => $v) {
            if (is_numeric($v)) {
                $out[is_numeric($k) ? (int) $k : (string) $k] = (float) $v;
            }
        }

        return $out ?: null;
    }

    /**
     * Normalise conversion_sample_rows pour la sauvegarde (liste de {dofus_level, dofus_value, krosmoz_level, krosmoz_value}).
     *
     * @return list<array{dofus_level: int|float, dofus_value: int|float, krosmoz_level: int|float, krosmoz_value: int|float}>|null
     */
    private function normalizeSampleRowsForSave(mixed $value): ?array
    {
        if ($value === null || ! is_array($value)) {
            return null;
        }
        $out = [];
        foreach ($value as $row) {
            if (! is_array($row)) {
                continue;
            }
            $dofusLevel = isset($row['dofus_level']) && is_numeric($row['dofus_level']) ? (float) $row['dofus_level'] : null;
            $krosmozLevel = isset($row['krosmoz_level']) && is_numeric($row['krosmoz_level']) ? (float) $row['krosmoz_level'] : null;
            if ($dofusLevel === null || $krosmozLevel === null) {
                continue;
            }
            $dofusValue = isset($row['dofus_value']) && $row['dofus_value'] !== '' && is_numeric($row['dofus_value']) ? (float) $row['dofus_value'] : null;
            $krosmozValue = isset($row['krosmoz_value']) && $row['krosmoz_value'] !== '' && is_numeric($row['krosmoz_value']) ? (float) $row['krosmoz_value'] : null;
            $out[] = [
                'dofus_level' => $dofusLevel,
                'dofus_value' => $dofusValue,
                'krosmoz_level' => $krosmozLevel,
                'krosmoz_value' => $krosmozValue,
            ];
        }

        return $out ?: null;
    }

    /**
     * Dérive un échantillon (niveau → valeur) à partir des lignes, pour compatibilité avec les lecteurs existants.
     *
     * @param  list<array{dofus_level: int|float, dofus_value: int|float, krosmoz_level: int|float, krosmoz_value: int|float}>|null  $rows
     * @return array<int|string, float>|null
     */
    private function deriveSampleFromRows(?array $rows, string $which): ?array
    {
        if ($rows === null || $rows === []) {
            return null;
        }
        $out = [];
        foreach ($rows as $row) {
            if ($which === 'dofus') {
                $key = $row['dofus_level'];
                $val = $row['dofus_value'];
            } else {
                $key = $row['krosmoz_level'];
                $val = $row['krosmoz_value'];
            }
            if ($key !== null && $val !== null && $val !== '' && is_numeric($val)) {
                $out[is_numeric($key) ? (int) $key : (string) $key] = (float) $val;
            }
        }

        return $out ?: null;
    }

    public function update(Request $request, string $characteristic_key): RedirectResponse
    {
        $characteristic = Characteristic::where('key', $characteristic_key)->first();
        if ($characteristic === null) {
            return redirect()->route('admin.characteristics.index')->with('error', 'Caractéristique introuvable.');
        }
        if ($characteristic->isLinked()) {
            return redirect()->route('admin.characteristics.show', $characteristic_key)
                ->with('error', 'Une caractéristique liée ne peut pas être modifiée ici. Modifiez la caractéristique maître.');
        }

        if ($request->boolean('convert_to_linked')) {
            return $this->convertToLinked($request, $characteristic);
        }

        if (is_array($request->input('entities'))) {
            $request->merge([
                'entities' => $this->normalizeNormsHelpSectionIdsInEntities($request->input('entities')),
            ]);
        }

        $this->normalizeEmptyTailwindPaletteFields($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'helper' => 'nullable|string',
            'icon' => 'nullable|string|max:64',
            'icon_false' => 'nullable|string|max:64',
            'color' => ['nullable', 'string', 'max:32', Rule::in(CharacteristicPaletteResolver::ALLOWED_PALETTES)],
            'type' => 'required|in:int,string,array,bool',
            'status' => ['nullable', 'string', Rule::in(Characteristic::STATUSES)],
            'unit' => 'nullable|string|max:64',
            'sort_order' => 'nullable|integer',
            'nature' => ['nullable', 'string', Rule::in(CharacteristicNature::values())],
            'intrinsic_characteristic_id' => 'nullable|integer|exists:characteristics,id',
            'entities' => 'array',
            'entities.*.entity' => 'required|in:'.implode(',', self::ENTITIES),
            'entities.*.db_column' => 'nullable|string|max:64',
            'entities.*.min' => 'nullable|string|max:512',
            'entities.*.max' => 'nullable|string|max:512',
            'entities.*.formula' => 'nullable|string',
            'entities.*.formula_display' => 'nullable|string',
            'entities.*.default_value' => 'nullable|string|max:512',
            'entities.*.conversion_formula' => 'nullable|string',
            'entities.*.conversion_function' => 'nullable|string|max:64',
            'entities.*.conversion_dofus_sample' => 'nullable|array',
            'entities.*.conversion_dofus_sample.*' => 'numeric',
            'entities.*.conversion_krosmoz_sample' => 'nullable|array',
            'entities.*.conversion_krosmoz_sample.*' => 'numeric',
            'entities.*.conversion_sample_rows' => 'nullable|array',
            'entities.*.conversion_sample_rows.*.dofus_level' => 'nullable|numeric',
            'entities.*.conversion_sample_rows.*.dofus_value' => 'nullable|numeric',
            'entities.*.conversion_sample_rows.*.krosmoz_level' => 'nullable|numeric',
            'entities.*.conversion_sample_rows.*.krosmoz_value' => 'nullable|numeric',
            'entities.*.forgemagie_max' => 'nullable|integer',
            'entities.*.base_price_per_unit' => 'nullable|numeric',
            'entities.*.rune_price_per_unit' => 'nullable|numeric',
            'entities.*.norms_grid' => 'nullable|array',
            'entities.*.norms_grid.very_weak' => 'nullable|array|size:20',
            'entities.*.norms_grid.weak' => 'nullable|array|size:20',
            'entities.*.norms_grid.neutral' => 'nullable|array|size:20',
            'entities.*.norms_grid.strong' => 'nullable|array|size:20',
            'entities.*.norms_grid.very_strong' => 'nullable|array|size:20',
            'entities.*.norms_formula' => 'nullable|string',
            'entities.*.norms_conditions' => 'nullable|array',
            'entities.*.norms_conditions.*.characteristic_key' => 'required|string|max:128',
            'entities.*.norms_conditions.*.operator' => 'required|in:>,>=,=,<=,<',
            'entities.*.norms_conditions.*.value' => 'required|numeric',
            'entities.*.norms_conditions.*.target' => 'required|in:power,level',
            'entities.*.norms_conditions.*.modifier' => 'required|integer',
            'entities.*.norms_conditions.*.comment' => 'nullable|string|max:500',
            'entities.*.norms_description' => 'nullable|string|max:2000',
            'entities.*.norms_help_section_id' => [
                'nullable',
                'integer',
                Rule::exists('sections', 'id')->whereNull('deleted_at'),
            ],
            'entities.*.item_type_ids' => 'nullable|array',
            'entities.*.item_type_ids.*' => 'integer|exists:item_types,id',
            'entity_override_keys' => 'nullable|array',
            'entity_override_keys.*' => 'string|max:32',
            'value_overrides' => 'nullable|array',
            'value_overrides.*.value' => 'required',
            'value_overrides.*.icon' => 'nullable|string|max:64',
            'value_overrides.*.color' => ['nullable', 'string', 'max:32', Rule::in(CharacteristicPaletteResolver::ALLOWED_PALETTES)],
            'value_overrides.*.subtitle' => 'nullable|string|max:500',
            'hide_when_empty' => 'nullable|boolean',
            'hide_when_false' => 'nullable|boolean',
        ]);

        $updatePayload = [
            'name' => $data['name'],
            'short_name' => $data['short_name'] ?? null,
            'descriptions' => $data['description'] ?? null,
            'helper' => $data['helper'] ?? null,
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? null,
            'type' => $data['type'],
            'unit' => $data['unit'] ?? null,
            'sort_order' => $data['sort_order'] ?? $characteristic->sort_order,
        ];
        if ($this->characteristicsTableHasStatusColumn()) {
            $updatePayload['status'] = $data['status'] ?? $characteristic->status ?? Characteristic::STATUS_A_VALIDER;
        }
        if ($this->characteristicsTableHasIconFalseColumn()) {
            $updatePayload['icon_false'] = $data['icon_false'] ?? null;
        }
        if ($this->characteristicsTableHasValueOverridesColumn()) {
            $updatePayload['value_overrides'] = $this->parseValueOverridesInput($data['value_overrides'] ?? null);
        }
        if ($this->characteristicsTableHasHideWhenEmptyColumn()) {
            $updatePayload['hide_when_empty'] = (bool) ($data['hide_when_empty'] ?? false);
        }
        if ($this->characteristicsTableHasHideWhenFalseColumn()) {
            $updatePayload['hide_when_false'] = (bool) ($data['hide_when_false'] ?? false);
        }
        if (Schema::hasColumn('characteristics', 'nature')) {
            $updatePayload['nature'] = $data['nature'] ?? null;
            $updatePayload['intrinsic_characteristic_id'] = $data['intrinsic_characteristic_id'] ?? null;
        }

        $characteristic->update($updatePayload);

        $group = $this->inferPrimaryGroup($characteristic);
        $allowedForGroup = self::ENTITIES_BY_GROUP[$group] ?? [];
        $allowedEntities = array_filter(
            $data['entities'] ?? [],
            fn (array $ent): bool => in_array($ent['entity'] ?? '', $allowedForGroup, true)
        );
        // Ne persister que '*' + entity_override_keys lorsque la clé est fournie (évite de recréer des lignes)
        $entitiesToProcess = $allowedEntities;
        if (array_key_exists('entity_override_keys', $data) && is_array($data['entity_override_keys'])) {
            $overrideKeys = array_values(array_filter(
                $data['entity_override_keys'],
                fn ($k): bool => is_string($k) && $k !== ''
            ));
            $entitiesToProcess = array_filter(
                $allowedEntities,
                fn (array $ent): bool => ($ent['entity'] ?? '') === '*' || in_array($ent['entity'] ?? '', $overrideKeys, true)
            );
        }
        foreach ($entitiesToProcess as $ent) {
            $formula = trim((string) ($ent['norms_formula'] ?? ''));
            if ($formula === '') {
                continue;
            }
            $errors = $this->normsGridGenerator->validateNormsFormula($formula);
            if ($errors !== []) {
                return redirect()->back()->withErrors(['entities' => implode(' ; ', $errors)])->withInput();
            }
        }

        $sentEntities = array_column($entitiesToProcess, 'entity');
        foreach ($entitiesToProcess as $ent) {
            $entity = $ent['entity'];
            $this->updateGroupRow($characteristic->id, $group, $entity, $ent);
        }

        $toRemove = array_filter($allowedForGroup, fn (string $e): bool => $e !== '*' && ! in_array($e, $sentEntities, true));
        foreach ($toRemove as $entity) {
            $this->deleteGroupRow($characteristic->id, $entity, $group);
        }

        $this->deleteOrphanGroupRows($characteristic->id, $group);

        $this->normsGridGenerator->regenerateKey($characteristic->key);

        $this->getter->clearCache();
        $this->colorCssGenerator->generate();

        return redirect()->route('admin.characteristics.show', $characteristic_key)->with('success', 'Caractéristique mise à jour.');
    }

    /**
     * Convertit une caractéristique autonome en caractéristique liée : lie à une maître et supprime les lignes entité.
     */
    private function convertToLinked(Request $request, Characteristic $characteristic): RedirectResponse
    {
        $validated = $request->validate([
            'linked_to_characteristic_id' => ['required', 'integer', 'exists:characteristics,id'],
        ]);
        $masterId = (int) $validated['linked_to_characteristic_id'];

        if ($masterId === $characteristic->id) {
            return redirect()->route('admin.characteristics.show', $characteristic->key)
                ->with('error', 'Une caractéristique ne peut pas être liée à elle-même.');
        }

        $master = Characteristic::find($masterId);
        if ($master->isLinked()) {
            return redirect()->route('admin.characteristics.show', $characteristic->key)
                ->with('error', 'La caractéristique maître choisie est elle-même une liée. Choisissez une caractéristique autonome.');
        }

        $characteristic->update(['linked_to_characteristic_id' => $masterId]);

        CharacteristicCreature::where('characteristic_id', $characteristic->id)->delete();
        CharacteristicObject::where('characteristic_id', $characteristic->id)->delete();
        CharacteristicSpell::where('characteristic_id', $characteristic->id)->delete();
        CharacteristicConsumable::where('characteristic_id', $characteristic->id)->delete();
        CharacteristicProfile::where('characteristic_id', $characteristic->id)->delete();

        $this->getter->clearCache();
        $this->colorCssGenerator->generate();

        return redirect()->route('admin.characteristics.show', $characteristic->key)
            ->with('success', 'Caractéristique convertie en liée. La définition est désormais celle de la caractéristique maître.');
    }

    /**
     * Upload d'icône pour une caractéristique (Spatie Media Library).
     * Requiert characteristic_id ; attache le média à la caractéristique et met à jour la colonne icon.
     */
    public function uploadIcon(Request $request, EntityImageMediaService $entityImageMediaService): JsonResponse
    {
        $validated = $request->validate([
            'characteristic_id' => ['required', 'integer', 'exists:characteristics,id'],
        ]);

        $characteristic = Characteristic::findOrFail($validated['characteristic_id']);
        $this->authorize('update', $characteristic);

        // Collection `icons` en singleFile : un attach réussi remplace l’ancien.
        // Ne pas clear avant validation, sinon un upload rejeté efface l’icône.
        $media = $entityImageMediaService->attachFromRequest(
            $characteristic,
            $request,
            'file',
            'icons',
            'icon',
            2048,
        );
        $payload = $entityImageMediaService->mediaPayload($media);

        return response()->json(array_merge(
            [
                'success' => true,
                'icon' => $media->getUrl(),
            ],
            $payload,
        ));
    }

    /**
     * Liste des caractéristiques regroupées par groupe (creature, object, spell) pour le menu gauche.
     *
     * @return array<string, list<array{id: string, name: string, short_name: string|null, icon: string|null, color: string|null, status: string}>>
     */
    private function buildCharacteristicsByGroup(): array
    {
        $byGroup = array_fill_keys(array_keys(self::ENTITIES_BY_GROUP), []);
        $characteristics = Characteristic::with('masterCharacteristic')->orderBy('sort_order')->orderBy('key')->get();
        foreach ($characteristics as $c) {
            $group = $this->inferPrimaryGroup($c);
            if (! array_key_exists($group, $byGroup)) {
                continue;
            }
            $effective = $c->effectiveCharacteristic();
            $nature = $effective->natureEnum();
            $byGroup[$group][] = [
                'id' => $c->key,
                'name' => $effective->name,
                'short_name' => $effective->short_name,
                'icon' => $effective->icon,
                'color' => $effective->color,
                'status' => $this->characteristicsTableHasStatusColumn() ? ($c->status ?? Characteristic::STATUS_A_VALIDER) : Characteristic::STATUS_A_VALIDER,
                'nature' => $nature?->value,
                'nature_label' => $nature?->label(),
            ];
        }

        return $byGroup;
    }

    /**
     * Navigation par entité : pour chaque entité, liste des caractéristiques qui ont une ligne pour cette entité.
     *
     * @return array<string, list<array{id: string, name: string}>>
     */
    private function buildEntityNav(): array
    {
        $byEntity = [];
        foreach (self::ENTITIES as $entity) {
            $byEntity[$entity] = [];
        }
        $ids = [];
        foreach (CharacteristicCreature::select('characteristic_id', 'entity')->distinct()->get() as $row) {
            $ids[$row->entity][] = $row->characteristic_id;
        }
        foreach (CharacteristicObject::select('characteristic_id', 'entity')->distinct()->get() as $row) {
            $ids[$row->entity][] = $row->characteristic_id;
        }
        foreach (CharacteristicSpell::select('characteristic_id', 'entity')->distinct()->get() as $row) {
            $ids[$row->entity][] = $row->characteristic_id;
        }
        foreach (CharacteristicConsumable::select('characteristic_id', 'entity')->distinct()->get() as $row) {
            $ids[$row->entity][] = $row->characteristic_id;
        }
        foreach (CharacteristicProfile::select('characteristic_id', 'entity')->distinct()->get() as $row) {
            $ids[$row->entity][] = $row->characteristic_id;
        }
        foreach ($ids as $entity => $characteristicIds) {
            $characteristicIds = array_unique($characteristicIds);
            $chars = Characteristic::whereIn('id', $characteristicIds)->orderBy('sort_order')->orderBy('key')->get();
            $byEntity[$entity] = $chars->map(fn ($c) => ['id' => $c->key, 'name' => $c->name ?? $c->key])->all();
        }

        return $byEntity;
    }

    /**
     * @return array<string, mixed>
     */
    private function groupRowToEntity(
        CharacteristicCreature|CharacteristicObject|CharacteristicSpell|CharacteristicConsumable|CharacteristicProfile $row,
        Characteristic $characteristic
    ): array {
        $out = [
            'entity' => $row->entity,
            'db_column' => $row->db_column,
            'min' => $row->min,
            'max' => $row->max,
            'formula' => $row->formula,
            'formula_display' => $row->formula_display,
            'default_value' => $row->default_value,
            'conversion_formula' => $row->conversion_formula,
            'conversion_function' => $row->conversion_function,
            'conversion_dofus_sample' => $row->conversion_dofus_sample,
            'conversion_krosmoz_sample' => $row->conversion_krosmoz_sample,
            'conversion_sample_rows' => $row->conversion_sample_rows,
            'forgemagie_max' => 0,
            'base_price_per_unit' => null,
            'rune_price_per_unit' => null,
            'norms_grid' => $row->norms_grid,
            'norms_conditions' => $row->norms_conditions,
            'norms_description' => $row->norms_description,
            'norms_help_section_id' => $row->norms_help_section_id,
        ];
        if ($row instanceof CharacteristicObject) {
            $out['forgemagie_max'] = $row->forgemagie_max;
            $out['base_price_per_unit'] = $row->base_price_per_unit;
            $out['rune_price_per_unit'] = $row->rune_price_per_unit;
            $out['item_type_ids'] = $row->relationLoaded('allowedItemTypes')
                ? $row->allowedItemTypes->pluck('id')->map(fn ($id): int => (int) $id)->values()->all()
                : [];
        }

        return $out;
    }

    /**
     * Construit les variables par défaut pour la prévisualisation (autres que la variable d’axe).
     *
     * @param  array{type: string, characteristic?: string, entries?: list<array{value?: string}>}  $decoded
     * @return array<string, int|float>
     */
    private function buildDefaultVariablesForPreview(string $formula, array $decoded, string $axisVar, string $entity): array
    {
        $defaults = [];
        $collectVar = function (string $varId) use ($axisVar, $entity, &$defaults): void {
            if ($varId === $axisVar || isset($defaults[$varId])) {
                return;
            }
            $lim = $this->getter->getLimitsByField($varId, $entity);
            $defaults[$varId] = $lim !== null ? (int) round(($lim['min'] + $lim['max']) / 2) : 10;
        };

        if ($decoded['type'] === 'table' && isset($decoded['entries'])) {
            foreach ($decoded['entries'] as $entry) {
                $v = $entry['value'] ?? null;
                if (is_string($v) && $v !== '' && preg_match_all('/\[(\w+)\]/', $v, $m)) {
                    foreach (array_unique($m[1]) as $varId) {
                        $collectVar($varId);
                    }
                }
            }
        } else {
            if (preg_match_all('/\[(\w+)\]/', $formula, $m)) {
                foreach (array_unique($m[1]) as $varId) {
                    $collectVar($varId);
                }
            }
        }

        return $defaults;
    }

    /**
     * Fusionne les définitions d'entités avec des valeurs par défaut pour les entités du groupe uniquement.
     * Évite de renvoyer des entités d'autres groupes (object/spell pour une caractéristique creature).
     */
    private function mergeDefaultEntityDefinitionsForGroup(array $entities, string $group): array
    {
        $byEntity = [];
        foreach ($entities as $ent) {
            $e = $ent['entity'] ?? null;
            if (is_string($e)) {
                $byEntity[$e] = $ent;
            }
        }
        $allowed = self::ENTITIES_BY_GROUP[$group] ?? ['*'];
        $out = [];
        foreach ($allowed as $entity) {
            $out[] = $byEntity[$entity] ?? [
                'entity' => $entity,
                'db_column' => null,
                'min' => null,
                'max' => null,
                'formula' => null,
                'formula_display' => null,
                'default_value' => null,
                'conversion_formula' => null,
                'conversion_function' => null,
                'forgemagie_max' => 0,
                'base_price_per_unit' => null,
                'rune_price_per_unit' => null,
                'norms_grid' => null,
                'norms_conditions' => null,
                'norms_description' => null,
                'norms_help_section_id' => null,
                'item_type_ids' => [],
            ];
        }

        return $out;
    }

    /**
     * Normalise la clé d'une caractéristique : ajoute le suffixe du groupe si absent.
     * Ex. life_dice + groupe creature → life_dice_creature ; life_creature → inchangé.
     */
    private function normalizeCharacteristicKey(string $key, string $group): string
    {
        $suffix = '_'.$group;
        if (strlen($key) >= strlen($suffix) && str_ends_with($key, $suffix)) {
            return $key;
        }

        return $key.$suffix;
    }

    private function updateGroupRow(int $characteristicId, string $group, string $entity, array $data): void
    {
        $sampleRows = $this->normalizeSampleRowsForSave($data['conversion_sample_rows'] ?? null);
        $common = [
            'db_column' => $data['db_column'] ?? null,
            'min' => $data['min'] ?? null,
            'max' => $data['max'] ?? null,
            'formula' => $data['formula'] ?? null,
            'formula_display' => $data['formula_display'] ?? null,
            'default_value' => $data['default_value'] ?? null,
            'conversion_formula' => $data['conversion_formula'] ?? null,
            'conversion_function' => trim((string) ($data['conversion_function'] ?? '')) === '' ? null : $data['conversion_function'],
            'conversion_dofus_sample' => $this->deriveSampleFromRows($sampleRows, 'dofus') ?? $this->normalizeSampleForSave($data['conversion_dofus_sample'] ?? null),
            'conversion_krosmoz_sample' => $this->deriveSampleFromRows($sampleRows, 'krosmoz') ?? $this->normalizeSampleForSave($data['conversion_krosmoz_sample'] ?? null),
            'conversion_sample_rows' => $sampleRows,
            'norms_grid' => $this->parseNormsGrid($data['norms_grid'] ?? null),
            'norms_formula' => $this->parseNormsFormula($data['norms_formula'] ?? null),
            'norms_conditions' => ! empty($data['norms_conditions']) ? array_values($data['norms_conditions']) : null,
            'norms_description' => ($data['norms_description'] ?? null) !== '' ? ($data['norms_description'] ?? null) : null,
            'norms_help_section_id' => $this->parseNormsHelpSectionId($data['norms_help_section_id'] ?? null),
        ];

        if ($group === 'creature' && in_array($entity, self::ENTITIES_BY_GROUP['creature'], true)) {
            CharacteristicCreature::updateOrCreate(
                ['characteristic_id' => $characteristicId, 'entity' => $entity],
                $common
            );

            return;
        }
        if ($group === 'object' && in_array($entity, self::ENTITIES_BY_GROUP['object'], true)) {
            $basePrice = $data['base_price_per_unit'] ?? null;
            $runePrice = $data['rune_price_per_unit'] ?? null;
            $charObj = CharacteristicObject::updateOrCreate(
                ['characteristic_id' => $characteristicId, 'entity' => $entity],
                array_merge($common, [
                    'forgemagie_max' => (int) ($data['forgemagie_max'] ?? 0),
                    'base_price_per_unit' => $basePrice !== null && $basePrice !== '' ? (float) $basePrice : null,
                    'rune_price_per_unit' => $runePrice !== null && $runePrice !== '' ? (float) $runePrice : null,
                ])
            );
            if (array_key_exists('item_type_ids', $data) && is_array($data['item_type_ids'])) {
                $ids = array_values(array_unique(array_filter(array_map('intval', $data['item_type_ids']))));
                $charObj->allowedItemTypes()->sync($ids);
            }

            return;
        }
        if ($group === 'spell' && in_array($entity, self::ENTITIES_BY_GROUP['spell'], true)) {
            CharacteristicSpell::updateOrCreate(
                ['characteristic_id' => $characteristicId, 'entity' => $entity],
                $common
            );

            return;
        }
        if ($group === 'consumable' && in_array($entity, self::ENTITIES_BY_GROUP['consumable'], true)) {
            CharacteristicConsumable::updateOrCreate(
                ['characteristic_id' => $characteristicId, 'entity' => $entity],
                $common
            );

            return;
        }
        if ($group === 'profile' && in_array($entity, self::ENTITIES_BY_GROUP['profile'], true)) {
            CharacteristicProfile::updateOrCreate(
                ['characteristic_id' => $characteristicId, 'entity' => $entity],
                $common
            );
        }
    }

    /**
     * Supprime la ligne d'une entité donnée pour une caractéristique (suppression de la spécificité).
     */
    private function deleteGroupRow(int $characteristicId, string $entity, string $group): void
    {
        if ($group === 'creature' && in_array($entity, ['monster', 'class', 'npc'], true)) {
            CharacteristicCreature::where('characteristic_id', $characteristicId)->where('entity', $entity)->delete();
        }
        if ($group === 'object' && in_array($entity, ['item', 'panoply'], true)) {
            CharacteristicObject::where('characteristic_id', $characteristicId)->where('entity', $entity)->delete();
        }
        if ($group === 'spell' && $entity === 'spell') {
            CharacteristicSpell::where('characteristic_id', $characteristicId)->where('entity', $entity)->delete();
        }
        if ($group === 'consumable' && $entity === 'consumable') {
            CharacteristicConsumable::where('characteristic_id', $characteristicId)->where('entity', $entity)->delete();
        }
        if ($group === 'profile' && in_array($entity, ['breed', 'specialization', 'monster'], true)) {
            CharacteristicProfile::where('characteristic_id', $characteristicId)->where('entity', $entity)->delete();
        }
    }

    /**
     * Supprime les lignes créées par erreur dans les tables des autres groupes (ex. lignes object/spell pour une caractéristique creature).
     */
    private function deleteOrphanGroupRows(int $characteristicId, string $currentGroup): void
    {
        if ($currentGroup !== 'creature') {
            CharacteristicCreature::where('characteristic_id', $characteristicId)->delete();
        }
        if ($currentGroup !== 'object') {
            CharacteristicObject::where('characteristic_id', $characteristicId)->delete();
        }
        if ($currentGroup !== 'spell') {
            CharacteristicSpell::where('characteristic_id', $characteristicId)->delete();
        }
        if ($currentGroup !== 'consumable') {
            CharacteristicConsumable::where('characteristic_id', $characteristicId)->delete();
        }
        if ($currentGroup !== 'profile') {
            CharacteristicProfile::where('characteristic_id', $characteristicId)->delete();
        }
    }

    /**
     * Remplace les chaînes vides par null pour éviter les erreurs de validation « integer » côté formulaire.
     *
     * @param  list<array<string, mixed>>  $entities
     * @return list<array<string, mixed>>
     */
    private function normalizeNormsHelpSectionIdsInEntities(array $entities): array
    {
        foreach ($entities as $i => $ent) {
            if (! is_array($ent)) {
                continue;
            }
            $v = $ent['norms_help_section_id'] ?? null;
            if ($v === '' || $v === false) {
                $entities[$i]['norms_help_section_id'] = null;
            }
        }

        return $entities;
    }

    /**
     * ID de section d’aide liée aux normes (nullable).
     */
    private function parseNormsHelpSectionId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            $id = (int) $value;

            return $id > 0 ? $id : null;
        }

        return null;
    }

    /**
     * Formule de génération des normes (nullable).
     */
    private function parseNormsFormula(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (! is_string($value)) {
            return null;
        }
        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * Aperçu JSON d'une grille générée depuis norms_formula (sans écriture).
     */
    public function normsPreview(Request $request, string $characteristic_key): JsonResponse
    {
        $validated = $request->validate([
            'norms_formula' => 'required|string',
            'entity' => 'nullable|string|max:32',
        ]);
        $formula = $validated['norms_formula'];
        $errors = $this->normsGridGenerator->validateNormsFormula($formula);
        if ($errors !== []) {
            return response()->json(['message' => implode(' ; ', $errors)], 422);
        }

        try {
            $grid = $this->normsGridGenerator->preview($formula, [], true);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'key' => $characteristic_key,
            'grid' => $grid,
        ]);
    }

    /**
     * Normalise la grille norms : conserve uniquement les 5 power levels attendus.
     * Retourne null si la grille est vide ou si toutes les cellules sont vides.
     *
     * @return array<string, list<int|float|null>>|null
     */
    private function parseNormsGrid(mixed $input): ?array
    {
        if (! is_array($input) || $input === []) {
            return null;
        }
        $powerLevels = ['very_weak', 'weak', 'neutral', 'strong', 'very_strong'];
        $grid = [];
        $hasValue = false;
        foreach ($powerLevels as $pl) {
            $row = $input[$pl] ?? [];
            if (! is_array($row)) {
                $grid[$pl] = array_fill(0, 20, null);

                continue;
            }
            $normalized = [];
            for ($i = 0; $i < 20; $i++) {
                $v = $row[$i] ?? null;
                if ($v !== null && $v !== '' && is_numeric($v)) {
                    $normalized[] = (float) $v;
                    $hasValue = true;
                } else {
                    $normalized[] = null;
                }
            }
            $grid[$pl] = $normalized;
        }

        return $hasValue ? $grid : null;
    }

    /**
     * @return list<array{value: mixed, icon?: string, color?: string, subtitle?: string}>|null
     */
    private function parseValueOverridesInput(mixed $input): ?array
    {
        if (! is_array($input) || $input === []) {
            return null;
        }

        $clean = [];
        foreach ($input as $entry) {
            if (! is_array($entry) || ! array_key_exists('value', $entry)) {
                continue;
            }
            $item = ['value' => $entry['value']];
            if (isset($entry['icon']) && is_string($entry['icon']) && $entry['icon'] !== '') {
                $item['icon'] = $entry['icon'];
            }
            if (isset($entry['color']) && is_string($entry['color']) && $entry['color'] !== '') {
                $item['color'] = $entry['color'];
            }
            if (isset($entry['subtitle']) && is_string($entry['subtitle']) && $entry['subtitle'] !== '') {
                $item['subtitle'] = $entry['subtitle'];
            }
            $clean[] = $item;
        }

        return $clean !== [] ? $clean : null;
    }
}
