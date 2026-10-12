<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Getter;

use App\Contracts\Characteristic\CharacteristicDefinitionLookup;
use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use App\Services\Characteristic\Formula\FormulaResolutionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Getter généraliste : fournit les définitions d’une caractéristique par clé et entité.
 * Résout entity → groupe (creature, object, spell) et fusionne table générale + table de groupe.
 */
final class CharacteristicGetterService implements CharacteristicDefinitionLookup
{
    /** Valeur entity = « s'applique à toutes les entités du groupe ». */
    public const ENTITY_ALL = '*';

    /** TTL cache index champ → clé (aligné sur le mapping DofusDB). */
    private const FIELD_MAP_CACHE_TTL = 3600;

    public function __construct(
        private readonly FormulaResolutionService $formulaResolution
    ) {}

    /** Entités du groupe creature */
    private const GROUP_CREATURE = ['monster', 'class', 'npc'];

    /** Entités du groupe object */
    private const GROUP_OBJECT = ['item', 'consumable', 'resource', 'panoply'];

    /** Entités du groupe spell */
    private const GROUP_SPELL = ['spell'];

    /**
     * Résultats mémoïsés de getDefinition (évite requêtes répétées dans une même requête HTTP / worker).
     *
     * @var array<string, array<string, mixed>|null>
     */
    private array $definitionMemo = [];

    /** @var list<string> */
    private const FIELD_MAP_ENTITIES = [
        ...self::GROUP_CREATURE,
        ...self::GROUP_OBJECT,
        ...self::GROUP_SPELL,
    ];

    /**
     * Retourne la définition complète d’une caractéristique pour une entité (nom, limites, formules, conversion, etc.).
     *
     * @return array<string, mixed>|null
     */
    public function getDefinition(string $characteristicKey, string $entity): ?array
    {
        $memoKey = $characteristicKey.'|'.$entity;
        if (array_key_exists($memoKey, $this->definitionMemo)) {
            return $this->definitionMemo[$memoKey];
        }

        $characteristic = Characteristic::query()
            ->where('key', '=', $characteristicKey)
            ->first();
        if ($characteristic === null) {
            return $this->definitionMemo[$memoKey] = null;
        }

        // Si la caractéristique est liée, on résout la définition via la caractéristique maître.
        if ($characteristic->linked_to_characteristic_id !== null) {
            $master = $characteristic->effectiveCharacteristic();

            // On récupère la ligne de base (entity='*') de la maître, quelle que soit sa table de groupe.
            $base = $this->findMasterBaseRow($master);
            if ($base === null) {
                return $this->definitionMemo[$memoKey] = null;
            }

            $def = $this->mergeDefinition($master, $base, null, $entity);
            // La clé exposée reste celle de la caractéristique liée (ex. level_object),
            // même si la config vient de la maître (ex. level_creature).
            $def['key'] = $characteristicKey;

            return $this->definitionMemo[$memoKey] = $def;
        }

        [$base, $overlay] = $this->findGroupRows($characteristic->id, $entity);
        if ($base === null && $overlay === null) {
            return $this->definitionMemo[$memoKey] = null;
        }

        return $this->definitionMemo[$memoKey] = $this->mergeDefinition($characteristic, $base, $overlay, $entity);
    }

    /**
     * Retourne les limites min/max pour une caractéristique et une entité.
     * Min/max peuvent être une valeur fixe, une formule ([level]*2) ou une table par caractéristique ;
     * ils sont évalués avec les variables fournies (ex. level, vitality). Sans variables, formules/tables
     * sont évaluées avec 0 pour les variables manquantes.
     *
     * @param  array<string, int|float>  $variables  Contexte pour l'évaluation (ex. ['level' => 5, 'vitality' => 10])
     * @return array{min: int, max: int}|null
     */
    public function getLimits(string $characteristicKey, string $entity, array $variables = []): ?array
    {
        $def = $this->getDefinition($characteristicKey, $entity);
        if ($def === null) {
            return null;
        }
        $minVal = $this->resolveLimitValue($def['min'] ?? null, $variables);
        $maxVal = $this->resolveLimitValue($def['max'] ?? null, $variables);
        if ($minVal === null || $maxVal === null) {
            return null;
        }

        return [
            'min' => (int) $minVal,
            'max' => (int) $maxVal,
        ];
    }

