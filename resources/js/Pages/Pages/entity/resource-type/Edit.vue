<script setup>
/**
 * Page d'édition d'un type de ressource.
 *
 * @description
 * Édition sheet (header compact + formulaire descriptor-driven).
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { ResourceType } from '@/Models/Entity/ResourceType';
import { getResourceTypeFieldDescriptors } from '@/Entities/resource-type/resource-type-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    resourceType: {
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


const resourceType = computed(() => {
    const raw = props.resourceType || page.props.resourceType || {};
    return raw instanceof ResourceType ? raw : new ResourceType(raw);
});

const fieldsConfig = computed(() => {
    const ctx = { meta: { capabilities: { updateAny: true } } };
    return createFieldsConfigFromDescriptors(getResourceTypeFieldDescriptors(ctx), ctx);
});

const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('resource-types') || isAdmin.value);

setPageTitle(`Modifier le type de ressource : ${resourceType.value.name || 'Sans nom'}`);
</script>

<template>
    <Head :title="`Modifier le type de ressource : ${resourceType?.name || 'Sans nom'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="resource-types"
            :entity="resourceType"
            :form-ref="entityEditFormRef"
            :title="resourceType.name || 'Type sans nom'"
            list-route-name="entities.resource-types.index"
            delete-route-name="entities.resource-types.delete"
            route-param-key="resourceType"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer ce type de ressource ? Il sera placé en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="resourceType"
            entity-type="resource-type"
            :fields-config="fieldsConfig"
            route-name-base="entities.resource-types"
            route-param-key="resourceType"
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
