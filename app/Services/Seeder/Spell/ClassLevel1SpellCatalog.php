<?php

declare(strict_types=1);

namespace App\Services\Seeder\Spell;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

/**
 * Catalogue des sorts de classe (kit niveau 1 + progression 2–12).
 *
 * @example $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::iopPath());
 */
final class ClassLevel1SpellCatalog
{
    public const SCHEMA_VERSION = '1';

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(private readonly array $payload) {}

    public static function directory(): string
    {
        return database_path('seeders/data/entities/spells');
    }

    public static function iopPath(): string
    {
        return self::directory().'/iop-level-1.json';
    }

    public static function craPath(): string
    {
        return self::directory().'/cra-level-1.json';
    }

    public static function eniripsaPath(): string
    {
        return self::directory().'/eniripsa-level-1.json';
    }

    public static function sramPath(): string
    {
        return self::directory().'/sram-level-1.json';
    }

    public static function xelorPath(): string
    {
        return self::directory().'/xelor-level-1.json';
    }

    public static function fecaPath(): string
    {
        return self::directory().'/feca-level-1.json';
    }

    public static function osamodasPath(): string
    {
        return self::directory().'/osamodas-level-1.json';
    }

    public static function enutrofPath(): string
    {
        return self::directory().'/enutrof-level-1.json';
    }

    public static function sadidaPath(): string
    {
        return self::directory().'/sadida-level-1.json';
    }

    public static function sacrieurPath(): string
    {
        return self::directory().'/sacrieur-level-1.json';
    }

    public static function pandawaPath(): string
    {
        return self::directory().'/pandawa-level-1.json';
    }

    public static function ecaflipPath(): string
    {
        return self::directory().'/ecaflip-level-1.json';
    }

    public static function roublardPath(): string
    {
        return self::directory().'/roublard-level-1.json';
    }

    public static function zobalPath(): string
    {
        return self::directory().'/zobal-level-1.json';
    }

    public static function steamerPath(): string
    {
        return self::directory().'/steamer-level-1.json';
    }

    public static function eliotropePath(): string
    {
        return self::directory().'/eliotrope-level-1.json';
    }

    public static function huppermagePath(): string
    {
        return self::directory().'/huppermage-level-1.json';
    }

    public static function ouginakPath(): string
    {
        return self::directory().'/ouginak-level-1.json';
    }

    public static function forgelancePath(): string
    {
        return self::directory().'/forgelance-level-1.json';
    }

    /**
     * @return list<self>
     */
    public static function loadAllInDirectory(?string $directory = null): array
    {
        $dir = $directory ?? self::directory();
        if (! is_dir($dir)) {
            return [];
        }

        $files = array_merge(
            File::glob($dir.'/*-level-1.json') ?: [],
            File::glob($dir.'/*-progression.json') ?: [],
        );
        sort($files);

        $catalogs = [];
        foreach ($files as $file) {
            $catalogs[] = self::load($file);
        }

        return $catalogs;
    }

    /**
     * @throws JsonException
     */
    public static function load(?string $path = null): self
    {
        $file = $path ?? self::iopPath();
        if (! File::isFile($file)) {
            throw new RuntimeException('Catalogue de sorts de classe introuvable : '.$file);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);

        return new self($payload);
    }

    public function breedName(): string
    {
        return trim((string) ($this->payload['breed'] ?? ''));
    }

