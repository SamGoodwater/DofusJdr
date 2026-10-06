<script setup>
/**
 * Panneau unifié « effets du sort » : liaison pivot effect_spell, édition par définition (degrés + seuils).
 */
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import { AREA_NOTATION_HELP } from '@/Utils/Entity/areaNotation.js';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import EffectGroupEditorForm from '@/Pages/Organismes/entity/EffectGroupEditorForm.vue';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import {
    formatConditionDispellable,
    formatConditionDuration,
    formatConditionIdentity,
    formatConditionMask,
    formatConditionMode,
    getConditionDispellableIcon,
} from '@/Composables/condition/conditionDisplay';
import AreaDisplay from '@/Pages/Molecules/entity/spell/AreaDisplay.vue';
import { formatSubEffectSelectLabel } from '@/Utils/Entity/subEffectLabels.js';

const props = defineProps({
    /**
     * @deprecated Non utilisé pour la liaison — recherche via GET /api/effects/definitions.
     */
    availableEffects: { type: Array, default: () => [] },
    effectFormOptions: { type: Object, default: () => ({}) },
    spellEffectGroups: { type: Array, default: () => [] },
    entityId: { type: Number, required: true },
    entityType: { type: String, default: 'spell' },
    /**
     * Masque le bouton « Enregistrer » du groupe d’effets : sauvegarde via le parent (ex. « Mettre à jour » du sort).
     */
    hideEffectGroupSubmitButton: { type: Boolean, default: false },
    /**
     * Éditeur affiché dans la modal (liste sorts) : enregistrement groupe d’effets en JSON sans navigation Inertia.
     */
    embeddedInModal: { type: Boolean, default: false },
    /**
     * Nom proposé pour une nouvelle définition (en général le nom du sort).
     */
    suggestedEffectName: { type: String, default: '' },
});

const emit = defineEmits(['effects-changed']);

const notificationStore = useNotificationStore();
const { canAccess } = usePermissions();
const effectEditorFormRef = ref(null);

const selectedAnchorId = ref(null);
const effectLinkSearch = ref('');
const effectToAttach = ref(0);
const attachLoading = ref(false);
const detachLoading = ref(false);
const errorMessage = ref('');
const lastAttachedDefinitionId = ref(null);
const definitionSearchResults = ref([]);
const definitionSearchLoading = ref(false);
let definitionSearchTimer = null;
let definitionSearchSeq = 0;

const showCreateForm = ref(false);
const createName = ref('');
const createTargetType = ref('direct');
const createArea = ref('');
const createLoading = ref(false);
/** @type {import('vue').Ref<Array<Record<string, unknown>>>} */
const createSubEffects = ref([]);

const TARGET_TYPE_OPTIONS = [
    { value: 'direct', label: 'Direct' },
    { value: 'trap', label: 'Piège' },
    { value: 'glyph', label: 'Glyphe' },
];

const createSubEffectOptions = computed(() => props.effectFormOptions?.sub_effects ?? []);

const createCharacteristicOptions = computed(() => {
    const base = props.effectFormOptions?.characteristics ?? [];
    const objectChars = props.effectFormOptions?.characteristics_object ?? [];
    const seen = new Set();
    const out = [];
    for (const c of [...base, ...objectChars]) {
        const key = String(c?.key ?? '');
        if (!key || seen.has(key)) continue;
        seen.add(key);
        out.push(c);
    }
    return out;
});

/**
 * @returns {Record<string, unknown>}
 */
function emptyCreateSubEffectRow() {
    const first = createSubEffectOptions.value[0];
    return {
        sub_effect_id: first?.id ?? '',
        scope: 'general',
        crit_only: false,
        logic_operator: 'AND',
        logic_condition: '',
        duration_formula: '',
        params: {
            characteristic: '',
            value_formula: '',
            value_formula_crit: '',
            life_steal_formula: '',
            cells_formula: '',
            movement_kind: 'movement',
            teleport: false,
        },
    };
}

