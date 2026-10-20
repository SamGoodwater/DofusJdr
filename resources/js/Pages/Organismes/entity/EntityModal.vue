<script setup>
/**
 * EntityModal Organism
 * 
 * @description
 * Modal pour afficher une entité avec les 4 vues possibles
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 * @props {String} view - Vue à afficher ('full', 'minimal', 'text'), défaut 'full'
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {Boolean} useStoredFormat - Utiliser le format stocké dans localStorage (défaut: true)
 * @emit close - Événement émis lors de la fermeture
 */
import { computed, defineAsyncComponent, shallowRef, watch } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import ConfirmModal from '@/Pages/Molecules/action/ConfirmModal.vue';
import EntityDofusdbRefreshPanel from '@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useEntityViewFormat } from '@/Composables/store/useEntityViewFormat';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { normalizeEntityType } from '@/Entities/entity-registry';
import { useEntityActionDispatcher } from '@/Composables/entity/useEntityActionDispatcher';
import { resolveEntityViewComponent } from '@/Utils/entity/resolveEntityViewComponent';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    view: {
        type: String,
        default: null,
        validator: (v) => !v || ['full', 'minimal', 'text'].includes(v)
    },
    open: {
        type: Boolean,
        default: false
    },
    useStoredFormat: {
        type: Boolean,
        default: true
    },
    /** Meta renvoyée par le tableau (ex. characteristics.creature.byDbColumn pour les monstres) */
    tableMeta: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close', 'edit', 'expand', 'copy-link', 'download-pdf', 'refresh', 'delete']);

// Préférences UI / PDF : type normalisé pluriel (spell | spells → spells)
const normalizedEntityType = normalizeEntityType(props.entityType);

// Utiliser le format stocké si useStoredFormat est true et que view n'est pas fourni
const { viewFormat, setViewFormat, availableFormats, minimalDisplayMode, setMinimalDisplayMode, availableMinimalDisplayModes } = useEntityViewFormat(normalizedEntityType);
const currentView = computed(() => {
    if (props.view) {
        return props.view;
    }
    if (props.useStoredFormat) {
        return viewFormat.value;
    }
    return 'full';
});

const modalSize = computed(() => {
    const sizes = {
        full: 'xl',
        minimal: 'md',
        text: 'sm',
    };
    return sizes[currentView.value] || 'xl';
});

const handleClose = () => {
    emit('close');
};

/**
 * Récupère le nom de l'entité en gérant les modèles et objets bruts
 */
const getEntityName = () => {
    // Si c'est une instance de modèle, utiliser le getter name
    if (props.entity && typeof props.entity._data !== 'undefined') {
        return props.entity.name || props.entity.title || 'Entité';
    }
    // Sinon, accès direct
    return props.entity?.name || props.entity?.title || 'Entité';
};

// Handlers pour les actions
const { downloadPdf } = useDownloadPdf(normalizedEntityType);

/** Pluriel normalisé (ex. spells) — actions, reload Inertia */
const entityTypePlural = computed(() => normalizeEntityType(props.entityType));

const { dispatchEntityAction, deleteConfirm, confirmPendingDelete, cancelPendingDelete, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } = useEntityActionDispatcher(entityTypePlural, {
    onOpenPage: (entity) => {
        emit('expand', entity);
        handleClose();
    },
    // Édition : le parent (EntitySurfaceHost) remplace l’affichage — ne pas fermer ici.
    onEditPage: (entity) => {
        emit('edit', entity);
    },
    openEditModal: (entity) => {
        emit('edit', entity);
    },
    actionContext: () => ({ inModal: true, modalMode: 'view' }),
    onCopyLink: (entity) => emit('copy-link', entity),
    onRefresh: (entity) => emit('refresh', entity),
    onDeleted: (entity) => {
        emit('delete', entity);
        emit('refresh', entity);
        handleClose();
    },
});

// Charger dynamiquement le composant de vue approprié
const ViewComponent = shallowRef(null);

