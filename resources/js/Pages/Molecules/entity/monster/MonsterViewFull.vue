<script setup>
/**
 * MonsterViewFull — Vue Full pour Monster
 * 
 * @description
 * Vue complète d'un monstre avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Monster} monster - Instance du modèle Monster
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from "vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import ImageViewer from "@/Pages/Molecules/data-display/ImageViewer.vue";
import CharacteristicsCard from "@/Pages/Organismes/data-display/CharacteristicsCard.vue";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import { buildCreatureCompetenceGroupsByPrimary } from "@/Utils/Entity/buildCreatureCompetenceGroups";
import { useCreatureResolvedStats } from "@/Composables/entity/useCreatureResolvedStats";
import { CHARACTERISTIC_CARD_DENSITY } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { useEntityActionDispatcher } from '@/Composables/entity/useEntityActionDispatcher';
import EntityDofusdbRefreshPanel from '@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { getEntityFieldShortLabel, shouldOmitLabelInMeta, resolveEntityFieldUi, resolveEntityBadgeUi } from "@/Utils/Entity/entity-view-ui";
import MonsterBossMark from "@/Pages/Molecules/entity/monster/MonsterBossMark.vue";
import EntityLanguagesInline from "@/Pages/Molecules/entity/language/EntityLanguagesInline.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import MonsterCreatureSpellsList from "@/Pages/Molecules/entity/monster/MonsterCreatureSpellsList.vue";
import MonsterCreatureItemsList from "@/Pages/Molecules/entity/monster/MonsterCreatureItemsList.vue";

const props = defineProps({
    monster: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    },
    inModal: {
        type: Boolean,
        default: false,
    },
    /** Meta du tableau (ex. characteristics.creature.byDbColumn) pour la carte caractéristiques */
    tableMeta: {
        type: Object,
        default: () => ({})
    },
    /** Runtime caractéristiques / formules (optionnel, aligné autres entités). */
    characteristicRuntime: { type: Object, default: null },
});


const actionsContext = computed(() =>
    props.inModal
        ? { inPanel: false, inModal: true, surface: 'modal', viewMode: 'full', modalMode: 'view' }
        : { inPanel: false, inPage: true, surface: 'page', viewMode: 'full' },
);

const headerMode = computed(() => (props.inModal ? 'compact' : 'full'));

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('monster');
const { dispatchEntityAction, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } =
    useEntityActionDispatcher('monsters', {
        onRefresh: () => router.reload({ only: ['monster', 'characteristicRuntime'] }),
    });
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('monsters', 'viewAny'),
        createAny: permissions.can('monsters', 'createAny'),
        updateAny: permissions.can('monsters', 'updateAny'),
        deleteAny: permissions.can('monsters', 'deleteAny'),
        manageAny: permissions.can('monsters', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getMonsterFieldDescriptors(ctx.value));

const autoUpdateValue = computed(() => {
    const v = props.monster?.auto_update ?? props.monster?._data?.auto_update;
    return typeof v === 'boolean' ? v : null;
});

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[MonsterViewFull] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

const headlineFields = computed(() =>
    ["creature_level", "monster_race", "size", "state"].filter(canShowField),
);

const isBoss = computed(() =>
    Boolean(props.monster?.isBoss ?? props.monster?._data?.is_boss),
);

const legendaryPa = computed(
    () => props.monster?.bossPa ?? props.monster?._data?.boss_pa ?? null,
);

const metaFields = computed(() =>
    ["creature_hostility"].filter(canShowField).filter((k) => !headlineFields.value.includes(k)),
);

const displayMetaFields = computed(() => [...headlineFields.value, ...metaFields.value]);

const userCanEditFields = computed(() =>
    ["read_level", "write_level", "dofus_version", "auto_update"].filter(canShowField),
);

const hasRelationsChips = computed(() => {
    const cell = props.monster.toCell("monster_summary_relations", {
        size: "lg",
        context: "extended",
    });
    const items = cell?.params?.items;
    return Array.isArray(items) && items.length > 0;
});

const technicalFields = computed(() => ([
    'dofusdb_id',
    'official_id',
    'created_at',
    'updated_at',
].filter(canShowField)));

const getFieldLabel = (fieldKey) => {
    return resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: 'monster',
    }).label;
};

const getFieldTooltip = (fieldKey) => resolveEntityFieldUi({
    fieldKey,
    descriptors: descriptors.value,
    tableMeta: props.tableMeta,
    entityType: 'monster',
}).tooltip;

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: 'monster',
    });

const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon;

const getFieldIconStyle = (fieldKey) => {
    const color = resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: 'monster',
    }).color;
    return color ? { color } : undefined;
};

const getCell = (fieldKey) => {
    return props.monster.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};

const creatureData = computed(() => {
    const m = props.monster;
    return m?.creature ?? m?.data?.creature ?? null;
});

const creatureIdForStats = computed(() => creatureData.value?.id ?? null);
const { runtime: creatureRuntimeStats } = useCreatureResolvedStats(creatureIdForStats);

