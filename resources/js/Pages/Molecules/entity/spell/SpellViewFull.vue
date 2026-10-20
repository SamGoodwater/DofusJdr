<script setup>
/**
 * SpellViewFull — Vue Full pour Spell
 *
 * @description
 * Fiche sort : en-tête (image, niveau, nom), identité (types, catégorie, élément, magie/physique, rituel, réaction),
 * description, utilisation, résolution, effets structurés (journal), puis métadonnées techniques.
 *
 * @props {Spell} spell - Instance du modèle Spell
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import CellRenderer from '@/Pages/Atoms/data-display/CellRenderer.vue';
import EntityPropertyDisplay from '@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from '@/Pages/Molecules/entity/shared/EntityViewHeader.vue';
import ImageViewer from '@/Pages/Molecules/data-display/ImageViewer.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import SpellEffectsJournal from '@/Pages/Molecules/entity/spell/SpellEffectsJournal.vue';
import SpellUsageBlock from '@/Pages/Molecules/entity/spell/SpellUsageBlock.vue';
import { resolveEntityFieldUi, resolveEntityBadgeUi } from '@/Utils/Entity/entity-view-ui';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { useEntityActionDispatcher } from '@/Composables/entity/useEntityActionDispatcher';
import EntityDofusdbRefreshPanel from '@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { getSpellFieldDescriptors } from '@/Entities/spell/spell-descriptors';
import { getByCharacteristicKey } from '@/Composables/store/useCharacteristicsStore';
import { getCharacteristicColorStyle, resolveDef } from '@/Composables/entity/useCharacteristicDisplay';
import { resolveSpellCharacteristicKey } from '@/Composables/entity/useSpellAbilityCharacteristic';
import { provideCharacteristicRuntime } from '@/Composables/entity/characteristicRuntimeContext';
import { spellTypesCellHasRenderableContent } from '@/Utils/Entity/spellTypeVisual.js';

const RESOLUTION_LABELS = Object.freeze({
    attack_roll: "Jet d'attaque (vs CA)",
    saving_throw: 'Jet de sauvegarde',
    auto_success: 'Réussite automatique',
});

const props = defineProps({
    spell: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    /** Balise du titre principal (h1 sur page fiche, h2 en modal). */
    titleTag: {
        type: String,
        default: 'h2',
        validator: (v) => ['h1', 'h2', 'h3'].includes(v),
    },
    /** Payload runtime (ex. Inertia `characteristicRuntime`) pour tooltips formules — transmis aux EntityPropertyDisplay */
    characteristicRuntime: {
        type: Object,
        default: null,
    },
    /** true dans EntityModal — header dense */
    inModal: {
        type: Boolean,
        default: false,
    },
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
const { dispatchEntityAction, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } =
    useEntityActionDispatcher('spells', {
        onRefresh: () => router.reload(),
    });
const { downloadPdf } = useDownloadPdf('spell');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('spells', 'viewAny'),
        createAny: permissions.can('spells', 'createAny'),
        updateAny: permissions.can('spells', 'updateAny'),
        deleteAny: permissions.can('spells', 'deleteAny'),
        manageAny: permissions.can('spells', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getSpellFieldDescriptors(ctx.value));

const autoUpdateValue = computed(() => {
    const v = props.spell?.auto_update ?? props.spell?._data?.auto_update;
    return typeof v === 'boolean' ? v : null;
});

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[SpellViewFull] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

const identityFieldKeys = computed(() =>
    ['spell_types', 'category', 'element'].filter(canShowField),
);

const userCanEditFields = computed(() => ['auto_update', 'read_level', 'write_level'].filter(canShowField));

const technicalFields = computed(() =>
    ['dofusdb_id', 'official_id', 'created_by', 'created_at', 'updated_at'].filter(canShowField),
);

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: 'spell',
    });

const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;

const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon;

const getFieldTooltip = (fieldKey) => getFieldUi(fieldKey).tooltip;

const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : undefined;
};

