<?php

declare(strict_types=1);

namespace App\Services\Characteristics;

use App\Enums\CharacteristicNature;
use App\Models\Characteristic;
use App\Models\CharacteristicConsumable;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicProfile;
use App\Models\CharacteristicSpell;
use App\Support\Characteristics\CharacteristicDefinitionJson;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Écrit les fichiers `stem-groupe-definition.json` depuis l’état actuel des tables SQL
 * (remplace l’ancien export en 4 fichiers PHP).
 */
final class CharacteristicDefinitionsExportFromDatabaseService
{
    /**
     * @return int nombre de fichiers écrits
     */
    public function exportToDataDirectory(): int
    {
        $written = 0;
        $chars = Characteristic::query()
            ->with(['masterCharacteristic', 'intrinsicCharacteristic'])
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get();

        foreach ($chars as $char) {
            $parsed = CharacteristicDefinitionNaming::parseCharacteristicKey($char->key);
            if ($parsed === null) {
                continue;
            }
            $stem = $parsed['stem'];
            $group = $parsed['group'];

            $entities = $this->buildEntitiesFromDatabase($char, $group);
            if ($entities === []) {
                continue;
            }

            $payload = [
                '_schema_version' => '1',
                'characteristic' => $this->buildCharacteristicBlock($char),
                'entities' => $entities,
                'relations' => null,
            ];

            $relPath = CharacteristicDefinitionNaming::definitionRelativePath($stem, $group);
            $abs = base_path($relPath);
            File::ensureDirectoryExists(dirname($abs));
            File::put($abs, CharacteristicDefinitionJson::encodePretty($payload));
            $written++;
        }

        return $written;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCharacteristicBlock(Characteristic $c): array
    {
        return [
            'key' => $c->key,
            'name' => $c->name,
            'short_name' => $c->short_name,
            'helper' => $c->helper,
            'descriptions' => $c->descriptions,
            'icon' => $c->icon,
            'icon_false' => $c->icon_false,
            'color' => $c->color,
            'value_overrides' => $c->value_overrides,
            'hide_when_empty' => $c->hide_when_empty,
            'hide_when_false' => Schema::hasColumn('characteristics', 'hide_when_false')
                ? (bool) ($c->hide_when_false ?? false)
                : false,
            'unit' => $c->unit,
            'type' => $c->type,
            'status' => $c->status ?? Characteristic::STATUS_A_VALIDER,
            'sort_order' => $c->sort_order,
            'group' => $c->group,
            'nature' => $c->nature instanceof CharacteristicNature
                ? $c->nature->value
                : ($c->nature !== null ? (string) $c->nature : null),
            'linked_to_key' => $c->masterCharacteristic?->key,
            'intrinsic_key' => $c->intrinsicCharacteristic?->key,
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function buildEntitiesFromDatabase(Characteristic $char, string $group): array
    {
        $entities = [];
        if ($group === CharacteristicDefinitionNaming::GROUP_CREATURE) {
            $rows = CharacteristicCreature::query()->where('characteristic_id', $char->id)->orderBy('entity')->get();
            foreach ($rows as $r) {
                $entities[$r->entity] = $this->filterCreatureEntity($r);
            }
        } elseif ($group === CharacteristicDefinitionNaming::GROUP_OBJECT) {
            $rows = CharacteristicObject::query()->where('characteristic_id', $char->id)->with('allowedItemTypes')->orderBy('entity')->get();
            foreach ($rows as $r) {
                $entities[$r->entity] = $this->filterObjectEntity($r);
            }
        } elseif ($group === CharacteristicDefinitionNaming::GROUP_SPELL) {
            $rows = CharacteristicSpell::query()->where('characteristic_id', $char->id)->orderBy('entity')->get();
            foreach ($rows as $r) {
                $entities[$r->entity] = $this->filterSpellEntity($r);
            }
        } elseif ($group === CharacteristicDefinitionNaming::GROUP_CONSUMABLE) {
            $rows = CharacteristicConsumable::query()->where('characteristic_id', $char->id)->orderBy('entity')->get();
            foreach ($rows as $r) {
                $entities[$r->entity] = $this->filterSimpleGroupEntity($r);
            }
        } elseif ($group === CharacteristicDefinitionNaming::GROUP_PROFILE) {
            $rows = CharacteristicProfile::query()->where('characteristic_id', $char->id)->orderBy('entity')->get();
            foreach ($rows as $r) {
                $entities[$r->entity] = $this->filterSimpleGroupEntity($r);
            }
        }

        return $entities;
    }

    /**
     * @return array<string, mixed>
     */
    private function filterSimpleGroupEntity(CharacteristicConsumable|CharacteristicProfile $r): array
    {
        $conversionFunction = $r->conversion_function;
        if ($conversionFunction === '') {
            $conversionFunction = null;
        }

        return [
            'dofusdb_characteristic_id' => $r->dofusdb_characteristic_id,
            'db_column' => $r->db_column,
            'min' => $r->min,
            'max' => $r->max,
            'formula' => $r->formula,
            'formula_display' => $r->formula_display,
            'default_value' => $r->default_value,
            'conversion_formula' => $r->conversion_formula,
            'conversion_function' => $conversionFunction,
            'conversion_dofus_sample' => $r->conversion_dofus_sample,
            'conversion_krosmoz_sample' => $r->conversion_krosmoz_sample,
            'conversion_sample_rows' => $r->conversion_sample_rows,
            'norms_grid' => $r->norms_grid,
            'norms_formula' => $r->norms_formula,
            'norms_conditions' => $r->norms_conditions,
            'norms_description' => $r->norms_description,
            'norms_help_section_id' => $r->norms_help_section_id,
            'value_available' => $r->value_available,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function filterCreatureEntity(CharacteristicCreature $r): array
    {
        $conversionFunction = $r->conversion_function;
        if ($conversionFunction === '') {
            $conversionFunction = null;
        }

        return [
            'dofusdb_characteristic_id' => $r->dofusdb_characteristic_id,
            'db_column' => $r->db_column,
            'min' => $r->min,
            'max' => $r->max,
            'formula' => $r->formula,
            'formula_display' => $r->formula_display,
            'default_value' => $r->default_value,
            'conversion_formula' => $r->conversion_formula,
            'conversion_function' => $conversionFunction,
            'conversion_dofus_sample' => $r->conversion_dofus_sample,
            'conversion_krosmoz_sample' => $r->conversion_krosmoz_sample,
            'conversion_sample_rows' => $r->conversion_sample_rows,
            'norms_grid' => $r->norms_grid,
            'norms_formula' => $r->norms_formula,
            'norms_conditions' => $r->norms_conditions,
            'norms_description' => $r->norms_description,
            'norms_help_section_id' => $r->norms_help_section_id,
            'limit_absolute' => $r->limit_absolute,
            'labels' => $r->labels,
            'validation' => $r->validation,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function filterObjectEntity(CharacteristicObject $r): array
    {
        $conversionFunction = $r->conversion_function;
        if ($conversionFunction === '') {
            $conversionFunction = null;
        }

        $allowedItemTypes = $r->relationLoaded('allowedItemTypes') ? $r->allowedItemTypes : collect();
        $itemTypeDofusIds = $allowedItemTypes
            ->pluck('dofusdb_type_id')
            ->filter(static fn ($id): bool => $id !== null && $id !== '')
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();
        $itemTypeIds = $allowedItemTypes->pluck('id')->map(fn ($id): int => (int) $id)->values()->all();
        $usePortableDofusIds = $itemTypeDofusIds !== []
            && count($itemTypeDofusIds) === $allowedItemTypes->count();

        return [
            'dofusdb_characteristic_id' => $r->dofusdb_characteristic_id,
            'db_column' => $r->db_column,
            'min' => $r->min,
            'max' => $r->max,
            'formula' => $r->formula,
            'formula_display' => $r->formula_display,
            'default_value' => $r->default_value,
            'conversion_formula' => $r->conversion_formula,
            'conversion_function' => $conversionFunction,
            'conversion_dofus_sample' => $r->conversion_dofus_sample,
            'conversion_krosmoz_sample' => $r->conversion_krosmoz_sample,
            'conversion_sample_rows' => $r->conversion_sample_rows,
            'norms_grid' => $r->norms_grid,
            'norms_formula' => $r->norms_formula,
            'norms_conditions' => $r->norms_conditions,
            'norms_description' => $r->norms_description,
            'norms_help_section_id' => $r->norms_help_section_id,
            'forgemagie_max' => (int) $r->forgemagie_max,
            'base_price_per_unit' => $r->base_price_per_unit !== null ? (float) $r->base_price_per_unit : null,
            'rune_price_per_unit' => $r->rune_price_per_unit !== null ? (float) $r->rune_price_per_unit : null,
            'value_available' => $r->value_available,
            'item_type_dofus_ids' => $usePortableDofusIds ? $itemTypeDofusIds : null,
            'item_type_ids' => ! $usePortableDofusIds && $itemTypeIds !== [] ? $itemTypeIds : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function filterSpellEntity(CharacteristicSpell $r): array
    {
        $conversionFunction = $r->conversion_function;
        if ($conversionFunction === '') {
            $conversionFunction = null;
        }

        return [
            'dofusdb_characteristic_id' => $r->dofusdb_characteristic_id,
            'db_column' => $r->db_column,
            'min' => $r->min,
            'max' => $r->max,
            'formula' => $r->formula,
            'formula_display' => $r->formula_display,
            'default_value' => $r->default_value,
            'conversion_formula' => $r->conversion_formula,
            'conversion_function' => $conversionFunction,
            'conversion_dofus_sample' => $r->conversion_dofus_sample,
            'conversion_krosmoz_sample' => $r->conversion_krosmoz_sample,
            'conversion_sample_rows' => $r->conversion_sample_rows,
            'norms_grid' => $r->norms_grid,
            'norms_formula' => $r->norms_formula,
            'norms_conditions' => $r->norms_conditions,
            'norms_description' => $r->norms_description,
            'norms_help_section_id' => $r->norms_help_section_id,
            'value_available' => $r->value_available,
        ];
    }
}
