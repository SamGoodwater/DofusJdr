<script setup>
/**
 * Capability Index Page
 *
 * @description
 * Liste des capacités ; affichage / édition via EntitySurfaceHost.
 *
 * @props {Object} capabilities - Collection paginée des capacités
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Capability } from "@/Models/Entity/Capability";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import {
    buildCapabilityFormFieldsConfig,
    CAPABILITY_FORM_FIELD_SECTIONS_CREATE,
    getCapabilityCreateDefaultEntity,
} from "@/Entities/capability/capability-form-config";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";
import { useEntityIndexSurface } from "@/Composables/entity/useEntityIndexSurface";

defineProps({
    capabilities: {
        type: Object,
        required: true
    },
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des Capacités');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('capabilities'));
const canModify = computed(() => canUpdateAny('capabilities'));

// Bulk request
const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf("capability");

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
    };
    const descriptors = getCapabilityFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
const serverUrl = computed(() => `${route('api.tables.capabilities')}?format=entities&limit=5000&_t=${refreshToken.value}`);

const capabilityCreateFieldsConfig = computed(() =>
    buildCapabilityFormFieldsConfig({ includeReadonlyMeta: false }),
);
const capabilityCreateDefaultEntity = getCapabilityCreateDefaultEntity();

// Calcul des entités sélectionnées depuis les IDs et les rows

// Bulk edit


const tableMeta = ref({});

const { openFullModal, openEdit } = useEntityIndexSurface("capabilities", {
    getTableMeta: () => tableMeta.value,
});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    // Si c'est déjà une instance Capability, l'utiliser directement
    const model = raw instanceof Capability ? raw : Capability.fromArray([raw])[0] || null;
    if (!model) return;
    openFullModal(model);
};

// État
const createModalOpen = ref(false);


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
    ModelClass: Capability,
    routeShowName: "entities.capabilities.show",
    routeShowParam: "capability",
    canModify: () => canModify.value,
    openFullModal,
    openEdit,
});

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    // Si c'est déjà une instance Capability, l'utiliser directement
    const model = targetEntity instanceof Capability ? targetEntity : Capability.fromArray([targetEntity])[0] || null;
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
            const cfg = getEntityRouteConfig('capability');
            const url = resolveEntityRouteUrl('capability', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            await downloadPdf(entityId);
            break;
        case 'delete':
            // TODO: Implémenter la suppression avec confirmation
            break;
    }
};

</script>

<template>
    <Head title="Liste des Capacités" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Capacités</h1>
                <p class="text-primary-200 mt-2">Gérez les capacités spéciales</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une capacité
            </Btn>
        </div>
        <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="capabilities"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="getEntityResponseAdapter('capabilities')"
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
            entity-type="capabilities"
            :fields-config="capabilityCreateFieldsConfig"
            :default-entity="capabilityCreateDefaultEntity"
            :field-sections="CAPABILITY_FORM_FIELD_SECTIONS_CREATE"
            characteristics-group="capability"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('capabilities')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />
    </div>
</template>
