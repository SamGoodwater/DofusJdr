<script setup>
/**
 * Popover — panneau flottant ouvert au clic (Esc / clic extérieur pour fermer).
 *
 * @description
 * S'appuie sur OverlayTrigger (trigger=click, interactive). Destiné aux contenus riches
 * (décomposition de caractéristique, aide de syntaxe de formule).
 *
 * @example
 * <Popover placement="bottom-start">
 *   <template #default="{ open }"><button @click="open">?</button></template>
 *   <template #content><p>Aide…</p></template>
 * </Popover>
 */
import { computed, useSlots } from "vue";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";

defineOptions({ inheritAttrs: false });

const props = defineProps({
    content: { type: [String, Object, Function], default: "" },
    placement: { type: String, default: "bottom-start" },
    maxWidth: { type: String, default: "sm" },
    panelClass: {
        type: String,
        default: "rounded-box border border-base-300 bg-base-100 p-3 text-base-content shadow-lg",
    },
    closeOnOutside: { type: Boolean, default: true },
    closeOnEscape: { type: Boolean, default: true },
});

const emit = defineEmits(["open", "close"]);
const slots = useSlots();

const overlayContent = computed(() =>
    slots.content ? { component: { render: () => slots.content() } } : props.content,
);
</script>

<template>
    <OverlayTrigger
        :content="overlayContent"
        trigger="click"
        :placement="placement"
        :max-width="maxWidth"
        :interactive="true"
        :close-on-outside="closeOnOutside"
        :close-on-escape="closeOnEscape"
        :panel-class="panelClass"
        @open="emit('open')"
        @close="emit('close')"
    >
        <slot />
    </OverlayTrigger>
</template>
