<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use App\Models\CharacteristicObject;
use App\Models\Type\ItemType;
use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use App\Support\Seeder\SeedMode;
use Database\Seeders\Concerns\RetriesWhenMysqlSchemaChanged;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Base pour les seeders des tables characteristic_creature, characteristic_object, characteristic_spell.
 * Source : fichiers `stem-groupe-definition.json` sous {@see CharacteristicDefinitionNaming::RELATIVE_ROOT}.
 */
abstract class CharacteristicGroupSeeder extends Seeder
{
    use RetriesWhenMysqlSchemaChanged;

    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * Clé entity par défaut si absente du row (ex. '*' ou 'spell').
     */
    protected function defaultEntity(): string
    {
        return '*';
    }

    /**
     * Attributs communs à creature, object et spell (limites, formules, conversion).
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function commonAttributes(array $row): array
    {
        return [
            'dofusdb_characteristic_id' => $row['dofusdb_characteristic_id'] ?? null,
            'db_column' => $row['db_column'] ?? null,
            'min' => $row['min'] ?? null,
            'max' => $row['max'] ?? null,
            'formula' => $row['formula'] ?? null,
            'formula_display' => $row['formula_display'] ?? null,
            'default_value' => $row['default_value'] ?? null,
            'conversion_formula' => $row['conversion_formula'] ?? null,
            'conversion_function' => isset($row['conversion_function']) && $row['conversion_function'] !== '' ? $row['conversion_function'] : null,
            'conversion_dofus_sample' => $row['conversion_dofus_sample'] ?? null,
            'conversion_krosmoz_sample' => $row['conversion_krosmoz_sample'] ?? null,
            'conversion_sample_rows' => $row['conversion_sample_rows'] ?? null,
            'norms_grid' => $row['norms_grid'] ?? null,
            'norms_formula' => $row['norms_formula'] ?? null,
            'norms_conditions' => $row['norms_conditions'] ?? null,
            'norms_description' => $row['norms_description'] ?? null,
            'norms_help_section_id' => $row['norms_help_section_id'] ?? null,
        ];
    }

    /**
     * Sous-dossier de {@see CharacteristicDefinitionNaming::RELATIVE_ROOT} (creature|object|spell).
     */
    abstract protected function jsonGroupSubdirectory(): string;

    /**
     * Chemins absolus des fichiers `*-definition.json` pour ce groupe (triés).
     *
     * @return list<string>
     */
    protected function jsonDefinitionPaths(): array
    {
        $dir = base_path(CharacteristicDefinitionNaming::RELATIVE_ROOT.'/'.$this->jsonGroupSubdirectory());
        if (! is_dir($dir)) {
            return [];
        }
        $paths = glob($dir.DIRECTORY_SEPARATOR.'*-definition.json') ?: [];
        $out = [];
        foreach ($paths as $path) {
            if (is_file($path)) {
                $out[] = $path;
            }
        }
        sort($out);

        return $out;
    }

    /**
     * Ids des caractéristiques indexées par clé métier (une requête).
     *
     * @return array<string, int>
     */
    protected function characteristicIdsByKey(): array
    {
        /** @var array<string, int> */
        return Characteristic::query()->pluck('id', 'key')->all();
    }

