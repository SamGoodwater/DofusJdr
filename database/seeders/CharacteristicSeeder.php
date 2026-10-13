<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use App\Support\Seeder\SeedMode;
use Database\Seeders\Concerns\RetriesWhenMysqlSchemaChanged;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Seed la table générale characteristics.
 */
class CharacteristicSeeder extends Seeder
{
    use RetriesWhenMysqlSchemaChanged;

    /**
     * Clés `*_spell` → `*_creature` quand le nommage ne suit pas la règle suffixe `_spell` → `_creature`.
     * Les autres sorts alignés sur une caractéristique créature sont résolus automatiquement.
     *
     * @var array<string, string>
     */
    private const SPELL_TO_CREATURE_STYLE_KEY = [
        'agi_spell' => 'agility_creature',
        'intel_spell' => 'intelligence_creature',
        'strong_spell' => 'strength_creature',
        'sagesse_spell' => 'wisdom_creature',
        'fixed_resistance_eau_spell' => 'fixed_resistance_water_creature',
        'fixed_resistance_feu_spell' => 'fixed_resistance_fire_creature',
        'fixed_resistance_terre_spell' => 'fixed_resistance_earth_creature',
        'fixed_resistance_neutre_spell' => 'fixed_resistance_neutral_creature',
        'res_air_spell' => 'resistance_air_creature',
        'res_eau_spell' => 'resistance_water_creature',
        'res_feu_spell' => 'resistance_fire_creature',
        'res_neutre_spell' => 'resistance_neutral_creature',
        'res_terre_spell' => 'resistance_earth_creature',
        'do_fixe_multiple_spell' => 'fixed_damage_multiple_creature',
        'bouclier_spell' => 'armor_class_creature',
        'dommages_spell' => 'fixed_damage_neutral_creature',
        'soin_spell' => 'heal_bonus_creature',
        'vol_vie_spell' => 'life_points_creature',
        'critical_spell' => 'critical_hit_creature',
        'spell_range_max_spell' => 'range_creature',
        'spell_range_min_spell' => 'range_creature',
    ];

