/**
 * Construit un viewModel de décomposition depuis un payload resolved-stats
 * et une clé de caractéristique.
 *
 * @param {object|null|undefined} runtime
 * @param {string} characteristicKey
 * @param {object|null|undefined} [baseVm]
 * @returns {object|null}
 */
import {
    mergeRuntimeIntoViewModel,
    levelTableFromRuntime,
} from "@/Composables/entity/useCharacteristicViewModel";

/**
 * @param {object|null|undefined} runtime
 * @param {string} characteristicKey
 * @returns {object|null}
 */
export function pickRuntimeComputedEntry(runtime, characteristicKey) {
    const key = String(characteristicKey || "").trim();
    if (!runtime || !key) return null;
    const computedMap = runtime.computed;
    if (computedMap && typeof computedMap === "object" && computedMap[key]) {
        return computedMap[key];
    }
    const levels = Array.isArray(runtime.levels) ? runtime.levels : [];
    for (const entry of levels) {
        const row = entry?.characteristics?.[key];
        if (row) return row;
    }
    return null;
}

/**
 * @param {object|null|undefined} runtime
 * @param {string} characteristicKey
 * @param {object|null|undefined} [baseVm]
 * @returns {object|null}
 */
export function resolveRuntimeModelForKey(runtime, characteristicKey, baseVm = null) {
    const key = String(characteristicKey || "").trim();
    if (!key) return null;
    const rc = pickRuntimeComputedEntry(runtime, key);
    if (!rc) return null;

    const base =
        baseVm && typeof baseVm === "object"
            ? { ...baseVm, key }
            : {
                  key,
                  name: rc.name || key,
                  displayValue: String(rc.total ?? rc.value ?? "—"),
              };

    const merged = mergeRuntimeIntoViewModel(base, rc);
    const levelTable = levelTableFromRuntime(runtime, key);
    if (levelTable.length) {
        merged.levelTable = levelTable;
    }
    return merged;
}

/**
 * Déduit id créature + entity query depuis les props Inertia de la page courante.
 *
 * @param {object|null|undefined} pageProps
 * @returns {{ creatureId: number|string, entityQuery: string, runtime: object|null }|null}
 */
export function resolveCreatureContextFromPageProps(pageProps) {
    const p = pageProps && typeof pageProps === "object" ? pageProps : {};

    if (p.characteristicRuntime && typeof p.characteristicRuntime === "object") {
        const fromRuntime = p.characteristicRuntime;
        const monster = p.monster;
        const npc = p.npc;
        if (monster) {
            const id = monster.creature_id || monster.creature?.id;
            if (id) {
                return { creatureId: id, entityQuery: "monster", runtime: fromRuntime };
            }
        }
        if (npc) {
            const id = npc.creature_id || npc.creature?.id;
            if (id) {
                return { creatureId: id, entityQuery: "npc", runtime: fromRuntime };
            }
        }
        return { creatureId: null, entityQuery: "monster", runtime: fromRuntime };
    }

    if (p.monster) {
        const id = p.monster.creature_id || p.monster.creature?.id;
        if (id) {
            return { creatureId: id, entityQuery: "monster", runtime: null };
        }
    }
    if (p.npc) {
        const id = p.npc.creature_id || p.npc.creature?.id;
        if (id) {
            return { creatureId: id, entityQuery: "npc", runtime: null };
        }
    }

    return null;
}
