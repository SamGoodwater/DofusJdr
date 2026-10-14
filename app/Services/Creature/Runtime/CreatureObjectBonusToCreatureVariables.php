<?php

declare(strict_types=1);

namespace App\Services\Creature\Runtime;

use App\Contracts\Characteristic\CharacteristicDefinitionLookup;

/**
 * Mappe les bonus agrégés d'objets (clés courtes) vers les clés de caractéristiques créature,
 * **sans** les additionner dans la variable de base.
 *
 * Les totaux restent séparés pour permettre la décomposition base / objets / contexte.
 *
 * @example
 *   $map = $merger->mapToCharacteristicKeys($entity, ['strength' => 2, 'athletics' => 1]);
 *   // ['strength_creature' => 2, 'athletics_creature' => 1]
 *
 * Les bonus compétences objet se rattachent à la clé stable `*_creature` (couche object
 * du runtime), tandis que la formule conserve `[…_bonus]` pour le bonus BDD créature.
 */
final class CreatureObjectBonusToCreatureVariables
{
    /**
     * Clé JSON objet (sans _object) → clé caractéristique créature (totaux compétences).
     *
     * @var array<string, string>
     */
    private const SKILL_SHORT_KEY_TO_BONUS_VARIABLE = [
        'athletics' => 'athletics_creature',
        'intimidation' => 'intimidation_creature',
        'acrobatics' => 'acrobatics_creature',
        'stealth' => 'stealth_creature',
        'sleight_of_hand' => 'sleight_of_hand_creature',
        'arcana' => 'arcana_creature',
        'history' => 'history_creature',
        'investigation' => 'investigation_creature',
        'nature' => 'nature_creature',
        'religion' => 'religion_creature',
        'animal_handling' => 'animal_handling_creature',
        'medicine' => 'medicine_creature',
        'perception' => 'perception_creature',
        'insight' => 'insight_creature',
        'survival' => 'survival_creature',
        'persuasion' => 'persuasion_creature',
        'performance' => 'performance_creature',
        'deception' => 'deception_creature',
    ];

    /**
     * Variables bonus FR encore acceptées dans les formules (`[athletisme_bonus]`, etc.).
     *
     * @var array<string, string>
     */
    private const SKILL_SHORT_KEY_TO_FRENCH_BONUS_COLUMN = [
        'athletics' => 'athletisme_bonus',
        'intimidation' => 'intimidation_bonus',
        'acrobatics' => 'acrobatie_bonus',
        'stealth' => 'discretion_bonus',
        'sleight_of_hand' => 'escamotage_bonus',
        'arcana' => 'arcane_bonus',
        'history' => 'histoire_bonus',
        'investigation' => 'investigation_bonus',
        'nature' => 'nature_bonus',
        'religion' => 'religion_bonus',
        'animal_handling' => 'dressage_bonus',
        'medicine' => 'medecine_bonus',
        'perception' => 'perception_bonus',
        'insight' => 'perspicacite_bonus',
        'survival' => 'survie_bonus',
        'persuasion' => 'persuasion_bonus',
        'performance' => 'representation_bonus',
        'deception' => 'supercherie_bonus',
    ];

    public function __construct(
        private readonly CharacteristicDefinitionLookup $getter
    ) {}

    /**
     * Noms de variables bonus compétences (français) autorisés dans les formules créature.
     *
     * @return list<string>
     */
    public static function frenchSkillBonusVariableNames(): array
    {
        return array_values(self::SKILL_SHORT_KEY_TO_FRENCH_BONUS_COLUMN);
    }

    /**
     * @return array<string, string>
     */
    public static function skillShortKeyMap(): array
    {
        return self::SKILL_SHORT_KEY_TO_BONUS_VARIABLE;
    }

    /**
     * Convertit les totaux d'objets (clés courtes) en map clé caractéristique => montant.
     *
     * @param  array<string, int>  $itemTotals
     * @return array<string, int>
     */
    public function mapToCharacteristicKeys(string $entity, array $itemTotals): array
    {
        $mapped = [];
        foreach ($itemTotals as $shortKey => $amount) {
            if ($amount === 0) {
                continue;
            }
            $target = $this->resolveTargetVariable((string) $shortKey, $entity);
            if ($target === null) {
                continue;
            }
            $mapped[$target] = ($mapped[$target] ?? 0) + (int) $amount;
        }

        return $mapped;
    }

    private function resolveTargetVariable(string $objectShortKey, string $entity): ?string
    {
        if (isset(self::SKILL_SHORT_KEY_TO_BONUS_VARIABLE[$objectShortKey])) {
            return self::SKILL_SHORT_KEY_TO_BONUS_VARIABLE[$objectShortKey];
        }

        $candidates = match ($objectShortKey) {
            'life_points_max' => ['life_points_creature'],
            'wakfu_recharge', 'wakfu_reserve' => ['wakfu_reserve_creature'],
            default => [$objectShortKey.'_creature'],
        };

        foreach ($candidates as $creatureKey) {
            if ($this->getter->getDefinition($creatureKey, $entity) !== null) {
                return $creatureKey;
            }
        }

        return null;
    }
}
