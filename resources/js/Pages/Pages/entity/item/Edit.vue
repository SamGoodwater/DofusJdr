<script setup>
/**
 * Item Edit Page
 *
 * @description
 * Édition sheet (header compact + grille 2 colonnes) + sous-éditeurs en containers.
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Item } from '@/Models/Entity/Item';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EffectUsagesManager from '@/Pages/Organismes/entity/EffectUsagesManager.vue';
import ObjectEffectsManager from '@/Pages/Organismes/entity/ObjectEffectsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import ItemPriceEditSection from '@/Pages/Molecules/entity/item/ItemPriceEditSection.vue';
import {
    buildItemFormFieldsConfig,
    ITEM_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/item/item-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    item: { type: Object, required: true },
    availableResources: { type: Array, default: () => [] },
    itemTypes: { type: Array, default: () => [] },
    effectUsages: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: 'item' },
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


const item = computed(() => {
    const itemData = props.item || page.props.item || {};
    return new Item(itemData);
});

const fieldsConfig = computed(() =>
    buildItemFormFieldsConfig({
        includeReadonlyMeta: true,
        itemTypes: props.itemTypes || [],
    }),
);

const fieldSections = ITEM_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('items') || isAdmin.value);

setPageTitle(`Modifier l'item : ${item.value.name || 'Nouvel item'}`);
</script>

<template>
    <Head :title="`Modifier l'item : ${item?.name || 'Nouvel item'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="items"
            :entity="item"
            :form-ref="entityEditFormRef"
            :title="item.name || 'Équipement sans nom'"
            list-route-name="entities.items.index"
            delete-route-name="entities.items.delete"
            route-param-key="item"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer cet équipement ? Il sera placé en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="item"
            entity-type="item"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :hidden-field-keys="['dofus_version']"
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
                    v-if="item.id"
                    title="Prix"
                    subtitle="Prix calculé et éventuel prix personnalisé."
                    icon="fa-solid fa-coins"
                    :span="2"
                    collapsible
                    :default-open="true"
                    root-class="mt-3 lg:col-span-2"
                >
                    <ItemPriceEditSection
                        :update-url="route('entities.items.update', { item: item.id })"
                        :recalculate-url="route('entities.items.recalculatePrice', { item: item.id })"
                        :price-calculated="item.priceCalculated"
                        :price-custom="item.priceCustom"
                        formula-hint="Bonus de caractéristiques + 150 kamas × niveau + 200 kamas × rareté."
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
                            :entity-id="item.id"
                        />
                        <ObjectEffectsManager
                            :object-effects="objectEffects"
                            :object-effect-characteristics="objectEffectCharacteristics"
                            :object-effect-monsters="objectEffectMonsters"
                            :entity-type="effectEntityType"
                            :entity-id="item.id"
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
                        :relations="item.resources || []"
                        :available-items="availableResources"
                        :entity-id="item.id"
                        entity-type="items"
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
