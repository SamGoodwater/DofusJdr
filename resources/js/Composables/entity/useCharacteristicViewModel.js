/**
 * useCharacteristicViewModel — Vue unifiée pour afficher une caractéristique / propriété.
 *
 * @description
 * Assemble définition BDD (via useCharacteristicsStore + resolveDef), métadonnées
 * descriptor/table (via useEntityPropertyDisplay) et optionnellement un payload runtime
 * (ex. GET /entities/creatures/{id}/resolved-stats → computed[key]).
 *
 * @see docs/frontend/entity-views/README.md
 */

import { computed, toValue } from "vue";
import { useEntityPropertyDisplay } from "@/Composables/entity/useEntityPropertyDisplay";
import {
    resolveDef,
    shouldHideCharacteristicLine,
} from "@/Composables/entity/useCharacteristicDisplay";
import { getEntityFieldTooltip } from "@/Utils/Entity/entity-view-ui";

/**
 * Groupes de caractéristiques à interroger selon le type d'entité (aligné resolveDef).
 *
 * @param {string} entityType
 * @returns {string[]}
 */
export function getSourceGroupsForEntityType(entityType) {
    const t = String(entityType || "").toLowerCase();
    const map = {
        monster: ["creature"],
        creature: ["creature"],
        class: ["creature"],
        npc: ["creature"],
        spell: ["spell", "capability"],
        capability: ["spell", "capability"],
        item: ["item", "resource"],
        resource: ["resource", "item"],
        consumable: ["consumable", "resource"],
        panoply: ["panoply", "item"],
    };
    return map[t] ?? ["creature", "item", "resource", "spell", "capability"];
}

/**
 * Fusionne une entrée runtime `computed` (backend) si présente pour la clé donnée.
 *
 * @param {Record<string, object>|undefined|null} computedMap
 * @param {string} key
 * @returns {object|null}
 */
function pickRuntimeComputed(computedMap, key) {
    if (!computedMap || typeof computedMap !== "object" || !key) {
        return null;
    }
    return computedMap[key] ?? null;
}

/**
 * @param {object} options
 * @param {import('vue').Ref<object>|function|object} options
 * @returns {{
 *   viewModel: import('vue').ComputedRef<object>,
 *   property: import('vue').ComputedRef<object>,
 *   value: import('vue').ComputedRef,
 *   displayValue: import('vue').ComputedRef<string>,
 *   unit: import('vue').ComputedRef<string>,
 *   characteristic: import('vue').ComputedRef<object|null>,
 * }}
 */
export function useCharacteristicViewModel(options) {
    const ep = useEntityPropertyDisplay(options);

    const viewModel = computed(() => {
        const o = toValue(options);
        const fieldKey = String(o?.fieldKey || "");
        const entityType = String(o?.entityType || "");
        const sourceGroups = getSourceGroupsForEntityType(entityType);
        const val = ep.value.value;
        const def = resolveDef(fieldKey, val, { sourceGroups, entityType }) || {};
        const prop = ep.property.value || {};
        const char = ep.characteristic.value || {};

        const key = def.key || fieldKey;
        const runtimeRoot = o?.runtime && typeof o.runtime === "object" ? o.runtime : {};
        const computedMap = runtimeRoot.computed;
        const rc =
            pickRuntimeComputed(computedMap, key) ||
            pickRuntimeComputed(computedMap, fieldKey);

        const formulaBdd = char.formula ?? def.formula ?? "";
        const formulaDisplay = char.formula_display ?? def.formula_display ?? "";

        const substituted = rc?.substituted != null ? String(rc.substituted) : "";
        const placeholders = Array.isArray(rc?.placeholders) ? rc.placeholders : [];

        const name = def.name ?? prop.label ?? key;
        const shortName = def.short_name ?? prop.shortLabel ?? name;

        const levelTable =
            Array.isArray(o?.levelTable) && o.levelTable.length > 0
                ? o.levelTable
                : Array.isArray(ep.levelTable.value)
                  ? ep.levelTable.value
                  : [];

        const hideWhenEmpty = Boolean(def.hide_when_empty);
        const hideWhenFalse = Boolean(def.hide_when_false);
        const characteristicType = def.type != null ? String(def.type) : "";

        return {
            key,
            name,
            shortName,
            icon: def._resolvedIcon ?? def.icon ?? prop.icon ?? "",
            color: def._resolvedColor ?? def.color ?? prop.color ?? "",
            helper:
                String(def.helper || "").trim() ||
                getEntityFieldTooltip(o?.descriptors?.[fieldKey]),
            descriptions: def.descriptions ?? "",
            limitMin: char.limit_min ?? def.limit_min ?? null,
            limitMax: char.limit_max ?? def.limit_max ?? null,
            subtitle: def._resolvedSubtitle ?? "",
            unit: ep.unit.value || def.unit || "",
            displayValue: ep.displayValue.value,
            rawValue: val,
            hideWhenEmpty,
            hideWhenFalse,
            characteristicType,
            hiddenWhenEmpty: shouldHideCharacteristicLine(
                {
                    hide_when_empty: hideWhenEmpty,
                    hide_when_false: hideWhenFalse,
                    type: characteristicType,
                },
                val,
            ),
            formulaBdd: String(formulaBdd || ""),
            formulaDisplay: String(formulaDisplay || ""),
            formulaMetaResolved: String(ep.formulaResolved.value || ""),
            formulaMetaRaw: String(ep.formulaRaw.value || ""),
            runtimeFormula: rc?.formula != null ? String(rc.formula) : "",
            substituted,
            placeholders,
            levelTable,
            tooltipLine: prop.tooltip ?? def.helper ?? "",
        };
    });

    return {
        ...ep,
        viewModel,
    };
}

