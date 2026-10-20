<script setup>
/**
 * Monster Index Page
 * 
 * @description
 * Page de liste des monstres avec tableau et modal
 * 
 * @props {Object} monsters - Collection paginée des monstres
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Monster } from "@/Models/Entity/Monster";
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
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { createFieldsConfigFromDescriptors, createDefaultEntityFromDescriptors } from "@/Utils/entity/descriptor-form";
import { normalizeIndexTableFilters } from "@/Composables/entity/useEntityIndexTableFilters";

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({})
    },
    creatures: {
        type: Array,
        default: () => []
    },
    monsterRaces: {
        type: Array,
        default: () => []
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des Monstres');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny, canAccess } = usePermissions();
const canCreate = computed(() => canCreatePermission('monsters'));
const canModify = computed(() => canUpdateAny('monsters'));

// Bulk request
const { copyToClipboard } = useCopyToClipboard();

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
        creatures: props.creatures || [],
        monsterRaces: props.monsterRaces || [],
    };
    const descriptors = getMonsterFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});

const indexTableFilters = computed(() => normalizeIndexTableFilters(props.filters));
const serverBaseUrl = computed(() => route('api.tables.monsters'));



// Calcul des entités sélectionnées depuis les IDs et les rows

// Fields config pour les formulaires (généré depuis les descriptors)
const monsterDescriptors = computed(() =>
    getMonsterFieldDescriptors({
        capabilities: { updateAny: canModify.value, createAny: canCreate.value },
        creatures: props.creatures || [],
        monsterRaces: props.monsterRaces || [],
    })
);
const fieldsConfig = computed(() =>
    createFieldsConfigFromDescriptors(monsterDescriptors.value, {
        capabilities: { updateAny: canModify.value },
        creatures: props.creatures || [],
        monsterRaces: props.monsterRaces || [],
    })
);
const defaultEntity = computed(() => createDefaultEntityFromDescriptors(monsterDescriptors.value));

// Bulk edit


const tableMeta = ref({});

const { openFullModal, openEdit } = useEntityIndexSurface("monsters", {
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
    const model = Monster.fromArray([raw])[0] || null;
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
    ModelClass: Monster,
    routeShowName: "entities.monsters.show",
    routeShowParam: "monster",
    canModify: () => canModify.value,
    openFullModal,
    openEdit,
});

// Handler pour les actions du tableau
const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    // Si c'est déjà une instance Monster, l'utiliser directement
    const model = targetEntity instanceof Monster ? targetEntity : Monster.fromArray([targetEntity])[0] || null;
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
            const cfg = getEntityRouteConfig('monster');
            const url = resolveEntityRouteUrl('monster', 'show', entityId, cfg);
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
    <Head title="Liste des Monstres" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Monstres</h1>
                <p class="text-primary-200 mt-2">Gérez les monstres du jeu</p>
            </div>
            <div class="flex gap-2">
                <Btn variant="ghost" @click="refreshToken++" title="Recharger le dataset">
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i>
                    Recharger
                </Btn>
                <Btn v-if="canAccess('contentManagement')" variant="ghost" @click="router.visit(route('admin.content.types.show', { kind: 'race' }))">
                    <i class="fa-solid fa-users mr-2"></i>
                    Races de monstres
                </Btn>
                <Btn v-if="canCreate" @click="handleCreate" color="primary">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer un monstre
                </Btn>
            </div>
        </div>
        <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="monsters"
                    :config="tableConfig"
                    server-side
                    :server-base-url="serverBaseUrl"
                    :refresh-token="refreshToken"
                    :initial-filter-values="indexTableFilters"
                    :response-adapter="getEntityResponseAdapter('monsters')"
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
            entity-type="monster"
            :fields-config="fieldsConfig"
            :default-entity="defaultEntity"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('monsters')"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />
    </div>
</template>
