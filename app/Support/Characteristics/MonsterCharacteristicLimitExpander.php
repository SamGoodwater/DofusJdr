<?php

declare(strict_types=1);

namespace App\Support\Characteristics;

/**
 * Élargit les bornes numériques des caractéristiques créature pour les monstres uniquement.
 *
 * Chaque côté de l’intervalle PJ (`*`) gagne 50 % de l’amplitude ; le min peut devenir négatif.
 * Ne pas l'appliquer aux limites absolues (`limit_absolute`) : leurs valeurs monstre sont fixées à la main
 * (mods 10, compétences et sauvegardes ±20).
 */
final class MonsterCharacteristicLimitExpander
{
    /** Stems dont les bornes restent métier (niveau, dés, hostilité). */
    public const SKIP_STEMS = [
        'level',
        'hostility',
        'hit_dice',
        'life_dice',
    ];

    /**
     * @return array{0: int, 1: int}
     */
    public static function expand(int $min, int $max): array
    {
        $span = $max - $min;
        $delta = $span === 0
            ? max(1, (int) round(abs($max) * 0.5))
            : (int) round($span * 0.5);

        return [$min - $delta, $max + $delta];
    }

    /**
     * Conserve le plus large des deux intervalles (overlay monstre déjà plus souple, etc.).
     *
     * @return array{0: int, 1: int}
     */
    public static function mergeLooser(int $expandedMin, int $expandedMax, ?int $existingMin, ?int $existingMax): array
    {
        return [
            min($expandedMin, $existingMin ?? $expandedMin),
            max($expandedMax, $existingMax ?? $expandedMax),
        ];
    }

    public static function parseStaticInt(mixed $raw): ?int
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (is_int($raw)) {
            return $raw;
        }
        if (is_float($raw)) {
            return (int) $raw;
        }
        $s = trim((string) $raw);

        return preg_match('/^-?\d+$/', $s) === 1 ? (int) $s : null;
    }

    public static function shouldSkipStem(string $stem): bool
    {
        return in_array($stem, self::SKIP_STEMS, true);
    }
}
