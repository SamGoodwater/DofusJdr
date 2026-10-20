<script setup>
/**
 * Monster Edit Page
 *
 * @description
 * Édition sheet (header compact + grille 2 colonnes) + sous-managers créature en containers.
 *
 * @props {Object} monster - Données du monstre à éditer
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Monster } from '@/Models/Entity/Monster';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EntityLanguagesEditor from '@/Pages/Organismes/entity/EntityLanguagesEditor.vue';
import CreatureTraitsEditor from '@/Pages/Organismes/entity/CreatureTraitsEditor.vue';
import CreatureComposableCharacteristicsEditor from '@/Pages/Organismes/entity/CreatureComposableCharacteristicsEditor.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import {
    buildMonsterFormFieldsConfig,
    MONSTER_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/monster/monster-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    monster: {
        type: Object,
        required: true,
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
    availableLanguages: {
        type: Array,
        default: () => [],
    },
    availableCreatureTraits: {
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


const fieldsConfig = computed(() => buildMonsterFormFieldsConfig());
const fieldSections = MONSTER_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('monsters') || isAdmin.value);

const monster = computed(() => {
    const monsterData = props.monster || page.props.monster || {};
    return new Monster(monsterData);
});

const monsterName = computed(() => {
    return monster.value.creature?.name || 'Nouveau monstre';
});

setPageTitle(`Modifier le monstre : ${monsterName.value}`);
</script>

<template>
    <Head :title="`Modifier le monstre : ${monsterName}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="monsters"
            :entity="monster"
            :form-ref="entityEditFormRef"
            :title="monsterName"
            list-route-name="entities.monsters.index"
            delete-route-name="entities.monsters.delete"
            route-param-key="monster"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer ce monstre ? Il sera placé en corbeille (récupération possible côté admin)."
        >
            <template #subtitle>
                Édition · #{{ monster.id }}
                <span v-if="monster.creature">
                    · Créature : {{ monster.creature.name }}
                </span>
            </template>
        </EntityEditHeader>

        <p class="text-xs text-base-content/60 px-1">
            Nom et statistiques (totaux / bonus) sont portés par la créature associée.
        </p>

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="monster"
            entity-type="monster"
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
                    v-if="monster.creature && monster.id"
                    title="Caractéristiques (totaux & contexte)"
                    icon="fa-solid fa-chart-simple"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="mt-3 lg:col-span-2"
                >
                    <CreatureComposableCharacteristicsEditor
                        :creature="monster.creature"
                        :entity-id="monster.id"
                        update-route-name="entities.monsters.update"
                        update-route-param-name="monster"
                    />
                </EntityEditContainer>

                <EntityEditContainer
                    title="Langues & traits"
                    icon="fa-solid fa-comments"
                    :span="2"
                    collapsible
                    :default-open="false"
                    :root-class="
                        monster.creature && monster.id
                            ? 'lg:col-span-2'
                            : 'mt-3 lg:col-span-2'
                    "
                >
                    <div class="space-y-4">
                        <EntityLanguagesEditor
                            v-if="monster.id"
                            entity-type="monster"
                            :relations="monster.languages || []"
                            :available-items="availableLanguages"
                            :entity-id="monster.id"
                        />

                        <CreatureTraitsEditor
                            v-if="monster.id"
                            :relations="monster.creature?.creatureTraits || monster.creatureTraits || []"
                            :available-items="availableCreatureTraits"
                            :entity-id="monster.id"
                            route-name="entities.monsters.updateCreatureTraits"
                            route-param-name="monster"
                            title="Traits du monstre"
                            help="Traits innés du monstre. Ces traits sont attachés directement à sa créature et n'ont pas de niveau d'activation."
                        />
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    title="Sorts d'invocation"
                    icon="fa-solid fa-wand-sparkles"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <EntityRelationsManager
                        :relations="monster.spellInvocations || []"
                        :available-items="availableSpells"
                        :entity-id="monster.id"
                        entity-type="monsters"
                        relation-type="spellInvocations"
                        relation-name="Sorts d'invocation du monstre"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            routeName: 'entities.monsters.updateSpellInvocations',
                            itemLabel: 'sort',
                            itemLabelPlural: 'sorts',
                            relatedEntityType: 'spells',
                            searchApiEntityType: 'spells',
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
                            :relations="monster.scenarios || []"
                            :available-items="availableScenarios"
                            :entity-id="monster.id"
                            entity-type="monsters"
                            relation-type="scenarios"
                            relation-name="Scénarios du monstre"
                            :config="{
                                displayFields: ['name', 'description'],
                                searchFields: ['name', 'description'],
                                itemLabel: 'scénario',
                                itemLabelPlural: 'scénarios',
                                searchApiEntityType: 'scenarios',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="monster.campaigns || []"
                            :available-items="availableCampaigns"
                            :entity-id="monster.id"
                            entity-type="monsters"
                            relation-type="campaigns"
                            relation-name="Campagnes du monstre"
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
