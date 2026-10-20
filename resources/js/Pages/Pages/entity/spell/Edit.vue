<script setup>
/**
 * Page d’édition d’un sort — formulaire structuré par sections (généralités, portée/coût/résolution, spécificités, admin).
 *
 * @props {Object} spell - Données du sort à éditer
 */
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { Spell } from '@/Models/Entity/Spell';
import SpellEditFormContent from '@/Pages/Organismes/entity/SpellEditFormContent.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    spell: {
        type: Object,
        required: true,
    },
    availableSpellTypes: {
        type: Array,
        default: () => [],
    },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: 'spell' },
    effectFormOptions: { type: Object, default: () => ({}) },
    spellDegrees: { type: Object, default: () => ({ degrees: [], default_degree_id: null }) },
    spellEffectGroups: { type: Array, default: () => [] },
    spellHolders: { type: Object, default: () => ({}) },
    /** True quand la page est chargée dans EntitySurfaceHost (modal). */
    embeddedInModal: {
        type: Boolean,
        default: false,
    },
});
const { isEmbeddedInModal, resolvedRedirectAfterUpdate } = useEntityEditEmbed(props);


const spell = computed(() => {
    const spellData = props.spell || page.props.spell || {};
    return new Spell(spellData);
});

setPageTitle(`Modifier le sort : ${spell.value.name || 'Nouveau sort'}`);
</script>

<template>
    <Head :title="`Modifier le sort : ${spell?.name || 'Nouveau sort'}`" />

    <Container class="space-y-6 pb-32 md:pb-36">
        <SpellEditFormContent
            :spell="spell"
            :available-spell-types="availableSpellTypes"
            :available-effects="availableEffects"
            :effect-entity-type="effectEntityType"
            :effect-form-options="effectFormOptions"
            :spell-degrees="spellDegrees"
            :spell-effect-groups="spellEffectGroups"
            :spell-holders="spellHolders"
            :embedded-in-modal="isEmbeddedInModal"
            :redirect-after-update="resolvedRedirectAfterUpdate"
        />
    </Container>
</template>
