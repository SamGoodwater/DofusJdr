<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CharacteristicProfile;

/**
 * Seed characteristic_profile (groupe Profil : classe, spécialisation, monstre).
 */
class ProfileCharacteristicSeeder extends CharacteristicGroupSeeder
{
    protected function defaultEntity(): string
    {
        return '*';
    }

    /**
     * @return class-string<CharacteristicProfile>
     */
    protected function modelClass(): string
    {
        return CharacteristicProfile::class;
    }

    protected function jsonGroupSubdirectory(): string
    {
        return 'profile';
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
