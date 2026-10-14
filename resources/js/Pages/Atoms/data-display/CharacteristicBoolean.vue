<script setup>
/**
 * CharacteristicBoolean — Atome d'affichage d'une caractéristique booléenne (oui/non).
 *
 * @description
 * Compact : icône seule, padding minimal. Normal : carte avec couleur (fond teinté, ombre, blur),
 * icône + label en dessous.
 *
 * @props {Object} def - Définition (key, name, icon, iconFalse?, color, descriptions)
 * @props {boolean} value - Valeur courante
 * @props {boolean} [compact] - Mode compact (icône seule)
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CharacteristicInfoPanel from "@/Pages/Molecules/data-display/CharacteristicInfoPanel.vue";
import {
    getCharacteristicColorStyle,
    getCharacteristicContainerStyle,
    resolveValueOverride,
} from "@/Composables/entity/useCharacteristicDisplay";

const props = defineProps({
    def: { type: Object, required: true },
    value: { type: Boolean, default: false },
    compact: { type: Boolean, default: false },
});

const override = computed(() => resolveValueOverride(props.def?.value_overrides, props.value));

const tooltipText = computed(() => {
    const sub = override.value?.subtitle;
    if (sub) return sub;
    return props.def?.descriptions || props.def?.helper || "";
});
const label = computed(() => props.def?.short_name || props.def?.name || props.def?.key || "—");
const iconSource = computed(() => {
    const ov = override.value;
    if (ov?.icon) return ov.icon;
    return props.value ? props.def?.icon : (props.def?.iconFalse ?? props.def?.icon);
});
const resolvedColor = computed(() => {
    const ov = override.value;
    if (ov?.color) return ov.color;
    return props.def?.color;
});
const infoModel = computed(() => ({
    key: props.def?.key || "",
    name: props.def?.name || props.def?.short_name || props.def?.key || "",
    shortName: props.def?.short_name || props.def?.name || "",
    icon: iconSource.value || props.def?.icon || "",
    color: resolvedColor.value || "",
    helper: props.def?.helper || "",
    descriptions: props.def?.descriptions || tooltipText.value || "",
    displayValue: props.value ? "Oui" : "Non",
    hideDisplayValueInTooltip: false,
}));
const useInfoPanel = computed(() => Boolean(props.def?.key));
const alt = computed(() => props.def?.name || props.def?.key || "—");

const iconStyle = computed(() =>
    props.value && resolvedColor.value ? getCharacteristicColorStyle(resolvedColor.value) ?? {} : {},
);
const containerStyle = computed(() =>
    props.compact || !props.value ? {} : getCharacteristicContainerStyle(resolvedColor.value),
);
</script>

<template>
    <div
        class="characteristic-boolean inline-flex min-w-0 transition-shadow"
        :class="compact ? 'items-center rounded px-1 py-0.5' : 'flex-col items-center rounded-box border border-base-content/15 px-2 py-1.5 text-base-content backdrop-blur-sm'"
        :style="compact ? {} : containerStyle"
    >
        <Tooltip v-if="(useInfoPanel || tooltipText) && compact" placement="top">
            <template #content>
                <CharacteristicInfoPanel v-if="useInfoPanel" :model="infoModel" tone="dark" />
                <span v-else class="text-xs text-white/90">{{ tooltipText }}</span>
            </template>
            <span
                class="flex items-center"
                :class="value ? '' : 'opacity-50'"
                :style="iconStyle"
            >
                <Icon
                    v-if="iconSource"
                    :source="iconSource"
                    :alt="alt"
                    size="xs"
                    :disabled="!value"
                />
            </span>
        </Tooltip>
        <template v-else-if="compact">
            <span
                class="flex items-center"
                :class="value ? '' : 'opacity-50'"
                :style="iconStyle"
            >
                <Icon
                    v-if="iconSource"
                    :source="iconSource"
                    :alt="alt"
                    size="xs"
                    :disabled="!value"
                />
            </span>
        </template>
        <template v-else>
            <Tooltip v-if="tooltipText" :content="tooltipText" placement="top">
                <span
                    class="flex items-center"
                    :class="value ? '' : 'opacity-50'"
                    :style="iconStyle"
                >
                    <Icon
                        v-if="iconSource"
                        :source="iconSource"
                        :alt="alt"
                        size="sm"
                        :disabled="!value"
                    />
                </span>
            </Tooltip>
            <span
                v-else
                class="flex items-center"
                :class="value ? '' : 'opacity-50'"
                :style="iconStyle"
            >
                <Icon
                    v-if="iconSource"
                    :source="iconSource"
                    :alt="alt"
                    size="sm"
                    :disabled="!value"
                />
            </span>
            <p class="mt-0.5 text-xs text-base-content/90">{{ label }}</p>
        </template>
    </div>
</template>
