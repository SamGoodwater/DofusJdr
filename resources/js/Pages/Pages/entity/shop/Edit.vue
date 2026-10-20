<script setup>
/**
 * Shop Edit Page
 *
 * @description
 * Édition sheet (header compact + grille 2 colonnes) + inventaire en containers.
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Shop } from '@/Models/Entity/Shop';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import {
    buildShopFormFieldsConfig,
    SHOP_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/shop/shop-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    shop: {
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
    /** True quand la page est chargée dans EntitySurfaceHost (modal). */
    embeddedInModal: {
        type: Boolean,
        default: false,
    },
});
const { isEmbeddedInModal, resolvedRedirectAfterUpdate } = useEntityEditEmbed(props);


const fieldsConfig = computed(() =>
    buildShopFormFieldsConfig({ includeReadonlyMeta: true }),
);
const fieldSections = SHOP_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('shops') || isAdmin.value);

const shop = computed(() => {
    const shopData = props.shop || page.props.shop || {};
    return new Shop(shopData);
});

setPageTitle(`Modifier la hotel de vente : ${shop.value.name || 'Nouvelle hotel de vente'}`);
</script>

<template>
    <Head :title="`Modifier la hotel de vente : ${shop?.name || 'Nouvelle hotel de vente'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="shops"
            :entity="shop"
            :form-ref="entityEditFormRef"
            :title="shop.name || 'Hôtel de vente sans nom'"
            list-route-name="entities.shops.index"
            delete-route-name="entities.shops.delete"
            route-param-key="shop"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer cet hôtel de vente ? Il sera placé en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="shop"
            entity-type="shop"
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
                    title="Inventaire (objets, conso, ressources)"
                    icon="fa-solid fa-basket-shopping"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="mt-3 lg:col-span-2"
                >
                    <div class="space-y-4">
                        <EntityRelationsManager
                            :relations="shop.items || []"
                            :available-items="availableItems"
                            :entity-id="shop.id"
                            entity-type="shops"
                            relation-type="items"
                            relation-name="Objets vendus dans la hotel de vente"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                pivotFields: ['quantity', 'price', 'comment'],
                                itemLabel: 'objet',
                                itemLabelPlural: 'objets',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="shop.consumables || []"
                            :available-items="availableConsumables"
                            :entity-id="shop.id"
                            entity-type="shops"
                            relation-type="consumables"
                            relation-name="Consommables vendus dans la hotel de vente"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                pivotFields: ['quantity', 'price', 'comment'],
                                itemLabel: 'consommable',
                                itemLabelPlural: 'consommables',
                            }"
                        />

                        <EntityRelationsManager
                            :relations="shop.resources || []"
                            :available-items="availableResources"
                            :entity-id="shop.id"
                            entity-type="shops"
                            relation-type="resources"
                            relation-name="Ressources vendues dans la hotel de vente"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                pivotFields: ['quantity', 'price', 'comment'],
                                itemLabel: 'ressource',
                                itemLabelPlural: 'ressources',
                            }"
                        />
                    </div>
                </EntityEditContainer>
            </template>
        </EntityEditForm>
    </Container>
</template>
