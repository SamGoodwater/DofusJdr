<script setup>
/**
 * Spell Index Page
 *
 * @description
 * Page de liste des sorts avec tableau. Affichage / édition en modal via {@link EntitySurfaceHost}.
 *
 * @props {Object} spells - Collection paginée des sorts
 */
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Spell } from "@/Models/Entity/Spell";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";

import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityTanStackTable from '@/Pages/Organismes/table/EntityTanStackTable.vue';
import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { getEntityResponseAdapter } from "@/Entities/entity-registry";
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import {
    buildSpellFormFieldsConfig,
    SPELL_FORM_FIELD_SECTIONS_CREATE,
    getSpellCreateDefaultEntity,
    mergeSpellTypesFieldIntoSpellFormConfig,
} from "@/Entities/spell/spell-form-config";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";
import { useEntityIndexSurface } from "@/Composables/entity/useEntityIndexSurface";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";
import { normalizeIndexTableFilters } from "@/Composables/entity/useEntityIndexTableFilters";

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({})
    },
    spellTypes: {
        type: Array,
        default: () => []
    }
});

const { setPageTitle } = usePageTitle();
setPageTitle('Liste des Sorts');

// Permissions
const { canCreate: canCreatePermission, canUpdateAny } = usePermissions();
const canCreate = computed(() => canCreatePermission('spells'));
const canModify = computed(() => canUpdateAny('spells'));

// Bulk request
const { copyToClipboard } = useCopyToClipboard();

const createModalOpen = ref(false);
const selectedIds = ref([]);
const tableRows = ref([]);
const refreshToken = ref(0);
const tableMeta = ref({});

const { openFullModal, openEdit } = useEntityIndexSurface("spells", {
    getTableMeta: () => tableMeta.value,
});

// Configuration du tableau avec permissions et contexte
const tableConfig = computed(() => {
    const ctx = {
        capabilities: { 
            updateAny: canModify.value,
            createAny: canCreate.value,
        },
        spellTypes: props.spellTypes || [],
    };
    const descriptors = getSpellFieldDescriptors(ctx);
    const config = TableConfig.fromDescriptors(descriptors, ctx);
    return config.build(ctx);
});
// Pagination côté serveur pour les gros volumes (10k+ sorts)
const serverBaseUrl = computed(() => route('api.tables.spells'));
const indexTableFilters = computed(() => normalizeIndexTableFilters(props.filters));


/** Formulaire création (même structure que la fiche édition). */
const spellCreateFieldsConfig = computed(() =>
    mergeSpellTypesFieldIntoSpellFormConfig(
        buildSpellFormFieldsConfig({ includeReadonlyMeta: false }),
        props.spellTypes || [],
    ),
);
const spellCreateDefaultEntity = getSpellCreateDefaultEntity();

const handleTableLoaded = ({ rows, meta }) => {
    tableRows.value = Array.isArray(rows) ? rows : [];
    tableMeta.value = meta || {};
};

const handleRowDoubleClick = (row) => {
    const raw = row?.rowParams?.entity;
    if (!raw) return;
    const model = Spell.fromArray([raw])[0] || null;
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
    // Tableau alimenté par l’API TanStack : forcer un refetch après création (même si Inertia a déjà rechargé la page).
    refreshToken.value++;
};

const { handleKeyboardIntent } = useEntityIndexTableIntents({
    ModelClass: Spell,
    routeShowName: "entities.spells.show",
    routeShowParam: "spell",
    canModify: () => canModify.value,
    openFullModal,
    openEdit,
});

const handleCreateRequest = () => {
    if (canCreate.value) {
        createModalOpen.value = true;
    }
};

const handleTableAction = async (actionKey, entity, row) => {
    const targetEntity = entity || row?.rowParams?.entity;
    if (!targetEntity) return;
    
    // Si c'est déjà une instance Spell, l'utiliser directement
    const model = targetEntity instanceof Spell ? targetEntity : Spell.fromArray([targetEntity])[0] || null;
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
            const cfg = getEntityRouteConfig('spell');
            const url = resolveEntityRouteUrl('spell', 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de l'entité copié !");
            }
            break;
        }

        case 'download-pdf':
            break;
        case 'delete':
            if (window.confirm('Supprimer ce sort ? Il sera placé en corbeille (récupération possible côté admin).')) {
                router.delete(route('entities.spells.delete', { spell: entityId }, false), {
                    replace: true,
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
    <Head title="Liste des Sorts" />
    
    <div class="space-y-6 pb-8 w-full">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Liste des Sorts</h1>
                <p class="text-primary-200 mt-2">Gérez les sorts et magies</p>
            </div>
            <Btn v-if="canCreate" @click="handleCreate" color="primary">
                <i class="fa-solid fa-plus mr-2"></i>
                Créer un sort
            </Btn>
        </div>
        <div class="min-w-0 overflow-x-auto">
                <EntityTanStackTable
                    entity-type="spells"
                    :config="tableConfig"
                    server-side
                    :server-base-url="serverBaseUrl"
                    :initial-filter-values="indexTableFilters"
                    :refresh-token="refreshToken"
                    :response-adapter="getEntityResponseAdapter('spells')"
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
            entity-type="spell"
            :fields-config="spellCreateFieldsConfig"
            :default-entity="spellCreateDefaultEntity"
            :field-sections="SPELL_FORM_FIELD_SECTIONS_CREATE"
            :hidden-field-keys="['dofus_version']"
            :show-state-toolbar="false"
            :show-access-levels-in-footer="false"
            :create-allow-field-keys="getEntityCreateAllowFieldKeys('spells')"
            characteristics-group="spell"
            @close="handleCloseCreateModal"
            @created="handleEntityCreated"
        />

    </div>
</template>
