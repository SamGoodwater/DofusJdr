<?php

declare(strict_types=1);

namespace App\Support\Profile;

/**
 * Colonnes de maîtrise sur breeds, specializations et monsters (groupe Profil).
 *
 * Compétences : 0 non formé, 1 maîtrise, 2 expertise.
 * Sauvegardes : 0 ou 1.
 */
final class ProfileMasteryColumns
{
    /**
     * Stem anglais → colonne SQL (même nom).
     *
     * @var list<string>
     */
    public const SKILL_COLUMNS = [
        'acrobatics_mastery',
        'animal_handling_mastery',
        'arcana_mastery',
        'athletics_mastery',
        'deception_mastery',
        'history_mastery',
        'insight_mastery',
        'intimidation_mastery',
        'investigation_mastery',
        'medicine_mastery',
        'nature_mastery',
        'perception_mastery',
        'performance_mastery',
        'persuasion_mastery',
        'religion_mastery',
        'sleight_of_hand_mastery',
        'stealth_mastery',
        'survival_mastery',
    ];

    /**
     * @var list<string>
     */
    public const SAVE_COLUMNS = [
        'save_strength_mastery',
        'save_intelligence_mastery',
        'save_agility_mastery',
        'save_chance_mastery',
        'save_vitality_mastery',
        'save_wisdom_mastery',
    ];

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [...self::SKILL_COLUMNS, ...self::SAVE_COLUMNS];
    }

    public static function maxFor(string $column): int
    {
        return in_array($column, self::SAVE_COLUMNS, true) ? 1 : 2;
    }
}
