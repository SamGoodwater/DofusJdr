<script setup>
/**
 * Lecture publique d’une section Barèmes (présélection sort possible).
 */
import { ref } from 'vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import CharacteristicScalesTool from '@/Pages/Organismes/tools/CharacteristicScalesTool.vue';
import SpellReferenceScalesModal from '@/Pages/Organismes/tools/SpellReferenceScalesModal.vue';

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const modalOpen = ref(false);
</script>

<template>
    <div class="space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm text-base-content/70 leading-snug">
                Barèmes indicatifs (normes des caractéristiques).
                Aussi disponible dans <strong>Outils → Barèmes</strong>.
            </p>
            <Btn size="sm" variant="ghost" @click="modalOpen = true">
                Agrandir
            </Btn>
        </div>

        <CharacteristicScalesTool
            :compact="Boolean(props.settings?.compact)"
            :initial-group="props.settings?.group || 'spell'"
            :initial-key="props.settings?.characteristic_key || ''"
        />

        <SpellReferenceScalesModal
            :open="modalOpen"
            :initial-group="props.settings?.group || 'spell'"
            :initial-key="props.settings?.characteristic_key || ''"
            @close="modalOpen = false"
        />
    </div>
</template>
