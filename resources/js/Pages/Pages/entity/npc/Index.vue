<script setup>
/**
 * Npc Index Page
 *
 * @description
 * Catalogue des PNJ : tableau server-side (même pattern que les monstres) et modal de fiche.
 *
 * @props {Object} npcs - Collection paginée des NPCs
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Npc } from "@/Models/Entity/Npc";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";
import { useEntityIndexSurface } from "@/Composables/entity/useEntityIndexSurface";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityTanStackTable from "@/Pages/Organismes/table/EntityTanStackTable.vue";
import CreateEntityModal from "@/Pages/Organismes/entity/CreateEntityModal.vue";
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getNpcFieldDescriptors } from "@/Entities/npc/npc-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";
import { normalizeIndexTableFilters } from "@/Composables/entity/useEntityIndexTableFilters";

const props = defineProps({
    npcs: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    creatures: {
        type: Array,
        default: () => [],
    },
    breeds: {
        type: Array,
        default: () => [],
    },
    specializations: {
        type: Array,
        default: () => [],
    },
});

const { setPageTitle } = usePageTitle();
setPageTitle("Liste des PNJ");

const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission("npcs"));
const canModify = computed(() => canUpdateAny("npcs"));

const { copyToClipboard } = useCopyToClipboard();

const createModalOpen = ref(false);
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);

const descriptorCtx = computed(() => ({
    capabilities: {
        updateAny: canModify.value,
        createAny: canCreate.value,
    },
    creatures: props.creatures || [],
    breeds: props.breeds || [],
    specializations: props.specializations || [],
}));

const tableConfig = computed(() => {
    const descriptors = getNpcFieldDescriptors(descriptorCtx.value);
    const config = TableConfig.fromDescriptors(descriptors, descriptorCtx.value);
    return config.build(descriptorCtx.value);
});

const indexTableFilters = computed(() => normalizeIndexTableFilters(props.filters));
const serverBaseUrl = computed(() => route("api.tables.npcs"));

const npcDescriptors = computed(() => getNpcFieldDescriptors(descriptorCtx.value));
const fieldsConfig = computed(() =>
    createFieldsConfigFromDescriptors(npcDescriptors.value, descriptorCtx.value),
);
const defaultEntity = computed(() => createDefaultEntityFromDescriptors(npcDescriptors.value));

const tableMeta = ref({});

const { openFullModal, openEdit } = useEntityIndexSurface("npcs", {
    getTableMeta: () => tableMeta.value,
});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

const openModal = (entity) => openFullModal(entity);

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = raw instanceof Npc ? raw : Npc.fromArray([raw])[0] || null;
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
    ModelClass: Npc,
    routeShowName: "entities.npcs.show",
    routeShowParam: "npc",
    canModify: () => canModify.value,
    openFullModal,
    openEdit,
});

const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;

    const model = targetEntity instanceof Npc ? targetEntity : Npc.fromArray([targetEntity])[0] || null;
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

        case "copy-link": {
            const cfg = getEntityRouteConfig("npc");
            const url = resolveEntityRouteUrl("npc", "show", entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case "download-pdf":
            break;
        case "delete":
            break;
    }
};




const handleModalDelete = () => {};
</script>

<template>
    <Head title="Liste des PNJ" />

    <div class="space-y-6 pb-8 w-full">
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des PNJ</h1>
                <p class="text-primary-200 mt-2">Gérez les personnages non-joueurs</p>
            </div>
            <div class="flex gap-2">
                <Btn variant="ghost" @click="refreshToken++" title="Recharger le dataset">
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i>
                    Recharger
                </Btn>
                <Btn v-if="canCreate" @click="handleCreate" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un PNJ
                </Btn>
            </div>
        </div>
        <div class="min-w-0 overflow-x-auto">
            <EntityTanStackTable
                entity-type="npcs"
                :config="tableConfig"
                server-side
                :server-base-url="serverBaseUrl"
                :refresh-token="refreshToken"
                :initial-filter-values="indexTableFilters"
                :response-adapter="getEntityResponseAdapter('npcs')"
                v-model:selected-ids="selectedIds"
                @loaded="handleTableLoaded"
                @row-dblclick="handleRowDoubleClick"
                @keyboard-intent="handleKeyboardIntent"
                @action="handleTableAction"
            />
        </div>

        <CreateEntityModal
            :open="createModalOpen"
            entity-type="npc"
            :fields-config="fieldsConfig"
            :default-entity="defaultEntity"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('npcs')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />
    </div>
</template>
