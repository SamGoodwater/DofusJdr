<script setup>
/**
 * Resource Edit Page
 *
 * @description
 * Édition sheet (header compact + grille 2 colonnes) + sous-éditeurs en containers.
 *
 * @props {Object|null} resource - Données de la ressource (nullable en création via page /create)
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Resource } from '@/Models/Entity/Resource';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import ObjectEffectsManager from '@/Pages/Organismes/entity/ObjectEffectsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import { RESOURCE_FORM_FIELD_SECTIONS_EDIT } from '@/Entities/resource/resource-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    resource: {
        type: Object,
        default: null,
    },
    resourceTypes: {
        type: Array,
        default: () => [],
    },
    availableShops: {
        type: Array,
        default: () => [],
    },
    availableScenarios: {
        type: Array,
        default: () => [],
    },
    availableCampaigns: {
        type: Array,
        default: () => [],
    },
    availableResourcesForRecipe: {
        type: Array,
        default: () => [],
    },
    objectEffects: { type: Array, default: () => [] },
    objectEffectCharacteristics: { type: Array, default: () => [] },
    objectEffectMonsters: { type: Array, default: () => [] },
    /** True quand la page est chargée dans EntitySurfaceHost (modal). */
    embeddedInModal: {
        type: Boolean,
        default: false,
    },
});
const { isEmbeddedInModal, resolvedRedirectAfterUpdate } = useEntityEditEmbed(props);


const ctx = computed(() => ({
    resourceTypes: props.resourceTypes,
    capabilities: page.props.auth?.user?.can || {},
    meta: {
        resourceTypes: props.resourceTypes,
        capabilities: page.props.auth?.user?.can || {},
    },
}));

const fieldsConfig = computed(() => {
    const descriptors = getResourceFieldDescriptors(ctx.value);
    return createFieldsConfigFromDescriptors(descriptors, ctx.value);
});

const fieldSections = RESOURCE_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('resources') || isAdmin.value);

const resource = computed(() => {
    const resourceData = props.resource || page.props.resource || {};
    return new Resource(resourceData || {});
});

setPageTitle(`Modifier la ressource : ${resource.value.name || 'Nouvelle ressource'}`);
</script>

<template>
    <Head :title="`Modifier la ressource : ${resource?.name || 'Nouvelle ressource'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="resources"
            :entity="resource"
            :form-ref="entityEditFormRef"
            :title="resource.name || 'Ressource sans nom'"
            list-route-name="entities.resources.index"
            delete-route-name="entities.resources.delete"
            route-param-key="resource"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer cette ressource ? Elle sera placée en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="resource"
            entity-type="resource"
            :fields-config="fieldsConfig"
            :is-updating="!!resource?.id"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            layout-profile="sheet"
            :hide-top-toolbar="true"
            :hide-action-dock="true"
            :fixed-footer-actions="false"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :compact-access-levels="true"
            :embedded-in-modal="isEmbeddedInModal"
            :redirect-after-update="resolvedRedirectAfterUpdate"
        >
            <template #after-sections>
                <EntityEditContainer
                    v-if="resource?.id"
                    title="Effets d’objet"
                    icon="fa-solid fa-wand-magic-sparkles"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="mt-3 lg:col-span-2"
                >
                    <ObjectEffectsManager
                        :object-effects="objectEffects"
                        :object-effect-characteristics="objectEffectCharacteristics"
                        :object-effect-monsters="objectEffectMonsters"
                        entity-type="resource"
                        :entity-id="resource.id"
                    />
                </EntityEditContainer>

                <EntityEditContainer
                    v-if="resource?.id"
                    title="Recette (ingrédients)"
                    subtitle="Ressources pour fabriquer cette ressource."
                    icon="fa-solid fa-hammer"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <EntityRelationsManager
                        :relations="resource.recipeIngredients || []"
                        :available-items="availableResourcesForRecipe"
                        :entity-id="resource.id"
                        entity-type="resources"
                        relation-type="recipe"
                        relation-name="Recette (ingrédients pour fabriquer cette ressource)"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            pivotFields: ['quantity'],
                            itemLabel: 'ressource',
                            itemLabelPlural: 'ressources',
                        }"
                    />
                </EntityEditContainer>

                <EntityEditContainer
                    title="Hôtels de vente"
                    icon="fa-solid fa-store"
                    :span="2"
                    collapsible
                    :default-open="false"
                    :root-class="resource?.id ? 'lg:col-span-2' : 'mt-3 lg:col-span-2'"
                >
                    <EntityRelationsManager
                        :relations="resource.shops || []"
                        :available-items="availableShops"
                        :entity-id="resource.id"
                        entity-type="resources"
                        relation-type="shops"
                        relation-name="hotels de vente associées"
                        :config="{
                            displayFields: ['name', 'description'],
                            searchFields: ['name', 'description'],
                            pivotFields: ['quantity', 'price', 'comment'],
                            itemLabel: 'hotel de vente',
                            itemLabelPlural: 'hotels de vente',
                        }"
                    />
                </EntityEditContainer>

                <EntityEditContainer
                    title="Scénarios & campagnes"
                    icon="fa-solid fa-map"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <div class="space-y-4">
                        <EntityRelationsManager
                            :relations="resource.scenarios || []"
                            :available-items="availableScenarios"
                            :entity-id="resource.id"
                            entity-type="resources"
                            relation-type="scenarios"
                            relation-name="Scénarios associés"
                            :config="{
                                displayFields: ['name', 'description'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'scénario',
                                itemLabelPlural: 'scénarios',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="resource.campaigns || []"
                            :available-items="availableCampaigns"
                            :entity-id="resource.id"
                            entity-type="resources"
                            relation-type="campaigns"
                            relation-name="Campagnes associées"
                            :config="{
                                displayFields: ['name', 'description'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'campagne',
                                itemLabelPlural: 'campagnes',
                            }"
                        />
                    </div>
                </EntityEditContainer>
            </template>
        </EntityEditForm>
    </Container>
</template>
