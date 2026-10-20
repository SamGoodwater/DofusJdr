<script setup>
/**
 * Breed Index Page (affichée « Liste des Classes »)
 *
 * @props {Object} breeds - Collection paginée des breeds
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Breed } from "@/Models/Entity/Breed";
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
import { getBreedFieldDescriptors } from "@/Entities/breed/breed-descriptors";

defineProps({
    breeds: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des Classes');

const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('breeds'));
const canModify = computed(() => canUpdateAny('breeds'));

const { copyToClipboard } = useCopyToClipboard();

const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);


const tableConfig = computed(() => {
    const ctx = {
        capabilities: {
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
    };
    const descriptors = getBreedFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
const serverUrl = computed(() => `${route('api.tables.breeds')}?format=entities&limit=5000&_t=${refreshToken.value}`);





const tableMeta = ref({});

const { openFullModal, openEdit } = useEntityIndexSurface("breeds", {
    getTableMeta: () => tableMeta.value,
});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

const openPreviewModal = (model) => {
    if (!model) return;
    openFullModal(model);
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = raw instanceof Breed ? raw : Breed.fromArray([raw])[0] || null;
    if (!model) return;
    openPreviewModal(model);
};

const { handleKeyboardIntent } = useEntityIndexTableIntents({
    ModelClass: Breed,
    routeShowName: "entities.breeds.show",
    routeShowParam: "breed",
    canModify: () => canModify.value,
    openFullModal: openPreviewModal,
    openEdit,
});

const handleCreateRequest = () => {
    if (canCreate.value) {
        createModalOpen.value = true;
    }
};

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


const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;

    const model = targetEntity instanceof Breed ? targetEntity : Breed.fromArray([targetEntity])[0] || null;
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
            const cfg = getEntityRouteConfig('breed');
            const url = resolveEntityRouteUrl('breed', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            break;
        case 'delete':
            break;
    }
};





const handleModalDelete = (_entity) => {};

</script>

<template>
    <Head title="Liste des Classes" />

    <div class="space-y-6 pb-8 w-full">
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Classes</h1>
                <p class="text-primary-200 mt-2">Gérez les classes jouables</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une classe
            </Btn>
        </div>

        <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="breeds"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="getEntityResponseAdapter('breeds')"
                    v-model:selected-ids="selectedIds"
                    @loaded="handleTableLoaded"
                    @row-dblclick="handleRowDoubleClick"
                    @keyboard-intent="handleKeyboardIntent"
                    @create-request="handleCreateRequest"
                    @action="handleTableAction"
                />
        </div>

        <CreateEntityModal
            :open="createModalOpen"
            entity-type="breeds"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('breeds')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />
    </div>
</template>
