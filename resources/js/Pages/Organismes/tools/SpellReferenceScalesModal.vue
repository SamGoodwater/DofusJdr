<script setup>
/**
 * SpellReferenceScalesModal Organism
 *
 * @description
 * Modal « Barèmes de sorts » : encapsule SpellReferenceScales pour un accès
 * global depuis le menu Outils (comme le lanceur de dés).
 *
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @emits close - Fermeture du modal
 *
 * @example
 * <SpellReferenceScalesModal :open="open" @close="open = false" />
 */
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import SpellReferenceScales from '@/Pages/Organismes/tools/SpellReferenceScales.vue';

defineProps({
    open: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

function closeModal() {
    emit('close');
}
</script>

<template>
    <Modal
        :open="open"
        size="xl"
        variant="glass"
        placement="middle-center"
        close-on-esc
        @close="closeModal"
    >
        <template #header>
            <h3 class="text-lg font-bold flex items-center gap-2">
                <Icon source="fa-table" pack="solid" alt="" size="md" />
                Barèmes de sorts
            </h3>
        </template>

        <SpellReferenceScales v-if="open" />

        <template #actions>
            <div class="flex gap-2 justify-end w-full">
                <Btn
                    color="neutral"
                    variant="ghost"
                    @click="closeModal"
                >
                    Fermer
                </Btn>
            </div>
        </template>
    </Modal>
</template>
