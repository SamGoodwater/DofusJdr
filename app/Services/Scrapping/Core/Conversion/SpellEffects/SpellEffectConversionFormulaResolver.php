<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

/**
 * Résout la characteristic_key (groupe spell) à utiliser pour la conversion de la valeur
 * d'un sous-effet, selon l'action (sub_effect_slug) et les params (ex. characteristic).
 *
 * @see docs/features/effects/README.md
 */
final class SpellEffectConversionFormulaResolver
{
    /** Mapping action (sub_effect_slug) → characteristic_key pour les Type 2 action (dommages, soin, bouclier, PV temp). */
    private const ACTION_TO_CHARACTERISTIC = [
        'frapper' => 'dommages_spell',
        'soigner' => 'soin_spell',
        'protéger' => 'bouclier_spell',
        'donner-pv-temporaires' => 'pv_temporaires_spell',
    ];

    /** Mapping type de déplacement → characteristic_key pour convertir les cases. */
    private const MOVEMENT_KIND_TO_CHARACTERISTIC = [
        'movement' => 'movement_distance_spell',
        'jump' => 'jump_distance_spell',
        'teleport' => 'teleport_distance_spell',
        'pull' => 'pull_distance_spell',
        'push' => 'push_distance_spell',
        'pousser' => 'push_distance_spell',
        'repousser' => 'push_distance_spell',
    ];

    /** Vol de vie : sous-effet unique « frapper » + params.life_steal_formula (conversion Dofus sur la même base « d »). */
    public const LIFE_STEAL_CHARACTERISTIC_KEY = 'vol_vie_spell';

    /** Actions avec conversion par caractéristique (booster, retirer, voler-caracteristiques). */
    private const PER_CHARACTERISTIC_SLUGS = [
        'booster',
        'retirer',
        'voler-caracteristiques',
    ];

    /** Entité pour toutes les conversions d'effets de sort. */
    public const ENTITY_SPELL = 'spell';

    /** Clés courtes désactivées (caractéristiques retirées du groupe spell). */
    /**
     * Effets Dofus exclus du JDR (critiques avancés, poussée, pièges).
     * Liste conservée ici en attendant migration complète vers une table en base.
     *
     * @var list<string>
     */
    private const IGNORED_KEYS = [
        'echec_critique',
        'prospection',
        'power',
        'powerful',
        'do_sagesse',
        'do_vitalite',
        'res_sagesse',
        'res_vitalite',
        'fixed_damage_sagesse',
        'fixed_damage_vitalite',
        // Critiques avancés / poussée / pièges — hors périmètre Krosmoz
        'critiques',
        'critical_damage',
        'critical_damage_bonus',
        'critical_damage_reduction',
        'critical_resistance',
        'push_damage',
        'pushback_damage',
        'push_resistance',
        'pushback_resistance',
        'dommages_poussee',
        'dommages_de_poussee',
        'resistance_poussee',
        'trap_damage',
        'dommages_piege',
        'dommages_pieges',
    ];

    /**
     * Retourne la characteristic_key (groupe spell) pour appliquer la conversion, ou null si pas de conversion.
     *
     * @param  string  $subEffectSlug  Slug du sous-effet (frapper, soigner, booster, …)
     * @param  array<string, mixed>  $params  Params du sous-effet (characteristic, value_formula, …)
     * @return string|null Clé pour DofusConversionService (ex. action_points_variation_spell) ou null
     */
    public function resolveCharacteristicKeyForConversion(string $subEffectSlug, array $params): ?string
    {
        if ($subEffectSlug === 'frapper' && $this->resolveLifeStealCharacteristicKeyForConversion($params) !== null) {
            return self::LIFE_STEAL_CHARACTERISTIC_KEY;
        }

        if (isset(self::ACTION_TO_CHARACTERISTIC[$subEffectSlug])) {
            return self::ACTION_TO_CHARACTERISTIC[$subEffectSlug];
        }

        if ($subEffectSlug === 'déplacer') {
            $kind = $params['movement_kind'] ?? null;
            if (is_string($kind) && isset(self::MOVEMENT_KIND_TO_CHARACTERISTIC[$kind])) {
                return self::MOVEMENT_KIND_TO_CHARACTERISTIC[$kind];
            }
            if (($params['teleport'] ?? false) === true) {
                return self::MOVEMENT_KIND_TO_CHARACTERISTIC['teleport'];
            }

            return self::MOVEMENT_KIND_TO_CHARACTERISTIC['movement'];
        }

        if (in_array($subEffectSlug, self::PER_CHARACTERISTIC_SLUGS, true)) {
            $char = $params['characteristic'] ?? null;
            if (is_string($char) && $char !== '') {
                $char = trim($char);
                if (in_array($char, self::IGNORED_KEYS, true)) {
                    return null;
                }

                return $this->normalizeSpellKey($char);
            }

            return null;
        }

        return null;
    }