/**
 * Enrichit un view model (ex. item formula) avec une entrée `computed` du payload resolved-stats.
 *
 * @param {object} vm
 * @param {object|null|undefined} rc
 * @returns {object}
 */
export function mergeRuntimeIntoViewModel(vm, rc) {
    if (!vm || typeof vm !== "object") {
        return vm;
    }
    if (!rc || typeof rc !== "object") {
        return vm;
    }
    const next = { ...vm };
    if (rc.formula != null) {
        next.runtimeFormula = String(rc.formula);
    }
    if (rc.substituted != null) {
        next.substituted = String(rc.substituted);
    }
    if (Array.isArray(rc.placeholders) && rc.placeholders.length > 0) {
        next.placeholders = rc.placeholders;
    }
    if (rc.formula_display != null && String(rc.formula_display).trim() !== "") {
        next.formulaDisplay = String(rc.formula_display);
    }
    if (rc.value != null || rc.total != null) {
        const total = rc.total ?? rc.value;
        next.rawValue = total;
        next.displayValue = String(total);
        next.total = total;
    }
    if (rc.base != null) next.base = rc.base;
    if (rc.object != null) next.object = rc.object;
    if (rc.context != null) next.context = rc.context;
    if (rc.ephemeral != null) next.ephemeral = rc.ephemeral;
    if (rc.permanent != null) next.permanent = rc.permanent;
    if (rc.capped != null) next.capped = Boolean(rc.capped);
    if (rc.source != null) next.source = rc.source;
    if (rc.context_raw != null) next.contextRaw = rc.context_raw;
    if (Array.isArray(rc.levelTable)) next.levelTable = rc.levelTable;
    if (Array.isArray(rc.itemContributions)) next.itemContributions = rc.itemContributions;

    return next;
}

/**
 * Construit la table niveau → valeurs à partir du payload `levels` du resolved-stats.
 *
 * @param {object|null|undefined} runtime
 * @param {string} characteristicKey
 * @returns {Array<{level:number,value:*,total:*,base:*,context:*,object:*}>}
 */
export function levelTableFromRuntime(runtime, characteristicKey) {
    const levels = runtime?.levels;
    if (!Array.isArray(levels) || !characteristicKey) return [];
    return levels
        .map((entry) => {
            const row = entry?.characteristics?.[characteristicKey];
            if (!row) return null;
            return {
                level: entry.level,
                value: row.total,
                total: row.total,
                base: row.base,
                context: row.context,
                object: row.object,
            };
        })
        .filter(Boolean);
}

/**
 * Entrée caractéristique pour un niveau effectif donné.
 *
 * @param {object|null|undefined} runtime
 * @param {number|string|null} levelEffective
 * @param {string} characteristicKey
 * @returns {object|null}
 */
export function characteristicAtLevel(runtime, levelEffective, characteristicKey) {
    if (!runtime || !characteristicKey) return null;
    const levels = Array.isArray(runtime.levels) ? runtime.levels : [];
    if (levels.length > 0 && levelEffective != null && levelEffective !== "") {
        const match = levels.find((entry) => Number(entry.level) === Number(levelEffective));
        if (match?.characteristics?.[characteristicKey]) {
            return match.characteristics[characteristicKey];
        }
        if (levels[0]?.characteristics?.[characteristicKey]) {
            return levels[0].characteristics[characteristicKey];
        }
    }
    return runtime.computed?.[characteristicKey] ?? null;
}

/**
 * Construit un view model à partir d'un item `type: 'formula'` (CharacteristicsCard / groupes créature).
 *
 * @param {object} item
 * @returns {object}
 */
