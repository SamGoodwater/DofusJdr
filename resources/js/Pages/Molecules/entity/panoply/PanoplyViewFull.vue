<script setup>
/**
 * PanoplyViewFull — Vue Full pour Panoply
 *
 * @description
 * Alignée sur BreedViewFull : EntityViewHeader, métas via EntityPropertyDisplay, bandeaux techniques / niveaux.
 *
 * @props {Panoply} panoply - Instance du modèle Panoply
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import PanoplyEquipmentTextList from "@/Pages/Molecules/entity/panoply/PanoplyEquipmentTextList.vue";
import PanoplyBonusTiers from "@/Pages/Molecules/entity/panoply/PanoplyBonusTiers.vue";
import PanoplyThumb from "@/Pages/Molecules/entity/panoply/PanoplyThumb.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import EntityFieldTooltip from "@/Pages/Molecules/entity/shared/EntityFieldTooltip.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { resolveEntityFieldUi, resolveEntityBadgeUi } from "@/Utils/Entity/entity-view-ui";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { useEntityActionDispatcher } from "@/Composables/entity/useEntityActionDispatcher";
import EntityDofusdbRefreshPanel from "@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getPanoplyFieldDescriptors } from "@/Entities/panoply/panoply-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { visiblePanoplyBonusTiers } from "@/Utils/entity/panoplyBonus";

const props = defineProps({
    panoply: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    inModal: {
        type: Boolean,
        default: false,
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    characteristicRuntime: { type: Object, default: null },
});


const actionsContext = computed(() =>
    props.inModal
        ? { inPanel: false, inModal: true, surface: 'modal', viewMode: 'full', modalMode: 'view' }
        : { inPanel: false, inPage: true, surface: 'page', viewMode: 'full' },
);

const headerMode = computed(() => (props.inModal ? 'compact' : 'full'));

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits([
    "edit",
    "copy-link",
    "download-pdf",
    "refresh",
    "view",
    "quick-view",
    "delete",
    "action",
]);

const { copyToClipboard } = useCopyToClipboard();
const { dispatchEntityAction, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } =
    useEntityActionDispatcher("panoplies", {
        onRefresh: () => router.reload(),
    });
const { downloadPdf } = useDownloadPdf("panoply");
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("panoplies", "viewAny"),
        createAny: permissions.can("panoplies", "createAny"),
        updateAny: permissions.can("panoplies", "updateAny"),
        deleteAny: permissions.can("panoplies", "deleteAny"),
        manageAny: permissions.can("panoplies", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getPanoplyFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn("[PanoplyViewFull] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

const metaFields = computed(() =>
    ["panoply_summary_relations", "state"].filter(canShowField)
);

const displayMetaFields = computed(() => metaFields.value);

const linkedItems = computed(() => {
    const raw = props.panoply?.items ?? props.panoply?._data?.items;
    return Array.isArray(raw) ? raw : [];
});

const bonusRaw = computed(() => props.panoply?.bonus ?? props.panoply?._data?.bonus ?? null);
const hasBonus = computed(() => visiblePanoplyBonusTiers(bonusRaw.value).length > 0);

const levelDisplay = computed(() => {
    const v = props.panoply?.level ?? props.panoply?._data?.level;
    return v !== null && v !== undefined && v !== "" ? String(v) : null;
});

const userCanEditFields = computed(() => ["read_level", "write_level"].filter(canShowField));

const technicalFields = computed(() =>
    ["dofusdb_id", "created_by", "created_at", "updated_at"].filter(canShowField)
);

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: "panoply",
    });

const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;

const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon;

const getFieldTooltip = (fieldKey) => getFieldUi(fieldKey).tooltip;

const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : undefined;
};

const getCell = (fieldKey) => {
    return props.panoply.toCell(fieldKey, {
        size: "lg",
        context: "extended",
    });
};

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        items_count: "warning",
        level: "warning",
        read_level: "primary",
        write_level: "secondary",
        dofusdb_id: "neutral",
        created_by: "neutral",
        created_at: "neutral",
        updated_at: "neutral",
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
        localColorMap: colorMap,
    }).color;
};

const getBadgeAutoParams = (fieldKey) => {
    const { autoLabel, autoScheme, autoTone } = resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
    });
    return { autoLabel, autoScheme, autoTone };
};

const asTextCell = (cell) => {
    if (!cell) return { type: "text", value: "-", params: {} };
    const v = cell?.value;
    return {
        type: "text",
        value: v === null || typeof v === "undefined" || String(v) === "" ? "-" : String(v),
        params: cell?.params || {},
    };
};

const handleAction = async (actionKey) => {
    const panoplyId = props.panoply.id;
    if (!panoplyId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.panoplies.show", { panoply: panoplyId }));
            emit("view", props.panoply);
            break;
        case "edit":
            await dispatchEntityAction("edit", props.panoply, { context: actionsContext.value });
            emit("edit", props.panoply);
            break;
        
        case "copy-link": {
            const cfg = getEntityRouteConfig("panoply");
            const url = resolveEntityRouteUrl("panoply", "show", panoplyId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la panoplie copié !");
            }
            emit("copy-link", props.panoply);
            break;
        }
        case "download-pdf":
            await downloadPdf(panoplyId);
            emit("download-pdf", props.panoply);
            break;
        case "refresh": {
            await dispatchEntityAction("refresh", props.panoply);
            break;
        }
        case "delete":
            emit("delete", props.panoply);
            break;
    }
};
</script>

<template>
    <div class="space-y-4">
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div class="w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56 overflow-hidden rounded-box border border-base-300/60 bg-base-200">
                    <PanoplyThumb
                        size="fill"
                        :items="linkedItems"
                        :label="panoply.name || 'Panoplie'"
                    />
                </div>
            </template>

            <template #title>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-2xl font-bold text-primary-100 wrap-break-word">{{ panoply.name }}</h2>
                    <EntityFieldTooltip
                        v-if="canShowField('level') && levelDisplay"
                        field-key="level"
                        entity-type="panoply"
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
                </div>
            </template>

            <template #subtitle>
                <p v-if="panoply.description" class="text-primary-300 mt-2 wrap-break-word">{{ panoply.description }}</p>
            </template>

            <template #mainInfos>
                <div
                    v-if="linkedItems.length"
                    class="mt-3"
                >
                    <div class="text-[0.625rem] font-semibold uppercase tracking-wide text-base-content/60 mb-1">
                        Équipements
                    </div>
                    <PanoplyEquipmentTextList :items="linkedItems" :table-meta="tableMeta" />
                </div>
                <div
                    v-if="hasBonus && canShowField('bonus')"
                    class="mt-3"
                    data-cy="panoply-full-bonus"
                >
                    <div class="text-[0.625rem] font-semibold uppercase tracking-wide text-base-content/60 mb-2">
                        Bonus de panoplie
                    </div>
                    <PanoplyBonusTiers
                        :bonus="bonusRaw"
                        layout="columns"
                        label-mode="full"
                    />
                </div>
                <div v-if="displayMetaFields.length > 0" class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <EntityPropertyDisplay
                        v-for="fieldKey in displayMetaFields"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="panoply"
                        entity-type="panoply"
                        display-mode="extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="sm"
                        class="max-w-[18rem] whitespace-normal wrap-break-word"
                    />
                </div>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="panoplies"
                        :entity="panoply"
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

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex items-center gap-2 min-w-0">
                            <Icon
                                :source="getFieldIcon(fieldKey)"
                                size="xs"
                                class="text-primary-300 shrink-0"
                                :style="getFieldIconStyle(fieldKey)"
                            />
                            <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                            <span class="min-w-0 wrap-break-word">
                                <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                            </span>
                        </div>
                    </Tooltip>
                </template>
            </div>

            <div v-if="userCanEditFields.length > 0" class="mt-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-primary-300 mb-2">Paramètres</div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                    <template v-for="fieldKey in userCanEditFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="inline-flex items-center gap-2 min-w-0">
                                <Icon
                                    :source="getFieldIcon(fieldKey)"
                                    size="xs"
                                    class="text-primary-300 shrink-0"
                                    :style="getFieldIconStyle(fieldKey)"
                                />
                                <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                                <span class="min-w-0 wrap-break-word">
                                    <Badge
                                        :color="getBadgeColor(fieldKey)"
                                        :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                                        :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                                        :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                                        size="sm"
                                    >
                                        <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                    </Badge>
                                </span>
                            </div>
                        </Tooltip>
                    </template>
                </div>
            </div>
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