function addCreateSubEffect() {
    if (!createSubEffectOptions.value.length) {
        errorMessage.value =
            'Référentiel de sous-effets indisponible. Rechargez la page puis réessayez.';
        return;
    }
    const row = emptyCreateSubEffectRow();
    if (createSubEffects.value.length > 0) {
        row.logic_operator = 'AND';
    } else {
        row.logic_operator = '';
    }
    createSubEffects.value.push(row);
}

function removeCreateSubEffect(index) {
    createSubEffects.value.splice(index, 1);
}

/**
 * @param {Record<string, unknown>} row
 */
function onCreateSubEffectChange(row) {
    row.params = {
        characteristic: '',
        value_formula: '',
        value_formula_crit: '',
        life_steal_formula: '',
        cells_formula: '',
        movement_kind: 'movement',
        teleport: false,
    };
}

/**
 * @param {Record<string, unknown>} row
 * @returns {string}
 */
function createSubEffectSlug(row) {
    const sub = createSubEffectOptions.value.find((s) => Number(s.id) === Number(row.sub_effect_id));
    return sub?.slug ?? '';
}

/**
 * @param {Record<string, unknown>} row
 * @returns {boolean}
 */
function createRowNeedsCharacteristic(row) {
    const slug = createSubEffectSlug(row);
    return ['frapper', 'soigner', 'protéger', 'booster', 'retirer', 'voler-caracteristiques'].includes(slug);
}

/**
 * @param {Record<string, unknown>} row
 * @returns {boolean}
 */
function createRowNeedsValue(row) {
    const slug = createSubEffectSlug(row);
    return !['appliquer-etat', 's-appliquer-etat', 'invoquer'].includes(slug);
}

/**
 * @param {Record<string, unknown>} row
 * @returns {Array<{key: string, label?: string, category?: string}>}
 */
function createCharacteristicsForRow(row) {
    const slug = createSubEffectSlug(row);
    const all = createCharacteristicOptions.value;
    if (slug === 'frapper' || slug === 'soigner' || slug === 'protéger') {
        return all.filter((c) => c.category === 'element');
    }
    if (slug === 'booster' || slug === 'retirer' || slug === 'voler-caracteristiques') {
        const objectChars = props.effectFormOptions?.characteristics_object ?? [];
        return objectChars.length ? objectChars : all;
    }
    return all;
}

function resetCreateFormFields() {
    createName.value = '';
    createArea.value = '';
    createTargetType.value = 'direct';
    createSubEffects.value = [];
}

const previewLevel = ref(1);
const previewData = ref(null);
const previewLoading = ref(false);
const canManageEffectsAdmin = computed(
    () => canAccess('effectsAdmin') || canAccess('adminPanel')
);

const selectedGroup = computed(() => {
    if (!selectedAnchorId.value || !props.spellEffectGroups?.length) {
        return null;
    }
    return props.spellEffectGroups.find((g) => g.anchor_effect_id === selectedAnchorId.value) ?? null;
});

const selectedDegreeIdForEditor = computed(() => selectedGroup.value?.group_effects?.[0]?.id ?? 0);

/** Doit précéder le watch immédiat sur `spellEffectGroups` (zone morte des const). */
const effectsEditorDirty = ref(false);

watch(
    () => props.spellEffectGroups,
    (groups) => {
        if (!groups?.length) {
            selectedAnchorId.value = null;
            effectsEditorDirty.value = false;
            return;
        }
        const ids = groups.map((g) => g.anchor_effect_id);
        if (lastAttachedDefinitionId.value) {
            const hit = groups.find((gr) => gr.anchor_effect_id === lastAttachedDefinitionId.value);
            if (hit) {
                selectedAnchorId.value = hit.anchor_effect_id;
                lastAttachedDefinitionId.value = null;
                return;
            }
        }
        if (!selectedAnchorId.value || !ids.includes(selectedAnchorId.value)) {
            selectedAnchorId.value = ids[0];
        }
    },
    { immediate: true }
);

const linkedDefinitionIds = computed(
    () => new Set((props.spellEffectGroups || []).map((g) => g.anchor_effect_id))
);