const creatureCharacteristicsGroups = computed(() =>
    buildCreatureCharacteristicGroups(creatureData.value, {
        runtime: creatureRuntimeStats.value,
    }),
);
const competenceGroups = computed(() =>
    buildCreatureCompetenceGroupsByPrimary(creatureData.value, {
        includeZero: true,
        runtime: creatureRuntimeStats.value,
    }),
);
const hasCreatureCharacteristics = computed(() => !!creatureData.value);

const linkedLanguages = computed(() => {
    const raw = props.monster?._data?.languages ?? props.monster?.languages;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedLanguages = computed(() => linkedLanguages.value.length > 0);

const linkedCreatureTraits = computed(() => {
    const c = creatureData.value;
    const raw =
        c?.creatureTraits ??
        c?.creature_traits ??
        props.monster?.creatureTraits ??
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

const linkedSpellInvocations = computed(() => {
    const raw = props.monster?._data?.spellInvocations ?? props.monster?.spellInvocations;
    return Array.isArray(raw) ? raw : [];
});

const hasSpellInvocations = computed(() => linkedSpellInvocations.value.length > 0);

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        creature_level: "warning",
        monster_race: "info",
        size: "secondary",
        is_boss: "warning",
        state: "neutral",
        creature_hostility: "neutral",
        boss_pa: "warning",
        read_level: "primary",
        write_level: "secondary",
        dofus_version: "secondary",
        auto_update: "warning",
        dofusdb_id: "neutral",
        official_id: "neutral",
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
            entityType: 'monster',
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
            entityType: 'monster',
        }),
    });
    return { autoLabel, autoScheme, autoTone };
};

const asTextCell = (cell) => {
    if (!cell) return { type: 'text', value: '-', params: {} };
    const v = cell?.value;
    return { type: 'text', value: (v === null || typeof v === 'undefined' || String(v) === '') ? '-' : String(v), params: cell?.params || {} };
};