    /**
     * Retourne les limites pour un champ de données (nom de colonne ou clé) et une entité.
     *
     * @param  array<string, int|float>  $variables  Contexte pour l'évaluation des formules min/max
     * @return array{min: int, max: int}|null
     */
    public function getLimitsByField(string $field, string $entity, array $variables = []): ?array
    {
        $key = $this->resolveFieldToKey($field, $entity);

        return $key !== null ? $this->getLimits($key, $entity, $variables) : null;
    }

    /**
     * Retourne la définition complète d'une caractéristique à partir d'un nom de champ (colonne ou clé).
     * Utilisé par le service Limit pour valider selon le type (boolean, list, string).
     *
     * @return array<string, mixed>|null
     */
    public function getDefinitionByField(string $field, string $entity): ?array
    {
        $key = $this->resolveFieldToKey($field, $entity);

        return $key !== null ? $this->getDefinition($key, $entity) : null;
    }

    /**
     * Retourne le groupe (creature, object, spell) pour une entité.
     */
    public function getGroupForEntity(string $entity): string
    {
        if (in_array($entity, self::GROUP_CREATURE, true)) {
            return 'creature';
        }
        if (in_array($entity, self::GROUP_OBJECT, true)) {
            return 'object';
        }
        if (in_array($entity, self::GROUP_SPELL, true)) {
            return 'spell';
        }

        return 'object';
    }

    /**
     * Résout un nom de champ (ex. level, life) ou un nom court (ex. level → level_creature) en clé BDD pour une entité.
     * Accepte la clé complète, le db_column, ou le nom court sans suffixe (_creature, _object, _spell).
     *
     * Une requête indexée par entité (cache applicatif) remplace le chargement complet des pivots à chaque résolution.
     */
    private function resolveFieldToKey(string $field, string $entity): ?string
    {
        $map = $this->getCachedFieldToKeyMap($entity);
        if (isset($map[$field])) {
            return $map[$field];
        }

        $suffix = $this->fieldKeySuffixForEntity($entity);
        if ($suffix === null) {
            return null;
        }

        $candidate = $field.$suffix;

        return $this->getDefinition($candidate, $entity) !== null ? $candidate : null;
    }

    /**
     * Index : clé complète, db_column, alias — pour une entité donnée.
     * Lignes `entity = *` puis surcharge entité (la surcharge écrase les alias en doublon).
     *
     * @return array<string, string>
     */
    private function getCachedFieldToKeyMap(string $entity): array
    {
        if (! in_array($entity, self::FIELD_MAP_ENTITIES, true)) {
            return [];
        }

        $cacheKey = 'characteristic.field_map.v1.'.$entity;

        return Cache::remember($cacheKey, self::FIELD_MAP_CACHE_TTL, function () use ($entity): array {
            return $this->buildFieldToKeyMap($entity);
        });
    }

    /**
     * @return array<string, string>
     */
    private function buildFieldToKeyMap(string $entity): array
    {
        if (in_array($entity, self::GROUP_CREATURE, true)) {
            return $this->accumulateFieldAliases(
                CharacteristicCreature::query()
                    ->whereIn('entity', [$entity, self::ENTITY_ALL], 'and', false)
                    ->with(['characteristic'])
                    ->get()
            );
        }
        if (in_array($entity, self::GROUP_OBJECT, true)) {
            return $this->accumulateFieldAliases(
                CharacteristicObject::query()
                    ->whereIn('entity', [$entity, self::ENTITY_ALL], 'and', false)
                    ->with(['characteristic'])
                    ->get()
            );
        }
        if (in_array($entity, self::GROUP_SPELL, true)) {
            return $this->accumulateFieldAliases(
                CharacteristicSpell::query()
                    ->whereIn('entity', [$entity, self::ENTITY_ALL], 'and', false)
                    ->with(['characteristic'])
                    ->get()
            );
        }

        return [];
    }

    /**
     * @param  iterable<int, CharacteristicCreature|CharacteristicObject|CharacteristicSpell>  $rows
     * @return array<string, string>
     */
    private function accumulateFieldAliases(iterable $rows): array
    {
        $sorted = collect($rows)->sortBy(fn ($r) => $r->entity === self::ENTITY_ALL ? 0 : 1)->values();
        $map = [];
        foreach ($sorted as $row) {
            $char = $row->characteristic;
            if ($char === null) {
                continue;
            }
            $key = $char->key;
            if ($key !== '') {
                $map[$key] = $key;
            }
            $col = $row->db_column;
            if (is_string($col) && $col !== '') {
                $map[$col] = $key;
            }
        }

        return $map;
    }

