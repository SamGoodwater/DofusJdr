<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Nature d'une caractéristique (rôle, indépendant du groupe).
 *
 * @example CharacteristicNature::BuffDebuff->label() // « Buff / Débuff / Vol »
 */
enum CharacteristicNature: string
{
    case Descriptif = 'descriptif';
    case Intrinsic = 'intrinsic';
    case BonusMalus = 'bonus_malus';
    case BuffDebuff = 'buff_debuff';
    case ActionValue = 'action_value';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Descriptif => 'Descriptif',
            self::Intrinsic => 'Intrinsèque',
            self::BonusMalus => 'Bonus / Malus',
            self::BuffDebuff => 'Buff / Débuff / Vol',
            self::ActionValue => 'Valeur d\'action',
        };
    }

    /**
     * Les natures qui doivent pointer une intrinsèque créature.
     */
    public function requiresIntrinsicLink(): bool
    {
        return $this === self::BonusMalus || $this === self::BuffDebuff;
    }
}