export function viewModelFromFormulaGroupItem(item) {
    const def = item?.def && typeof item.def === "object" ? item.def : {};
    const unit = item?.unit || def.unit || "";
    const v = item?.value;
    const valStr = v === null || v === undefined || v === "" ? "—" : String(v);
    const displayValue = unit ? `${valStr} ${unit}`.trim() : valStr;
    const hideWhenEmpty = Boolean(def.hide_when_empty);
    const characteristicType = def.type != null ? String(def.type) : "";
    const rawIcon = def._resolvedIcon || def.icon || "";
    const icon =
        typeof rawIcon === "string" &&
        rawIcon &&
        !rawIcon.includes("/") &&
        !rawIcon.startsWith("fa-") &&
        !rawIcon.startsWith("http")
            ? `icons/caracteristics/${rawIcon}`
            : rawIcon;
    return {
        key: def.key || "unknown",
        name: def.name || def.key || "—",
        shortName: def.short_name || def.name || def.key || "—",
        icon,
        color: def.color || "",
        helper: def.helper || "",
        descriptions: def.descriptions || "",
        subtitle: def._resolvedSubtitle || "",
        unit,
        displayValue,
        rawValue: v,
        hideWhenEmpty,
        characteristicType,
        hiddenWhenEmpty: shouldHideCharacteristicLine(
            { hide_when_empty: hideWhenEmpty, type: characteristicType },
            v,
        ),
        formulaBdd: String(def.formula || ""),
        formulaDisplay: String(def.formula_display || ""),
        formulaMetaResolved: String(item?.formulaResolved || ""),
        formulaMetaRaw: String(item?.formulaRaw || ""),
        runtimeFormula: "",
        substituted: "",
        placeholders: [],
        levelTable: Array.isArray(item?.levelTable) ? item.levelTable : [],
        tooltipLine: def.helper || def.descriptions || "",
    };
}

/**
 * View model pour les chips inline (CharacteristicChip / tableaux).
 *
 * @param {object} item
 * @returns {object}
 */
export function viewModelFromChipItem(item) {
    const i = item && typeof item === "object" ? item : {};
    const unit = i.unit || "";
    const raw = i.value;
    const v =
        raw === null || raw === undefined || raw === ""
            ? "—"
            : typeof raw === "boolean"
              ? raw
                  ? "Oui"
                  : "Non"
              : String(raw);
    const displayValue = unit && v !== "—" ? `${v} ${unit}`.trim() : v;

    const def = i.def && typeof i.def === "object" ? i.def : null;
    const descRaw = i.descriptions ?? def?.descriptions;
    const descriptionsStr = Array.isArray(descRaw)
        ? descRaw.map((p) => String(p).trim()).filter(Boolean).join(" ")
        : String(descRaw || "").trim();

    const helperRaw = i.helper ?? def?.helper;
    const helperStr =
        typeof helperRaw === "string" && helperRaw.trim() !== ""
            ? helperRaw.trim()
            : String(i.tooltip || "").trim();

    const name = String(i.name || def?.name || i.label || "").trim();
    const shortName = String(
        i.shortLabel || i.short_name || def?.short_name || i.label || "",
    ).trim();

    return {
        key: i.key || def?.key || "chip",
        name: name || shortName,
        shortName: shortName || name,
        icon: i.icon || def?.icon || "",
        color: i.color || def?.color || "",
        helper: helperStr,
        descriptions: descriptionsStr,
        subtitle: i.subtitle || "",
        unit,
        displayValue,
        rawValue: raw,
        formulaBdd: "",
        formulaDisplay: "",
        formulaMetaResolved: "",
        formulaMetaRaw: "",
        runtimeFormula: "",
        substituted: "",
        placeholders: [],
        levelTable: [],
        tooltipLine: helperStr,
    };
}

export function viewModelFromLegacyProperty(property, value) {
    const p = property && typeof property === "object" ? property : {};
    let display = "—";
    if (value !== null && value !== undefined && value !== "") {
        if (typeof value === "boolean") {
            display = value ? "Oui" : "Non";
        } else {
            display = String(value);
        }
    }
    return {
        key: "legacy",
        name: p.label || "",
        shortName: p.shortLabel || p.label || "",
        icon: p.icon || "",
        color: p.color || "",
        helper: p.tooltip || "",
        descriptions: "",
        subtitle: "",
        unit: "",
        displayValue: display,
        rawValue: value,
        formulaBdd: "",
        formulaDisplay: "",
        formulaMetaResolved: "",
        formulaMetaRaw: "",
        runtimeFormula: "",
        substituted: "",
        placeholders: [],
        levelTable: [],
        tooltipLine: p.tooltip || p.label || "",
    };
}
