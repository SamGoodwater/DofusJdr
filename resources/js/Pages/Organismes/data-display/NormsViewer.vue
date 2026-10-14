<script setup>
/**
 * Composant assembleur des normes : table + chart + sélecteur de conditions.
 * Layout responsive : côte à côte (lg) ou empilé (sm).
 * @prop {string} [helpSectionHtml] HTML brut depuis l’API ; affichage via sanitizeHtml() (règles détaillées).
 * @prop {string} [helpSectionTitle] Titre optionnel au-dessus du bloc règles.
 */
import { computed, ref, toRef } from 'vue';
import NormsTable from '@/Pages/Molecules/data-display/NormsTable.vue';
import NormsChart from '@/Pages/Molecules/data-display/NormsChart.vue';
import NormsConditionSelector from '@/Pages/Molecules/data-input/NormsConditionSelector.vue';
import ResponsiveActionButton from '@/Pages/Atoms/action/ResponsiveActionButton.vue';
import { useNormsReader } from '@/Composables/characteristic/useNormsReader';
import { sanitizeHtml } from '@/Utils/security/sanitizeHtml';
import { resolveCharacteristicUiColor } from '@/Utils/color/Color';
import {
    POWER_LEVELS,
    POWER_LABELS,
    POWER_COLORS,
} from '@/Utils/Characteristic/normsConstants';

const props = defineProps({
    grid: { type: Object, required: true },
    conditions: { type: Array, default: () => [] },
    description: { type: String, default: '' },
    characteristicName: { type: String, default: '' },
    /** Hex, token `nuance-palette` ou nom de palette Tailwind (`indigo`, `brown`, …). */
    characteristicColor: { type: String, default: 'indigo' },
    availableCharacteristics: { type: Object, default: () => ({}) },
    minLimit: { type: [String, Number, null], default: null },
    maxLimit: { type: [String, Number, null], default: null },
    /** Contenu brut (sanitisé côté client comme SectionTextRead). */
    helpSectionHtml: { type: String, default: '' },
    helpSectionTitle: { type: String, default: '' },
    /** Active un switch Tableau/Graphique (au lieu d'un affichage côte à côte). */
    enableViewToggle: { type: Boolean, default: false },
    /** Affiche le bandeau titre + bouton d'aide. */
    showHeader: { type: Boolean, default: true },
    /** Affiche le switch en mode compact (icônes uniquement). */
    compactToggle: { type: Boolean, default: false },
});

const gridRef = toRef(props, 'grid');
const conditionsRef = toRef(props, 'conditions');

const {
    selectedLevel,
    activeConditionIndices,
    toggleCondition,
    selectLevel,
    effectivePowerIndex,
    effectivePowerLevel,
    effectiveLevelIndex,
    resolvedValue,
} = useNormsReader(gridRef, conditionsRef);

const helpOpen = ref(false);
const activeView = ref('chart');

function parseNumericLimit(limit) {
    if (limit === null || limit === undefined) return null;
    const str = String(limit).trim();
    if (!/^-?\d+([.,]\d+)?$/.test(str)) return null;
    const num = Number(str.replace(',', '.'));
    return Number.isFinite(num) ? num : null;
}

const minNumeric = computed(() => parseNumericLimit(props.minLimit));
const maxNumeric = computed(() => parseNumericLimit(props.maxLimit));

const sanitizedHelpHtml = computed(() => sanitizeHtml(props.helpSectionHtml || ''));

