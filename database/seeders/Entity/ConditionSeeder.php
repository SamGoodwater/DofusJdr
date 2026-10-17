<?php

namespace Database\Seeders\Entity;

use App\Models\Entity\Condition;
use App\Models\User;
use App\Services\Condition\PlayableConditionCatalog;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = ['state' => Condition::STATE_PLAYABLE, 'read_level' => User::ROLE_GUEST, 'write_level' => User::ROLE_ADMIN];
        $conditions = [
            [
                'name' => 'Pesanteur',
                'description' => 'La créature ne peut pas se téléporter ou être téléportée',
                'image' => 'https://api.dofusdb.fr/img/states/stateImmobilization.png',
                'cant_be_moved' => true,
                'cant_be_pushed' => true,
                'cant_switch_position' => true,
            ],
            [
                'name' => 'Empoisonné',
                'description' => 'Une créature empoisonnée a un désavantage aux jets d\'attaque et aux jets de caractéristique. Elle peut également subir des dégâts en fonction de la source du poison.',
                'image' => '/storage/images/icons/states/empoisonne.png',
            ],
            [
                'name' => 'Étourdi',
                'description' => "Une créature étourdie est incapable d'agir (PA = 0 quelque soit ces bonus), ne peut plus bouger (PM=0 quelque soit ces bonus) et parle de manière hésitante.\nLa créature rate automatiquement ses jets de sauvegarde de Force et de Dextérité.\nLes jets d'attaque contre la créature ont un avantage.",
                'image' => '/storage/images/icons/states/etourdi.png',
                'prevents_spell_cast' => true,
                'prevents_fight' => true,
            ],
            [
                'name' => 'Ralenti',
                'description' => 'La créature voit ces PM divisé par 2. Hors combat la cible ne peut pas courir.',
                'image' => '/storage/images/icons/states/ralenti.png',
            ],
            [
                'name' => 'Affaibli',
                'description' => 'La créature voit ces dommages divisé par 2. De plus elle se déplace 2 fois moins vite (hors combat).',
                'image' => 'https://api.dofusdb.fr/img/states/stateWeakened.png',
            ],
            [
                'name' => 'Aveuglé',
                'description' => "Une créature aveuglée ne voit pas et rate automatiquement tout jet de caractéristique qui nécessite la vue.\nLes jets d'attaque contre la créature ont un avantage, et les jets d'attaque de la créature ont un désavantage.",
                'image' => '/storage/images/icons/states/aveugle.png',
                'write_level' => User::ROLE_GAME_MASTER,
            ],
        ];
        foreach ($conditions as $condition) {
            Condition::query()->firstOrCreate(
                ['name' => $condition['name']],
                array_merge($defaults, $condition),
            );
        }

        app(PlayableConditionCatalog::class)->sync();
    }
}