    /**
     * Normalise les ids `item_types` pour la table pivot (entiers strictement positifs, uniques).
     *
     * @param  array<mixed>  $raw
     * @return list<int>
     */
    protected function normalizeItemTypeIdsForSync(array $raw): array
    {
        $ids = [];
        foreach ($raw as $id) {
            if (is_int($id) && $id > 0) {
                $ids[] = $id;
            } elseif (is_string($id) && $id !== '' && ctype_digit($id)) {
                $v = (int) $id;
                if ($v > 0) {
                    $ids[] = $v;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Résout les ids pivot `item_types.id` pour une ligne entité objet.
     * Si `item_type_dofus_ids` est renseigné : résolution par `dofusdb_type_id` uniquement (pas de repli
     * sur `item_type_ids`, souvent des ids BDD d’un autre environnement).
     * Sinon : `item_type_ids` filtrés pour ne garder que des ids présents en base.
     *
     * @param  array<string, mixed>  $row
     * @return list<int>
     */
    protected function resolveCharacteristicObjectItemTypeIdsForSync(array $row): array
    {
        $dofusProvided = isset($row['item_type_dofus_ids']) && is_array($row['item_type_dofus_ids'])
            && $row['item_type_dofus_ids'] !== [];

        if ($dofusProvided) {
            $dofus = [];
            foreach ($row['item_type_dofus_ids'] as $id) {
                if (is_int($id) && $id > 0) {
                    $dofus[] = $id;
                } elseif (is_string($id) && $id !== '' && ctype_digit($id)) {
                    $v = (int) $id;
                    if ($v > 0) {
                        $dofus[] = $v;
                    }
                }
            }
            $dofus = array_values(array_unique($dofus));
            if ($dofus !== []) {
                /** @var list<int> */
                $resolved = ItemType::query()
                    ->whereIn('dofusdb_type_id', $dofus)
                    ->pluck('id')
                    ->map(static fn ($id) => (int) $id)
                    ->all();
                if ($resolved !== []) {
                    return $resolved;
                }
            }

            // DofusDb ids fournis mais aucun enregistrement : ne pas retomber sur `item_type_ids`
            // (souvent des ids BDD copiés depuis un autre environnement → violation FK).
            return [];
        }

        if (isset($row['item_type_ids']) && is_array($row['item_type_ids'])) {
            $candidates = $this->normalizeItemTypeIdsForSync($row['item_type_ids']);
            if ($candidates === []) {
                return [];
            }

            /** @var list<int> */
            return ItemType::query()
                ->whereIn('id', $candidates)
                ->pluck('id')
                ->map(static fn ($id) => (int) $id)
                ->all();
        }

        return [];
    }

    /**
     * Seed depuis les fichiers `stem-groupe-definition.json`.
     */
    protected function seedPivotsFromJsonDefinitions(): void
    {
        $paths = $this->jsonDefinitionPaths();
        $modelClass = $this->modelClass();
        $idsByKey = $this->characteristicIdsByKey();
        $n = 0;
        foreach ($paths as $path) {
            try {
                $def = CharacteristicDefinitionReader::load($path);
            } catch (\Throwable) {
                continue;
            }
            $key = $def['characteristic']['key'] ?? '';
            if (! is_string($key) || $key === '') {
                continue;
            }
            $characteristicId = $idsByKey[$key] ?? null;
            if ($characteristicId === null) {
                continue;
            }
            foreach ($def['entities'] as $entity => $payload) {
                if (! is_string($entity) || $entity === '') {
                    continue;
                }
                if (! is_array($payload)) {
                    continue;
                }
                $row = array_merge($payload, [
                    'characteristic_key' => $key,
                    'entity' => $entity,
                ]);
                $attributes = $this->mapRowToAttributes($row);
                $model = $this->retryOnMysqlSchemaChanged(
                    fn () => SeedMode::upsert(
                        $modelClass,
                        [
                            'characteristic_id' => $characteristicId,
                            'entity' => $entity,
                        ],
                        $attributes
                    )
                );
                if ($model instanceof CharacteristicObject
                    && (SeedMode::overwrite() || SeedMode::wasRecentlyCreated($model))
                ) {
                    $itemTypeIds = $this->resolveCharacteristicObjectItemTypeIdsForSync($row);
                    $this->retryOnMysqlSchemaChanged(
                        static fn () => $model->allowedItemTypes()->sync($itemTypeIds)
                    );
                }
                $n++;
            }
        }
        if ($this->command) {
            $this->command->info(class_basename(static::class).' : '.$n.' ligne(s) pivot (JSON).');
        }
    }

    public function run(): void
    {
        if ($this->jsonDefinitionPaths() === []) {
            throw new \RuntimeException(
                'Aucune définition JSON pour '.static::class.'. Attendu : '
                .CharacteristicDefinitionNaming::RELATIVE_ROOT.'/'.$this->jsonGroupSubdirectory().'/*-definition.json'
            );
        }
        $this->seedPivotsFromJsonDefinitions();
    }

    /**
     * Mappe une ligne du fichier vers les attributs à passer à updateOrCreate.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    abstract protected function mapRowToAttributes(array $row): array;
}
