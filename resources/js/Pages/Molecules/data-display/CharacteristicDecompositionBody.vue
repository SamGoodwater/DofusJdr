<script setup>
/**
 * Corps de décomposition d'une caractéristique (popover Full) — ton fiche de jeu.
 *
 * @props {Object} model — viewModel enrichi (base, object, context, ephemeral, capped, total, placeholders…)
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import {
    humanizeCharacteristicFormulaText,
    mapPlaceholdersForPlayer,
    normalizeCharacteristicIcon,
} from "@/Utils/Entity/characteristicTooltipLabels";
import { buildFormulaTableView } from "@/Utils/characteristic/formulaConfig";
import CharacteristicFormulaRichText from "@/Pages/Molecules/data-display/CharacteristicFormulaRichText.vue";

const props = defineProps({
    model: {
        type: Object,
        required: true,
    },
});

function fmt(value) {
    if (value === null || value === undefined || value === "") return "—";
    return String(value);
}

const iconSource = computed(() =>
    normalizeCharacteristicIcon(props.model?.icon || props.model?._resolvedIcon || ""),
);

const formulaTable = computed(() => buildFormulaTableView(props.model?.formulaDisplay || ""));

const friendlyFormula = computed(() => {
    if (formulaTable.value) return "";
    return humanizeCharacteristicFormulaText(props.model?.formulaDisplay || "");
});

const friendlyResolution = computed(() =>
    humanizeCharacteristicFormulaText(props.model?.substituted || ""),
);

const termRows = computed(() => mapPlaceholdersForPlayer(props.model?.placeholders));

const hasEphemeral = computed(() => {
    const n = Number(props.model?.ephemeral);
    return Number.isFinite(n) && n !== 0;
});

const capLabel = computed(() => {
    if (!props.model?.capped) return "";
    const min = props.model?.limitMin;
    const max = props.model?.limitMax;
    if (min != null && max != null) return `Plafonné (entre ${min} et ${max})`;
    return "Plafonné";
});

const hasLayers =
    computed(() =>
        props.model?.base != null ||
        props.model?.object != null ||
        props.model?.context != null ||
        props.model?.total != null,
    );
</script>

<template>
    <div class="characteristic-decomposition max-w-sm space-y-3 text-left text-sm">
        <div class="flex items-start gap-2.5">
            <Icon
                v-if="iconSource"
                :source="iconSource"
                :alt="model.name || ''"
                size="md"
                class="mt-0.5 shrink-0 opacity-90"
            />
            <div class="min-w-0">
                <div class="font-semibold leading-snug">{{ model.name }}</div>
                <div class="text-lg font-semibold tabular-nums">{{ model.displayValue ?? "—" }}</div>
                <p v-if="model.source === 'total_column'" class="text-xs opacity-70">
                    Valeur fixée sur la fiche
                </p>
                <p v-else-if="model.source === 'composed'" class="text-xs opacity-70">
                    Calculée : base, équipement et bonus de contexte
                </p>
            </div>
        </div>

        <dl
            v-if="hasLayers"
            class="grid grid-cols-[1fr_auto] gap-x-3 gap-y-1 rounded-md border border-base-300/70 bg-base-200/40 px-2.5 py-2 text-xs"
        >
            <dt class="opacity-70">Base</dt>
            <dd class="text-right tabular-nums">{{ fmt(model.base) }}</dd>
            <dt class="opacity-70">Équipement</dt>
            <dd class="text-right tabular-nums">{{ fmt(model.object) }}</dd>
            <dt class="opacity-70">Contexte</dt>
            <dd class="text-right tabular-nums">
                {{ fmt(model.context) }}
                <span v-if="model.contextRaw" class="ml-1 opacity-60">({{ model.contextRaw }})</span>
            </dd>
            <template v-if="hasEphemeral">
                <dt class="opacity-70">Effets temporaires</dt>
                <dd class="text-right tabular-nums">{{ fmt(model.ephemeral) }}</dd>
            </template>
            <dt class="font-medium">Total</dt>
            <dd class="text-right font-medium tabular-nums">{{ fmt(model.total ?? model.displayValue) }}</dd>
        </dl>
        <p v-if="capLabel" class="badge badge-warning badge-sm">{{ capLabel }}</p>

        <div
            v-if="formulaTable || friendlyFormula || friendlyResolution || termRows.length"
            class="space-y-2 rounded-md border border-base-300/70 px-2.5 py-2 text-xs"
        >
            <p class="text-[10px] font-semibold uppercase tracking-wider opacity-60">Calcul</p>
            <div v-if="formulaTable">
                <CharacteristicFormulaRichText :formula="String(model.formulaDisplay || '')" />
            </div>
            <div v-else-if="friendlyFormula">
                <span class="opacity-60">Formule · </span>{{ friendlyFormula }}
            </div>
            <div v-if="friendlyResolution">
                <span class="opacity-60">Avec tes valeurs · </span>
                <span class="tabular-nums">{{ friendlyResolution }}</span>
            </div>
            <ul v-if="termRows.length" class="space-y-1">
                <li
                    v-for="row in termRows"
                    :key="row.id"
                    class="flex justify-between gap-3 border-t border-dashed border-base-300 pt-1 first:border-t-0 first:pt-0"
                >
                    <span class="min-w-0 opacity-80">{{ row.label }}</span>
                    <span class="shrink-0 font-medium tabular-nums">{{ row.value }}</span>
                </li>
            </ul>
        </div>

        <ul
            v-if="Array.isArray(model.itemContributions) && model.itemContributions.length"
            class="space-y-1 text-xs"
        >
            <p class="text-[10px] font-semibold uppercase tracking-wider opacity-60">Objets</p>
            <li
                v-for="line in model.itemContributions"
                :key="line.item_id"
                class="flex justify-between gap-2 border-t border-dashed border-base-300 pt-1"
            >
                <span class="truncate">{{ line.name }} ×{{ line.quantity }}</span>
                <span class="tabular-nums">{{ line.amount }}</span>
            </li>
        </ul>

        <div
            v-if="Array.isArray(model.levelTable) && model.levelTable.length > 1"
            class="space-y-1"
        >
            <p class="text-[10px] font-semibold uppercase tracking-wider opacity-60">
                Selon le niveau
            </p>
            <div class="flex flex-wrap gap-1">
                <span
                    v-for="row in model.levelTable"
                    :key="row.level"
                    class="inline-flex items-center gap-1 rounded bg-base-200 px-1.5 py-0.5 text-[11px] tabular-nums"
                >
                    <span class="opacity-60">Niv.{{ row.level }}</span>
                    <span class="font-medium">{{ row.value ?? row.total }}</span>
                </span>
            </div>
        </div>
    </div>
</template>