    /**
     * Clé de conversion pour le montant « PV volés » lorsque `life_steal_formula` est renseignée (frapper).
     */
    public function resolveLifeStealCharacteristicKeyForConversion(array $params): ?string
    {
        $raw = $params['life_steal_formula'] ?? null;
        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        return self::LIFE_STEAL_CHARACTERISTIC_KEY;
    }

    /** Mapping clés courtes (mapping DofusDB) → characteristic_key du groupe spell en BDD. */
    private const SPELL_KEY_ALIASES = [
        'pa' => 'action_points_variation_spell',
        'po' => 'range_spell',
        'pm' => 'movement_points_spell',
        'range' => 'range_spell',
        'movement_points' => 'movement_points_spell',
        'fuite' => 'dodge_spell',
        'tacle' => 'tackle_spell',
        'dodge' => 'dodge_spell',
        'tackle' => 'tackle_spell',
        'strong' => 'strong_spell',
        'vitality' => 'vitality_spell',
        'sagesse' => 'sagesse_spell',
        'chance' => 'chance_spell',
        'agi' => 'agi_spell',
        'intel' => 'intel_spell',
        'critical' => 'critical_spell',
        'res_terre' => 'res_terre_spell',
        'res_feu' => 'res_feu_spell',
        'res_eau' => 'res_eau_spell',
        'res_air' => 'res_air_spell',
        'res_neutre' => 'res_neutre_spell',
        'do_fixe_multiple' => 'do_fixe_multiple_spell',
        'esquive_pa' => 'dodge_action_points_spell',
        'esquive_pm' => 'dodge_movement_points_spell',
        'res_fixe_terre' => 'fixed_resistance_terre_spell',
        'hostility' => 'hostility_spell',
        'res_fixe_feu' => 'fixed_resistance_feu_spell',
        'res_fixe_eau' => 'fixed_resistance_eau_spell',
        'res_fixe_air' => 'fixed_resistance_air_spell',
        'res_fixe_neutre' => 'fixed_resistance_neutre_spell',
        // —— Type 2 creature (équivalents spell des caractéristiques creature) ——
        'ini' => 'initiative_spell',
        'initiative' => 'initiative_spell',
        'ca' => 'armor_class_spell',
        'armor_class' => 'armor_class_spell',
        'touch' => 'hit_bonus_spell',
        'hit_bonus' => 'hit_bonus_spell',
        'invocation' => 'summoning_spell',
        'invocations' => 'summoning_spell',
        'summoning' => 'summoning_spell',
        'heal_bonus' => 'heal_bonus_spell',
        'do_neutre' => 'fixed_damage_neutral_spell',
        'do_terre' => 'fixed_damage_earth_spell',
        'do_feu' => 'fixed_damage_fire_spell',
        'do_air' => 'fixed_damage_air_spell',
        'do_eau' => 'fixed_damage_water_spell',
        'save_vitality' => 'save_vitality_spell',
        'save_wisdom' => 'save_wisdom_spell',
        'save_strength' => 'save_strength_spell',
        'save_intelligence' => 'save_intelligence_spell',
        'save_chance' => 'save_chance_spell',
        'save_agility' => 'save_agility_spell',
        'wakfu_reserve' => 'wakfu_reserve_spell',
        'mastery_bonus' => 'mastery_bonus_spell',
        'pv_temporaires' => 'pv_temporaires_spell',
        'maxLifePoints' => 'pv_temporaires_spell',
        // Compétences actives (miroir object → spell)
        'acrobatics' => 'acrobatics_spell',
        'animal_handling' => 'animal_handling_spell',
        'arcana' => 'arcana_spell',
        'athletics' => 'athletics_spell',
        'deception' => 'deception_spell',
        'history' => 'history_spell',
        'insight' => 'insight_spell',
        'intimidation' => 'intimidation_spell',
        'investigation' => 'investigation_spell',
        'medicine' => 'medicine_spell',
        'nature' => 'nature_spell',
        'perception' => 'perception_spell',
        'performance' => 'performance_spell',
        'persuasion' => 'persuasion_spell',
        'religion' => 'religion_spell',
        'sleight_of_hand' => 'sleight_of_hand_spell',
        'stealth' => 'stealth_spell',
        'survival' => 'survival_spell',
    ];

    /**
     * Normalise la clé pour le groupe spell (alias ou suffixe _spell).
     */
    private function normalizeSpellKey(string $key): string
    {
        $key = trim($key);
        if ($key === '') {
            return $key;
        }
        if (isset(self::SPELL_KEY_ALIASES[$key])) {
            return self::SPELL_KEY_ALIASES[$key];
        }
        if (str_ends_with($key, '_spell')) {
            return $key;
        }

        return $key.'_spell';
    }
}
