<script setup>
/**
 * Consumable Index Page
 * 
 * @description
 * Page de liste des consommables avec tableau et modal
 * 
 * @props {Object} consumables - Collection paginée des consommables
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Consumable } from "@/Models/Entity/Consumable";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";
import { useEntityIndexSurface } from "@/Composables/entity/useEntityIndexSurface";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getConsumableFieldDescriptors } from "@/Entities/consumable/consumable-descriptors";
import { normalizeIndexTableFilters } from "@/Composables/entity/useEntityIndexTableFilters";

const props = defineProps({
    consumables: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    consumableTypes: {
        type: Array,
        default: () => []
    }
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des Consommables');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('consumables'));
const canModify = computed(() => canUpdateAny('consumables'));

// Bulk request
const { copyToClipboard } = useCopyToClipboard();

// État
const createModalOpen = ref(false);

// Table v2
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);


// Configuration du tableau avec permissions et contexte
const tableConfig = computed(() => {
    const ctx = {
        capabilities: { 
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
        consumableTypes: props.consumableTypes || [],
    };
    const descriptors = getConsumableFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
const indexTableFilters = computed(() => normalizeIndexTableFilters(props.filters));
const serverBaseUrl = computed(() => route('api.tables.consumables'));





const tableMeta = ref({});

const { openFullModal, openEdit } = useEntityIndexSurface("consumables", {
    getTableMeta: () => tableMeta.value,
});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Consumable.fromArray([raw])[0] || null;
    if (!model) return;
    openFullModal(model);
};

const handleCreate = () => {
    createModalOpen.value = true;
};

const handleCloseCreateModal = () => {
    createModalOpen.value = false;
};

const handleEntityCreated = () => {
    createModalOpen.value = false;
    refreshToken.value++;
};



const { handleKeyboardIntent } = useEntityIndexTableIntents({
    ModelClass: Consumable,
    routeShowName: "entities.consumables.show",
    routeShowParam: "consumable",
    canModify: () => canModify.value,
    openFullModal,
    openEdit,
});

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    const model = Consumable.fromArray([targetEntity])[0] || null;
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
            const cfg = getEntityRouteConfig('consumable');
            const url = resolveEntityRouteUrl('consumable', 'show', entityId, cfg);
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
    <Head title="Liste des Consommables" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Consommables</h1>
                <p class="text-primary-200 mt-2">Gérez les consommables (potions, parchemins, etc.)</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer un consommable
            </Btn>
        </div>
        <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="consumables"
                    :config="tableConfig"
                    server-side
                    :server-base-url="serverBaseUrl"
                    :refresh-token="refreshToken"
                    :initial-filter-values="indexTableFilters"
                    :response-adapter="getEntityResponseAdapter('consumables')"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                    @keyboard-intent="handleKeyboardIntent"
                    @action="handleTableAction"
                />
        </div>

        <!-- Modal de création -->
        <CreateEntityModal
            :open="createModalOpen"
            entity-type="consumable"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('consumables')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />
    </div>
</template>
