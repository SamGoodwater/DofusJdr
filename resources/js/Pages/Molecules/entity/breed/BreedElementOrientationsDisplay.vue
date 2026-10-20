<script setup>
/**
 * Affichage des 4 voix / orientations de classe.
 *
 * @props {Record<string, string|null|undefined>} orientationMap - air|earth|fire|water => orientation_key
 * @props {'icons'|'cards'} variant - icons = bandeau compact ; cards = fiche page/modal (valeur + voie)
 */
import { computed } from "vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import {
    BREED_ELEMENT_CARD_SURFACE_CLASS,
    BREED_ELEMENT_CHARACTERISTIC_LABELS,
    BREED_ELEMENT_KEYS,
    BREED_ELEMENT_LABELS,
    BREED_ELEMENT_PATH_LABELS,
    BREED_ELEMENT_PATH_TEXT_CLASS,
    BREED_ELEMENT_VOICE_ICON_PATH,
    breedOrientationIconUrl,
    breedOrientationLabel,
} from "@/Utils/entity/breedOrientations";

const props = defineProps({
    orientationMap: {
        type: Object,
        default: () => ({}),
    },
    /** Taille visuelle des pictos (variant icons) */
    size: {
        type: String,
        default: "sm",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
    /**
     * icons = compact (minimal / line) ;
     * cards = icône + valeur large + voie colorée (full / modal)
     */
    variant: {
        type: String,
        default: "icons",
        validator: (v) => ["icons", "cards"].includes(v),
    },
});

const sizeClass = {
    xs: "h-5 w-5",
    sm: "h-6 w-6",
    md: "h-8 w-8",
};

const slotClass = computed(() => sizeClass[props.size] || sizeClass.sm);

const orientationSlots = computed(() => {
    const m = props.orientationMap || {};
    return BREED_ELEMENT_KEYS.map((el) => {
        const orientationKey =
            m[el] != null && String(m[el]).trim() !== "" ? String(m[el]).trim() : null;
        return {
            element: el,
            label: BREED_ELEMENT_LABELS[el] || el,
            pathLabel: BREED_ELEMENT_PATH_LABELS[el] || el,
            characteristicLabel: BREED_ELEMENT_CHARACTERISTIC_LABELS[el] || "",
            voiceIcon: BREED_ELEMENT_VOICE_ICON_PATH[el],
            orientationKey,
            orientationLabel: breedOrientationLabel(orientationKey),
            orientationUrl: breedOrientationIconUrl(orientationKey),
            cardClass: BREED_ELEMENT_CARD_SURFACE_CLASS[el],
            pathTextClass: BREED_ELEMENT_PATH_TEXT_CLASS[el],
        };
    });
});
</script>

<template>
    <div
        v-if="variant === 'cards'"
        class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3"
        data-cy="breed-element-orientations"
        data-variant="cards"
    >
        <div
            v-for="s in orientationSlots"
            :key="s.element"
            class="flex min-w-0 flex-col items-center gap-1.5 rounded-xl px-2 py-3 text-center shadow-sm"
            :class="s.cardClass"
            :title="`${s.pathLabel} (${s.characteristicLabel}) — ${s.orientationLabel}`"
        >
            <Image
                v-if="s.orientationUrl"
                :source="s.orientationUrl"
                :alt="s.orientationLabel"
                fit="contain"
                rounded="sm"
                class="h-10 w-10 shrink-0 sm:h-12 sm:w-12"
            />
            <Image
                v-else
                :source="s.voiceIcon"
                :alt="s.label"
                fit="contain"
                class="h-10 w-10 shrink-0 opacity-80 sm:h-12 sm:w-12"
            />
            <span class="text-sm font-semibold leading-tight tracking-wide text-base-content sm:text-base">
                {{ s.orientationLabel }}
            </span>
            <span
                class="text-[0.65rem] font-medium uppercase tracking-wider sm:text-xs"
                :class="s.pathTextClass"
            >
                {{ s.pathLabel }}
            </span>
        </div>
    </div>

    <div
        v-else
        class="flex flex-wrap items-center gap-1.5"
        data-cy="breed-element-orientations"
        data-variant="icons"
    >
        <Tooltip
            v-for="s in orientationSlots"
            :key="s.element"
            :content="
                s.orientationKey
                    ? `${s.pathLabel} — ${s.orientationLabel}`
                    : `${s.pathLabel} — orientation non définie`
            "
            placement="top"
        >
            <span
                class="inline-flex items-center gap-0.5 rounded-md border border-base-300/60 bg-base-200/40 px-1 py-0.5"
            >
                <Image
                    :source="s.voiceIcon"
                    :alt="s.label"
                    fit="contain"
                    :class="[slotClass, 'shrink-0']"
                />
                <Image
                    v-if="s.orientationUrl"
                    :source="s.orientationUrl"
                    :alt="s.orientationKey || ''"
                    fit="contain"
                    rounded="sm"
                    :class="[slotClass, 'shrink-0']"
                />
                <span
                    v-else
                    :class="slotClass"
                    class="inline-flex items-center justify-center rounded-sm bg-base-300/50 text-[10px] text-base-content/50"
                >
                    —
                </span>
            </span>
        </Tooltip>
    </div>
</template>
