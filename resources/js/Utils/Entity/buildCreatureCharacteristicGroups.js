import { getByDbColumnMap, getByComputedKeyMap } from "@/Composables/store/useCharacteristicsStore";
import {
    getDisplayValue,
    isPoCac,
    PO_CAC_ICON,
} from "@/Composables/entity/useCharacteristicDisplay";
import {
    CREATURE_ABILITY_STAT_DEFS,
    CREATURE_CHARACTERISTIC_COMBAT_COMPUTED_KEYS,
    CREATURE_CHARACTERISTIC_GROUPS,
    CREATURE_CHARACTERISTIC_SUMMARY_COMBAT_KEYS,
    CREATURE_RESISTANCE_PERCENT_FULL_LABELS,
    CREATURE_RESISTANCE_PERCENT_LABELS,
} from "@/Utils/Entity/creatureCharacteristicGroups.manifest";
import { SKILL_ABSOLUTE_LIMIT } from "@/Utils/Entity/buildCreatureCompetenceGroups";

/**
 * Construit les groupes de caractéristiques pour CharacteristicsCard à partir d'une créature.
 *
 * @param {Object|null} creature - Données de la créature (life, pa, pm, strong, etc.)
 * @param {Object} [options]
 * @param {'summary'|'full'} [options.mode='full'] - summary = mods + combat ; full = tous les groupes
 * @param {Object} [options.byDbColumn]
 * @param {Object} [options.byComputedKey]
 * @param {Object|null} [options.runtime] - resolved-stats : privilégie les totaux runtime
 * @returns {Array<{ title: string, kind?: string, characteristics: Array }>}
 *
 * @example
 * buildCreatureCharacteristicGroups(creature, { mode: 'summary', runtime })
 */
export function buildCreatureCharacteristicGroups(creature, options = {}) {
    if (!creature || typeof creature !== "object") {
        return [];
    }

    const mode = options.mode === "summary" ? "summary" : "full";
    const byDb =
        options?.byDbColumn && typeof options.byDbColumn === "object"
            ? options.byDbColumn
            : getByDbColumnMap("creature");
    const byComp =
        options?.byComputedKey && typeof options.byComputedKey === "object"
            ? options.byComputedKey
            : getByComputedKeyMap("creature");
    const runtime = options.runtime && typeof options.runtime === "object" ? options.runtime : null;

    const getDef = (dbColumn) => byDb[dbColumn] || { key: dbColumn, name: dbColumn, short_name: dbColumn };
    const getCompDef = (key) => byComp[key] || { key, name: key, short_name: key };

    const resolveDbValue = (dbColumn, def) =>
        resolveCreatureDbValue(creature, dbColumn, def || getDef(dbColumn), runtime);

    const makeFormula = (dbColumn, { forceShow = false } = {}) => {
        const def = getDef(dbColumn);
        const value = resolveDbValue(dbColumn, def);
        if (value === null || value === undefined || value === "") return null;
        const displayValue = getDisplayValue(dbColumn, value, def);
        const poCac = dbColumn === "po" && isPoCac(value);
        const resolvedDef = poCac
            ? {
                  ...def,
                  key: def.key || dbColumn,
                  icon: PO_CAC_ICON,
                  helper: "Corps à corps",
                  hide_when_empty: false,
              }
            : {
                  ...def,
                  key: def.key || dbColumn,
                  ...(forceShow ? { hide_when_empty: false } : {}),
              };
        return {
            type: "formula",
            def: resolvedDef,
            value: poCac ? "" : displayValue,
            formulaResolved: "",
            formulaRaw: "",
        };
    };

    const addFormulas = (dbColumns, opts) => dbColumns.map((col) => makeFormula(col, opts)).filter(Boolean);

    const makeComputedFormula = (computedKey, { forceShow = false } = {}) => {
        const def = getCompDef(computedKey);
        const value = resolveComputedCharacteristicValue(creature, computedKey, runtime);
        if (value === null || value === undefined || value === "") {
            if (!forceShow) return null;
        }
        const displayValue =
            value === null || value === undefined || value === ""
                ? "—"
                : getDisplayValue(computedKey, value, def);
        return {
            type: "formula",
            def: {
                ...def,
                key: def.key || computedKey,
                hide_when_empty: forceShow ? false : def.hide_when_empty,
            },
            value: String(displayValue),
            formulaResolved: "",
            formulaRaw: "",
        };
    };

    const addComputedFormulas = (keys, opts) =>
        keys.map((key) => makeComputedFormula(key, opts)).filter(Boolean);

    if (mode === "summary") {
        const groups = [];
        const mods = buildModifierItems(creature, getCompDef, runtime);
        if (mods.length > 0) {
            groups.push({ title: "", kind: "modifiers", characteristics: mods });
        }
        const combat = [
            ...addFormulas([...CREATURE_CHARACTERISTIC_SUMMARY_COMBAT_KEYS], { forceShow: true }),
            ...addComputedFormulas([...CREATURE_CHARACTERISTIC_COMBAT_COMPUTED_KEYS], { forceShow: true }),
        ];
        if (combat.length > 0) {
            groups.push({ title: "", kind: "combatSummary", characteristics: combat });
        }
        return groups;
    }

    const groups = [];

    for (const groupDef of CREATURE_CHARACTERISTIC_GROUPS) {
        let items = [];
        if (groupDef.kind === "db") {
            items = [
                ...addFormulas([...(groupDef.dbColumns || [])], { forceShow: groupDef.id === "combat" }),
                ...addComputedFormulas([...(groupDef.computedKeys || [])], {
                    forceShow: groupDef.id === "combat",
                }),
            ];
        } else if (groupDef.kind === "abilityStack") {
            items = buildAbilityStackItems(creature, getDef, getCompDef, byDb, runtime);
            if (items.length > 0) {
                groups.push({
                    title: groupDef.title,
                    kind: "abilityStack",
                    spread: Boolean(groupDef.spread),
                    characteristics: items,
                });
            }
            continue;
        } else if (groupDef.kind === "resistances") {
            items = buildResistanceItems(creature, getDef, runtime);
        } else if (groupDef.kind === "damages") {
            items = buildDamageItems(creature, makeFormula, getCompDef, runtime);
        }
        if (items.length > 0) {
            groups.push({
                title: groupDef.title,
                kind: groupDef.kind,
                spread: Boolean(groupDef.spread),
                characteristics: items,
            });
        }
    }

    return groups;
}

