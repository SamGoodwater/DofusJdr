<script setup>
/**
 * NpcViewFull — Vue Full pour NPC
 *
 * @description
 * Fiche complète d’un PNJ (coquille narrative + kit Creature), sans panneau DofusDB.
 */
import { computed } from "vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import ImageViewer from "@/Pages/Molecules/data-display/ImageViewer.vue";
import CharacteristicsCard from "@/Pages/Organismes/data-display/CharacteristicsCard.vue";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import { buildCreatureCompetenceGroupsByPrimary } from "@/Utils/Entity/buildCreatureCompetenceGroups";
import { useCreatureResolvedStats } from "@/Composables/entity/useCreatureResolvedStats";
import { CHARACTERISTIC_CARD_DENSITY } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { useEntityActionDispatcher } from "@/Composables/entity/useEntityActionDispatcher";
import EntityDofusdbRefreshPanel from "@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getNpcFieldDescriptors } from "@/Entities/npc/npc-descriptors";
import { getEntityFieldShortLabel, shouldOmitLabelInMeta, resolveEntityFieldUi, resolveEntityBadgeUi } from "@/Utils/Entity/entity-view-ui";
import EntityLanguagesInline from "@/Pages/Molecules/entity/language/EntityLanguagesInline.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import MonsterCreatureSpellsList from "@/Pages/Molecules/entity/monster/MonsterCreatureSpellsList.vue";
import MonsterCreatureItemsList from "@/Pages/Molecules/entity/monster/MonsterCreatureItemsList.vue";
import PanoplyViewText from "@/Pages/Molecules/entity/panoply/PanoplyViewText.vue";
import ShopViewText from "@/Pages/Molecules/entity/shop/ShopViewText.vue";

const props = defineProps({
    npc: {
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
    titleTag: {
        type: String,
        default: "h2",
        validator: (v) => ["h1", "h2", "h3"].includes(v),
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    characteristicRuntime: { type: Object, default: null },
});

const actionsContext = computed(() =>
    props.inModal
        ? { inPanel: false, inModal: true, surface: "modal", viewMode: "full", modalMode: "view" }
        : { inPanel: false, inPage: true, surface: "page", viewMode: "full" },
);

const headerMode = computed(() => (props.inModal ? "compact" : "full"));

const emit = defineEmits(["edit", "copy-link", "download-pdf", "refresh", "view", "quick-view", "delete", "action"]);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf("npc");
const { dispatchEntityAction, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } =
    useEntityActionDispatcher("npcs", {
        onRefresh: () => router.reload({ only: ["npc", "characteristicRuntime"] }),
    });
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("npcs", "viewAny") || permissions.can("npc", "viewAny"),
        createAny: permissions.can("npcs", "createAny") || permissions.can("npc", "createAny"),
        updateAny: permissions.can("npcs", "updateAny") || permissions.can("npc", "updateAny"),
        deleteAny: permissions.can("npcs", "deleteAny") || permissions.can("npc", "deleteAny"),
        manageAny: permissions.can("npcs", "manageAny") || permissions.can("npc", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getNpcFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf || desc?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn("[NpcViewFull] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

const headlineFields = computed(() =>
    ["creature_level", "breed", "npc_role", "size", "state"].filter(canShowField),
);

const metaFields = computed(() =>
    ["creature_location", "creature_hostility", "age", "specialization"]
        .filter(canShowField)
        .filter((k) => !headlineFields.value.includes(k)),
);

const displayMetaFields = computed(() => [...headlineFields.value, ...metaFields.value]);

const userCanEditFields = computed(() =>
    ["read_level", "write_level"].filter(canShowField),
);

const technicalFields = computed(() =>
    ["created_at", "updated_at"].filter(canShowField),
);

const hasRelationsChips = computed(() => {
    const cell = props.npc.toCell("npc_summary_relations", {
        size: "lg",
        context: "extended",
    });
    const items = cell?.params?.items;
    return Array.isArray(items) && items.length > 0;
});

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: "npc",
    });

const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;
const getFieldTooltip = (fieldKey) => getFieldUi(fieldKey).tooltip;
const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon;
const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : undefined;
};

const getCell = (fieldKey) => {
    return props.npc.toCell(fieldKey, {
        size: "lg",
        context: "extended",
    });
};

const creatureData = computed(() => props.npc?.creature ?? props.npc?._data?.creature ?? null);
const creatureIdForStats = computed(() => creatureData.value?.id ?? null);
const { runtime: creatureRuntimeStats } = useCreatureResolvedStats(creatureIdForStats, "npc");
const effectiveRuntime = computed(
    () => props.characteristicRuntime ?? creatureRuntimeStats.value ?? null,
);
provideCharacteristicRuntime(effectiveRuntime);

const creatureCharacteristicsGroups = computed(() =>
    buildCreatureCharacteristicGroups(creatureData.value, {
        runtime: effectiveRuntime.value,
    }),
);
const competenceGroups = computed(() =>
    buildCreatureCompetenceGroupsByPrimary(creatureData.value, {
        includeZero: true,
        runtime: effectiveRuntime.value,
    }),
);
const hasCreatureCharacteristics = computed(() => !!creatureData.value);

const linkedLanguages = computed(() => {
    const raw = props.npc?._data?.languages ?? props.npc?.languages;
    return Array.isArray(raw) ? raw : [];
});
const hasLinkedLanguages = computed(() => linkedLanguages.value.length > 0);

const linkedCreatureTraits = computed(() => {
    const raw =
        props.npc?._data?.creature?.creatureTraits ??
        props.npc?.creature?.creatureTraits ??
        [];
    return Array.isArray(raw) ? raw : [];
});
const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);