const filteredEffectsForAttach = computed(() =>
    (definitionSearchResults.value || []).filter(
        (e) => e?.effect_definition_id != null && !linkedDefinitionIds.value.has(e.effect_definition_id),
    ),
);

function effectOptionLabel(e) {
    return e.name || e.slug || `Effet #${e.effect_definition_id}`;
}

/**
 * Recherche serveur des définitions non liées (évite le payload massif d’édition).
 */
async function searchEffectDefinitions() {
    if (!props.entityId) {
        definitionSearchResults.value = [];
        return;
    }
    const seq = ++definitionSearchSeq;
    definitionSearchLoading.value = true;
    try {
        const { data } = await axios.get('/api/effects/definitions', {
            params: {
                q: effectLinkSearch.value.trim(),
                limit: 30,
                exclude_spell_id: props.entityId,
            },
        });
        if (seq !== definitionSearchSeq) {
            return;
        }
        definitionSearchResults.value = Array.isArray(data?.data) ? data.data : [];
    } catch (err) {
        if (seq !== definitionSearchSeq) {
            return;
        }
        definitionSearchResults.value = [];
        errorMessage.value =
            err.response?.data?.message || 'Impossible de rechercher les définitions d’effet.';
    } finally {
        if (seq === definitionSearchSeq) {
            definitionSearchLoading.value = false;
        }
    }
}

function scheduleDefinitionSearch() {
    if (definitionSearchTimer) {
        clearTimeout(definitionSearchTimer);
    }
    definitionSearchTimer = setTimeout(() => {
        definitionSearchTimer = null;
        searchEffectDefinitions();
    }, 250);
}

watch(effectLinkSearch, () => {
    effectToAttach.value = 0;
    scheduleDefinitionSearch();
});

watch(
    () => props.entityId,
    () => {
        effectToAttach.value = 0;
        scheduleDefinitionSearch();
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    if (definitionSearchTimer) {
        clearTimeout(definitionSearchTimer);
    }
});

async function reloadSpellEffectData() {
    if (props.embeddedInModal) {
        emit('effects-changed');
        await searchEffectDefinitions();
        return;
    }
    await router.reload({
        only: ['spellEffectGroups'],
        preserveState: true,
        preserveScroll: true,
    });
    await searchEffectDefinitions();
}

function openCreateForm() {
    showCreateForm.value = true;
    errorMessage.value = '';
    if (!createName.value.trim() && props.suggestedEffectName) {
        createName.value = props.suggestedEffectName;
    }
    if (createSubEffects.value.length === 0 && createSubEffectOptions.value.length > 0) {
        addCreateSubEffect();
    }
}

function closeCreateForm() {
    showCreateForm.value = false;
    errorMessage.value = '';
    resetCreateFormFields();
}

/**
 * Crée une définition (degré 1 + sous-effets optionnels) et la lie au sort, sans quitter la fiche.
 */
async function createAndAttachEffect() {
    const name = createName.value.trim();
    if (!name) {
        errorMessage.value = 'Indiquez un nom pour l’effet.';
        return;
    }
    const incomplete = createSubEffects.value.find((row) => !row.sub_effect_id);
    if (incomplete) {
        errorMessage.value = 'Chaque sous-effet doit avoir une action choisie.';
        return;
    }
    createLoading.value = true;
    errorMessage.value = '';
    try {
        const initial_sub_effects = createSubEffects.value.map((row, i) => ({
            sub_effect_id: Number(row.sub_effect_id),
            order: i,
            scope: row.scope || 'general',
            duration_formula: row.duration_formula || null,
            logic_operator: i > 0 ? row.logic_operator || 'AND' : null,
            logic_condition: i > 0 && row.logic_operator === 'OR' ? row.logic_condition || null : null,
            crit_only: Boolean(row.crit_only),
            params: row.params && typeof row.params === 'object' ? { ...row.params } : null,
        }));
        const { data } = await axios.post('/api/effects/spell-effects', {
            spell_id: props.entityId,
            name,
            target_type: createTargetType.value || 'direct',
            initial_area: createArea.value.trim() || null,
            initial_sub_effects,
        });
        const id = Number(data?.data?.id ?? 0);
        lastAttachedDefinitionId.value = id > 0 ? id : null;
        showCreateForm.value = false;
        resetCreateFormFields();
        await reloadSpellEffectData();
    } catch (err) {
        const payload = err.response?.data;
        const fieldError = payload?.errors
            ? Object.values(payload.errors).flat()[0]
            : null;
        errorMessage.value = fieldError || payload?.message || 'Impossible de créer cet effet.';
    } finally {
        createLoading.value = false;
    }
}

