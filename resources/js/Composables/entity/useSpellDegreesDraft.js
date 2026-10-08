/**
 * Brouillon local des degrés d’un sort : dirty, résolution d’affichage, flush bulk.
 *
 * @example
 * const draft = useSpellDegreesDraft({ spellId, spellDegrees, spellFallback });
 * await draft.flushAll();
 */
import { computed, ref, toRaw, watch } from 'vue';
import axios from 'axios';

/** @type {ReadonlyArray<'own'|'previous'|'spell'>} */
export const SPELL_PROPERTIES_SOURCES = Object.freeze(['own', 'previous', 'spell']);

/** @type {ReadonlyArray<string>} */
export const SPELL_DEGREE_PROPERTY_KEYS = Object.freeze([
    'pa',
    'po_min',
    'po_max',
    'po_editable',
    'sight_line',
    'cast_in_line',
    'cast_in_diagonal',
    'area',
    'cast_per_turn',
    'cast_per_target',
    'number_between_two_cast',
    'global_cooldown',
    'max_stack',
    'duration',
    'casting_time',
]);

/**
 * Clone JSON sûr pour proxies Vue / Inertia.
 *
 * @param {unknown} value
 * @returns {any}
 */
export function clonePlain(value) {
    return JSON.parse(JSON.stringify(toRaw(value ?? null)));
}

/**
 * @param {Record<string, unknown>|null|undefined} spell
 * @returns {Record<string, unknown>}
 */
export function spellFallbackProperties(spell) {
    const out = {};
    for (const key of SPELL_DEGREE_PROPERTY_KEYS) {
        if (key === 'area') {
            const raw = spell?.area;
            out[key] = typeof raw === 'string' && raw.trim() !== '' ? raw : null;
            continue;
        }
        out[key] = spell?.[key] ?? null;
    }
    return out;
}

/**
 * Miroir front de SpellDegreeResolver::resolveProperties.
 *
 * @param {object} deg
 * @param {object[]} degrees
 * @param {Record<string, unknown>} spellProps
 * @param {number} [depth]
 * @returns {Record<string, unknown>}
 */
export function resolveDegreeProperties(deg, degrees, spellProps, depth = 0) {
    if (!deg || depth > 20) {
        return withSources(spellProps, 'spell');
    }
    const source = SPELL_PROPERTIES_SOURCES.includes(deg.properties_source)
        ? deg.properties_source
        : 'own';

    if (source === 'spell') {
        return withSources(spellProps, 'spell');
    }

    if (source === 'previous') {
        const idx = degrees.findIndex((d) => Number(d.id) === Number(deg.id));
        const previous = idx > 0 ? degrees[idx - 1] : null;
        if (!previous) {
            return withSources(spellProps, 'spell');
        }
        const resolved = resolveDegreeProperties(previous, degrees, spellProps, depth + 1);
        return retagSources(resolved, 'previous');
    }

    const own = deg.properties || {};
    const out = {};
    for (const key of SPELL_DEGREE_PROPERTY_KEYS) {
        const ownVal = own[key];
        const emptyArea = key === 'area' && (ownVal == null || ownVal === '');
        if (ownVal != null && !emptyArea) {
            out[key] = ownVal;
            out[`${key}_source`] = 'degree';
        } else {
            out[key] = spellProps[key] ?? null;
            out[`${key}_source`] = out[key] != null ? 'spell' : 'none';
        }
    }
    return out;
}

/**
 * @param {Record<string, unknown>} props
 * @param {string} tag
 * @returns {Record<string, unknown>}
 */
function withSources(props, tag) {
    const out = {};
    for (const key of SPELL_DEGREE_PROPERTY_KEYS) {
        out[key] = props?.[key] ?? null;
        out[`${key}_source`] = out[key] != null ? tag : 'none';
    }
    return out;
}

/**
 * @param {Record<string, unknown>} properties
 * @param {string} tag
 * @returns {Record<string, unknown>}
 */
function retagSources(properties, tag) {
    const out = { ...properties };
    for (const key of SPELL_DEGREE_PROPERTY_KEYS) {
        const srcKey = `${key}_source`;
        if (out[srcKey] !== 'none') {
            out[srcKey] = tag;
        }
    }
    return out;
}

/**
 * Entier nullable pour le round-trip bulk (`value_min`, dés…).
 * Chaîne vide / NaN → null (pas 0), pour ne pas inventer une magnitude.
 *
 * @param {unknown} value
 * @returns {number|null}
 */
