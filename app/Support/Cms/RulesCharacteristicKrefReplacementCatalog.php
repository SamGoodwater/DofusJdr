<?php

namespace App\Support\Cms;

/**
 * Catalogue des remplacements texte → shortcode {@code [[kref:characteristic:clé|libellé]]}
 * pour les fichiers Markdown des règles (hors blocs masqués).
 *
 * @see private/game/rules/REFERENCE_KREF_CONVERSIONS_CARACTERISTIQUES.md
 */
final class RulesCharacteristicKrefReplacementCatalog
{
    /**
     * @return list<array{0: string, 1: string}> Paires [libellé à matcher, clé BDD]
     */
    public static function orderedPairs(): array
    {
        $pairs = [
            ['Nombre d’invocations', 'summoning_creature'],
            ["Nombre d'invocations", 'summoning_creature'],
            ['Points d’action (PA)', 'action_points_creature'],
            ["Points d'action (PA)", 'action_points_creature'],
            ['Points de mouvement (PM)', 'movement_points_creature'],
            ['Points de vie (PV)', 'life_points_creature'],
            ['Classe d’armure (CA)', 'armor_class_creature'],
            ["Classe d'armure (CA)", 'armor_class_creature'],
            ['Portée (PO)', 'range_creature'],
            ['Points d’action', 'action_points_creature'],
            ["Points d'action", 'action_points_creature'],
            ['Points de Mouvement', 'movement_points_creature'],
            ['Points de mouvement', 'movement_points_creature'],
            ['Points de Vie', 'life_points_creature'],
            ['Points de vie', 'life_points_creature'],
            ['Classe d’Armure', 'armor_class_creature'],
            ['Classe d’armure', 'armor_class_creature'],
            ["Classe d'Armure", 'armor_class_creature'],
            ["Classe d'armure", 'armor_class_creature'],
            ['Réserve de Wakfu', 'wakfu_reserve_creature'],
            ['Bonus de maîtrise', 'mastery_bonus_creature'],
            ['Esquive PA', 'dodge_action_points_creature'],
            ['Esquive PM', 'dodge_movement_points_creature'],
            // Modificateurs / sauvegardes / DD (clés calculées *_creature) — avant les noms seuls.
            // DD avant « Jet de sauvegarde » / « Sauvegarde » (préfixe plus long).
            ['DD de sauvegarde de Vitalité', 'save_dc_vitality_creature'],
            ['DD de sauvegarde de Sagesse', 'save_dc_wisdom_creature'],
            ['DD de sauvegarde de Force', 'save_dc_strength_creature'],
            ['DD de sauvegarde d’Intelligence', 'save_dc_intelligence_creature'],
            ["DD de sauvegarde d'Intelligence", 'save_dc_intelligence_creature'],
            ['DD de sauvegarde de Chance', 'save_dc_chance_creature'],
            ['DD de sauvegarde d’Agilité', 'save_dc_agility_creature'],
            ["DD de sauvegarde d'Agilité", 'save_dc_agility_creature'],
            ['DD de save de Vitalité', 'save_dc_vitality_creature'],
            ['DD de save de Sagesse', 'save_dc_wisdom_creature'],
            ['DD de save de Force', 'save_dc_strength_creature'],
            ['DD de save d’Intelligence', 'save_dc_intelligence_creature'],
            ["DD de save d'Intelligence", 'save_dc_intelligence_creature'],
            ['DD de save de Chance', 'save_dc_chance_creature'],
            ['DD de save d’Agilité', 'save_dc_agility_creature'],
            ["DD de save d'Agilité", 'save_dc_agility_creature'],
            ['Jet de sauvegarde de Vitalité', 'save_vitality_creature'],
            ['Jet de sauvegarde de Sagesse', 'save_wisdom_creature'],
            ['Jet de sauvegarde de Force', 'save_strength_creature'],
            ['Jet de sauvegarde d’Intelligence', 'save_intelligence_creature'],
            ["Jet de sauvegarde d'Intelligence", 'save_intelligence_creature'],
            ['Jet de sauvegarde de Chance', 'save_chance_creature'],
            ['Jet de sauvegarde d’Agilité', 'save_agility_creature'],
            ["Jet de sauvegarde d'Agilité", 'save_agility_creature'],
            ['jet de sauvegarde de Vitalité', 'save_vitality_creature'],
            ['jet de sauvegarde de Sagesse', 'save_wisdom_creature'],
            ['jet de sauvegarde de Force', 'save_strength_creature'],
            ['jet de sauvegarde d’Intelligence', 'save_intelligence_creature'],
            ["jet de sauvegarde d'Intelligence", 'save_intelligence_creature'],
            ['jet de sauvegarde de Chance', 'save_chance_creature'],
            ['jet de sauvegarde d’Agilité', 'save_agility_creature'],
            ["jet de sauvegarde d'Agilité", 'save_agility_creature'],
            ['Sauvegarde de Vitalité', 'save_vitality_creature'],
            ['Sauvegarde de Sagesse', 'save_wisdom_creature'],
            ['Sauvegarde de Force', 'save_strength_creature'],
            ['Sauvegarde d’Intelligence', 'save_intelligence_creature'],
            ["Sauvegarde d'Intelligence", 'save_intelligence_creature'],
            ['Sauvegarde de Chance', 'save_chance_creature'],
            ['Sauvegarde d’Agilité', 'save_agility_creature'],
            ["Sauvegarde d'Agilité", 'save_agility_creature'],
            ['sauvegarde de Vitalité', 'save_vitality_creature'],
            ['sauvegarde de Sagesse', 'save_wisdom_creature'],
            ['sauvegarde de Force', 'save_strength_creature'],
            ['sauvegarde d’Intelligence', 'save_intelligence_creature'],
            ["sauvegarde d'Intelligence", 'save_intelligence_creature'],
            ['sauvegarde de Chance', 'save_chance_creature'],
            ['sauvegarde d’Agilité', 'save_agility_creature'],
            ["sauvegarde d'Agilité", 'save_agility_creature'],
            ['Modificateur de Vitalité', 'modifier_vitality_creature'],
            ['Modificateur de Sagesse', 'modifier_wisdom_creature'],
            ['Modificateur de Force', 'modifier_strength_creature'],
            ['Modificateur d’Intelligence', 'modifier_intelligence_creature'],
            ["Modificateur d'Intelligence", 'modifier_intelligence_creature'],
            ['Modificateur de Chance', 'modifier_chance_creature'],
            ['Modificateur d’Agilité', 'modifier_agility_creature'],
            ["Modificateur d'Agilité", 'modifier_agility_creature'],
            ['modificateur de Vitalité', 'modifier_vitality_creature'],
            ['modificateur de Sagesse', 'modifier_wisdom_creature'],
            ['modificateur de Force', 'modifier_strength_creature'],
            ['modificateur d’Intelligence', 'modifier_intelligence_creature'],
            ["modificateur d'Intelligence", 'modifier_intelligence_creature'],
            ['modificateur de Chance', 'modifier_chance_creature'],
            ['modificateur d’Agilité', 'modifier_agility_creature'],
            ["modificateur d'Agilité", 'modifier_agility_creature'],
            // Bonus d’équipement (objets) — clés *_object
            ['bonus d’équipement du tacle', 'tackle_object'],
            ["bonus d'équipement du tacle", 'tackle_object'],
            ['bonus d’équipement de la fuite', 'dodge_object'],
            ["bonus d'équipement de la fuite", 'dodge_object'],
            ['Bonus d’équipement du tacle', 'tackle_object'],
            ["Bonus d'équipement du tacle", 'tackle_object'],
            ['Bonus d’équipement de la fuite', 'dodge_object'],
            ["Bonus d'équipement de la fuite", 'dodge_object'],
            // Résistances % et fixes (créature, aligné BDD)
            ['Résistance Vitalité %', 'resistance_vitalite_creature'],
            ['Résistance Sagesse %', 'resistance_sagesse_creature'],
            ['Résistance Eau %', 'resistance_water_creature'],
            ['Résistance Air %', 'resistance_air_creature'],
            ['Résistance Feu %', 'resistance_fire_creature'],
            ['Résistance Terre %', 'resistance_earth_creature'],
            ['Résistance Neutre %', 'resistance_neutral_creature'],
            ['Résistance fixe Eau', 'fixed_resistance_water_creature'],
            ['Résistance fixe Air', 'fixed_resistance_air_creature'],
            ['Résistance fixe Feu', 'fixed_resistance_fire_creature'],
            ['Résistance fixe Terre', 'fixed_resistance_earth_creature'],
            ['Résistance fixe Neutre', 'fixed_resistance_neutral_creature'],
            ['Intelligence', 'intelligence_creature'],
            ['Vitalité', 'vitality_creature'],
            ['Agilité', 'agility_creature'],
            ['Sagesse', 'wisdom_creature'],
            ['Chance', 'chance_creature'],
            ['Tacle', 'tackle_creature'],
            ['Force', 'strength_creature'],
            ['Représentation', 'performance_creature'],
            ['Investigation', 'investigation_creature'],
            ['Intimidation', 'intimidation_creature'],
            ['Perspicacité', 'insight_creature'],
            ['Persuasion', 'persuasion_creature'],
            ['Acrobaties', 'acrobatics_creature'],
            ['Athlétisme', 'athletics_creature'],
            ['Discrétion', 'stealth_creature'],
            ['Escamotage', 'sleight_of_hand_creature'],
            ['Médecine', 'medicine_creature'],
            ['Religion', 'religion_creature'],
            ['Dressage', 'animal_handling_creature'],
            ['Survie', 'survival_creature'],
            ['Histoire', 'history_creature'],
            ['Perception', 'perception_creature'],
            ['Supercherie', 'deception_creature'],
            ['Tromperie', 'deception_creature'],
            ['Nature', 'nature_creature'],
            ['Arcanes', 'arcana_creature'],
            ['Arcane', 'arcana_creature'],
            ['Acrobatie', 'acrobatics_creature'],
            ['Herbaliste', 'nature_creature'],
        ];

        usort($pairs, static fn (array $a, array $b): int => mb_strlen($b[0], 'UTF-8') <=> mb_strlen($a[0], 'UTF-8'));

        return $pairs;
    }

