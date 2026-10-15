<script setup>
/**
 * Rendu public d'une section CharacteristicNorms.
 * Affiche le panneau MJ unifié (barème + conversion + propriétés).
 */
import { computed } from 'vue';
import CharacteristicGmPanel from '@/Pages/Organismes/data-display/CharacteristicGmPanel.vue';

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const characteristicKey = computed(() => props.settings?.characteristic_key || '');
const entity = computed(() => props.settings?.entity || '*');
const group = computed(() => props.settings?.group || '');
</script>

<template>
    <div class="section-characteristic-norms">
        <div v-if="!characteristicKey" class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation" />
            <span>Aucune caractéristique configurée.</span>
        </div>
        <CharacteristicGmPanel
            v-else
            :characteristic-key="characteristicKey"
            :group="group"
            :entity="entity"
            :tabs="['bareme', 'conversion', 'proprietes']"
        />
    </div>
</template>
