<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Admin;

use App\Enums\CharacteristicNature;
use App\Models\Characteristic;
use App\Models\CharacteristicConsumable;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicProfile;
use App\Models\CharacteristicSpell;
use App\Services\Characteristic\Conversion\ConversionFunctionRegistry;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Config\ScrappingMappingService;
use Illuminate\Support\Facades\Schema;

/**
 * Construit le payload Inertia pour la page « show » d'une caractéristique (selected, mappings, etc.).
 * Une seule responsabilité : assembler les données d'affichage à partir du modèle et des services.
 *
 * @see docs/features/characteristics/README.md
 */
final class CharacteristicShowPayloadBuilder
{
    private const ENTITIES_BY_GROUP = [
        'creature' => ['*', 'monster', 'class', 'npc'],
        'object' => ['*', 'item', 'panoply'],
        'spell' => ['*', 'spell'],
        'consumable' => ['*', 'consumable'],
        'profile' => ['*', 'breed', 'specialization', 'monster'],
    ];

    /** Entités DofusDB pour le panneau Mapping (une ligne par entité, modal « Lier »). */
    private const SCRAPPING_MAPPING_ENTITIES_BY_GROUP = [
        'creature' => ['monster', 'breed'],
        'object' => ['item', 'panoply'],
        'spell' => ['spell'],
        'consumable' => ['consumable'],
        'profile' => [],
    ];

    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly ScrappingMappingService $mappingService,
        private readonly ConversionFunctionRegistry $conversionFunctionRegistry
    ) {}

    /**
     * Payload pour la vue show : selected, scrappingMappingsUsingThis, characteristicsForConvertToLinked.
     *
     * @return array{selected: array<string, mixed>, scrappingMappingsUsingThis: list<array>, characteristicsForConvertToLinked: list<array>}
     */
    public function build(Characteristic $characteristic): array
    {
        $characteristic->loadMissing(['masterCharacteristic', 'intrinsicCharacteristic']);
        $effective = $characteristic->effectiveCharacteristic();
        $group = $this->inferPrimaryGroup($characteristic);
        $entitiesForGroup = self::ENTITIES_BY_GROUP[$group] ?? ['*'];

        $buildResult = $this->buildEntities($characteristic, $characteristic->key, $group, $entitiesForGroup);
        $entities = $buildResult['entities'];
        $entityOverrideKeys = $buildResult['entity_override_keys'];
        $conversionFormulas = $this->buildConversionFormulas($characteristic->key, $entitiesForGroup);

        $hasIconFalse = Schema::hasColumn('characteristics', 'icon_false');
        $hasValueOverrides = Schema::hasColumn('characteristics', 'value_overrides');
        $hasHideWhenEmpty = Schema::hasColumn('characteristics', 'hide_when_empty');
        $hasHideWhenFalse = Schema::hasColumn('characteristics', 'hide_when_false');
        $nature = $effective->natureEnum();

        $selected = [
            'id' => $characteristic->key,
            'name' => $effective->name,
            'short_name' => $effective->short_name,
            'description' => $effective->descriptions,
            'helper' => $effective->helper,
            'icon' => $effective->icon,
            'icon_false' => $hasIconFalse ? $effective->icon_false : null,
            'color' => $effective->color,
            'value_overrides' => $hasValueOverrides ? $effective->value_overrides : null,
            'hide_when_empty' => $hasHideWhenEmpty ? (bool) $effective->hide_when_empty : false,
            'hide_when_false' => $hasHideWhenFalse ? (bool) ($effective->hide_when_false ?? false) : false,
            'type' => $effective->type,
            'status' => Schema::hasColumn('characteristics', 'status') ? ($effective->status ?? Characteristic::STATUS_A_VALIDER) : Characteristic::STATUS_A_VALIDER,
            'unit' => $effective->unit,
            'sort_order' => $characteristic->sort_order,
            'entities' => $entities,
            'entity_override_keys' => $entityOverrideKeys,
            'conversion_formulas' => $conversionFormulas,
            'conversionFunctionOptions' => $this->conversionFunctionRegistry->options(),
            'group' => $group,
            'nature' => $nature?->value,
            'nature_label' => $nature?->label(),
            'intrinsic_key' => $characteristic->intrinsicCharacteristic?->key,
            'intrinsic_characteristic_id' => $characteristic->intrinsic_characteristic_id,
            'natures' => array_map(
                static fn (CharacteristicNature $n): array => ['value' => $n->value, 'label' => $n->label()],
                CharacteristicNature::cases()
            ),
            'entitiesByGroup' => self::ENTITIES_BY_GROUP,
            'scrappingMappingEntities' => self::SCRAPPING_MAPPING_ENTITIES_BY_GROUP[$group] ?? [],
            'is_linked' => $characteristic->isLinked(),
            'master_key' => $characteristic->isLinked() ? $effective->key : null,
        ];

        $scrappingMappingsUsingThis = $this->mappingService->listMappingsForCharacteristic($characteristic->id);
        $characteristicsForConvertToLinked = [];
        if (! $characteristic->isLinked()) {
            $characteristicsForConvertToLinked = Characteristic::whereNull('linked_to_characteristic_id')
                ->where('id', '!=', $characteristic->id)
                ->orderBy('sort_order')
                ->orderBy('key')
                ->get()
                ->map(fn (Characteristic $c): array => [
                    'id' => $c->id,
                    'key' => $c->key,
                    'name' => $c->name,
                    'group' => $c->group ?? $this->inferPrimaryGroup($c),
                ])
                ->all();
        }

        return [
            'selected' => $selected,
            'scrappingMappingsUsingThis' => $scrappingMappingsUsingThis,
            'characteristicsForConvertToLinked' => $characteristicsForConvertToLinked,
        ];
    }

    /**
     * Construit la liste des entités pour le formulaire et les clés des spécificités réellement en BDD.
     * Garantit une ligne par entité du groupe (dont '*') pour le panneau Conversion.
     *
     * @return array{entities: list<array<string, mixed>>, entity_override_keys: list<string>}
     */
    private function buildEntities(Characteristic $characteristic, string $characteristicKey, string $group, array $entitiesForGroup): array
    {
        $entities = [];
        $entityOverrideKeys = [];
        if ($characteristic->isLinked()) {
            foreach ($entitiesForGroup as $entity) {
                $def = $this->getter->getDefinition($characteristicKey, $entity);
                $entities[] = $def !== null ? $this->definitionToEntityRow($def) : $this->defaultEntityRow($entity);
            }
        } else {
            $byEntity = [];
            foreach (CharacteristicCreature::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
                $ent = $this->groupRowToEntity($row, $characteristic);
                $byEntity[$ent['entity'] ?? ''] = $ent;
            }
            foreach (CharacteristicObject::with('allowedItemTypes')->where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
                $ent = $this->groupRowToEntity($row, $characteristic);
                $byEntity[$ent['entity'] ?? ''] = $ent;
            }
            foreach (CharacteristicSpell::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
                $ent = $this->groupRowToEntity($row, $characteristic);
                $byEntity[$ent['entity'] ?? ''] = $ent;
            }
            foreach (CharacteristicConsumable::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
                $ent = $this->groupRowToEntity($row, $characteristic);
                $byEntity[$ent['entity'] ?? ''] = $ent;
            }
            foreach (CharacteristicProfile::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
                $ent = $this->groupRowToEntity($row, $characteristic);
                $byEntity[$ent['entity'] ?? ''] = $ent;
            }
            $entityOverrideKeys = array_values(array_filter(
                array_keys($byEntity),
                fn (string $e): bool => $e !== '*'
            ));
            foreach ($entitiesForGroup as $entity) {
                $entities[] = $byEntity[$entity] ?? $this->defaultEntityRow($entity);
            }
        }

        return ['entities' => $entities, 'entity_override_keys' => $entityOverrideKeys];
    }

    /**
     * Ligne d'entité par défaut (pour garantir la présence de '*' et des autres entités du groupe).
     *
     * @return array<string, mixed>
     */
    private function defaultEntityRow(string $entity): array
    {
        return [
            'entity' => $entity,
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
            'conversion_sample_rows' => $this->defaultConversionSampleRows(),
            'forgemagie_max' => 0,
            'base_price_per_unit' => null,
            'rune_price_per_unit' => null,
            'norms_grid' => null,
            'norms_formula' => null,
            'norms_power_coefficients' => null,
            'norms_conditions' => null,
            'norms_description' => null,
            'norms_help_section_id' => null,
            'limit_absolute' => null,
            'item_type_ids' => [],
        ];
    }

    /**
     * @return list<array{dofus_level: int, dofus_value: mixed, krosmoz_level: int, krosmoz_value: mixed}>
     */
    private function defaultConversionSampleRows(): array
    {
        return [
            ['dofus_level' => 1, 'dofus_value' => '', 'krosmoz_level' => 1, 'krosmoz_value' => ''],
            ['dofus_level' => 200, 'dofus_value' => '', 'krosmoz_level' => 20, 'krosmoz_value' => ''],
        ];
    }

    /**
     * @return list<array{entity: string, conversion_formula: string, formula_display: string, handler_name: string}>
     */
    private function buildConversionFormulas(string $characteristicKey, array $entitiesForGroup): array
    {
        $out = [];
        foreach ($entitiesForGroup as $entity) {
            if ($entity === '*') {
                continue;
            }
            $def = $this->getter->getDefinition($characteristicKey, $entity);
            $out[] = [
                'entity' => $entity,
                'conversion_formula' => $def['conversion_formula'] ?? '',
                'formula_display' => $def['formula_display'] ?? '',
                'handler_name' => '',
            ];
        }

        return $out;
    }

    private function inferPrimaryGroup(Characteristic $characteristic): string
    {
        if (is_string($characteristic->group) && $characteristic->group !== '') {
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

    /**
     * @param  array<string, mixed>  $def
     * @return array<string, mixed>
     */
    private function definitionToEntityRow(array $def): array
    {
        $out = [
            'entity' => $def['entity'] ?? '*',
            'db_column' => $def['db_column'] ?? null,
            'min' => $def['min'] ?? null,
            'max' => $def['max'] ?? null,
            'formula' => $def['formula'] ?? null,
            'formula_display' => $def['formula_display'] ?? null,
            'default_value' => $def['default_value'] ?? null,
            'conversion_formula' => $def['conversion_formula'] ?? null,
            'conversion_function' => $def['conversion_function'] ?? null,
            'conversion_dofus_sample' => $def['conversion_dofus_sample'] ?? null,
            'conversion_krosmoz_sample' => $def['conversion_krosmoz_sample'] ?? null,
            'conversion_sample_rows' => null,
            'forgemagie_max' => 0,
            'base_price_per_unit' => null,
            'rune_price_per_unit' => null,
            'norms_grid' => $def['norms_grid'] ?? null,
            'norms_formula' => $def['norms_formula'] ?? null,
            'norms_power_coefficients' => $def['norms_power_coefficients'] ?? null,
            'norms_conditions' => $def['norms_conditions'] ?? null,
            'norms_description' => $def['norms_description'] ?? null,
            'norms_help_section_id' => $def['norms_help_section_id'] ?? null,
            'limit_absolute' => $def['limit_absolute'] ?? null,
        ];
        if (isset($def['forgemagie_max'])) {
            $out['forgemagie_max'] = (int) $def['forgemagie_max'];
        }
        if (array_key_exists('base_price_per_unit', $def)) {
            $out['base_price_per_unit'] = $def['base_price_per_unit'];
        }
        if (array_key_exists('rune_price_per_unit', $def)) {
            $out['rune_price_per_unit'] = $def['rune_price_per_unit'];
        }
        if (array_key_exists('allowed_item_type_ids', $def)) {
            $out['item_type_ids'] = is_array($def['allowed_item_type_ids'] ?? null)
                ? array_map('intval', $def['allowed_item_type_ids'])
                : [];
        }

        return $out;
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
            'norms_formula' => $row->norms_formula,
            'norms_power_coefficients' => $row->norms_power_coefficients,
            'norms_conditions' => $row->norms_conditions,
            'norms_description' => $row->norms_description,
            'norms_help_section_id' => $row->norms_help_section_id,
            'limit_absolute' => $row instanceof CharacteristicCreature ? $row->limit_absolute : null,
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
}