    /**
     * Abréviations courantes (contexte JDR) → shortcode avec libellé court.
     *
     * @return list<array{0: string, 1: string, 2: string}> [regex sans délimiteurs, clé, libellé affiché]
     */
    public static function abbreviationPatterns(): array
    {
        return [
            ['(?<![\p{L}\p{N}_])PA(?![\p{L}\p{N}_])', 'action_points_creature', 'PA'],
            ['(?<![\p{L}\p{N}_])PM(?![\p{L}\p{N}_])', 'movement_points_creature', 'PM'],
            ['(?<![\p{L}\p{N}_])PO(?![\p{L}\p{N}_])', 'range_creature', 'PO'],
            ['(?<![\p{L}\p{N}_])PV(?![\p{L}\p{N}_])', 'life_points_creature', 'PV'],
            ['(?<![\p{L}\p{N}_])CA(?![\p{L}\p{N}_])', 'armor_class_creature', 'CA'],
        ];
    }

    public static function applyToMarkdown(string $markdown): string
    {
        $markdown = KrefShortcodeReplacer::flattenNestedShortcodes($markdown);
        $placeholders = [];
        $masked = self::maskProtectedRegions($markdown, $placeholders);
        $masked = self::applyWordPairs($masked, $placeholders);
        $masked = self::applyAbbreviationPatterns($masked);

        return self::unmask($masked, $placeholders);
    }

