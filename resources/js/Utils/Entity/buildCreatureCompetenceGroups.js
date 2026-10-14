import { getByDbColumnMap, getByComputedKeyMap } from "@/Composables/store/useCharacteristicsStore";

/**
 * Colonnes `*_mastery` (0–2) dans l’ordre logique d’affichage.
 * @type {readonly string[]}
 */
export const CREATURE_MASTERY_DB_COLUMNS = Object.freeze([
    "acrobatie_mastery",
    "discretion_mastery",
    "escamotage_mastery",
    "athletisme_mastery",
    "intimidation_mastery",
    "dressage_mastery",
    "medecine_mastery",
    "nature_mastery",
    "perception_mastery",
    "perspicacite_mastery",
    "survie_mastery",
    "arcane_mastery",
    "histoire_mastery",
    "investigation_mastery",
    "religion_mastery",
    "supercherie_mastery",
    "representation_mastery",
    "persuasion_mastery",
]);

/**
 * Caractéristique primaire (clé stats créature) associée à chaque maîtrise de compétence.
 * @type {Readonly<Record<string, 'strong'|'agi'|'intel'|'sagesse'|'chance'>>}
 */
export const MASTERY_DB_COLUMN_TO_PRIMARY_STAT = Object.freeze({
    acrobatie_mastery: "agi",
    discretion_mastery: "agi",
    escamotage_mastery: "agi",
    athletisme_mastery: "strong",
    intimidation_mastery: "strong",
    dressage_mastery: "sagesse",
    medecine_mastery: "sagesse",
    perception_mastery: "sagesse",
    perspicacite_mastery: "sagesse",
    survie_mastery: "sagesse",
    arcane_mastery: "intel",
    histoire_mastery: "intel",
    investigation_mastery: "intel",
    nature_mastery: "intel",
    religion_mastery: "intel",
    supercherie_mastery: "chance",
    representation_mastery: "chance",
    persuasion_mastery: "chance",
});

/**
 * Colonne maîtrise → clé caractéristique totale (formule runtime).
 * @type {Readonly<Record<string, string>>}
 */
export const MASTERY_DB_COLUMN_TO_SKILL_KEY = Object.freeze({
    acrobatie_mastery: "acrobatics_creature",
    discretion_mastery: "stealth_creature",
    escamotage_mastery: "sleight_of_hand_creature",
    athletisme_mastery: "athletics_creature",
    intimidation_mastery: "intimidation_creature",
    dressage_mastery: "animal_handling_creature",
    medecine_mastery: "medicine_creature",
    nature_mastery: "nature_creature",
    perception_mastery: "perception_creature",
    perspicacite_mastery: "insight_creature",
    survie_mastery: "survival_creature",
    arcane_mastery: "arcana_creature",
    histoire_mastery: "history_creature",
    investigation_mastery: "investigation_creature",
    religion_mastery: "religion_creature",
    supercherie_mastery: "deception_creature",
    representation_mastery: "performance_creature",
    persuasion_mastery: "persuasion_creature",
});

/**
 * Compétences à caractéristique variable (choix fiche créature).
 * @type {Readonly<Record<string, { defaultStat: string, statChoiceColumn?: string, alternateStats?: string[] }>>}
 */
export const MASTERY_ALTERNATE_STAT_CONFIG = Object.freeze({
    intimidation_mastery: {
        defaultStat: "strong",
        statChoiceColumn: "intimidation_ability",
        alternateStats: ["chance"],
    },
});

/** Ordre des sous-groupes « par stat » (titres FR). */
const PRIMARY_STAT_ORDER = ["strong", "agi", "intel", "sagesse", "chance"];

const PRIMARY_STAT_TITLE = {
    strong: "Force",
    agi: "Agilité",
    intel: "Intelligence",
    sagesse: "Sagesse",
    chance: "Chance",
};

