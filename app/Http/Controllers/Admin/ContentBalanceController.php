<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Hub d'équilibrage : liens vers les caractéristiques à normes générées.
 */
final class ContentBalanceController extends Controller
{
    public function index(): InertiaResponse
    {
        $families = [
            [
                'title' => 'Survie',
                'description' => 'PV, dés de vie, CA (normes créature existantes).',
                'keys' => [
                    ['key' => 'life_points_creature', 'label' => 'Points de vie'],
                    ['key' => 'hit_dice_creature', 'label' => 'Dés de vie'],
                    ['key' => 'armor_class_creature', 'label' => 'Classe d’armure'],
                ],
            ],
            [
                'title' => 'Offensive',
                'description' => 'Dégâts et vol de vie (lancement 3 PA).',
                'keys' => [
                    ['key' => 'dommages_spell', 'label' => 'Dommages'],
                    ['key' => 'vol_vie_spell', 'label' => 'Vol de vie'],
                ],
            ],
            [
                'title' => 'Soin & protection',
                'description' => 'Soin, PV temporaires, bouclier.',
                'keys' => [
                    ['key' => 'soin_spell', 'label' => 'Soin'],
                    ['key' => 'pv_temporaires_spell', 'label' => 'PV temporaires'],
                    ['key' => 'bouclier_spell', 'label' => 'Bouclier'],
                ],
            ],
            [
                'title' => 'PA / PM',
                'description' => 'Budgets créature et retraits de sorts.',
                'keys' => [
                    ['key' => 'action_points_creature', 'label' => 'PA créature'],
                    ['key' => 'movement_points_creature', 'label' => 'PM créature'],
                    ['key' => 'action_points_spell', 'label' => 'Retrait PA'],
                    ['key' => 'movement_points_spell', 'label' => 'Retrait PM'],
                ],
            ],
            [
                'title' => 'Placement',
                'description' => 'Poussée (movement_distance), attirance, téléport, bond.',
                'keys' => [
                    ['key' => 'movement_distance_spell', 'label' => 'Poussée / déplacement'],
                    ['key' => 'pull_distance_spell', 'label' => 'Attirance'],
                    ['key' => 'teleport_distance_spell', 'label' => 'Téléportation'],
                    ['key' => 'jump_distance_spell', 'label' => 'Bond'],
                ],
            ],
            [
                'title' => 'Invocations',
                'description' => 'Mêmes valeurs que les monstres (life_points / PA / PM / CA overlay monster).',
                'keys' => [
                    ['key' => 'summoning_spell', 'label' => 'Nombre d’invocations (sort)'],
                    ['key' => 'summoning_creature', 'label' => 'Nombre d’invocations (créature)'],
                    ['key' => 'life_points_creature', 'label' => 'PV (réf. monstre)'],
                ],
            ],
            [
                'title' => 'Consommables',
                'description' => 'Alignés sur un lancement de sort équivalent.',
                'keys' => [
                    ['key' => 'life_points_restore_object', 'label' => 'Restauration de PV'],
                    ['key' => 'temporary_life_points_object', 'label' => 'PV temporaires objet'],
                    ['key' => 'shield_points_object', 'label' => 'Points de bouclier objet'],
                ],
            ],
        ];

        return Inertia::render('Admin/Content/Balance/Index', [
            'families' => $families,
        ]);
    }
}