async function attachEffect() {
    if (!effectToAttach.value) {
        return;
    }
    attachLoading.value = true;
    errorMessage.value = '';
    try {
        lastAttachedDefinitionId.value = effectToAttach.value;
        await axios.post('/api/effects/spell-attachments', {
            spell_id: props.entityId,
            effect_id: effectToAttach.value,
        });
        effectToAttach.value = 0;
        effectLinkSearch.value = '';
        await reloadSpellEffectData();
    } catch (err) {
        lastAttachedDefinitionId.value = null;
        errorMessage.value = err.response?.data?.message || "Impossible de lier cet effet au sort.";
    } finally {
        attachLoading.value = false;
    }
}

async function detachCurrentGroup() {
    const g = selectedGroup.value;
    if (!g?.anchor_effect_id) {
        return;
    }
    if (!confirm('Retirer cette définition d’effet du sort ?')) {
        return;
    }
    detachLoading.value = true;
    errorMessage.value = '';
    try {
        await axios.delete('/api/effects/spell-attachments', {
            data: {
                spell_id: props.entityId,
                effect_id: g.anchor_effect_id,
            },
        });
        await reloadSpellEffectData();
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Impossible de détacher cet effet.';
    } finally {
        detachLoading.value = false;
    }
}

async function fetchPreview() {
    if (!props.entityId) {
        return;
    }
    previewLoading.value = true;
    previewData.value = null;
    errorMessage.value = '';
    try {
        const { data } = await axios.get('/api/effects/for-entity', {
            params: {
                entity_type: props.entityType,
                entity_id: props.entityId,
                level: previewLevel.value,
            },
        });
        previewData.value = data.data || [];
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Erreur lors du chargement de l’aperçu.';
    } finally {
        previewLoading.value = false;
    }
}

watch([previewLevel, () => props.entityId], () => fetchPreview(), { immediate: true });

function isStateSubEffect(sub) {
    const slug = String(sub?.action_slug || '');
    return slug === 'appliquer-etat' || slug === "s-appliquer-etat";
}

function conditionModeLabel(sub) {
    return formatConditionMode(sub?.action_slug, { variant: 'table' });
}

function conditionName(sub) {
    const ctx = sub?.context ?? {};
    return formatConditionIdentity(ctx?.condition_name, ctx?.condition_dofusdb_id);
}

function conditionMeta(sub) {
    const ctx = sub?.context ?? {};
    const bits = [formatConditionDuration(ctx?.duration), formatConditionMask(ctx?.target_mask)].filter(Boolean);
    return bits.join(' · ');
}

function stateDispellableText(sub) {
    return formatConditionDispellable(sub?.context?.dispellable);
}

function targetTypeLabel(type) {
    const m = { direct: 'Direct', trap: 'Piège', glyph: 'Glyphe' };
    return m[String(type || 'direct')] || type;
}

function showTargetTypeBadge(type) {
    return type === 'trap' || type === 'glyph';
}

const patchUrlForSelectedGroup = computed(() => {
    if (!selectedGroup.value || !props.entityId) {
        return '';
    }
    return route('entities.spells.updateEffectGroup', {
        spell: props.entityId,
        effect: selectedGroup.value.anchor_effect_id,
    });
});

/**
 * Enregistre le groupe d’effets sélectionné avant le PATCH du sort (si l’éditeur est monté).
 *
 * @returns {Promise<boolean>} true pour poursuivre la soumission du formulaire entité
 */
