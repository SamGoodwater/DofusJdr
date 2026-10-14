<script setup>
/**
 * CharacteristicProperty — affichage unifié d'une caractéristique (tooltip riche unique).
 *
 * @description
 * S’appuie sur le view model (`useCharacteristicViewModel` ou `viewModel` pré-calculé).
 * Variantes : densité (full / short / icon-only), habillage badge (none / solid / outline), layout (inline / card).
 *
 * @props {Object} [viewModel] — Si fourni, utilisé tel quel (prioritaire sur le mode entité).
 * @props {string} [fieldKey] — Mode entité : clé champ
 * @props {Object} [entity]
 * @props {string} [entityType]
 * @props {Object} [descriptors]
 * @props {Object} [tableMeta]
 * @props {Object|null} [runtime] — ex. payload resolved-stats racine `{ computed: { [key]: {...} } }`
 * @props {Array} [levelTable]
 * @props {string} [formulaResolved]
 * @props {string} [formulaRaw]
 * @props {string} density — full | short | icon-only
 * @props {string} badge — none | solid | outline
 * @props {string} layout — inline | card
 * @props {boolean} [showValue] — Afficher la valeur dans le déclencheur (désactiver pour l’ancien variant `icon` de PropertyDisplay)
 * @props {boolean} [showLabel] — Afficher le libellé court / complet (Cat., Types, etc.)
 * @props {boolean} [showIcon] — Afficher l’icône caractéristique (désactiver si une icône externe la remplace)
 * @props {boolean} [wrapValue] — Valeur multiligne (description, textarea) : pas de truncate
 * @props {string} size — xs | sm | md
 *
 * @example
 * <CharacteristicProperty :view-model="vm" density="short" layout="inline" badge="none" />
 * <CharacteristicProperty field-key="pa" :entity="monster" entity-type="monster" density="full" layout="card" />
 */
import { computed } from "vue";
import { colord } from "colord";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Popover from "@/Pages/Atoms/feedback/Popover.vue";
import CharacteristicInfoPanel from "@/Pages/Molecules/data-display/CharacteristicInfoPanel.vue";
import { colorList } from "@/Pages/Atoms/atomMap";
import { useCharacteristicViewModel } from "@/Composables/entity/useCharacteristicViewModel";
import {
    getCharacteristicColorStyle,
    getCharacteristicContainerStyle,
    shouldHideCharacteristicLine,
} from "@/Composables/entity/useCharacteristicDisplay";
import {
    CHARACTERISTIC_PROPERTY_BADGE,
    CHARACTERISTIC_PROPERTY_DENSITY,
    CHARACTERISTIC_PROPERTY_LAYOUT,
} from "@/Utils/Entity/Constants";
import { TAILWIND_CHARACTERISTIC_PALETTES } from "@/Constants/tailwindCharacteristicPalettes";