const handleAction = async (actionKey) => {
    const monsterId = props.monster.id;
    if (!monsterId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.monsters.show', { monster: monsterId }));
            emit('view', props.monster);
            break;
        case 'quick-view':
            emit('quick-view', props.monster);
            break;
        case 'edit':
            await dispatchEntityAction("edit", props.monster, { context: actionsContext.value });
            emit('edit', props.monster);
            break;
        
        case 'copy-link': {
            const cfg = getEntityRouteConfig('monster');
            const url = resolveEntityRouteUrl('monster', 'show', monsterId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien du monstre copié !");
            }
            emit('copy-link', props.monster);
            break;
        }
        case 'download-pdf':
            await downloadPdf(monsterId);
            emit('download-pdf', props.monster);
            break;
        case 'refresh': {
            await dispatchEntityAction('refresh', props.monster);
            break;
        }
        case 'delete':
            emit('delete', props.monster);
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
                        v-if="monster.creature?.image"
                        :src="monster.creature.image"
                        :alt="monster.creature?.name || 'Monstre'"
                        :caption="monster.creature?.name || ''"
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
                        class="w-full h-full entity-radius-box bg-base-200 flex items-center justify-center border border-base-300"
                    >
                        <Icon source="fa-solid fa-dragon" :alt="monster.creature?.name || 'Monstre'" size="xl" class="text-primary-400" />
                    </div>
                </div>
            </template>

            <template #title>
                <div class="flex w-full min-w-0 items-start gap-3">
                    <MonsterBossMark
                        v-if="isBoss && canShowField('is_boss')"
                        :tooltip="getFieldTooltip('is_boss')"
                        :legendary-pa="legendaryPa"
                        size-class="h-10 w-10"
                        class="mt-1 shrink-0"
                    />
                    <h2 class="min-w-0 flex-1 break-words text-2xl font-bold text-primary-100">
                        <CellRenderer :cell="getCell('creature_name')" ui-color="primary" />
                    </h2>
                </div>
            </template>

            <template #mainInfos>
                <div v-if="displayMetaFields.length > 0" class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <template v-for="fieldKey in displayMetaFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="flex items-start justify-between gap-2 min-w-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <Icon :source="getFieldIcon(fieldKey)" :alt="getFieldLabel(fieldKey)" size="xs" class="text-primary-300 flex-shrink-0" :style="getFieldIconStyle(fieldKey)" />
                                    <span
                                        v-if="!shouldOmitLabelInMeta(fieldKey)"
                                        class="text-xs uppercase font-semibold text-primary-300 truncate"
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
                    v-if="canShowField('monster_summary_relations') && hasRelationsChips"
                    class="mt-3 rounded-lg border border-base-300/50 bg-base-100/15 p-2.5"
                >
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-primary-400 mb-2">
                        Relations & contenus
                    </p>
                    <CellRenderer
                        :cell="getCell('monster_summary_relations')"
                        ui-color="primary"
                        class="text-sm [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                    />
                </div>
            </template>

            <template #subtitle>
                <p v-if="monster.creature?.description" class="text-primary-300 mt-2 break-words">
                    {{ monster.creature.description }}
                </p>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="monsters"
                        :entity="monster"
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
            class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-2"
            role="region"
            aria-label="Traits du monstre"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Traits</h3>
            <CreatureTraitBadges :traits="linkedCreatureTraits" size="sm" />
        </div>
        <div
            v-else
            class="rounded-box border border-dashed border-base-300/70 bg-base-100/20 px-4 py-3 text-sm text-primary-300/80"
            role="status"
        >
            Aucun trait renseigné pour ce monstre.
        </div>

        <div
            v-if="hasLinkedLanguages"
            class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-2"
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

        <!-- Carte caractéristiques complète (mode étendu) -->
        <section v-if="hasCreatureCharacteristics" class="pt-4 border-t border-base-300">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300 mb-3">
                Caractéristiques
            </h3>
            <CharacteristicsCard
                :entity="creatureData"
                :groups="creatureCharacteristicsGroups"
                :density="inModal ? CHARACTERISTIC_CARD_DENSITY.labeled : CHARACTERISTIC_CARD_DENSITY.spacious"
                :runtime="creatureRuntimeStats"
            />
        </section>
        <section
            v-else
            class="pt-4 border-t border-base-300"
            role="status"
        >
            <p class="text-sm text-primary-300/80">
                Pas de créature associée — les caractéristiques ne peuvent pas être affichées.
            </p>
        </section>

        <section
            v-if="competenceGroups.length"
            class="pt-4 border-t border-base-300"
            role="region"
            aria-label="Compétences"
        >
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300 mb-3">
                Compétences
            </h3>
            <CharacteristicsCard
                :entity="creatureData"
                :groups="competenceGroups"
                :density="inModal ? CHARACTERISTIC_CARD_DENSITY.labeled : CHARACTERISTIC_CARD_DENSITY.spacious"
                :runtime="creatureRuntimeStats"
            />
        </section>

        <section
            class="pt-4 border-t border-base-300 space-y-3"
            role="region"
            aria-label="Sorts de la créature"
        >
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">
                Sorts
            </h3>
            <MonsterCreatureSpellsList
                v-if="hasLinkedSpells"
                :creature="creatureData"
                :table-meta="tableMeta"
                :characteristic-runtime="characteristicRuntime || creatureRuntimeStats"
                section-class="rounded-box border border-base-300 bg-base-100/40 p-4"
            />
            <p
                v-else
                class="rounded-box border border-dashed border-base-300/70 bg-base-100/20 px-4 py-3 text-sm text-primary-300/80"
                role="status"
            >
                Aucun sort lié à la créature de ce monstre.
            </p>
        </section>

        <section
            v-if="hasLinkedItems"
            class="pt-4 border-t border-base-300 space-y-3"
            role="region"
            aria-label="Équipements de la créature"
        >
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">
                Équipements
            </h3>
            <MonsterCreatureItemsList
                :creature="creatureData"
                :table-meta="tableMeta"
                :characteristic-runtime="characteristicRuntime || creatureRuntimeStats"
                section-class="rounded-box border border-base-300 bg-base-100/40 p-4"
            />
        </section>

        <section
            v-if="hasSpellInvocations"
            class="pt-2 space-y-2"
            role="region"
            aria-label="Invocations"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">
                Sorts d’invocation
            </h3>
            <ul class="flex list-none flex-wrap gap-2 p-0 m-0">
                <li
                    v-for="spell in linkedSpellInvocations"
                    :key="spell.id"
                    class="rounded-badge border border-base-300 bg-base-100/50 px-2.5 py-1 text-xs text-primary-100"
                >
                    {{ spell.name || `Sort #${spell.id}` }}
                </li>
            </ul>
        </section>

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex items-center gap-2 min-w-0">
                            <Icon :source="getFieldIcon(fieldKey)" :alt="getFieldLabel(fieldKey)" size="xs" class="text-primary-300 flex-shrink-0" :style="getFieldIconStyle(fieldKey)" />
                            <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                            <span class="min-w-0 break-words">
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
                                <Icon :source="getFieldIcon(fieldKey)" :alt="getFieldLabel(fieldKey)" size="xs" class="text-primary-300 flex-shrink-0" :style="getFieldIconStyle(fieldKey)" />
                                <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                                <span class="min-w-0 break-words">
                                    <template v-if="fieldKey === 'auto_update'">
                                        <Icon
                                            v-if="autoUpdateValue !== null"
                                            :source="autoUpdateValue ? 'fa-solid fa-check' : 'fa-solid fa-xmark'"
                                            :alt="autoUpdateValue ? 'Oui' : 'Non'"
                                            size="sm"
                                            :class="autoUpdateValue ? 'text-success-800' : 'text-error-800'"
                                        />
                                        <span v-else>—</span>
                                    </template>
                                    <template v-else>
                                        <Badge
                                            :color="getBadgeColor(fieldKey)"
                                            :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                                            :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                                            :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                                            size="sm"
                                        >
                                            <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                        </Badge>
                                    </template>
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