export function nullableInt(value) {
    if (value === null || value === undefined || value === '') {
        return null;
    }
    const n = Number(value);
    return Number.isFinite(n) ? n : null;
}

/**
 * @param {unknown} value
 * @returns {string|null}
 */
function nullableLogicGroup(value) {
    if (typeof value === 'string') {
        const trimmed = value.trim();
        return trimmed === '' ? null : trimmed;
    }
    return value == null ? null : String(value);
}

/**
 * @param {object} row
 * @param {number} index
 * @returns {object}
 */
export function serializeEffectRowForApi(row, index) {
    return {
        sub_effect_id: Number(row.sub_effect_id),
        order: index,
        scope: row.scope || 'general',
        value_min: nullableInt(row.value_min),
        value_max: nullableInt(row.value_max),
        dice_num: nullableInt(row.dice_num),
        dice_side: nullableInt(row.dice_side),
        duration_formula: row.duration_formula || null,
        logic_group: nullableLogicGroup(row.logic_group),
        logic_operator: index > 0 ? row.logic_operator || 'AND' : null,
        logic_condition: row.logic_condition || null,
        crit_only: Boolean(row.crit_only),
        params: row.params || {},
    };
}

/**
 * @param {object} options
 * @param {import('vue').MaybeRefOrGetter<number>} options.spellId
 * @param {import('vue').MaybeRefOrGetter<object>} options.spellDegrees
 * @param {import('vue').MaybeRefOrGetter<object|null|undefined>} [options.spell]
 */
