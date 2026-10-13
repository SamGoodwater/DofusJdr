<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CharacteristicConsumable;

/**
 * Seed characteristic_consumable (groupe consommable).
 */
class ConsumableCharacteristicSeeder extends CharacteristicGroupSeeder
{
    protected function defaultEntity(): string
    {
        return '*';
    }

    /**
     * @return class-string<CharacteristicConsumable>
     */
    protected function modelClass(): string
    {
        return CharacteristicConsumable::class;
    }

    protected function jsonGroupSubdirectory(): string
    {
        return 'consumable';
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function mapRowToAttributes(array $row): array
    {
        return array_merge($this->commonAttributes($row), [
            'value_available' => $row['value_available'] ?? null,
        ]);
    }
}
