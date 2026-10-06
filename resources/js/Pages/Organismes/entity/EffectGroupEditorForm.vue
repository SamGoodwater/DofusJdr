<script setup>
/**
 * Édition d’un groupe d’effets (champs communs + onglets par degré + sous-effets).
 * Utilisable depuis l’admin effets ou la fiche sort.
 */
import { computed, reactive, ref, useSlots, watch } from 'vue';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';
import EntityPickerCore from '@/Pages/Organismes/entity/EntityPickerCore.vue';
import AreaNotationEditor from '@/Pages/Molecules/entity/spell/AreaNotationEditor.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EditActionDock from '@/Pages/Molecules/action/EditActionDock.vue';
import { ACTION } from '@/Utils/atomic-design/actionLabels';
import { AREA_NOTATION_HELP, isValidAreaNotation } from '@/Utils/Entity/areaNotation.js';
import { formatSubEffectSelectLabel } from '@/Utils/Entity/subEffectLabels.js';
import { METERS_PER_CASE, previewMetersFromCellsFormula } from '@/Utils/Entity/displacementFormat.js';

/** Exposé au template (règle 1 case = 1,5 m). */
const CASE_SIZE_METERS = METERS_PER_CASE;

const props = defineProps({
    options: {
        type: Object,
        default: () => ({
            effect_groups: [],
            sub_effects: [],
            scopes: [],
            characteristics: [],
            monsters: [],
            conditions: [],
            characteristics_object: [],
        }),
    },
    groupEffects: { type: Array, required: true },
    /** Effet courant (onglet initial). */
    selectedEffectId: { type: Number, required: true },
    /** URL de PATCH (admin ou fiche sort). */
    patchUrl: { type: String, required: true },
    /** Titre optionnel au-dessus du formulaire. */
    heading: { type: String, default: '' },
    /** Libellé du bouton de soumission. */
    submitLabel: { type: String, default: ACTION.save.label },
    /** Masque le bouton primaire (sauvegarde déclenchée par le parent, ex. fiche sort). */
    hideSubmitButton: { type: Boolean, default: false },
    /** Formulaire affiché dans une modale : pas de {@link EditActionDock}, boutons compacts. */
    embeddedInModal: { type: Boolean, default: false },
    /** Admin effets : affiche « Supprimer ce degré » (requiert adminEffectId). */
    showAdminDegreeDelete: { type: Boolean, default: false },
    /** ID de la définition d’effet (Effect), pour la route destroy-degree. */
    adminEffectId: { type: Number, default: null },
    /**
     * Si true : PATCH en JSON sans visite Inertia (ex. modal « modifier le sort » sur la liste).
     * Sinon : Inertia + redirection « retour » côté serveur (pas vers une autre page fixe).
     */
    saveWithoutInertia: { type: Boolean, default: false },
});

const emit = defineEmits(['dirty-change']);

const notificationStore = useNotificationStore();
const localConditions = ref([]);

watch(
    () => props.options.conditions,
    (conditions) => {
        localConditions.value = [...(conditions ?? [])];
    },
    { immediate: true, deep: true },
);

const common = reactive({
    name: '',
    description: '',
    target_type: 'direct',
});

const degreeForms = ref([]);
const activeTab = ref(0);
/** Snapshot JSON après hydrate / save réussie — pour badge « non enregistré ». */
const savedSnapshot = ref('');

const groupSaveForm = useForm({
    common: {},
    degrees: [],
});

const deleteDegreeForm = useForm({});

/** PATCH groupe d’effets hors Inertia (modal liste sorts). */
const jsonSaving = ref(false);

const slots = useSlots();
const hasDegreeExtraSlot = computed(() => Boolean(slots['degree-extra']));

function defaultParamsForSubEffect() {
    return {
        characteristic: '',
        value_formula: '',
        value_formula_crit: '',
        life_steal_formula: '',
        monster_id: '',
        condition_id: '',
        condition_dofusdb_id: '',
        condition_name: '',
        dispellable: false,
        cells_formula: '',
        movement_kind: 'movement',
        teleport: false,
    };
}

function getParamSchemaForRow(row) {
    if (!row?.sub_effect_id || !props.options?.sub_effects) return null;
    const sub = props.options.sub_effects.find((s) => s.id === row.sub_effect_id);
    return sub?.param_schema ?? null;
}

/** Anciennes clés config (effect_sub_effects) → clés BDD groupe object. */
const LEGACY_CHAR_TO_OBJECT_KEY = Object.freeze({
    action_points: 'action_points_object',
    movement_points: 'movement_points_object',
    range: 'range_object',
    agility: 'agility_object',
    strength: 'strength_object',
    intelligence: 'intelligence_object',
    chance: 'chance_object',
    wisdom: 'wisdom_object',
    vitality: 'vitality_object',
    life_points: 'life_points_max_object',
    shield: 'armor_class_object',
    earth: 'fixed_damage_earth_object',
    fire: 'fixed_damage_fire_object',
    water: 'fixed_damage_water_object',
    air: 'fixed_damage_air_object',
    neutral: 'fixed_damage_neutral_object',
});

/**
 * @param {number|string} subEffectId
 * @param {object} params
 */
function normalizeLegacyCharacteristicKeyForSubEffect(subEffectId, params) {
    const sub = props.options.sub_effects?.find((s) => s.id === subEffectId);
    const slug = sub?.slug ?? '';
    if (!['booster', 'retirer', 'voler-caracteristiques'].includes(slug)) {
        return;
    }
    const ch = params?.characteristic;
    if (typeof ch !== 'string' || ch === '' || ch.endsWith('_spell')) {
        return;
    }
    const mapped = LEGACY_CHAR_TO_OBJECT_KEY[ch];
    if (mapped) {
        params.characteristic = mapped.replace(/_object$/, '_spell');
    }
}

