<script setup>
/**
 * Panneau d'édition des normes (chartes) pour une caractéristique.
 * Grille 5 puissances × 20 niveaux + formule de génération + coefficients de palier + conditions.
 */
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';
import {
    POWER_LEVELS,
    POWER_LABELS,
    POWER_COLORS,
    MAX_LEVEL,
    CONDITION_OPERATORS,
    CONDITION_TARGETS,
    DEFAULT_POWER_COEFFICIENTS,
    POWER_COEFFICIENT_KEYS,
} from '@/Utils/Characteristic/normsConstants';

const props = defineProps({
    modelValue: { type: Object, required: true },
    characteristicsList: { type: Array, default: () => [] },
    characteristicKey: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const grid = computed({
    get: () => props.modelValue.norms_grid || emptyGrid(),
    set: (v) => emit('update:modelValue', { ...props.modelValue, norms_grid: v }),
});

const localFormula = ref('');
watch(
    () => props.modelValue.norms_formula,
    (val) => { localFormula.value = val || ''; },
    { immediate: true }
);
const hasFormula = computed(() => (localFormula.value || '').trim() !== '');
const formulaUsesPower = computed(() => /\[power\]/i.test(localFormula.value || ''));
const previewError = ref('');
const previewLoading = ref(false);
let previewTimer = null;

/**
 * Coefficients locaux (null = mode [power] / manuel sans coefs).
 * @type {import('vue').Ref<Record<string, number>|null>}
 */
const localCoefficients = ref(null);
watch(
    () => props.modelValue.norms_power_coefficients,
    (val) => {
        if (!val || typeof val !== 'object') {
            localCoefficients.value = null;
            return;
        }
        const next = {};
        POWER_COEFFICIENT_KEYS.forEach((k) => {
            next[k] = Number(val[k]);
        });
        if (POWER_COEFFICIENT_KEYS.every((k) => Number.isFinite(next[k]) && next[k] > 0)) {
            localCoefficients.value = next;
        } else {
            localCoefficients.value = null;
        }
    },
    { immediate: true, deep: true }
);

const hasCoefficients = computed(() => localCoefficients.value !== null);
const coefficientsDisabled = computed(() => formulaUsesPower.value);

function emitFormula() {
    emit('update:modelValue', {
        ...props.modelValue,
        norms_formula: localFormula.value.trim() === '' ? null : localFormula.value,
        norms_power_coefficients: localCoefficients.value,
    });
    schedulePreview();
}

function emitCoefficients() {
    emit('update:modelValue', {
        ...props.modelValue,
        norms_formula: localFormula.value.trim() === '' ? null : localFormula.value,
        norms_power_coefficients: localCoefficients.value,
    });
    schedulePreview();
}

function clearFormula() {
    if (!confirm('Repasser en grille manuelle (supprimer la formule) ?')) return;
    localFormula.value = '';
    emitFormula();
}

function applyDefaultCoefficients() {
    if (coefficientsDisabled.value) return;
    localCoefficients.value = { ...DEFAULT_POWER_COEFFICIENTS };
    emitCoefficients();
}

function clearCoefficients() {
    localCoefficients.value = null;
    emitCoefficients();
}

/**
 * @param {string} key
 * @param {string|number} raw
 */
function updateCoefficient(key, raw) {
    if (coefficientsDisabled.value) return;
    const n = Number(raw);
    if (!localCoefficients.value) {
        localCoefficients.value = { ...DEFAULT_POWER_COEFFICIENTS };
    }
    localCoefficients.value = {
        ...localCoefficients.value,
        [key]: Number.isFinite(n) ? n : localCoefficients.value[key],
    };
    emitCoefficients();
}

function schedulePreview() {
    if (previewTimer) clearTimeout(previewTimer);
    previewTimer = setTimeout(() => {
        previewFormula();
    }, 400);
}

async function previewFormula() {
    previewError.value = '';
    if (!hasFormula.value || !props.characteristicKey) {
        return;
    }
    if (hasCoefficients.value && formulaUsesPower.value) {
        previewError.value = 'Les coefficients sont incompatibles avec [power] dans la formule.';
        return;
    }
    previewLoading.value = true;
    try {
        const payload = {
            norms_formula: localFormula.value,
            entity: props.modelValue.entity || '*',
        };
        if (hasCoefficients.value && !formulaUsesPower.value) {
            payload.norms_power_coefficients = localCoefficients.value;
        }
        const { data } = await axios.post(
            route('admin.characteristics.norms-preview', props.characteristicKey),
            payload
        );
        if (data?.grid) {
            grid.value = data.grid;
        }
    } catch (e) {
        previewError.value = e?.response?.data?.message || e?.message || 'Aperçu impossible';
    } finally {
        previewLoading.value = false;
    }
}

/**
 * Copie locale des conditions, synchronisée avec les props.
 */
const localConditions = ref([]);
watch(
    () => props.modelValue.norms_conditions,
    (val) => {
        const incoming = val || [];
        if (JSON.stringify(incoming) !== JSON.stringify(localConditions.value)) {
            localConditions.value = incoming.map((c) => ({ ...c }));
        }
    },
    { immediate: true, deep: true }
);

function emitConditions() {
    emit('update:modelValue', {
        ...props.modelValue,
        norms_conditions: localConditions.value.map((c) => ({ ...c })),
    });
}

const localDescription = ref('');
watch(
    () => props.modelValue.norms_description,
    (val) => { localDescription.value = val || ''; },
    { immediate: true }
);
function emitDescription() {
    emit('update:modelValue', { ...props.modelValue, norms_description: localDescription.value });
}

const localHelpSectionId = ref('');
watch(
    () => props.modelValue.norms_help_section_id,
    (val) => {
        localHelpSectionId.value = val !== null && val !== undefined && val !== '' ? String(val) : '';
    },
    { immediate: true }
);
function emitHelpSectionId() {
    const raw = localHelpSectionId.value.trim();
    const id = raw === '' ? null : Number.parseInt(raw, 10);
    emit('update:modelValue', {
        ...props.modelValue,
        norms_help_section_id: Number.isFinite(id) && id > 0 ? id : null,
    });
}

function emptyGrid() {
    const g = {};
    POWER_LEVELS.forEach((pl) => { g[pl] = Array(MAX_LEVEL).fill(null); });
    return g;
}

function updateCell(powerLevel, levelIndex, value) {
    if (hasFormula.value) return;
    const newGrid = { ...grid.value };
    newGrid[powerLevel] = [...(newGrid[powerLevel] || Array(MAX_LEVEL).fill(null))];
    newGrid[powerLevel][levelIndex] = value !== '' && value !== null ? Number(value) : null;
    grid.value = newGrid;
}

function clearGrid() {
    if (hasFormula.value) return;
    if (!confirm('Vider toute la grille ?')) return;
    grid.value = emptyGrid();
}

function fillLinear(powerLevel) {
    if (hasFormula.value) return;
    const row = grid.value[powerLevel] || [];
    const first = row[0] ?? 0;
    const last = row[MAX_LEVEL - 1] ?? 0;
    if (first === 0 && last === 0) return;
    const newRow = [];
    for (let i = 0; i < MAX_LEVEL; i++) {
        newRow.push(Math.round(first + (last - first) * i / (MAX_LEVEL - 1)));
    }
    const newGrid = { ...grid.value };
    newGrid[powerLevel] = newRow;
    grid.value = newGrid;
}

function addCondition() {
    localConditions.value.push({
        characteristic_key: '', operator: '=', value: 0, target: 'power', modifier: 0, comment: '',
    });
    emitConditions();
}

function removeCondition(index) {
    localConditions.value.splice(index, 1);
    emitConditions();
}

function updateConditionLocal(index, field, value) {
    if (localConditions.value[index]) {
        localConditions.value[index][field] = value;
    }
}

function updateConditionAndEmit(index, field, value) {
    updateConditionLocal(index, field, value);
    emitConditions();
}

const levels = Array.from({ length: MAX_LEVEL }, (_, i) => i + 1);

const hasGrid = computed(() => {
    if (!props.modelValue.norms_grid) return false;
    return POWER_LEVELS.some((pl) =>
        (props.modelValue.norms_grid[pl] || []).some((v) => v !== null && v !== '')
    );
});

/** Options formatées pour le SelectSearchField des caractéristiques */
const characteristicsOptions = computed(() =>
    (props.characteristicsList || []).map((c) => ({
        value: c.id || c.key,
        label: c.name || c.id || c.key,
    }))
);
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-semibold text-title">Normes (Charte)</h4>
            <Btn v-if="hasGrid && !hasFormula" size="xs" color="error" variant="ghost" @click="clearGrid">
                Vider la grille
            </Btn>
        </div>

        <div>
            <label class="label text-xs">Description</label>
            <textarea
                v-model="localDescription"
                @change="emitDescription"
                class="textarea textarea-bordered textarea-sm w-full"
                rows="2"
                placeholder="Description de la norme (contexte, usage…)"
            />
        </div>

        <div>
            <label class="label text-xs">Section d’aide (CMS, template Texte)</label>
            <input
                v-model="localHelpSectionId"
                type="number"
                min="1"
                class="input input-bordered input-sm w-full max-w-xs font-mono"
                placeholder="ID section (ex. depuis l’éditeur de page)"
                @change="emitHelpSectionId"
            />
            <p class="text-xs text-content/50 mt-1">
                Optionnel : ID d’une section « Texte » ; son contenu s’affiche sous la charte sur le site et dans les catalogues.
            </p>
        </div>

        <div class="space-y-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <label class="label text-xs py-0">Formule (palier neutre)</label>
                <div class="flex gap-1">
                    <Btn
                        size="xs"
                        color="primary"
                        variant="ghost"
                        :disabled="!hasFormula || previewLoading"
                        @click="previewFormula"
                    >
                        {{ previewLoading ? 'Calcul…' : 'Aperçu' }}
                    </Btn>
                    <Btn v-if="hasFormula" size="xs" color="warning" variant="ghost" @click="clearFormula">
                        Manuel
                    </Btn>
                </div>
            </div>
            <textarea
                v-model="localFormula"
                class="textarea textarea-bordered textarea-sm w-full font-mono text-xs"
                rows="3"
                placeholder='Ex. round(3*pow([level],0.633))'
                @change="emitFormula"
                @input="schedulePreview"
            />
            <p class="text-xs text-content/50">
                Une formule de <code>[level]</code> et de <code>[clé]</code> (norme au même niveau).
                Avec des coefficients ci-dessous, la formule définit le palier <strong>neutre</strong> ;
                les autres paliers = neutre × coef. Sans coefficients, utiliser <code>[power]</code> (−2…+2).
                Si renseignée, la grille est calculée à l’enregistrement.
            </p>
            <p v-if="previewError" class="text-xs text-error">{{ previewError }}</p>
            <p v-if="hasFormula" class="text-xs text-warning">
                Grille en lecture seule (générée par formule).
            </p>
        </div>

        <div class="space-y-2 rounded-lg border border-base-300 bg-base-200/30 p-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h5 class="text-xs font-semibold text-title">Coefficients de palier</h5>
                <div class="flex flex-wrap gap-1">
                    <Btn
                        size="xs"
                        color="primary"
                        variant="ghost"
                        :disabled="coefficientsDisabled"
                        @click="applyDefaultCoefficients"
                    >
                        Valeurs par défaut
                    </Btn>
                    <Btn
                        v-if="hasCoefficients"
                        size="xs"
                        color="warning"
                        variant="ghost"
                        @click="clearCoefficients"
                    >
                        Sans coefficients
                    </Btn>
                </div>
            </div>
            <p v-if="coefficientsDisabled" class="text-xs text-warning">
                La formule contient <code>[power]</code> : les coefficients sont désactivés (un seul mode).
            </p>
            <p v-else class="text-xs text-content/50">
                Neutre = 1 (fixe). Sans coefficients, la formule peut utiliser <code>[power]</code> ou hériter des paliers des dépendances.
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                <div
                    v-for="pl in POWER_LEVELS"
                    :key="'coef-' + pl"
                    class="flex flex-col gap-1"
                >
                    <label class="text-xs font-medium flex items-center gap-1">
                        <span
                            class="inline-block w-2 h-2 rounded-full"
                            :style="{ backgroundColor: POWER_COLORS[pl] }"
                        />
                        {{ POWER_LABELS[pl] }}
                    </label>
                    <input
                        v-if="pl === 'neutral'"
                        type="number"
                        class="input input-xs input-bordered w-full bg-base-200"
                        :value="1"
                        readonly
                        disabled
                    />
                    <input
                        v-else
                        type="number"
                        step="0.01"
                        min="0.01"
                        class="input input-xs input-bordered w-full"
                        :class="{ 'bg-base-200': coefficientsDisabled }"
                        :disabled="coefficientsDisabled"
                        :value="localCoefficients?.[pl] ?? ''"
                        :placeholder="String(DEFAULT_POWER_COEFFICIENTS[pl])"
                        @change="updateCoefficient(pl, $event.target.value)"
                    />
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-xs table-pin-rows">
                <thead>
                    <tr>
                        <th class="sticky left-0 z-10 bg-base-200 min-w-[100px]">Puissance</th>
                        <th
                            v-for="lvl in levels"
                            :key="lvl"
                            class="text-center min-w-[48px] text-xs"
                        >{{ lvl }}</th>
                        <th class="min-w-[60px]"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="pl in POWER_LEVELS" :key="pl">
                        <td class="sticky left-0 z-10 bg-base-200 font-medium text-xs whitespace-nowrap">
                            <span
                                class="inline-block w-2 h-2 rounded-full mr-1"
                                :style="{ backgroundColor: POWER_COLORS[pl] }"
                            />
                            {{ POWER_LABELS[pl] }}
                        </td>
                        <td v-for="(lvl, idx) in levels" :key="idx" class="p-0">
                            <input
                                type="number"
                                class="input input-xs input-bordered w-full text-center p-0.5"
                                :class="{ 'bg-base-200': hasFormula }"
                                :value="grid[pl]?.[idx] ?? ''"
                                :readonly="hasFormula"
                                @input="updateCell(pl, idx, $event.target.value)"
                                step="any"
                            />
                        </td>
                        <td>
                            <Btn
                                v-if="!hasFormula"
                                size="xs"
                                variant="ghost"
                                @click="fillLinear(pl)"
                                title="Interpoler linéairement entre level 1 et 20"
                            >
                                <i class="fa-solid fa-chart-line text-xs" />
                            </Btn>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <h5 class="text-xs font-semibold text-title">Conditions de lecture</h5>
                <Btn size="xs" color="primary" variant="ghost" @click="addCondition">
                    <i class="fa-solid fa-plus mr-1" /> Condition
                </Btn>
            </div>

            <div
                v-for="(cond, idx) in localConditions"
                :key="'cond-' + idx"
                class="flex flex-wrap items-center gap-2 p-2 rounded-lg bg-base-200/50 border border-base-300"
            >
                <div class="flex-1 min-w-[140px]">
                    <SelectSearchField
                        size="xs"
                        placeholder="Caractéristique…"
                        :options="characteristicsOptions"
                        :model-value="cond.characteristic_key"
                        @update:model-value="updateConditionAndEmit(idx, 'characteristic_key', $event)"
                    />
                </div>

                <select
                    class="select select-xs select-bordered w-16"
                    :value="cond.operator"
                    @change="updateConditionAndEmit(idx, 'operator', $event.target.value)"
                >
                    <option v-for="op in CONDITION_OPERATORS" :key="op" :value="op">{{ op }}</option>
                </select>

                <input
                    type="number"
                    class="input input-xs input-bordered w-20"
                    v-model.number="cond.value"
                    @change="emitConditions"
                    placeholder="Valeur"
                />

                <span class="text-xs text-content/60">→</span>

                <select
                    class="select select-xs select-bordered w-32"
                    :value="cond.target"
                    @change="updateConditionAndEmit(idx, 'target', $event.target.value)"
                >
                    <option v-for="t in CONDITION_TARGETS" :key="t.value" :value="t.value">{{ t.label }}</option>
                </select>

                <input
                    type="number"
                    class="input input-xs input-bordered w-16"
                    v-model.number="cond.modifier"
                    @change="emitConditions"
                    placeholder="±N"
                />

                <input
                    type="text"
                    class="input input-xs input-bordered flex-1 min-w-[120px]"
                    v-model="cond.comment"
                    @change="emitConditions"
                    placeholder="Commentaire…"
                />

                <Btn size="xs" color="error" variant="ghost" @click="removeCondition(idx)">
                    <i class="fa-solid fa-trash text-xs" />
                </Btn>
            </div>

            <p v-if="!localConditions.length" class="text-xs text-content/50 italic">
                Aucune condition. Cliquez sur « + Condition » pour ajouter une règle de lecture.
            </p>
        </div>
    </div>
</template>
