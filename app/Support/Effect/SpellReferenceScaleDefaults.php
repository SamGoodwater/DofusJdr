<?php

declare(strict_types=1);

namespace App\Support\Effect;

/**
 * Constantes des barèmes de sorts (clés caractéristiques, paliers).
 *
 * Source de vérité runtime : `characteristic_*.norms_grid` (éventuellement
 * générée via `norms_formula`). Plus de grilles ni de PA_max codés en dur.
 *
 * @example
 * $key = SpellReferenceScaleDefaults::characteristicKey(SpellReferenceScaleDefaults::ACTION_DAMAGE);
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

    /** Clés characteristic_* pour chaque action. */
    public const CHARACTERISTIC_KEYS = [
        self::ACTION_DAMAGE => 'dommages_spell',
        self::ACTION_HEAL => 'soin_spell',
        self::ACTION_SHIELD => 'bouclier_spell',
        self::ACTION_TEMP_HP => 'pv_temporaires_spell',
        self::ACTION_LIFE_STEAL => 'vol_vie_spell',
    ];

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