function characteristicsForRow(row) {
    const slug = subEffectSlugForRow(row);
    if (slug === 'booster' || slug === 'retirer' || slug === 'voler-caracteristiques') {
        return (
            props.options.characteristics_spell_buff ??
            props.options.characteristics_object ??
            []
        );
    }
    const schema = getParamSchemaForRow(row);
    const param = schema?.params?.find((p) => p.key === 'characteristic');
    const categories = param?.categories;
    const actionList = props.options.characteristics_spell_action ?? [];
    const actionFiltered = actionList.filter((c) => (c.sub_effect_slugs ?? []).includes(slug));
    if (actionFiltered.length) {
        if (categories?.length) {
            return actionFiltered.filter((c) => categories.includes(c.category));
        }
        return actionFiltered;
    }
    if (!categories?.length) return props.options.characteristics ?? [];
    return (props.options.characteristics ?? []).filter((c) => categories.includes(c.category));
}

/** Options select pour les caractéristiques d'une ligne de sous-effet. */
function characteristicSelectOptionsForRow(row) {
    return characteristicsForRow(row).map((c) => ({
        value: c.key,
        label: c.label ?? c.key,
    }));
}

function rowHasCharacteristicParam(row) {
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'characteristic') ?? false;
}

function rowHasValueParam(row) {
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'value') ?? false;
}

function rowHasMonsterParam(row) {
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.type === 'monster') ?? false;
}

/** @param {object} row */
function subEffectSlugForRow(row) {
    const sub = props.options.sub_effects?.find((s) => s.id === row.sub_effect_id);
    return sub?.slug ?? '';
}

/** @param {object} row */
function rowHasConditionParam(row) {
    const slug = subEffectSlugForRow(row);
    if (slug === 'appliquer-etat' || slug === 's-appliquer-etat') {
        return true;
    }
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'condition_id' || p.type === 'condition') ?? false;
}

/** @param {object} row */
function rowHasCellsFormulaParam(row) {
    const slug = subEffectSlugForRow(row);
    if (slug === 'déplacer') {
        return true;
    }
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'cells_formula') ?? false;
}

/** @param {object} row */
function rowHasTeleportParam(row) {
    const slug = subEffectSlugForRow(row);
    if (slug === 'déplacer') {
        return true;
    }
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'teleport') ?? false;
}

/** @param {object} row */
function rowHasMovementKindParam(row) {
    const slug = subEffectSlugForRow(row);
    if (slug === 'déplacer') {
        return true;
    }
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'movement_kind') ?? false;
}

const MOVEMENT_KIND_OPTIONS = Object.freeze([
    { value: 'movement', label: 'Déplacement' },
    { value: 'jump', label: 'Saut / bond' },
    { value: 'teleport', label: 'Téléportation' },
    { value: 'push', label: 'Repousse' },
    { value: 'pull', label: 'Attirance' },
]);

function syncMovementKindSideEffects(row) {
    if (subEffectSlugForRow(row) !== 'déplacer') {
        return;
    }
    row.params.teleport = row.params?.movement_kind === 'teleport';
}

function syncTeleportSideEffects(row) {
    if (subEffectSlugForRow(row) !== 'déplacer') {
        return;
    }
    row.params.movement_kind = row.params?.teleport ? 'teleport' : 'movement';
}

/** Garde value_formula alignée sur cells_formula pour le sous-effet déplacement (même source que le scrapping). */
function syncDeplacementValueFormula(row) {
    if (subEffectSlugForRow(row) !== 'déplacer') {
        return;
    }
    const c = row.params?.cells_formula;
    row.params.value_formula = typeof c === 'string' ? c : '';
}

/**
 * Aperçu mètres sous le champ cases (littéral numérique uniquement).
 *
 * @param {object} row
 * @returns {string|null}
 */
function deplacementMetersPreview(row) {
    if (subEffectSlugForRow(row) !== 'déplacer') {
        return null;
    }
    return previewMetersFromCellsFormula(row.params?.cells_formula);
}

/**
 * @param {object} st - entrée conditions
 * @returns {string|null}
 */
function conditionIconSource(st) {
    if (!st?.icon || typeof st.icon !== 'string' || st.icon.trim() === '') {
        return null;
    }
    const t = st.icon.trim();
    if (t.startsWith('http://') || t.startsWith('https://') || t.startsWith('/') || t.includes('icons/')) {
        return t;
    }
    return `icons/caracteristics/${t}`;
}

/**
 * @param {object} row
 * @returns {Array<object>}
 */
function filteredConditionsForRow(row) {
    const list = localConditions.value ?? [];
    const q = String(row._editor_condition_q ?? '')
        .trim()
        .toLowerCase();
    let out = list;
    if (q !== '') {
        out = list.filter((st) => {
            const name = String(st.name ?? '').toLowerCase();
            const idStr = String(st.id ?? '');
            const dof = String(st.dofusdb_id ?? '');
            return name.includes(q) || idStr === q || dof === q;
        });
    }
    return out.slice(0, 100);
}

/**
 * @param {object} row
 * @param {object} st
 */
function selectCondition(row, st) {
    row.params.condition_id = st.id;
    row.params.condition_dofusdb_id = st.dofusdb_id;
    row.params.condition_name = st.name ?? '';
}

