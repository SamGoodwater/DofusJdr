<script setup>
/**
 * Modal « Barèmes » : outil générique multi-groupes.
 *
 * @props {Boolean} open
 * @emits close
 */
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import CharacteristicScalesTool from '@/Pages/Organismes/tools/CharacteristicScalesTool.vue';

defineProps({
    open: { type: Boolean, default: false },
    initialGroup: { type: String, default: 'all' },
    initialKey: { type: String, default: '' },
});

const emit = defineEmits(['close']);

function closeModal() {
    emit('close');
}
</script>

<template>
    <Modal
        :open="open"
        size="wide"
        variant="glass"
        placement="middle-center"
        close-on-esc
        @close="closeModal"
    >
        <template #header>
            <h3 class="text-lg font-bold flex items-center gap-2">
                <Icon source="fa-table" pack="solid" alt="" size="md" />
                Barèmes
            </h3>
        </template>

        <CharacteristicScalesTool
            v-if="open"
            fill-height
            :initial-group="initialGroup"
            :initial-key="initialKey"
        />

        <template #actions>
            <div class="flex gap-2 justify-end w-full">
                <Btn color="neutral" variant="ghost" @click="closeModal">
                    Fermer
                </Btn>
            </div>
        </template>
    </Modal>
</template>
