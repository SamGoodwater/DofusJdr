<script setup>
/**
 * Re-provide le contexte d’édition embarquée **à l’intérieur** du Modal (Teleport).
 *
 * @description
 * Garantit que `inject(ENTITY_EDIT_EMBED_KEY)` fonctionne pour la page Edit chargée
 * en modal, indépendamment du Teleport vers `body`.
 */
import { provide, reactive, watch } from "vue";
import { ENTITY_EDIT_EMBED_KEY } from "@/Composables/entity/useEntityEditEmbed";

const props = defineProps({
    onCancel: { type: Function, required: true },
    onSaved: { type: Function, required: true },
    onExpand: { type: Function, required: true },
});

const embedState = reactive({
    embeddedInModal: true,
    onCancel: props.onCancel,
    onSaved: props.onSaved,
    onExpand: props.onExpand,
});

watch(
    () => [props.onCancel, props.onSaved, props.onExpand],
    ([onCancel, onSaved, onExpand]) => {
        embedState.onCancel = onCancel;
        embedState.onSaved = onSaved;
        embedState.onExpand = onExpand;
    },
);

provide(ENTITY_EDIT_EMBED_KEY, embedState);
</script>

<template>
    <slot />
</template>
