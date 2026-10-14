<script setup>
/**
 * Compatibilité : l’ancien outil « Barèmes de sorts » délègue à l’outil générique
 * avec présélection du groupe sort.
 *
 * @props {Boolean} compact
 * @example
 * <SpellReferenceScales />
 */
import CharacteristicScalesTool from '@/Pages/Organismes/tools/CharacteristicScalesTool.vue';

defineProps({
    compact: { type: Boolean, default: false },
    initialKey: { type: String, default: 'dommages_spell' },
});
</script>

<template>
    <CharacteristicScalesTool
        :compact="compact"
        initial-group="spell"
        :initial-key="initialKey"
    />
</template>
