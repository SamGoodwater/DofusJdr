<script setup>
/**
 * Fiche joueur d'une caractéristique (tooltip / popover).
 *
 * Contenu : nom, icône, description, min / défaut / max, formula_display,
 * « Avec tes valeurs » si runtime fourni, table par niveau.
 * Pas de décomposition des couches (réservée au panneau MJ).
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import {
    humanizeCharacteristicFormulaText,
    mapPlaceholdersForPlayer,
    normalizeCharacteristicIcon,
} from "@/Utils/Entity/characteristicTooltipLabels";
import { formatCharacteristicLimitLine } from "@/Utils/Entity/entity-view-ui";
import { buildFormulaTableView } from "@/Utils/characteristic/formulaConfig";
import CharacteristicFormulaRichText from "@/Pages/Molecules/data-display/CharacteristicFormulaRichText.vue";
import { useCharacteristicCard } from "@/Composables/characteristic/useCharacteristicCard";

const props = defineProps({
    model: {
        type: Object,
        required: true,
    },
    /** Groupe API (creature/object/…) ; déduit de la clé si omis. */
    group: {
        type: String,
        default: "",
    },
    entity: {
        type: String,
        default: "*",
    },
    /** Charge l'API card pour compléter descriptions / défaut / formula_display. */
    fetchCard: {
        type: Boolean,
        default: true,
    },
    /** Variante sombre (Tooltip glass) ou claire (Popover). */
    tone: {
        type: String,
        default: "dark",
        validator: (v) => ["dark", "light"].includes(v),
    },
});

const cardOptions = computed(() => ({
    key: props.model?.key || "",
    group: props.group || props.model?.cardGroup || inferGroupFromKey(props.model?.key),
    entity: props.entity || props.model?.cardEntity || "*",
    viewModel: props.model,
    enabled: props.fetchCard && Boolean(props.model?.key),
}));

const { enrichedViewModel } = useCharacteristicCard(cardOptions);
const vm = enrichedViewModel;

const isDark = computed(() => props.tone === "dark");

const iconSource = computed(() =>
    normalizeCharacteristicIcon(vm.value?.icon || vm.value?._resolvedIcon || ""),
);

const formulaTable = computed(() => buildFormulaTableView(vm.value?.formulaDisplay || ""));

const friendlyFormula = computed(() => {
    if (formulaTable.value) return "";
    return humanizeCharacteristicFormulaText(vm.value?.formulaDisplay || "");
});

const friendlyResolution = computed(() =>
    humanizeCharacteristicFormulaText(vm.value?.substituted || ""),
);

const termRows = computed(() => mapPlaceholdersForPlayer(vm.value?.placeholders));

const hasCalculation = computed(
    () =>
        Boolean(
            formulaTable.value ||
                friendlyFormula.value ||
                friendlyResolution.value ||
                termRows.value.length,
        ),
);

const levelRows = computed(() => {
    const table = vm.value?.levelTable;
    if (!Array.isArray(table) || table.length <= 1) return [];
    return table;
});

const limitLine = computed(() => {
    const base = formatCharacteristicLimitLine({
        limit_min: vm.value?.limitMin,
        limit_max: vm.value?.limitMax,
    });
    const def = vm.value?.defaultValue;
    const parts = [];
    if (base) parts.push(base);
    if (def !== null && def !== undefined && String(def).trim() !== "") {
        parts.push(`défaut ${def}`);
    }
    if (vm.value?.limitAbsolute) {
        parts.push("limite absolue");
    }
    return parts.join(" · ");
});

const limitsByLevel = computed(() => {
    const rows = vm.value?.limitsByLevel;
    if (!Array.isArray(rows) || rows.length === 0 || !vm.value?.limitsDependOnLevel) {
        return [];
    }
    return rows;
});

const rootClass = computed(() =>
    isDark.value
        ? "characteristic-info-panel max-w-xs space-y-2.5 text-left text-sm text-white/95"
        : "characteristic-info-panel max-w-sm space-y-2.5 text-left text-sm text-base-content",
);

const mutedClass = computed(() => (isDark.value ? "text-white/55" : "text-base-content/55"));
const softClass = computed(() => (isDark.value ? "text-white/80" : "text-base-content/80"));
const softerClass = computed(() => (isDark.value ? "text-white/65" : "text-base-content/65"));
const boxClass = computed(() =>
    isDark.value
        ? "rounded-md border border-white/15 bg-white/5 px-2.5 py-2"
        : "rounded-md border border-base-content/15 bg-base-200/60 px-2.5 py-2",
);
const chipClass = computed(() =>
    isDark.value
        ? "rounded bg-white/10 px-1.5 py-0.5 text-[11px] tabular-nums text-white/85"
        : "rounded bg-base-200 px-1.5 py-0.5 text-[11px] tabular-nums text-base-content/85",
);