const linkedSpells = computed(() => {
    const raw = creatureData.value?.spells;
    return Array.isArray(raw) ? raw : [];
});
const hasLinkedSpells = computed(() => linkedSpells.value.length > 0);

const linkedItems = computed(() => {
    const raw = creatureData.value?.items;
    return Array.isArray(raw) ? raw : [];
});
const hasLinkedItems = computed(() => linkedItems.value.length > 0);

const linkedPanoplies = computed(() => {
    const raw = props.npc?._data?.panoplies ?? props.npc?.panoplies;
    return Array.isArray(raw) ? raw : [];
});
const hasLinkedPanoplies = computed(() => linkedPanoplies.value.length > 0);

const linkedShop = computed(() => props.npc?._data?.shop ?? props.npc?.shop ?? null);

const storyText = computed(() => {
    const v = props.npc?.story ?? props.npc?._data?.story;
    return v && String(v).trim() ? String(v) : "";
});
const historicalText = computed(() => {
    const v = props.npc?.historical ?? props.npc?._data?.historical;
    return v && String(v).trim() ? String(v) : "";
});

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        creature_level: "warning",
        breed: "info",
        npc_role: "secondary",
        size: "secondary",
        state: "neutral",
        creature_hostility: "neutral",
        read_level: "primary",
        write_level: "secondary",
        created_at: "neutral",
        updated_at: "neutral",
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: resolveEntityFieldUi({
            fieldKey,
            descriptors: descriptors.value,
            tableMeta: props.tableMeta,
            entityType: "npc",
        }),
        localColorMap: colorMap,
    }).color;
};

