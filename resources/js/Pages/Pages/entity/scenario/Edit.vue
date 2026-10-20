<script setup>
/**
 * Scenario Edit Page
 *
 * @description
 * Édition sheet (header compact + grille 2 colonnes) + relations en containers.
 *
 * @props {Object} scenario - Données du scénario à éditer
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Scenario } from '@/Models/Entity/Scenario';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import {
    buildScenarioFormFieldsConfig,
    SCENARIO_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/scenario/scenario-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    scenario: {
        type: Object,
        required: true,
    },
    availableItems: {
        type: Array,
        default: () => [],
    },
    availableConsumables: {
        type: Array,
        default: () => [],
    },
    availableResources: {
        type: Array,
        default: () => [],
    },
    availableSpells: {
        type: Array,
        default: () => [],
    },
    availablePanoplies: {
        type: Array,
        default: () => [],
    },
    /** True quand la page est chargée dans EntitySurfaceHost (modal). */
    embeddedInModal: {
        type: Boolean,
        default: false,
    },
});
const { isEmbeddedInModal, resolvedRedirectAfterUpdate } = useEntityEditEmbed(props);


const fieldsConfig = computed(() =>
    buildScenarioFormFieldsConfig({ includeReadonlyMeta: true }),
);
const fieldSections = SCENARIO_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('scenarios') || isAdmin.value);

const scenario = computed(() => {
    const scenarioData = props.scenario || page.props.scenario || {};
    return new Scenario(scenarioData);
});

setPageTitle(`Modifier le scénario : ${scenario.value.name || 'Nouveau scénario'}`);
</script>

<template>
    <Head :title="`Modifier le scénario : ${scenario?.name || 'Nouveau scénario'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="scenarios"
            :entity="scenario"
            :form-ref="entityEditFormRef"
            :title="scenario.name || 'Scénario sans nom'"
            list-route-name="entities.scenarios.index"
            delete-route-name="entities.scenarios.delete"
            route-param-key="scenario"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer ce scénario ? Il sera placé en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="scenario"
            entity-type="scenario"
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
            :compact-access-levels="true"
            :embedded-in-modal="isEmbeddedInModal"
            :redirect-after-update="resolvedRedirectAfterUpdate"
        >
            <template #after-sections>
                <EntityEditContainer
                    title="Contenu lié"
                    subtitle="Objets, consommables, sorts et panoplies."
                    icon="fa-solid fa-boxes-stacked"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="mt-3 lg:col-span-2"
                >
                    <div class="space-y-4">
                        <EntityRelationsManager
                            :relations="scenario.items || []"
                            :available-items="availableItems"
                            :entity-id="scenario.id"
                            entity-type="scenarios"
                            relation-type="items"
                            relation-name="Objets du scénario"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'objet',
                                itemLabelPlural: 'objets',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="scenario.consumables || []"
                            :available-items="availableConsumables"
                            :entity-id="scenario.id"
                            entity-type="scenarios"
                            relation-type="consumables"
                            relation-name="Consommables du scénario"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'consommable',
                                itemLabelPlural: 'consommables',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="scenario.resources || []"
                            :available-items="availableResources"
                            :entity-id="scenario.id"
                            entity-type="scenarios"
                            relation-type="resources"
                            relation-name="Ressources du scénario"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'ressource',
                                itemLabelPlural: 'ressources',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="scenario.spells || []"
                            :available-items="availableSpells"
                            :entity-id="scenario.id"
                            entity-type="scenarios"
                            relation-type="spells"
                            relation-name="Sorts du scénario"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'sort',
                                itemLabelPlural: 'sorts',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="scenario.panoplies || []"
                            :available-items="availablePanoplies"
                            :entity-id="scenario.id"
                            entity-type="scenarios"
                            relation-type="panoplies"
                            relation-name="Panoplies du scénario"
                            :config="{
                                displayFields: ['name', 'description'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'panoplie',
                                itemLabelPlural: 'panoplies',
                            }"
                        />
                    </div>
                </EntityEditContainer>
            </template>
        </EntityEditForm>
    </Container>
</template>