async function createConditionForRow(row) {
    const name = String(row._editor_condition_q ?? '').trim();
    if (!name) {
        return;
    }

    try {
        const { data } = await axios.post(route('entities.conditions.store'), {
            name,
            description: null,
            state: 'playable',
            read_level: 0,
            write_level: 4,
        });
        localConditions.value = [...localConditions.value, data];
        selectCondition(row, data);
        row._editor_condition_q = '';
        notificationStore.success('Condition créée et sélectionnée.', { duration: 2500, placement: 'top-right' });
    } catch (error) {
        notificationStore.error('Impossible de créer la condition.', { duration: 5000, placement: 'top-center' });
        console.warn('[EffectGroupEditorForm] création condition échouée', error);
    }
}

/**
 * @param {object} params
 */
function resolveConditionIdFromLegacy(params) {
    if (params == null || typeof params !== 'object') {
        return;
    }
    if (params.condition_id != null && params.condition_id !== '') {
        return;
    }
    const dof = params.condition_dofusdb_id;
    if (dof == null || dof === '') {
        return;
    }
    const match = (localConditions.value ?? []).find((st) => Number(st.dofusdb_id) === Number(dof));
    if (match) {
        params.condition_id = match.id;
    }
}

/** Sous-effet « frapper » (id ou slug), même si param_schema en base n’est pas encore à jour. */
function isFrapperSubEffectRow(row) {
    if (!row?.sub_effect_id || !props.options?.sub_effects) {
        return false;
    }
    const sub = props.options.sub_effects.find((s) => s.id === row.sub_effect_id);
    return sub?.slug === 'frapper' || sub?.type_slug === 'frapper';
}

function rowHasLifeStealFormulaParam(row) {
    const schema = getParamSchemaForRow(row);
    if (schema?.params?.some((p) => p.key === 'life_steal_formula')) {
        return true;
    }
    return isFrapperSubEffectRow(row);
}

function characteristicLabelForRow(row) {
    const schema = getParamSchemaForRow(row);
    const param = schema?.params?.find((p) => p.key === 'characteristic');
    return param?.label ?? 'Caractéristique';
}

/**
 * Texte d’aide sous le champ valeur selon l’action.
 *
 * @param {object} row
 * @returns {string}
 */
function valueHelpTextForRow(row) {
    const sub = props.options.sub_effects?.find((s) => s.id === row.sub_effect_id);
    const slug = sub?.slug ?? '';
    if (slug === 'frapper') {
        return 'Dégâts primaires (hors résistances) : ndX, plages [min-max], [level], caractéristiques entre crochets, floor(), etc.';
    }
    if (slug === 'soigner') {
        return 'Montant de soin : formule numérique ou dés (ndX).';
    }
    if (slug === 'protéger') {
        return 'Montant de bouclier / protection absorbée (sans élément), comme un soin.';
    }
    if (slug === 'booster' || slug === 'retirer' || slug === 'voler-caracteristiques') {
        return 'Valeur appliquée à la caractéristique choisie (bonus ou malus selon l’action).';
    }
    if (slug === 'autre') {
        return 'Texte ou formule libre (aperçu / import DofusDB).';
    }
    return 'Formule : ndX, [min-max], [level], [agi], floor(), etc.';
}

/** @param {object} row */
function durationHelpTextForRow(row) {
    const sub = props.options.sub_effects?.find((s) => s.id === row.sub_effect_id);
    const slug = sub?.slug ?? '';
    if (slug === 'appliquer-etat' || slug === 's-appliquer-etat') {
        return 'Durée de l’état en tours (combat) ou secondes (hors combat), selon le contexte.';
    }
    return 'Durée d’effet : nombre de tours en combat, ou secondes hors combat — selon le contexte de résolution.';
}

const TARGET_TYPE_OPTIONS = [
    { value: 'direct', label: 'Direct' },
    { value: 'trap', label: 'Piège' },
    { value: 'glyph', label: 'Glyphe' },
];

/** Badge Piège / Glyphe uniquement (pas « Direct ») — aligné sur SpellEffectsUnifiedSection. */
function targetTypeLabel(type) {
    const m = { direct: 'Direct', trap: 'Piège', glyph: 'Glyphe' };
    return m[String(type || 'direct')] || type;
}

function showTargetTypeBadge(type) {
    return type === 'trap' || type === 'glyph';
}

function mapSubEffectsFromApi(subEffects) {
    const rows = (subEffects || []).map((s, idx) => {
        const rawOp = s.logic_operator ?? '';
        const logic_operator =
            idx > 0 && (rawOp === '' || rawOp == null) ? 'AND' : rawOp;

        return {
            sub_effect_id: s.id,
            order: s.order ?? 0,
            scope: s.scope ?? 'general',
            value_min: s.value_min ?? '',
            value_max: s.value_max ?? '',
            dice_num: s.dice_num ?? '',
            dice_side: s.dice_side ?? '',
            duration_formula: s.duration_formula ?? '',
            logic_group: s.logic_group ?? '',
            logic_operator,
            logic_condition: s.logic_condition ?? '',
            crit_only: s.crit_only ?? false,
            _editor_condition_q: '',
            params: (() => {
                const merged = {
                    ...defaultParamsForSubEffect(),
                    ...(s.params && typeof s.params === 'object' ? s.params : {}),
                };
                resolveConditionIdFromLegacy(merged);
                if (merged.dispellable == null) {
                    merged.dispellable = false;
                }
                if (merged.teleport == null) {
                    merged.teleport = false;
                }
                if (!merged.movement_kind || merged.movement_kind === 'movement') {
                    merged.movement_kind = merged.teleport ? 'teleport' : 'movement';
                }
                if (merged.movement_kind === 'teleport') {
                    merged.teleport = true;
                }
                normalizeLegacyCharacteristicKeyForSubEffect(s.id, merged);
                return merged;
            })(),
        };
    });
    rows.forEach((row) => syncDeplacementValueFormula(row));
    return rows;
}

