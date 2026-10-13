<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CharacteristicCreature;

/**
 * Seed characteristic_creature (groupe creature : monster, class, npc).
 */
class CreatureCharacteristicSeeder extends CharacteristicGroupSeeder
{
    /**
     * @return class-string<CharacteristicCreature>
     */
    protected function modelClass(): string
    {
        return CharacteristicCreature::class;
    }

    protected function jsonGroupSubdirectory(): string
    {
        return 'creature';
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function mapRowToAttributes(array $row): array
    {
        return array_merge($this->commonAttributes($row), [
            'limit_absolute' => isset($row['limit_absolute']) ? (bool) $row['limit_absolute'] : null,
            'labels' => $row['labels'] ?? null,
            'validation' => $row['validation'] ?? null,
        ]);
    }
}