/**
 * @param {string|null|undefined} key
 * @returns {string}
 */
function inferGroupFromKey(key) {
    const k = String(key || "");
    if (k.endsWith("_creature")) return "creature";
    if (k.endsWith("_object")) return "object";
    if (k.endsWith("_spell")) return "spell";
    if (k.endsWith("_consumable")) return "consumable";
    if (k.endsWith("_profile")) return "profile";
    return "";
}
</script>

<template>
    <div :class="rootClass">
        <div class="flex items-start gap-2.5">
            <Icon
                v-if="iconSource"
                :source="iconSource"
                :alt="vm.name || ''"
                size="md"
                class="mt-0.5 shrink-0 opacity-95"
            />
            <div class="min-w-0 space-y-0.5">
                <div class="font-semibold leading-snug tracking-wide">{{ vm.name }}</div>
                <div
                    v-if="!vm.hideDisplayValueInTooltip"
                    class="text-xl font-semibold tabular-nums"
                    :class="isDark ? 'text-white' : 'text-base-content'"
                >
                    {{ vm.displayValue ?? "—" }}
                </div>
            </div>
        </div>

        <p v-if="vm.helper" class="text-xs leading-relaxed" :class="softClass">
            {{ vm.helper }}
        </p>
        <p
            v-if="vm.descriptions && vm.descriptions !== vm.helper"
            class="text-xs leading-relaxed italic"
            :class="softerClass"
        >
            {{ vm.descriptions }}
        </p>
        <p v-if="limitLine" class="text-xs leading-relaxed" :class="softerClass">
            {{ limitLine }}
        </p>

        <div v-if="hasCalculation" class="space-y-2" :class="boxClass">
            <p class="text-[10px] font-semibold uppercase tracking-wider" :class="mutedClass">
                Calcul
            </p>

            <div v-if="formulaTable" class="text-xs leading-snug" :class="softClass">
                <CharacteristicFormulaRichText :formula="String(vm.formulaDisplay || '')" />
            </div>

            <div v-else-if="friendlyFormula" class="text-xs leading-snug" :class="softClass">
                <span :class="mutedClass">Formule · </span>
                {{ friendlyFormula }}
            </div>

            <div v-if="friendlyResolution" class="text-xs leading-snug" :class="softClass">
                <span :class="mutedClass">Avec tes valeurs · </span>
                <span class="tabular-nums">{{ friendlyResolution }}</span>
            </div>

            <ul v-if="termRows.length" class="space-y-1 pt-0.5">
                <li
                    v-for="row in termRows"
                    :key="row.id"
                    class="flex items-baseline justify-between gap-3 border-t border-dashed pt-1 text-xs first:border-t-0 first:pt-0"
                    :class="isDark ? 'border-white/15' : 'border-base-content/15'"
                >
                    <span class="min-w-0" :class="softClass">{{ row.label }}</span>
                    <span
                        class="shrink-0 font-medium tabular-nums"
                        :class="isDark ? 'text-white' : 'text-base-content'"
                    >{{ row.value }}</span>
                </li>
            </ul>
        </div>

        <div v-if="levelRows.length" class="space-y-1">
            <p class="text-[10px] font-semibold uppercase tracking-wider" :class="mutedClass">
                Selon le niveau
            </p>
            <div class="flex flex-wrap gap-1">
                <span
                    v-for="row in levelRows"
                    :key="row.level"
                    class="inline-flex items-center gap-1"
                    :class="chipClass"
                >
                    <span :class="mutedClass">Niv.{{ row.level }}</span>
                    <span class="font-medium">{{ row.value ?? row.total }}</span>
                </span>
            </div>
        </div>

        <div v-else-if="limitsByLevel.length" class="space-y-1">
            <p class="text-[10px] font-semibold uppercase tracking-wider" :class="mutedClass">
                Limites selon le niveau
            </p>
            <div class="flex flex-wrap gap-1">
                <span
                    v-for="row in limitsByLevel"
                    :key="row.level"
                    class="inline-flex items-center gap-1"
                    :class="chipClass"
                >
                    <span :class="mutedClass">Niv.{{ row.level }}</span>
                    <span class="font-medium">{{ row.min }}…{{ row.max }}</span>
                </span>
            </div>
        </div>
    </div>
</template>
