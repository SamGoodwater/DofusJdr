<script setup>
/**
 * Item Index Page
 * 
 * @description
 * Page de liste des items avec tableau et modal
 * 
 * @props {Object} items - Collection paginée des items
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Item } from "@/Models/Entity/Item";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";
import { useEntityIndexSurface } from "@/Composables/entity/useEntityIndexSurface";
import { normalizeIndexTableFilters } from "@/Composables/entity/useEntityIndexTableFilters";

// Props Inertia : filtres + catalogues de filtres. La liste vient de api.tables.items.
const props = defineProps({
    filters: {
        type: Object,
        default: () => ({})
    },
    itemTypes: {
        type: Array,
        default: () => [],
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des Objets');

const { copyToClipboard } = useCopyToClipboard();

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('items'));
const canModify = computed(() => canUpdateAny('items'));
// const canManage = computed(() => canManageAny('items'));

// Table v2 state (client-first)
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const indexTableFilters = computed(() => normalizeIndexTableFilters(props.filters));
const serverBaseUrl = computed(() => route('api.tables.items'));


const tableMeta = ref({});

const { openFullModal, openEdit } = useEntityIndexSurface("items", {
    getTableMeta: () => tableMeta.value,
});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

// État
const createModalOpen = ref(false);


// Configuration du tableau avec permissions et contexte
const tableConfig = computed(() => {
    const ctx = {
        capabilities: { 
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
        itemTypes: props.itemTypes || [],
    };
    const descriptors = getItemFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});

// Handlers
const openModal = (entity) => openFullModal(entity);

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Item.fromArray([raw])[0] || null;
    if (!model) return;
    openFullModal(model);
};

const { handleKeyboardIntent } = useEntityIndexTableIntents({
    ModelClass: Item,
    routeShowName: "entities.items.show",
    routeShowParam: "item",
    canModify: () => canModify.value,
    openFullModal: openModal,
    openEdit,
});

const handleCreateRequest = () => {
    if (canCreate.value) {
        createModalOpen.value = true;
    }
};

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    const model = Item.fromArray([targetEntity])[0] || null;
    if (!model) return;
    
    const entityId = model.id;
    if (!entityId) return;

    switch (actionKey) {
        case 'view':
        case 'quick-view':
            openFullModal(model);
            break;

        case 'edit':
            openEdit(model);
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig('item');
            const url = resolveEntityRouteUrl('item', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            // TODO: Implémenter le téléchargement PDF
            break;
        case 'delete':
            // TODO: Implémenter la suppression avec confirmation
            break;
    }
};

</script>

<template>
    <Head title="Liste des Objets" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Objets</h1>
                <p class="text-primary-200 mt-2">Gérez les objets et équipements</p>
            </div>
            <div class="flex gap-2 items-center">
                <Btn v-if="canCreate" @click="handleCreate" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un objet
                </Btn>
            </div>
        </div>

        <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="items"
                    :config="tableConfig"
                    server-side
                    :server-base-url="serverBaseUrl"
                    :refresh-token="refreshToken"
                    :initial-filter-values="indexTableFilters"
                    :response-adapter="getEntityResponseAdapter('items')"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                    @keyboard-intent="handleKeyboardIntent"
                    @create-request="handleCreateRequest"
                    @action="handleTableAction"
                />
        </div>

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="item"
            :fields-config="fieldsConfig"
            :default-entity="defaultEntity"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('items')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />
    </div>
</template>

