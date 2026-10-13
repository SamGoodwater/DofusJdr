<?php

declare(strict_types=1);

namespace App\Services\Characteristics;

use App\Support\Characteristics\CharacteristicDefinitionNaming;

/**
 * Règles qualité éditoriales pour les JSON characteristic-definitions (release 1.3.2).
 */
final class CharacteristicDefinitionQualityService
{
    /** @var list<string> */
    private const NORMS_EXEMPT_STEMS = [
        'name', 'description', 'price', 'weight', 'level', 'rarity', 'category', 'casting_time',
        'hostility', 'life_dice',
        'duration', 'resolution_mode', 'save_dc_formula', 'save_success_note', 'spell_type',
        'attack_characteristic_key', 'save_characteristic_key', 'element', 'is_magic', 'is_passive',
        'allows_reaction', 'range_editable', 'ritual_available', 'sight_line', 'auto_success_if_willing_target',
        'cast_in_line', 'cast_in_diagonal', 'target_type', 'max_stack', 'global_cooldown',
        'life_dice', 'hit_dice', 'hostility', 'mastery_bonus', 'wakfu_reserve', 'wakfu_recharge',
        'life_points_restore', 'shield_points', 'temporary_life_points',
    ];

    /**
     * @param  array<string, mixed>  $definition
     * @return list<string>
     */
    public function qualityIssues(string $path, array $definition): array
    {
        $issues = [];
        $key = (string) ($definition['characteristic']['key'] ?? '');
        $parsed = CharacteristicDefinitionNaming::parseCharacteristicKey($key);
        if ($parsed === null) {
            return ['clé non parsable'];
        }

        $type = (string) ($definition['characteristic']['type'] ?? 'int');
        $helper = (string) ($definition['characteristic']['helper'] ?? '');
        $entities = $definition['entities'] ?? [];
        if (! is_array($entities)) {
            return ['entities invalide'];
        }

        $star = $entities['*'] ?? null;
        $hasEntityRows = $star !== null || $this->hasNonWildcardEntityRows($entities);
        if (! $hasEntityRows && empty($definition['characteristic']['linked_to_key'])) {
            $issues[] = 'entities[*] manquant';

            return $issues;
        }

        if (is_array($star)) {
            $issues = array_merge($issues, $this->issuesForEntityRow($parsed, $type, $helper, '*', $star));
        }

        $monster = $entities['monster'] ?? null;
        if (is_array($monster)) {
            $issues = array_merge($issues, $this->issuesForEntityRow($parsed, $type, $helper, 'monster', $monster));
        }

        return $issues;
    }

    /**
     * @param  array{stem: string, group: string}  $parsed
     * @param  array<string, mixed>  $row
     * @return list<string>
     */
    private function issuesForEntityRow(array $parsed, string $type, string $helper, string $entityKey, array $row): array
    {
        $issues = [];
        $stem = $parsed['stem'];
        $group = $parsed['group'];

        // La grille de normes est portée par `*` (référence éditoriale), pas par chaque overlay.
        if ($entityKey === '*' && $this->requiresNormsGrid($group, $type, $stem)) {
            $grid = $row['norms_grid'] ?? null;
            if ($grid === null || $grid === [] || $grid === '') {
                $issues[] = 'norms_grid manquant';
            }
        }

        if ($entityKey === '*' && $group === 'object' && $this->helperImpliesItemTypeRestriction($helper)) {
            $dofus = $row['item_type_dofus_ids'] ?? null;
            $ids = $row['item_type_ids'] ?? null;
            $hasRestriction = is_array($dofus) && $dofus !== [] || is_array($ids) && $ids !== [];
            if (! $hasRestriction) {
                $issues[] = 'item_type_dofus_ids manquant (helper équipement ciblé)';
            }
        }

        if ($this->expectsConversion($group, $type, $stem, $entityKey)) {
            $formula = $row['conversion_formula'] ?? null;
            if ($formula === null || $formula === '') {
                $issues[] = $entityKey === 'monster'
                    ? 'conversion_formula vide (overlay monster)'
                    : 'conversion_formula vide';
            }
        }

        $displayIssue = self::formulaDisplayIssue($row['formula_display'] ?? null);
        if ($displayIssue !== null) {
            $issues[] = $displayIssue;
        }

        return $issues;
    }

