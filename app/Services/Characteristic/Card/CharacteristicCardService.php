<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Card;

use App\Models\Characteristic;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Support\Characteristics\CharacteristicGroupRegistry;
use Illuminate\Support\Facades\Cache;

/**
 * Construit la fiche publique d'une caractéristique (tooltip joueur / panneau MJ).
 */
final class CharacteristicCardService
{
    public const CACHE_PREFIX = 'characteristics:card:v1:';

    public function __construct(
        private readonly CharacteristicGetterService $getter,
    ) {}

    /**
     * @return array<string, mixed>|null
     */
    public function build(string $key, string $group, string $entity = '*'): ?array
    {
        if (! CharacteristicGroupRegistry::isValid($group)) {
            throw new \InvalidArgumentException('Groupe invalide.');
        }

        $entity = $entity === '' ? '*' : $entity;
        $cacheKey = self::CACHE_PREFIX.$key.'|'.$group.'|'.$entity;

        return Cache::remember($cacheKey, 3600, function () use ($key, $group, $entity): ?array {
            $characteristic = Characteristic::query()->where('key', $key)->first();
            if ($characteristic === null) {
                return null;
            }

            $pivot = CharacteristicGroupRegistry::findPivotRow($characteristic->id, $group, $entity);
            if ($pivot === null) {
                return null;
            }

            $effectiveEntity = (string) ($pivot->entity ?? $entity);
            $def = $this->getter->getDefinition($key, $effectiveEntity)
                ?? $this->getter->getDefinition($key, '*');
            if ($def === null) {
                return null;
            }

            $limits = $this->getter->getLimits($key, $effectiveEntity) ?? $this->getter->getLimits($key, '*');
            $limitsByLevel = $this->limitsByLevel($key, $effectiveEntity, $def);

            return [
                'key' => (string) ($def['key'] ?? $key),
                'group' => $group,
                'entity' => $effectiveEntity,
                'name' => (string) ($def['name'] ?? $key),
                'short_name' => $def['short_name'] ?? null,
                'icon' => $def['icon'] ?? null,
                'color' => $def['color'] ?? null,
                'unit' => $def['unit'] ?? null,
                'type' => $def['type'] ?? null,
                'nature' => $def['nature'] ?? null,
                'helper' => $def['helper'] ?? null,
                'descriptions' => $def['descriptions'] ?? null,
                'db_column' => $def['db_column'] ?? null,
                'formula' => $def['formula'] ?? null,
                'formula_display' => $def['formula_display'] ?? null,
                'default_value' => $def['default_value'] ?? null,
                'min' => $def['min'] ?? null,
                'max' => $def['max'] ?? null,
                'limit_min' => $limits['min'] ?? null,
                'limit_max' => $limits['max'] ?? null,
                'limit_absolute' => (bool) ($def['limit_absolute'] ?? false),
                'limits_depend_on_level' => $limitsByLevel !== null,
                'limits_by_level' => $limitsByLevel,
                'conversion_formula' => $def['conversion_formula'] ?? null,
                'conversion_dofus_sample' => $def['conversion_dofus_sample'] ?? null,
                'conversion_krosmoz_sample' => $def['conversion_krosmoz_sample'] ?? null,
                'forgemagie_max' => $def['forgemagie_max'] ?? null,
                'base_price_per_unit' => $def['base_price_per_unit'] ?? null,
                'rune_price_per_unit' => $def['rune_price_per_unit'] ?? null,
                'allowed_item_type_ids' => $def['allowed_item_type_ids'] ?? null,
                'has_norms_grid' => is_array($def['norms_grid'] ?? null),
            ];
        });
    }

    public function forget(string $key, ?string $group = null, ?string $entity = null): void
    {
        if ($group !== null && $entity !== null) {
            Cache::forget(self::CACHE_PREFIX.$key.'|'.$group.'|'.$entity);

            return;
        }

        foreach (CharacteristicGroupRegistry::groups() as $g) {
            Cache::forget(self::CACHE_PREFIX.$key.'|'.$g.'|*');
            if ($entity !== null) {
                Cache::forget(self::CACHE_PREFIX.$key.'|'.$g.'|'.$entity);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $def
     * @return list<array{level: int, min: int|null, max: int|null}>|null
     */
    private function limitsByLevel(string $key, string $entity, array $def): ?array
    {
        $minRaw = is_scalar($def['min'] ?? null) ? trim((string) $def['min']) : '';
        $maxRaw = is_scalar($def['max'] ?? null) ? trim((string) $def['max']) : '';
        $depends = ($minRaw !== '' && ! is_numeric($minRaw) && str_contains($minRaw, 'level'))
            || ($maxRaw !== '' && ! is_numeric($maxRaw) && str_contains($maxRaw, 'level'));
        if (! $depends) {
            return null;
        }

        $rows = [];
        for ($level = 1; $level <= 20; $level++) {
            $limits = $this->getter->getLimits($key, $entity, [
                'level' => $level,
                'level_creature' => $level,
            ]);
            $rows[] = [
                'level' => $level,
                'min' => $limits['min'] ?? null,
                'max' => $limits['max'] ?? null,
            ];
        }

        return $rows;
    }
}
