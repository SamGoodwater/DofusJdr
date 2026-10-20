<script setup>
/**
 * Page d’édition sheet d’un PNJ : identité + kit de jeu en containers.
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Npc } from '@/Models/Entity/Npc';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EntityLanguagesEditor from '@/Pages/Organismes/entity/EntityLanguagesEditor.vue';
import CreatureTraitsEditor from '@/Pages/Organismes/entity/CreatureTraitsEditor.vue';
import CreatureComposableCharacteristicsEditor from '@/Pages/Organismes/entity/CreatureComposableCharacteristicsEditor.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import {
    buildNpcFormFieldsConfig,
    NPC_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/npc/npc-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    npc: {
        type: Object,
        required: true,
    },
    availablePanoplies: {
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
    availableSpells: {
        type: Array,
        default: () => [],
    },
    availableItems: {
        type: Array,
        default: () => [],
    },
    availableLanguages: {
        type: Array,
        default: () => [],
    },
    availableCreatureTraits: {
        type: Array,
        default: () => [],
    },
    availableBreeds: {
        type: Array,
        default: () => [],
    },
    availableSpecializations: {
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
    buildNpcFormFieldsConfig({
        breeds: props.availableBreeds || [],
        specializations: props.availableSpecializations || [],
    }),
);

const fieldSections = NPC_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('npcs') || isAdmin.value);

const npc = computed(() => {
    const npcData = props.npc || page.props.npc || {};
    return npcData instanceof Npc ? npcData : new Npc(npcData);
});

const npcName = computed(() => npc.value.creature?.name || npc.value.name || 'Nouveau PNJ');

const creatureSpells = computed(
    () => npc.value.creature?.spells ?? npc.value._data?.creature?.spells ?? [],
);
const creatureItems = computed(
    () => npc.value.creature?.items ?? npc.value._data?.creature?.items ?? [],
);

setPageTitle(`Modifier le PNJ : ${npcName.value}`);
</script>

<template>
    <Head :title="`Modifier le PNJ : ${npcName}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="npcs"
            :entity="npc"
            :form-ref="entityEditFormRef"
            :title="npcName"
            list-route-name="entities.npcs.index"
            delete-route-name="entities.npcs.delete"
            route-param-key="npc"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer ce PNJ ? Il sera placé en corbeille (récupération possible côté admin)."
        >
            <template #subtitle>
                Édition · #{{ npc.id }}
                <span v-if="npc.creature">
                    · Créature : {{ npc.creature.name }}
                </span>
            </template>
        </EntityEditHeader>

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="npc"
            entity-type="npc"
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
                    v-if="npc.creature && npc.id"
                    title="Caractéristiques (totaux & contexte)"
                    icon="fa-solid fa-chart-simple"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="mt-3 lg:col-span-2"
                >
                    <CreatureComposableCharacteristicsEditor
                        :creature="npc.creature"
                        :entity-id="npc.id"
                        update-route-name="entities.npcs.update"
                        update-route-param-name="npc"
                    />
                </EntityEditContainer>

                <EntityEditContainer
                    title="Langues & traits"
                    icon="fa-solid fa-comments"
                    :span="2"
                    collapsible
                    :default-open="false"
                    :root-class="
                        npc.creature && npc.id ? 'lg:col-span-2' : 'mt-3 lg:col-span-2'
                    "
                >
                    <div class="space-y-4">
                        <EntityLanguagesEditor
                            v-if="npc.id"
                            entity-type="npc"
                            :relations="npc.languages || []"
                            :available-items="availableLanguages"
                            :entity-id="npc.id"
                        />
                        <CreatureTraitsEditor
                            v-if="npc.id"
                            :relations="npc.creature?.creatureTraits || []"
                            :available-items="availableCreatureTraits"
                            :entity-id="npc.id"
                            route-name="entities.npcs.updateCreatureTraits"
                            route-param-name="npc"
                            title="Traits du PNJ"
                            help="Traits innés du PNJ, attachés à sa créature."
                        />
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    title="Sorts"
                    icon="fa-solid fa-wand-sparkles"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <div class="space-y-4">
                        <EntityRelationsManager
                            :relations="creatureSpells"
                            :available-items="availableSpells"
                            :entity-id="npc.id"
                            entity-type="npcs"
                            relation-type="spells"
                            relation-name="Sorts du PNJ"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                routeName: 'entities.npcs.updateSpells',
                                itemLabel: 'sort',
                                itemLabelPlural: 'sorts',
                                relatedEntityType: 'spells',
                                searchApiEntityType: 'spells',
                            }"
                        />
                        <p v-if="npc.breedId" class="text-xs text-base-content/60">
                            La liste proposée est préfiltrée sur la classe du PNJ ; la recherche catalogue
                            permet d’ajouter d’autres sorts.
                        </p>
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    title="Équipements"
                    icon="fa-solid fa-shield-halved"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <div class="space-y-4">
                        <EntityRelationsManager
                            :relations="creatureItems"
                            :available-items="availableItems"
                            :entity-id="npc.id"
                            entity-type="npcs"
                            relation-type="items"
                            relation-name="Équipement porté"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                routeName: 'entities.npcs.updateItems',
                                itemLabel: 'objet',
                                itemLabelPlural: 'objets',
                                relatedEntityType: 'items',
                                searchApiEntityType: 'items',
                            }"
                        />
                        <p class="text-xs text-base-content/60">
                            Un objet par emplacement (deux anneaux). Les types hors équipement sont refusés.
                        </p>
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    title="Panoplies"
                    icon="fa-solid fa-layer-group"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <EntityRelationsManager
                        :relations="npc.panoplies || []"
                        :available-items="availablePanoplies"
                        :entity-id="npc.id"
                        entity-type="npcs"
                        relation-type="panoplies"
                        relation-name="Panoplies du PNJ"
                        :config="{
                            displayFields: ['name', 'description'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'panoplie',
                            itemLabelPlural: 'panoplies',
                            searchApiEntityType: 'panoplies',
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
                            :relations="npc.scenarios || []"
                            :available-items="availableScenarios"
                            :entity-id="npc.id"
                            entity-type="npcs"
                            relation-type="scenarios"
                            relation-name="Scénarios du PNJ"
                            :config="{
                                displayFields: ['name', 'description'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'scénario',
                                itemLabelPlural: 'scénarios',
                                searchApiEntityType: 'scenarios',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="npc.campaigns || []"
                            :available-items="availableCampaigns"
                            :entity-id="npc.id"
                            entity-type="npcs"
                            relation-type="campaigns"
                            relation-name="Campagnes du PNJ"
                            :config="{
                                displayFields: ['name', 'description'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'campagne',
                                itemLabelPlural: 'campagnes',
                                searchApiEntityType: 'campaigns',
                            }"
                        />
                    </div>
                </EntityEditContainer>
            </template>
        </EntityEditForm>
    </Container>
</template>