    /**
     * @param  array<string, string>  $placeholders
     */
    private static function maskProtectedRegions(string $markdown, array &$placeholders): string
    {
        $i = 0;
        $next = static function (string $content) use (&$placeholders, &$i): string {
            $token = '%%KREF_INJECT_MASK_'.$i.'%%';
            $placeholders[$token] = $content;
            $i++;

            return $token;
        };

        $out = (string) preg_replace_callback(
            '/\[\[kref:[^\]]+\]\]/u',
            static fn (array $m) => $next($m[0]),
            $markdown
        );

        $out = (string) preg_replace_callback(
            '/```[\s\S]*?```/u',
            static fn (array $m) => $next($m[0]),
            $out
        );

        $out = (string) preg_replace_callback(
            '/`[^`\n]+`/u',
            static fn (array $m) => $next($m[0]),
            $out
        );

        return $out;
    }

    /**
     * @param  array<string, string>  $placeholders
     */
    private static function unmask(string $markdown, array $placeholders): string
    {
        if ($placeholders === []) {
            return $markdown;
        }

        return strtr($markdown, $placeholders);
    }

    /**
     * @param  array<string, string>  $placeholders
     */
    private static function applyWordPairs(string $markdown, array &$placeholders): string
    {
        foreach (self::orderedPairs() as [$label, $key]) {
            $quoted = preg_quote($label, '/');
            $replacement = '[[kref:characteristic:'.$key.'|'.$label.']]';
            // Limites « mot » Unicode : `\b` échoue après `%` (ex. « Résistance Eau % ») car `%` et
            // l’espace sont deux non-mots. Les `[[kref:…]]` insérés sont masqués après chaque passe
            // pour éviter qu’une sous-chaîne du libellé (ex. « sauvegarde d’Intelligence ») soit
            // re-matchée à l’intérieur du shortcode.
            $pattern = '/(?<![\p{L}\p{N}_])'.$quoted.'(?![\p{L}\p{N}_])/u';
            $next = preg_replace($pattern, $replacement, $markdown);
            $markdown = is_string($next) ? $next : $markdown;
            $markdown = self::maskInlineKrefShortcodes($markdown, $placeholders);
        }

        return $markdown;
    }

    /**
     * Remplace chaque shortcode {@code [[kref:…]]} par un jeton masqué pour les passes suivantes.
     *
     * @param  array<string, string>  $placeholders
     */
    private static function maskInlineKrefShortcodes(string $markdown, array &$placeholders): string
    {
        return (string) preg_replace_callback(
            '/\[\[kref:[^\]]+\]\]/u',
            static function (array $m) use (&$placeholders): string {
                $content = $m[0];
                foreach ($placeholders as $token => $stored) {
                    if ($stored === $content) {
                        return $token;
                    }
                }
                $i = 0;
                while (isset($placeholders['%%KREF_INJECT_MASK_'.$i.'%%'])) {
                    $i++;
                }
                $token = '%%KREF_INJECT_MASK_'.$i.'%%';
                $placeholders[$token] = $content;

                return $token;
            },
            $markdown
        );
    }

    private static function applyAbbreviationPatterns(string $markdown): string
    {
        foreach (self::abbreviationPatterns() as [$inner, $key, $display]) {
            $pattern = '/'.$inner.'/u';
            $replacement = '[[kref:characteristic:'.$key.'|'.$display.']]';
            $next = preg_replace($pattern, $replacement, $markdown);
            $markdown = is_string($next) ? $next : $markdown;
        }

        return $markdown;
    }
}