    private function fieldKeySuffixForEntity(string $entity): ?string
    {
        if (in_array($entity, self::GROUP_CREATURE, true)) {
            return '_creature';
        }
        if (in_array($entity, self::GROUP_OBJECT, true)) {
            return '_object';
        }
        if (in_array($entity, self::GROUP_SPELL, true)) {
            return '_spell';
        }

        return null;
    }

    /**
     * Retourne la formule de conversion Dofus → Krosmoz pour une caractéristique et une entité.
     */
    public function getConversionFormula(string $characteristicKey, string $entity): ?string
    {
        $def = $this->getDefinition($characteristicKey, $entity);
        $formula = $def['conversion_formula'] ?? null;

        return is_string($formula) && trim($formula) !== '' ? $formula : null;
    }

    /**
     * Retourne l'identifiant de la fonction de conversion optionnelle pour une caractéristique et une entité.
     */
    public function getConversionFunctionId(string $characteristicKey, string $entity): ?string
    {
        $def = $this->getDefinition($characteristicKey, $entity);
        $id = $def['conversion_function'] ?? null;

        return is_string($id) && trim($id) !== '' ? $id : null;
    }

    /** Cache TTL pour le mapping dofusdb_id → characteristic_key par groupe (secondes). */
    private const DOFUSDB_TO_KEY_CACHE_TTL = 3600;

    /**
     * Retourne le mapping complet dofusdb_characteristic_id → characteristic_key pour un groupe (une requête, mis en cache).
     * À utiliser en batch pour éviter N+1 (ex. itemEffectsToKrosmozBonus avec plusieurs effets par item).
     *
     * @param  'object'|'creature'|'spell'  $group
     * @return array<int, string> dofusdb_characteristic_id => characteristic key
     */
    public function getDofusdbToCharacteristicKeyMap(string $group): array
    {
        $cacheKey = 'characteristic.dofusdb_to_key.'.$group;

        return Cache::remember($cacheKey, self::DOFUSDB_TO_KEY_CACHE_TTL, function () use ($group): array {
            $query = match ($group) {
                'object' => CharacteristicObject::query()
                    ->whereNotNull('dofusdb_characteristic_id', 'and')
                    ->with(['characteristic']),
                'creature' => CharacteristicCreature::query()
                    ->whereNotNull('dofusdb_characteristic_id', 'and')
                    ->with(['characteristic']),
                'spell' => CharacteristicSpell::query()
                    ->whereNotNull('dofusdb_characteristic_id', 'and')
                    ->with(['characteristic']),
                default => null,
            };
            if ($query === null) {
                return [];
            }
            $map = [];
            foreach ($query->get() as $row) {
                $char = $row->characteristic;
                if ($char instanceof Characteristic && $char->key !== '') {
                    $map[$row->dofusdb_characteristic_id] = $char->key;
                }
            }

            return $map;
        });
    }

    /**
     * Résout un id DofusDB (GET /characteristics) vers la clé Krosmoz de la caractéristique (M2).
     * Pour plusieurs résolutions (ex. boucle effets), préférer getDofusdbToCharacteristicKeyMap() pour éviter N+1.
     *
     * @param  'object'|'creature'|'spell'  $group  Groupe de caractéristiques (table de groupe)
     *
     * @example getCharacteristicKeyByDofusdbCharacteristicId(10, 'object') === 'strength_object'
     */
    public function getCharacteristicKeyByDofusdbCharacteristicId(int $dofusdbCharacteristicId, string $group): ?string
    {
        $map = $this->getDofusdbToCharacteristicKeyMap($group);

        return $map[$dofusdbCharacteristicId] ?? null;
    }

    /** Invalide les caches du getter (dofusdb_id → key par groupe). À appeler après mise à jour des tables de groupe. */
    public function clearCache(): void
    {
        foreach (['object', 'creature', 'spell'] as $group) {
            Cache::forget('characteristic.dofusdb_to_key.'.$group);
        }
        foreach (self::FIELD_MAP_ENTITIES as $ent) {
            Cache::forget('characteristic.field_map.v1.'.$ent);
        }
        $this->definitionMemo = [];
    }

