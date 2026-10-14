<script setup>
/**
 * Extras propres aux barèmes de sorts : bandes basse/moy/haute, dés, budget par tour.
 * Affiché dans l'outil Barème générique quand la caractéristique sélectionnée s'y prête.
 */
import { computed, ref, watch } from "vue";
import axios from "axios";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import {
    DISPLAY_BANDS,
    SPELL_SCALE_FAMILIES,
} from "@/Composables/tools/useSpellReferenceScales.js";
import { formatDiceSummary, typicalModifier } from "@/Utils/dice/diceEquivalents.js";
import { POWER_LEVELS, NEUTRAL_INDEX, MAX_LEVEL } from "@/Utils/Characteristic/normsConstants";

const props = defineProps({
    characteristicKey: { type: String, required: true },
});

const loading = ref(false);
const error = ref("");
const norms = ref(null);
const actionPointsGrid = ref(null);
const level = ref(1);
const convertValue = ref("");

const family = computed(
    () => SPELL_SCALE_FAMILIES.find((f) => f.key === props.characteristicKey) || null,
);

const FALLBACK_ACTION_POINTS = [
    6, 6, 6, 7, 7, 7, 8, 8, 8, 9,
    9, 9, 10, 10, 10, 11, 11, 11, 12, 12,
];

async function load() {
    if (!family.value) {
        norms.value = null;
        return;
    }
    loading.value = true;
    error.value = "";
    try {
        const [spellRes, paRes] = await Promise.all([
            axios.get(`/api/characteristics/${encodeURIComponent(props.characteristicKey)}/norms/*`, {
                params: { group: "spell" },
            }),
            axios.get("/api/characteristics/action_points_creature/norms/*", {
                params: { group: "creature" },
            }),
        ]);
        norms.value = spellRes.data;
        actionPointsGrid.value = paRes.data?.norms?.grid || null;
    } catch (e) {
        error.value = e?.response?.data?.error || "Impossible de charger les extras sort.";
        norms.value = null;
    } finally {
        loading.value = false;
    }
}

watch(() => props.characteristicKey, load, { immediate: true });

function gridValue(grid, power, levelIndex) {
    const series = grid?.[power];
    if (!Array.isArray(series)) return null;
    return series[levelIndex] ?? null;
}

const clampedLevel = computed(() =>
    Math.max(1, Math.min(MAX_LEVEL, Number(level.value) || 1)),
);

const levelIndex = computed(() => clampedLevel.value - 1);

const mod = computed(() => typicalModifier(clampedLevel.value));

const bandRows = computed(() => {
    const grid = norms.value?.norms?.grid;
    if (!grid) return [];
    return DISPLAY_BANDS.map((band) => {
        const value = gridValue(grid, band.power, levelIndex.value);
        const n = Number(value);
        const turn =
            Number.isFinite(n)
                ? Math.max(1, Math.round((n * actionPoints.value) / 3))
                : null;
        return {
            ...band,
            value,
            turn,
            dice: Number.isFinite(n) ? formatDiceSummary(n, { mod: mod.value }) : "—",
            diceTurn: turn != null ? formatDiceSummary(turn, { mod: mod.value }) : "—",
        };
    });
});

const actionPoints = computed(() => {
    const grid = actionPointsGrid.value;
    const power = POWER_LEVELS[NEUTRAL_INDEX] || "neutral";
    const value = gridValue(grid, power, levelIndex.value);
    const n = Number(value);
    if (Number.isFinite(n)) return n;
    return FALLBACK_ACTION_POINTS[levelIndex.value] ?? 6;
});

const convertSuggestions = computed(() => {
    const raw = String(convertValue.value || "").trim().replace(",", ".");
    if (!raw) return [];
    const n = Number(raw);
    if (!Number.isFinite(n) || n <= 0) return [];
    return [
        {
            label: "Dés",
            text: formatDiceSummary(n, { mod: 0 }),
        },
        {
            label: "Modificateur typique (niveau)",
            text: String(mod.value),
        },
    ];
});
</script>

<template>
    <div v-if="family" class="rounded-box border border-base-content/15 p-3 space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h4 class="text-sm font-semibold">Extras sort — {{ family.label }}</h4>
            <label class="form-control w-24">
                <span class="label-text text-xs">Niveau</span>
                <input
                    v-model.number="level"
                    type="number"
                    min="1"
                    :max="MAX_LEVEL"
                    class="input input-bordered input-sm"
                />
            </label>
        </div>

        <div v-if="loading" class="text-xs text-base-content/60">Chargement…</div>
        <div v-else-if="error" class="alert alert-warning text-xs">{{ error }}</div>
        <template v-else>
            <div class="overflow-x-auto">
                <table class="table table-zebra table-sm">
                    <thead>
                        <tr>
                            <th>Bande</th>
                            <th>Par sort</th>
                            <th>Dés</th>
                            <th>Budget tour</th>
                            <th>Dés (tour)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in bandRows" :key="row.power">
                            <td>{{ row.short }}</td>
                            <td class="tabular-nums font-medium">{{ row.value ?? "—" }}</td>
                            <td class="text-xs">{{ row.dice }}</td>
                            <td class="tabular-nums">{{ row.turn ?? "—" }}</td>
                            <td class="text-xs">{{ row.diceTurn }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-end gap-3">
                <InputField
                    v-model="convertValue"
                    label="Convertir une valeur"
                    type="number"
                    class="w-40"
                    size="sm"
                />
                <ul v-if="convertSuggestions.length" class="text-xs space-y-1">
                    <li v-for="s in convertSuggestions" :key="s.label">
                        <span class="text-base-content/55">{{ s.label }} · </span>{{ s.text }}
                    </li>
                </ul>
            </div>
            <p class="text-[11px] text-base-content/55">
                PA de référence (neutre) au niveau {{ clampedLevel }} : {{ actionPoints }}.
                Budget tour = valeur × PA ÷ 3.
            </p>
        </template>
    </div>
</template>