function currentEditorSnapshot() {
    return JSON.stringify({
        common: {
            name: common.name,
            description: common.description,
            target_type: common.target_type,
        },
        degrees: degreeForms.value,
    });
}

function captureSavedSnapshot() {
    savedSnapshot.value = currentEditorSnapshot();
}

function initDegreeFormsFromProps() {
    const list = props.groupEffects;
    if (!list?.length) {
        savedSnapshot.value = '';
        return;
    }
    const first = list[0];
    common.name = first.name ?? '';
    common.description = first.description ?? '';
    common.target_type = first.target_type ?? 'direct';
    degreeForms.value = list.map((ge) => ({
        id: ge.id,
        degree: ge.degree,
        slug: ge.slug ?? '',
        area: ge.area ?? '',
        required_creature_level:
            ge.required_creature_level === null || ge.required_creature_level === undefined
                ? ''
                : String(ge.required_creature_level),
        effect_sub_effects: mapSubEffectsFromApi(ge.sub_effects || []),
    }));
    const idx = list.findIndex((e) => e.id === props.selectedEffectId);
    activeTab.value = idx >= 0 ? idx : 0;
    captureSavedSnapshot();
}

const isDirty = ref(false);

function refreshDirtyState() {
    const dirty = savedSnapshot.value !== '' && currentEditorSnapshot() !== savedSnapshot.value;
    if (isDirty.value !== dirty) {
        isDirty.value = dirty;
        emit('dirty-change', dirty);
    }
}

watch([common, degreeForms, savedSnapshot], () => refreshDirtyState(), {
    deep: true,
    flush: 'post',
});

watch(
    () => [props.groupEffects, props.selectedEffectId],
    () => initDegreeFormsFromProps(),
    { immediate: true, deep: true }
);

function addSubEffect() {
    const first = props.options.sub_effects?.[0];
    if (!first) return;
    const rows = degreeForms.value[activeTab.value]?.effect_sub_effects;
    if (!rows) return;
    const isLinked = rows.length > 0;
    rows.push({
        sub_effect_id: first.id,
        order: rows.length,
        scope: 'general',
        value_min: '',
        value_max: '',
        dice_num: '',
        dice_side: '',
        duration_formula: '',
        logic_group: '',
        logic_operator: isLinked ? 'AND' : '',
        logic_condition: '',
        crit_only: false,
        _editor_condition_q: '',
        params: defaultParamsForSubEffect(),
    });
}

function onSubEffectChange(row) {
    row.params = { ...defaultParamsForSubEffect() };
    row._editor_condition_q = '';
}

function removeSubEffect(index) {
    const rows = degreeForms.value[activeTab.value]?.effect_sub_effects;
    if (!rows) return;
    rows.splice(index, 1);
    rows.forEach((row, i) => {
        row.order = i;
    });
}

function duplicateSubEffect(index) {
    const rows = degreeForms.value[activeTab.value]?.effect_sub_effects;
    if (!rows) return;
    const original = rows[index];
    if (!original) return;
    const clone = JSON.parse(JSON.stringify(original));
    rows.splice(index + 1, 0, clone);
    rows.forEach((row, i) => {
        row.order = i;
    });
}

function payloadRows(rows) {
    return rows.map((row, i) => ({
        sub_effect_id: Number(row.sub_effect_id),
        order: i,
        scope: row.scope || 'general',
        value_min: row.value_min !== '' && row.value_min != null ? Number(row.value_min) : null,
        value_max: row.value_max !== '' && row.value_max != null ? Number(row.value_max) : null,
        dice_num: row.dice_num !== '' && row.dice_num != null ? Number(row.dice_num) : null,
        dice_side: row.dice_side !== '' && row.dice_side != null ? Number(row.dice_side) : null,
        duration_formula: row.duration_formula || null,
        logic_group: row.logic_group || null,
        logic_operator:
            i > 0 ? row.logic_operator || 'AND' : row.logic_operator || null,
        logic_condition: row.logic_condition || null,
        crit_only: Boolean(row.crit_only),
        params: row.params && typeof row.params === 'object' ? row.params : null,
    }));
}

function normalizeCreatureLevel(raw) {
    if (raw === '' || raw == null) {
        return null;
    }
    const n = Number(raw);
    return Number.isFinite(n) ? n : null;
}

/** @returns {boolean} false si une zone est renseignée mais invalide (onglet basculé sur le degré fautif). */
function validateDegreeAreas() {
    for (let i = 0; i < degreeForms.value.length; i += 1) {
        const raw = degreeForms.value[i]?.area;
        if (raw != null && String(raw).trim() !== '' && !isValidAreaNotation(raw)) {
            activeTab.value = i;
            return false;
        }
    }
    return true;
}

function applyGroupPayloadToForm() {
    groupSaveForm.common = {
        name: common.name || null,
        description: common.description || null,
        target_type: common.target_type || 'direct',
    };
    groupSaveForm.degrees = degreeForms.value.map((d) => ({
        id: d.id,
        slug: d.slug || null,
        area: d.area || null,
        required_creature_level: normalizeCreatureLevel(d.required_creature_level),
        effect_sub_effects: payloadRows(d.effect_sub_effects),
    }));
}

function csrfHeaders() {
    const t = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    return t ? { 'X-CSRF-TOKEN': t } : {};
}

/**
 * PATCH JSON (pas de navigation) — succès / erreurs via toasts.
 *
 * @returns {Promise<void>}
 */