    /**
     * Trouve la ligne de base (entity='*') et la surcharge (entity spécifique) pour characteristic_id + entity.
     * Permet d'affiner les propriétés du groupe pour une entité précise (ex. formule PV pour monster uniquement).
     *
     * @return array{0: CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null, 1: CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null}
     */
    private function findGroupRows(int $characteristicId, string $entity): array
    {
        if (in_array($entity, self::GROUP_CREATURE, true)) {
            $rows = CharacteristicCreature::query()
                ->where('characteristic_id', '=', $characteristicId)
                ->whereIn('entity', [$entity, self::ENTITY_ALL], 'and', false)
                ->get();

            return [
                $rows->firstWhere('entity', self::ENTITY_ALL),
                $rows->firstWhere('entity', $entity),
            ];
        }
        if (in_array($entity, self::GROUP_OBJECT, true)) {
            $rows = CharacteristicObject::query()
                ->where('characteristic_id', '=', $characteristicId)
                ->whereIn('entity', [$entity, self::ENTITY_ALL], 'and', false)
                ->with(['allowedItemTypes'])
                ->get();

            return [
                $rows->firstWhere('entity', self::ENTITY_ALL),
                $rows->firstWhere('entity', $entity),
            ];
        }
        if (in_array($entity, self::GROUP_SPELL, true)) {
            $rows = CharacteristicSpell::query()
                ->where('characteristic_id', '=', $characteristicId)
                ->whereIn('entity', [$entity, self::ENTITY_ALL], 'and', false)
                ->get();

            return [
                $rows->firstWhere('entity', self::ENTITY_ALL),
                $rows->firstWhere('entity', $entity),
            ];
        }

        return [null, null];
    }

    /**
     * Fusionne base et overlay : pour chaque propriété du groupe, la valeur non nulle de l'overlay l'emporte.
     */
    private function pickGroupValue(
        CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null $base,
        CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null $overlay,
        string $attribute
    ): mixed {
        $overlayVal = $overlay !== null ? $overlay->getAttribute($attribute) : null;
        if ($overlayVal !== null && $overlayVal !== '') {
            return $overlayVal;
        }

        return $base !== null ? $base->getAttribute($attribute) : null;
    }

    /**
     * Résout une limite (min ou max) : valeur fixe, formule ou table → entier.
     *
     * @param  mixed  $value  Valeur en BDD (string numérique, formule ou JSON table)
     * @param  array<string, int|float>  $variables  Contexte pour l'évaluation
     */
    private function resolveLimitValue(mixed $value, array $variables): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        $s = trim((string) $value);
        if ($s === '') {
            return null;
        }
        if (is_numeric($s)) {
            return (int) (float) $s;
        }
        $evaluated = $this->formulaResolution->evaluate($s, $variables);
        if ($evaluated === null) {
            return null;
        }