export function useSpellDegreesDraft(options) {
    const degreesPayload = ref({ degrees: [], default_degree_id: null });
    const activeIndex = ref(0);
    const saving = ref(false);
    const errorMessage = ref('');
    const dirty = ref(false);
    const dirtyDegreeIds = ref(/** @type {Set<number>} */ (new Set()));

    const spellId = computed(() => Number(unrefMaybe(options.spellId)));
    const spellProps = computed(() => spellFallbackProperties(unrefMaybe(options.spell)));

    watch(
        () => unrefMaybe(options.spellDegrees),
        (v) => {
            degreesPayload.value = {
                degrees: Array.isArray(v?.degrees) ? clonePlain(v.degrees) : [],
                default_degree_id: v?.default_degree_id ?? null,
            };
            if (activeIndex.value >= degreesPayload.value.degrees.length) {
                activeIndex.value = 0;
            }
            dirty.value = false;
            dirtyDegreeIds.value = new Set();
            errorMessage.value = '';
        },
        { immediate: true, deep: true },
    );

    const degrees = computed(() => degreesPayload.value.degrees || []);
    const active = computed(() => degrees.value[activeIndex.value] || null);
    const hasDegrees = computed(() => degrees.value.length > 0);

    function markDirty(degreeId = active.value?.id) {
        dirty.value = true;
        if (degreeId != null) {
            const next = new Set(dirtyDegreeIds.value);
            next.add(Number(degreeId));
            dirtyDegreeIds.value = next;
        }
    }

    function selectDegree(index) {
        if (index === activeIndex.value) return;
        if (index < 0 || index >= degrees.value.length) return;
        activeIndex.value = index;
    }

    function ensureProperties(deg) {
        if (!deg.properties) deg.properties = {};
        if (!SPELL_PROPERTIES_SOURCES.includes(deg.properties_source)) {
            deg.properties_source = 'own';
        }
        return deg.properties;
    }

    function resolvedFor(deg) {
        return resolveDegreeProperties(deg, degrees.value, spellProps.value);
    }

    function resolvedActive() {
        return active.value ? resolvedFor(active.value) : withSources(spellProps.value, 'spell');
    }

    function isDegreeDirty(id) {
        return dirtyDegreeIds.value.has(Number(id));
    }

    /**
     * @param {object} deg
     * @returns {object}
     */
    function buildDegreeApiPayload(deg) {
        const props = ensureProperties(deg);
        const body = {
            id: Number(deg.id),
            required_level: deg.required_level,
            inherits_effects: Boolean(deg.inherits_effects),
            properties_source: deg.properties_source || 'own',
        };
        // Uniquement les clés de lancement du degré (évite element/target_type/ritual résiduels).
        for (const key of SPELL_DEGREE_PROPERTY_KEYS) {
            if (Object.prototype.hasOwnProperty.call(props, key)) {
                body[key] = props[key];
            }
        }
        if (!deg.inherits_effects) {
            body.effects = (deg.rows || []).map((row, i) => serializeEffectRowForApi(row, i));
        }
        return body;
    }

    async function reloadDegrees() {
        const { data } = await axios.get(`/api/spells/${spellId.value}/degrees`);
        degreesPayload.value = data?.data || { degrees: [], default_degree_id: null };
        dirty.value = false;
        dirtyDegreeIds.value = new Set();
        return degreesPayload.value;
    }

    async function addDegree() {
        saving.value = true;
        errorMessage.value = '';
        try {
            const prev = degrees.value[degrees.value.length - 1];
            const nextLevel = prev?.required_level != null ? Number(prev.required_level) + 1 : 1;
            const { data } = await axios.post(`/api/spells/${spellId.value}/degrees`, {
                required_level: nextLevel,
            });
            degreesPayload.value = data?.data || degreesPayload.value;
            activeIndex.value = Math.max(0, (degreesPayload.value.degrees?.length || 1) - 1);
            return degreesPayload.value;
        } catch (err) {
            errorMessage.value = err.response?.data?.message || 'Impossible d’ajouter le degré.';
            throw err;
        } finally {
            saving.value = false;
        }
    }

    async function deleteActiveDegree() {
        const deg = active.value;
        if (!deg?.id) return degreesPayload.value;
        saving.value = true;
        errorMessage.value = '';
        try {
            const { data } = await axios.delete(`/api/spells/${spellId.value}/degrees/${deg.id}`);
            degreesPayload.value = data?.data || { degrees: [] };
            activeIndex.value = 0;
            dirty.value = false;
            dirtyDegreeIds.value = new Set();
            return degreesPayload.value;
        } catch (err) {
            errorMessage.value = err.response?.data?.message || 'Suppression impossible.';
            throw err;
        } finally {
            saving.value = false;
        }
    }

    async function materializeEffects() {
        const deg = active.value;
        if (!deg?.id) return;
        saving.value = true;
        errorMessage.value = '';
        try {
            const { data } = await axios.post(
                `/api/spells/${spellId.value}/degrees/${deg.id}/materialize-effects`,
            );
            degreesPayload.value = data?.data || degreesPayload.value;
            markDirty(deg.id);
        } catch (err) {
            errorMessage.value = err.response?.data?.message || 'Personnalisation impossible.';
            throw err;
        } finally {
            saving.value = false;
        }
    }

    /**
     * Enregistre tous les degrés dirty (ou tous si forceAll).
     *
     * @param {{ forceAll?: boolean }} [opts]
     * @returns {Promise<boolean>}
     */
    async function flushAll(opts = {}) {
        if (!dirty.value && !opts.forceAll) return true;
        const list = degrees.value.filter(
            (d) => opts.forceAll || dirtyDegreeIds.value.has(Number(d.id)),
        );
        if (!list.length) {
            dirty.value = false;
            return true;
        }
        saving.value = true;
        errorMessage.value = '';
        try {
            const { data } = await axios.put(`/api/spells/${spellId.value}/degrees`, {
                degrees: list.map((d) => buildDegreeApiPayload(d)),
            });
            degreesPayload.value = data?.data || degreesPayload.value;
            dirty.value = false;
            dirtyDegreeIds.value = new Set();
            return true;
        } catch (err) {
            errorMessage.value =
                Object.values(err.response?.data?.errors || {}).flat()[0] ||
                err.response?.data?.message ||
                'Enregistrement des degrés impossible.';
            return false;
        } finally {
            saving.value = false;
        }
    }

    return {
        degreesPayload,
        degrees,
        active,
        activeIndex,
        hasDegrees,
        saving,
        errorMessage,
        dirty,
        dirtyDegreeIds,
        spellProps,
        markDirty,
        selectDegree,
        ensureProperties,
        resolvedFor,
        resolvedActive,
        isDegreeDirty,
        reloadDegrees,
        addDegree,
        deleteActiveDegree,
        materializeEffects,
        flushAll,
        buildDegreeApiPayload,
    };
}

/**
 * @param {import('vue').MaybeRefOrGetter<any>} value
 * @returns {any}
 */
function unrefMaybe(value) {
    if (typeof value === 'function') {
        try {
            return value();
        } catch {
            return value;
        }
    }
    return value?.value !== undefined ? value.value : value;
}
