<?php

namespace App\Http\Controllers\Api\Table;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\Type\SpellType;
use App\Services\Effect\SpellEffectDefinitionsSerializer;
use App\Services\Effect\SpellEffectUsagesDataService;
use App\Services\Spell\SpellDegreeResolver;
use App\Support\AreaConstants;
use App\Support\ElementBitmask;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * SpellTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les sorts.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class SpellTableController extends Controller
{
    use InterpretsEntityTableFilters;
    use InterpretsEntityTableSort;

    public function __construct(
        private readonly SpellEffectUsagesDataService $spellEffectUsagesDataService,
        private readonly SpellEffectDefinitionsSerializer $spellEffectDefinitionsSerializer,
        private readonly SpellDegreeResolver $spellDegreeResolver
    ) {}

    /**
     * Propriétés affichées depuis le premier degré, avec repli sur le sort.
     *
     * @return array<string, mixed>
     */
    private function effectiveProperties(Spell $spell): array
    {
        $degree = $this->spellDegreeResolver->selectDegree($spell);

        return $this->spellDegreeResolver->resolveProperties($spell, $degree);
    }

    private function formatRange(mixed $min, mixed $max): string
    {
        $min = trim((string) ($min ?? ''));
        $max = trim((string) ($max ?? ''));
        if ($min === '' && $max === '') {
            return '';
        }
        if ($min === '' || $max === '' || $min === $max) {
            return $min !== '' ? $min : $max;
        }

        return $min.' - '.$max;
    }

    /**
     * Données communes liste / ligne (effets résolus, définitions + invocations, portée brute).
     *
     * @return array{
     *     effect_usages_summary: string,
     *     effect_usages_chips: list<array<string, mixed>>,
     *     effects_definitions: list<array<string, mixed>>,
     *     po_min: string|null,
     *     po_max: string|null
     * }
     */
    private function buildSpellTableDisplayPayload(Spell $spell): array
    {
        $effectUsagesData = $this->spellEffectUsagesDataService->build($spell);

        return [
            'effect_usages_summary' => $effectUsagesData['summary'],
            'effect_usages_chips' => $effectUsagesData['chips'],
            'effects_definitions' => $this->spellEffectDefinitionsSerializer->serialize($spell->effects ?? collect()),
            'po_min' => $spell->po_min,
            'po_max' => $spell->po_max,
            'resolution_mode' => (string) ($spell->resolution_mode ?? 'attack_roll'),
            'attack_characteristic_key' => $spell->attack_characteristic_key,
            'save_characteristic_key' => $spell->save_characteristic_key,
            'save_dc_formula' => $spell->save_dc_formula,
            'save_success_note' => $spell->save_success_note,
            'auto_success_if_willing_target' => (bool) ($spell->auto_success_if_willing_target ?? false),
        ];
    }

    /**
     * Filtres sorts : égalité / whereIn + types, PO, zone, sous-effet.
     *
     * @param  Builder<Spell>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applySpellTableFilters(Builder $query, array $filters): void
    {
        $scalar = [
            'id' => ['id', 'int'],
            'category' => ['category', 'int'],
            'element' => ['element', 'int'],
            'is_magic' => ['is_magic', 'int'],
            'allows_reaction' => ['allows_reaction', 'int'],
            'powerful' => ['powerful', 'int'],
            'state' => ['state', 'string'],
            'sight_line' => ['sight_line', 'int'],
            'po_editable' => ['po_editable', 'int'],
            'ritual_available' => ['ritual_available', 'int'],
            'auto_update' => ['auto_update', 'int'],
        ];
        foreach (['level', 'pa'] as $rangeKey) {
            if ($this->hasFilterValue($filters, $rangeKey)) {
                $this->applyIntegerRangeFilter($query, $rangeKey, $filters[$rangeKey]);
            }
        }
        foreach ($scalar as $key => [$column, $cast]) {
            if ($this->hasFilterValue($filters, $key)) {
                $this->applyEqualityFilter($query, $column, $filters[$key], $cast);
            }
        }

        if ($this->hasFilterValue($filters, 'types')) {
            $ids = array_values(array_filter(
                $this->castFilterList($filters['types'], 'int'),
                fn ($id) => (int) $id > 0
            ));
            if ($ids !== []) {
                $query->whereHas('spellTypes', function (Builder $q) use ($ids) {
                    $q->whereIn($q->qualifyColumn('id'), $ids);
                });
            }
        }

        if ($this->hasFilterValue($filters, 'po')) {
            if ($this->isRangeFilterValue($filters['po'])) {
                $this->applyIntegerRangeOverlapFilter($query, 'po_min', 'po_max', $filters['po']);
            } else {
                $values = $this->normalizeFilterList($filters['po']);
                $query->where(function (Builder $q) use ($values) {
                    foreach ($values as $value) {
                        if ($value === '6') {
                            $q->orWhere('po_min', '>=', 6)->orWhere('po_max', '>=', 6);

                            continue;
                        }
                        $q->orWhere('po_min', $value)->orWhere('po_max', $value);
                    }
                });
            }
        }

        if ($this->hasFilterValue($filters, 'area')) {
            $areas = $this->normalizeFilterList($filters['area']);
            $query->whereHas('effects.degrees', fn (Builder $q) => $q->whereIn('area', $areas));
        }

        if ($this->hasFilterValue($filters, 'sub_effect')) {
            $slugs = $this->normalizeFilterList($filters['sub_effect']);
            $query->whereHas(
                'effects.degrees.effectSubEffects.subEffect',
                fn (Builder $q) => $q->whereIn('slug', $slugs)
            );
        }
    }

    /**
     * Tri tableau sorts : colonnes SQL + alias `po` (po_min/po_max) et `area` (sous-requête degré).
     *
     * @param  Builder<Spell>  $query
     */
    private function applySpellTableSort(Builder $query, Request $request): void
    {
        $allowedSort = [
            'id', 'name', 'description', 'level', 'pa', 'po', 'area', 'element', 'category',
            'is_magic', 'allows_reaction', 'casting_time', 'ritual_available', 'cast_per_turn',
            'cast_per_target', 'number_between_two_cast', 'duration', 'sight_line', 'po_editable',
            'state', 'auto_update', 'read_level', 'write_level', 'dofusdb_id', 'created_at', 'updated_at',
        ];

        $sorts = $request->input('sorts');
        if (is_array($sorts) && $sorts !== []) {
            $applied = false;
            foreach ($sorts as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $field = (string) ($item['field'] ?? $item['column'] ?? '');
                $dir = strtolower((string) ($item['dir'] ?? $item['order'] ?? 'asc'));
                if ($field === '' || ! in_array($field, $allowedSort, true)) {
                    continue;
                }
                if (! in_array($dir, ['asc', 'desc'], true)) {
                    $dir = 'asc';
                }
                $this->applyOneSpellSortColumn($query, $field, $dir);
                $applied = true;
            }
            if ($applied) {
                return;
            }
        }

        $sort = (string) $request->get('sort', 'id');
        $order = strtolower((string) $request->get('order', 'desc'));
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }
        if (in_array($sort, $allowedSort, true)) {
            $this->applyOneSpellSortColumn($query, $sort, $order);

            return;
        }

        $query->latest();
    }

    /**
     * @param  Builder<Spell>  $query
     */
    private function applyOneSpellSortColumn(Builder $query, string $field, string $dir): void
    {
        if ($field === 'po') {
            $query->orderBy('po_min', $dir)->orderBy('po_max', $dir);

            return;
        }

        if ($field === 'area') {
            // Premier degré (ORDER BY degree) du premier effet lié — aligné sur getAreaAttribute.
            $query->orderByRaw(
                '(SELECT ed.area FROM effect_degrees ed
                    INNER JOIN effect_spell es ON es.effect_id = ed.effect_id
                    WHERE es.spell_id = spells.id
                    ORDER BY ed.degree ASC
                    LIMIT 1) '.$dir
            );

            return;
        }

        $query->orderBy($field, $dir);
    }

    /** Construit la cellule area (chips avec icône) pour le format cells. */
    private function buildAreaCell(?string $area): array
    {
        if ($area === null || trim($area) === '') {
            return [
                'type' => 'text',
                'value' => '—',
                'params' => ['sortValue' => '', 'searchValue' => ''],
            ];
        }
        $value = (string) $area;

        return [
            'type' => 'chips',
            'value' => '',
            'params' => [
                'items' => [
                    [
                        'icon' => AreaConstants::getIconPath($area),
                        'value' => $value,
                        'tooltip' => 'Zone: '.$value,
                    ],
                ],
                'sortValue' => $value,
                'searchValue' => $value,
            ],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Spell::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['level', 'pa', 'category', 'element', 'is_magic', 'allows_reaction', 'powerful', 'state', 'sight_line', 'po_editable', 'types', 'po', 'area', 'sub_effect'] as $k) {
            if (! array_key_exists($k, $filters) && $request->has($k)) {
                $filters[$k] = $request->get($k);
            }
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $limit = (int) $request->integer('limit', $request->has('page') ? 25 : 5000);
        $limit = max(1, min($limit, 20000));

        $page = max(1, (int) $request->integer('page', 1));
        $offset = ($page - 1) * $limit;

        $sortsPayload = $request->input('sorts');
        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (is_array($sortsPayload) && isset($sortsPayload[0]) && is_array($sortsPayload[0])) {
            $sort = (string) ($sortsPayload[0]['field'] ?? $sortsPayload[0]['column'] ?? $sort);
            $order = strtolower((string) ($sortsPayload[0]['dir'] ?? $sortsPayload[0]['order'] ?? $order));
        }
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = Spell::query()
            ->visibleToUser($request->user())
            ->with([
                'createdBy',
                'spellTypes',
                'effects.degrees.effectSubEffects.subEffect',
                'degrees.effects.subEffect',
            ])
            ->withCount(['spellTypes', 'breeds', 'creatures', 'monsters']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('official_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $this->applySpellTableFilters($query, $filters);
        $this->applyEntityTableIdList($query, $request);

        $this->applySpellTableSort($query, $request);

        $total = $query->count();
        $lastPage = (int) max(1, ceil($total / $limit));
        $rows = $query->skip($offset)->limit($limit)->get();

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Spell::class),
            'createAny' => Gate::allows('createAny', Spell::class),
            'updateAny' => Gate::allows('updateAny', Spell::class),
            'deleteAny' => Gate::allows('deleteAny', Spell::class),
            'manageAny' => Gate::allows('manageAny', Spell::class),
        ];

        $visibleSpells = Spell::query()->visibleToUser($request->user());
        $poMinBounds = $this->integerColumnBounds($visibleSpells, 'po_min', 0, 20);
        $poMaxBounds = $this->integerColumnBounds($visibleSpells, 'po_max', 0, 20);
        $filterOptions = [
            'level' => $this->integerColumnBounds($visibleSpells, 'level', 1, 200),
            'area' => collect(AreaConstants::SHAPES)
                ->map(fn (string $shape) => ['value' => $shape, 'label' => AreaConstants::getShapeLabel($shape)])
                ->values()->all(),
            'types' => SpellType::query()->orderBy('name')->get(['id', 'name', 'color', 'icon', 'show_in_catalog'])
                ->map(fn (SpellType $t) => [
                    'value' => (string) $t->id,
                    'label' => $t->name,
                    'color' => $t->color,
                    'icon' => $t->icon,
                    'show_in_catalog' => (bool) $t->show_in_catalog,
                ])
                ->values()->all(),
            'pa' => $this->integerColumnBounds($visibleSpells, 'pa', 0, 12),
            'po' => [
                'min' => min($poMinBounds['min'], $poMaxBounds['min']),
                'max' => max($poMinBounds['max'], $poMaxBounds['max']),
            ],
            'sub_effect' => SubEffect::query()->orderBy('type_slug')->orderBy('slug')->get(['id', 'slug', 'type_slug'])
                ->map(fn (SubEffect $s) => ['value' => $s->slug, 'label' => $s->slug])
                ->values()->all(),
            'category' => [
                ['value' => '0', 'label' => 'Sort de classe', 'color' => 'sky-600'],
                ['value' => '1', 'label' => 'Sort de créature', 'color' => 'amber-600'],
                ['value' => '2', 'label' => 'Sort apprenable', 'color' => 'violet-600'],
                ['value' => '3', 'label' => 'Sort consommable', 'color' => 'emerald-600'],
            ],
            'element' => ElementBitmask::allFilterOptions(),
            'is_magic' => [
                ['value' => '1', 'label' => 'Wakfu'],
                ['value' => '0', 'label' => 'Physique'],
            ],
            'allows_reaction' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'powerful' => [
                ['value' => '0', 'label' => 'Normal'],
                ['value' => '1', 'label' => 'Puissant'],
            ],
            'sight_line' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'po_editable' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'ritual_available' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'auto_update' => [
                ['value' => '1', 'label' => 'Oui'],
                ['value' => '0', 'label' => 'Non'],
            ],
            'state' => EntityState::options(),
        ];

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (Spell $sp) {
                $createdBy = $sp->createdBy;
                $effective = $this->effectiveProperties($sp);
                $displayPayload = $this->buildSpellTableDisplayPayload($sp);
                $effectSubEffectSlugs = $sp->effects
                    ->flatMap(fn ($e) => $e->degrees->flatMap(fn ($d) => $d->effectSubEffects))
                    ->map(fn ($ese) => $ese->subEffect?->slug)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return array_merge([
                    'id' => $sp->id,
                    'official_id' => $sp->official_id,
                    'dofusdb_id' => $sp->dofusdb_id,
                    'name' => $sp->name,
                    'description' => $sp->description,
                    'effect' => $sp->effect,
                    'effect_sub_effect_slugs' => $effectSubEffectSlugs,
                    'area' => $effective['area'] ?? null,
                    'level' => $sp->level,
                    'po' => $this->formatRange($effective['po_min'] ?? null, $effective['po_max'] ?? null),
                    'po_min' => $effective['po_min'] ?? null,
                    'po_max' => $effective['po_max'] ?? null,
                    'po_editable' => (bool) ($effective['po_editable'] ?? false),
                    'pa' => $effective['pa'] ?? null,
                    'casting_time' => $effective['casting_time'] ?? null,
                    'ritual_available' => $effective['ritual_available'] ?? null,
                    'cast_per_turn' => $effective['cast_per_turn'] ?? null,
                    'cast_per_target' => $effective['cast_per_target'] ?? null,
                    'sight_line' => (bool) ($effective['sight_line'] ?? false),
                    'number_between_two_cast' => $effective['number_between_two_cast'] ?? null,
                    'duration' => $effective['duration'] ?? null,
                    'element' => $effective['element'] ?? null,
                    'category' => $sp->category,
                    'is_magic' => (bool) $sp->is_magic,
                    'allows_reaction' => (bool) ($effective['allows_reaction'] ?? false),
                    'powerful' => $sp->powerful,
                    'state' => (string) ($sp->state ?? 'draft'),
                    'read_level' => (int) ($sp->read_level ?? 0),
                    'write_level' => (int) ($sp->write_level ?? 0),
                    'image' => $sp->image,
                    'auto_update' => (bool) $sp->auto_update,
                    'spellTypes' => $sp->spellTypes?->map(fn ($t) => [
                        'id' => $t->id,
                        'name' => $t->name,
                        'color' => $t->color,
                        'icon' => $t->icon,
                    ])->values()->all() ?? [],
                    'spell_types_count' => (int) ($sp->spell_types_count ?? 0),
                    'breeds_count' => (int) ($sp->breeds_count ?? 0),
                    'creatures_count' => (int) ($sp->creatures_count ?? 0),
                    'monsters_count' => (int) ($sp->monsters_count ?? 0),
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $sp->created_at?->toISOString(),
                    'updated_at' => $sp->updated_at?->toISOString(),
                ], $displayPayload);
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'spells',
                    'query' => [
                        'search' => $search,
                        'filters' => $filters,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $limit,
                        'page' => $page,
                    ],
                    'pagination' => [
                        'total' => $total,
                        'perPage' => $limit,
                        'currentPage' => $page,
                        'lastPage' => $lastPage,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => $filterOptions,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Spell $sp) {
            $showHref = route('entities.spells.show', $sp->id);
            $dofusDbHref = $sp->dofusdb_id ? "https://www.dofus.com/fr/mmorpg/encyclopedie/sorts/{$sp->dofusdb_id}" : null;
            $effective = $this->effectiveProperties($sp);
            $effectiveRange = $this->formatRange($effective['po_min'] ?? null, $effective['po_max'] ?? null);

            $createdBy = $sp->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $createdAtLabel = $sp->created_at ? $sp->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $sp->created_at ? $sp->created_at->getTimestamp() : 0;
            $updatedAtLabel = $sp->updated_at ? $sp->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $sp->updated_at ? $sp->updated_at->getTimestamp() : 0;

            $types = $sp->spellTypes?->pluck('name')->filter()->values()->all() ?? [];
            $typesLabel = count($types) ? implode(', ', $types) : '-';

            $displayPayload = $this->buildSpellTableDisplayPayload($sp);

            return [
                'id' => $sp->id,
                'cells' => [
                    'id' => [
                        'type' => 'text',
                        'value' => (string) $sp->id,
                        'params' => ['sortValue' => $sp->id, 'filterValue' => (string) $sp->id],
                    ],
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $sp->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $sp->name,
                            'sortValue' => (string) $sp->name,
                        ],
                    ],
                    'description' => [
                        'type' => 'text',
                        'value' => $sp->description ?: '-',
                        'params' => ['searchValue' => (string) ($sp->description ?? ''), 'sortValue' => (string) ($sp->description ?? '')],
                    ],
                    'level' => [
                        'type' => 'text',
                        'value' => $sp->level ?: '-',
                        'params' => [
                            'filterValue' => (string) ($sp->level ?? ''),
                            'sortValue' => is_numeric((string) $sp->level) ? (int) $sp->level : (string) ($sp->level ?? ''),
                            'searchValue' => (string) ($sp->level ?? ''),
                        ],
                    ],
                    'pa' => [
                        'type' => 'text',
                        'value' => ($effective['pa'] ?? null) ?: '-',
                        'params' => [
                            'filterValue' => (string) ($effective['pa'] ?? ''),
                            'sortValue' => is_numeric((string) ($effective['pa'] ?? ''))
                                ? (int) $effective['pa']
                                : (string) ($effective['pa'] ?? ''),
                        ],
                    ],
                    'po' => [
                        'type' => 'text',
                        'value' => $effectiveRange ?: '-',
                        'params' => [
                            'sortValue' => $effectiveRange,
                        ],
                    ],
                    'area' => $this->buildAreaCell($effective['area'] ?? null),
                    'element' => [
                        'type' => 'badge',
                        'value' => ($effective['element'] ?? null) !== null ? (string) $effective['element'] : '-',
                        'params' => [
                            'filterValue' => (string) ($effective['element'] ?? ''),
                            'sortValue' => $effective['element'] ?? 0,
                        ],
                    ],
                    'category' => [
                        'type' => 'badge',
                        'value' => $sp->category !== null ? (string) $sp->category : '-',
                        'params' => ['filterValue' => (string) ($sp->category ?? ''), 'sortValue' => $sp->category ?? 0],
                    ],
                    'spell_types' => [
                        'type' => 'text',
                        'value' => $typesLabel,
                        'params' => [
                            'searchValue' => $typesLabel,
                            'sortValue' => $typesLabel,
                        ],
                    ],
                    'is_magic' => [
                        'type' => 'badge',
                        'value' => $sp->is_magic ? 'Wakfu' : 'Physique',
                        'params' => ['filterValue' => $sp->is_magic ? '1' : '0', 'sortValue' => $sp->is_magic ? 1 : 0],
                    ],
                    'allows_reaction' => [
                        'type' => 'badge',
                        'value' => ($effective['allows_reaction'] ?? false) ? 'Oui' : 'Non',
                        'params' => [
                            'filterValue' => ($effective['allows_reaction'] ?? false) ? '1' : '0',
                            'sortValue' => ($effective['allows_reaction'] ?? false) ? 1 : 0,
                        ],
                    ],
                    'casting_time' => [
                        'type' => 'text',
                        'value' => ($effective['casting_time'] ?? null) ?: '-',
                        'params' => ['sortValue' => (string) ($effective['casting_time'] ?? '')],
                    ],
                    'ritual_available' => [
                        'type' => 'badge',
                        'value' => ($effective['ritual_available'] ?? false) ? 'Oui' : 'Non',
                        'params' => [
                            'filterValue' => ($effective['ritual_available'] ?? false) ? '1' : '0',
                            'sortValue' => ($effective['ritual_available'] ?? false) ? 1 : 0,
                        ],
                    ],
                    'cast_per_turn' => [
                        'type' => 'text',
                        'value' => ($effective['cast_per_turn'] ?? null) ?: '-',
                        'params' => ['sortValue' => (string) ($effective['cast_per_turn'] ?? '')],
                    ],
                    'cast_per_target' => [
                        'type' => 'text',
                        'value' => ($effective['cast_per_target'] ?? null) ?: '-',
                        'params' => ['sortValue' => (string) ($effective['cast_per_target'] ?? '')],
                    ],
                    'number_between_two_cast' => [
                        'type' => 'text',
                        'value' => ($effective['number_between_two_cast'] ?? null) ?: '-',
                        'params' => ['sortValue' => (string) ($effective['number_between_two_cast'] ?? '')],
                    ],
                    'duration' => [
                        'type' => 'text',
                        'value' => ($effective['duration'] ?? null) ?: '-',
                        'params' => ['sortValue' => (string) ($effective['duration'] ?? '')],
                    ],
                    'sight_line' => [
                        'type' => 'badge',
                        'value' => ($effective['sight_line'] ?? false) ? 'Oui' : 'Non',
                        'params' => [
                            'sortValue' => ($effective['sight_line'] ?? false) ? 1 : 0,
                            'filterValue' => ($effective['sight_line'] ?? false) ? '1' : '0',
                        ],
                    ],
                    'po_editable' => [
                        'type' => 'badge',
                        'value' => ($effective['po_editable'] ?? false) ? 'Oui' : 'Non',
                        'params' => [
                            'sortValue' => ($effective['po_editable'] ?? false) ? 1 : 0,
                            'filterValue' => ($effective['po_editable'] ?? false) ? '1' : '0',
                        ],
                    ],
                    'state' => [
                        'type' => 'badge',
                        'value' => (string) ($sp->state ?? 'draft'),
                        'params' => ['filterValue' => (string) ($sp->state ?? ''), 'sortValue' => (string) ($sp->state ?? '')],
                    ],
                    'auto_update' => [
                        'type' => 'badge',
                        'value' => $sp->auto_update ? 'Oui' : 'Non',
                        'params' => ['sortValue' => $sp->auto_update ? 1 : 0],
                    ],
                    'image' => [
                        'type' => 'thumb',
                        'value' => $sp->image ?: '',
                        'params' => ['sortValue' => $sp->image ? 1 : 0],
                    ],
                    'read_level' => [
                        'type' => 'badge',
                        'value' => (string) ($sp->read_level ?? 0),
                        'params' => ['sortValue' => (int) ($sp->read_level ?? 0)],
                    ],
                    'write_level' => [
                        'type' => 'badge',
                        'value' => (string) ($sp->write_level ?? 0),
                        'params' => ['sortValue' => (int) ($sp->write_level ?? 0)],
                    ],
                    'dofusdb_id' => [
                        'type' => 'route',
                        'value' => $sp->dofusdb_id ? (string) $sp->dofusdb_id : '-',
                        'params' => [
                            'href' => $dofusDbHref,
                            'target' => '_blank',
                            'sortValue' => $sp->dofusdb_id ?? 0,
                            'filterValue' => (string) ($sp->dofusdb_id ?? ''),
                        ],
                    ],
                    'created_by' => [
                        'type' => 'text',
                        'value' => $createdByLabel,
                        'params' => [
                            'sortValue' => $createdByLabel,
                            'searchValue' => $createdByLabel,
                        ],
                    ],
                    'created_at' => [
                        'type' => 'text',
                        'value' => $createdAtLabel,
                        'params' => [
                            'sortValue' => $createdAtSort,
                            'searchValue' => $createdAtLabel,
                        ],
                    ],
                    'updated_at' => [
                        'type' => 'text',
                        'value' => $updatedAtLabel,
                        'params' => [
                            'sortValue' => $updatedAtSort,
                            'searchValue' => $updatedAtLabel,
                        ],
                    ],
                ],
                'rowParams' => [
                    'entity' => array_merge([
                        'id' => $sp->id,
                        'official_id' => $sp->official_id,
                        'dofusdb_id' => $sp->dofusdb_id,
                        'name' => $sp->name,
                        'description' => $sp->description,
                        'effect' => $sp->effect,
                        'area' => $effective['area'] ?? null,
                        'level' => $sp->level,
                        'po' => $effectiveRange,
                        'po_min' => $effective['po_min'] ?? null,
                        'po_max' => $effective['po_max'] ?? null,
                        'po_editable' => (bool) ($effective['po_editable'] ?? false),
                        'pa' => $effective['pa'] ?? null,
                        'casting_time' => $effective['casting_time'] ?? null,
                        'ritual_available' => $effective['ritual_available'] ?? null,
                        'cast_per_turn' => $effective['cast_per_turn'] ?? null,
                        'cast_per_target' => $effective['cast_per_target'] ?? null,
                        'sight_line' => (bool) ($effective['sight_line'] ?? false),
                        'number_between_two_cast' => $effective['number_between_two_cast'] ?? null,
                        'duration' => $effective['duration'] ?? null,
                        'element' => $effective['element'] ?? null,
                        'category' => $sp->category,
                        'is_magic' => (bool) $sp->is_magic,
                        'allows_reaction' => (bool) ($effective['allows_reaction'] ?? false),
                        'powerful' => $sp->powerful,
                        'state' => (string) ($sp->state ?? 'draft'),
                        'read_level' => (int) ($sp->read_level ?? 0),
                        'write_level' => (int) ($sp->write_level ?? 0),
                        'image' => $sp->image,
                        'auto_update' => (bool) $sp->auto_update,
                        'spellTypes' => $sp->spellTypes?->map(fn ($t) => [
                            'id' => $t->id,
                            'name' => $t->name,
                            'color' => $t->color,
                            'icon' => $t->icon,
                        ])->values()->all() ?? [],
                        'spell_types_count' => (int) ($sp->spell_types_count ?? 0),
                        'breeds_count' => (int) ($sp->breeds_count ?? 0),
                        'creatures_count' => (int) ($sp->creatures_count ?? 0),
                        'monsters_count' => (int) ($sp->monsters_count ?? 0),
                        'createdBy' => $createdBy ? [
                            'id' => $createdBy->id,
                            'name' => $createdBy->name,
                            'email' => $createdBy->email,
                        ] : null,
                    ], $displayPayload),
                ],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'spells',
                'query' => [
                    'search' => $search,
                    'filters' => $filters,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                    'page' => $page,
                ],
                'pagination' => [
                    'total' => $total,
                    'perPage' => $limit,
                    'currentPage' => $page,
                    'lastPage' => $lastPage,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
            ],
            'rows' => $tableRows,
        ]);
    }
}
