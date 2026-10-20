<script setup>
/**
 * Page d'édition d'un état (Condition).
 *
 * @description
 * Édition sheet (header compact + formulaire descriptor-driven).
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Condition } from '@/Models/Entity/Condition';
import { getConditionFieldDescriptors } from '@/Entities/condition/condition-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    condition: {
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


const condition = computed(() => {
    const raw = props.condition || page.props.condition || {};
    return raw instanceof Condition ? raw : new Condition(raw);
});

const fieldsConfig = computed(() => {
    const ctx = { meta: { capabilities: { updateAny: true } } };
    return createFieldsConfigFromDescriptors(getConditionFieldDescriptors(ctx), ctx);
});

const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('conditions') || isAdmin.value);

setPageTitle(`Modifier l'état : ${condition.value.name || 'Sans nom'}`);
</script>

<template>
    <Head :title="`Modifier l'état : ${condition?.name || 'Sans nom'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="conditions"
            :entity="condition"
            :form-ref="entityEditFormRef"
            :title="condition.name || 'État sans nom'"
            list-route-name="entities.conditions.index"
            delete-route-name="entities.conditions.delete"
            route-param-key="condition"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer cet état ? Il sera placé en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="condition"
            entity-type="condition"
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