// Mapper les types d'entités vers leurs noms de composants
const ENTITY_COMPONENT_MAP = {
    'resources': 'Resource',
    'items': 'Item',
    'consumables': 'Consumable',
    'spells': 'Spell',
    'monsters': 'Monster',
    'npcs': 'Npc',
    'breeds': 'Breed',
    'campaigns': 'Campaign',
    'scenarios': 'Scenario',
    'conditions': 'Condition',
    'panoplies': 'Panoply',
    'capabilities': 'Capability',
    'creature-traits': 'CreatureTrait',
    'specializations': 'Specialization',
    'resource-types': 'ResourceType',
    'shops': 'Shop',
};

// Fonction pour charger le composant de vue
const loadViewComponent = async () => {
    try {
        const component = await resolveEntityViewComponent(props.entityType, currentView.value);
        if (component) {
            ViewComponent.value = defineAsyncComponent(() => Promise.resolve(component));
        } else {
            ViewComponent.value = null;
        }
    } catch (error) {
        console.error(`[EntityModal] Erreur lors du chargement du composant pour ${props.entityType}/${currentView.value}:`, error);
        ViewComponent.value = null;
    }
};

// Charger le composant uniquement quand le modal est ouvert (évite les résolutions inutiles)
watch(
    [() => props.open, () => props.entityType, currentView],
    ([open]) => {
        if (open) {
            loadViewComponent();
        }
    },
    { immediate: true }
);

// Props passées au composant de vue (computed pour éviter recalcul à chaque rendu)
const componentProps = computed(() => {
    const normalizedType = normalizeEntityType(props.entityType);
    const entityName = ENTITY_COMPONENT_MAP[normalizedType];

    if (!entityName) {
        return { entity: props.entity, showActions: false };
    }

    const propName = entityName.charAt(0).toLowerCase() + entityName.slice(1);

    const common = {
        [propName]: props.entity,
        showActions: false,
        ...(Object.keys(props.tableMeta || {}).length > 0 ? { tableMeta: props.tableMeta } : {}),
    };

    if (currentView.value === 'minimal') {
        return {
            ...common,
            displayMode: minimalDisplayMode.value,
        };
    }

    if (currentView.value === 'full') {
        return {
            ...common,
            inModal: true,
            titleTag: 'h2',
        };
    }

    return common;
});

const handleAction = async (actionKey, entity) => {
    const targetEntity = entity || props.entity;
    const entityId = targetEntity?.id ?? props.entity?.id ?? null;
    if (!entityId) return;

    switch (actionKey) {
        case 'download-pdf':
            await downloadPdf(entityId);
            emit('download-pdf', targetEntity);
            break;
        default:
            await dispatchEntityAction(actionKey, targetEntity);
    }
};
</script>

