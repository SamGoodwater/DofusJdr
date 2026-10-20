<?php

namespace Database\Seeders\Type;

use App\Models\Type\SpellType;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Types de sorts alignés sur les orientations de classe / caractéristique `spell_type_spell`.
 *
 * Anciens noms (Offensif, Buff…) sont renommés en place pour conserver les IDs et pivots.
 */
class SpellTypeSeeder extends Seeder
{
    /**
     * Ancien libellé → définition cible (nom affiché = orientation).
     *
     * @var array<string, array{name: string, description: string, color: string, icon: string}>
     */
    private const ORIENTATION_TYPES = [
        'Offensif' => [
            'name' => 'Dégâts',
            'description' => 'Sort offensif infligeant des dommages directs.',
            'color' => '#d90410',
            'icon' => 'degats',
        ],
        'Défensif' => [
            'name' => 'Protection',
            'description' => 'Sort renforçant la défense ou générant un bouclier.',
            'color' => '#8805fb',
            'icon' => 'protection',
        ],
        'Soin' => [
            'name' => 'Soin',
            'description' => 'Sort restaurant des points de vie.',
            'color' => '#f65db0',
            'icon' => 'soin',
        ],
        'Buff' => [
            'name' => 'Amélioration',
            'description' => 'Sort augmentant les caractéristiques ou capacités du lanceur ou d’un allié.',
            'color' => '#42bffd',
            'icon' => 'amelioration',
        ],
        'Debuff' => [
            'name' => 'Entrave',
            'description' => 'Sort affaiblissant, contrôlant ou limitant les actions d’un ennemi.',
            'color' => '#eeede9',
            'icon' => 'entrave',
        ],
        'Invocation' => [
            'name' => 'Invocation',
            'description' => 'Sort faisant apparaître une créature ou un objet sur le terrain.',
            'color' => '#fdd901',
            'icon' => 'invocation',
        ],
        'Téléportation' => [
            'name' => 'Placement',
            'description' => 'Sort de déplacement, repositionnement ou téléportation.',
            'color' => '#aad12a',
            'icon' => 'placement',
        ],
        'Transformation' => [
            'name' => 'Tank',
            'description' => 'Sort orienté encaissement, aggro ou absorption de dégâts.',
            'color' => '#f36702',
            'icon' => 'tank',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemUser = User::getSystemUser();
        $createdBy = $systemUser ? $systemUser->id : null;

        $defaults = [
            'state' => 'playable',
            'show_in_catalog' => true,
            'allow_scrap' => true,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $createdBy,
        ];

        foreach (self::ORIENTATION_TYPES as $legacyName => $attrs) {
            $row = SpellType::query()
                ->where('name', $legacyName)
                ->orWhere('name', $attrs['name'])
                ->first();

            if ($row !== null) {
                $row->fill(array_merge($attrs, [
                    'show_in_catalog' => true,
                    'allow_scrap' => true,
                ]));
                if ($row->created_by === null && $createdBy !== null) {
                    $row->created_by = $createdBy;
                }
                $row->save();

                continue;
            }

            SpellType::query()->create(array_merge($defaults, $attrs));
        }

        $this->command?->info('✅ Types de sorts alignés sur les orientations : '.count(self::ORIENTATION_TYPES));
    }
}