async function patchEffectGroupJson() {
    applyGroupPayloadToForm();
    jsonSaving.value = true;
    try {
        await axios.patch(props.patchUrl, groupSaveForm.data(), {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...csrfHeaders(),
            },
        });
        captureSavedSnapshot();
        notificationStore.success('Effets du groupe enregistrés.');
    } catch (e) {
        const errs = e?.response?.data?.errors;
        let detail = "Impossible d’enregistrer le groupe d’effets.";
        if (errs && typeof errs === 'object') {
            const vals = Object.values(errs).flat();
            if (vals.length && vals[0]) {
                detail = String(Array.isArray(vals[0]) ? vals[0][0] : vals[0]);
            }
        } else if (e?.response?.data?.message) {
            detail = String(e.response.data.message);
        }
        notificationStore.error(detail, { duration: 8000, placement: 'top-right' });
        const err = new Error(detail);
        err.toasted = true;
        throw err;
    } finally {
        jsonSaving.value = false;
    }
}

function submitGroup() {
    if (!validateDegreeAreas()) {
        return;
    }
    if (props.saveWithoutInertia) {
        patchEffectGroupJson().catch(() => {});
        return;
    }
    applyGroupPayloadToForm();
    groupSaveForm.patch(props.patchUrl, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => captureSavedSnapshot(),
    });
}

/**
 * Même logique que {@link submitGroup}, en Promise (enchaînement depuis le formulaire parent).
 *
 * @returns {Promise<{ ok: true } | { ok: false, reason: 'validation_area' }>}
 */
function submitGroupAsync() {
    if (!validateDegreeAreas()) {
        return Promise.resolve({ ok: false, reason: 'validation_area' });
    }
    if (props.saveWithoutInertia) {
        return patchEffectGroupJson()
            .then(() => ({ ok: true }))
            .catch((e) => {
                if (!e?.toasted) {
                    notificationStore.error('Impossible d’enregistrer les effets du sort.', {
                        duration: 5000,
                        placement: 'top-right',
                    });
                }
                return { ok: false, reason: 'request' };
            });
    }
    applyGroupPayloadToForm();
    return new Promise((resolve, reject) => {
        groupSaveForm.patch(props.patchUrl, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                captureSavedSnapshot();
                resolve({ ok: true });
            },
            onError: (errors) => {
                const err = new Error('Effect group validation failed');
                err.errors = errors;
                reject(err);
            },
        });
    });
}

function getActiveEffectId() {
    return degreeForms.value[activeTab.value]?.id ?? null;
}

const saving = computed(() => groupSaveForm.processing || deleteDegreeForm.processing || jsonSaving.value);

/** ID définition d’effet (Effect) exploitable pour la route (évite NaN / valeurs invalides). */
const adminEffectIdForRoute = computed(() => {
    const n = Number(props.adminEffectId);
    return Number.isFinite(n) && n > 0 ? n : null;
});

/** Affiche le bouton admin (toujours visible si props OK, même avec un seul degré — alors désactivé). */
const showAdminDegreeDeleteUi = computed(
    () =>
        props.showAdminDegreeDelete &&
        adminEffectIdForRoute.value != null &&
        degreeForms.value.length > 0
);

/** Peut réellement supprimer le degré de l’onglet actif (≥ 2 degrés, id connu). */
const canDeleteActiveDegree = computed(
    () =>
        showAdminDegreeDeleteUi.value &&
        degreeForms.value.length > 1 &&
        degreeForms.value[activeTab.value]?.id != null
);

const deleteDegreeDisabledHint =
    'Au moins deux degrés sont requis pour en supprimer un. Pour retirer toute la définition, utilisez « Supprimer » dans l’en-tête de la page.';

function deleteActiveDegree() {
    if (!canDeleteActiveDegree.value) return;
    const deg = degreeForms.value[activeTab.value];
    if (!deg?.id || adminEffectIdForRoute.value == null) return;
    if (
        !confirm(
            `Supprimer le degré ${deg.degree} ? Les sous-effets de ce degré seront supprimés. Cette action est irréversible.`
        )
    ) {
        return;
    }
    deleteDegreeForm.delete(route('admin.effects.destroy-degree', [adminEffectIdForRoute.value, deg.id]));
}

/** Validation du champ zone pour l’onglet degré actif (affiche une erreur si rempli mais invalide). */
const activeAreaValidation = computed(() => {
    const raw = degreeForms.value[activeTab.value]?.area;
    if (raw == null || String(raw).trim() === '') return undefined;
    return isValidAreaNotation(raw)
        ? undefined
        : { state: 'error', message: `Notation invalide. ${AREA_NOTATION_HELP}` };
});

defineExpose({
    getActiveEffectId,
    degreeForms,
    activeTab,
    saving,
    isDirty,
    submitGroup,
    submitGroupAsync,
    /** Revient à l’état reçu du serveur (« Annuler les modifications »). */
    resetToSaved: initDegreeFormsFromProps,
});
</script>

