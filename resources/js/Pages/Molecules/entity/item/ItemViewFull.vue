<script setup>
/**
 * ItemViewFull — Vue Full pour Item (équipement)
 *
 * @description
 * Vue complète d'un équipement avec toutes les informations affichées.
 * Layout aligné sur Resource : image + 3 lignes (nom+niveau+type / rareté+poids+prix / description),
 * puis effet, bonus, ingrédients, bloc admin (write permission).
 *
 * @props {Item} item - Instance du modèle Item
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from '@/Pages/Molecules/entity/shared/EntityViewHeader.vue';
import ImageViewer from '@/Pages/Molecules/data-display/ImageViewer.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { useEntityActionDispatcher } from '@/Composables/entity/useEntityActionDispatcher';
import EntityDofusdbRefreshPanel from '@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { getItemFieldDescriptors } from '@/Entities/item/item-descriptors';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { getRarityConfig, getRoleConfig } from '@/Utils/Entity/SharedConstants';
import { resolveEntityFieldUi, resolveEntityBadgeUi } from '@/Utils/Entity/entity-view-ui';
import EntityFieldTooltip from '@/Pages/Molecules/entity/shared/EntityFieldTooltip.vue';
import EntityRuleNotes from '@/Pages/Molecules/entity/shared/EntityRuleNotes.vue';
import { itemRuleNotes } from '@/Utils/Entity/itemRuleNotes';
import ResourceIngredientsList from '@/Pages/Molecules/data-display/ResourceIngredientsList.vue';
import RichTextReadonlyView from '@/Pages/Molecules/data-display/RichTextReadonlyView.vue';
import CharacteristicEffectsGrid from '@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue';
import { buildCharacteristicEffectCell } from '@/Composables/entity/useCharacteristicEffectFormatter';
import ItemPanoplyMark from '@/Pages/Molecules/entity/item/ItemPanoplyMark.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import { buildKamasPriceTooltip } from '@/Utils/Entity/buildKamasPriceTooltip';

const props = defineProps({
    item: { type: Object, required: true },
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
    const raw = props.item?.effect ?? props.item?._data?.effect;
    if (raw == null || String(raw).trim() === '') return '';
    const s = String(raw);
    return s.includes('<') ? s : '';
});

const { copyToClipboard } = useCopyToClipboard();
const { dispatchEntityAction, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } =
    useEntityActionDispatcher('items', {
        onRefresh: () => router.reload(),
    });
const { downloadPdf } = useDownloadPdf('item');
const permissions = usePermissions();

const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can('items', 'viewAny'),
        createAny: permissions.can('items', 'createAny'),
        updateAny: permissions.can('items', 'updateAny'),
        deleteAny: permissions.can('items', 'deleteAny'),
        manageAny: permissions.can('items', 'manageAny'),
    },
    meta: { capabilities: {} },
}));

const descriptors = computed(() => getItemFieldDescriptors(ctx.value));
const autoUpdateValue = computed(() => {
    const v = props.item?.auto_update ?? props.item?._data?.auto_update;
    return typeof v === 'boolean' ? v : null;
});

const ingredients = computed(() => {
    const raw = props.item?.resources ?? props.item?._data?.resources ?? [];
    return Array.isArray(raw) ? raw : [];
});

const ruleNotes = computed(() => itemRuleNotes(props.item));

const bonusItems = computed(() => {
    const bonus = props.item?.bonus ?? props.item?._data?.bonus;
    const effect = props.item?.effect ?? props.item?._data?.effect;
    const cell = buildCharacteristicEffectCell({
        rawValues: [bonus, effect],
        options: { context: 'extended' },
        sourceGroups: ['item', 'panoply'],
        size: 'lg',
    });
    return cell?.type === 'chips' ? cell.params?.items || [] : [];
});

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
    resolveEntityFieldUi({ fieldKey, descriptors: descriptors.value, tableMeta: props.tableMeta, entityType: 'item' });
const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;
const getFieldTooltip = (fieldKey) => {
    if (fieldKey === 'price') {
        return buildKamasPriceTooltip({
            entityType: 'item',
            priceCalculated: props.item?.priceCalculated ?? props.item?._data?.price_calculated,
            priceCustom: props.item?.priceCustom ?? props.item?._data?.price_custom,
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
    props.item.toCell(fieldKey, { size: 'lg', context: 'extended' });

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        item_type: 'info',
        level: 'warning',
        rarity: 'auto',
        read_level: 'primary',
        write_level: 'secondary',
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
        localColorMap: colorMap,
    }).color;
};

const levelDisplay = computed(() => {
    const v = props.item?.level ?? props.item?._data?.level;
    return v !== null && v !== undefined && v !== '' ? String(v) : '-';
});
const typeDisplay = computed(() => {
    const it = props.item?.itemType ?? props.item?._data?.itemType;
    return it?.name ?? it?.label ?? '-';
});
const rarityDisplay = computed(() => {
    const r = props.item?.rarity ?? props.item?._data?.rarity ?? 0;
    const cfg = getRarityConfig(r);
    return cfg?.label ?? '-';
});
const rarityBadgeColor = computed(() => {
    const r = props.item?.rarity ?? props.item?._data?.rarity ?? 0;
    const cfg = getRarityConfig(r);
    return cfg?.color ?? 'neutral';
});
const readLevelLabel = computed(() => {
    const v = props.item?.read_level ?? props.item?._data?.read_level ?? 0;
    const cfg = getRoleConfig(v);
    return cfg?.label ?? '-';
});
const writeLevelLabel = computed(() => {
    const v = props.item?.write_level ?? props.item?._data?.write_level ?? 0;
    const cfg = getRoleConfig(v);
    return cfg?.label ?? '-';
});

/** Prix total affiché uniquement si &gt; 0 (kamas entiers). */
const priceKamasDisplay = computed(() => {
    const raw = props.item?.price ?? props.item?._data?.price;
    if (raw === null || raw === undefined || raw === '') {
        return null;
    }
    const n = Math.round(Number(raw));
    return Number.isFinite(n) && n > 0 ? n : null;
});