async function flushEffectGroupSave() {
    if (!effectEditorFormRef.value?.submitGroupAsync) {
        return true;
    }
    try {
        const result = await effectEditorFormRef.value.submitGroupAsync();
        if (!result.ok) {
            if (result.reason === 'validation_area') {
                notificationStore.error(`Notation de zone invalide. ${AREA_NOTATION_HELP}`, {
                    duration: 8000,
                    placement: 'top-right',
                });
            }
            return false;
        }
        return true;
    } catch (e) {
        notificationStore.error('Impossible d’enregistrer les effets du sort.', {
            duration: 5000,
            placement: 'top-right',
        });
        console.error(e);
        return false;
    }
}

function onEffectGroupDirtyChange(dirty) {
    effectsEditorDirty.value = Boolean(dirty);
}

defineExpose({
    flushEffectGroupSave,
    isDirty: effectsEditorDirty,
});
</script>

<template>
    <Container>
        <div class="mb-4">
            <div class="flex flex-wrap items-center gap-2 border-b border-base-300 pb-2">
                <h2 class="text-base font-semibold">Effets du sort</h2>
                <span
                    v-if="effectsEditorDirty"
                    class="badge badge-sm badge-warning"
                    title="Le groupe d’effets sélectionné a des changements non enregistrés"
                >
                    Modifications en attente
                </span>
            </div>
            <p class="text-sm text-base-content/70 mt-2">
                Liez une définition existante, ou créez-en une ici. La zone, le niveau et les sous-effets se règlent
                dans le bloc qui s’ouvre ensuite, sans quitter cette page.
            </p>
            <p
                v-if="hideEffectGroupSubmitButton"
                class="mt-2 rounded-lg border border-base-300 bg-base-200/50 px-3 py-2 text-xs text-base-content/80"
            >
                Les changements du bloc sélectionné sont enregistrés avec
                <strong>Mettre à jour</strong>, en bas du formulaire du sort.
                <span v-if="effectsEditorDirty" class="mt-1 block font-medium text-warning">
                    Des modifications d’effets sont en attente d’enregistrement.
                </span>
            </p>
        </div>

        <p v-if="errorMessage" class="text-error text-sm mb-3">{{ errorMessage }}</p>

        <div class="card bg-base-100 shadow border border-base-300 mb-6">
            <div class="card-body py-4 gap-3">
                <h3 class="card-title text-base">Rechercher et lier une définition d’effet</h3>
                <p class="text-sm text-base-content/70">
                    Recherche serveur (30 résultats max) ; les blocs déjà liés au sort sont exclus.
                </p>
                <div class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="label text-xs">Recherche</label>
                        <input
                            v-model="effectLinkSearch"
                            type="search"
                            class="input input-bordered input-sm w-full"
                            placeholder="Nom ou slug de définition…"
                            autocomplete="off"
                        />
                    </div>
                    <div class="flex-1 min-w-[220px]">
                        <label class="label text-xs">Définition à lier</label>
                        <select
                            v-model.number="effectToAttach"
                            class="select select-bordered select-sm w-full"
                            :disabled="definitionSearchLoading"
                        >
                            <option :value="0">
                                {{
                                    definitionSearchLoading
                                        ? 'Recherche…'
                                        : filteredEffectsForAttach.length
                                          ? '— Choisir —'
                                          : 'Aucun résultat'
                                }}
                            </option>
                            <option
                                v-for="e in filteredEffectsForAttach"
                                :key="e.effect_definition_id"
                                :value="e.effect_definition_id"
                            >
                                {{ effectOptionLabel(e) }}
                            </option>
                        </select>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        :disabled="!effectToAttach || attachLoading"
                        @click="attachEffect"
                    >
                        {{ attachLoading ? 'Liaison…' : 'Lier au sort' }}
                    </button>
                    <button
                        v-if="canManageEffectsAdmin"
                        type="button"
                        class="btn btn-sm btn-outline"
                        data-cy="create-spell-effect"
                        @click="showCreateForm ? closeCreateForm() : openCreateForm()"
                    >
                        {{ showCreateForm ? 'Fermer' : 'Créer un effet' }}
                    </button>
                </div>
                <form
                    v-if="canManageEffectsAdmin && showCreateForm"
                    class="grid gap-3 border-t border-base-300 pt-3 sm:grid-cols-2"
                    data-cy="create-spell-effect-form"
                    @submit.prevent="createAndAttachEffect"
                >
                    <div>
                        <label class="label text-xs" for="spell-effect-create-name">Nom</label>
                        <input
                            id="spell-effect-create-name"
                            v-model="createName"
                            type="text"
                            class="input input-bordered input-sm w-full"
                            maxlength="255"
                            required
                            autocomplete="off"
                        />
                    </div>
                    <div>
                        <label class="label text-xs" for="spell-effect-create-target">Type de cible</label>
                        <select
                            id="spell-effect-create-target"
                            v-model="createTargetType"
                            class="select select-bordered select-sm w-full"
                        >
                            <option v-for="opt in TARGET_TYPE_OPTIONS" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label text-xs" for="spell-effect-create-area">Zone du premier degré</label>
                        <input
                            id="spell-effect-create-area"
                            v-model="createArea"
                            type="text"
                            class="input input-bordered input-sm w-full"
                            placeholder="Optionnel : point, circle-1-2, line-1x3…"
                            autocomplete="off"
                        />
                    </div>

                    <div class="sm:col-span-2 space-y-3 rounded-box border border-base-300 bg-base-200/30 p-3">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <h4 class="text-sm font-semibold">Sous-effets</h4>
                                <p class="text-xs text-base-content/60 mt-0.5">
                                    Définissez au moins une action (frapper, soigner…). Vous pourrez affiner ensuite dans
                                    l’éditeur.
                                </p>
                            </div>
                            <button
                                type="button"
                                class="btn btn-sm btn-primary shrink-0"
                                data-cy="create-spell-effect-add-sub"
                                :disabled="!createSubEffectOptions.length"
                                @click="addCreateSubEffect"
                            >
                                + Ajouter un sous-effet
                            </button>
                        </div>

                        <p
                            v-if="!createSubEffectOptions.length"
                            class="text-sm text-warning"
                        >
                            Référentiel de sous-effets indisponible — rechargez la page.
                        </p>

                        <div
                            v-else-if="!createSubEffects.length"
                            class="text-sm text-base-content/70 py-2"
                        >
                            Aucun sous-effet. Cliquez sur « Ajouter un sous-effet ».
                        </div>

                        <div v-else class="space-y-3">
                            <div
                                v-for="(row, index) in createSubEffects"
                                :key="'create-sub-' + index"
                                class="rounded-box border border-base-300 bg-base-100 p-3 space-y-2"
                                data-cy="create-spell-effect-sub-row"
                            >
                                <div
                                    v-if="index > 0"
                                    class="flex flex-wrap items-end gap-2 pb-2 border-b border-dashed border-base-300"
                                >
                                    <div class="min-w-40">
                                        <label class="label text-xs py-0">Enchaînement</label>
                                        <select
                                            v-model="row.logic_operator"
                                            class="select select-bordered select-sm w-full"
                                        >
                                            <option value="AND">ET — le précédent doit s’appliquer</option>
                                            <option value="OR">OU — si la condition &gt; 0</option>
                                        </select>
                                    </div>
                                    <div v-if="row.logic_operator === 'OR'" class="flex-1 min-w-40">
                                        <label class="label text-xs py-0">Condition</label>
                                        <input
                                            v-model="row.logic_condition"
                                            type="text"
                                            class="input input-bordered input-sm w-full"
                                            placeholder="ex: [target_is_ally]"
                                        />
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-end gap-2">
                                    <div class="min-w-40 flex-1">
                                        <label class="label text-xs py-0">Action</label>
                                        <select
                                            v-model="row.sub_effect_id"
                                            class="select select-bordered select-sm w-full"
                                            required
                                            @change="onCreateSubEffectChange(row)"
                                        >
                                            <option value="">— Choisir —</option>
                                            <option
                                                v-for="s in createSubEffectOptions"
                                                :key="s.id"
                                                :value="s.id"
                                            >
                                                {{ formatSubEffectSelectLabel(s) }}
                                            </option>
                                        </select>
                                    </div>
                                    <div v-if="createRowNeedsCharacteristic(row)" class="min-w-36 flex-1">
                                        <label class="label text-xs py-0">
                                            {{
                                                ['frapper', 'soigner', 'protéger'].includes(createSubEffectSlug(row))
                                                    ? 'Élément'
                                                    : 'Caractéristique'
                                            }}
                                        </label>
                                        <select
                                            v-model="row.params.characteristic"
                                            class="select select-bordered select-sm w-full"
                                        >
                                            <option value="">— Choisir —</option>
                                            <option
                                                v-for="c in createCharacteristicsForRow(row)"
                                                :key="c.key"
                                                :value="c.key"
                                            >
                                                {{ c.label || c.key }}
                                            </option>
                                        </select>
                                    </div>
                                    <div v-if="createRowNeedsValue(row)" class="min-w-36 flex-1">
                                        <label class="label text-xs py-0">Valeur (formule)</label>
                                        <input
                                            v-model="row.params.value_formula"
                                            type="text"
                                            class="input input-bordered input-sm w-full"
                                            placeholder="ex: 2d6, [1-4], [level]*2"
                                        />
                                    </div>
                                    <label class="flex items-center gap-2 cursor-pointer pb-1">
                                        <input
                                            v-model="row.crit_only"
                                            type="checkbox"
                                            class="checkbox checkbox-sm"
                                        />
                                        <span class="text-xs whitespace-nowrap">Critique seulement</span>
                                    </label>
                                    <button
                                        type="button"
                                        class="btn btn-ghost btn-sm btn-square text-error"
                                        title="Retirer ce sous-effet"
                                        @click="removeCreateSubEffect(index)"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2 flex flex-wrap gap-2">
                        <button
                            type="submit"
                            class="btn btn-sm btn-primary"
                            data-cy="create-spell-effect-submit"
                            :disabled="createLoading"
                        >
                            {{ createLoading ? 'Création…' : 'Créer et lier' }}
                        </button>
                        <button type="button" class="btn btn-sm btn-ghost" @click="closeCreateForm">
                            Annuler
                        </button>
                    </div>
                    <p class="sm:col-span-2 text-xs text-base-content/60">
                        L’effet est créé avec un premier degré et les sous-effets ci-dessus. Affinez-les ensuite dans le
                        bloc d’édition (durée, états, déplacement…).
                    </p>
                </form>
            </div>
        </div>

        <div v-if="spellEffectGroups.length > 1" class="mb-4">
            <label class="label text-xs">Bloc d’effets à éditer</label>
            <select v-model.number="selectedAnchorId" class="select select-bordered select-sm max-w-xl w-full">
                <option v-for="g in spellEffectGroups" :key="g.anchor_effect_id" :value="g.anchor_effect_id">
                    {{ g.label }} ({{ g.group_effects?.length ?? 0 }} degré(s))
                </option>
            </select>
        </div>

        <EffectGroupEditorForm
            v-if="selectedGroup && patchUrlForSelectedGroup && selectedDegreeIdForEditor > 0"
            ref="effectEditorFormRef"
            :key="selectedGroup.anchor_effect_id"
            :options="effectFormOptions"
            :group-effects="selectedGroup.group_effects"
            :selected-effect-id="selectedDegreeIdForEditor"
            :patch-url="patchUrlForSelectedGroup"
            :heading="selectedGroup.label"
            :hide-submit-button="hideEffectGroupSubmitButton"
            :save-without-inertia="embeddedInModal"
            :embedded-in-modal="embeddedInModal"
            @dirty-change="onEffectGroupDirtyChange"
        />

        <div v-if="selectedGroup && patchUrlForSelectedGroup" class="mt-3">
            <button
                type="button"
                class="btn btn-sm btn-ghost text-error"
                :disabled="detachLoading"
                @click="detachCurrentGroup"
            >
                {{ detachLoading ? '…' : 'Détacher ce bloc du sort' }}
            </button>
        </div>

        <div
            v-else-if="!spellEffectGroups.length"
            class="rounded-box border border-dashed border-base-300 bg-base-200/40 p-4 text-sm space-y-1"
        >
            <p class="font-medium text-base-content">Aucun effet lié</p>
            <p class="text-base-content/70">
                Recherchez une définition existante, ou créez-en une ci-dessus. Les sous-effets s’ajoutent
                ensuite dans cette page.
            </p>
        </div>

        <details class="collapse collapse-arrow bg-base-200/40 border border-base-300 rounded-box mt-8">
            <summary class="collapse-title font-medium min-h-0 py-3">
                Aperçu pour un niveau de créature
            </summary>
            <div class="collapse-content text-sm pt-0">
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <InputField v-model="previewLevel" label="Niveau créature" type="number" class="w-28" />
                    <button
                        type="button"
                        class="btn btn-sm btn-ghost"
                        :disabled="previewLoading"
                        @click="fetchPreview"
                    >
                        {{ previewLoading ? 'Chargement…' : 'Rafraîchir' }}
                    </button>
                </div>
                <div v-if="previewLoading" class="text-base-content/70">Chargement…</div>
                <div v-else-if="previewData && previewData.length === 0" class="text-base-content/70">
                    Aucun effet pour ce niveau.
                </div>
                <div v-else-if="previewData" class="space-y-3">
                    <div
                        v-for="(item, i) in previewData"
                        :key="i"
                        class="rounded-box border border-base-300 bg-base-200/40 p-3"
                    >
                        <div class="text-sm flex flex-wrap items-center gap-2">
                            <span class="font-medium">{{ item.effect?.name || item.effect?.slug || 'Effet' }}</span>
                            <span
                                v-if="showTargetTypeBadge(item.effect?.target_type)"
                                class="badge badge-sm badge-primary badge-outline"
                                :title="'Type de cible : ' + targetTypeLabel(item.effect?.target_type)"
                            >
                                {{ targetTypeLabel(item.effect?.target_type) }}
                            </span>
                            <AreaDisplay
                                v-if="item.effect?.area"
                                :area="item.effect.area"
                                class="text-base-content/80"
                            />
                            <span class="text-base-content/70"> — {{ item.resolved_text || item.description || '—' }}</span>
                        </div>

                        <div
                            v-if="Array.isArray(item.resolved?.sub_effects) && item.resolved.sub_effects.length"
                            class="mt-2 pl-3 border-l-2 border-base-300 space-y-1"
                        >
                            <p class="text-xs uppercase tracking-wide text-base-content/60 font-semibold">
                                Sous-effets (normal)
                            </p>
                            <ul class="space-y-1 text-xs">
                                <li v-for="(sub, si) in item.resolved.sub_effects" :key="si" class="flex flex-col gap-0.5">
                                    <template v-if="isStateSubEffect(sub)">
                                        <span class="flex flex-wrap items-center gap-1">
                                            <Icon
                                                v-if="getConditionDispellableIcon(sub?.context?.dispellable)"
                                                :source="getConditionDispellableIcon(sub?.context?.dispellable)"
                                                size="xs"
                                                class="opacity-80"
                                            />
                                            <span class="font-medium">{{ conditionModeLabel(sub) }}</span>
                                            <span>{{ conditionName(sub) }}</span>
                                            <span v-if="conditionMeta(sub)" class="text-base-content/60">{{ conditionMeta(sub) }}</span>
                                            <span v-if="stateDispellableText(sub)" class="text-base-content/50">{{
                                                stateDispellableText(sub)
                                            }}</span>
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span>{{ sub.text || '—' }}</span>
                                    </template>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </details>
    </Container>
</template>
