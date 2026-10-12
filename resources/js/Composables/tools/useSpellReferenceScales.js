/**
 * Charge et résout les barèmes de sorts (normes) pour l’outil de référence.
 *
 * @description
 * Récupère les grilles `dommages_spell`, `soin_spell`, `bouclier_spell`,
 * `pv_temporaires_spell`, `vol_vie_spell` et les PA créature, applique les
 * conditions cochées et dérive le budget par tour. Invocations = normes monstre.
 *
 * @example
 * const { families, level, resolvedRows } = useSpellReferenceScales();
 */
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { POWER_LEVELS, NEUTRAL_INDEX, MAX_LEVEL } from '@/Utils/Characteristic/normsConstants';
import { formatDiceSummary, suggestDice, typicalModifier } from '@/Utils/dice/diceEquivalents.js';

export const SPELL_SCALE_FAMILIES = [
    { key: 'dommages_spell', action: 'damage', label: 'Dégâts', short: 'Dmg' },
    { key: 'soin_spell', action: 'heal', label: 'Soins', short: 'Soin' },
    { key: 'bouclier_spell', action: 'shield', label: 'Bouclier', short: 'Bouclier' },
    { key: 'pv_temporaires_spell', action: 'temp_hp', label: 'PV temporaires', short: 'PV temp.' },
    { key: 'vol_vie_spell', action: 'life_steal', label: 'Vol de vie', short: 'Vol vie' },
];

/** Bandes affichées : basse / moyenne / haute = 3ème / 2ème / 1er rôle. */
export const DISPLAY_BANDS = [
    { power: 'weak', label: 'Basse (3ème rôle)', short: 'Bas' },
    { power: 'neutral', label: 'Moyenne (2ème rôle)', short: 'Moy' },
    { power: 'strong', label: 'Haute (1er rôle)', short: 'Haut' },
];

/** Repli PA si la norme créature n’est pas encore chargée. */
const FALLBACK_ACTION_POINTS = [
    6, 6, 6, 7, 7, 7, 8, 8, 8, 9,
    9, 9, 10, 10, 10, 11, 11, 11, 12, 12,
];