const props = defineProps({
    viewModel: {
        type: Object,
        default: null,
    },
    fieldKey: { type: String, default: "" },
    entity: { type: Object, default: null },
    entityType: { type: String, default: "" },
    descriptors: { type: Object, default: () => ({}) },
    tableMeta: { type: Object, default: () => ({}) },
    runtime: { type: Object, default: null },
    levelTable: { type: Array, default: () => [] },
    formulaResolved: { type: String, default: "" },
    formulaRaw: { type: String, default: "" },
    density: {
        type: String,
        default: CHARACTERISTIC_PROPERTY_DENSITY.full,
        validator: (v) => Object.values(CHARACTERISTIC_PROPERTY_DENSITY).includes(v),
    },
    badge: {
        type: String,
        default: CHARACTERISTIC_PROPERTY_BADGE.none,
        validator: (v) => Object.values(CHARACTERISTIC_PROPERTY_BADGE).includes(v),
    },
    layout: {
        type: String,
        default: CHARACTERISTIC_PROPERTY_LAYOUT.inline,
        validator: (v) => Object.values(CHARACTERISTIC_PROPERTY_LAYOUT).includes(v),
    },
    showValue: { type: Boolean, default: true },
    showLabel: { type: Boolean, default: true },
    showIcon: { type: Boolean, default: true },
    /** Si true, la valeur s’affiche en entier (retour à la ligne) au lieu d’être tronquée. */
    wrapValue: { type: Boolean, default: false },
    size: {
        type: String,
        default: "sm",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
    /** Chemin MediaManager (ex. icons/caracteristics/cac.webp) : remplace le libellé texte court / long. */
    labelImageSource: { type: String, default: "" },
    labelImageAlt: { type: String, default: "" },
    /** Classes Tailwind pour la valeur (ex. text-red-600) ; désactive la couleur carac. sur la valeur. */
    valueTextClass: { type: String, default: "" },
    /** Si true et que le viewModel porte une décomposition, ouvre un Popover au clic plutôt qu'un Tooltip. */
    preferDecompositionPopover: { type: Boolean, default: false },
});

const entityOpts = computed(() => ({
    fieldKey: props.fieldKey,
    entity: props.entity,
    entityType: props.entityType,
    descriptors: props.descriptors,
    tableMeta: props.tableMeta,
    runtime: props.runtime,
    levelTable: props.levelTable,
    formulaResolved: props.formulaResolved,
    formulaRaw: props.formulaRaw,
}));

const hasEntityBinding = computed(
    () => Boolean(props.fieldKey && props.entity && props.entityType),
);

const { viewModel: vmFromEntity } = useCharacteristicViewModel(entityOpts);

const model = computed(() => {
    if (props.viewModel && typeof props.viewModel === "object") {
        return props.viewModel;
    }
    if (hasEntityBinding.value) {
        return vmFromEntity.value;
    }
    return {};
});

/** Masquage fiche joueur : définition `hide_when_empty` + valeur vide (voir {@link shouldHideCharacteristicLine}). */
const hiddenWhenEmpty = computed(() => {
    const m = model.value;
    if (m?.hiddenWhenEmpty != null) {
        return m.hiddenWhenEmpty;
    }
    return shouldHideCharacteristicLine(
        {
            hide_when_empty: m?.hideWhenEmpty,
            hide_when_false: m?.hideWhenFalse,
            type: m?.characteristicType ?? "",
        },
        m?.rawValue,
    );
});

const displayText = computed(() => {
    const d = model.value?.displayValue;
    if (d === undefined || d === null || d === "") return "—";
    return String(d);
});

const valueStyle = computed(() => getCharacteristicColorStyle(model.value?.color) ?? {});

/** Couleur issue de la carac. : masquée si `valueTextClass` impose le style (ex. CàC en rouge). */
const valueColorStyle = computed(() => (props.valueTextClass ? {} : valueStyle.value));

const labelImagePx = computed(() => {
    const map = { xs: "14", sm: "16", md: "18" };
    return map[props.size] ?? "16";
});
const containerStyle = computed(() =>
    props.layout === CHARACTERISTIC_PROPERTY_LAYOUT.card
        ? getCharacteristicContainerStyle(model.value?.color) ?? {}
        : {},
);

const effectiveBadgeColor = computed(() => {
    const c = model.value?.color;
    if (!c || typeof c !== "string") return "neutral";
    const s = c.trim();
    const low = s.toLowerCase();
    if (TAILWIND_CHARACTERISTIC_PALETTES.includes(low) && !s.includes("-")) {
        return `${low}-400`;
    }
    return s;
});

/** Couleurs Daisy uniquement pour la prop `color` du Tooltip. */
const characteristicTooltipColor = computed(() => {
    const c = model.value?.color;
    if (!c || typeof c !== "string") return "";
    const s = c.trim();
    return s !== "" && colorList.includes(s) ? s : "";
});

/**
 * Hex / rgb / oklch / tokens Tailwind-Daisy (`amber-700`) → `--color` pour le box-shadow du tooltip.
 * Aligné sur {@link getCharacteristicColorStyle} : les chips élément (vue minimal) n’ont pas de hex.
 */
const characteristicTooltipAccentStyle = computed(() => {
    const c = model.value?.color;
    if (!c || typeof c !== "string") return {};
    const s = c.trim();
    if (s === "" || colorList.includes(s)) return {};
    if (s.startsWith("#")) {
        const co = colord(s);
        if (!co.isValid()) return {};
        return { "--color": co.toHex() };
    }
    if (s.includes("-")) {
        return { "--color": `var(--color-${s})` };
    }
    const low = s.toLowerCase();
    if (TAILWIND_CHARACTERISTIC_PALETTES.includes(low)) {
        return { "--color": `var(--color-${low}-400)` };
    }
    const co = colord(s);
    if (co.isValid()) return { "--color": co.toHex() };
    return {};
});

/** Clé BDD (ex. `life_points_creature`) → classe `.color-{key}` (CSS généré depuis la BDD). */
const characteristicTooltipAccentClass = computed(() => {
    const c = model.value?.color;
    if (!c || typeof c !== "string") return "";
    const s = c.trim();
    if (s === "" || colorList.includes(s)) return "";
    if (characteristicTooltipAccentStyle.value && characteristicTooltipAccentStyle.value["--color"]) {
        return "";
    }
    if (!/^[a-z][a-z0-9_]*$/i.test(s)) return "";
    return `color-${s}`;
});

const badgeSize = computed(() => {
    const map = { xs: "xs", sm: "sm", md: "md" };
    return map[props.size] ?? "sm";
});

const iconSize = computed(() => {
    const map = { xs: "xs", sm: "xs", md: "sm" };
    return map[props.size] ?? "xs";
});

const textSizeClass = computed(() => {
    const map = { xs: "text-xs", sm: "text-sm", md: "text-base" };
    return map[props.size] ?? "text-sm";
});

const isCard = computed(() => props.layout === CHARACTERISTIC_PROPERTY_LAYOUT.card);
const isShort = computed(() => props.density === CHARACTERISTIC_PROPERTY_DENSITY.short);
const isIconOnly = computed(() => props.density === CHARACTERISTIC_PROPERTY_DENSITY.iconOnly);
const useBadge = computed(
    () =>
        props.badge === CHARACTERISTIC_PROPERTY_BADGE.solid ||
        props.badge === CHARACTERISTIC_PROPERTY_BADGE.outline,
);
const badgeVariant = computed(() =>
    props.badge === CHARACTERISTIC_PROPERTY_BADGE.outline ? "outline" : "soft",
);

/** Popover au clic (densités labeled/spacious) ; sinon Tooltip au survol (densité icon). */
const usePinnedPopover = computed(() => Boolean(props.preferDecompositionPopover));

/** Classes valeur : truncate par défaut ; wrap pour textes longs (description…). */
const valueOverflowClass = computed(() =>
    props.wrapValue ? "min-w-0 whitespace-normal break-words" : "truncate",
);
</script>

<template>
    <template v-if="!hiddenWhenEmpty">
    <component
        :is="usePinnedPopover ? Popover : Tooltip"
        :placement="usePinnedPopover ? 'bottom-start' : 'top'"
        class="inline-flex max-w-full min-w-0"
        :class="wrapValue && 'w-full'"
        :color="usePinnedPopover ? undefined : characteristicTooltipColor"
        :accent-class="usePinnedPopover ? undefined : characteristicTooltipAccentClass"
        :accent-style="usePinnedPopover ? undefined : characteristicTooltipAccentStyle"
    >
        <template #content>
            <CharacteristicInfoPanel
                :model="model"
                :tone="usePinnedPopover ? 'light' : 'dark'"
            />
        </template>

        <!-- Badge -->
        <Badge
            v-if="useBadge"
            :color="effectiveBadgeColor"
            :size="badgeSize"
            :variant="badgeVariant"
            class="inline-flex max-w-full min-w-0 items-center gap-1 text-base-content"
            :truncate="!wrapValue"
        >
            <Icon
                v-if="showIcon && model.icon"
                :source="model.icon"
                :alt="model.name || ''"
                :size="iconSize"
                class="shrink-0 opacity-95"
            />
            <span
                v-if="showValue"
                class="font-medium"
                :class="[valueOverflowClass, valueTextClass]"
                :style="valueColorStyle"
            >{{ displayText }}</span>
        </Badge>

        <!-- Carte (hors badge) -->
        <div
            v-else-if="isCard"
            class="characteristic-property text-base-content inline-block min-w-0 rounded-box border border-base-content/15 px-2.5 py-2 backdrop-blur-sm transition-shadow"
            :class="wrapValue && 'w-full'"
            :style="containerStyle"
        >
            <div class="flex items-start justify-between gap-2">
                <span
                    class="font-medium"
                    :class="[valueOverflowClass, valueTextClass]"
                    :style="valueColorStyle"
                >{{ displayText }}</span>
                <Icon
                    v-if="showIcon && model.icon"
                    :source="model.icon"
                    :alt="model.name || ''"
                    :size="iconSize"
                    class="shrink-0 opacity-90"
                    :style="valueStyle"
                />
            </div>
            <p v-if="showLabel && model.name" class="mt-0.5 text-xs text-base-content/90">{{ model.name }}</p>
        </div>

        <!-- Inline -->
        <span
            v-else
            class="characteristic-property text-base-content inline-flex max-w-full min-w-0 gap-1"
            :class="[textSizeClass, wrapValue ? 'w-full flex-wrap items-start' : 'items-center']"
        >
            <Icon
                v-if="showIcon && model.icon"
                :source="model.icon"
                :alt="model.name || ''"
                :size="iconSize"
                class="shrink-0 opacity-90"
                :style="valueStyle"
            />
            <template v-if="isShort">
                <Image
                    v-if="showLabel && labelImageSource"
                    :source="labelImageSource"
                    :alt="labelImageAlt || model.shortName || model.name || ''"
                    :width="labelImagePx"
                    :height="labelImagePx"
                    fit="contain"
                    class="inline-block shrink-0 opacity-95"
                />
                <span v-else-if="showLabel && model.shortName" class="shrink-0 text-base-content/90">
                    {{ showValue ? `${model.shortName}:` : model.shortName }}
                </span>
                <span
                    v-if="showValue"
                    class="font-medium"
                    :class="[valueOverflowClass, valueTextClass]"
                    :style="valueColorStyle"
                >{{ displayText }}</span>
            </template>
            <template v-else-if="isIconOnly">
                <span
                    v-if="showValue"
                    class="font-medium"
                    :class="[valueOverflowClass, valueTextClass]"
                    :style="valueColorStyle"
                >{{ displayText }}</span>
            </template>
            <template v-else>
                <Image
                    v-if="showLabel && labelImageSource"
                    :source="labelImageSource"
                    :alt="labelImageAlt || model.name || ''"
                    :width="labelImagePx"
                    :height="labelImagePx"
                    fit="contain"
                    class="inline-block shrink-0 opacity-95"
                />
                <span v-else-if="showLabel && model.name" class="shrink-0 text-base-content/90">
                    {{ showValue ? `${model.name}:` : model.name }}
                </span>
                <span
                    v-if="showValue"
                    class="font-medium"
                    :class="[valueOverflowClass, valueTextClass]"
                    :style="valueColorStyle"
                >{{ displayText }}</span>
            </template>
        </span>
    </component>
    </template>
</template>