        return (int) round($evaluated);
    }

    /**
     * Fusionne la caractéristique générale et les lignes de groupe (base + surcharge entité) en un seul tableau.
     * Les propriétés non généralistes (min, max, formula, etc.) sont prises sur la surcharge si non vides, sinon sur la base.
     *
     * @param  CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null  $base  Ligne entity='*'
     * @param  CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null  $overlay  Ligne entity précise (ex. monster)
     * @return array<string, mixed>
     */
    private function mergeDefinition(
        Characteristic $characteristic,
        CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null $base,
        CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null $overlay,
        string $entity
    ): array {
        $row = $overlay ?? $base;
        $out = [
            'id' => $characteristic->id,
            'key' => $characteristic->key,
            'name' => $characteristic->name,
            'short_name' => $characteristic->short_name,
            'helper' => $characteristic->helper,
            'descriptions' => $characteristic->descriptions,
            'icon' => $characteristic->icon,
            'color' => $characteristic->color,
            'unit' => $characteristic->unit,
            'type' => $characteristic->type,
            'hide_when_empty' => (bool) ($characteristic->hide_when_empty ?? false),
            'hide_when_false' => Schema::hasColumn('characteristics', 'hide_when_false')
                ? (bool) ($characteristic->hide_when_false ?? false)
                : false,
            'entity' => $entity,
            'db_column' => $this->pickGroupValue($base, $overlay, 'db_column') ?? $characteristic->key,
            'min' => $this->pickGroupValue($base, $overlay, 'min'),
            'max' => $this->pickGroupValue($base, $overlay, 'max'),
            'formula' => $this->pickGroupValue($base, $overlay, 'formula'),
            'formula_display' => $this->pickGroupValue($base, $overlay, 'formula_display'),
            'default_value' => $this->pickGroupValue($base, $overlay, 'default_value'),
            'conversion_formula' => $this->pickGroupValue($base, $overlay, 'conversion_formula'),
            'conversion_function' => $this->pickGroupValue($base, $overlay, 'conversion_function'),
            'conversion_dofus_sample' => $this->pickGroupValue($base, $overlay, 'conversion_dofus_sample'),
            'conversion_krosmoz_sample' => $this->pickGroupValue($base, $overlay, 'conversion_krosmoz_sample'),
        ];
        if ($row instanceof CharacteristicObject) {
            $out['forgemagie_max'] = $this->pickGroupValue($base, $overlay, 'forgemagie_max') ?? $row->forgemagie_max;
            $out['base_price_per_unit'] = $this->pickGroupValue($base, $overlay, 'base_price_per_unit');
            $out['rune_price_per_unit'] = $this->pickGroupValue($base, $overlay, 'rune_price_per_unit');
            $out['value_available'] = $this->pickGroupValue($base, $overlay, 'value_available') ?? $row->value_available;
            $overlayObj = $overlay instanceof CharacteristicObject ? $overlay : null;
            $baseObj = $base instanceof CharacteristicObject ? $base : null;
            if ($baseObj !== null) {
                $baseObj->loadMissing('allowedItemTypes');
            }
            if ($overlayObj !== null) {
                $overlayObj->loadMissing('allowedItemTypes');
            }
            $effectiveItemTypes = ($overlayObj && $overlayObj->allowedItemTypes->isNotEmpty())
                ? $overlayObj->allowedItemTypes
                : ($baseObj ? $baseObj->allowedItemTypes : collect());
            $restricted = $effectiveItemTypes->isNotEmpty();
            $out['allowed_item_type_restricted'] = $restricted;
            $out['allowed_item_type_ids'] = $restricted ? $effectiveItemTypes->pluck('id')->values()->all() : [];
        }
        if ($row instanceof CharacteristicCreature) {
            $out['labels'] = $this->pickGroupValue($base, $overlay, 'labels') ?? $row->labels;
            $out['validation'] = $this->pickGroupValue($base, $overlay, 'validation') ?? $row->validation;
        }
        if ($row instanceof CharacteristicSpell) {
            $out['value_available'] = $this->pickGroupValue($base, $overlay, 'value_available') ?? $row->value_available;
        }

        $out['norms_grid'] = $this->pickGroupValue($base, $overlay, 'norms_grid');
        $out['norms_formula'] = $this->pickGroupValue($base, $overlay, 'norms_formula');
        $out['norms_conditions'] = $this->pickGroupValue($base, $overlay, 'norms_conditions');
        $out['norms_description'] = $this->pickGroupValue($base, $overlay, 'norms_description');
        $out['norms_help_section_id'] = $this->pickGroupValue($base, $overlay, 'norms_help_section_id');

        return $out;
    }

    /**
     * Retourne la ligne de base (entity='*' ou équivalent) pour une caractéristique maître,
     * en cherchant dans les trois tables de groupe. Utilisé pour les caractéristiques liées.
     */
    private function findMasterBaseRow(Characteristic $characteristic): CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null
    {
        $id = $characteristic->id;

        // On privilégie la ligne avec entity='*' si elle existe.
        $row = CharacteristicCreature::query()
            ->where('characteristic_id', '=', $id)
            ->where('entity', '=', self::ENTITY_ALL)
            ->first();
        if ($row !== null) {
            return $row;
        }

        $row = CharacteristicObject::query()
            ->where('characteristic_id', '=', $id)
            ->where('entity', '=', self::ENTITY_ALL)
            ->first();
        if ($row !== null) {
            return $row;
        }

        $row = CharacteristicSpell::query()
            ->where('characteristic_id', '=', $id)
            ->where('entity', '=', self::ENTITY_ALL)
            ->first();
        if ($row !== null) {
            return $row;
        }

        // Fallback : première ligne trouvée dans l'une des tables.
        $row = CharacteristicCreature::query()
            ->where('characteristic_id', '=', $id)
            ->first();
        if ($row !== null) {
            return $row;
        }

        $row = CharacteristicObject::query()
            ->where('characteristic_id', '=', $id)
            ->first();
        if ($row !== null) {
            return $row;
        }

        return CharacteristicSpell::query()
            ->where('characteristic_id', '=', $id)
            ->first();
    }
}
