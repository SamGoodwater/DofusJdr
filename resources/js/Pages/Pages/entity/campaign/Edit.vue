<script setup>
/**
 * Campaign Edit Page
 *
 * @description
 * Édition sheet (header compact + grille 2 colonnes) + relations en containers.
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Campaign } from '@/Models/Entity/Campaign';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import {
    buildCampaignFormFieldsConfig,
    CAMPAIGN_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/campaign/campaign-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    campaign: {
        type: Object,
        required: true,
    },
    availableUsers: {
        type: Array,
        default: () => [],
    },
    availableScenarios: {
        type: Array,
        default: () => [],
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
    buildCampaignFormFieldsConfig({ includeReadonlyMeta: true }),
);
const fieldSections = CAMPAIGN_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('campaigns') || isAdmin.value);

const campaign = computed(() => {
    const campaignData = props.campaign || page.props.campaign || {};
    return new Campaign(campaignData);
});

setPageTitle(`Modifier la campagne : ${campaign.value.name || 'Nouvelle campagne'}`);
</script>

<template>
    <Head :title="`Modifier la campagne : ${campaign?.name || 'Nouvelle campagne'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="campaigns"
            :entity="campaign"
            :form-ref="entityEditFormRef"
            :title="campaign.name || 'Campagne sans nom'"
            list-route-name="entities.campaigns.index"
            delete-route-name="entities.campaigns.delete"
            route-param-key="campaign"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer cette campagne ? Elle sera placée en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="campaign"
            entity-type="campaign"
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
                    title="Participants & scénarios"
                    icon="fa-solid fa-users"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="mt-3 lg:col-span-2"
                >
                    <div class="space-y-4">
                        <EntityRelationsManager
                            :relations="campaign.users || []"
                            :available-items="availableUsers"
                            :entity-id="campaign.id"
                            entity-type="campaigns"
                            relation-type="users"
                            relation-name="Utilisateurs de la campagne"
                            :config="{
                                displayFields: ['name', 'email'],
                                searchFields: ['name', 'email'],
                                itemLabel: 'utilisateur',
                                itemLabelPlural: 'utilisateurs',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="campaign.scenarios || []"
                            :available-items="availableScenarios"
                            :entity-id="campaign.id"
                            entity-type="campaigns"
                            relation-type="scenarios"
                            relation-name="Scénarios de la campagne"
                            :config="{
                                displayFields: ['name', 'description'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'scénario',
                                itemLabelPlural: 'scénarios',
                            }"
                        />
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    title="Contenu lié"
                    subtitle="Objets, consommables, sorts et panoplies."
                    icon="fa-solid fa-boxes-stacked"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <div class="space-y-4">
                        <EntityRelationsManager
                            :relations="campaign.items || []"
                            :available-items="availableItems"
                            :entity-id="campaign.id"
                            entity-type="campaigns"
                            relation-type="items"
                            relation-name="Objets de la campagne"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'objet',
                                itemLabelPlural: 'objets',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="campaign.consumables || []"
                            :available-items="availableConsumables"
                            :entity-id="campaign.id"
                            entity-type="campaigns"
                            relation-type="consumables"
                            relation-name="Consommables de la campagne"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'consommable',
                                itemLabelPlural: 'consommables',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="campaign.resources || []"
                            :available-items="availableResources"
                            :entity-id="campaign.id"
                            entity-type="campaigns"
                            relation-type="resources"
                            relation-name="Ressources de la campagne"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'ressource',
                                itemLabelPlural: 'ressources',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="campaign.spells || []"
                            :available-items="availableSpells"
                            :entity-id="campaign.id"
                            entity-type="campaigns"
                            relation-type="spells"
                            relation-name="Sorts de la campagne"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'sort',
                                itemLabelPlural: 'sorts',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="campaign.panoplies || []"
                            :available-items="availablePanoplies"
                            :entity-id="campaign.id"
                            entity-type="campaigns"
                            relation-type="panoplies"
                            relation-name="Panoplies de la campagne"
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