/**
 * Résout une valeur colonne : total explicite créature, sinon total runtime, sinon fallback formule.
 *
 * @param {Object} creature
 * @param {string} dbColumn
 * @param {Object} def
 * @param {Object|null} runtime
 * @returns {string|number|null}
 */
export function resolveCreatureDbValue(creature, dbColumn, def, runtime) {
    const raw = creature?.[dbColumn];
    if (raw !== null && raw !== undefined && raw !== "") {
        return raw;
    }

    const key = def?.key || `${dbColumn}_creature`;
    const fromRuntime = runtimeDisplayValue(runtime, key) ?? runtimeDisplayValue(runtime, `${dbColumn}_creature`);
    // Runtime peut renvoyer 0 tant que la formule CA n’est pas en BDD (avant reseed).
    if (fromRuntime != null) {
        const n = Number(fromRuntime);
        if (dbColumn === "ca" && Number.isFinite(n) && n === 0) {
            const fb = fallbackDbValue(creature, dbColumn, runtime);
            if (fb != null) return fb;
        }
        return fromRuntime;
    }

    return fallbackDbValue(creature, dbColumn, runtime);
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
 * Fallback local si runtime absent (aligné sur les formules seed).
 *
 * @param {Object} creature
 * @param {string} dbColumn
 * @param {Object|null} runtime
 * @returns {string|number|null}
 */
/**
 * Valeur d’une caractéristique calculée (clé métier, ex. wakfu_reserve_creature).
 *
 * @param {Object} creature
 * @param {string} computedKey
 * @param {Object|null} runtime
 * @returns {string|number|null}
 */
export function resolveComputedCharacteristicValue(creature, computedKey, runtime) {
    const fromRuntime = runtimeDisplayValue(runtime, computedKey);
    if (fromRuntime != null && fromRuntime !== "") {
        return fromRuntime;
    }
    if (computedKey === "wakfu_reserve_creature") {
        const level = parseInt(creature?.level, 10) || 1;
        const masteryBonus = Math.min(1 + Math.floor(level / 4), 6);
        return masteryBonus;
    }
    return null;
}

function fallbackDbValue(creature, dbColumn, runtime) {
    if (dbColumn === "ca") {
        const modVit =
            runtimeDisplayValue(runtime, "modifier_vitality_creature") ?? computeStatModifier(creature, "vitality");
        const n = Number(modVit);
        return 10 + (Number.isFinite(n) ? n : 0);
    }
    if (dbColumn === "ini") {
        const modInt =
            runtimeDisplayValue(runtime, "modifier_intelligence_creature") ?? computeStatModifier(creature, "intel");
        const n = Number(modInt);
        return Number.isFinite(n) ? n : 0;
    }
    if (dbColumn === "invocation") {
        return 1;
    }
    if (dbColumn === "po") {
        return 0;
    }
    if (dbColumn === "do_fixe_multiple") {
        return 0;
    }
    return null;
}

/**
 * @param {Object} creature
 * @param {string} stat
 * @returns {number}
 */
function computeStatModifier(creature, stat) {
    const level = parseInt(creature.level, 10) || 1;
    // Repli sans runtime : limite absolue du mod (min −2, max ⌊niv/4⌋+2 plafonné à +7).
    const modMax = Math.min(Math.floor(level / 4) + 2, 7);
    const statVal = parseInt(creature[stat], 10) || 10;
    const rawMod = Math.floor((statVal - 10) / 2);
    return Math.max(Math.min(rawMod, modMax), -2);
}

/**
 * @param {number|string|null|undefined} mod
 * @returns {string}
 */
function formatSignedModifier(mod) {
    const n = Number(mod);
    if (!Number.isFinite(n)) return String(mod ?? "");
    return n >= 0 ? `+${n}` : String(n);
}

function buildModifierItems(creature, getCompDef, runtime) {
    const modifierItems = [];
    for (const { stat, modKey } of CREATURE_ABILITY_STAT_DEFS) {
        const def = getCompDef(modKey);
        const fromRuntime = runtimeDisplayValue(runtime, modKey);
        const mod = fromRuntime != null ? fromRuntime : computeStatModifier(creature, stat);
        modifierItems.push({
            type: "formula",
            def: { ...def, key: def.key || modKey, hide_when_empty: false },
            value: formatSignedModifier(mod),
            formulaResolved: "",
            formulaRaw: "",
        });
    }
    return modifierItems;
}

/**
 * Colonnes empilées score → modificateur → sauvegarde.
 *
 * @returns {Array<{ type: 'abilityColumn', stat: string, score: Object, modifier: Object, save: Object }>}
 */
function buildAbilityStackItems(creature, getDef, getCompDef, byDb, runtime) {
    const levelForSave = parseInt(creature.level, 10) || 1;
    const masteryBonus = Math.min(1 + Math.floor(levelForSave / 4), 6);
    const items = [];

    for (const defRow of CREATURE_ABILITY_STAT_DEFS) {
        const { stat, modKey, saveKey, saveBonusColumn, saveMasteryColumn, saveLabel } = defRow;
        const scoreDef = getDef(stat);
        const scoreRaw = resolveCreatureDbValue(creature, stat, scoreDef, runtime);
        const scoreVal = scoreRaw !== null && scoreRaw !== undefined && scoreRaw !== "" ? scoreRaw : 10;

        const modDef = getCompDef(modKey);
        const fromRuntimeMod = runtimeDisplayValue(runtime, modKey);
        const mod = fromRuntimeMod != null ? fromRuntimeMod : computeStatModifier(creature, stat);

        const saveDef =
            byDb[saveBonusColumn] ||
            getCompDef(saveKey) ||
            { key: saveKey, name: `Sauv. ${saveLabel}`, short_name: saveLabel };
        const fromRuntimeSave = runtimeDisplayValue(runtime, saveKey);
        let saveDisplay;
        if (fromRuntimeSave != null) {
            saveDisplay = String(fromRuntimeSave);
        } else {
            const modN = Number(mod);
            const bonus = parseInt(creature[saveBonusColumn], 10) || 0;
            const mastery = parseInt(creature[saveMasteryColumn], 10) || 0;
            const rawSave = (Number.isFinite(modN) ? modN : 0) + masteryBonus * mastery + bonus;
            const saveTotal = Math.max(-SKILL_ABSOLUTE_LIMIT, Math.min(SKILL_ABSOLUTE_LIMIT, rawSave));
            saveDisplay = String(saveTotal);
        }

        items.push({
            type: "abilityColumn",
            stat,
            score: {
                type: "formula",
                def: { ...scoreDef, key: scoreDef.key || `${stat}_creature`, hide_when_empty: false },
                value: String(scoreVal),
                formulaResolved: "",
                formulaRaw: "",
            },
            modifier: {
                type: "formula",
                def: { ...modDef, key: modDef.key || modKey, hide_when_empty: false },
                value: formatSignedModifier(mod),
                formulaResolved: "",
                formulaRaw: "",
            },
            save: {
                type: "formula",
                def: { ...saveDef, key: saveDef.key || saveKey, short_name: saveLabel, hide_when_empty: false },
                value: saveDisplay,
                formulaResolved: "",
                formulaRaw: "",
            },
        });
    }
    return items;
}

/**
 * Libellé palier relatif ; null si 0 % / absent / inconnu.
 *
 * @param {unknown} percent
 * @returns {string|null}
 */
export function resistancePercentLabel(percent) {
    if (percent === null || percent === undefined || String(percent) === "") return null;
    const n = Number(percent);
    if (!Number.isFinite(n) || n === 0) return null;
    return CREATURE_RESISTANCE_PERCENT_LABELS[n] ?? `${n}%`;
}

function buildResistanceItems(creature, getDef, runtime) {
    const els = ["neutre", "terre", "feu", "air", "eau"];
    const resItems = [];
    for (const el of els) {
        const fixedDb = `res_fixe_${el}`;
        const percentDb = `res_${el}`;
        const fixedDef = getDef(fixedDb);
        const percentDef = getDef(percentDb);
        const fixed = resolveCreatureDbValue(creature, fixedDb, fixedDef, runtime);
        const percent = resolveCreatureDbValue(creature, percentDb, percentDef, runtime);
        const hasFixed = fixed !== null && fixed !== undefined && String(fixed) !== "";
        const label = resistancePercentLabel(percent);
        if (!hasFixed && !label) continue;

        let display = "";
        if (hasFixed && label) display = `${fixed} (${label})`;
        else if (hasFixed) display = String(fixed);
        else display = `(${label})`;

        const def = fixedDef || percentDef;
        const full =
            CREATURE_RESISTANCE_PERCENT_FULL_LABELS[
                Number(percent)
            ] ?? label;
        resItems.push({
            type: "formula",
            def: {
                ...def,
                key: def.key || percentDb,
                hide_when_empty: false,
                helper: label ? `${def.helper || def.name || ""} · ${full}`.trim() : def.helper,
            },
            value: display,
            formulaResolved: "",
            formulaRaw: "",
        });
    }
    return resItems;
}

function buildDamageItems(creature, makeFormula, getCompDef, runtime) {
    const els = ["neutre", "terre", "feu", "air", "eau"];
    const dmgItems = [];
    const touchItem = makeFormula("touch");
    if (touchItem) dmgItems.push(touchItem);
    for (const el of els) {
        const item = makeFormula(`do_fixe_${el}`);
        if (item) dmgItems.push(item);
    }

    // DO mult. : toujours listé (hide_when_empty=false) — colonne + runtime / équipements
    const multiItem =
        makeFormula("do_fixe_multiple", { forceShow: true }) ||
        buildFixedDamageMultipleFallback(creature, getCompDef, runtime);
    if (multiItem) dmgItems.push(multiItem);

    return dmgItems;
}

/**
 * Fallback si la def n’est pas encore indexée par db_column (avant reseed / cache).
 *
 * @param {Object} creature
 * @param {(key: string) => Object} getCompDef
 * @param {Object|null} runtime
 * @returns {{ type: string, def: Object, value: string, formulaResolved: string, formulaRaw: string }|null}
 */
function buildFixedDamageMultipleFallback(creature, getCompDef, runtime) {
    const multiKey = "fixed_damage_multiple_creature";
    const multiFromRuntime = runtimeDisplayValue(runtime, multiKey);
    const aggregated = runtime?.items?.aggregated;
    const fromItems =
        aggregated && typeof aggregated === "object"
            ? (aggregated.fixed_damage_multiple ?? aggregated[multiKey] ?? null)
            : null;
    const multiRaw =
        creature?.do_fixe_multiple ?? creature?.fixed_damage_multiple ?? multiFromRuntime ?? fromItems ?? 0;
    if (multiRaw === null || multiRaw === undefined || multiRaw === "") return null;
    const def = getCompDef(multiKey);
    return {
        type: "formula",
        def: {
            ...def,
            key: def.key || multiKey,
            db_column: def.db_column || "do_fixe_multiple",
            hide_when_empty: false,
        },
        value: String(multiRaw),
        formulaResolved: "",
        formulaRaw: "",
    };
}