<template>
    <div class="space-y-6">
        <div v-if="heading" class="flex flex-wrap items-center gap-2">
            <h3 class="text-lg font-semibold">{{ heading }}</h3>
            <span
                v-if="showTargetTypeBadge(common.target_type)"
                class="badge badge-sm badge-primary badge-outline shrink-0"
                :title="'Type de cible : ' + targetTypeLabel(common.target_type)"
            >
                {{ targetTypeLabel(common.target_type) }}
            </span>
            <span
                v-if="isDirty"
                class="badge badge-sm badge-warning shrink-0"
                title="Modifications non enregistrées dans ce groupe d’effets"
            >
                Non enregistré
            </span>
        </div>
        <form class="space-y-6" @submit.prevent="submitGroup">
            <div class="card bg-base-100 shadow border border-base-300">
                <div class="card-body">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="card-title text-lg">Données communes au groupe</h2>
                        <span
                            v-if="!heading && showTargetTypeBadge(common.target_type)"
                            class="badge badge-sm badge-primary badge-outline shrink-0"
                            :title="'Type de cible : ' + targetTypeLabel(common.target_type)"
                        >
                            {{ targetTypeLabel(common.target_type) }}
                        </span>
                        <span
                            v-if="!heading && isDirty"
                            class="badge badge-sm badge-warning shrink-0"
                            title="Modifications non enregistrées dans ce groupe d’effets"
                        >
                            Non enregistré
                        </span>
                    </div>
                    <p class="text-sm text-base-content/70 mb-2">
                        Appliquées à chaque degré : nom, description, type de cible. La zone et le seuil de niveau créature se règlent dans chaque onglet de degré.
                    </p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <InputField v-model="common.name" label="Nom" name="common_name" />
                        <div class="sm:col-span-2">
                            <InputField v-model="common.description" label="Description (aperçu)" name="common_description" type="textarea" />
                        </div>
                        <SelectField
                            v-model="common.target_type"
                            label="Type de cible"
                            name="common_target_type"
                            :options="TARGET_TYPE_OPTIONS"
                            :searchable="false"
                            helper="Direct, piège ou glyphe."
                        />
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow border border-base-300">
                <div class="card-body space-y-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                        <h2 class="card-title text-lg">Degrés</h2>
                        <button
                            v-if="showAdminDegreeDeleteUi"
                            type="button"
                            class="btn btn-ghost btn-error btn-sm w-full sm:w-auto shrink-0"
                            :disabled="!canDeleteActiveDegree || saving"
                            :title="saving || canDeleteActiveDegree ? undefined : deleteDegreeDisabledHint"
                            @click="deleteActiveDegree"
                        >
                            Supprimer ce degré
                        </button>
                    </div>
                    <div role="tablist" class="tabs tabs-boxed flex-wrap gap-1">
                        <button
                            v-for="(tab, ti) in degreeForms"
                            :key="tab.id"
                            type="button"
                            role="tab"
                            class="tab"
                            :class="{ 'tab-active': activeTab === ti }"
                            @click="activeTab = ti"
                        >
                            Degré {{ tab.degree ?? '?' }}
                        </button>
                    </div>
                    <p
                        v-if="showAdminDegreeDeleteUi && degreeForms.length === 1"
                        class="text-xs text-base-content/60"
                    >
                        Avec un seul degré, supprimez plutôt toute la définition (bouton sous le formulaire).
                    </p>

                    <div v-if="degreeForms[activeTab]" class="space-y-4 border-t border-base-300 pt-4">
                        <p
                            v-if="degreeForms[activeTab].slug"
                            class="text-[0.65rem] leading-tight text-base-content/40 font-mono tracking-tight -mt-1 mb-1"
                            title="Identifiant technique du degré (non modifiable depuis cet écran)"
                        >
                            {{ degreeForms[activeTab].slug }}
                        </p>
                        <div class="grid gap-4 sm:grid-cols-2 max-w-3xl">
                            <InputField
                                v-model="degreeForms[activeTab].required_creature_level"
                                label="Niveau créature min."
                                :name="'lvl_d' + degreeForms[activeTab].degree"
                                type="number"
                                helper="Vide = toujours actif pour le porteur."
                            />
                        </div>
                        <div class="space-y-2 max-w-3xl">
                            <p class="text-sm font-medium text-base-content">Zone (ce degré)</p>
                            <AreaNotationEditor
                                v-model="degreeForms[activeTab].area"
                                :name="'area_d' + degreeForms[activeTab].degree"
                                :validation="activeAreaValidation"
                            />
                        </div>
                        <div
                            v-if="hasDegreeExtraSlot && degreeForms[activeTab]?.id"
                            class="rounded-box border border-base-300 bg-base-200/40 p-4"
                        >
                            <slot
                                name="degree-extra"
                                :effect-id="degreeForms[activeTab].id"
                                :degree="degreeForms[activeTab].degree"
                                :tab-index="activeTab"
                            />
                        </div>
                        <div
                            class="flex flex-wrap items-start justify-between gap-3 border-t border-base-300/80 pt-4"
                        >
                            <div>
                                <h3 class="font-semibold text-base">
                                    Sous-effets (degré {{ degreeForms[activeTab].degree ?? '?' }})
                                </h3>
                                <p class="text-xs text-base-content/60 mt-0.5 max-w-xl">
                                    Enchaînement : entre deux blocs, définissez le lien avec le précédent (ET = le précédent
                                    doit s’appliquer ; OU = ce bloc s’applique si la condition &gt; 0).
                                </p>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary shrink-0" @click="addSubEffect">
                                + Ajouter un sous-effet
                            </button>
                        </div>
                        <div v-if="!degreeForms[activeTab].effect_sub_effects.length" class="text-sm text-base-content/70 py-4">
                            Aucun sous-effet. Utilisez « Ajouter un sous-effet ».
                        </div>
                        <div v-else class="space-y-3">
                            <template
                                v-for="(row, index) in degreeForms[activeTab].effect_sub_effects"
                                :key="'d' + activeTab + '-r' + index"
                            >
                                <!-- Lien logique avec le bloc précédent (hors carte) -->
                                <div
                                    v-if="index > 0"
                                    class="rounded-lg border border-dashed border-base-300 bg-base-200/50 px-3 py-2"
                                >
                                    <div class="text-xs font-semibold uppercase tracking-wide text-base-content/70">
                                        Lien avec le sous-effet précédent
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-end gap-2">
                                        <div class="min-w-44">
                                            <label class="label text-xs py-0">Enchaînement</label>
                                            <select
                                                v-model="row.logic_operator"
                                                class="select select-bordered select-sm w-full"
                                            >
                                                <option value="AND">ET — le précédent doit s’appliquer</option>
                                                <option value="OR">OU — si la condition &gt; 0</option>
                                            </select>
                                        </div>
                                        <div v-if="row.logic_operator === 'OR'" class="flex-1 min-w-48 max-w-lg">
                                            <label class="label text-xs py-0">Condition (formule &gt; 0)</label>
                                            <input
                                                v-model="row.logic_condition"
                                                type="text"
                                                class="input input-bordered input-sm w-full"
                                                placeholder="ex: [target_is_ally]"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="rounded-box border border-base-300 bg-base-100 overflow-hidden shadow-sm"
                                >
                                    <!-- Barre : action, contexte, critique seulement, actions -->
                                    <div
                                        class="flex flex-wrap items-end gap-2 gap-y-2 px-3 py-2 border-b border-base-300 bg-base-200/40"
                                    >
                                        <div class="min-w-40 flex-1">
                                            <label class="text-xs font-medium text-base-content/70">Action</label>
                                            <select
                                                v-model="row.sub_effect_id"
                                                class="select select-bordered select-sm w-full mt-0.5"
                                                required
                                                @change="onSubEffectChange(row)"
                                            >
                                                <option value="">— Choisir —</option>
                                                <option
                                                    v-for="s in options.sub_effects"
                                                    :key="s.id"
                                                    :value="s.id"
                                                >
                                                    {{ formatSubEffectSelectLabel(s) }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="w-29">
                                            <label class="text-xs font-medium text-base-content/70">Contexte</label>
                                            <select
                                                v-model="row.scope"
                                                class="select select-bordered select-sm w-full mt-0.5"
                                            >
                                                <option
                                                    v-for="sc in options.scopes"
                                                    :key="sc.value"
                                                    :value="sc.value"
                                                >
                                                    {{ sc.label }}
                                                </option>
                                            </select>
                                        </div>
                                        <label
                                            class="flex items-center gap-2 cursor-pointer shrink-0 max-w-56 mb-0.5"
                                        >
                                            <input v-model="row.crit_only" type="checkbox" class="checkbox checkbox-sm" />
                                            <span class="text-xs leading-snug">Uniquement si critique</span>
                                        </label>
                                        <div class="flex gap-0.5 ml-auto">
                                            <button
                                                type="button"
                                                class="btn btn-ghost btn-sm btn-square"
                                                title="Dupliquer ce sous-effet"
                                                @click="duplicateSubEffect(index)"
                                            >
                                                +
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-ghost btn-sm btn-square text-error"
                                                title="Supprimer ce sous-effet"
                                                @click="removeSubEffect(index)"
                                            >
                                                ×
                                            </button>
                                        </div>
                                    </div>

                                    <div v-if="row.sub_effect_id" class="p-3 space-y-4">
                                        <!-- État (référentiel local) -->
                                        <div v-if="rowHasConditionParam(row)" class="space-y-2 max-w-xl">
                                            <label class="text-xs font-medium text-base-content/80">État</label>
                                            <input
                                                v-model="row._editor_condition_q"
                                                type="search"
                                                class="input input-bordered input-sm w-full"
                                                placeholder="Rechercher par nom ou n°…"
                                                autocomplete="off"
                                            />
                                            <ul
                                                class="menu menu-xs bg-base-200 rounded-box max-h-48 overflow-y-auto border border-base-300 p-1"
                                                role="listbox"
                                            >
                                                <li v-for="st in filteredConditionsForRow(row)" :key="st.id">
                                                    <button
                                                        type="button"
                                                        class="flex items-center gap-2 text-left rounded-btn"
                                                        :class="
                                                            Number(row.params.condition_id) === Number(st.id)
                                                                ? 'bg-base-300 font-medium'
                                                                : ''
                                                        "
                                                        @click="selectCondition(row, st)"
                                                    >
                                                        <Icon
                                                            v-if="conditionIconSource(st)"
                                                            :source="conditionIconSource(st)"
                                                            :alt="st.name || ''"
                                                            size="xs"
                                                        />
                                                        <span class="truncate">{{ st.name }} ({{ st.dofusdb_id }})</span>
                                                    </button>
                                                </li>
                                                <li v-if="String(row._editor_condition_q ?? '').trim()">
                                                    <button
                                                        type="button"
                                                        class="flex items-center gap-2 text-left rounded-btn text-primary font-medium"
                                                        @click="createConditionForRow(row)"
                                                    >
                                                        <Icon source="fa-solid fa-plus" size="xs" />
                                                        <span class="truncate">
                                                            Créer « {{ String(row._editor_condition_q ?? '').trim() }} »
                                                        </span>
                                                    </button>
                                                </li>
                                            </ul>
                                            <label
                                                v-if="rowHasConditionParam(row)"
                                                class="flex items-center gap-2 cursor-pointer"
                                            >
                                                <input
                                                    v-model="row.params.dispellable"
                                                    type="checkbox"
                                                    class="checkbox checkbox-sm"
                                                />
                                                <span class="text-xs">Dissipable</span>
                                            </label>
                                        </div>

                                        <!-- Déplacement : cases + téléportation -->
                                        <div v-if="rowHasCellsFormulaParam(row)" class="space-y-2 max-w-xl">
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-base-content/80"
                                                    >Nombre de cases (formule)</label
                                                >
                                                <input
                                                    v-model="row.params.cells_formula"
                                                    type="text"
                                                    inputmode="decimal"
                                                    class="input input-bordered input-sm w-full"
                                                    placeholder="ex: 3, 0,33, [level], 1d3+1…"
                                                    @input="syncDeplacementValueFormula(row)"
                                                />
                                                <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                    Distance en <span class="font-medium">cases</span>                                                     (1 case =
                                                    {{ CASE_SIZE_METERS }} m). Décimales autorisées (ex. 0,33 ≈ 0,5 m). Les
                                                    formules avec dés ou variables n’affichent pas de conversion automatique.
                                                </p>
                                                <p
                                                    v-if="deplacementMetersPreview(row)"
                                                    class="text-[0.7rem] font-medium tabular-nums text-primary"
                                                >
                                                    {{ deplacementMetersPreview(row) }}
                                                </p>
                                            </div>
                                            <label v-if="rowHasTeleportParam(row)" class="flex items-center gap-2 cursor-pointer">
                                                <input
                                                    v-model="row.params.teleport"
                                                    type="checkbox"
                                                    class="checkbox checkbox-sm"
                                                    @change="syncTeleportSideEffects(row)"
                                                />
                                                <span class="text-xs">Téléportation (sinon déplacement simple)</span>
                                            </label>
                                            <div v-if="rowHasMovementKindParam(row)" class="space-y-1">
                                                <label class="text-xs font-medium text-base-content/80">Type de mouvement</label>
                                                <select
                                                    v-model="row.params.movement_kind"
                                                    class="select select-bordered select-sm w-full"
                                                    @change="syncMovementKindSideEffects(row)"
                                                >
                                                    <option
                                                        v-for="opt in MOVEMENT_KIND_OPTIONS"
                                                        :key="opt.value"
                                                        :value="opt.value"
                                                    >
                                                        {{ opt.label }}
                                                    </option>
                                                </select>
                                                <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                    Sert aux normes et à la conversion : saut, téléportation, attirance et repousse n’ont pas les mêmes plafonds.
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Caractéristique / élément -->
                                        <template v-if="rowHasCharacteristicParam(row)">
                                            <SelectSearchField
                                                size="xs"
                                                :label="characteristicLabelForRow(row)"
                                                :placeholder="characteristicLabelForRow(row) + '…'"
                                                :options="characteristicSelectOptionsForRow(row)"
                                                :model-value="row.params.characteristic"
                                                @update:model-value="row.params.characteristic = $event"
                                                :searchable="characteristicsForRow(row).length > 8"
                                            />
                                        </template>

                                        <!-- Valeur + critique -->
                                        <div
                                            v-if="rowHasValueParam(row)"
                                            class="grid gap-3 sm:grid-cols-2"
                                        >
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-base-content/80">Valeur (formule)</label>
                                                <input
                                                    v-model="row.params.value_formula"
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    placeholder="ex: 2d6, [1-4], [level]*2+[agi]"
                                                />
                                                <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                    {{ valueHelpTextForRow(row) }}
                                                </p>
                                            </div>
                                            <div v-if="!row.crit_only" class="space-y-1">
                                                <label class="text-xs font-medium text-base-content/80"
                                                    >Valeur critique (optionnel)</label
                                                >
                                                <input
                                                    v-model="row.params.value_formula_crit"
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    placeholder="ex: [value]*2, 3d6…"
                                                />
                                                <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                    Remplace la valeur ci-contre lors d’un coup critique (si vide : même
                                                    valeur que la colonne de gauche).
                                                </p>
                                            </div>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-xs font-medium text-base-content/80"
                                                >Durée (formule, tours ou secondes)</label
                                            >
                                            <input
                                                v-model="row.duration_formula"
                                                type="text"
                                                class="input input-bordered input-sm w-full max-w-xl"
                                                placeholder="ex: 2 (tours), [level]/2, 10 (secondes)…"
                                            />
                                            <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                {{ durationHelpTextForRow(row) }}
                                            </p>
                                        </div>

                                        <div v-if="rowHasLifeStealFormulaParam(row)" class="space-y-1 max-w-xl">
                                            <label class="text-xs font-medium text-base-content/80"
                                                >PV volés (formule, optionnel)</label
                                            >
                                            <input
                                                v-model="row.params.life_steal_formula"
                                                type="text"
                                                class="input input-bordered input-sm w-full"
                                                placeholder="ex: [dgt]/2, 50%, [dgt]+2d4"
                                            />
                                            <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                Vide = dégâts seuls. Avec formule : vol de vie — [dgt] = dégâts primaires ;
                                                « 50% » = moitié des dégâts rendus en PV au lanceur.
                                            </p>
                                        </div>

                                        <template v-if="rowHasMonsterParam(row)">
                                            <div class="space-y-1 max-w-md">
                                                <label class="text-xs font-medium text-base-content/80">Monstre</label>
                                                <EntityPickerCore
                                                    v-model="row.params.monster_id"
                                                    entity-type="monsters"
                                                    :multiple="false"
                                                    variant="compact"
                                                    placeholder="Choisir un monstre…"
                                                    size="sm"
                                                />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="!hideSubmitButton" class="flex flex-wrap items-center gap-2">
                <template v-if="embeddedInModal">
                    <Btn
                        type="button"
                        color="primary"
                        size="sm"
                        :disabled="groupSaveForm.processing"
                        @click="submitGroup"
                    >
                        <i :class="ACTION.save.icon" class="mr-1.5" aria-hidden="true"></i>
                        {{ groupSaveForm.processing ? ACTION.save.processing : submitLabel }}
                    </Btn>
                </template>
                <EditActionDock
                    v-else
                    :primary-label="submitLabel"
                    :processing="groupSaveForm.processing"
                    :show-secondary="false"
                    :secondary-actions="[]"
                    :fixed-on-desktop="false"
                    @primary="submitGroup"
                />
                <p
                    v-if="groupSaveForm.recentlySuccessful"
                    :class="embeddedInModal ? 'text-xs text-success' : 'text-sm text-success'"
                >
                    Enregistré.
                </p>
            </div>
        </form>
    </div>
</template>
