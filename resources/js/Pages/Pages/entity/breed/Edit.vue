<script setup>
/**
 * Page d’édition sheet d’une classe (Breed).
 *
 * @props {Object} breed - BreedResource
 * @props {Array} availableSpells - Sorts disponibles pour la liaison pivot
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Breed } from '@/Models/Entity/Breed';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import BreedSpellSlotsEditor from '@/Pages/Organismes/entity/BreedSpellSlotsEditor.vue';
import BreedCapabilitiesEditor from '@/Pages/Organismes/entity/BreedCapabilitiesEditor.vue';
import CreatureTraitsEditor from '@/Pages/Organismes/entity/CreatureTraitsEditor.vue';
import EntityLanguagesEditor from '@/Pages/Organismes/entity/EntityLanguagesEditor.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import BreedElementOrientationsEditor from '@/Pages/Organismes/entity/BreedElementOrientationsEditor.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import { getBreedFieldDescriptors } from '@/Entities/breed/breed-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import { BREED_FORM_FIELD_SECTIONS_EDIT } from '@/Entities/breed/breed-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canUpdateAny, canDeleteAny, isAdmin } = usePermissions();
const canModify = computed(() => canUpdateAny('breeds'));

const props = defineProps({
    breed: {
        type: Object,
        required: true,
    },
    availableSpells: {
        type: Array,
        default: () => [],
    },
    breedOrientationKeys: {
        type: Array,
        default: () => [],
    },
    availableCapabilities: {
        type: Array,
        default: () => [],
    },
    availableCreatureTraits: {
        type: Array,
        default: () => [],
    },
    availableLanguages: {
        type: Array,
        default: () => [],
    },
    availableSections: {
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


const breed = computed(() => {
    const raw = props.breed || page.props.breed || {};
    return raw instanceof Breed ? raw : new Breed(raw);
});

const orientationKeyOptions = computed(
    () =>
        props.breedOrientationKeys?.length
            ? props.breedOrientationKeys
            : page.props.breedOrientationKeys || [],
);

const fieldsConfig = computed(() => {
    const ctx = {
        capabilities: { updateAny: canModify.value, createAny: false },
        meta: { capabilities: { updateAny: canModify.value } },
    };
    return createFieldsConfigFromDescriptors(getBreedFieldDescriptors(ctx), ctx);
});

const fieldSections = BREED_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('breeds') || isAdmin.value);

setPageTitle(`Modifier la classe : ${breed.value.name || '-'}`);
</script>

<template>
    <Head :title="`Modifier : ${breed?.name || 'Classe'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="breeds"
            :entity="breed"
            :form-ref="entityEditFormRef"
            :title="breed.name || 'Classe sans nom'"
            list-route-name="entities.breeds.index"
            delete-route-name="entities.breeds.delete"
            route-param-key="breed"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer cette classe ? Elle sera placée en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="breed"
            entity-type="breed"
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
                    v-if="breed.id"
                    title="Orientations élémentaires"
                    icon="fa-solid fa-fire"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="mt-3 lg:col-span-2"
                >
                    <BreedElementOrientationsEditor
                        :breed-id="breed.id"
                        :initial-map="breed.elementOrientations"
                        :orientation-keys="orientationKeyOptions"
                    />
                </EntityEditContainer>

                <EntityEditContainer
                    v-if="breed.id"
                    title="Sorts & capacités"
                    icon="fa-solid fa-wand-sparkles"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <div class="space-y-4">
                        <BreedSpellSlotsEditor
                            :relations="breed.spells || []"
                            :available-items="availableSpells"
                            :entity-id="breed.id"
                        />
                        <BreedCapabilitiesEditor
                            :relations="breed.capabilities || []"
                            :available-items="availableCapabilities"
                            :entity-id="breed.id"
                        />
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    v-if="breed.id"
                    title="Traits & langues"
                    icon="fa-solid fa-comments"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <div class="space-y-4">
                        <CreatureTraitsEditor
                            :relations="breed.creatureTraits || []"
                            :available-items="availableCreatureTraits"
                            :entity-id="breed.id"
                            route-name="entities.breeds.updateCreatureTraits"
                            route-param-name="breed"
                            title="Traits de classe"
                            help="Traits permanents gagnés par les personnages de cette classe. Le niveau indique quand le trait devient actif."
                            with-level
                            search-api-entity-type="creature-traits"
                        />
                        <EntityLanguagesEditor
                            entity-type="breed"
                            :relations="breed.languages || []"
                            :available-items="availableLanguages"
                            :entity-id="breed.id"
                        />
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    v-if="breed.id"
                    title="Sections"
                    icon="fa-solid fa-bookmark"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <EntityRelationsManager
                        :relations="breed.sections || []"
                        :available-items="availableSections"
                        :entity-id="breed.id"
                        entity-type="breeds"
                        relation-type="sections"
                        relation-name="Sections"
                        :config="{
                            itemLabel: 'section',
                            itemLabelPlural: 'sections',
                            displayFields: ['title', 'slug'],
                            searchFields: ['title', 'slug'],
                            routeName: 'entities.breeds.updateSections',
                            relatedEntityType: 'sections',
                            pivotFields: ['level'],
                        }"
                    />
                </EntityEditContainer>
            </template>
        </EntityEditForm>
    </Container>
</template>
