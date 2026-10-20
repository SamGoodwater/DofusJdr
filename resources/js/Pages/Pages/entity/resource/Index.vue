<script setup>
/**
 * Resource Index Page
 * 
 * @description
 * Page de liste des ressources avec tableau et modal
 * 
 * @props {Object} resources - Collection paginée des ressources
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { Resource } from "@/Models/Entity/Resource";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";
import { useEntityIndexSurface } from "@/Composables/entity/useEntityIndexSurface";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";
import { normalizeIndexTableFilters } from "@/Composables/entity/useEntityIndexTableFilters";

const props = defineProps({
    resources: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    resourceTypes: {
        type: Array,
        default: () => []
    },
    can: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();

// Notifications
const notificationStore = useNotificationStore();
const { copyToClipboard } = useCopyToClipboard();
setPageTitle('Liste des Ressources');

// Permissions
const permissionsApi = usePermissions();
const { canUpdateAny, canCreate: canCreatePermission, canAccess } = permissionsApi;
const canCreate = computed(() => Boolean(props.can?.create ?? canCreatePermission('resources')));
const canModify = computed(() => Boolean(props.can?.updateAny ?? canUpdateAny('resources')));
// canManageAny gardé pour plus tard (actions de maintenance)
// const canManage = computed(() => Boolean(props.can?.manageAny ?? canManageAny('resources')));

// État
const createModalOpen = ref(false);
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
        resourceTypes: props.resourceTypes || [],
    };
    const descriptors = getResourceFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});

const indexTableFilters = computed(() => normalizeIndexTableFilters(props.filters));
const serverBaseUrl = computed(() => route('api.tables.resources'));



// Handlers
const openModal = (entity) => openFullModal(entity);


const tableMeta = ref({});

const { openFullModal, openEdit } = useEntityIndexSurface("resources", {
    getTableMeta: () => tableMeta.value,
});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Resource.fromArray([raw])[0] || null;
    if (!model) return;
    openFullModal(model);
};

// Descriptors -> fieldsConfig (source de vérité unique)
const resourceDescriptors = computed(() =>
    getResourceFieldDescriptors({
        capabilities: { updateAny: canModify.value },
        resourceTypes: props.resourceTypes || [],
    })
);
const fieldsConfig = computed(() =>
    createFieldsConfigFromDescriptors(resourceDescriptors.value, {
        capabilities: { updateAny: canModify.value },
        resourceTypes: props.resourceTypes || [],
    })
);
const defaultEntity = computed(() => createDefaultEntityFromDescriptors(resourceDescriptors.value));

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
    ModelClass: Resource,
    routeShowName: "entities.resources.show",
    routeShowParam: "resource",
    canModify: () => canModify.value,
    openFullModal: openModal,
    openEdit,
});

const handleRefreshAll = () => {
    refreshToken.value++;
};


// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    const model = Resource.fromArray([targetEntity])[0] || null;
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
            const cfg = getEntityRouteConfig('resource');
            const url = resolveEntityRouteUrl('resource', 'show', entityId, cfg);
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

// Handlers pour les actions du modal (reçoivent directement l'entité depuis l'événement)






</script>

<template>
    <Head title="Liste des Ressources" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Ressources</h1>
                <p class="text-primary-200 mt-2">Gérez les ressources (matériaux, composants, etc.)</p>
            </div>
            <div class="flex gap-2">
                <Btn variant="ghost" @click="handleRefreshAll" title="Recharger le dataset">
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i>
                    Recharger
                </Btn>
                <Btn v-if="canAccess('contentManagement')" variant="ghost" @click="router.visit(route('admin.content.types.show', { kind: 'resource' }))">
                    <i class="fa-solid fa-tags mr-2"></i>
                    Types de ressources
                </Btn>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une ressource
            </Btn>
        </div>
        </div>

        <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="resources"
                    :config="tableConfig"
                    server-side
                    :server-base-url="serverBaseUrl"
                    :refresh-token="refreshToken"
                    :initial-filter-values="indexTableFilters"
                    :response-adapter="getEntityResponseAdapter('resources')"
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
            entity-type="resource"
            :fields-config="fieldsConfig"
            :default-entity="defaultEntity"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('resources')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />
    </div>
</template>