    public function run(): void
    {
        $rows = $this->loadCharacteristicRowsFromDefinitionFiles();
        if ($rows === []) {
            throw new \RuntimeException(
                'Aucune définition caractéristique JSON. Attendu : des fichiers *-definition.json sous '
                .CharacteristicDefinitionNaming::RELATIVE_ROOT.'/{creature,object,spell,consumable,profile}/'
            );
        }
        // Dédoublonnage par clé pour éviter les violations de contrainte unique (ex. exécution parallèle de tests).
        $byKey = [];
        foreach ($rows as $row) {
            $k = $row['key'] ?? '';
            if ($k !== '') {
                $byKey[$k] = $row;
            }
        }
        $rows = array_values($byKey);

        foreach ($rows as &$row) {
            $row = $this->normalizeCharacteristicDisplayLabels($row);
        }
        unset($row);

        $rows = $this->applySpellVisualsFromCreatureCharacteristics($rows);

        // Surcharges centralisées (surtout visuels) : fusionnées quand la ligne ne les fixe pas.
        // - descriptions, value_overrides, icon_false : null ou absent → repli sur ce fichier.
        // - icon / color : repli seulement si la clé est absente sur la ligne (voir JSON : clés omises si null dans toSeederRow).
        $defaults = $this->loadIconsAndColorsDefaults();
        $icons = $defaults['icons'] ?? [];
        $iconsFalse = $defaults['icons_false'] ?? [];
        $colors = $defaults['colors'] ?? [];
        $descriptions = $defaults['descriptions'] ?? [];
        $valueOverrides = $defaults['value_overrides'] ?? [];

        $hasIconFalse = Schema::hasColumn('characteristics', 'icon_false');
        $hasValueOverrides = Schema::hasColumn('characteristics', 'value_overrides');
        $hasHideWhenEmpty = Schema::hasColumn('characteristics', 'hide_when_empty');
        $hasHideWhenFalse = Schema::hasColumn('characteristics', 'hide_when_false');
        $hasStatus = Schema::hasColumn('characteristics', 'status');

        // 1) Création / mise à jour des caractéristiques sans gérer les liens
        foreach ($rows as $row) {
            $key = $row['key'] ?? '';
            $payload = [
                'name' => $row['name'],
                'short_name' => $row['short_name'] ?? null,
                'helper' => $row['helper'] ?? null,
                'descriptions' => $row['descriptions'] ?? $descriptions[$key] ?? null,
                // Clé absente ou null explicite : pas de repli sur les defaults (icône / couleur vides volontaires).
                'icon' => array_key_exists('icon', $row) ? $row['icon'] : ($icons[$key] ?? null),
                'color' => array_key_exists('color', $row) ? $row['color'] : ($colors[$key] ?? null),
                'unit' => $row['unit'] ?? null,
                'type' => $row['type'] ?? 'string',
                'sort_order' => (int) ($row['sort_order'] ?? 0),
                // Nouveau : groupe explicite ; peut rester null (calculé par inferPrimaryGroup côté service).
                'group' => $row['group'] ?? null,
                // Les liens sont gérés dans un second passage pour garantir que toutes les maîtres existent.
                'linked_to_characteristic_id' => null,
                'intrinsic_characteristic_id' => null,
            ];
            if (Schema::hasColumn('characteristics', 'nature')) {
                $payload['nature'] = $row['nature'] ?? null;
            }
            if ($hasStatus) {
                $payload['status'] = $row['status'] ?? Characteristic::STATUS_A_VALIDER;
            }
            if ($hasIconFalse) {
                $payload['icon_false'] = $row['icon_false'] ?? ($iconsFalse[$key] ?? null);
            }
            if ($hasValueOverrides) {
                $payload['value_overrides'] = $row['value_overrides'] ?? ($valueOverrides[$key] ?? null);
            }
            if ($hasHideWhenEmpty) {
                $payload['hide_when_empty'] = (bool) ($row['hide_when_empty'] ?? false);
            }
            if ($hasHideWhenFalse) {
                $payload['hide_when_false'] = (bool) ($row['hide_when_false'] ?? false);
            }
            $this->retryOnMysqlSchemaChanged(
                static fn () => SeedMode::upsert(Characteristic::class, ['key' => $key], $payload)
            );
        }

        // 2) Deuxième passage : rattacher linked_to_key et intrinsic_key
        $idsByKey = Characteristic::query()->pluck('id', 'key')->all();
        $overwrite = SeedMode::overwrite();
        $hasIntrinsic = Schema::hasColumn('characteristics', 'intrinsic_characteristic_id');
        foreach ($rows as $row) {
            $childId = $idsByKey[$row['key']] ?? null;
            if ($childId === null) {
                continue;
            }

            if (! empty($row['linked_to_key'])) {
                $masterId = $idsByKey[$row['linked_to_key']] ?? null;
                if ($masterId === null) {
                    if ($this->command) {
                        $this->command->warn(sprintf(
                            'CharacteristicSeeder : caractéristique maître introuvable pour %s (linked_to_key=%s).',
                            $row['key'],
                            $row['linked_to_key']
                        ));
                    }
                } else {
                    $query = Characteristic::whereKey($childId);
                    if (! $overwrite) {
                        $query->whereNull('linked_to_characteristic_id');
                    }
                    $query->update([
                        'linked_to_characteristic_id' => $masterId,
                    ]);
                }
            }

            if ($hasIntrinsic && ! empty($row['intrinsic_key'])) {
                $intrinsicId = $idsByKey[$row['intrinsic_key']] ?? null;
                if ($intrinsicId === null) {
                    if ($this->command) {
                        $this->command->warn(sprintf(
                            'CharacteristicSeeder : intrinsèque introuvable pour %s (intrinsic_key=%s).',
                            $row['key'],
                            $row['intrinsic_key']
                        ));
                    }
                } else {
                    $query = Characteristic::whereKey($childId);
                    if (! $overwrite) {
                        $query->whereNull('intrinsic_characteristic_id');
                    }
                    $query->update([
                        'intrinsic_characteristic_id' => $intrinsicId,
                    ]);
                }
            }
        }

        if ($this->command) {
            $this->command->info('CharacteristicSeeder : '.count($rows).' ligne(s).');
        }
    }

