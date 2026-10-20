<script setup>
/**
 * Page d’édition sheet d’une spécialisation.
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Specialization } from '@/Models/Entity/Specialization';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import CreatureTraitsEditor from '@/Pages/Organismes/entity/CreatureTraitsEditor.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import { getSpecializationFieldDescriptors } from '@/Entities/specialization/specialization-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import { SPECIALIZATION_FORM_FIELD_SECTIONS_EDIT } from '@/Entities/specialization/specialization-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    specialization: { type: Object, required: true },
    availableSpells: { type: Array, default: () => [] },
    availableCapabilities: { type: Array, default: () => [] },
    availableCreatureTraits: { type: Array, default: () => [] },
    availableConsumables: { type: Array, default: () => [] },
    availableResources: { type: Array, default: () => [] },
    availableItems: { type: Array, default: () => [] },
    availableSections: { type: Array, default: () => [] },
    /** True quand la page est chargée dans EntitySurfaceHost (modal). */
    embeddedInModal: {
        type: Boolean,
        default: false,
    },
});
const { isEmbeddedInModal, resolvedRedirectAfterUpdate } = useEntityEditEmbed(props);


const specialization = computed(() => {
    const raw = props.specialization || page.props.specialization || {};
    return raw instanceof Specialization ? raw : new Specialization(raw);
});

setPageTitle(`Modifier la spécialisation : ${specialization.value.name || '-'}`);

const fieldsConfig = computed(() => {
    const ctx = {
        capabilities: { updateAny: true, createAny: false },
        meta: { capabilities: { updateAny: true } },
    };
    return createFieldsConfigFromDescriptors(getSpecializationFieldDescriptors(ctx), ctx);
});

const fieldSections = SPECIALIZATION_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('specializations') || isAdmin.value);
</script>

<template>
    <Head :title="`Modifier : ${specialization?.name || 'Spécialisation'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="specializations"
            :entity="specialization"
            :form-ref="entityEditFormRef"
            :title="specialization.name || 'Spécialisation sans nom'"
            list-route-name="entities.specializations.index"
            delete-route-name="entities.specializations.delete"
            route-param-key="specialization"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer cette spécialisation ? Elle sera placée en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="specialization"
            entity-type="specialization"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            layout-profile="sheet"
            :hide-top-toolbar="true"
            :hide-action-dock="true"
            :fixed-footer-actions="false"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :footer-secondary-actions="false"
            :compact-access-levels="true"
            :embedded-in-modal="isEmbeddedInModal"
            :redirect-after-update="resolvedRedirectAfterUpdate"
        >
            <template #after-sections>
                <EntityEditContainer
                    v-if="specialization.id"
                    title="Sorts & capacités"
                    icon="fa-solid fa-wand-sparkles"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="mt-3 lg:col-span-2"
                >
                    <div class="space-y-4">
                        <EntityRelationsManager
                            :relations="specialization.spells || []"
                            :available-items="availableSpells"
                            :entity-id="specialization.id"
                            entity-type="specializations"
                            relation-type="spells"
                            relation-name="Sorts"
                            :config="{
                                itemLabel: 'sort',
                                itemLabelPlural: 'sorts',
                                displayFields: ['name'],
                                searchFields: ['name'],
                                routeName: 'entities.specializations.updateSpells',
                                relatedEntityType: 'spells',
                                searchApiEntityType: 'spells',
                                pivotFields: ['level'],
                            }"
                        />

                        <EntityRelationsManager
                            :relations="specialization.capabilities || []"
                            :available-items="availableCapabilities"
                            :entity-id="specialization.id"
                            entity-type="specializations"
                            relation-type="capabilities"
                            relation-name="Capacités"
                            :config="{
                                itemLabel: 'capacité',
                                itemLabelPlural: 'capacités',
                                displayFields: ['name'],
                                searchFields: ['name'],
                                routeName: 'entities.specializations.updateCapabilities',
                                relatedEntityType: 'capabilities',
                                searchApiEntityType: 'capabilities',
                                pivotFields: ['level'],
                            }"
                        />
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    v-if="specialization.id"
                    title="Traits"
                    icon="fa-solid fa-star"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <CreatureTraitsEditor
                        :relations="specialization.creatureTraits || []"
                        :available-items="availableCreatureTraits"
                        :entity-id="specialization.id"
                        route-name="entities.specializations.updateCreatureTraits"
                        route-param-name="specialization"
                        title="Traits de spécialisation"
                        help="Traits permanents gagnés via cette spécialisation. Le niveau indique quand le trait devient actif."
                        with-level
                        search-api-entity-type="creature-traits"
                    />
                </EntityEditContainer>

                <EntityEditContainer
                    v-if="specialization.id"
                    title="Objets, conso & ressources"
                    icon="fa-solid fa-boxes-stacked"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <div class="space-y-4">
                        <EntityRelationsManager
                            :relations="specialization.consumables || []"
                            :available-items="availableConsumables"
                            :entity-id="specialization.id"
                            entity-type="specializations"
                            relation-type="consumables"
                            relation-name="Consommables"
                            :config="{
                                itemLabel: 'consommable',
                                itemLabelPlural: 'consommables',
                                displayFields: ['name'],
                                searchFields: ['name'],
                                routeName: 'entities.specializations.updateConsumables',
                                relatedEntityType: 'consumables',
                                searchApiEntityType: 'consumables',
                                pivotFields: ['level', 'quantity'],
                            }"
                        />

                        <EntityRelationsManager
                            :relations="specialization.resources || []"
                            :available-items="availableResources"
                            :entity-id="specialization.id"
                            entity-type="specializations"
                            relation-type="resources"
                            relation-name="Ressources"
                            :config="{
                                itemLabel: 'ressource',
                                itemLabelPlural: 'ressources',
                                displayFields: ['name'],
                                searchFields: ['name'],
                                routeName: 'entities.specializations.updateResources',
                                relatedEntityType: 'resources',
                                searchApiEntityType: 'resources',
                                pivotFields: ['level', 'quantity'],
                            }"
                        />

                        <EntityRelationsManager
                            :relations="specialization.items || []"
                            :available-items="availableItems"
                            :entity-id="specialization.id"
                            entity-type="specializations"
                            relation-type="items"
                            relation-name="Items"
                            :config="{
                                itemLabel: 'item',
                                itemLabelPlural: 'items',
                                displayFields: ['name'],
                                searchFields: ['name'],
                                routeName: 'entities.specializations.updateItems',
                                relatedEntityType: 'items',
                                searchApiEntityType: 'items',
                                pivotFields: ['level', 'quantity'],
                            }"
                        />
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    v-if="specialization.id"
                    title="Sections"
                    icon="fa-solid fa-bookmark"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <EntityRelationsManager
                        :relations="specialization.sections || []"
                        :available-items="availableSections"
                        :entity-id="specialization.id"
                        entity-type="specializations"
                        relation-type="sections"
                        relation-name="Sections"
                        :config="{
                            itemLabel: 'section',
                            itemLabelPlural: 'sections',
                            displayFields: ['title', 'slug'],
                            searchFields: ['title', 'slug'],
                            routeName: 'entities.specializations.updateSections',
                            relatedEntityType: 'sections',
                            pivotFields: ['level'],
                        }"
                    />
                </EntityEditContainer>
            </template>
        </EntityEditForm>
    </Container>
</template>