export function useSpellReferenceScales() {
    const loading = ref(false);
    const error = ref(null);
    const normsByKey = ref({});
    const actionPointsGrid = ref(null);
    const level = ref(1);
    const activeConditionIndices = ref(new Set());
    const convertValue = ref('');

    async function fetchAll() {
        loading.value = true;
        error.value = null;
        try {
            const results = await Promise.all([
                ...SPELL_SCALE_FAMILIES.map(async (family) => {
                    const { data } = await axios.get(
                        `/api/characteristics/${encodeURIComponent(family.key)}/norms/*`,
                        { params: { group: 'spell' } },
                    );
                    return ['spell', family.key, data];
                }),
                axios
                    .get('/api/characteristics/action_points_creature/norms/*', {
                        params: { group: 'creature' },
                    })
                    .then(({ data }) => ['creature', 'action_points_creature', data]),
            ]);
            const map = {};
            for (const [group, key, data] of results) {
                if (group === 'creature') {
                    actionPointsGrid.value = data?.norms?.grid || null;
                } else {
                    map[key] = data;
                }
            }
            normsByKey.value = map;
        } catch (e) {
            error.value = e?.response?.data?.error || 'Impossible de charger les barèmes.';
            normsByKey.value = {};
            actionPointsGrid.value = null;
        } finally {
            loading.value = false;
        }
    }

    onMounted(fetchAll);

    const conditions = computed(() => {
        const first = normsByKey.value.dommages_spell?.norms?.conditions;
        return Array.isArray(first) ? first : [];
    });

    function toggleCondition(index) {
        const next = new Set(activeConditionIndices.value);
        if (next.has(index)) {
            next.delete(index);
        } else {
            next.add(index);
        }
        activeConditionIndices.value = next;
    }

    function clearConditions() {
        activeConditionIndices.value = new Set();
    }

    const powerOffset = computed(() => {
        let offset = 0;
        for (const idx of activeConditionIndices.value) {
            const cond = conditions.value[idx];
            if (cond?.target === 'power') {
                offset += Number(cond.modifier) || 0;
            }
        }
        return offset;
    });

    const levelOffset = computed(() => {
        let offset = 0;
        for (const idx of activeConditionIndices.value) {
            const cond = conditions.value[idx];
            if (cond?.target === 'level') {
                offset += Number(cond.modifier) || 0;
            }
        }
        return offset;
    });

    const effectiveLevel = computed(() => {
        return Math.max(1, Math.min(MAX_LEVEL, level.value + levelOffset.value));
    });

    function bandPowerKey(basePower) {
        const baseIndex = POWER_LEVELS.indexOf(basePower);
        const idx = Math.max(0, Math.min(POWER_LEVELS.length - 1, baseIndex + powerOffset.value));
        return POWER_LEVELS[idx];
    }

    function actionPointsAt(atLevel, powerKey = 'neutral') {
        const grid = actionPointsGrid.value;
        const row = grid?.[powerKey] || grid?.neutral;
        if (Array.isArray(row) && row[atLevel - 1] != null) {
            return Number(row[atLevel - 1]);
        }
        return FALLBACK_ACTION_POINTS[atLevel - 1] ?? 6;
    }

    const MAX_ACTION_POINTS = computed(() => {
        return Array.from({ length: MAX_LEVEL }, (_, i) => actionPointsAt(i + 1, 'neutral'));
    });

    function castValue(familyKey, powerKey, atLevel) {
        const grid = normsByKey.value[familyKey]?.norms?.grid;
        if (!grid) {
            return null;
        }
        const row = grid[powerKey];
        if (!Array.isArray(row)) {
            return null;
        }
        return row[atLevel - 1] ?? null;
    }

    function turnFromCast(cast, atLevel, powerKey = 'neutral') {
        if (cast === null || cast === undefined) {
            return null;
        }
        const pa = actionPointsAt(atLevel, powerKey);
        return Math.max(1, Math.round(Number(cast) * pa / 3));
    }

    const mod = computed(() => typicalModifier(effectiveLevel.value));

    const resolvedRows = computed(() => {
        const atLevel = effectiveLevel.value;
        return SPELL_SCALE_FAMILIES.map((family) => {
            const formula = normsByKey.value[family.key]?.norms?.formula
                || normsByKey.value[family.key]?.norms_formula
                || '';
            const bands = DISPLAY_BANDS.map((band) => {
                const powerKey = bandPowerKey(band.power);
                const cast = castValue(family.key, powerKey, atLevel);
                const turn = turnFromCast(cast, atLevel, powerKey);
                return {
                    ...band,
                    powerKey,
                    cast,
                    turn,
                    diceCast: cast !== null ? formatDiceSummary(cast, { mod: mod.value }) : '—',
                    diceTurn: turn !== null ? formatDiceSummary(turn, { mod: mod.value }) : '—',
                };
            });
            return { ...family, bands, formula };
        });
    });

    const convertSuggestions = computed(() => {
        const raw = String(convertValue.value).trim().replace(',', '.');
        if (!raw) {
            return null;
        }
        const num = Number(raw);
        if (!Number.isFinite(num) || num <= 0) {
            return null;
        }
        return suggestDice(num, { mod: 0 });
    });

    const description = computed(() => {
        return normsByKey.value.dommages_spell?.norms?.description || '';
    });

    return {
        loading,
        error,
        level,
        mod,
        conditions,
        activeConditionIndices,
        toggleCondition,
        clearConditions,
        powerOffset,
        levelOffset,
        effectiveLevel,
        resolvedRows,
        convertValue,
        convertSuggestions,
        description,
        fetchAll,
        NEUTRAL_INDEX,
        MAX_LEVEL,
        MAX_ACTION_POINTS,
        DISPLAY_BANDS,
        SPELL_SCALE_FAMILIES,
        formatDiceSummary,
        typicalModifier,
        suggestDice,
        castValue,
        turnFromCast,
        normsByKey,
        actionPointsAt,
    };
}