/** Couleur d’accent résolue (variables CSS ou hex) pour texte et pastille. */
const resolvedCharacteristicColor = computed(() => resolveCharacteristicUiColor(props.characteristicColor) || resolveCharacteristicUiColor('indigo'));
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <h4 v-if="showHeader" class="text-sm font-semibold text-base-content/80">
                {{ characteristicName || "Charte de caractéristique" }}
            </h4>
            <div v-else />

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="btn btn-ghost btn-xs"
                    @click="helpOpen = !helpOpen"
                >
                    <i class="fa-solid fa-circle-question text-info mr-1" />
                    Aide
                </button>

                <div v-if="enableViewToggle" class="flex items-center gap-1">
                    <ResponsiveActionButton
                        :label="compactToggle ? '' : 'Tableau'"
                        icon="fa-solid fa-table-cells"
                        :color="activeView === 'table' ? 'primary' : 'neutral'"
                        size="xs"
                        :aria-label="'Vue tableau'"
                        @click="activeView = 'table'"
                    />
                    <ResponsiveActionButton
                        :label="compactToggle ? '' : 'Graphique'"
                        icon="fa-solid fa-chart-line"
                        :color="activeView === 'chart' ? 'primary' : 'neutral'"
                        size="xs"
                        :aria-label="'Vue graphique'"
                        @click="activeView = 'chart'"
                    />
                </div>
            </div>
        </div>

        <div
            v-if="helpOpen"
            class="bg-base-200/40 border border-base-300 rounded-lg p-3 text-sm space-y-2"
        >
            <p>Le tableau indique les valeurs recommandées par <strong>niveau</strong> (colonnes) et par <strong>puissance</strong> (lignes).</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Cliquez sur un <strong>numéro de niveau</strong> (en-tête) ou sur une <strong>cellule</strong> pour sélectionner la colonne correspondante.</li>
                <li>Survolez une cellule pour voir des <strong>équivalents en dés</strong> (la valeur est une moyenne).</li>
                <li>La ligne « <strong>Neutre</strong> » est sélectionnée par défaut. Activez des <strong>conditions</strong> (chips ci-dessous) pour décaler la lecture vers une ligne plus forte ou plus faible.</li>
                <li>Certaines conditions décalent le <strong>niveau</strong> (colonne) au lieu de la puissance.</li>
                <li>La cellule à l'intersection de la ligne et de la colonne actives est la <strong>valeur de référence</strong>.</li>
            </ul>
            <div class="flex flex-wrap gap-2 mt-2">
                <span
                    v-for="pl in POWER_LEVELS"
                    :key="pl"
                    class="inline-flex items-center gap-1 text-xs"
                >
                    <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: POWER_COLORS[pl] }" />
                    {{ POWER_LABELS[pl] }}
                </span>
            </div>
        </div>

        <!-- Valeur résolue -->
        <div v-if="selectedLevel !== null" class="flex items-center gap-3 text-sm">
            <span class="text-base-content/60">
                Niveau <strong :style="{ color: resolvedCharacteristicColor }">{{ selectedLevel }}</strong>
                <template v-if="effectiveLevelIndex !== null && effectiveLevelIndex !== selectedLevel - 1">
                    → lecture au niveau <strong>{{ effectiveLevelIndex + 1 }}</strong>
                </template>
                ·
                Puissance <strong :style="{ color: POWER_COLORS[effectivePowerLevel] }">{{ POWER_LABELS[effectivePowerLevel] }}</strong>
            </span>
            <span v-if="resolvedValue !== null" class="badge badge-lg font-bold" :style="{ backgroundColor: resolvedCharacteristicColor, color: 'white' }">
                {{ resolvedValue }}
            </span>
        </div>

        <div class="space-y-3">
            <div v-show="!enableViewToggle || activeView === 'table'" class="min-w-0 space-y-3">
                <NormsTable
                    :grid="grid"
                    :effective-power-index="effectivePowerIndex"
                    :selected-level="selectedLevel"
                    :effective-level-index="effectiveLevelIndex"
                    :min-numeric="minNumeric"
                    :max-numeric="maxNumeric"
                    @select-level="selectLevel"
                />

                <div class="space-y-1">
                    <p
                        v-if="description"
                        class="text-xs italic text-base-content/60 border-l-2 border-base-300 pl-3"
                    >
                        {{ description }}
                    </p>
                    <p v-if="minLimit !== null || maxLimit !== null" class="text-xs text-base-content/60">
                        Limites :
                        <template v-if="minLimit !== null">min <strong>{{ minLimit }}</strong></template>
                        <template v-if="minLimit !== null && maxLimit !== null"> · </template>
                        <template v-if="maxLimit !== null">max <strong>{{ maxLimit }}</strong></template>
                        <template v-if="minNumeric !== null || maxNumeric !== null">
                            · Les valeurs hors bornes sont surlignées.
                        </template>
                    </p>
                </div>

                <div v-if="conditions.length" class="space-y-1">
                    <h5 class="text-xs font-semibold text-base-content/60 uppercase tracking-wide">Modificateurs</h5>
                    <NormsConditionSelector
                        :conditions="conditions"
                        :active-indices="activeConditionIndices"
                        :available-characteristics="availableCharacteristics"
                        @toggle="toggleCondition"
                    />
                </div>
            </div>

            <div v-show="!enableViewToggle || activeView === 'chart'" class="w-full">
                <NormsChart
                    :grid="grid"
                    :effective-power-index="effectivePowerIndex"
                    :selected-level="selectedLevel"
                    :effective-level-index="effectiveLevelIndex"
                />
            </div>
        </div>

        <div
            v-if="sanitizedHelpHtml"
            class="border-t border-base-300 pt-4 space-y-2"
        >
            <h5 class="text-xs font-semibold text-base-content/60 uppercase tracking-wide">
                {{ helpSectionTitle || 'Règles et conseils' }}
            </h5>
            <!-- eslint-disable-next-line vue/no-v-html -- contenu sanitizé via sanitizeHtml() -->
            <div class="prose prose-sm max-w-none text-base-content norms-help-section-content" v-html="sanitizedHelpHtml" />
        </div>
    </div>
</template>