    /**
     * Formule affichée : texte joueur abrégé, sans clé brute ni terme consommable / sort.
     *
     * @example formulaDisplayIssue('mod. Int + équip.') // null
     * @example formulaDisplayIssue('[initiative_spell]') // 'formula_display : clé brute ou terme éphémère'
     */
    public static function formulaDisplayIssue(mixed $display): ?string
    {
        if (! is_string($display) || trim($display) === '' || str_starts_with(trim($display), '{')) {
            return null;
        }
        if (preg_match('/\[|_(creature|object|spell|consumable|profile)\b/', $display) === 1) {
            return 'formula_display : clé brute ou terme éphémère';
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $entities
     */
    private function hasNonWildcardEntityRows(array $entities): bool
    {
        foreach ($entities as $key => $row) {
            if ($key !== '*' && is_array($row)) {
                return true;
            }
        }

        return false;
    }

    private function requiresNormsGrid(string $group, string $type, string $stem): bool
    {
        if (in_array($type, ['string', 'bool'], true)) {
            return false;
        }
        if (in_array($stem, self::NORMS_EXEMPT_STEMS, true)) {
            return false;
        }
        if (str_ends_with($stem, '_passive') && $group === 'object') {
            return true;
        }
        if ($group === 'creature' && $this->isCreatureCompetenceStem($stem)) {
            return false;
        }

        return in_array($group, ['creature', 'object', 'spell'], true);
    }

    private function expectsConversion(string $group, string $type, string $stem, string $entityKey = '*'): bool
    {
        if (in_array($type, ['string', 'bool'], true)) {
            return false;
        }
        if (in_array($stem, self::NORMS_EXEMPT_STEMS, true)) {
            return false;
        }

        if ($group === 'object' && $this->isObjectCompetenceBonusStem($stem)) {
            return true;
        }

        // Import monstre : la courbe jouable est sur l’overlay `monster`.
        if ($group === 'creature' && $entityKey === 'monster') {
            return ! str_starts_with($stem, 'modifier_')
                && ! str_starts_with($stem, 'save_')
                && ! $this->isCreatureCompetenceStem($stem);
        }

        return $group === 'spell' || ($group === 'object' && ! str_contains($stem, 'passive'));
    }

    private function isCreatureCompetenceStem(string $stem): bool
    {
        if (str_ends_with($stem, '_passive') || str_ends_with($stem, '_mastery')) {
            return true;
        }

        return in_array($stem, [
            'acrobatics', 'animal_handling', 'arcana', 'athletics', 'deception', 'history',
            'insight', 'intimidation', 'investigation', 'medicine', 'nature', 'perception',
            'performance', 'persuasion', 'religion', 'sleight_of_hand', 'stealth', 'survival',
        ], true);
    }

    public function isObjectCompetenceBonusStem(string $stem): bool
    {
        if (str_ends_with($stem, '_passive')) {
            return false;
        }

        return in_array($stem, [
            'acrobatics', 'animal_handling', 'arcana', 'athletics', 'deception', 'history',
            'insight', 'intimidation', 'investigation', 'medicine', 'nature', 'perception',
            'performance', 'persuasion', 'religion', 'sleight_of_hand', 'stealth', 'survival',
        ], true);
    }

    public function helperImpliesItemTypeRestriction(string $helper): bool
    {
        return $this->suggestedDofusTypeIdsForHelper($helper) !== [];
    }

    /**
     * @return list<int>
     */
    public function suggestedDofusTypeIdsForHelper(string $helper): array
    {
        $h = mb_strtolower($helper);
        $ids = [];
        if (str_contains($h, 'amulette')) {
            $ids[] = 1;
        }
        if (str_contains($h, 'anneau')) {
            $ids[] = 9;
        }
        if (str_contains($h, 'ceinture')) {
            $ids[] = 10;
        }
        if (str_contains($h, 'botte')) {
            $ids[] = 11;
        }
        if (str_contains($h, 'chapeau')) {
            $ids[] = 16;
        }
        if (str_contains($h, 'cape')) {
            $ids[] = 17;
        }
        // Emplacement équipement « bouclier(s) » uniquement — pas « points de bouclier » (conso).
        if (str_contains($h, 'bouclier') && ! str_contains($h, 'points de bouclier')) {
            $ids[] = 82;
        }
        if ($this->helperMentionsWeapons($h)) {
            $ids = array_merge($ids, [2, 3, 4, 5, 6, 7, 8, 19, 21, 22, 114, 271]);
        }

        return array_values(array_unique($ids));
    }

    /**
     * Détecte une mention d'arme dans l'aide (évite le faux positif « arc » ⊂ « Arcanes »).
     */
    private function helperMentionsWeapons(string $helperLower): bool
    {
        if (str_contains($helperLower, 'arme')
            || str_contains($helperLower, 'baguette')
            || str_contains($helperLower, 'bâton')
            || str_contains($helperLower, 'baton')
            || str_contains($helperLower, 'dague')
            || str_contains($helperLower, 'épée')
            || str_contains($helperLower, 'epee')
            || str_contains($helperLower, 'marteau')
            || str_contains($helperLower, 'pelle')
            || str_contains($helperLower, 'hache')
            || str_contains($helperLower, 'pioche')
            || str_contains($helperLower, 'faux')
            || str_contains($helperLower, 'lance')) {
            return true;
        }

        // Mot entier « arc » / « arcs » uniquement (pas « arcanes »).
        return (bool) preg_match('/\barcs?\b/u', $helperLower);
    }
}
