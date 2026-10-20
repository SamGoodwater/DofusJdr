<script setup>
/**
 * ConsumableViewFull — Vue Full pour Consumable
 *
 * @description
 * Vue complète d'un consommable avec toutes les informations affichées.
 * Layout aligné sur Resource : image + 3 lignes (nom+niveau+type / rareté+prix / description),
 * puis effet, ingrédients, bloc admin (write permission).
 *
 * @props {Consumable} consumable - Instance du modèle Consumable
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import CellRenderer from '@/Pages/Atoms/data-display/CellRenderer.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from '@/Pages/Molecules/entity/shared/EntityViewHeader.vue';
import ImageViewer from '@/Pages/Molecules/data-display/ImageViewer.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { useEntityActionDispatcher } from '@/Composables/entity/useEntityActionDispatcher';
import EntityDofusdbRefreshPanel from '@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { getConsumableFieldDescriptors } from '@/Entities/consumable/consumable-descriptors';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { getRarityConfig } from '@/Utils/Entity/SharedConstants';
import { resolveEntityFieldUi, resolveEntityBadgeUi } from '@/Utils/Entity/entity-view-ui';
import { buildKamasPriceTooltip } from '@/Utils/Entity/buildKamasPriceTooltip';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityFieldTooltip from '@/Pages/Molecules/entity/shared/EntityFieldTooltip.vue';
import EntityRuleNotes from '@/Pages/Molecules/entity/shared/EntityRuleNotes.vue';
import { consumableRuleNotes } from '@/Utils/Entity/consumableRuleNotes';
import ResourceIngredientsList from '@/Pages/Molecules/data-display/ResourceIngredientsList.vue';
import RichTextReadonlyView from '@/Pages/Molecules/data-display/RichTextReadonlyView.vue';

const props = defineProps({
    consumable: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    inModal: {
        type: Boolean,
        default: false,
    },
    tableMeta: { type: Object, default: () => ({}) },
});


const actionsContext = computed(() =>
    props.inModal
        ? { inPanel: false, inModal: true, surface: 'modal', viewMode: 'full', modalMode: 'view' }
        : { inPanel: false, inPage: true, surface: 'page', viewMode: 'full' },
);

const headerMode = computed(() => (props.inModal ? 'compact' : 'full'));

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'delete', 'action']);

const effectHtml = computed(() => {
    const raw = props.consumable?.effect ?? props.consumable?._data?.effect;
    if (raw == null || String(raw).trim() === '') return '';
    const s = String(raw);
    return s.includes('<') ? s : '';
});

const { copyToClipboard } = useCopyToClipboard();
const { dispatchEntityAction, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } =
    useEntityActionDispatcher('consumables', {
        onRefresh: () => router.reload(),
    });
const { downloadPdf } = useDownloadPdf('consumable');
const permissions = usePermissions();

const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can('consumables', 'viewAny'),
        createAny: permissions.can('consumables', 'createAny'),
        updateAny: permissions.can('consumables', 'updateAny'),
        deleteAny: permissions.can('consumables', 'deleteAny'),
        manageAny: permissions.can('consumables', 'manageAny'),
    },
    meta: { capabilities: {} },
}));

const descriptors = computed(() => getConsumableFieldDescriptors(ctx.value));

const ingredients = computed(() => {
    const raw = props.consumable?.resources ?? props.consumable?._data?.resources ?? [];
    return Array.isArray(raw) ? raw : [];
});

const ruleNotes = computed(() => consumableRuleNotes(props.consumable));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch {
            return false;
        }
    }
    return true;
};

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({ fieldKey, descriptors: descriptors.value, tableMeta: props.tableMeta, entityType: 'consumable' });
const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;
const getFieldTooltip = (fieldKey) => {
    if (fieldKey === 'price') {
        return buildKamasPriceTooltip({
            entityType: 'consumable',
            priceCalculated: props.consumable?.priceCalculated ?? props.consumable?._data?.price_calculated,
            priceCustom: props.consumable?.priceCustom ?? props.consumable?._data?.price_custom,
        });
    }
    return getFieldUi(fieldKey).tooltip;
};
const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon || 'fa-solid fa-info-circle';
const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : {};
};
const getFieldUnit = (fieldKey) => getFieldUi(fieldKey).characteristic?.unit ?? '';

const getCell = (fieldKey) =>
    props.consumable.toCell(fieldKey, { size: 'lg', context: 'extended' });

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        consumable_type: 'info',
        level: 'warning',
        rarity: 'auto',
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
        localColorMap: colorMap,
    }).color;
};

const levelDisplay = computed(() => {
    const v = props.consumable?.level ?? props.consumable?._data?.level;
    return v !== null && v !== undefined && v !== '' ? String(v) : '-';
});
const typeDisplay = computed(() => {
    const ct = props.consumable?.consumableType ?? props.consumable?._data?.consumableType;
    return ct?.name ?? ct?.label ?? '-';
});
const rarityDisplay = computed(() => {
    const r = props.consumable?.rarity ?? props.consumable?._data?.rarity ?? 0;
    const cfg = getRarityConfig(r);
    return cfg?.label ?? '-';
});
const rarityBadgeColor = computed(() => {
    const r = props.consumable?.rarity ?? props.consumable?._data?.rarity ?? 0;
    const cfg = getRarityConfig(r);
    return cfg?.color ?? 'neutral';
});

const isPlayable = computed(() => {
    const state = props.consumable?.state ?? props.consumable?._data?.state;
    return state === 'playable';
});

const canRecalculatePrice = computed(() =>
    Boolean(props.consumable?.can?.update ?? props.consumable?._data?.can?.update) && !isPlayable.value
);

function recalculatePrice() {
    const id = props.consumable?.id ?? props.consumable?._data?.id;
    if (!id) {
        return;
    }
    router.post(route('entities.consumables.recalculatePrice', { consumable: id }), {}, { preserveScroll: true });
}

const handleAction = async (actionKey) => {
    const consumableId = props.consumable.id;
    if (!consumableId) return;
    switch (actionKey) {
        case 'view':
            router.visit(route('entities.consumables.show', { consumable: consumableId }));
            emit('view', props.consumable);
            break;
        case 'edit':
            await dispatchEntityAction("edit", props.consumable, { context: actionsContext.value });
            emit('edit', props.consumable);
            break;
        
        case 'copy-link': {
            const cfg = getEntityRouteConfig('consumable');
            const url = resolveEntityRouteUrl('consumable', 'show', consumableId, cfg);
            if (url) await copyToClipboard(`${window.location.origin}${url}`, 'Lien copié !');
            emit('copy-link', props.consumable);
            break;
        }
        case 'download-pdf':
            await downloadPdf(consumableId);
            emit('download-pdf', props.consumable);
            break;
        case 'refresh': {
            await dispatchEntityAction('refresh', props.consumable);
            break;
        }
        case 'delete':
            emit('delete', props.consumable);
            break;
    }
};
</script>

<template>
    <div class="space-y-4">
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div class="group relative w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56">
                    <ImageViewer
                        v-if="consumable.image"
                        :src="consumable.image"
                        :alt="consumable.name || 'Consommable'"
                        :caption="consumable.name || ''"
                        preload="hover"
                        :image-props="{ size: 'xl', rounded: 'lg', fit: 'cover', class: 'w-full h-full' }"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-flask" :alt="consumable.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">{{ consumable.name }}</h2>
                        <template v-if="canShowField('level')">
                            <EntityFieldTooltip
                                field-key="level"
                                entity-type="consumable"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge
                                    :color="getBadgeColor('level')"
                                    :auto-label="levelDisplay"
                                    auto-scheme="level"
                                    auto-tone="mid"
                                    size="sm"
                                >
                                    Nvx {{ levelDisplay }}
                                </Badge>
                            </EntityFieldTooltip>
                        </template>
                        <template v-if="canShowField('consumable_type')">
                            <EntityFieldTooltip
                                field-key="consumable_type"
                                entity-type="consumable"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge
                                    color="auto"
                                    :auto-label="typeDisplay"
                                    auto-scheme="labelHash"
                                    auto-tone="light"
                                    size="sm"
                                >
                                    {{ typeDisplay }}
                                </Badge>
                            </EntityFieldTooltip>
                        </template>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                        <template v-if="canShowField('rarity')">
                            <EntityFieldTooltip
                                field-key="rarity"
                                entity-type="consumable"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge
                                    :color="rarityBadgeColor"
                                    :auto-label="String(consumable.rarity ?? 0)"
                                    auto-scheme="rarity"
                                    auto-tone="mid"
                                    size="sm"
                                >
                                    {{ rarityDisplay }}
                                </Badge>
                            </EntityFieldTooltip>
                        </template>
                        <template v-if="canShowField('price')">
                            <span class="inline-flex items-center gap-1">
                            <Tooltip :content="getFieldTooltip('price')" placement="top">
                                <span class="inline-flex items-center gap-1" :style="getFieldIconStyle('price')">
                                    <Icon :source="getFieldIcon('price')" :alt="getFieldLabel('price')" size="xs" />
                                    <span class="font-semibold">{{ getFieldLabel('price') }}</span><span> {{ consumable.price ?? 0 }}{{ getFieldUnit('price') ? ` ${getFieldUnit('price')}` : '' }}</span>
                                </span>
                            </Tooltip>
                            <Tooltip v-if="canRecalculatePrice" content="Actualiser le prix automatique" placement="top">
                                <Btn
                                    type="button"
                                    variant="ghost"
                                    size="xs"
                                    circle
                                    aria-label="Actualiser le prix"
                                    @click="recalculatePrice"
                                >
                                    <i class="fa-solid fa-rotate" aria-hidden="true"></i>
                                </Btn>
                            </Tooltip>
                            </span>
                        </template>
                    </div>
                </div>
            </template>

            <template #mainInfos />
            <template #subtitle>
                <p v-if="consumable.description" class="text-primary-300 mt-2 break-words">{{ consumable.description }}</p>
                <EntityRuleNotes
                    :notes="ruleNotes"
                    notes-class="mt-1.5 text-sm leading-snug text-primary-200/80"
                />
            </template>

            <template #actions>
                <div class="flex items-center gap-2">
                    <EntityActions
                        v-if="showActions"
                        entity-type="consumable"
                        :entity="consumable"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="actionsContext"
                        @action="handleAction"
                    />
                </div>
            </template>
        </EntityViewHeader>

        <!-- Effet -->
        <div v-if="canShowField('effect') && (consumable.effect || consumable._data?.effect)" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Effet</h3>
            <div class="text-primary-200 p-3 rounded-lg bg-base-200/50 entity-radius-box">
                <RichTextReadonlyView
                    v-if="effectHtml"
                    :html="effectHtml"
                    :enable-rich-references="true"
                />
                <CellRenderer v-else :cell="getCell('effect')" ui-color="primary" />
            </div>
        </div>

        <!-- Ingrédients -->
        <div v-if="ingredients.length > 0" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Ingrédients</h3>
            <ResourceIngredientsList :ingredients="ingredients" />
        </div>
    </div>
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

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