    /**
     * Retire le suffixe « (effet) » / « (effets) » du libellé et « eff. » en fin d’abréviation
     * (ex. « PM eff. » → « PM »), pour alléger l’affichage des caractéristiques liées aux sorts.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizeCharacteristicDisplayLabels(array $row): array
    {
        if (! empty($row['name']) && is_string($row['name'])) {
            $cleaned = preg_replace('/\s+\(effets?\)$/u', '', $row['name']);
            $row['name'] = is_string($cleaned) ? $cleaned : $row['name'];
        }

        if (isset($row['short_name']) && is_string($row['short_name']) && $row['short_name'] !== '') {
            $short = preg_replace('/\s+eff\.$/iu', '', $row['short_name']);
            $short = trim(is_string($short) ? $short : $row['short_name']);
            $row['short_name'] = $short !== '' ? $short : null;
        }

        return $row;
    }

    /**
     * Pour chaque caractéristique de groupe `spell`, recopie `icon` et `color` depuis la caractéristique
     * `creature` équivalente lorsqu’elle existe (données déjà harmonisées côté créature).
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function applySpellVisualsFromCreatureCharacteristics(array $rows): array
    {
        $creatureByKey = [];
        foreach ($rows as $r) {
            $k = $r['key'] ?? '';
            if ($k === '' || ($r['group'] ?? null) !== 'creature') {
                continue;
            }
            $creatureByKey[$k] = $r;
        }

        foreach ($rows as $i => $row) {
            if (($row['group'] ?? null) !== 'spell') {
                continue;
            }
            $sourceKey = $this->resolveCreatureStyleSourceKey($row, $creatureByKey);
            if ($sourceKey === null || ! isset($creatureByKey[$sourceKey])) {
                continue;
            }
            $src = $creatureByKey[$sourceKey];
            $icon = $src['icon'] ?? null;
            $color = $src['color'] ?? null;
            if (is_string($icon) && $icon !== '') {
                $rows[$i]['icon'] = $icon;
            }
            if (is_string($color) && $color !== '') {
                $rows[$i]['color'] = $color;
            }
        }

        return $rows;
    }

    /**
     * @param  array<string, array<string, mixed>>  $creatureByKey
     */
    private function resolveCreatureStyleSourceKey(array $spellRow, array $creatureByKey): ?string
    {
        $linked = $spellRow['linked_to_key'] ?? null;
        if (is_string($linked) && $linked !== '' && isset($creatureByKey[$linked])) {
            return $linked;
        }

        $key = $spellRow['key'] ?? '';
        if (! is_string($key) || $key === '') {
            return null;
        }

        if (isset(self::SPELL_TO_CREATURE_STYLE_KEY[$key])) {
            $mapped = self::SPELL_TO_CREATURE_STYLE_KEY[$key];

            return isset($creatureByKey[$mapped]) ? $mapped : null;
        }

        if (! str_ends_with($key, '_spell')) {
            return null;
        }

        $candidate = substr($key, 0, -strlen('_spell')).'_creature';

        return isset($creatureByKey[$candidate]) ? $candidate : null;
    }

    /**
     * Surcharges visuelles optionnelles (réserve ; tout est porté par les JSON de définition).
     *
     * @return array{icons: array<string, string>, icons_false: array<string, string>, colors: array<string, string>, descriptions: array<string, string>, value_overrides: array<string, list<array<string, mixed>>>}
     */
    private function loadIconsAndColorsDefaults(): array
    {
        return ['icons' => [], 'icons_false' => [], 'colors' => [], 'descriptions' => [], 'value_overrides' => []];
    }

    /**
     * Une ligne par fichier `stem-groupe-definition.json` (bloc `characteristic`).
     *
     * @return list<array<string, mixed>>
     */
    private function loadCharacteristicRowsFromDefinitionFiles(): array
    {
        $rows = [];
        foreach (CharacteristicDefinitionReader::allDefinitionAbsolutePaths() as $path) {
            try {
                $def = CharacteristicDefinitionReader::load($path);
            } catch (\Throwable) {
                continue;
            }
            $c = $def['characteristic'];
            if (! is_array($c) || ! isset($c['key']) || ! is_string($c['key']) || $c['key'] === '') {
                continue;
            }
            $rows[] = $c;
        }

        return $rows;
    }
}