    /**
     * @return list<array{
     *     key: string,
     *     name: string,
     *     dofusdb_id: string|null,
     *     official_id: string|null,
     *     character_level: int,
     *     slot_index: int,
     *     choice_order: int,
     *     types: list<string>,
     *     element: string|null,
     *     pa: string,
     *     po_min: string,
     *     po_max: string,
     *     po_editable: bool,
     *     sight_line: bool,
     *     cast_per_turn: string,
     *     cast_per_target: string,
     *     number_between_two_cast: string,
     *     cast_in_line: bool,
     *     state: string,
     *     max_stack: int,
     *     resolution_mode: string,
     *     attack_characteristic_key: string|null,
     *     save_characteristic_key: string|null,
     *     save_dc_formula: string|null,
     *     save_success_note: string|null,
     *     auto_success_if_willing_target: bool,
     *     is_magic: bool,
     *     powerful: int,
     *     duration: string|null,
     *     target_type: string,
     *     effect: string,
     *     description: string,
     *     sub_effects: list<array<string, mixed>>,
     *     intensification: list<array<string, mixed>>
     * }>
     */
    public function entries(): array
    {
        $entries = [];
        foreach (is_array($this->payload['entries'] ?? null) ? $this->payload['entries'] : [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $key = trim((string) ($row['key'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            if ($key === '' || $name === '') {
                continue;
            }

            $types = [];
            foreach (is_array($row['types'] ?? null) ? $row['types'] : [] as $typeName) {
                $label = trim((string) $typeName);
                if ($label !== '') {
                    $types[] = $label;
                }
            }

            $subEffects = [];
            foreach (is_array($row['sub_effects'] ?? null) ? $row['sub_effects'] : [] as $sub) {
                if (! is_array($sub)) {
                    continue;
                }
                $slug = trim((string) ($sub['slug'] ?? ''));
                if ($slug === '') {
                    continue;
                }
                $params = is_array($sub['params'] ?? null) ? $sub['params'] : [];
                $subEffects[] = [
                    'slug' => $slug,
                    'area' => $this->nullableString($sub['area'] ?? null) ?? 'point',
                    'order' => max(0, (int) ($sub['order'] ?? 0)),
                    'duration_formula' => $this->nullableString($sub['duration_formula'] ?? null),
                    'params' => $params,
                ];
            }

            $element = $this->nullableString($row['element'] ?? null);

            $entries[] = [
                'key' => $key,
                'name' => $name,
                'dofusdb_id' => $this->nullableString($row['dofusdb_id'] ?? null),
                'official_id' => $this->nullableString($row['official_id'] ?? null),
                'character_level' => max(1, (int) ($row['character_level'] ?? 1)),
                'slot_index' => max(1, (int) ($row['slot_index'] ?? 1)),
                'choice_order' => max(0, (int) ($row['choice_order'] ?? 0)),
                'types' => $types,
                'element' => $element,
                'pa' => (string) ($row['pa'] ?? '3'),
                'po_min' => (string) ($row['po_min'] ?? '0'),
                'po_max' => (string) ($row['po_max'] ?? '0'),
                'po_editable' => (bool) ($row['po_editable'] ?? false),
                'sight_line' => (bool) ($row['sight_line'] ?? true),
                'cast_per_turn' => (string) ($row['cast_per_turn'] ?? '1'),
                'cast_per_target' => (string) ($row['cast_per_target'] ?? '0'),
                'number_between_two_cast' => (string) ($row['number_between_two_cast'] ?? '0'),
                'cast_in_line' => (bool) ($row['cast_in_line'] ?? false),
                'state' => $this->spellState($row['state'] ?? $this->payload['state'] ?? null),
                'max_stack' => max(0, (int) ($row['max_stack'] ?? 0)),
                'resolution_mode' => (string) ($row['resolution_mode'] ?? 'attack_roll'),
                'attack_characteristic_key' => $this->nullableString($row['attack_characteristic_key'] ?? null),
                'save_characteristic_key' => $this->nullableString($row['save_characteristic_key'] ?? null),
                'save_dc_formula' => $this->nullableString($row['save_dc_formula'] ?? null),
                'save_success_note' => $this->nullableString($row['save_success_note'] ?? null),
                'auto_success_if_willing_target' => (bool) ($row['auto_success_if_willing_target'] ?? false),
                'is_magic' => (bool) ($row['is_magic'] ?? false),
                'powerful' => max(0, (int) ($row['powerful'] ?? 0)),
                'duration' => $this->nullableString($row['duration'] ?? null),
                'target_type' => $this->targetType($row['target_type'] ?? null),
                'effect' => (string) ($row['effect'] ?? ''),
                'description' => (string) ($row['description'] ?? ''),
                'sub_effects' => $subEffects,
                'intensification' => $this->intensification($row['intensification'] ?? null),
            ];
        }

        return $entries;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function intensification(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $tiers = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $subs = [];
            foreach (is_array($row['sub_effects'] ?? null) ? $row['sub_effects'] : [] as $sub) {
                if (! is_array($sub)) {
                    continue;
                }
                $slug = trim((string) ($sub['slug'] ?? ''));
                if ($slug === '') {
                    continue;
                }
                $params = is_array($sub['params'] ?? null) ? $sub['params'] : [];
                $subs[] = [
                    'slug' => $slug,
                    'area' => $this->nullableString($sub['area'] ?? null) ?? 'point',
                    'order' => max(0, (int) ($sub['order'] ?? 0)),
                    'duration_formula' => $this->nullableString($sub['duration_formula'] ?? null),
                    'params' => $params,
                ];
            }
            $tiers[] = [
                'tier' => trim((string) ($row['tier'] ?? '')),
                'required_creature_level' => max(1, (int) ($row['required_creature_level'] ?? 13)),
                'effect' => (string) ($row['effect'] ?? ''),
                'sub_effects' => $subs,
            ];
        }

        return $tiers;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value) && ! is_int($value)) {
            return null;
        }
        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * État de fiche. Défaut `auto` ; un catalogue peut imposer `playable`.
     */
    private function spellState(mixed $value): string
    {
        $state = $this->nullableString($value) ?? 'auto';

        return in_array($state, ['raw', 'draft', 'auto', 'playable', 'archived'], true)
            ? $state
            : 'auto';
    }

    private function targetType(mixed $value): string
    {
        $trimmed = $this->nullableString($value) ?? 'direct';

        return in_array($trimmed, ['direct', 'trap', 'glyph'], true) ? $trimmed : 'direct';
    }
}
