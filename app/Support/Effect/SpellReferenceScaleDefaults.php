<?php

declare(strict_types=1);

namespace App\Support\Effect;

/**
 * Grilles de barèmes de sorts (lancement 3 PA) alignées sur les règles 5.3.1.
 *
 * Source de vérité runtime : `characteristic_spell.norms_grid`.
 * Ces constantes servent de repli (tests, workers hors cache) et de référence
 * pour le seed / la doc. Courbe tour neutre dégâts : round(8 + (L−1)×2.1).
 *
 * @example
 * $grid = SpellReferenceScaleDefaults::grid(SpellReferenceScaleDefaults::ACTION_DAMAGE);
 */
final class SpellReferenceScaleDefaults
{
    public const ACTION_DAMAGE = 'damage';

    public const ACTION_HEAL = 'heal';

    public const ACTION_SHIELD = 'shield';

    public const ACTION_TEMP_HP = 'temp_hp';

    public const ACTION_LIFE_STEAL = 'life_steal';

    /** @var list<string> */
    public const POWER_LEVELS = [
        'very_weak',
        'weak',
        'neutral',
        'strong',
        'very_strong',
    ];

    /** PA disponibles par tour selon le niveau (index 0 = niveau 1). */
    public const MAX_ACTION_POINTS = [
        6, 6, 6, 7, 7, 7, 8, 8, 8, 9,
        9, 9, 10, 10, 10, 11, 11, 11, 12, 12,
    ];

    /** Clés characteristic_* pour chaque action. */
    public const CHARACTERISTIC_KEYS = [
        self::ACTION_DAMAGE => 'dommages_spell',
        self::ACTION_HEAL => 'soin_spell',
        self::ACTION_SHIELD => 'bouclier_spell',
        self::ACTION_TEMP_HP => 'pv_temporaires_spell',
        self::ACTION_LIFE_STEAL => 'vol_vie_spell',
    ];

    /**
     * Budgets par lancement (3 PA), 5 paliers × 20 niveaux.
     *
     * @var array<string, array<string, list<int>>>
     */
    private const CAST_GRIDS = [
        self::ACTION_DAMAGE => [
            'very_weak' => [2, 3, 3, 3, 3, 4, 4, 5, 5, 5, 5, 5, 5, 5, 6, 6, 6, 6, 6, 6],
            'weak' => [3, 4, 5, 5, 5, 6, 6, 6, 7, 7, 7, 8, 8, 8, 8, 8, 9, 9, 9, 9],
            'neutral' => [4, 5, 6, 6, 7, 8, 8, 9, 9, 9, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12],
            'strong' => [5, 7, 8, 8, 9, 10, 10, 11, 12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 15, 15],
            'very_strong' => [6, 8, 9, 9, 10, 12, 12, 13, 14, 14, 15, 16, 16, 16, 17, 17, 17, 18, 18, 18],
        ],
        self::ACTION_HEAL => [
            'very_weak' => [2, 2, 3, 3, 3, 3, 3, 3, 4, 4, 4, 4, 4, 4, 5, 5, 5, 5, 5, 5],
            'weak' => [3, 3, 4, 4, 4, 5, 5, 5, 6, 6, 6, 6, 6, 6, 7, 7, 7, 7, 7, 7],
            'neutral' => [3, 4, 5, 5, 6, 6, 6, 7, 8, 8, 8, 8, 8, 8, 9, 9, 9, 10, 10, 10],
            'strong' => [4, 5, 6, 6, 7, 8, 8, 9, 9, 9, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12],
            'very_strong' => [5, 6, 7, 7, 8, 10, 10, 11, 11, 11, 12, 12, 12, 13, 13, 13, 14, 14, 14, 15],
        ],
        self::ACTION_LIFE_STEAL => [
            'very_weak' => [1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 3, 3, 3],
            'weak' => [2, 2, 3, 3, 3, 3, 3, 3, 3, 3, 4, 4, 4, 4, 4, 4, 4, 5, 5, 5],
            'neutral' => [2, 3, 3, 3, 3, 4, 4, 5, 5, 5, 5, 5, 5, 5, 6, 6, 6, 6, 6, 6],
            'strong' => [3, 3, 4, 4, 4, 5, 5, 5, 6, 6, 6, 6, 6, 7, 7, 7, 7, 8, 8, 8],
            'very_strong' => [3, 4, 5, 5, 5, 6, 6, 6, 7, 7, 7, 8, 8, 8, 8, 8, 9, 9, 9, 9],
        ],
    ];

    /**
     * @return array<string, list<int>>
     */
    public static function grid(string $action): array
    {
        if ($action === self::ACTION_SHIELD || $action === self::ACTION_TEMP_HP) {
            return self::CAST_GRIDS[self::ACTION_HEAL];
        }

        return self::CAST_GRIDS[$action] ?? self::CAST_GRIDS[self::ACTION_DAMAGE];
    }

    public static function characteristicKey(string $action): string
    {
        return self::CHARACTERISTIC_KEYS[$action] ?? self::CHARACTERISTIC_KEYS[self::ACTION_DAMAGE];
    }

    /**
     * Modificateur de caractéristique typique (combat) : ⌊niv/4⌋+1.
     */
    public static function typicalModifier(int $level): int
    {
        $level = max(1, min(20, $level));

        return intdiv($level, 4) + 1;
    }
}