const getBadgeAutoParams = (fieldKey) => {
    const { autoLabel, autoScheme, autoTone } = resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: resolveEntityFieldUi({
            fieldKey,
            descriptors: descriptors.value,
            tableMeta: props.tableMeta,
            entityType: "npc",
        }),
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
    const npcId = props.npc.id;
    if (!npcId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.npcs.show", { npc: npcId }));
            emit("view", props.npc);
            break;
        case "quick-view":
            emit("quick-view", props.npc);
            break;
        case "edit":
            await dispatchEntityAction("edit", props.npc, { context: actionsContext.value });
            emit("edit", props.npc);
            break;
        case "copy-link": {
            const cfg = getEntityRouteConfig("npc");
            const url = resolveEntityRouteUrl("npc", "show", npcId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien du PNJ copié !");
            }
            emit("copy-link", props.npc);
            break;
        }
        case "download-pdf":
            await downloadPdf(npcId);
            emit("download-pdf", props.npc);
            break;
        case "refresh":
            await dispatchEntityAction("refresh", props.npc);
            emit("refresh", props.npc);
            break;
        case "delete":
            emit("delete", props.npc);
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
                        v-if="npc.creature?.image"
                        :src="npc.creature.image"
                        :alt="npc.creature?.name || 'PNJ'"
                        :caption="npc.creature?.name || ''"
                        preload="hover"
                        :image-props="{
                            size: 'xl',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />
                    <div
                        v-else
                        class="entity-radius-box flex h-full w-full items-center justify-center border border-base-300 bg-base-200"
                    >
                        <Icon source="fa-solid fa-user" :alt="npc.creature?.name || 'PNJ'" size="xl" class="text-primary-400" />
                    </div>
                </div>
            </template>

            <template #title>
                <component :is="titleTag" class="min-w-0 flex-1 break-words text-2xl font-bold text-primary-100">
                    <CellRenderer :cell="getCell('creature_name')" ui-color="primary" />
                </component>
            </template>

            <template #mainInfos>
                <div v-if="displayMetaFields.length > 0" class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    <template v-for="fieldKey in displayMetaFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="flex min-w-0 items-start justify-between gap-2">
                                <div class="flex min-w-0 items-center gap-2">
                                    <Icon
                                        :source="getFieldIcon(fieldKey)"
                                        :alt="getFieldLabel(fieldKey)"
                                        size="xs"
                                        class="flex-shrink-0 text-primary-300"
                                        :style="getFieldIconStyle(fieldKey)"
                                    />
                                    <span
                                        v-if="!shouldOmitLabelInMeta(fieldKey)"
                                        class="truncate text-xs font-semibold uppercase text-primary-300"
                                    >
                                        {{ getEntityFieldShortLabel(fieldKey, getFieldLabel(fieldKey)) }}
                                    </span>
                                </div>
                                <CellRenderer
                                    :cell="getCell(fieldKey)"
                                    ui-color="primary"
                                    class="max-w-[18rem] whitespace-normal break-words [&_.inline-flex]:min-w-0"
                                />
                            </div>
                        </Tooltip>
                    </template>
                </div>
                <div
                    v-if="canShowField('npc_summary_relations') && hasRelationsChips"
                    class="mt-3 rounded-lg border border-base-300/50 bg-base-100/15 p-2.5"
                >
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-primary-400">
                        Relations & contenus
                    </p>
                    <CellRenderer
                        :cell="getCell('npc_summary_relations')"
                        ui-color="primary"
                        class="text-sm [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                    />
                </div>
            </template>

            <template #subtitle>
                <p v-if="npc.creature?.description" class="mt-2 break-words text-primary-300">
                    {{ npc.creature.description }}
                </p>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="npcs"
                        :entity="npc"
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

        <div
            v-if="hasLinkedCreatureTraits"
            class="space-y-2 rounded-box border border-base-300 bg-base-100/40 p-4"
            role="region"
            aria-label="Traits du PNJ"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Traits</h3>
            <CreatureTraitBadges :traits="linkedCreatureTraits" size="sm" />
        </div>
        <div
            v-else
            class="rounded-box border border-dashed border-base-300/70 bg-base-100/20 px-4 py-3 text-sm text-primary-300/80"
            role="status"
        >
            Aucun trait renseigné pour ce PNJ.
        </div>

        <div
            v-if="hasLinkedLanguages"
            class="space-y-2 rounded-box border border-base-300 bg-base-100/40 p-4"
            role="region"
            aria-label="Langues"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Langues</h3>
            <EntityLanguagesInline :languages="linkedLanguages" :show-label="false" />
        </div>
        <div
            v-else
            class="rounded-box border border-dashed border-base-300/70 bg-base-100/20 px-4 py-3 text-sm text-primary-300/80"
            role="status"
        >
            Aucune langue renseignée.
        </div>

        <section v-if="storyText" class="space-y-2 border-t border-base-300 pt-4" aria-label="Histoire">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Histoire</h3>
            <p class="whitespace-pre-wrap break-words text-sm text-primary-100">{{ storyText }}</p>
        </section>
        <section v-if="historicalText" class="space-y-2" aria-label="Historique">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Historique</h3>
            <p class="whitespace-pre-wrap break-words text-sm text-primary-100">{{ historicalText }}</p>
        </section>

        <section v-if="hasCreatureCharacteristics" class="border-t border-base-300 pt-4">
            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-primary-300">
                Caractéristiques
            </h3>
            <CharacteristicsCard
                :entity="creatureData"
                :groups="creatureCharacteristicsGroups"
                :density="inModal ? CHARACTERISTIC_CARD_DENSITY.labeled : CHARACTERISTIC_CARD_DENSITY.spacious"
                :runtime="effectiveRuntime"
            />
        </section>
        <section v-else class="border-t border-base-300 pt-4" role="status">
            <p class="text-sm text-primary-300/80">
                Pas de créature associée — les caractéristiques ne peuvent pas être affichées.
            </p>
        </section>

        <section
            v-if="competenceGroups.length"
            class="border-t border-base-300 pt-4"
            role="region"
            aria-label="Compétences"
        >
            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-primary-300">
                Compétences
            </h3>
            <CharacteristicsCard
                :entity="creatureData"
                :groups="competenceGroups"
                :density="inModal ? CHARACTERISTIC_CARD_DENSITY.labeled : CHARACTERISTIC_CARD_DENSITY.spacious"
                :runtime="effectiveRuntime"
            />
        </section>

        <section class="space-y-3 border-t border-base-300 pt-4" role="region" aria-label="Sorts de la créature">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Sorts</h3>
            <MonsterCreatureSpellsList
                v-if="hasLinkedSpells"
                :creature="creatureData"
                :table-meta="tableMeta"
                :characteristic-runtime="effectiveRuntime"
                section-class="rounded-box border border-base-300 bg-base-100/40 p-4"
            />
            <p
                v-else
                class="rounded-box border border-dashed border-base-300/70 bg-base-100/20 px-4 py-3 text-sm text-primary-300/80"
                role="status"
            >
                Aucun sort lié à ce PNJ.
            </p>
        </section>

        <section
            v-if="hasLinkedItems"
            class="space-y-3 border-t border-base-300 pt-4"
            role="region"
            aria-label="Équipements de la créature"
        >
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Équipements</h3>
            <MonsterCreatureItemsList
                :creature="creatureData"
                :table-meta="tableMeta"
                :characteristic-runtime="effectiveRuntime"
                section-class="rounded-box border border-base-300 bg-base-100/40 p-4"
            />
        </section>

        <section
            v-if="hasLinkedPanoplies"
            class="space-y-2 border-t border-base-300 pt-4"
            role="region"
            aria-label="Panoplies"
        >
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Panoplies</h3>
            <ul class="m-0 flex list-none flex-wrap gap-2 p-0">
                <li v-for="panoply in linkedPanoplies" :key="panoply.id">
                    <PanoplyViewText :panoply="panoply" />
                </li>
            </ul>
        </section>

        <section
            v-if="linkedShop"
            class="space-y-2 border-t border-base-300 pt-4"
            role="region"
            aria-label="Boutique"
        >
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Boutique</h3>
            <ShopViewText :shop="linkedShop" />
        </section>

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="border-t border-base-300 pt-3">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex min-w-0 items-center gap-2">
                            <Icon
                                :source="getFieldIcon(fieldKey)"
                                :alt="getFieldLabel(fieldKey)"
                                size="xs"
                                class="flex-shrink-0 text-primary-300"
                                :style="getFieldIconStyle(fieldKey)"
                            />
                            <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                            <span class="min-w-0 break-words">
                                <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                            </span>
                        </div>
                    </Tooltip>
                </template>
            </div>

            <div v-if="userCanEditFields.length > 0" class="mt-4">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-primary-300">Paramètres</div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                    <template v-for="fieldKey in userCanEditFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="inline-flex min-w-0 items-center gap-2">
                                <Icon
                                    :source="getFieldIcon(fieldKey)"
                                    :alt="getFieldLabel(fieldKey)"
                                    size="xs"
                                    class="flex-shrink-0 text-primary-300"
                                    :style="getFieldIconStyle(fieldKey)"
                                />
                                <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                                <span class="min-w-0 break-words">
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
