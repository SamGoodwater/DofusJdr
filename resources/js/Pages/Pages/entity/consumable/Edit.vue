<script setup>
/**
 * Consumable Edit Page
 *
 * @description
 * Édition sheet (header compact + grille 2 colonnes) + sous-éditeurs en containers.
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Consumable } from '@/Models/Entity/Consumable';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EffectUsagesManager from '@/Pages/Organismes/entity/EffectUsagesManager.vue';
import ObjectEffectsManager from '@/Pages/Organismes/entity/ObjectEffectsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import ItemPriceEditSection from '@/Pages/Molecules/entity/item/ItemPriceEditSection.vue';
import {
    buildConsumableFormFieldsConfig,
    CONSUMABLE_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/consumable/consumable-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    consumable: {
        type: Object,
        required: true,
    },
    availableConsumableTypes: {
        type: Array,
        default: () => [],
    },
    availableResources: {
        type: Array,
        default: () => [],
    },
    effectUsages: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: 'consumable' },
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


const consumable = computed(() => {
    const data = props.consumable || page.props.consumable || {};
    return new Consumable(data);
});

const fieldsConfig = computed(() =>
    buildConsumableFormFieldsConfig({
        includeReadonlyMeta: true,
        consumableTypes: props.availableConsumableTypes || [],
    }),
);

const fieldSections = CONSUMABLE_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('consumables') || isAdmin.value);

setPageTitle(`Modifier le consommable : ${consumable.value.name || 'Sans nom'}`);
</script>

<template>
    <Head :title="`Modifier le consommable : ${consumable?.name || 'Sans nom'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="consumables"
            :entity="consumable"
            :form-ref="entityEditFormRef"
            :title="consumable.name || 'Consommable sans nom'"
            list-route-name="entities.consumables.index"
            delete-route-name="entities.consumables.delete"
            route-param-key="consumable"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer ce consommable ? Il sera placé en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="consumable"
            entity-type="consumable"
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
                    v-if="consumable.id"
                    title="Prix"
                    subtitle="Prix calculé et éventuel prix personnalisé."
                    icon="fa-solid fa-coins"
                    :span="2"
                    collapsible
                    :default-open="true"
                    root-class="mt-3 lg:col-span-2"
                >
                    <ItemPriceEditSection
                        :update-url="route('entities.consumables.update', { consumable: consumable.id })"
                        :recalculate-url="route('entities.consumables.recalculatePrice', { consumable: consumable.id })"
                        :price-calculated="consumable.priceCalculated"
                        :price-custom="consumable.priceCustom"
                        :allow-recalculate="(consumable.state ?? consumable._data?.state) !== 'playable'"
                        formula-hint="Somme des prix des ressources de la recette. Sans recette, le total vient du prix personnalisé (barème JDR)."
                    />
                </EntityEditContainer>

                <EntityEditContainer
                    title="Effets & usages"
                    subtitle="Usages d’effets et effets d’objet."
                    icon="fa-solid fa-wand-magic-sparkles"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <div class="space-y-4">
                        <EffectUsagesManager
                            :effect-usages="effectUsages"
                            :available-effects="availableEffects"
                            :entity-type="effectEntityType"
                            :entity-id="consumable.id"
                        />
                        <ObjectEffectsManager
                            :object-effects="objectEffects"
                            :object-effect-characteristics="objectEffectCharacteristics"
                            :object-effect-monsters="objectEffectMonsters"
                            :entity-type="effectEntityType"
                            :entity-id="consumable.id"
                        />
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    title="Recette de craft"
                    subtitle="Ressources nécessaires."
                    icon="fa-solid fa-hammer"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <EntityRelationsManager
                        :relations="consumable.resources || []"
                        :available-items="availableResources"
                        :entity-id="consumable.id"
                        entity-type="consumables"
                        relation-type="resources"
                        relation-name="Ressources nécessaires (recette de craft)"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            pivotFields: ['quantity'],
                            itemLabel: 'ressource',
                            itemLabelPlural: 'ressources',
                        }"
                    />
                </EntityEditContainer>
            </template>
        </EntityEditForm>
    </Container>
</template>