const getCell = (fieldKey) =>
    props.spell.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        level: 'warning',
        auto_update: 'warning',
        read_level: 'primary',
        write_level: 'secondary',
        dofusdb_id: 'neutral',
        official_id: 'neutral',
        created_by: 'neutral',
        created_at: 'neutral',
        updated_at: 'neutral',
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
    if (!cell) return { type: 'text', value: '-', params: {} };
    const v = cell?.value;
    return {
        type: 'text',
        value: v === null || typeof v === 'undefined' || String(v) === '' ? '-' : String(v),
        params: cell?.params || {},
    };
};

const magicDef = computed(() =>
    resolveDef('is_magic_spell', props.spell?.isMagic, { sourceGroups: ['spell'] }),
);

const effectsDefinitions = computed(() =>
    Array.isArray(props.spell?.effectsDefinitions) ? props.spell.effectsDefinitions : [],
);

const spellDegreesPayload = computed(() => props.spell?.spellDegrees || { degrees: [], default_degree_id: null });

const activeDegree = ref(null);

function onActiveDegreeChange(deg) {
    activeDegree.value = deg;
}

/**
 * Entité pour la méta PA/PO/LoS : clone Spell avec `_data` surchargé par le degré actif.
 *
 * @returns {import('@/Models/Entity/Spell').Spell|object}
 */
const usageEntity = computed(() => {
    const base = props.spell;
    const propsDeg = activeDegree.value?.properties;
    if (!base || !propsDeg) {
        return base;
    }
    const merged = { ...(base._data || {}) };
    for (const [k, v] of Object.entries(propsDeg)) {
        if (v !== null && v !== undefined) {
            merged[k] = v;
        }
    }
    merged._active_degree_properties = propsDeg;
    // Réutiliser la classe Spell si disponible sur l’instance.
    const Ctor = base.constructor;
    if (typeof Ctor === 'function' && Ctor !== Object) {
        try {
            return new Ctor(merged);
        } catch {
            /* fallback objet plat */
        }
    }
    return { ...base, _data: merged, ...merged };
});

const resolutionLabel = computed(() => {
    const mode = usageEntity.value?.resolutionMode;
    return RESOLUTION_LABELS[mode] || mode;
});

const attackCharKeyResolved = computed(() =>
    resolveSpellCharacteristicKey(usageEntity.value?.attackCharacteristicKey, 'attack'),
);

const saveCharKeyResolved = computed(() =>
    resolveSpellCharacteristicKey(usageEntity.value?.saveCharacteristicKey, 'save'),
);

const attackCharDef = computed(() => {
    const k = attackCharKeyResolved.value;
    return k ? getByCharacteristicKey('spell', k) : null;
});

const saveCharDef = computed(() => {
    const k = saveCharKeyResolved.value;
    return k ? getByCharacteristicKey('spell', k) : null;
});

const ritualDef = computed(() =>
    resolveDef('ritual_available_spell', usageEntity.value?.isRitual, { sourceGroups: ['spell'] }),
);

const showRitualBadge = computed(() => usageEntity.value?.isRitual === true);

const ritualTooltipText = computed(() => {
    const d = ritualDef.value;
    if (d?._resolvedSubtitle) return d._resolvedSubtitle;
    const t = d?.helper || (Array.isArray(d?.descriptions) ? d.descriptions.join(' ') : d?.descriptions) || '';
    return t || 'Ce sort peut être lancé comme un rituel (incantation prolongée).';
});

const reactionDef = computed(() => {
    const val = typeof usageEntity.value?.allowsReaction === 'boolean'
        ? usageEntity.value.allowsReaction
        : Boolean(usageEntity.value?._data?.allows_reaction);
    return resolveDef('allows_reaction_spell', val, { sourceGroups: ['spell'] });
});

const showReactionBadge = computed(() => {
    const s = usageEntity.value;
    if (typeof s?.allowsReaction === 'boolean') {
        return s.allowsReaction === true;
    }
    return Boolean(s?._data?.allows_reaction);
});

