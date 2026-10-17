/**
 * Manifeste canonique des groupes de caractéristiques créature.
 *
 * Une seule source d’ordre / titres pour toutes les densités d’affichage.
 *
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 */

/**
 * Stats de combat du résumé compact (sous les modificateurs).
 * @type {readonly string[]}
 */
export const CREATURE_CHARACTERISTIC_SUMMARY_COMBAT_KEYS = Object.freeze([
    "pa",
    "pm",
    "life",
    "ca",
    "po",
    "ini",
    "invocation",
]);

/**
 * Caractéristiques calculées (sans colonne SQL) affichées avec le bloc combat.
 * @type {readonly string[]}
 */
export const CREATURE_CHARACTERISTIC_COMBAT_COMPUTED_KEYS = Object.freeze([
    "wakfu_reserve_creature",
]);

/**
 * @deprecated Utiliser CREATURE_CHARACTERISTIC_SUMMARY_COMBAT_KEYS (+ mods en summary).
 * Conservé pour les tests / imports existants.
 */
export const CREATURE_CHARACTERISTIC_SUMMARY_KEYS = CREATURE_CHARACTERISTIC_SUMMARY_COMBAT_KEYS;

/**
 * Ordre des scores / mods / sauvegardes / DD (colonnes empilées).
 * @type {readonly string[]}
 */
export const CREATURE_ABILITY_STAT_ORDER = Object.freeze([
    "strong",
    "intel",
    "agi",
    "chance",
    "vitality",
    "sagesse",
]);

/**
 * @typedef {Object} CreatureAbilityStatDef
 * @property {string} stat - Colonne score (`strong`, …)
 * @property {string} modKey - Clé computed modificateur
 * @property {string} saveKey - Clé computed sauvegarde
 * @property {string} saveDcKey - Clé computed DD de sauvegarde (lanceur)
 * @property {string} saveBonusColumn - Colonne bonus sauvegarde
 * @property {string} saveMasteryColumn - Colonne maîtrise sauvegarde
 * @property {string} saveLabel - Libellé court
 */

/** @type {ReadonlyArray<CreatureAbilityStatDef>} */
export const CREATURE_ABILITY_STAT_DEFS = Object.freeze([
    {
        stat: "strong",
        modKey: "modifier_strength_creature",
        saveKey: "save_strength_creature",
        saveDcKey: "save_dc_strength_creature",
        saveBonusColumn: "save_strength_bonus",
        saveMasteryColumn: "save_strength_mastery",
        saveLabel: "For",
    },
    {
        stat: "intel",
        modKey: "modifier_intelligence_creature",
        saveKey: "save_intelligence_creature",
        saveDcKey: "save_dc_intelligence_creature",
        saveBonusColumn: "save_intelligence_bonus",
        saveMasteryColumn: "save_intelligence_mastery",
        saveLabel: "Int",
    },
    {
        stat: "agi",
        modKey: "modifier_agility_creature",
        saveKey: "save_agility_creature",
        saveDcKey: "save_dc_agility_creature",
        saveBonusColumn: "save_agility_bonus",
        saveMasteryColumn: "save_agility_mastery",
        saveLabel: "Agi",
    },
    {
        stat: "chance",
        modKey: "modifier_chance_creature",
        saveKey: "save_chance_creature",
        saveDcKey: "save_dc_chance_creature",
        saveBonusColumn: "save_chance_bonus",
        saveMasteryColumn: "save_chance_mastery",
        saveLabel: "Cha",
    },
    {
        stat: "vitality",
        modKey: "modifier_vitality_creature",
        saveKey: "save_vitality_creature",
        saveDcKey: "save_dc_vitality_creature",
        saveBonusColumn: "save_vitality_bonus",
        saveMasteryColumn: "save_vitality_mastery",
        saveLabel: "Vit",
    },
    {
        stat: "sagesse",
        modKey: "modifier_wisdom_creature",
        saveKey: "save_wisdom_creature",
        saveDcKey: "save_dc_wisdom_creature",
        saveBonusColumn: "save_wisdom_bonus",
        saveMasteryColumn: "save_wisdom_mastery",
        saveLabel: "Sag",
    },
]);

/**
 * Paliers de résistance relative (règles 5.3.1) → code court (UI dense).
 * V = Vulnérable, F = Faiblesse, R = Résistant, I = Invulnérable.
 * @type {Readonly<Record<number, string>>}
 */
export const CREATURE_RESISTANCE_PERCENT_LABELS = Object.freeze({
    [-100]: "V",
    [-50]: "F",
    50: "R",
    100: "I",
});

/** Libellés longs (tooltips / docs). */
export const CREATURE_RESISTANCE_PERCENT_FULL_LABELS = Object.freeze({
    [-100]: "Vulnérable",
    [-50]: "Faiblesse",
    50: "Résistant",
    100: "Invulnérable",
});

/**
 * @typedef {Object} CreatureCharacteristicGroupDef
 * @property {string} id
 * @property {string} title
 * @property {'db'|'abilityStack'|'resistances'|'damages'} kind
 * @property {string[]} [dbColumns]
 * @property {string[]} [computedKeys] - Clés métier sans colonne SQL (runtime / formules)
 * @property {boolean} [spread] - Répartir les items sur toute la largeur disponible
 */

/** @type {ReadonlyArray<CreatureCharacteristicGroupDef>} */
export const CREATURE_CHARACTERISTIC_GROUPS = Object.freeze([
    {
        id: "combat",
        title: "Combat",
        kind: "db",
        dbColumns: Object.freeze(["pa", "pm", "po", "life", "ini", "invocation", "ca"]),
        computedKeys: CREATURE_CHARACTERISTIC_COMBAT_COMPUTED_KEYS,
    },
    {
        id: "abilities",
        title: "Caractéristiques",
        kind: "abilityStack",
    },
    {
        id: "resistances",
        title: "Résistances",
        kind: "resistances",
        spread: true,
    },
    {
        id: "damages",
        title: "Dommages",
        kind: "damages",
        spread: true,
    },
    {
        id: "control",
        title: "Contrôle",
        kind: "db",
        spread: true,
        dbColumns: Object.freeze([
            "dodge_pa",
            "dodge_pm",
            "fuite",
            "tacle",
            "critical_hit",
            "heal_bonus",
        ]),
    },
]);

/** Densités de CharacteristicsCard (surfaces UI). */
export const CHARACTERISTIC_CARD_DENSITY = Object.freeze({
    icon: "icon",
    labeled: "labeled",
    spacious: "spacious",
});

export {
    CREATURE_COMPOSABLE_COLUMNS,
    contextColumnForComposable,
} from "@/Support/Creature/creatureComposableColumns.js";
export { buildCreatureComposableEditSections } from "@/Utils/Entity/buildCreatureComposableEditSections.js";