const canRecalculatePrice = computed(() =>
    Boolean(props.item?.can?.update ?? props.item?._data?.can?.update)
);

function recalculatePrice() {
    const itemId = props.item?.id ?? props.item?._data?.id;
    if (!itemId) {
        return;
    }
    router.post(route('entities.items.recalculatePrice', { item: itemId }), {}, { preserveScroll: true });
}

const handleAction = async (actionKey) => {
    const itemId = props.item.id;
    if (!itemId) return;
    switch (actionKey) {
        case 'view':
            router.visit(route('entities.items.show', { item: itemId }));
            emit('view', props.item);
            break;
        case 'edit':
            await dispatchEntityAction("edit", props.item, { context: actionsContext.value });
            emit('edit', props.item);
            break;
        
        case 'copy-link': {
            const cfg = getEntityRouteConfig('item');
            const url = resolveEntityRouteUrl('item', 'show', itemId, cfg);
            if (url) await copyToClipboard(`${window.location.origin}${url}`, 'Lien copié !');
            emit('copy-link', props.item);
            break;
        }
        case 'download-pdf':
            await downloadPdf(itemId);
            emit('download-pdf', props.item);
            break;
        case 'refresh': {
            await dispatchEntityAction('refresh', props.item);
            break;
        }
        case 'delete':
            emit('delete', props.item);
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
                        v-if="item.image"
                        :src="item.image"
                        :alt="item.name || 'Équipement'"
                        :caption="item.name || ''"
                        preload="hover"
                        :image-props="{ size: 'xl', rounded: 'lg', fit: 'cover', class: 'w-full h-full' }"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-sword" :alt="item.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">{{ item.name }}</h2>
                        <template v-if="canShowField('level')">
                            <EntityFieldTooltip
                                field-key="level"
                                entity-type="item"
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
                        <template v-if="canShowField('item_type')">
                            <EntityFieldTooltip
                                field-key="item_type"
                                entity-type="item"
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
                                entity-type="item"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge
                                    :color="rarityBadgeColor"
                                    :auto-label="String(item.rarity ?? 0)"
                                    auto-scheme="rarity"
                                    auto-tone="mid"
                                    size="sm"
                                >
                                    {{ rarityDisplay }}
                                </Badge>
                            </EntityFieldTooltip>
                        </template>
                        <template v-if="canShowField('weight')">
                            <Tooltip :content="getFieldTooltip('weight')" placement="top">
                                <span class="inline-flex items-center gap-1" :style="getFieldIconStyle('weight')">
                                    <Icon :source="getFieldIcon('weight')" :alt="getFieldLabel('weight')" size="xs" />
                                    <span class="font-semibold">{{ getFieldLabel('weight') }}</span><span> {{ item.weight ?? '-' }}{{ getFieldUnit('weight') ? ` ${getFieldUnit('weight')}` : '' }}</span>
                                </span>
                            </Tooltip>
                        </template>
                        <template v-if="canShowField('price') && (priceKamasDisplay != null || canRecalculatePrice)">
                            <span class="inline-flex items-center gap-1">
                            <Tooltip :content="getFieldTooltip('price')" placement="top">
                                <span class="inline-flex items-center gap-1" :style="getFieldIconStyle('price')">
                                    <Icon :source="getFieldIcon('price')" :alt="getFieldLabel('price')" size="xs" />
                                    <span class="font-semibold">{{ getFieldLabel('price') }}</span><span> {{ priceKamasDisplay ?? 0 }}{{ getFieldUnit('price') ? ` ${getFieldUnit('price')}` : '' }}</span>
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
                <p v-if="item.description" class="text-primary-300 mt-2 break-words">{{ item.description }}</p>
                <EntityRuleNotes
                    :notes="ruleNotes"
                    notes-class="mt-1.5 text-sm leading-snug text-primary-200/80"
                />
            </template>

            <template #actions>
                <div class="flex items-center gap-2">
                    <EntityActions
                        v-if="showActions"
                        entity-type="item"
                        :entity="item"
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

        <!-- Effet (texte riche éventuel) -->
        <div v-if="canShowField('effect') && effectHtml" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Effet</h3>
            <div class="text-primary-200 p-3 rounded-lg bg-base-200/50 entity-radius-box">
                <RichTextReadonlyView
                    :html="effectHtml"
                    :enable-rich-references="true"
                />
            </div>
        </div>

        <!-- Bonus -->
        <div v-if="canShowField('bonus') && bonusItems.length > 0" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Bonus</h3>
            <div class="text-primary-200 p-3 rounded-lg bg-base-200/50 entity-radius-box">
                <CharacteristicEffectsGrid :items="bonusItems" label-mode="full" />
            </div>
        </div>

        <!-- Panoplie -->
        <ItemPanoplyMark :item="item" density="full" :table-meta="tableMeta" />

        <!-- Ingrédients -->
        <div v-if="ingredients.length > 0" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Ingrédients</h3>
            <ResourceIngredientsList :ingredients="ingredients" />
        </div>

        <!-- Bloc admin (write permission) -->
        <section
            v-if="userCanEdit && (canShowField('read_level') || canShowField('write_level') || canShowField('dofus_version') || canShowField('dofusdb_id') || canShowField('auto_update') || canShowField('created_by') || canShowField('created_at') || canShowField('updated_at'))"
            role="region"
            aria-label="Administration"
            class="rounded-box overflow-hidden border border-base-300 bg-base-200/50 border-glass-primary-md"
        >
            <div class="px-5 py-4 flex items-center gap-3 border-b border-base-300/80 bd-glass-xs">
                <div class="flex w-10 h-10 shrink-0 items-center justify-center rounded-lg bg-primary-500/20 text-primary-400">
                    <Icon source="fa-solid fa-shield-halved" size="sm" aria-hidden="true" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-primary-200">Administration</h3>
                    <p class="text-xs text-base-content/60 mt-0.5">Paramètres techniques</p>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div v-if="canShowField('read_level') || canShowField('write_level')" class="space-y-3">
                    <h4 class="text-xs font-medium uppercase tracking-wider text-base-content/60">Accès</h4>
                    <div class="flex flex-wrap gap-2">
                        <Badge
                            v-if="canShowField('read_level')"
                            :color="getBadgeColor('read_level')"
                            :auto-label="String(item.read_level ?? 0)"
                            auto-scheme="level"
                            size="xs"
                            variant="soft"
                        >
                            <Icon source="fa-solid fa-eye" size="xs" class="mr-1" />
                            {{ readLevelLabel }}
                        </Badge>
                        <Badge
                            v-if="canShowField('write_level')"
                            :color="getBadgeColor('write_level')"
                            :auto-label="String(item.write_level ?? 0)"
                            auto-scheme="level"
                            size="xs"
                            variant="soft"
                        >
                            <Icon source="fa-solid fa-pen-to-square" size="xs" class="mr-1" />
                            {{ writeLevelLabel }}
                        </Badge>
                    </div>
                </div>
                <div v-if="canShowField('dofus_version') || canShowField('dofusdb_id') || canShowField('auto_update')" class="space-y-3 pt-3 border-t border-base-300/60">
                    <h4 class="text-xs font-medium uppercase tracking-wider text-base-content/60">Source</h4>
                    <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                        <div v-if="canShowField('dofus_version')" class="inline-flex items-center gap-2">
                            <Icon source="fa-solid fa-gamepad" size="xs" class="text-primary-400 opacity-80" />
                            <span class="text-base-content/70">Dofus</span>
                            <span class="font-mono text-xs text-base-content">{{ item.dofus_version ?? '—' }}</span>
                        </div>
                        <div v-if="canShowField('dofusdb_id')" class="inline-flex items-center gap-2">
                            <Icon source="fa-solid fa-link" size="xs" class="text-primary-400 opacity-80" />
                            <span class="text-base-content/70">DofusDB ID</span>
                            <span class="font-mono text-xs text-base-content">{{ item.dofusdb_id ?? '—' }}</span>
                        </div>
                        <div v-if="canShowField('auto_update')" class="inline-flex items-center gap-2">
                            <Icon source="fa-solid fa-arrows-rotate" size="xs" class="text-primary-400 opacity-80" />
                            <span class="text-base-content/70">Auto-update</span>
                            <Icon
                                v-if="autoUpdateValue !== null"
                                :source="autoUpdateValue ? 'fa-solid fa-check-circle' : 'fa-solid fa-times-circle'"
                                size="sm"
                                :class="autoUpdateValue ? 'text-success' : 'text-error'"
                                :alt="autoUpdateValue ? 'Oui' : 'Non'"
                            />
                            <span v-else class="text-base-content/60">—</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