/** Contenu structuré pour l’infobulle « réaction » (règles PA / round). */
const reactionTooltipBlocks = computed(() => {
    const d = reactionDef.value;
    const title = d?.short_name || d?.name || 'Réaction';
    const lines = [];
    if (d?._resolvedSubtitle) {
        lines.push(d._resolvedSubtitle);
    }
    const helper = typeof d?.helper === 'string' && d.helper.trim() !== '' ? d.helper.trim() : '';
    if (helper && helper !== d?._resolvedSubtitle) {
        lines.push(helper);
    }
    const descRaw = d?.descriptions;
    const desc =
        Array.isArray(descRaw) ? descRaw.map((p) => String(p).trim()).filter(Boolean).join(' ') : String(descRaw || '').trim();
    if (desc && desc !== helper && desc !== d?._resolvedSubtitle) {
        lines.push(desc);
    }
    if (lines.length === 0) {
        lines.push(
            'Ce sort peut être lancé en réaction pendant un round de combat.',
            'Chaque créature dispose d’une réaction par round, utilisable à tout moment du round.',
            'Les PA dépensés pour une réaction ne sont pas réattribués au début du tour suivant.',
        );
    }
    return { title, lines };
});

function characteristicTooltipText(meta) {
    if (!meta) return '';
    return (
        meta.helper ||
        (Array.isArray(meta.descriptions) ? meta.descriptions.join(' ') : meta.descriptions) ||
        ''
    );
}

const showSpellTypesCell = computed(() =>
    spellTypesCellHasRenderableContent(getCell('spell_types')),
);

const hasStructuredEffects = computed(() => effectsDefinitions.value.length > 0);
const hasLegacyEffectText = computed(
    () => props.spell?.effect != null && String(props.spell.effect).trim() !== '',
);

const handleAction = async (actionKey) => {
    const spellId = props.spell.id;
    if (!spellId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.spells.show', { spell: spellId }));
            emit('view', props.spell);
            break;
        case 'edit':
            await dispatchEntityAction("edit", props.spell, { context: actionsContext.value });
            emit('edit', props.spell);
            break;
        
        case 'copy-link': {
            const cfg = getEntityRouteConfig('spell');
            const url = resolveEntityRouteUrl('spell', 'show', spellId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, 'Lien du sort copié !');
            }
            emit('copy-link', props.spell);
            break;
        }
        case 'download-pdf':
            await downloadPdf(spellId);
            emit('download-pdf', props.spell);
            break;
        case 'refresh': {
            await dispatchEntityAction('refresh', props.spell);
            break;
        }
        case 'delete':
            emit('delete', props.spell);
            break;
    }
};
</script>