/** Noms FR de secours si la définition store est absente. */
const MASTERY_DB_COLUMN_FALLBACK_NAME = Object.freeze({
    acrobatie_mastery: "Acrobaties",
    discretion_mastery: "Discrétion",
    escamotage_mastery: "Escamotage",
    athletisme_mastery: "Athlétisme",
    intimidation_mastery: "Intimidation",
    dressage_mastery: "Dressage",
    medecine_mastery: "Médecine",
    nature_mastery: "Nature",
    perception_mastery: "Perception",
    perspicacite_mastery: "Perspicacité",
    survie_mastery: "Survie",
    arcane_mastery: "Arcanes",
    histoire_mastery: "Histoire",
    investigation_mastery: "Investigation",
    religion_mastery: "Religion",
    supercherie_mastery: "Supercherie",
    representation_mastery: "Représentation",
    persuasion_mastery: "Persuasion",
});

/**
 * @param {Object} creature
 * @param {string} masteryColumn
 * @returns {'strong'|'agi'|'intel'|'sagesse'|'chance'|undefined}
 */
export function resolvePrimaryStatForMastery(creature, masteryColumn) {
    const alt = MASTERY_ALTERNATE_STAT_CONFIG[masteryColumn];
    if (alt?.statChoiceColumn) {
        const raw = creature?.[alt.statChoiceColumn];
        if (raw === "chance" || raw === "strong") {
            return raw === "chance" ? "chance" : "strong";
        }
    }
    return MASTERY_DB_COLUMN_TO_PRIMARY_STAT[masteryColumn];
}

/**
 * @param {string} dbColumn
 * @param {Object} def
 * @returns {string}
 */
function skillDisplayName(dbColumn, def) {
    const fallback = MASTERY_DB_COLUMN_FALLBACK_NAME[dbColumn] || dbColumn;
    const candidate = String(def?.name || def?.short_name || "").trim();
    const raw =
        !candidate ||
        candidate === dbColumn ||
        candidate.endsWith("_mastery") ||
        candidate.endsWith("_creature")
            ? fallback
            : candidate;
    return String(raw)
        .replace(/\s*\(palier\s+ma[îi]trise\)/gi, "")
        .replace(/\s*pal\.\s*$/i, "")
        .trim();
}

/**
 * @param {Object|null} runtime
 * @param {string} key
 * @returns {string|number|null}
 */
function runtimeDisplayValue(runtime, key) {
    if (!runtime || !key) return null;
    const levels = runtime.levels;
    if (Array.isArray(levels) && levels.length > 0) {
        const row = levels[0]?.characteristics?.[key];
        if (row && row.total != null && row.total !== "") return row.total;
        if (row && row.value != null && row.value !== "") return row.value;
    }
    const computed = runtime.computed;
    if (computed && typeof computed === "object") {
        const row = computed[key];
        if (row && row.total != null && row.total !== "") return row.total;
        if (row && row.value != null && row.value !== "") return row.value;
        if (typeof row === "number" || typeof row === "string") return row;
    }
    return null;
}

/**
 * Total compétence résolu exclusivement par le runtime serveur.
 *
 * @param {Object} creature
 * @param {string} masteryColumn
 * @param {Object|null} [runtime]
 * @returns {{ total: number|null, tier: number, tag: ''|'M'|'E' }}
 */
export function resolveCreatureSkillTotal(creature, masteryColumn, runtime = null) {
    const skillKey = MASTERY_DB_COLUMN_TO_SKILL_KEY[masteryColumn];
    const tierRaw = creature?.[masteryColumn];
    const tier = tierRaw === null || tierRaw === undefined || tierRaw === ""
        ? 0
        : Number.parseInt(String(tierRaw), 10);
    const safeTier = Number.isFinite(tier) ? Math.max(0, Math.min(2, tier)) : 0;
    const tag = safeTier === 2 ? "E" : safeTier === 1 ? "M" : "";

    if (skillKey) {
        const fromRuntime = runtimeDisplayValue(runtime, skillKey);
        if (fromRuntime != null) {
            const n = Number(fromRuntime);
            if (Number.isFinite(n)) {
                return { total: n, tier: safeTier, tag };
            }
        }
    }

    return { total: null, tier: safeTier, tag };
}

/**
 * @param {number|null} total
 * @param {''|'M'|'E'} tag
 * @returns {string}
 */
export function formatCreatureSkillDisplay(total, tag) {
    if (total === null || total === undefined || total === "") {
        return tag === "M" || tag === "E" ? `— (${tag})` : "—";
    }
    const n = Number(total);
    const signed = Number.isFinite(n) ? (n >= 0 ? `+${n}` : String(n)) : String(total ?? "");
    if (tag === "M" || tag === "E") {
        return `${signed} (${tag})`;
    }
    return signed;
}

