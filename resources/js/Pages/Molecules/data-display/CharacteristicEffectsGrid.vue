<script setup>
/**
 * CharacteristicEffectsGrid — Grille d'effets/caractéristiques responsive.
 *
 * @description
 * Affiche une liste d'items (icône + nom + valeur + unité) en grille responsive.
 * Chaque ligne ouvre la fiche joueur unifiée (CharacteristicInfoPanel).
 *
 * @props {Array} items - [{ key, icon, color, name, shortLabel, value, unit, tooltip }]
 * @props {String} labelMode - 'full' | 'short' | 'icon-only'
 * @props {String} [group] - Groupe API pour la fiche (object, consumable…)
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CharacteristicInfoPanel from "@/Pages/Molecules/data-display/CharacteristicInfoPanel.vue";
import { getCharacteristicColorStyle } from "@/Composables/entity/useCharacteristicDisplay";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    labelMode: {
        type: String,
        default: "full",
        validator: (v) => ["full", "short", "icon-only"].includes(v),
    },
    group: {
        type: String,
        default: "",
    },
});

const chipItems = computed(() =>
    (props.items || []).filter((item) => item && item.value != null && item.value !== ""),
);

const gridClass = computed(
    () =>
        "grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-3 gap-y-1.5 items-center",
);

const getColorStyle = (item) => getCharacteristicColorStyle(item?.color);

const displayLabel = (item) => {
    if (props.labelMode === "full") return item?.name ?? item?.label ?? "";
    if (props.labelMode === "short") return item?.shortLabel ?? item?.name ?? item?.label ?? "";
    return "";
};

/** Valeur avec unité si disponible */
const displayValueWithUnit = (item) => {
    const v = item?.value ?? "";
    const unit = item?.unit;
    return unit ? `${v} ${unit}` : v;
};

/**
 * @param {object} item
 * @returns {object}
 */
function infoModel(item) {
    return {
        key: item?.key || item?.characteristic_key || "",
        name: item?.name || item?.label || "",
        shortName: item?.shortLabel || item?.name || "",
        icon: item?.icon || "",
        color: item?.color || "",
        helper: item?.helper || "",
        descriptions: item?.descriptions || "",
        displayValue: displayValueWithUnit(item),
        formulaDisplay: item?.formula_display || item?.formulaDisplay || "",
        limitMin: item?.limit_min ?? item?.limitMin ?? null,
        limitMax: item?.limit_max ?? item?.limitMax ?? null,
        defaultValue: item?.default_value ?? item?.defaultValue ?? null,
    };
}

const showLabel = computed(() => props.labelMode !== "icon-only");
</script>

<template>
    <div :class="gridClass">
        <Tooltip
            v-for="(item, idx) in chipItems"
            :key="idx"
            placement="top"
            class="inline-flex items-center gap-1.5 min-w-0"
        >
            <template #content>
                <CharacteristicInfoPanel
                    v-if="item.key || item.characteristic_key"
                    :model="infoModel(item)"
                    :group="group"
                    tone="dark"
                />
                <span v-else class="text-xs text-white/90">
                    {{ item.tooltip || `${item.name || item.label || ""}: ${displayValueWithUnit(item)}` }}
                </span>
            </template>
            <Icon
                v-if="item.icon"
                :source="item.icon"
                :alt="item.name || item.label || ''"
                size="xs"
                class="shrink-0 opacity-80"
                :style="getColorStyle(item)"
            />
            <template v-if="showLabel">
                <span
                    v-if="displayLabel(item)"
                    class="text-xs truncate shrink min-w-0"
                    :style="getColorStyle(item)"
                >
                    {{ displayLabel(item) }}:
                </span>
            </template>
            <span class="text-xs font-medium truncate min-w-0" :style="getColorStyle(item)">
                {{ displayValueWithUnit(item) }}
            </span>
        </Tooltip>
        <span v-if="!chipItems.length" class="text-base-content/40 text-xs col-span-full">—</span>
    </div>
</template>
