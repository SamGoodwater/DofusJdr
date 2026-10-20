<script setup>
/**
 * Specialization Index Page
 * 
 * @description
 * Page de liste des spécialisations avec tableau et modal
 * 
 * @props {Object} specializations - Collection paginée des spécialisations
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Specialization } from "@/Models/Entity/Specialization";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";
import { useEntityIndexSurface } from "@/Composables/entity/useEntityIndexSurface";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getSpecializationFieldDescriptors } from "@/Entities/specialization/specialization-descriptors";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";

const props = defineProps({
    specializations: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const { setPageTitle } = usePageTitle();

setPageTitle('Liste des Spécialisations');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('specializations'));
const canModify = computed(() => canUpdateAny('specializations'));

// Bulk request
const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf("specialization");

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
    const descriptors = getSpecializationFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
const serverUrl = computed(() => `${route('api.tables.specializations')}?format=entities&limit=5000&_t=${refreshToken.value}`);



// Calcul des entités sélectionnées depuis les IDs et les rows

// Bulk edit


const tableMeta = ref({});

const { openFullModal, openEdit } = useEntityIndexSurface("specializations", {
    getTableMeta: () => tableMeta.value,
});
const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    // Si c'est déjà une instance Specialization, l'utiliser directement
    const model = raw instanceof Specialization ? raw : Specialization.fromArray([raw])[0] || null;
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
    ModelClass: Specialization,
    routeShowName: "entities.specializations.show",
    routeShowParam: "specialization",
    canModify: () => canModify.value,
    openFullModal,
    openEdit,
});

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    // Si c'est déjà une instance Specialization, l'utiliser directement
    const model = targetEntity instanceof Specialization ? targetEntity : Specialization.fromArray([targetEntity])[0] || null;
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
            const cfg = getEntityRouteConfig('specialization');
            const url = resolveEntityRouteUrl('specialization', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            await downloadPdf(entityId);
            break;
        case 'delete':
            if (window.confirm("Supprimer cette spécialisation ? Elle sera placée en corbeille.")) {
                router.delete(route("entities.specializations.delete", { specialization: entityId }), {
                    onSuccess: () => {
                        refreshToken.value++;
                    },
                });
            }
            break;
    }
};

</script>

<template>
    <Head title="Liste des Spécialisations" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Spécialisations</h1>
                <p class="text-primary-200 mt-2">Gérez les spécialisations de classes</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer une spécialisation
            </Btn>
        </div>
        <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="specializations"
                    :config="tableConfig"
                    :server-url="serverUrl"
                    :response-adapter="getEntityResponseAdapter('specializations')"
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
            entity-type="specialization"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('specializations')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />
    </div>
</template>