/**
 * Construit des groupes pour CharacteristicsCard : une section par caractéristique primaire
 * contenant les compétences (totaux calculés) rattachées à cette stat.
 *
 * Formule (seed) : mod + mastery_bonus × palier(0|1|2) + bonus BDD (+ objets via runtime).
 *
 * @param {Object|null} creature
 * @param {Object} [options]
 * @param {Record<string, Object>} [options.byDbColumn]
 * @param {Record<string, Object>} [options.byComputedKey]
 * @param {Object|null} [options.runtime]
 * @param {boolean} [options.includeZero=false] - Inclure les compétences non maîtrisées
 * @returns {Array<{ title: string, characteristics: Array }>}
 *
 * @example
 * buildCreatureCompetenceGroupsByPrimary(creature, { runtime, includeZero: true })
 */
export function buildCreatureCompetenceGroupsByPrimary(creature, options = {}) {
    if (!creature || typeof creature !== "object") {
        return [];
    }

    const byDb =
        options?.byDbColumn && typeof options.byDbColumn === "object"
            ? options.byDbColumn
            : getByDbColumnMap("creature");
    const byComp =
        options?.byComputedKey && typeof options.byComputedKey === "object"
            ? options.byComputedKey
            : getByComputedKeyMap("creature");
    const runtime = options.runtime && typeof options.runtime === "object" ? options.runtime : null;
    const includeZero = options.includeZero === true;

    const getMasteryDef = (dbColumn) =>
        byDb[dbColumn] || { key: dbColumn, name: dbColumn, short_name: dbColumn };
    const getSkillDef = (skillKey, masteryColumn) =>
        byComp[skillKey] ||
        getMasteryDef(masteryColumn) ||
        { key: skillKey, name: skillKey, short_name: skillKey };

    /** @type {Record<string, string[]>} */
    const byPrimary = {};
    for (const stat of PRIMARY_STAT_ORDER) {
        byPrimary[stat] = [];
    }

    for (const dbColumn of CREATURE_MASTERY_DB_COLUMNS) {
        const primary = resolvePrimaryStatForMastery(creature, dbColumn);
        if (!primary || !byPrimary[primary]) continue;
        const raw = creature[dbColumn];
        const n = raw === null || raw === undefined || raw === "" ? 0 : Number.parseInt(String(raw), 10);
        if (!Number.isFinite(n)) continue;
        if (!includeZero && n <= 0) continue;
        byPrimary[primary].push(dbColumn);
    }

    const groups = [];
    for (const stat of PRIMARY_STAT_ORDER) {
        const columns = byPrimary[stat];
        if (!columns.length) continue;

        const characteristics = [];
        for (const dbColumn of columns) {
            const skillKey = MASTERY_DB_COLUMN_TO_SKILL_KEY[dbColumn];
            const skillDef = getSkillDef(skillKey, dbColumn);
            const masteryDef = getMasteryDef(dbColumn);
            const skillName = skillDisplayName(dbColumn, {
                name: skillDef.name || masteryDef.name,
                short_name: skillDef.short_name || masteryDef.short_name,
            });
            const { total, tag } = resolveCreatureSkillTotal(creature, dbColumn, runtime);
            const signed = formatCreatureSkillDisplay(total, tag);

            characteristics.push({
                type: "formula",
                /** Empêche le runtime d’écraser le libellé « Nom +N (M|E) ». */
                lockSkillDisplay: true,
                skillName,
                skillTag: tag,
                def: {
                    ...skillDef,
                    key: skillKey || masteryDef.key || dbColumn,
                    name: skillName,
                    short_name: skillName,
                    icon: skillDef.icon || masteryDef.icon,
                    color: skillDef.color || masteryDef.color,
                    hide_when_empty: false,
                },
                value: `${skillName} ${signed}`,
                formulaResolved: "",
                formulaRaw: "",
            });
        }
        groups.push({
            title: PRIMARY_STAT_TITLE[stat] ?? stat,
            characteristics,
        });
    }

    return groups;
}