<template>
    <div class="space-y-4">
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div class="group relative w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56">
                    <div class="absolute top-2 right-2 z-20 transition-opacity duration-150 group-hover:opacity-0">
                        <Badge
                            :color="getBadgeColor('level')"
                            :auto-label="getBadgeAutoParams('level').autoLabel"
                            :auto-scheme="getBadgeAutoParams('level').autoScheme"
                            :auto-tone="getBadgeAutoParams('level').autoTone"
                            size="sm"
                        >
                            <CellRenderer :cell="asTextCell(getCell('level'))" ui-color="primary" />
                        </Badge>
                    </div>

                    <ImageViewer
                        v-if="spell.image"
                        :source="spell.image"
                        :alt="spell.name || 'Sort'"
                        :caption="spell.name || ''"
                        preload="hover"
                        :image-props="{
                            size: 'xl',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />

                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-wand-magic-sparkles" :alt="spell.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <component :is="titleTag" class="text-2xl font-bold text-primary-100 break-words">
                    {{ spell.name }}
                </component>
            </template>

            <template #subtitle />

            <template #mainInfos>
                <div v-if="identityFieldKeys.length > 0" class="mt-3 flex flex-wrap gap-3 items-start">
                    <EntityPropertyDisplay
                        v-for="fieldKey in identityFieldKeys"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="spell"
                        entity-type="spell"
                        display-mode="extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="sm"
                        class="max-w-[20rem] whitespace-normal break-words"
                    />
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <Tooltip
                        v-if="typeof spell.isMagic === 'boolean' && magicDef"
                        :content="magicDef._resolvedSubtitle || characteristicTooltipText(magicDef) || 'Orientation du sort.'"
                        placement="top"
                    >
                        <span
                            class="inline-flex items-center gap-2 text-sm font-medium cursor-default"
                            :style="(magicDef._resolvedColor || magicDef.color) ? getCharacteristicColorStyle(magicDef._resolvedColor || magicDef.color) : undefined"
                        >
                            <Icon
                                v-if="magicDef._resolvedIcon || magicDef.icon"
                                :source="magicDef._resolvedIcon || magicDef.icon"
                                :alt="spell.isMagic ? 'Wakfu' : 'Physique'"
                                size="sm"
                            />
                            <span>{{ spell.isMagic ? (magicDef.short_name || 'Wakfu') : 'Physique' }}</span>
                        </span>
                    </Tooltip>

                    <Tooltip v-if="showRitualBadge" :content="ritualTooltipText" placement="top">
                        <span
                            class="inline-flex items-center gap-2 text-sm cursor-default"
                            :style="(ritualDef?._resolvedColor || ritualDef?.color) ? getCharacteristicColorStyle(ritualDef._resolvedColor || ritualDef.color) : undefined"
                        >
                            <Icon
                                :source="ritualDef?._resolvedIcon || ritualDef?.icon || 'fa-solid fa-book-open'"
                                :alt="ritualDef?.short_name || 'Rituel'"
                                size="sm"
                            />
                            <span>{{ ritualDef?.short_name || ritualDef?.name || 'Rituel' }}</span>
                        </span>
                    </Tooltip>

                    <Tooltip v-if="showReactionBadge" placement="top">
                        <template #content>
                            <div class="max-w-xs text-left text-sm space-y-2 text-base-content">
                                <p class="font-semibold leading-snug">{{ reactionTooltipBlocks.title }}</p>
                                <p v-for="(line, idx) in reactionTooltipBlocks.lines" :key="idx" class="leading-snug">
                                    {{ line }}
                                </p>
                            </div>
                        </template>
                        <span
                            class="inline-flex items-center gap-2 text-sm font-medium cursor-default"
                            :style="(reactionDef?._resolvedColor || reactionDef?.color) ? getCharacteristicColorStyle(reactionDef._resolvedColor || reactionDef.color) : undefined"
                        >
                            <Icon
                                :source="reactionDef?._resolvedIcon || reactionDef?.icon || 'icons/caracteristics/is_reaction.webp'"
                                :alt="reactionDef?.short_name || 'Réaction'"
                                size="sm"
                            />
                            <span>{{ reactionDef?.short_name || reactionDef?.name || 'Réaction' }}</span>
                        </span>
                    </Tooltip>

                </div>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="spells"
                        :entity="spell"
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

        <section v-if="spell.description" class="space-y-2">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Description</h3>
            <p class="text-primary-200 whitespace-pre-wrap break-words leading-relaxed">{{ spell.description }}</p>
        </section>

        <section class="space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Utilisation</h3>
            <div class="p-3 bg-base-200 entity-radius-box min-w-0">
                <SpellUsageBlock
                    parts="meta"
                    :descriptors="descriptors"
                    :entity="usageEntity"
                    :can-show-field="canShowField"
                    :show-spell-types-cell="showSpellTypesCell"
                    property-size="sm"
                    row-class="gap-2 text-sm text-primary-100"
                    hover-inner-gap-class="gap-2"
                    notes-class="mt-1.5 text-sm leading-snug text-primary-200/80"
                />
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Résolution</h3>
            <div class="p-4 bg-base-200 entity-radius-box space-y-3">
                <p class="text-primary-100 font-medium">{{ resolutionLabel }}</p>

                <Tooltip
                    v-if="usageEntity.resolutionMode === 'attack_roll' && attackCharDef"
                    :content="
                        characteristicTooltipText(attackCharDef) ||
                        'Caractéristique utilisée pour le jet d’attaque de ce sort.'
                    "
                    placement="top"
                >
                    <div
                        class="inline-flex flex-wrap items-center gap-2 text-sm text-primary-200 cursor-default"
                        :style="attackCharDef.color ? getCharacteristicColorStyle(attackCharDef.color) : undefined"
                    >
                        <Icon
                            v-if="attackCharDef.icon"
                            :source="attackCharDef.icon"
                            :alt="attackCharDef.short_name || ''"
                            size="sm"
                        />
                        <span>{{ attackCharDef.short_name || attackCharDef.name }}</span>
                    </div>
                </Tooltip>

                <div v-if="usageEntity.resolutionMode === 'saving_throw'" class="space-y-2">
                    <Tooltip
                        v-if="saveCharDef"
                        :content="
                            characteristicTooltipText(saveCharDef) ||
                            'Caractéristique de sauvegarde ciblée par ce sort.'
                        "
                        placement="top"
                    >
                        <div
                            class="inline-flex flex-wrap items-center gap-2 text-sm text-primary-200 cursor-default"
                            :style="saveCharDef.color ? getCharacteristicColorStyle(saveCharDef.color) : undefined"
                        >
                            <Icon
                                v-if="saveCharDef.icon"
                                :source="saveCharDef.icon"
                                :alt="saveCharDef.short_name || ''"
                                size="sm"
                            />
                            <span>{{ saveCharDef.short_name || saveCharDef.name }}</span>
                        </div>
                    </Tooltip>
                    <p v-if="usageEntity.saveDcFormula" class="text-sm text-primary-200 break-words">
                        <span class="text-primary-400 font-semibold">DD : </span>
                        <span class="font-mono">{{ usageEntity.saveDcFormula }}</span>
                    </p>
                    <p v-if="usageEntity.saveSuccessNote" class="text-sm text-primary-300 whitespace-pre-wrap break-words">
                        <span class="text-primary-400 font-semibold">Si réussite : </span>
                        {{ usageEntity.saveSuccessNote }}
                    </p>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Effets</h3>
            <div
                v-if="hasStructuredEffects || hasLegacyEffectText"
                class="p-3 bg-base-200/70 entity-radius-box space-y-2"
            >
                <SpellUsageBlock
                    parts="effects"
                    :entity="spell"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    :can-show-field="canShowField"
                    :max-effect-rows="6"
                    flush-effects
                    notes-class="mb-2 text-sm leading-snug text-primary-200/80"
                    resolution-class="mb-1 text-sm text-primary-200/80"
                    cell-class="text-sm leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                />
                <p
                    v-if="hasStructuredEffects"
                    class="text-xs text-primary-400"
                >
                    Détail par degré ci-dessous.
                </p>
            </div>
            <SpellEffectsJournal
                v-if="hasStructuredEffects"
                :spell-degrees="spellDegreesPayload"
                :definitions="effectsDefinitions"
                sub-effect-layout="large"
                @active-degree-change="onActiveDegreeChange"
            />
            <div
                v-else-if="hasLegacyEffectText"
                class="rounded-box border border-dashed border-base-300 bg-base-200/40 p-3 space-y-1"
            >
                <p class="text-xs font-medium text-primary-300">
                    Texte d’effet libre (non structuré)
                </p>
                <p class="text-sm text-primary-200 whitespace-pre-wrap wrap-break-word">
                    {{ spell.effect }}
                </p>
            </div>
            <div
                v-else
                class="rounded-box border border-dashed border-base-300 bg-base-200/30 p-4 text-sm text-primary-400"
            >
                <p class="font-medium text-primary-300">Aucun effet renseigné</p>
                <p class="mt-1 text-xs leading-relaxed">
                    Pas de degré ni de texte libre. Ajoutez des degrés depuis l’édition du sort, ou
                    saisissez un résumé dans le champ description des effets.
                </p>
            </div>
        </section>

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex items-center gap-2 min-w-0">
                            <Icon
                                :source="getFieldIcon(fieldKey)"
                                :alt="getFieldLabel(fieldKey)"
                                size="xs"
                                class="text-primary-300 flex-shrink-0"
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
                <div class="text-xs font-semibold uppercase tracking-wide text-primary-300 mb-2">Paramètres</div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                    <template v-for="fieldKey in userCanEditFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="inline-flex items-center gap-2 min-w-0">
                                <Icon
                                    :source="getFieldIcon(fieldKey)"
                                    :alt="getFieldLabel(fieldKey)"
                                    size="xs"
                                    class="text-primary-300 flex-shrink-0"
                                    :style="getFieldIconStyle(fieldKey)"
                                />
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