<template>
    <Modal 
        :open="open" 
        :size="modalSize"
        placement="middle-center"
        close-on-esc
        close-on-outside-click
        :close-on-button="false"
        @close="handleClose">
        
        <template #header>
            <div class="flex min-w-0 w-full items-center gap-4">
                <h3 class="min-w-0 truncate text-lg font-bold text-primary-100">
                    {{ getEntityName() }}
                </h3>
                <div class="flex min-w-8 flex-1 items-center justify-end gap-2">
                    <Dropdown
                        v-if="!view && useStoredFormat"
                        class="shrink-0"
                        placement="bottom-end"
                        variant="glass"
                        color="primary"
                        size="sm"
                        :close-on-content-click="false"
                        aria-label="Options d’affichage"
                    >
                        <template #trigger>
                            <Btn size="sm" variant="glass" color="primary" aria-label="Options d’affichage">
                                <Icon source="fa-solid fa-sliders" size="sm" />
                            </Btn>
                        </template>
                        <template #content>
                            <div class="p-3 space-y-3 min-w-64">
                                <div class="text-xs font-semibold uppercase tracking-wide text-primary-200/80">
                                    Format
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <Btn
                                        v-for="f in availableFormats"
                                        :key="f.value"
                                        size="sm"
                                        :variant="viewFormat === f.value ? 'glass' : 'ghost'"
                                        color="primary"
                                        class="justify-start gap-2"
                                        @click="setViewFormat(f.value)"
                                    >
                                        <Icon :source="f.icon" size="sm" />
                                        <span class="truncate">{{ f.label }}</span>
                                    </Btn>
                                </div>

                                <div v-if="viewFormat === 'minimal'" class="pt-2 border-t border-base-300">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-primary-200/80 mb-2">
                                        Minimal
                                    </div>
                                    <div class="space-y-1">
                                        <Btn
                                            v-for="m in availableMinimalDisplayModes"
                                            :key="m.value"
                                            size="sm"
                                            :variant="minimalDisplayMode === m.value ? 'glass' : 'ghost'"
                                            color="primary"
                                            class="w-full justify-start"
                                            @click="setMinimalDisplayMode(m.value)"
                                        >
                                            {{ m.label }}
                                        </Btn>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Dropdown>

                    <div class="ml-auto flex min-w-8 flex-1 justify-end">
                        <EntityActions
                            :entity-type="entityTypePlural"
                            :entity="entity"
                            format="buttons"
                            display="icon-only"
                            size="sm"
                            color="primary"
                            :context="{ inModal: true, modalMode: 'view' }"
                            @action="handleAction"
                        />
                    </div>
                    <Btn
                        circle
                        size="sm"
                        variant="ghost"
                        class="shrink-0"
                        aria-label="Fermer"
                        @click="handleClose"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </Btn>
                </div>
            </div>
        </template>

        <div>
            <component
                v-if="ViewComponent"
                :is="ViewComponent"
                :key="`${entityType}-${currentView}`"
                v-bind="componentProps"
            />
            <div v-else class="text-center text-primary-300 py-8">
                <p>Chargement de la vue...</p>
            </div>
        </div>

        <template #actions>
            <Btn @click="handleClose">Fermer</Btn>
        </template>
    </Modal>

    <ConfirmModal
        :open="deleteConfirm.open"
        :title="deleteConfirm.title"
        :message="deleteConfirm.message"
        :details="deleteConfirm.details"
        confirm-label="Mettre en corbeille"
        confirm-color="error"
        confirm-icon="fa-solid fa-trash-can"
        @confirm="confirmPendingDelete"
        @cancel="cancelPendingDelete"
        @close="cancelPendingDelete"
    />
    <EntityDofusdbRefreshPanel
        :open="refreshConfirm.open"
        :loading="refreshConfirm.loading"
        :applying="refreshConfirm.applying"
        :preview="refreshConfirm.preview"
        :error="refreshConfirm.error"
        :playable="refreshConfirm.playable"
        :entity-label="refreshConfirm.entityLabel"
        :entity-type="refreshConfirm.entityType"
        :show-dofusdb="refreshConfirm.showDofusdb"
        :show-ai="refreshConfirm.showAi"
        :show-json="refreshConfirm.showJson"
        :ai-brief="refreshConfirm.aiBrief"
        :ai-submitting="refreshConfirm.aiSubmitting"
        :ai-error="refreshConfirm.aiError"
        :ai-success="refreshConfirm.aiSuccess"
        :ai-estimate="refreshConfirm.aiEstimate"
        :ai-usage="refreshConfirm.aiUsage"
        :ai-action-label="refreshConfirm.aiActionLabel"
        :diff="refreshConfirm.diff"
        :diff-busy="refreshConfirm.diffBusy"
        @confirm="confirmPendingRefresh"
        @save-diff="confirmUpdateDiffSave"
        @restore-diff="confirmUpdateDiffRestore"
        @close="cancelPendingRefresh"
        @update:ai-brief="(v) => (refreshConfirm.aiBrief = v)"
        @convert="submitAiConvert"
        @inject="submitAiInject"
    />
</template>

