import { computed, ref, toValue, watch } from "vue";
import axios from "axios";

/** @type {Map<string, object>} */
const cardCache = new Map();
/** @type {Map<string, Promise<object|null>>} */
const inflight = new Map();

const CARD_GROUPS = Object.freeze([
    "creature",
    "object",
    "spell",
    "consumable",
    "profile",
]);

/**
 * Normalise un groupe UI (item/panoply/capability…) vers un groupe API card.
 *
 * @param {string|null|undefined} group
 * @returns {string|null}
 */
export function normalizeCharacteristicCardGroup(group) {
    const g = String(group || "")
        .trim()
        .toLowerCase();
    if (!g) return null;
    if (CARD_GROUPS.includes(g)) return g;
    if (g === "item" || g === "panoply" || g === "resource") return "object";
    if (g === "capability") return "spell";
    if (g === "monster" || g === "npc" || g === "class") return "creature";
    if (g === "breed" || g === "specialization") return "profile";
    return null;
}

/**
 * @param {string} key
 * @param {string|null} group
 * @param {string} entity
 * @returns {string}
 */
function cacheKey(key, group, entity) {
    return `${key}|${group || ""}|${entity || "*"}`;
}

/**
 * Charge la fiche caractéristique (API card), avec cache mémoire.
 *
 * @param {string} key
 * @param {{ group?: string|null, entity?: string|null }} [opts]
 * @returns {Promise<object|null>}
 */
export async function loadCharacteristicCard(key, opts = {}) {
    const characteristicKey = String(key || "").trim();
    if (!characteristicKey) return null;

    const group = normalizeCharacteristicCardGroup(opts.group);
    const entity = String(opts.entity || "*").trim() || "*";
    const ck = cacheKey(characteristicKey, group, entity);
    if (cardCache.has(ck)) return cardCache.get(ck);
    if (inflight.has(ck)) return inflight.get(ck);

    const promise = (async () => {
        try {
            const params = { entity };
            if (group) params.group = group;
            const url = route("api.characteristics.card", { key: characteristicKey });
            const { data } = await axios.get(url, { params });
            const card = data?.card && typeof data.card === "object" ? data.card : null;
            cardCache.set(ck, card);
            return card;
        } catch {
            cardCache.set(ck, null);
            return null;
        } finally {
            inflight.delete(ck);
        }
    })();

    inflight.set(ck, promise);
    return promise;
}

/**
 * Fusionne une fiche API avec un viewModel / runtime déjà calculé.
 *
 * @param {object|null|undefined} baseVm
 * @param {object|null|undefined} card
 * @returns {object}
 */
export function mergeCardIntoViewModel(baseVm, card) {
    const vm = baseVm && typeof baseVm === "object" ? { ...baseVm } : {};
    if (!card || typeof card !== "object") {
        return vm;
    }

    if (card.name) vm.name = card.name;
    if (card.short_name) vm.shortName = card.short_name;
    if (card.icon) vm.icon = card.icon;
    if (card.color) vm.color = card.color;
    if (card.unit != null && card.unit !== "") vm.unit = card.unit;
    if (card.helper) vm.helper = card.helper;
    if (card.descriptions) vm.descriptions = card.descriptions;
    if (card.nature) vm.nature = card.nature;
    if (card.formula_display) vm.formulaDisplay = String(card.formula_display);
    if (card.formula) vm.formulaBdd = String(card.formula);
    if (card.default_value != null) vm.defaultValue = card.default_value;
    if (card.limit_min != null) vm.limitMin = card.limit_min;
    if (card.limit_max != null) vm.limitMax = card.limit_max;
    if (card.min != null) vm.minRaw = card.min;
    if (card.max != null) vm.maxRaw = card.max;
    vm.limitAbsolute = Boolean(card.limit_absolute);
    vm.limitsDependOnLevel = Boolean(card.limits_depend_on_level);
    if (Array.isArray(card.limits_by_level)) {
        vm.limitsByLevel = card.limits_by_level;
    }
    if (card.group) vm.cardGroup = card.group;
    if (card.entity) vm.cardEntity = card.entity;
    if (card.key) vm.key = card.key;

    return vm;
}

/**
 * Composable : charge la fiche et produit un viewModel enrichi.
 *
 * @param {import('vue').MaybeRefOrGetter<{
 *   key?: string,
 *   group?: string|null,
 *   entity?: string|null,
 *   viewModel?: object|null,
 *   enabled?: boolean,
 * }>} options
 */
export function useCharacteristicCard(options) {
    const card = ref(null);
    const loading = ref(false);
    const error = ref(null);

    const key = computed(() => {
        const o = toValue(options) || {};
        return String(o.key || o.viewModel?.key || "").trim();
    });

    const group = computed(() => {
        const o = toValue(options) || {};
        return normalizeCharacteristicCardGroup(o.group || o.viewModel?.cardGroup);
    });

    const entity = computed(() => {
        const o = toValue(options) || {};
        return String(o.entity || o.viewModel?.cardEntity || "*").trim() || "*";
    });

    const enabled = computed(() => {
        const o = toValue(options) || {};
        return o.enabled !== false && Boolean(key.value);
    });

    async function refresh() {
        if (!enabled.value) {
            card.value = null;
            return;
        }
        loading.value = true;
        error.value = null;
        try {
            card.value = await loadCharacteristicCard(key.value, {
                group: group.value,
                entity: entity.value,
            });
        } catch (e) {
            error.value = e;
            card.value = null;
        } finally {
            loading.value = false;
        }
    }

    watch([key, group, entity, enabled], refresh, { immediate: true });

    const enrichedViewModel = computed(() => {
        const o = toValue(options) || {};
        return mergeCardIntoViewModel(o.viewModel || {}, card.value);
    });

    return {
        card,
        loading,
        error,
        enrichedViewModel,
        refresh,
    };
}
