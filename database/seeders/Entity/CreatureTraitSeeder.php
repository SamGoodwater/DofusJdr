<?php

namespace Database\Seeders\Entity;

use App\Models\Entity\CreatureTrait;
use App\Models\User;
use App\Support\Seeder\SeedMode;
use Illuminate\Database\Seeder;

class CreatureTraitSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = ['state' => CreatureTrait::STATE_PLAYABLE, 'read_level' => User::ROLE_GUEST, 'write_level' => User::ROLE_ADMIN];
        /** @var list<array{name: string, description: string, image: string}> $traits */
        $traits = require database_path('seeders/data/entities/creature-traits/playable-catalog.php');
        foreach ($traits as $trait) {
            SeedMode::upsert(CreatureTrait::class, ['name' => $trait['name']], array_merge($defaults, $trait));
        }
    }
}
