<script setup>
/**
 * Tableau interactif de normes : 5 lignes puissance × 20 colonnes niveaux.
 * Clic sur en-tête ou cellule → sélectionne le niveau. Survol → tooltip dés (moyenne).
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import {
    POWER_LEVELS,
    POWER_LABELS,
    POWER_COLORS,
    POWER_BG_COLORS,
    MAX_LEVEL,
    NEUTRAL_INDEX,
} from '@/Utils/Characteristic/normsConstants';
import { levelColor } from '@/Utils/Characteristic/levelColorScale';
import { formatDiceHoverTooltip } from '@/Utils/dice/diceEquivalents.js';

const props = defineProps({
    grid: { type: Object, required: true },
    effectivePowerIndex: { type: Number, default: NEUTRAL_INDEX },
    selectedLevel: { type: Number, default: null },
    effectiveLevelIndex: { type: Number, default: null },
    minNumeric: { type: Number, default: null },
    maxNumeric: { type: Number, default: null },
    /** Affiche les équivalents dés au survol (valeurs = moyennes). */
    diceTooltips: { type: Boolean, default: true },
});

const emit = defineEmits(['select-level']);

const levels = Array.from({ length: MAX_LEVEL }, (_, i) => i + 1);

const activePowerLevel = computed(() => POWER_LEVELS[props.effectivePowerIndex]);
const activeColIndex = computed(() => props.effectiveLevelIndex);

function cellValue(powerLevel, levelIndex) {
    const row = props.grid?.[powerLevel];
    if (!row) return null;
    return row[levelIndex];
}

function isResolvedCell(powerLevel, levelIndex) {
    return powerLevel === activePowerLevel.value && levelIndex === activeColIndex.value;
}

function isOutOfBounds(value) {
    if (value === null || value === undefined || value === '') return false;
    const num = Number(value);
    if (!Number.isFinite(num)) return false;
    if (props.minNumeric !== null && num < props.minNumeric) return true;
    if (props.maxNumeric !== null && num > props.maxNumeric) return true;
    return false;
}

function diceTooltipFor(value) {
    if (!props.diceTooltips) return '';
    return formatDiceHoverTooltip(value);
}

function onSelectLevel(lvl) {
    emit('select-level', lvl);
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="table table-xs table-pin-rows table-pin-cols">
            <thead>
                <tr>
                    <th class="sticky left-0 z-10 bg-base-200" />
                    <th
                        v-for="lvl in levels"
                        :key="lvl"
                        class="text-center cursor-pointer select-none transition-colors text-xs min-w-[2.25rem] hover:bg-primary/10"
                        :class="{
                            'font-bold': selectedLevel === lvl,
                        }"
                        :style="{
                            color: levelColor(lvl),
                            backgroundColor: activeColIndex === lvl - 1 ? 'rgba(99, 102, 241, 0.12)' : undefined,
                        }"
                        :title="`Sélectionner le niveau ${lvl}`"
                        @click="onSelectLevel(lvl)"
                    >
                        {{ lvl }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(pl, plIdx) in POWER_LEVELS"
                    :key="pl"
                    class="transition-all duration-200"
                    :style="{
                        backgroundColor: plIdx === effectivePowerIndex ? POWER_BG_COLORS[pl] : undefined,
                    }"
                >
                    <td
                        class="sticky left-0 z-10 bg-base-200 font-medium text-xs whitespace-nowrap pr-2"
                        :style="{ borderLeftColor: POWER_COLORS[pl], borderLeftWidth: plIdx === effectivePowerIndex ? '3px' : '1px' }"
                    >
                        <span
                            class="inline-block w-2 h-2 rounded-full mr-1"
                            :style="{ backgroundColor: POWER_COLORS[pl] }"
                        />
                        {{ POWER_LABELS[pl] }}
                    </td>
                    <td
                        v-for="(lvl, lvlIdx) in levels"
                        :key="lvlIdx"
                        class="p-0 text-center text-xs transition-all duration-200"
                        :class="{
                            'font-bold ring-2 ring-primary rounded': isResolvedCell(pl, lvlIdx),
                            'bg-error/10 text-error': isOutOfBounds(cellValue(pl, lvlIdx)),
                        }"
                        :style="{
                            backgroundColor: activeColIndex === lvlIdx
                                ? (plIdx === effectivePowerIndex ? 'rgba(99, 102, 241, 0.2)' : 'rgba(99, 102, 241, 0.06)')
                                : undefined,
                        }"
                    >
                        <Tooltip
                            v-if="cellValue(pl, lvlIdx) !== null && cellValue(pl, lvlIdx) !== undefined && diceTooltipFor(cellValue(pl, lvlIdx))"
                            :content="diceTooltipFor(cellValue(pl, lvlIdx))"
                            placement="top"
                            class="block h-full w-full"
                        >
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs h-7 min-h-0 w-full rounded-none px-0 font-inherit tabular-nums hover:bg-primary/15"
                                :aria-label="`Niveau ${lvl}, ${POWER_LABELS[pl]} : ${cellValue(pl, lvlIdx)}`"
                                @click="onSelectLevel(lvl)"
                            >
                                {{ cellValue(pl, lvlIdx) }}
                            </button>
                        </Tooltip>
                        <button
                            v-else-if="cellValue(pl, lvlIdx) !== null && cellValue(pl, lvlIdx) !== undefined"
                            type="button"
                            class="btn btn-ghost btn-xs h-7 min-h-0 w-full rounded-none px-0 font-inherit tabular-nums hover:bg-primary/15"
                            :aria-label="`Niveau ${lvl}, ${POWER_LABELS[pl]} : ${cellValue(pl, lvlIdx)}`"
                            @click="onSelectLevel(lvl)"
                        >
                            {{ cellValue(pl, lvlIdx) }}
                        </button>
                        <span v-else class="inline-flex h-7 w-full items-center justify-center text-base-content/20">—</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
