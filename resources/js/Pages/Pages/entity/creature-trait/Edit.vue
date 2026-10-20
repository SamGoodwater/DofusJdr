<script setup>
/**
 * Page d'édition d'un trait de créature.
 *
 * @description
 * Édition sheet (header compact + formulaire descriptor-driven).
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { CreatureTrait } from '@/Models/Entity/CreatureTrait';
import { getCreatureTraitFieldDescriptors } from '@/Entities/creature-trait/creature-trait-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    creatureTrait: {
        type: Object,
        required: true,
    },
    /** True quand la page est chargée dans EntitySurfaceHost (modal). */
    embeddedInModal: {
        type: Boolean,
        default: false,
    },
});
const { isEmbeddedInModal, resolvedRedirectAfterUpdate } = useEntityEditEmbed(props);


const creatureTrait = computed(() => {
    const raw = props.creatureTrait || page.props.creatureTrait || {};
    return raw instanceof CreatureTrait ? raw : new CreatureTrait(raw);
});

const fieldsConfig = computed(() => {
    const ctx = { meta: { capabilities: { updateAny: true } } };
    return createFieldsConfigFromDescriptors(getCreatureTraitFieldDescriptors(ctx), ctx);
});

const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('creature-traits') || isAdmin.value);

setPageTitle(`Modifier le trait : ${creatureTrait.value.name || 'Sans nom'}`);
</script>

<template>
    <Head :title="`Modifier le trait : ${creatureTrait?.name || 'Sans nom'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="creature-traits"
            :entity="creatureTrait"
            :form-ref="entityEditFormRef"
            :title="creatureTrait.name || 'Trait sans nom'"
            list-route-name="entities.creature-traits.index"
            delete-route-name="entities.creature-traits.delete"
            route-param-key="creatureTrait"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer ce trait ? Il sera placé en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="creatureTrait"
            entity-type="creature-trait"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :show-state-toolbar="true"
            layout-profile="sheet"
            :hide-top-toolbar="true"
            :hide-action-dock="true"
            :fixed-footer-actions="false"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :embedded-in-modal="isEmbeddedInModal"
            :redirect-after-update="resolvedRedirectAfterUpdate"
        />
    </Container>
</template>
