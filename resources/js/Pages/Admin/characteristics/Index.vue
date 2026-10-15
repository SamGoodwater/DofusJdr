<script setup>
/**
 * Admin Caractéristiques — Liste à gauche, panneau d'édition à droite.
 * Accès : admin et super_admin.
 * Pour chaque champ formule : graphique (variable entre min et max).
 */
import { computed, inject, nextTick, onMounted, ref, watch } from 'vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import ColorCore from '@/Pages/Atoms/data-input/ColorCore.vue';
import FormulaOrTableField from '@/Pages/Molecules/data-input/FormulaOrTableField.vue';
import FormulaOrTableFieldWithChart from '@/Pages/Organismes/data-input/FormulaOrTableFieldWithChart.vue';
import FormulaExpressionInput from '@/Pages/Molecules/data-input/FormulaExpressionInput.vue';
import ConversionChartBlock from '@/Pages/Admin/characteristics/ConversionChartBlock.vue';
import MappingPanel from '@/Pages/Admin/characteristics/MappingPanel.vue';
import NormsPanel from '@/Pages/Admin/characteristics/NormsPanel.vue';
import ItemTypesMultiField from '@/Pages/Molecules/data-input/ItemTypesMultiField.vue';
import SidebarNav from '@/Pages/Organismes/layout/SidebarNav.vue';
import axios from 'axios';
import { resolveCharacteristicUiColor } from '@/Utils/color/Color';
import { TAILWIND_CHARACTERISTIC_PALETTES } from '@/Constants/tailwindCharacteristicPalettes';

const { setPageTitle } = usePageTitle();
const notificationStore = inject('notificationStore', null);

/** Contenu d’aide pour les champs formule (affiché dans le popover « ? »). */
const formulaHelpContent = {
    title: 'Construire une formule',
    variables:
        'Variables évaluées côté serveur : toujours entre crochets, ex. [vitality_creature], [modifier_intelligence_creature], [level], [d] (conversion). Les champs proposent l’autocomplétion après 3 caractères.',
    operators: 'Opérateurs : + - * / et parenthèses ( ).',
    funcs1: 'Fonctions à 1 argument : floor, ceil, round, sqrt, abs, cos, sin, tan, asin, acos, atan.',
    funcs2: 'Fonctions à 2 arguments : pow(base, exp), min(a, b), max(a, b).',
    examples:
        'Exemples : [modifier_intelligence_creature], 10+[modifier_vitality_creature], [level_creature]*2, floor([d]/10), min(10,[level]*2). Formule affichée : texte joueur abrégé (ex. mod. Int + équip.), sans consommable ni sort.',
    equipment:
        'Groupe créature : listez tous les termes — intrinsèques, équipement (*_object), consommable (*_consumable), sort (*_spell). Les termes consommable et sort (nature Buff / Débuff) sont éphémères : ils valent 0 hors combat et ne sont jamais bornés par une limite absolue.',
};

const props = defineProps({
    /** Caractéristiques regroupées par groupe : { creature: [{ id, name, ... }], object: [...], spell: [...] } */
    characteristicsByGroup: { type: Object, default: () => ({ creature: [], object: [], spell: [], consumable: [], profile: [] }) },
    selected: { type: Object, default: null },
    createMode: { type: Boolean, default: false },
    groups: { type: Array, default: () => ['creature', 'object', 'spell', 'consumable', 'profile'] },
    /** Options de nature (enum) pour les selects admin. */
    natures: { type: Array, default: () => [] },
    /** Caractéristiques créature intrinsèques (lien bonus_malus / buff_debuff). */
    intrinsicCharacteristicOptions: { type: Array, default: () => [] },
    entitiesByGroup: { type: Object, default: () => ({}) },
    entitiesTemplate: { type: Object, default: () => ({}) },
    /** Liste des caractéristiques non liées pour "Lier à" / "Copier depuis" (mode création). */
    characteristicsForLinkOrCopy: { type: Array, default: () => [] },
    /** Données de la caractéristique source pour préremplir le formulaire "Copier depuis". */
    copyFromCharacteristic: { type: Object, default: null },
    /** Liste des caractéristiques maîtres possibles pour « Convertir en liée » (mode édition, non liée). */
    characteristicsForConvertToLinked: { type: Array, default: () => [] },
    /** Règles de mapping scrapping (source, entity, mapping_key, from_path, targets) qui utilisent cette caractéristique (panneau 3). */
    scrappingMappingsUsingThis: { type: Array, default: () => [] },
    /** Options pour le select "Fonction de conversion" (mode création : passé à la racine). */
    conversionFunctionOptions: { type: Array, default: () => [] },
    /** Types d'équipement (item_types) pour restriction des caractéristiques groupe object. */
    itemTypes: { type: Array, default: () => [] },
});

const characteristicStatusOptions = [
    { value: 'a_valider', label: 'À valider' },
    { value: 'en_cours_de_validation', label: 'En cours de validation' },
    { value: 'validee', label: 'Validée' },
];

const characteristicStatusDotMap = {
    a_valider: { label: 'À valider', class: 'bg-slate-500' },
    en_cours_de_validation: { label: 'En cours de validation', class: 'bg-emerald-700' },
    validee: { label: 'Validée', class: 'bg-green-700' },
};

/** Options du select "Fonction de conversion" : depuis selected (édition) ou props (création). */
const conversionFunctionSelectOptions = computed(() =>
    (props.selected?.conversionFunctionOptions ?? props.conversionFunctionOptions ?? []).map((opt) => ({
        value: opt.id ?? opt.value ?? '',
        label: opt.label ?? opt.id ?? '',
    }))
);

/** Groupe « object » : édition ou création (form.group). */
const isObjectCharacteristicGroup = computed(() => (props.selected?.group ?? form.group) === 'object');
/** Groupe « creature » : formule complète (équipement explicite, buffs éphémères) et limite absolue. */
const isCreatureCharacteristicGroup = computed(() => (props.selected?.group ?? form.group) === 'creature');

/**
 * Normalise les ids (select multiple renvoie parfois des chaînes).
 * @param {unknown} raw
 * @returns {number[]}
 */
function normalizeItemTypeIds(raw) {
    if (!Array.isArray(raw)) return [];
    return [...new Set(raw.map((id) => parseInt(String(id), 10)).filter((n) => !Number.isNaN(n)))];
}

/** Entités affichées en panneaux supplémentaires (hors Général). Clic sur "Spécifier pour une entité". */
const selectedEntityOverrides = ref([]);

/** Mode de création : nouvelle, lier à une existante, ou copier depuis une existante. */
const createModeType = ref('new');
/** Pour le mode "Lier" : id de la caractéristique maître et groupe de la liée. */
const linkForm = ref({ linked_to_characteristic_id: '', group: 'object', key: '' });
/** Pour le mode "Copier" : clé choisie dans le select (redirection vers ?copy_from=key). */
const copySelectKey = ref('');
/** Pour « Convertir en liée » (édition) : id de la caractéristique maître choisie. */
const convertToLinkedMasterId = ref('');
/** Afficher le panneau « Convertir en caractéristique liée » (déplié au clic sur Lier). */
const showConvertToLinkedPanel = ref(false);

/** Fermer le panneau Lier quand on change de caractéristique. */
watch(() => props.selected?.id, () => { showConvertToLinkedPanel.value = false; });

/** Popover aide formules : rendu dans body pour passer au-dessus du menu (Teleport). null = fermé, sinon { left, top } en px. */
const formulaHelpAnchor = ref(null);
const formulaHelpPopoverRef = ref(null);

/** Proposition de formule de conversion (affichée jusqu'à validation ou annulation). */
const conversionSuggestionFormula = ref(null);
const conversionSuggestionR2 = ref(null);
const conversionSuggestionLoading = ref(false);
const conversionSuggestionError = ref(null);
/** Entité concernée par la suggestion en cours ('*' = groupe, sinon clé d'entité). */
const conversionSuggestionForEntity = ref(null);

/** Ligne entity pour le groupe (*) ou une entité spécifique. */
function getConversionRow(entityKey) {
    return entityKey === '*' ? generalEntityRow() : entityRow(entityKey);
}

/** Textes d’aide pour les boutons de proposition de formule (tooltips). */
const conversionSuggestionTooltips = {
    table: 'Utilise exactement les paires (d, k) du tableau. Idéal pour une courbe irrégulière ou un contrôle point par point.',
    linear: 'k = a×d + b. À utiliser quand les points sont presque alignés (croissance régulière).',
    power: 'k = a×d^b. À utiliser quand la croissance accélère avec d (typique des stats Dofus).',
    shifted_power: 'k = a + b×((d-c)/e)^f. Courbe en puissance qui ne part pas de 0. Utile quand les valeurs ne suivent pas une simple puissance.',
    exponential: 'k = a×exp(b×d). Croissance très rapide. Exige des valeurs Krosmoz > 0.',
    log: 'k = a×ln(d) + b. Croissance qui ralentit. Exige des valeurs Dofus > 0.',
    polynomial2: 'k = a×d² + b×d + c. Courbe avec une seule courbure (parabole). Au moins 3 points.',
};

/** Demande une formule suggérée (table, linéaire, carré, carré décalé). Utilise les lignes du tableau (paires d, k). */
function requestConversionSuggestion(curveType, entityKey = '*') {
    const row = getConversionRow(entityKey);
    if (!row) {
        conversionSuggestionForEntity.value = entityKey;
        conversionSuggestionError.value = 'Aucune ligne d\'entité pour ce groupe. Rechargez la page ou enregistrez d\'abord la caractéristique.';
        conversionSuggestionFormula.value = null;
        conversionSuggestionR2.value = null;
        return;
    }
    const sampleRows = row.conversion_sample_rows ?? [];
    const pairs = sampleRows
        .filter((r) => r.dofus_value !== '' && r.dofus_value != null && r.krosmoz_value !== '' && r.krosmoz_value != null)
        .filter((r) => !Number.isNaN(Number(r.dofus_value)) && !Number.isNaN(Number(r.krosmoz_value)))
        .map((r) => ({ d: Number(r.dofus_value), k: Number(r.krosmoz_value) }));
    if (pairs.length < 2) {
        conversionSuggestionError.value = 'Renseignez au moins 2 lignes avec des valeurs Dofus et Krosmoz pour proposer une formule.';
        conversionSuggestionFormula.value = null;
        conversionSuggestionR2.value = null;
        conversionSuggestionForEntity.value = null;
        return;
    }
    conversionSuggestionError.value = null;
    conversionSuggestionFormula.value = null;
    conversionSuggestionR2.value = null;
    conversionSuggestionForEntity.value = entityKey;
    conversionSuggestionLoading.value = true;
    const suggestUrl = typeof route === 'function' ? (() => { try { return route('admin.characteristics.suggest-conversion-formula'); } catch { return '/admin/content/characteristics/suggest-conversion-formula'; } })() : '/admin/content/characteristics/suggest-conversion-formula';
    axios
        .post(suggestUrl, {
            pairs,
            curve_type: curveType,
        })
        .then((res) => {
            conversionSuggestionFormula.value = res.data.formula ?? null;
            conversionSuggestionR2.value = res.data.r2 ?? null;
            conversionSuggestionLoading.value = false;
        })
        .catch((err) => {
            conversionSuggestionError.value = err.response?.data?.message ?? err.message ?? 'Erreur lors de la suggestion';
            conversionSuggestionFormula.value = null;
            conversionSuggestionR2.value = null;
            conversionSuggestionLoading.value = false;
        });
}

/** Ajoute une ligne au tableau d'échantillons (groupe ou entité spécifique). */
function addConversionSampleRow(entityKey = '*') {
    const row = getConversionRow(entityKey);
    if (!row) return;
    const rows = Array.isArray(row.conversion_sample_rows) ? [...row.conversion_sample_rows] : getDefaultConversionSampleRows();
    const last = rows[rows.length - 1];
    const nextDofus = last ? Number(last.dofus_level) + 40 : 200;
    const nextKrosmoz = last ? Number(last.krosmoz_level) + 4 : 20;
    rows.push({ dofus_level: nextDofus, dofus_value: '', krosmoz_level: nextKrosmoz, krosmoz_value: '' });
    row.conversion_sample_rows = rows;
}

/** Calcule les bornes min/max des échantillons (Dofus = abscisses, Krosmoz = ordonnées) pour le graphique. */
function conversionBoundsFromRow(row) {
    if (!row?.conversion_sample_rows?.length) return null;
    const values = row.conversion_sample_rows
        .filter((r) => r.dofus_value !== '' && r.dofus_value != null && r.krosmoz_value !== '' && r.krosmoz_value != null)
        .filter((r) => !Number.isNaN(Number(r.dofus_value)) && !Number.isNaN(Number(r.krosmoz_value)))
        .map((r) => ({ d: Number(r.dofus_value), k: Number(r.krosmoz_value) }));
    if (values.length === 0) return null;
    const dMin = Math.min(...values.map((v) => v.d));
    const dMax = Math.max(...values.map((v) => v.d));
    const kMin = Math.min(...values.map((v) => v.k));
    const kMax = Math.max(...values.map((v) => v.k));
    if (dMin === dMax) return null;
    return { dMin, dMax, kMin, kMax };
}

/** Supprime une ligne du tableau d'échantillons. Minimum 1 ligne. */
function removeConversionSampleRow(entityKey, index) {
    if (entityKey === undefined) {
        entityKey = '*';
    }
    const row = getConversionRow(entityKey);
    if (!row || !Array.isArray(row.conversion_sample_rows) || row.conversion_sample_rows.length <= 1) return;
    row.conversion_sample_rows = row.conversion_sample_rows.filter((_, i) => i !== index);
}

/** Valide la formule suggérée : remplace la valeur du champ conversion pour l'entité concernée et efface la proposition. */
function applyConversionSuggestion() {
    const entityKey = conversionSuggestionForEntity.value ?? '*';
    const row = getConversionRow(entityKey);
    if (row && conversionSuggestionFormula.value) {
        row.conversion_formula = conversionSuggestionFormula.value;
        conversionSuggestionFormula.value = null;
        conversionSuggestionR2.value = null;
        conversionSuggestionForEntity.value = null;
    }
}

/** Annule la proposition (sans modifier le champ). */
function clearConversionSuggestion() {
    conversionSuggestionFormula.value = null;
    conversionSuggestionR2.value = null;
    conversionSuggestionError.value = null;
    conversionSuggestionForEntity.value = null;
}

function openFormulaHelp(e) {
    const rect = e.currentTarget.getBoundingClientRect();
    formulaHelpAnchor.value = { left: rect.left, top: rect.bottom + 4 };
    nextTick(() => {
        const close = (ev) => {
            if (formulaHelpPopoverRef.value?.contains(ev.target)) return;
            formulaHelpAnchor.value = null;
            document.removeEventListener('click', close);
        };
        document.addEventListener('click', close);
    });
}

defineOptions({ layout: AdminArea });
setPageTitle('Administration des caractéristiques');

const page = usePage();
const adminUnlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showAdminConfirmModal = ref(false);
function onAdminPasswordConfirmed() {
    adminUnlocked.value = true;
}

/** Lignes par défaut pour le tableau Dofus/Krosmoz (6 paires niveau/valeur). */
function getDefaultConversionSampleRows() {
    const dofusLevels = [1, 40, 80, 120, 160, 200];
    const krosmozLevels = [1, 4, 8, 12, 16, 20];
    return dofusLevels.map((dofusLevel, i) => ({
        dofus_level: dofusLevel,
        dofus_value: '',
        krosmoz_level: krosmozLevels[i] ?? i + 1,
        krosmoz_value: '',
    }));
}

/** Conversion formulas par défaut pour un groupe (entités du groupe hors '*'). */
function defaultConversionFormulasForGroup(entitiesByGroup, group = 'creature') {
    const entities = (entitiesByGroup?.[group] ?? []).filter((e) => e !== '*');
    return entities.map((entity) => ({
        entity,
        conversion_formula: '',
        formula_display: '',
        handler_name: '',
    }));
}

function buildFormData(selected, entitiesByGroup = null) {
    if (!selected) {
        return {
            key: '',
            group: 'creature',
            name: '',
            short_name: '',
            description: '',
            helper: '',
            icon: '',
            color: '',
            type: 'int',
            status: 'a_valider',
            unit: '',
            sort_order: 0,
            nature: '',
            intrinsic_characteristic_id: '',
            hide_when_empty: false,
            hide_when_false: false,
            value_overrides: [],
            entities: [],
            conversion_formulas: defaultConversionFormulasForGroup(entitiesByGroup ?? {}, 'creature'),
        };
    }
    const group = selected.group ?? 'creature';
    const allowedEntityKeys = entitiesByGroup?.[group] ?? ['*', 'monster', 'class', 'npc'];
    const entitiesForGroup = (selected.entities ?? []).filter((e) => allowedEntityKeys.includes(e.entity));
    // Le backend envoie déjà conversion_formulas uniquement pour les entités du groupe de la caractéristique
    const conversionFormulas = (selected.conversion_formulas ?? []).map((row) => ({
        entity: row.entity,
        conversion_formula: row.conversion_formula ?? '',
        formula_display: row.formula_display ?? '',
        handler_name: row.handler_name ?? '',
    }));
    return {
        key: '',
        group: '',
        name: selected.name ?? '',
        short_name: selected.short_name ?? '',
        description: selected.description ?? '',
        helper: selected.helper ?? '',
        icon: selected.icon ?? '',
        color: selected.color ?? '',
        type: selected.type ?? 'int',
        status: selected.status ?? 'a_valider',
        unit: selected.unit ?? '',
        sort_order: selected.sort_order ?? 0,
        nature: selected.nature ?? '',
        intrinsic_characteristic_id: selected.intrinsic_characteristic_id ?? '',
        hide_when_empty: selected.hide_when_empty ?? false,
        hide_when_false: selected.hide_when_false ?? false,
        value_overrides: Array.isArray(selected.value_overrides) ? selected.value_overrides.map((o) => ({ ...o })) : [],
        entities: entitiesForGroup.map((e) => ({
            entity: e.entity,
            db_column: e.db_column ?? '',
            min: e.min ?? '',
            max: e.max ?? '',
            limit_absolute: e.limit_absolute ?? null,
            formula: e.formula ?? '',
            formula_display: e.formula_display ?? '',
            default_value: e.default_value ?? '',
            forgemagie_max: e.forgemagie_max ?? 0,
            base_price_per_unit: e.base_price_per_unit ?? '',
            rune_price_per_unit: e.rune_price_per_unit ?? '',
            conversion_formula: e.conversion_formula ?? '',
            conversion_function: e.conversion_function ?? '',
            conversion_dofus_sample: e.conversion_dofus_sample ?? null,
            conversion_krosmoz_sample: e.conversion_krosmoz_sample ?? null,
            conversion_sample_rows: (e.conversion_sample_rows && e.conversion_sample_rows.length) ? e.conversion_sample_rows : getDefaultConversionSampleRows(),
            norms_grid: e.norms_grid || null,
            norms_formula: e.norms_formula || null,
            norms_power_coefficients: e.norms_power_coefficients || null,
            norms_conditions: e.norms_conditions || [],
            norms_description: e.norms_description || '',
            norms_help_section_id: e.norms_help_section_id ?? null,
            item_type_ids: Array.isArray(e.item_type_ids) ? e.item_type_ids.map((id) => parseInt(String(id), 10)).filter((n) => !Number.isNaN(n)) : [],
        })),
        conversion_formulas: conversionFormulas,
    };
}

const form = useForm(buildFormData(props.selected, props.entitiesByGroup));

/** Enregistrement d’une fiche existante (router.patch, hors form.processing). */
const characteristicSaving = ref(false);

/**
 * Photo des champs enregistrables, pour n’activer « Enregistrer » qu’après une vraie modification.
 *
 * @returns {string}
 */
function characteristicEditSnapshot() {
    return JSON.stringify({
        name: form.name ?? '',
        short_name: form.short_name ?? '',
        description: form.description ?? '',
        helper: form.helper ?? '',
        icon: form.icon ?? '',
        color: form.color ?? '',
        type: form.type ?? '',
        status: form.status ?? '',
        unit: form.unit ?? '',
        sort_order: form.sort_order ?? 0,
        nature: form.nature ?? '',
        intrinsic_characteristic_id: form.intrinsic_characteristic_id ?? '',
        hide_when_empty: !!form.hide_when_empty,
        hide_when_false: !!form.hide_when_false,
        value_overrides: form.value_overrides ?? [],
        entities: form.entities ?? [],
        entity_override_keys: selectedEntityOverrides.value,
    });
}

/** Dernière photo considérée comme enregistrée. */
const characteristicBaseline = ref('');

/** Aligne la photo sur l’état courant (chargement ou succès). */
function captureCharacteristicBaseline() {
    characteristicBaseline.value = characteristicEditSnapshot();
}

/** Vrai dès qu’un champ de la fiche diffère de la dernière photo enregistrée. */
const characteristicDirty = computed(() => characteristicEditSnapshot() !== characteristicBaseline.value);

watch(
    () => props.selected,
    (s) => {
        const data = buildFormData(s, props.entitiesByGroup);
        if (data.key !== undefined) form.key = data.key;
        if (data.group !== undefined) form.group = data.group;
        form.name = data.name;
        form.short_name = data.short_name;
        form.description = data.description;
        form.helper = data.helper;
        form.icon = data.icon;
        form.color = data.color;
        form.type = data.type;
        form.status = data.status;
        form.unit = data.unit;
        form.sort_order = data.sort_order;
        form.nature = data.nature ?? '';
        form.intrinsic_characteristic_id = data.intrinsic_characteristic_id ?? '';
        form.hide_when_empty = data.hide_when_empty;
        form.hide_when_false = data.hide_when_false;
        form.value_overrides = data.value_overrides;
        form.entities = data.entities;
        form.conversion_formulas = data.conversion_formulas;
        // Réhydrater les panneaux « spécifique entité » à partir des clés renvoyées par le serveur (ex. après enregistrement)
        selectedEntityOverrides.value = Array.isArray(s?.entity_override_keys) ? [...s.entity_override_keys] : [];
        // Réinitialiser la proposition de formule quand on change de caractéristique
        conversionSuggestionFormula.value = null;
        conversionSuggestionR2.value = null;
        conversionSuggestionError.value = null;
        conversionSuggestionForEntity.value = null;
        captureCharacteristicBaseline();
    },
    { immediate: true }
);

function addEntityOverride(entityKey) {
    if (!selectedEntityOverrides.value.includes(entityKey)) {
        selectedEntityOverrides.value = [...selectedEntityOverrides.value, entityKey];
    }
    // S'assurer qu'une ligne existe dans form.entities pour cette entité
    const existing = (form.entities ?? []).find((e) => e.entity === entityKey);
    if (!existing) {
        const defaultRow = {
            entity: entityKey,
            db_column: '',
            min: '',
            max: '',
            limit_absolute: null,
            formula: '',
            formula_display: '',
            default_value: '',
            forgemagie_max: 0,
            base_price_per_unit: '',
            rune_price_per_unit: '',
            conversion_formula: '',
            conversion_function: '',
            conversion_dofus_sample: null,
            conversion_krosmoz_sample: null,
            conversion_sample_rows: getDefaultConversionSampleRows(),
            norms_grid: null,
            norms_formula: null,
            norms_power_coefficients: null,
            norms_conditions: [],
            norms_description: '',
            norms_help_section_id: null,
            item_type_ids: [],
        };
        form.entities = [...(form.entities ?? []), defaultRow];
    }
}
async function removeEntityOverride(entityKey) {
    const label = entityLabels[entityKey] || entityKey;
    if (!confirm(`Supprimer la spécificité pour ${label} ? Les paramètres spécifiques à cette entité seront perdus.`)) {
        return;
    }
    selectedEntityOverrides.value = selectedEntityOverrides.value.filter((e) => e !== entityKey);
    const entitiesFiltered = (form.entities ?? []).filter((e) => e.entity !== entityKey);
    form.entities = entitiesFiltered;
    await nextTick();
    // Envoyer les entités filtrées + entity_override_keys pour que le backend supprime et ne recrée pas
    router.patch(route('admin.characteristics.update', props.selected.id), {
        name: form.name,
        short_name: form.short_name,
        description: form.description,
        helper: form.helper,
        icon: form.icon,
        color: form.color,
        type: form.type,
        status: form.status,
        unit: form.unit,
        sort_order: form.sort_order,
        nature: form.nature || null,
        intrinsic_characteristic_id: form.intrinsic_characteristic_id ? Number(form.intrinsic_characteristic_id) : null,
        hide_when_empty: form.hide_when_empty,
        hide_when_false: form.hide_when_false,
        entities: entitiesFiltered,
        entity_override_keys: selectedEntityOverrides.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore?.success?.('Caractéristique mise à jour.', { duration: 5000 });
        },
    });
}
function confirmDelete() {
    if (props.selected?.id && confirm('Supprimer cette caractéristique ? Les données associées seront perdues.')) {
        router.delete(route('admin.characteristics.destroy', props.selected.id));
    }
}

// En mode création : n'initialiser qu'avec la ligne par défaut (*). Les spécificités s'ajoutent via « Spécifier pour une entité ». Si copyFromCharacteristic est fourni, préremplir le formulaire.
watch(
    () => [props.createMode, props.entitiesTemplate, props.entitiesByGroup, form.group, props.copyFromCharacteristic],
    () => {
        if (props.createMode && props.copyFromCharacteristic) {
            createModeType.value = 'copy';
            const src = props.copyFromCharacteristic;
            copySelectKey.value = src.key ?? '';
            form.group = src.group ?? form.group;
            form.name = src.name ?? '';
            form.short_name = src.short_name ?? '';
            form.description = src.descriptions ?? '';
            form.helper = src.helper ?? '';
            form.icon = src.icon ?? '';
            form.color = src.color ?? '';
            form.type = src.type ?? 'int';
            form.status = src.status ?? 'a_valider';
            form.unit = src.unit ?? '';
            form.sort_order = src.sort_order ?? 0;
            form.hide_when_empty = src.hide_when_empty ?? false;
            form.hide_when_false = src.hide_when_false ?? false;
            form.value_overrides = Array.isArray(src.value_overrides) ? src.value_overrides.map((o) => ({ ...o })) : [];
            form.nature = src.nature ?? '';
            form.intrinsic_characteristic_id = src.intrinsic_characteristic_id ?? '';
            form.key = (src.key ?? '').replace(/_creature$|_object$|_spell$|_consumable$|_profile$/, '') || '';
            if (Array.isArray(src.entities) && src.entities.length) {
                form.entities = src.entities.map((e) => ({
                    entity: e.entity ?? '*',
                    db_column: e.db_column ?? '',
                    min: e.min ?? '',
                    max: e.max ?? '',
                    limit_absolute: e.limit_absolute ?? null,
                    formula: e.formula ?? '',
                    formula_display: e.formula_display ?? '',
                    default_value: e.default_value ?? '',
                    forgemagie_max: e.forgemagie_max ?? 0,
                    base_price_per_unit: e.base_price_per_unit ?? '',
                    rune_price_per_unit: e.rune_price_per_unit ?? '',
                    conversion_formula: e.conversion_formula ?? '',
                    conversion_function: e.conversion_function ?? '',
                    conversion_dofus_sample: e.conversion_dofus_sample ?? null,
                    conversion_krosmoz_sample: e.conversion_krosmoz_sample ?? null,
                    conversion_sample_rows: (e.conversion_sample_rows && e.conversion_sample_rows.length)
                        ? e.conversion_sample_rows
                        : getDefaultConversionSampleRows(),
                    norms_grid: e.norms_grid || null,
                    norms_formula: e.norms_formula || null,
                    norms_power_coefficients: e.norms_power_coefficients || null,
                    norms_conditions: e.norms_conditions || [],
                    norms_description: e.norms_description || '',
                    norms_help_section_id: e.norms_help_section_id ?? null,
                    item_type_ids: Array.isArray(e.item_type_ids) ? e.item_type_ids.map((id) => parseInt(String(id), 10)).filter((n) => !Number.isNaN(n)) : [],
                }));
            }
            form.conversion_formulas = defaultConversionFormulasForGroup(props.entitiesByGroup ?? {}, form.group);
            return;
        }
        if (props.createMode && form.group) {
            if (props.entitiesTemplate && !props.copyFromCharacteristic) {
                const template = props.entitiesTemplate[form.group];
                if (Array.isArray(template) && template.length) {
                    const defaultRow = template.find((e) => (e.entity ?? '*') === '*') ?? template[0];
                    form.entities = [
                        {
                            entity: defaultRow.entity ?? '*',
                            db_column: defaultRow.db_column ?? '',
                            min: defaultRow.min ?? '',
                            max: defaultRow.max ?? '',
                            limit_absolute: defaultRow.limit_absolute ?? null,
                            formula: defaultRow.formula ?? '',
                            formula_display: defaultRow.formula_display ?? '',
                            default_value: defaultRow.default_value ?? '',
                            forgemagie_max: defaultRow.forgemagie_max ?? 0,
                            base_price_per_unit: defaultRow.base_price_per_unit ?? '',
                            rune_price_per_unit: defaultRow.rune_price_per_unit ?? '',
                            conversion_formula: defaultRow.conversion_formula ?? '',
                            conversion_function: defaultRow.conversion_function ?? '',
                            conversion_dofus_sample: defaultRow.conversion_dofus_sample ?? null,
                            conversion_krosmoz_sample: defaultRow.conversion_krosmoz_sample ?? null,
                            conversion_sample_rows: (defaultRow.conversion_sample_rows && defaultRow.conversion_sample_rows.length)
                                ? defaultRow.conversion_sample_rows
                                : getDefaultConversionSampleRows(),
                            norms_grid: defaultRow.norms_grid ?? null,
                            norms_formula: defaultRow.norms_formula ?? null,
                            norms_power_coefficients: defaultRow.norms_power_coefficients ?? null,
                            norms_conditions: defaultRow.norms_conditions || [],
                            norms_description: defaultRow.norms_description || '',
                            norms_help_section_id: defaultRow.norms_help_section_id ?? null,
                            item_type_ids: Array.isArray(defaultRow.item_type_ids) ? [...defaultRow.item_type_ids] : [],
                        },
                    ];
                }
            }
            if (!props.copyFromCharacteristic) {
                form.conversion_formulas = defaultConversionFormulasForGroup(props.entitiesByGroup ?? {}, form.group);
            }
        }
    },
    { immediate: true }
);

const conversionHandlers = ref([]);

function loadConversionHandlers() {
    axios
        .get(route('admin.dofus-conversion-formulas.handlers'))
        .then((res) => {
            conversionHandlers.value = res.data.handlers ?? [];
        })
        .catch(() => {
            conversionHandlers.value = [];
        });
}

onMounted(() => {
    loadConversionHandlers();
    // En mode création, pré-sélection du groupe depuis l'URL (?group=creature)
    if (props.createMode) {
        const params = new URLSearchParams(window.location.search);
        const group = params.get('group');
        if (group && props.groups.includes(group)) {
            form.group = group;
        }
    }
});

/** Libellés pour chaque entité (affichage dans les cartes et panneau Mapping). */
const entityLabels = {
    '*': 'Défaut (toutes les entités du groupe)',
    monster: 'Monstre',
    breed: 'Classes',
    class: 'Classe',
    npc: 'PNJ',
    item: 'Équipement',
    consumable: 'Consommable',
    resource: 'Ressource',
    panoply: 'Panoplie',
    spell: 'Sort',
    specialization: 'Spécialisation',
};

/** Groupes avec libellés. */
const groupLabels = {
    creature: 'Créature (monstre, classe, PNJ)',
    object: 'Objet (équipement, panoplie)',
    spell: 'Sort',
    consumable: 'Consommable',
    profile: 'Profil',
};

const natureFilter = ref('');

const natureSelectOptions = computed(() => props.natures ?? props.selected?.natures ?? []);

const natureRequiresIntrinsicLink = computed(() => {
    const n = form.nature ?? props.selected?.nature ?? '';
    return n === 'bonus_malus' || n === 'buff_debuff';
});

const characteristicsByGroupFiltered = computed(() => {
    const raw = props.characteristicsByGroup ?? {};
    const filter = (natureFilter.value ?? '').trim();
    if (!filter) {
        return raw;
    }
    const out = {};
    for (const [group, list] of Object.entries(raw)) {
        out[group] = (list ?? []).filter((c) => (c?.nature ?? '') === filter);
    }
    return out;
});

/** Liste plate de toutes les caractéristiques (pour le select des conditions de normes). */
const allCharacteristicsFlat = computed(() => {
    const groups = props.characteristicsByGroup ?? {};
    return Object.values(groups).flat().map((c) => ({
        id: c.id,
        key: c.id,
        name: c.name ?? c.id,
    }));
});

/** Ligne entity (défaut groupe) pour le panneau Général. */
function generalEntityRow() {
    return (form.entities ?? []).find((e) => e.entity === '*') ?? null;
}

/** Ligne entity pour une entité donnée. */
function entityRow(entityKey) {
    return (form.entities ?? []).find((e) => e.entity === entityKey) ?? null;
}

/** Entités du groupe (hors '*') pour le dropdown "Spécifier pour une entité". */
const entitiesForSpecifyDropdown = computed(() => {
    const group = props.selected?.group;
    if (!group) return [];
    const list = (props.selected?.entitiesByGroup ?? props.entitiesByGroup)?.[group] ?? [];
    return list.filter((e) => e !== '*');
});

/** Caractéristiques du groupe courant (pour dropdown "Dépend de" des formules table). */
const characteristicsForFormulaOptions = computed(() => {
    const group = props.selected?.group;
    if (!group) return [];
    return props.characteristicsByGroup[group] ?? [];
});

/** Caractéristiques pour les tables min/max : en création utilise form.group, en édition selected.group. */
const limitCharacteristicOptions = computed(() => {
    const group = props.selected?.group ?? form.group;
    if (!group) return [];
    return props.characteristicsByGroup?.[group] ?? [];
});

/** Variables réservées + toutes les clés (creature, object, spell) pour l’autocomplétion des formules. */
const reservedFormulaVariableSuggestions = [
    { id: 'd', name: 'Valeur Dofus (conversion)' },
    { id: 'level', name: 'Niveau JDR' },
];

const allCharacteristicKeySuggestions = computed(() => {
    const groups = ['creature', 'object', 'spell', 'consumable', 'profile'];
    const seen = new Set();
    const out = [];
    for (const r of reservedFormulaVariableSuggestions) {
        if (r.id) {
            seen.add(r.id);
            out.push(r);
        }
    }
    for (const g of groups) {
        const list = props.characteristicsByGroup?.[g] ?? [];
        for (const c of list) {
            const id = c.id;
            if (!id || seen.has(id)) continue;
            seen.add(id);
            out.push({
                id,
                name: c.name ?? id,
                short_name: c.short_name ?? null,
            });
        }
    }
    return out;
});

/** Clés de nature buff_debuff (termes éphémères) indexées pour l’aide formule. */
const ephemeralCharacteristicKeySet = computed(() => {
    const set = new Set();
    for (const g of ['creature', 'object', 'spell', 'consumable', 'profile']) {
        for (const c of props.characteristicsByGroup?.[g] ?? []) {
            if ((c?.nature ?? '') === 'buff_debuff' && c.id) {
                set.add(String(c.id));
            }
        }
    }
    return set;
});

/**
 * Termes éphémères (`buff_debuff`) présents dans une formule `[clé]`.
 *
 * @param {unknown} formula
 * @returns {string[]}
 */
function ephemeralTermsInFormula(formula) {
    if (typeof formula !== 'string' || formula.trim() === '') {
        return [];
    }
    const keys = ephemeralCharacteristicKeySet.value;
    const seen = new Set();
    const out = [];
    for (const match of formula.matchAll(/\[([a-zA-Z_][a-zA-Z0-9_]*)\]/g)) {
        const id = match[1];
        if (seen.has(id)) {
            continue;
        }
        if (keys.has(id) || /_(consumable|spell)$/.test(id)) {
            seen.add(id);
            out.push(id);
        }
    }
    return out;
}

/** Clé finale en création : ajoute le suffixe du groupe si absent (ex. life_dice → life_dice_creature). */
const normalizedCreateKey = computed(() => {
    const key = (form.key ?? '').trim();
    const group = form.group ?? 'creature';
    if (!key) return '';
    const suffix = `_${group}`;
    if (key.length >= suffix.length && key.endsWith(suffix)) return key;
    return key + suffix;
});

/** Options pour la table de conversion : d (valeur Dofus), level (niveau JDR). */
const conversionTableCharacteristicOptions = [
    { id: 'd', name: 'Valeur Dofus (d)' },
    { id: 'level', name: 'Niveau JDR (level)' },
];

/** Retourne la valeur CSS pour afficher la couleur (hex, token avec nuance, ou nom de palette Tailwind). */
function displayColor(val) {
    const css = resolveCharacteristicUiColor(val);
    return css || null;
}

/** Style CSS pour le thème de la caractéristique : glass, bordures, graphique, boutons, inputs. */
const characteristicColorStyle = computed(() => {
    const raw = form.color && typeof form.color === 'string' ? form.color.trim() : '';
    if (!raw) return {};
    if (/^#([0-9A-Fa-f]{3}){1,2}$/.test(raw)) {
        return {
            '--color': raw,
            '--bg-color': raw,
            '--chart-fill': `color-mix(in srgb, ${raw} 15%, transparent)`,
            '--color-primary': raw,
            '--color-primary-content': '#fff',
        };
    }
    const token = resolveCharacteristicUiColor(raw);
    if (!token.startsWith('var(')) return {};
    return {
        '--color': token,
        '--bg-color': token,
        '--chart-fill': `color-mix(in srgb, ${token} 15%, transparent)`,
        '--color-primary': token,
        '--color-primary-content': '#fff',
    };
});

/** Pastille d’aperçu pour surcharges (palette Tailwind ou hex legacy). */
function overrideColorSwatchClass(val) {
    if (!val || typeof val !== 'string') return '';
    const t = val.trim();
    if (/^#([0-9A-Fa-f]{3}){1,2}$/.test(t)) return '';
    const low = t.toLowerCase();
    return TAILWIND_CHARACTERISTIC_PALETTES.includes(low) ? `bg-${low}-500` : '';
}

/** Style inline si la valeur est encore en hex. */
function overrideColorSwatchStyle(val) {
    if (!val || typeof val !== 'string') return {};
    const t = val.trim();
    return /^#([0-9A-Fa-f]{3}){1,2}$/.test(t) ? { backgroundColor: t } : {};
}

/** URL des icônes : storage/app/public/images/icons/caracteristics/ (servi via /storage/...) */
const iconBasePath = '/storage/images/icons/caracteristics';
function iconUrl(icon) {
    if (!icon || typeof icon !== 'string') return null;
    if (icon.startsWith('fa-') || icon.startsWith('http')) return null;
    return `${iconBasePath}/${icon.includes('/') ? icon.split('/').pop() : icon}`;
}
function isImageIcon(icon) {
    return icon && typeof icon === 'string' && !icon.startsWith('fa-') && !icon.startsWith('http');
}

const iconUploading = ref(false);
function getXsrfToken() {
    const match = document.cookie.match(/\bXSRF-TOKEN=([^;]*)/);
    return match ? decodeURIComponent(match[1]) : null;
}
async function onIconFileChange(event) {
    const file = event.target.files?.[0];
    if (!file) return;
    const characteristicId = props.selected?.id;
    if (!characteristicId) {
        return; // En édition uniquement : la caractéristique doit exister pour attacher l'icône
    }
    iconUploading.value = true;
    try {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('characteristic_id', String(characteristicId));
        const headers = { Accept: 'application/json' };
        const token = getXsrfToken();
        if (token) headers['X-XSRF-TOKEN'] = token;
        const res = await axios.post(route('admin.characteristics.upload-icon'), formData, { headers });
        if (res.data?.success && res.data?.icon) {
            form.icon = res.data.icon;
        }
    } finally {
        iconUploading.value = false;
        event.target.value = '';
    }
}

/** Classes de fond par zone : thème entités. */
const entityBgClasses = {
    '*': 'bg-base-200',
    monster: 'bg-color-monster-100',
    class: 'bg-color-breed-100',
    breed: 'bg-color-breed-100',
    specialization: 'bg-color-breed-100',
    npc: 'bg-color-npc-100',
    item: 'bg-color-item-100',
    consumable: 'bg-color-item-100',
    resource: 'bg-color-resource-100',
    panoply: 'bg-color-panoply-100',
    spell: 'bg-color-spell-100',
};

function submit() {
    if (props.createMode) {
        if (createModeType.value === 'link') {
            router.post(route('admin.characteristics.store'), {
                create_mode: 'link',
                linked_to_characteristic_id: Number(linkForm.value.linked_to_characteristic_id),
                group: linkForm.value.group,
                key: linkForm.value.key?.trim() || undefined,
            });
            return;
        }
        form.post(route('admin.characteristics.store'));
        return;
    }
    if (!props.selected?.id || !characteristicDirty.value || characteristicSaving.value) return;
    // N'envoyer que '*' et les spécificités choisies pour ne pas créer de lignes en BDD
    const entitiesToSend = (form.entities ?? []).filter(
        (e) => e.entity === '*' || selectedEntityOverrides.value.includes(e.entity)
    );
    router.patch(route('admin.characteristics.update', props.selected.id), {
        name: form.name,
        short_name: form.short_name,
        description: form.description,
        helper: form.helper,
        icon: form.icon,
        color: form.color,
        type: form.type,
        status: form.status,
        unit: form.unit,
        sort_order: form.sort_order,
        nature: form.nature || null,
        intrinsic_characteristic_id: form.intrinsic_characteristic_id ? Number(form.intrinsic_characteristic_id) : null,
        hide_when_empty: form.hide_when_empty,
        hide_when_false: form.hide_when_false,
        value_overrides: (form.value_overrides ?? []).filter((o) => o.value !== '' && o.value !== undefined),
        entities: entitiesToSend.map((e) => ({
            ...e,
            item_type_ids: props.selected?.group === 'object' ? normalizeItemTypeIds(e.item_type_ids) : e.item_type_ids,
        })),
        entity_override_keys: selectedEntityOverrides.value,
    }, {
        preserveScroll: true,
        onStart: () => {
            characteristicSaving.value = true;
        },
        onSuccess: () => {
            notificationStore?.success?.('Caractéristique mise à jour.', { duration: 5000 });
            nextTick(() => captureCharacteristicBaseline());
        },
        onFinish: () => {
            characteristicSaving.value = false;
        },
    });
}

function submitLinkDisabled() {
    return !linkForm.value.linked_to_characteristic_id || !linkForm.value.group;
}

function goCopyFrom(key) {
    if (key) router.visit(route('admin.characteristics.create') + '?copy_from=' + encodeURIComponent(key));
}

function submitConvertToLinked() {
    if (!props.selected?.id || !convertToLinkedMasterId.value) return;
    if (!confirm('Convertir cette caractéristique en liée ? La clé sera conservée mais les paramètres par entité (formules, bornes, conversion) actuels seront supprimés et remplacés par ceux de la maître.')) return;
    router.patch(route('admin.characteristics.update', props.selected.id), {
        convert_to_linked: true,
        linked_to_characteristic_id: Number(convertToLinkedMasterId.value),
    }, {
        onSuccess: () => {
            notificationStore?.success?.('Caractéristique convertie en liée.', { duration: 5000 });
        },
    });
}
</script>

<template>
    <Head title="Caractéristiques" />

    <div
        v-if="!adminUnlocked"
        class="rounded-box border border-warning/40 bg-warning/10 p-8 mx-auto max-w-lg my-8 text-center space-y-4"
    >
        <p class="text-warning-content">
            Cette section permet de modifier les caractéristiques et les données associées. Confirme ton mot de passe pour continuer.
        </p>
        <Btn color="primary" @click="showAdminConfirmModal = true">
            Accéder à l’administration des caractéristiques
        </Btn>
    </div>

    <div v-else class="flex h-full min-h-0 w-full flex-col lg:flex-row">
        <!-- Liste à gauche (vue par caractéristique) -->
        <SidebarNav
            title="Caractéristiques"
            description="Définitions, formules et bornes min/max par type d'entité (monstre, classe, objet). Cliquez pour éditer."
            :items-by-group="characteristicsByGroupFiltered"
            :group-labels="groupLabels"
            groups-mode="collapse"
            searchable
            search-placeholder="Filtrer par nom ou clé…"
            :search-keys="['name', 'id', 'key']"
            :get-item-href="(c) => route('admin.characteristics.show', c.id)"
            :is-item-active="(c) => selected?.id === c.id"
            :get-item-css-classes="(c) => {
                if (!c?.color) return '';
                const col = String(c.color).trim();
                if (col.startsWith('#')) return '';
                if (col.includes('-')) return `color-${col} box-shadow-glass-xs`;
                return `color-${col}-500 box-shadow-glass-xs`;
            }"
            :get-item-color="(c) => displayColor(c.color)"
            :get-item-icon="(c) => c.icon || null"
            :get-item-label="(c) => c.name || c.id"
            :get-item-label-secondary="(c) => (c.id ? `[${c.id}]` : null)"
            icon-base-path="/storage/images/icons/caracteristics"
        >
            <template #nav-before>
                <label class="form-control w-full px-2 pb-2">
                    <span class="label-text text-xs">Filtrer par nature</span>
                    <select v-model="natureFilter" class="select select-bordered select-xs w-full">
                        <option value="">Toutes les natures</option>
                        <option v-for="opt in natureSelectOptions" :key="opt.value" :value="opt.value">
                            {{ opt.label }}
                        </option>
                    </select>
                </label>
            </template>
            <template #item-suffix="{ item }">
                <span v-if="item?.nature_label" class="badge badge-ghost badge-xs mr-1">{{ item.nature_label }}</span>
                <Tooltip
                    :content="characteristicStatusDotMap[item?.status]?.label ?? 'À valider'"
                    placement="left"
                >
                    <span
                        class="inline-block h-2.5 w-2.5 rounded-full ring-1 ring-base-300/80"
                        :class="characteristicStatusDotMap[item?.status]?.class ?? 'bg-slate-500'"
                        aria-hidden="true"
                    />
                </Tooltip>
            </template>
            <template #empty>
                Aucune caractéristique. Ajoutez-en via un groupe ci-dessous, ou peuplez la base via
                <code class="text-xs">project:seed</code>.
            </template>
            <template v-for="group in groups" :key="group" #[`group-${group}`]>
                <Link
                    :href="route('admin.characteristics.create') + '?group=' + group"
                    class="btn btn-ghost btn-sm mt-1 justify-start gap-2 text-primary"
                >
                    <i class="fa fa-plus text-xs" />
                    Ajouter une caractéristique
                </Link>
            </template>
        </SidebarNav>

        <!-- Panneau central -->
        <main class="min-w-0 flex-1 p-4 sm:p-6">
            <!-- Mode création : nouvelle / lier / copier -->
            <template v-if="createMode">
                <h1 class="mb-2 text-2xl font-bold">Nouvelle caractéristique</h1>
                <div class="mb-6 flex flex-wrap gap-2">
                    <div class="join">
                        <button
                            type="button"
                            class="join-item btn btn-sm"
                            :class="createModeType === 'new' ? 'btn-primary' : 'btn-ghost'"
                            @click="createModeType = 'new'"
                        >
                            Nouvelle
                        </button>
                        <button
                            type="button"
                            class="join-item btn btn-sm"
                            :class="createModeType === 'link' ? 'btn-primary' : 'btn-ghost'"
                            @click="createModeType = 'link'"
                        >
                            Lier à une existante
                        </button>
                        <button
                            type="button"
                            class="join-item btn btn-sm"
                            :class="createModeType === 'copy' ? 'btn-primary' : 'btn-ghost'"
                            @click="createModeType = 'copy'; copySelectKey = ''"
                        >
                            Copier depuis une existante
                        </button>
                    </div>
                    <select
                        v-if="createModeType === 'copy'"
                        v-model="copySelectKey"
                        class="select select-bordered select-sm max-w-xs"
                        @change="goCopyFrom(copySelectKey)"
                    >
                        <option value="">Choisir une caractéristique à copier…</option>
                        <option
                            v-for="c in characteristicsForLinkOrCopy"
                            :key="c.id"
                            :value="c.key"
                        >
                            {{ c.name }} [{{ c.key }}] ({{ c.group }})
                        </option>
                    </select>
                </div>
                <p v-if="createModeType === 'new'" class="mb-6 text-sm text-base-content/70">
                    Choisissez le groupe d'entités, la clé et le nom, puis renseignez les paramètres par entité.
                </p>
                <p v-if="createModeType === 'link'" class="mb-6 text-sm text-base-content/70">
                    Créez une caractéristique « liée » qui réutilise la définition d'une caractéristique maître (même nom, formules, conversion). La liée n'a pas de configuration propre.
                </p>
                <p v-if="createModeType === 'copy'" class="mb-6 text-sm text-base-content/70">
                    Préremplir le formulaire à partir d'une caractéristique existante pour créer une nouvelle caractéristique autonome (modifiable).
                </p>

                <!-- Formulaire "Lier à une existante" -->
                <form v-if="createModeType === 'link'" @submit.prevent="submit" class="space-y-6">
                    <div class="card bg-base-100 shadow bg-color-campaign-100">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Lier à une caractéristique maître</h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="label"><span class="label-text">Caractéristique maître</span></label>
                                    <select
                                        v-model="linkForm.linked_to_characteristic_id"
                                        class="select select-bordered w-full"
                                        required
                                    >
                                        <option value="">Choisir…</option>
                                        <option
                                            v-for="c in characteristicsForLinkOrCopy"
                                            :key="c.id"
                                            :value="c.id"
                                        >
                                            {{ c.name }} [{{ c.key }}] ({{ c.group }})
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Groupe de la liée</span></label>
                                    <select
                                        v-model="linkForm.group"
                                        class="select select-bordered w-full"
                                        required
                                    >
                                        <option
                                            v-for="g in groups"
                                            :key="g"
                                            :value="g"
                                        >
                                            {{ groupLabels[g] || g }}
                                        </option>
                                    </select>
                                    <p class="mt-1 text-xs text-base-content/70">La liée sera utilisée pour ce groupe (ex. level_object si la maître est level_creature).</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">Clé personnalisée (optionnel)</span></label>
                                    <input
                                        v-model="linkForm.key"
                                        type="text"
                                        class="input input-bordered w-full max-w-md"
                                        placeholder="ex. level_object"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Si vide, la clé sera dérivée automatiquement (ex. level_object).</p>
                                </div>
                            </div>
                            <div class="card-actions justify-end pt-4">
                                <button type="submit" class="btn btn-primary" :disabled="submitLinkDisabled()">
                                    Créer le lien
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Formulaire principal (Nouvelle ou Copier) -->
                <form v-if="createModeType !== 'link'" @submit.prevent="submit" class="space-y-6">
                    <div v-if="copyFromCharacteristic" class="alert alert-info mb-6">
                        <span>Tu copies depuis <strong>{{ copyFromCharacteristic.name }}</strong> [{{ copyFromCharacteristic.key }}].</span>
                    </div>
                    <div class="card bg-base-100 shadow bg-color-campaign-100">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Groupe et identifiant</h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="label"><span class="label-text">Groupe d'entités</span></label>
                                    <select
                                        v-model="form.group"
                                        class="select select-bordered w-full"
                                        required
                                    >
                                        <option
                                            v-for="g in groups"
                                            :key="g"
                                            :value="g"
                                        >
                                            {{ groupLabels[g] || g }}
                                        </option>
                                    </select>
                                    <p class="mt-1 text-xs text-base-content/70">Détermine quelles entités (monstre, objet, sort…) pourront utiliser cette caractéristique.</p>
                                </div>
                                <InputField
                                    v-model="form.key"
                                    label="Clé (identifiant technique)"
                                    name="key"
                                    helper="Lettres minuscules, chiffres et underscores. Si tu omets le suffixe (_creature, _object, _spell), il sera ajouté automatiquement selon le groupe choisi (ex. life_dice → life_dice_creature)."
                                    required
                                />
                                <p v-if="createMode && form.key && form.group" class="text-xs text-base-content/60 mt-1">
                                    Clé enregistrée : <span class="font-mono">{{ normalizedCreateKey }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-base-100 shadow bg-color-campaign-100">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Définition</h2>
                            <p class="text-sm text-base-content/70">Nom, affichage et type de donnée.</p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="form.name" label="Nom" name="name" required />
                                <InputField v-model="form.short_name" label="Nom abrégé" name="short_name" helper="Listes compactes." />
                                <InputField v-model="form.description" label="Description" name="description" type="textarea" class="sm:col-span-2" />
                                <div>
                                    <label class="label"><span class="label-text">Couleur</span></label>
                                    <ColorCore
                                        v-model="form.color"
                                        limit-to="tailwind"
                                        name="color"
                                        class="input-primary w-full max-w-md"
                                        placeholder="Choisir une palette"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Palette Tailwind (ex. blue, brown). Valeur stockée sans nuance.</p>
                                </div>
                                <div>
                                    <InputField v-model="form.type" label="Type" name="type" :options="['int', 'string', 'array', 'bool']" />
                                </div>
                                <InputField v-model="form.unit" label="Unité" name="unit" />
                                <InputField v-model="form.sort_order" label="Ordre" name="sort_order" type="number" />
                                <div>
                                    <label class="label"><span class="label-text">Nature</span></label>
                                    <select v-model="form.nature" class="select select-bordered w-full">
                                        <option value="">—</option>
                                        <option v-for="opt in natureSelectOptions" :key="opt.value" :value="opt.value">
                                            {{ opt.label }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="natureRequiresIntrinsicLink">
                                    <label class="label"><span class="label-text">Bonus de (caractéristique créature)</span></label>
                                    <select v-model="form.intrinsic_characteristic_id" class="select select-bordered w-full">
                                        <option value="">Choisir…</option>
                                        <option
                                            v-for="opt in intrinsicCharacteristicOptions"
                                            :key="opt.id"
                                            :value="opt.id"
                                        >
                                            {{ opt.name }} [{{ opt.key }}]
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Paramètres par entité du groupe</h2>
                            <p class="text-sm text-base-content/70">
                                Pour chaque entité (ou « Défaut » pour toutes), définissez les bornes min/max et éventuellement une formule. Un graphique s'affiche dès qu'une formule est renseignée.
                            </p>
                            <div
                                v-for="ent in form.entities"
                                :key="ent.entity"
                                class="collapse collapse-arrow border border-base-300 bg-base-200/30 rounded-box mb-3"
                            >
                                <input type="checkbox" />
                                <div class="collapse-title font-medium">
                                    {{ entityLabels[ent.entity] || ent.entity }}
                                </div>
                                <div class="collapse-content">
                                    <div class="grid gap-4 sm:grid-cols-2 pt-2">
                                        <InputField v-model="ent.db_column" label="Colonne BDD" name="db_column" />
                                        <div class="sm:col-span-2">
                                            <FormulaOrTableField
                                                :model-value="ent.min"
                                                @update:model-value="(v) => (ent.min = v)"
                                                :characteristic-options="limitCharacteristicOptions"
                                                :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                                :autocomplete-use-brackets="true"
                                                label="Min"
                                                placeholder="ex: 0, 1 ou [level]*2"
                                            />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <FormulaOrTableField
                                                :model-value="ent.max"
                                                @update:model-value="(v) => (ent.max = v)"
                                                :characteristic-options="limitCharacteristicOptions"
                                                :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                                :autocomplete-use-brackets="true"
                                                label="Max"
                                                placeholder="ex: 100, 200 ou [level]*10"
                                            />
                                            <p v-if="isObjectCharacteristicGroup" class="mt-1 text-xs text-base-content/70">
                                                Groupe objet : max = bonus équipement seul ; total avec forgemagie = max + max forgemagie (champ dédié après enregistrement).
                                            </p>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="label">
                                                <span class="label-text flex items-center gap-1">
                                                    Formule
                                                    <button type="button" class="btn btn-circle btn-ghost btn-xs cursor-pointer" aria-label="Aide formules" @click.stop="openFormulaHelp">?</button>
                                                </span>
                                            </label>
                                            <FormulaOrTableField
                                                :model-value="ent.formula"
                                                @update:model-value="(v) => (ent.formula = v)"
                                                :characteristic-options="limitCharacteristicOptions"
                                                :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                                :autocomplete-use-brackets="true"
                                                placeholder="ex: [level]*2 ou table par niveau"
                                            />
                                            <p v-if="isCreatureCharacteristicGroup" class="mt-1 text-xs text-base-content/70">
                                                Formule complète : intrinsèques + équipement (*_object) + consommable (*_consumable) + sort (*_spell). Consommable et sort sont éphémères (hors limite absolue).
                                            </p>
                                            <p
                                                v-if="isCreatureCharacteristicGroup && ephemeralTermsInFormula(ent.formula).length"
                                                class="mt-1 text-xs text-warning"
                                            >
                                                Termes éphémères détectés :
                                                {{ ephemeralTermsInFormula(ent.formula).join(', ') }}
                                                (0 hors combat, hors limite absolue).
                                            </p>
                                    <label v-if="isCreatureCharacteristicGroup" class="label cursor-pointer justify-start gap-2">
                                        <input
                                            type="checkbox"
                                            class="checkbox checkbox-sm"
                                            :checked="Boolean(ent.limit_absolute)"
                                            @change="(e) => (ent.limit_absolute = e.target.checked)"
                                        />
                                        <span class="label-text">Limite absolue : min/max bornent le total permanent (base + équipement + maîtrise + contexte)</span>
                                    </label>
                                            <p v-if="ent.formula" class="mt-2 text-xs text-base-content/60">Le graphique sera disponible après enregistrement.</p>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="label"><span class="label-text">Formule (affichage)</span></label>
                                            <FormulaExpressionInput
                                                :model-value="ent.formula_display"
                                                :suggestions="allCharacteristicKeySuggestions"
                                                :use-brackets="false"
                                                placeholder="ex: mod. Int + équip."
                                                input-class="input input-bordered w-full font-mono text-sm"
                                                @update:model-value="(v) => (ent.formula_display = v)"
                                            />
                                            <p class="mt-1 text-xs text-base-content/70">Texte joueur abrégé (ex. mod. Int + équip.), sans consommable ni sort ; vide = formule brute humanisée.</p>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="label"><span class="label-text">Valeur par défaut</span></label>
                                            <FormulaExpressionInput
                                                :model-value="ent.default_value"
                                                :suggestions="allCharacteristicKeySuggestions"
                                                :use-brackets="true"
                                                placeholder="ex: 8 ou [level_creature]"
                                                input-class="input input-bordered w-full font-mono text-sm"
                                                @update:model-value="(v) => (ent.default_value = v)"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <div class="sticky bottom-0 z-20 mt-4 flex justify-end gap-2 border-t border-base-300 bg-base-100/95 px-2 py-3 backdrop-blur">
                        <Link :href="route('admin.characteristics.index')" class="btn btn-ghost">Annuler</Link>
                        <Btn type="submit" color="primary" :disabled="form.processing">Créer</Btn>
                    </div>
                        </div>
                    </div>
                </form>
            </template>

            <!-- Mode édition : caractéristique existante -->
            <template v-else-if="selected">
                <div
                    class="sticky top-0 z-30 -mx-4 -mt-4 mb-3 flex flex-wrap items-center gap-x-2 gap-y-1 border-b border-base-300 bg-base-100/95 px-3 py-1.5 shadow-sm backdrop-blur-md sm:-mx-6 sm:-mt-6 sm:px-4"
                    data-cy="characteristic-edit-header"
                >
                    <div class="flex min-w-28 flex-1 items-center gap-1.5 overflow-hidden sm:min-w-40 sm:gap-2">
                        <h1
                            class="min-w-0 truncate text-sm font-semibold leading-tight text-base-content sm:text-base"
                            :style="displayColor(form.color) ? { borderLeftColor: displayColor(form.color) } : {}"
                            :class="displayColor(form.color) ? 'border-l-4 pl-2' : ''"
                            :title="(form.name || selected.name || selected.id) + ' [' + selected.id + ']'"
                        >
                            {{ form.name || selected.name || selected.id }}
                        </h1>
                        <span
                            v-if="selected.group"
                            class="badge badge-xs badge-ghost hidden shrink-0 sm:inline-flex"
                        >
                            {{ groupLabels[selected.group] || selected.group }}
                        </span>
                        <code
                            class="hidden shrink-0 truncate rounded bg-base-200 px-1 font-mono text-[11px] text-base-content/70 lg:inline"
                            :title="'Clé formule : [' + selected.id + ']'"
                        >[{{ selected.id }}]</code>
                    </div>
                    <div class="ml-auto flex shrink-0 flex-wrap items-center justify-end gap-1">
                        <select
                            v-model="form.status"
                            class="select select-bordered select-xs h-7 min-h-7 w-29 max-w-[40vw] sm:select-sm sm:h-8 sm:w-44 sm:max-w-none"
                            :disabled="!!selected.is_linked"
                            :title="selected.is_linked ? 'Cette caractéristique est liée : statut non modifiable ici.' : 'État de validation interne'"
                            aria-label="État de validation"
                        >
                            <option
                                v-for="opt in characteristicStatusOptions"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                {{ opt.label }}
                            </option>
                        </select>
                        <button
                            v-if="!selected.is_linked && characteristicsForConvertToLinked?.length"
                            type="button"
                            class="btn btn-xs btn-ghost btn-outline shrink-0 gap-1 border-warning/50 text-warning"
                            :class="{ 'btn-active': showConvertToLinkedPanel }"
                            :aria-label="showConvertToLinkedPanel ? 'Masquer le panneau Lier' : 'Lier cette caractéristique à une maître'"
                            :title="showConvertToLinkedPanel ? 'Masquer le panneau Lier' : 'Lier cette caractéristique à une maître'"
                            @click="showConvertToLinkedPanel = !showConvertToLinkedPanel"
                        >
                            <i class="fa fa-link text-[10px]" aria-hidden="true" />
                            <span class="hidden sm:inline">Lier</span>
                        </button>
                        <Btn
                            v-if="!selected.is_linked"
                            type="button"
                            color="primary"
                            size="xs"
                            class="shrink-0 gap-1"
                            data-cy="characteristic-save"
                            :disabled="!characteristicDirty || characteristicSaving"
                            :aria-label="characteristicSaving ? 'Enregistrement en cours' : 'Enregistrer les modifications'"
                            :title="characteristicDirty ? 'Enregistrer les modifications' : 'Aucune modification à enregistrer'"
                            @click="submit"
                        >
                            <span v-if="characteristicSaving" class="loading loading-spinner loading-xs" aria-hidden="true" />
                            <i v-else class="fa-solid fa-save" aria-hidden="true" />
                            <span class="max-sm:sr-only">{{ characteristicSaving ? 'Enregistrement…' : 'Enregistrer' }}</span>
                        </Btn>
                        <button
                            v-if="!selected.is_linked"
                            type="button"
                            class="btn btn-ghost btn-xs shrink-0 text-error"
                            aria-label="Supprimer cette caractéristique"
                            title="Supprimer"
                            @click="confirmDelete"
                        >
                            <i class="fa-solid fa-trash" aria-hidden="true" />
                        </button>
                    </div>
                </div>
                <div v-if="selected.is_linked && selected.master_key" class="alert alert-info mb-4 py-2 text-sm">
                    <span>Caractéristique <strong>liée</strong> : les paramètres se modifient sur la maître.</span>
                    <Link :href="route('admin.characteristics.show', selected.master_key)" class="btn btn-xs btn-ghost">
                        Ouvrir la maître
                    </Link>
                </div>

                <div
                    v-show="showConvertToLinkedPanel"
                    v-if="!selected.is_linked && characteristicsForConvertToLinked?.length"
                    class="card bg-base-100 shadow border border-warning/30 mb-6"
                >
                    <div class="card-body">
                        <h2 class="card-title text-lg">Convertir en caractéristique liée</h2>
                        <p class="text-sm text-base-content/70">
                            Liez cette caractéristique à une maître existante : la clé <code class="rounded bg-base-200 px-1 font-mono">[{{ selected.id }}]</code> est conservée, mais les paramètres par entité (formules, bornes, conversion) seront remplacés par ceux de la maître.
                        </p>
                        <div class="flex flex-wrap items-end gap-4 mt-2">
                            <div class="form-control min-w-[280px]">
                                <label class="label"><span class="label-text">Caractéristique maître</span></label>
                                <select
                                    v-model="convertToLinkedMasterId"
                                    class="select select-bordered w-full"
                                >
                                    <option value="">Choisir…</option>
                                    <option
                                        v-for="c in characteristicsForConvertToLinked"
                                        :key="c.id"
                                        :value="c.id"
                                    >
                                        {{ c.name }} [{{ c.key }}] ({{ c.group }})
                                    </option>
                                </select>
                            </div>
                            <button
                                type="button"
                                class="btn btn-warning btn-outline"
                                :disabled="!convertToLinkedMasterId"
                                @click="submitConvertToLinked"
                            >
                                Convertir en liée
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="transition-colors duration-200 characteristic-theme"
                    :class="{ 'has-characteristic-color': !!characteristicColorStyle['--color'] }"
                    :style="characteristicColorStyle"
                >
                <form @submit.prevent="submit" class="space-y-6">
                    <fieldset :disabled="!!selected.is_linked" class="contents">
                    <!-- Panel Général -->
                    <section class="space-y-4">
                        <h2 class="text-xl font-semibold text-base-content">Général</h2>
                    <!-- Définition -->
                    <div class="card shadow border-glass-sm relative overflow-hidden">
                        <div class="card-body bg-base-100 rounded-box">
                            <h3 class="card-title text-lg">Définition</h3>
                            <p class="text-sm text-base-content/70">
                                Nom, affichage (icône, couleur), type de donnée et règles (unité, entités concernées). Forgemagie et prix sont dans la section Équipement.
                            </p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="form.name" label="Nom" name="name" required />
                                <InputField
                                    v-model="form.short_name"
                                    label="Nom abrégé"
                                    name="short_name"
                                    helper="Utilisé dans les listes compactes."
                                />
                                <InputField v-model="form.description" label="Description" name="description" type="textarea" />
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">Icône</span></label>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div v-if="form.icon" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-box bg-base-200">
                                            <i v-if="form.icon.startsWith('fa-')" :class="['fa text-xl', form.icon]" />
                                            <img
                                                v-else-if="isImageIcon(form.icon)"
                                                :src="iconUrl(form.icon)"
                                                :alt="form.name"
                                                class="h-8 w-8 object-contain"
                                                @error="($e) => ($e.target.style.display = 'none')"
                                            />
                                            <span v-else class="text-sm">{{ form.icon }}</span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <input
                                                type="file"
                                                accept="image/*"
                                                class="file-input file-input-bordered file-input-sm w-full max-w-xs"
                                                :disabled="iconUploading"
                                                @change="onIconFileChange"
                                            />
                                            <p class="text-xs text-base-content/70">
                                                Fichier dans <code class="rounded bg-base-300 px-1">storage/app/public/images/icons/caracteristics/</code>. Ou saisir <code class="rounded bg-base-300 px-1">fa-heart</code> pour Font Awesome.
                                            </p>
                                        </div>
                                        <input
                                            v-model="form.icon"
                                            type="text"
                                            class="input input-bordered input-sm w-48 font-mono"
                                            placeholder="fa-heart ou nom fichier"
                                        />
                                        <span v-if="iconUploading" class="text-sm text-base-content/60">Envoi…</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Couleur</span></label>
                                    <ColorCore
                                        v-model="form.color"
                                        limit-to="tailwind"
                                        name="color"
                                        class="input-primary w-full max-w-md"
                                        placeholder="Choisir une palette"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Palette Tailwind. Utilisée pour les badges, graphiques et indicateurs.</p>
                                </div>
                                <div>
                                    <InputField v-model="form.type" label="Type" name="type" :options="['int', 'string', 'array', 'bool']" />
                                    <p class="mt-1 text-xs text-base-content/70">int = nombre, string = texte, array = liste de valeurs, bool = vrai/faux.</p>
                                </div>
                                <InputField
                                    v-model="form.unit"
                                    label="Unité"
                                    name="unit"
                                    helper="ex. %, points, dégâts (affichage uniquement)."
                                />
                                <InputField
                                    v-model="form.sort_order"
                                    label="Ordre"
                                    name="sort_order"
                                    type="number"
                                    helper="Ordre d’affichage dans les listes (plus petit = plus haut)."
                                />
                                <div>
                                    <label class="label"><span class="label-text">Nature</span></label>
                                    <select v-model="form.nature" class="select select-bordered w-full">
                                        <option value="">—</option>
                                        <option v-for="opt in natureSelectOptions" :key="opt.value" :value="opt.value">
                                            {{ opt.label }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="natureRequiresIntrinsicLink">
                                    <label class="label"><span class="label-text">Bonus de (caractéristique créature)</span></label>
                                    <select v-model="form.intrinsic_characteristic_id" class="select select-bordered w-full">
                                        <option value="">Choisir…</option>
                                        <option
                                            v-for="opt in intrinsicCharacteristicOptions"
                                            :key="opt.id"
                                            :value="opt.id"
                                        >
                                            {{ opt.name }} [{{ opt.key }}]
                                        </option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2 flex flex-wrap items-center gap-3 rounded-box border border-base-300 bg-base-200/40 px-4 py-3">
                                    <input
                                        id="hide_when_empty"
                                        v-model="form.hide_when_empty"
                                        type="checkbox"
                                        class="checkbox checkbox-sm"
                                    />
                                    <label for="hide_when_empty" class="cursor-pointer text-sm">
                                        Masquer la ligne en jeu lorsque la valeur est vide
                                        <span class="block text-xs text-base-content/70">
                                            Selon le type : chaîne vide, tableau vide, entier à 0 ; les booléens « Non » restent affichés.
                                        </span>
                                    </label>
                                </div>
                                <div class="sm:col-span-2 flex flex-wrap items-center gap-3 rounded-box border border-base-300 bg-base-200/40 px-4 py-3">
                                    <input
                                        id="hide_when_false"
                                        v-model="form.hide_when_false"
                                        type="checkbox"
                                        class="checkbox checkbox-sm"
                                    />
                                    <label for="hide_when_false" class="cursor-pointer text-sm">
                                        Masquer la ligne lorsque le booléen est faux
                                        <span class="block text-xs text-base-content/70">
                                            Utile pour un indicateur « passif » affiché uniquement si la capacité est passive.
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panneau — Surcharges visuelles par valeur -->
                    <section class="space-y-4">
                        <h2 class="text-xl font-semibold text-base-content border-b border-base-300 pb-2">Surcharges visuelles par valeur</h2>
                        <div class="space-y-4 pt-2">
                            <div
                                v-for="(ov, idx) in form.value_overrides"
                                :key="idx"
                                class="card shadow border border-base-200 border-glass-sm relative overflow-hidden"
                            >
                                <div class="card-body bg-base-200 rounded-box">
                                    <div class="flex items-center justify-between">
                                        <h3 class="card-title text-base">
                                            Surcharge #{{ idx + 1 }}
                                            <span v-if="ov.value !== '' && ov.value !== undefined" class="badge badge-sm badge-outline font-mono">{{ String(ov.value) }}</span>
                                        </h3>
                                        <Tooltip content="Supprimer cette surcharge" placement="left">
                                            <Btn size="xs" variant="ghost" class="text-error" @click="form.value_overrides.splice(idx, 1)">
                                                <i class="fa-solid fa-trash-can" />
                                            </Btn>
                                        </Tooltip>
                                    </div>
                                    <p class="text-sm text-base-content/70">Quand la caractéristique vaut cette valeur, l'icône, la couleur et le sous-texte ci-dessous remplacent les valeurs par défaut.</p>

                                    <div class="grid gap-4 sm:grid-cols-2 pt-2">
                                        <!-- Valeur déclencheuse -->
                                        <div>
                                            <InputField
                                                :model-value="ov.value === true ? 'true' : ov.value === false ? 'false' : String(ov.value ?? '')"
                                                @update:model-value="(v) => {
                                                    if (v === 'true') ov.value = true;
                                                    else if (v === 'false') ov.value = false;
                                                    else if (/^-?\d+$/.test(String(v))) ov.value = Number(v);
                                                    else ov.value = v;
                                                }"
                                                label="Valeur déclencheuse"
                                                placeholder="true / false / 0 / 1 / fire…"
                                                helper="La valeur qui active cette surcharge."
                                            />
                                        </div>
                                        <!-- Sous-texte -->
                                        <div>
                                            <InputField
                                                v-model="ov.subtitle"
                                                label="Sous-texte"
                                                placeholder="Texte contextuel pour cette valeur…"
                                                helper="Affiché en italique dans les tooltips à la place de la description."
                                            />
                                        </div>
                                        <!-- Icône -->
                                        <div>
                                            <label class="label"><span class="label-text">Icône</span></label>
                                            <div class="flex items-center gap-2">
                                                <div
                                                    v-if="ov.icon"
                                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded border border-base-300 bg-base-100"
                                                >
                                                    <img
                                                        :src="`/storage/images/${ov.icon.includes('/') ? ov.icon : 'icons/caracteristics/' + ov.icon}`"
                                                        :alt="ov.icon"
                                                        class="h-6 w-6 object-contain"
                                                        @error="(e) => e.target.style.display = 'none'"
                                                    />
                                                </div>
                                                <input
                                                    v-model="ov.icon"
                                                    type="text"
                                                    class="input input-bordered input-sm flex-1 font-mono"
                                                    placeholder="cac.webp ou fa-icon"
                                                />
                                            </div>
                                            <input
                                                type="file"
                                                accept="image/*"
                                                class="file-input file-input-bordered file-input-xs w-full max-w-xs mt-1.5"
                                                @change="(e) => {
                                                    const file = e.target.files?.[0];
                                                    if (file) ov.icon = file.name;
                                                    e.target.value = '';
                                                }"
                                            />
                                            <p class="mt-1 text-xs text-base-content/60">Nom du fichier dans <code class="rounded bg-base-300 px-1">caracteristics/</code> ou préfixe <code class="rounded bg-base-300 px-1">fa-</code> pour Font Awesome.</p>
                                        </div>
                                        <!-- Couleur -->
                                        <div>
                                            <label class="label"><span class="label-text">Couleur</span></label>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <ColorCore
                                                    v-model="ov.color"
                                                    limit-to="tailwind"
                                                    class="max-w-xs flex-1"
                                                    placeholder="Défaut"
                                                />
                                                <div
                                                    v-if="ov.color"
                                                    class="h-6 w-6 shrink-0 rounded-full border border-base-300"
                                                    :class="overrideColorSwatchClass(ov.color)"
                                                    :style="overrideColorSwatchStyle(ov.color)"
                                                />
                                            </div>
                                            <p class="mt-1 text-xs text-base-content/60">Palette Tailwind. Laissez vide pour garder la couleur par défaut.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- État vide -->
                            <div v-if="!form.value_overrides?.length" class="rounded-box border border-dashed border-base-300 bg-base-200/30 px-6 py-8 text-center">
                                <i class="fa-solid fa-layer-group mb-2 text-2xl text-base-content/30" />
                                <p class="text-sm text-base-content/50">Aucune surcharge définie. Les valeurs par défaut (icône, couleur) seront utilisées pour toutes les valeurs.</p>
                            </div>
                        </div>
                        <Btn size="sm" variant="outline" @click="form.value_overrides.push({ value: '', icon: '', color: '', subtitle: '' })">
                            <i class="fa-solid fa-plus mr-1" /> Ajouter une surcharge
                        </Btn>
                    </section>

                    <!-- Panneau 1 — Limite et valeur (défaut, min, max : expressions dynamiques) -->
                    <section class="space-y-4" v-if="generalEntityRow()">
                        <h2 class="text-xl font-semibold text-base-content border-b border-base-300 pb-2">Panneau 1 — Limite et valeur</h2>
                        <div class="space-y-4 pt-2">
                    <div v-for="ent in (generalEntityRow() ? [generalEntityRow()] : [])" :key="ent.entity" class="card shadow border border-base-200 border-glass-sm relative overflow-hidden">
                        <div class="card-body bg-base-200 rounded-box">
                            <h3 class="card-title text-lg">Limite et défaut</h3>
                            <p class="text-sm text-base-content/70">
                                Bornes min/max et formule de calcul pour ce type d’entité. Le graphique montre l’évolution en fonction du niveau.
                            </p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <FormulaOrTableField
                                        :model-value="ent.min"
                                        @update:model-value="(v) => (ent.min = v)"
                                        :characteristic-options="limitCharacteristicOptions"
                                        :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                        :autocomplete-use-brackets="true"
                                        label="Min (valeur fixe, formule ou table)"
                                        placeholder="ex: 0, 1 ou [level]*2"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <FormulaOrTableField
                                        :model-value="ent.max"
                                        @update:model-value="(v) => (ent.max = v)"
                                        :characteristic-options="limitCharacteristicOptions"
                                        :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                        :autocomplete-use-brackets="true"
                                        label="Max (valeur fixe, formule ou table)"
                                        placeholder="ex: 100 ou [level]*10"
                                    />
                                    <p v-if="isObjectCharacteristicGroup" class="mt-1 text-xs text-base-content/70">
                                        Groupe objet : ce max est le plafond bonus équipement seul (hors forgemagie). Total avec forgemagie = ce max + « Max forgemagie » ci‑dessous.
                                    </p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label">
                                        <span class="label-text flex items-center gap-1">
                                            Formule ou table par caractéristique
                                            <button type="button" class="btn btn-circle btn-ghost btn-xs cursor-pointer" aria-label="Aide formules" @click.stop="openFormulaHelp">?</button>
                                        </span>
                                    </label>
                                    <FormulaOrTableFieldWithChart
                                        :model-value="ent.formula"
                                        @update:model-value="(v) => (ent.formula = v)"
                                        :characteristic-options="characteristicsForFormulaOptions"
                                        :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                        :autocomplete-use-brackets="true"
                                        placeholder="ex: [vitality]*10+[level]*2"
                                        :preview="{ characteristicKey: selected.id, entity: ent.entity, variable: 'level', mode: 'formula' }"
                                        chart-y-label="Résultat"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Syntaxe : [id], floor(), ceil(), round(), sqrt(), pow(), min(), max(), cos/sin/tan, etc. + - * /</p>
                                    <p v-if="isCreatureCharacteristicGroup" class="mt-1 text-xs text-base-content/70">
                                        Formule complète : intrinsèques + équipement (*_object) + consommable (*_consumable) + sort (*_spell). Consommable et sort sont éphémères (hors limite absolue).
                                    </p>
                                    <p
                                        v-if="isCreatureCharacteristicGroup && ephemeralTermsInFormula(ent.formula).length"
                                        class="mt-1 text-xs text-warning"
                                    >
                                        Termes éphémères détectés :
                                        {{ ephemeralTermsInFormula(ent.formula).join(', ') }}
                                        (0 hors combat, hors limite absolue).
                                    </p>
                                    <label v-if="isCreatureCharacteristicGroup" class="label cursor-pointer justify-start gap-2">
                                        <input
                                            type="checkbox"
                                            class="checkbox checkbox-sm"
                                            :checked="Boolean(ent.limit_absolute)"
                                            @change="(e) => (ent.limit_absolute = e.target.checked)"
                                        />
                                        <span class="label-text">Limite absolue : min/max bornent le total permanent (base + équipement + maîtrise + contexte)</span>
                                    </label>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">Formule (affichage)</span></label>
                                    <FormulaExpressionInput
                                        :model-value="ent.formula_display"
                                        :suggestions="allCharacteristicKeySuggestions"
                                        :use-brackets="false"
                                        placeholder="ex: mod. Int + équip."
                                        input-class="input input-bordered w-full font-mono text-sm"
                                        @update:model-value="(v) => (ent.formula_display = v)"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Texte joueur abrégé (ex. mod. Int + équip.), sans consommable ni sort ; vide = formule brute humanisée.</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">Valeur par défaut</span></label>
                                    <FormulaExpressionInput
                                        :model-value="ent.default_value"
                                        :suggestions="allCharacteristicKeySuggestions"
                                        :use-brackets="true"
                                        placeholder="ex: 8 ou [level_creature]"
                                        input-class="input input-bordered w-full font-mono text-sm"
                                        @update:model-value="(v) => (ent.default_value = v)"
                                    />
                                </div>
                                <!-- Forgemagie et prix : uniquement pour le groupe object -->
                                <template v-if="isObjectCharacteristicGroup">
                                    <div class="sm:col-span-2 border-t border-base-300 pt-4 mt-2">
                                        <h3 class="mb-3 text-sm font-semibold text-base-content/80">Prix / unité et forgemagie (groupe objet)</h3>
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div class="flex flex-col gap-1">
                                                <InputField v-model="ent.forgemagie_max" label="Max forgemagie" type="number" />
                                                <p class="text-xs text-base-content/70">
                                                    Valeur maximale ajoutable par forgemagie. Mettre 0 si la forgemagie ne s’applique pas à cette caractéristique.
                                                </p>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <InputField
                                                    v-model="ent.base_price_per_unit"
                                                    label="Prix par unité (kamas)"
                                                    type="number"
                                                    step="0.01"
                                                    helper="Prix de base par point de bonus (création équipement)."
                                                />
                                                <InputField
                                                    v-model="ent.rune_price_per_unit"
                                                    label="Prix rune par unité (kamas)"
                                                    helper="Prix de la rune de forgemagie par unité."
                                                    type="number"
                                                    step="0.01"
                                                />
                                                <p class="text-xs text-base-content/70">Prix base et rune pour création équipement et forgemagie (optionnel).</p>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <ItemTypesMultiField
                                                v-model="ent.item_type_ids"
                                                :options="itemTypes"
                                            />
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                                </div>
                    </section>

                    <!-- Panneau 2 — Conversion (formule DofusDB → Krosmoz, graphe, échantillonnage) -->
                    <section v-if="generalEntityRow()" class="collapse collapse-arrow rounded-box border border-base-300 bg-base-100">
                        <input type="checkbox" class="peer" />
                        <h2 class="collapse-title text-xl font-semibold text-base-content">Panneau 2 — Conversion</h2>
                        <div class="collapse-content space-y-4 pb-4">
                    <div class="card shadow border-glass-sm relative overflow-hidden">
                        <div class="card-body bg-base-100 rounded-box bg-color-neutral-100">
                            <h3 class="card-title text-lg">Formule de conversion</h3>
                            <p class="text-sm text-base-content/70">
                                Formules utilisées lors du scrapping pour convertir les valeurs DofusDB en valeurs JDR. Variable <code class="rounded bg-base-300 px-1">[d]</code> = valeur Dofus, <code class="rounded bg-base-300 px-1">[level]</code> = niveau JDR (pour la vie). Une formule différente par type d’entité (monstre, classe, équipement).
                            </p>
                            <div v-if="generalEntityRow()" class="space-y-4 rounded-box border border-base-300 bg-base-200/30 p-4">
                                <!-- Formule affichée (lecture seule, au-dessus du champ) -->
                                <div>
                                    <span class="label-text text-base-content/70">Formule affichée</span>
                                    <p class="mt-0.5 font-mono text-sm">
                                        {{ generalEntityRow().conversion_formula?.trim() ? generalEntityRow().conversion_formula : '[vide]' }}
                                    </p>
                                </div>
                                <!-- Champ formule (sans graphique intégré) -->
                                <div>
                                    <label class="label">
                                        <span class="label-text flex items-center gap-1">
                                            Formule ou table par valeur (tout le groupe)
                                            <button type="button" class="btn btn-circle btn-ghost btn-xs cursor-pointer" aria-label="Aide formules" @click.stop="openFormulaHelp">?</button>
                                        </span>
                                    </label>
                                    <FormulaOrTableField
                                        :model-value="generalEntityRow().conversion_formula"
                                        @update:model-value="(v) => (generalEntityRow().conversion_formula = v)"
                                        :characteristic-options="conversionTableCharacteristicOptions"
                                        :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                        :autocomplete-use-brackets="true"
                                        placeholder="ex: [d]/10 ou floor([d]/200)+[level]*5"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Syntaxe : [d], [level], floor(), ceil(), round(), sqrt(), pow(), min(), max(), etc. + - * /</p>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Fonction de conversion</span></label>
                                    <select
                                        v-model="generalEntityRow().conversion_function"
                                        class="select select-bordered w-full"
                                        aria-label="Choisir une fonction de conversion"
                                    >
                                        <option value="">Aucune</option>
                                        <option
                                            v-for="opt in conversionFunctionSelectOptions"
                                            :key="opt.value"
                                            :value="opt.value"
                                        >
                                            {{ opt.label }}
                                        </option>
                                    </select>
                                    <p class="mt-1 text-xs text-base-content/70">
                                        Optionnelle. Appliquée après la formule (ou seule si pas de formule). Accès aux données converties et brutes.
                                    </p>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Formule (affichage)</span></label>
                                    <FormulaExpressionInput
                                        :model-value="generalEntityRow().formula_display"
                                        :suggestions="allCharacteristicKeySuggestions"
                                        :use-brackets="false"
                                        placeholder="ex: mod. Int + équip."
                                        input-class="input input-bordered w-full font-mono text-sm"
                                        @update:model-value="(v) => (generalEntityRow().formula_display = v)"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Texte joueur abrégé (ex. mod. Int + équip.), sans consommable ni sort ; vide = formule brute humanisée.</p>
                                </div>

                                <!-- Graphique en grand sous la formule -->
                                <ConversionChartBlock
                                    :formula="generalEntityRow().conversion_formula"
                                    :conversion-bounds="conversionBoundsFromRow(generalEntityRow())"
                                    :characteristic-key="selected.id"
                                    entity-key="*"
                                    chart-x-label="d (Dofus)"
                                    chart-y-label="k (JDR)"
                                    :chart-height="280"
                                />

                                <!-- Fonctions disponibles (masqué par défaut) -->
                                <div class="collapse collapse-arrow rounded-box border border-base-300 bg-base-200/50">
                                    <input type="checkbox" />
                                    <div class="collapse-title min-h-0 py-2 text-sm font-semibold text-base-content/80">
                                        Fonctions disponibles (Table, Linéaire, Carré, etc.)
                                    </div>
                                    <div class="collapse-content space-y-4">
                                        <span class="label-text block mb-2">Proposer une formule (à valider ensuite)</span>
                                        <div class="flex flex-wrap gap-2">
                                            <Tooltip :content="conversionSuggestionTooltips.table" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('table', '*')">Table</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.linear" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('linear', '*')">Linéaire</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.power" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('power', '*')">Carré</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.shifted_power" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('shifted_power', '*')">Carré décalé</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.exponential" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('exponential', '*')">Exponentielle</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.log" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('log', '*')">Logarithmique</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.polynomial2" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('polynomial2', '*')">Polynôme 2</button>
                                            </Tooltip>
                                        </div>
                                        <p class="mt-1 text-xs text-base-content/60">Les formules générées sont enveloppées dans floor() pour le graphique et des entiers.</p>
                                        <p v-if="conversionSuggestionLoading && conversionSuggestionForEntity === '*'" class="mt-2 text-sm text-info">Calcul en cours…</p>
                                        <p v-else-if="conversionSuggestionError && conversionSuggestionForEntity === '*'" class="mt-2 text-sm text-error">{{ conversionSuggestionError }}</p>
                                        <div v-else-if="conversionSuggestionFormula && conversionSuggestionForEntity === '*'" class="mt-3 p-3 rounded-box bg-base-300 border border-base-content/10">
                                            <p class="text-sm font-medium text-base-content/80 mb-1">Proposition :</p>
                                            <code class="block text-xs break-all mb-2">{{ conversionSuggestionFormula }}</code>
                                            <p v-if="conversionSuggestionR2 != null" class="text-xs text-base-content/70 mb-2">R² = {{ conversionSuggestionR2 }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" class="btn btn-sm btn-primary" @click="applyConversionSuggestion">Valider (remplacer le champ)</button>
                                                <button type="button" class="btn btn-sm btn-ghost" @click="clearConversionSuggestion">Annuler</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Échantillonnage (masqué par défaut) -->
                                <div class="collapse collapse-arrow rounded-box border border-base-300 bg-base-200/50">
                                    <input type="checkbox" />
                                    <div class="collapse-title min-h-0 py-2 text-sm font-semibold text-base-content/80">
                                        Échantillons (automatisation des formules)
                                    </div>
                                    <div class="collapse-content space-y-4 pt-2">
                                    <p class="text-xs text-base-content/70">Tableau : 2 colonnes Dofus (niveau, valeur) et 2 colonnes Krosmoz (niveau, valeur). Au moins 2 lignes renseignées pour proposer une formule. 6 points donnent en général un bon ajustement.</p>
                                    <div class="overflow-x-auto">
                                        <table class="table table-sm table-zebra">
                                            <thead>
                                                <tr>
                                                    <th class="bg-base-300">Dofus (niv.)</th>
                                                    <th class="bg-base-300">Dofus (valeur)</th>
                                                    <th class="bg-base-300">Krosmoz (niv.)</th>
                                                    <th class="bg-base-300">Krosmoz (valeur)</th>
                                                    <th class="bg-base-300 w-10"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(sampleRow, idx) in (generalEntityRow()?.conversion_sample_rows || getDefaultConversionSampleRows())" :key="idx">
                                                    <td>
                                                        <input
                                                            v-model.number="sampleRow.dofus_level"
                                                            type="number"
                                                            min="1"
                                                            class="input input-bordered input-sm w-20"
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            v-model.number="sampleRow.dofus_value"
                                                            type="number"
                                                            step="any"
                                                            class="input input-bordered input-sm w-24"
                                                            placeholder="—"
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            v-model.number="sampleRow.krosmoz_level"
                                                            type="number"
                                                            min="1"
                                                            class="input input-bordered input-sm w-20"
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            v-model.number="sampleRow.krosmoz_value"
                                                            type="number"
                                                            step="any"
                                                            class="input input-bordered input-sm w-24"
                                                            placeholder="—"
                                                        />
                                                    </td>
                                                    <td>
                                                        <button
                                                            type="button"
                                                            class="btn btn-ghost btn-xs btn-error"
                                                            :disabled="(generalEntityRow()?.conversion_sample_rows?.length ?? 0) <= 1"
                                                            :aria-label="'Supprimer la ligne ' + (idx + 1)"
                                                            @click="removeConversionSampleRow('*', idx)"
                                                        >
                                                            −
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-sm btn-ghost mt-2" @click="addConversionSampleRow">+ Ajouter une ligne</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </section>

                    <!-- Panneau Normes (Général / *) -->
                    <section v-if="generalEntityRow()" class="card shadow border border-base-200 mt-4 border-glass-sm">
                        <div class="card-body bg-base-100 rounded-box">
                            <div class="collapse collapse-arrow bg-base-200/30">
                                <input type="checkbox" checked />
                                <div class="collapse-title text-sm font-semibold">Normes (Charte) — Général</div>
                                <div class="collapse-content">
                                    <NormsPanel
                                        :model-value="generalEntityRow()"
                                        :characteristics-list="allCharacteristicsFlat"
                                        :characteristic-key="form.key || selected?.key || ''"
                                        @update:model-value="Object.assign(generalEntityRow(), $event)"
                                    />
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Panneau 3 — Mapping (lien DofusDB ↔ Krosmoz, unique par entité) -->
                    <section class="collapse collapse-arrow rounded-box border border-base-300 bg-base-100 mt-4">
                        <input type="checkbox" class="peer" />
                        <h2 class="collapse-title text-xl font-semibold text-base-content">Panneau 3 — Mapping</h2>
                        <div class="collapse-content pb-4">
                            <MappingPanel
                                :scrapping-mappings-using-this="scrappingMappingsUsingThis ?? []"
                                :characteristic-key="selected?.id ?? ''"
                                :mapping-entities="selected?.scrappingMappingEntities ?? []"
                                :entity-labels="entityLabels"
                            />
                        </div>
                    </section>

                    <!-- Panneaux entité spécifique (sous Conversion) -->
                    <div v-for="entityKey in selectedEntityOverrides" :key="entityKey" class="card shadow border border-base-200 mt-4 border-glass-sm relative overflow-hidden" :class="entityBgClasses[entityKey]">
                        <div class="card-body bg-base-100 rounded-box" v-if="entityRow(entityKey)">
                            <div class="flex justify-between items-center flex-wrap gap-2">
                                <h2 class="card-title text-lg">{{ entityLabels[entityKey] || entityKey }}</h2>
                                <button type="button" class="btn btn-ghost btn-sm btn-error" @click="removeEntityOverride(entityKey)">
                                    Supprimer la spécificité pour {{ entityLabels[entityKey] || entityKey }}
                                </button>
                            </div>
                            <p class="text-sm text-base-content/70 mb-4">Surcharge des paramètres et de la conversion pour cette entité uniquement.</p>

                            <!-- Limite et défaut (même interface que Général) -->
                            <div class="space-y-4 mb-6">
                                <h3 class="text-sm font-semibold text-base-content/80">Limite et défaut</h3>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <FormulaOrTableField
                                            :model-value="entityRow(entityKey).min"
                                            @update:model-value="(v) => (entityRow(entityKey).min = v)"
                                            :characteristic-options="limitCharacteristicOptions"
                                            :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                            :autocomplete-use-brackets="true"
                                            label="Min"
                                            placeholder="ex: 0 ou [level]*2"
                                        />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <FormulaOrTableField
                                            :model-value="entityRow(entityKey).max"
                                            @update:model-value="(v) => (entityRow(entityKey).max = v)"
                                            :characteristic-options="limitCharacteristicOptions"
                                            :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                            :autocomplete-use-brackets="true"
                                            label="Max"
                                            placeholder="ex: 100 ou [level]*10"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label class="label">
                                        <span class="label-text flex items-center gap-1">
                                            Formule ou table par caractéristique
                                            <button type="button" class="btn btn-circle btn-ghost btn-xs cursor-pointer" aria-label="Aide formules" @click.stop="openFormulaHelp">?</button>
                                        </span>
                                    </label>
                                    <FormulaOrTableFieldWithChart
                                        :model-value="entityRow(entityKey).formula"
                                        @update:model-value="(v) => (entityRow(entityKey).formula = v)"
                                        :characteristic-options="characteristicsForFormulaOptions"
                                        :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                        :autocomplete-use-brackets="true"
                                        placeholder="ex: [level]*2"
                                        :preview="{ characteristicKey: selected.id, entity: entityKey, variable: 'level', mode: 'formula' }"
                                        chart-y-label="Résultat"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Syntaxe : [id], floor(), ceil(), round(), sqrt(), pow(), min(), max(), etc. + - * /</p>
                                    <p v-if="isCreatureCharacteristicGroup" class="mt-1 text-xs text-base-content/70">
                                        Formule complète : intrinsèques + équipement (*_object) + consommable (*_consumable) + sort (*_spell). Consommable et sort sont éphémères (hors limite absolue).
                                    </p>
                                    <p
                                        v-if="isCreatureCharacteristicGroup && ephemeralTermsInFormula(entityRow(entityKey).formula).length"
                                        class="mt-1 text-xs text-warning"
                                    >
                                        Termes éphémères détectés :
                                        {{ ephemeralTermsInFormula(entityRow(entityKey).formula).join(', ') }}
                                        (0 hors combat, hors limite absolue).
                                    </p>
                                    <label v-if="isCreatureCharacteristicGroup" class="label cursor-pointer justify-start gap-2">
                                        <input
                                            type="checkbox"
                                            class="checkbox checkbox-sm"
                                            :checked="Boolean(entityRow(entityKey).limit_absolute)"
                                            @change="(e) => (entityRow(entityKey).limit_absolute = e.target.checked)"
                                        />
                                        <span class="label-text">Limite absolue : min/max bornent le total permanent (base + équipement + maîtrise + contexte)</span>
                                    </label>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">Formule (affichage)</span></label>
                                    <FormulaExpressionInput
                                        :model-value="entityRow(entityKey).formula_display"
                                        :suggestions="allCharacteristicKeySuggestions"
                                        :use-brackets="false"
                                        placeholder="ex: mod. Int + équip."
                                        input-class="input input-bordered w-full font-mono text-sm"
                                        @update:model-value="(v) => (entityRow(entityKey).formula_display = v)"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">Valeur par défaut</span></label>
                                    <FormulaExpressionInput
                                        :model-value="entityRow(entityKey).default_value"
                                        :suggestions="allCharacteristicKeySuggestions"
                                        :use-brackets="true"
                                        placeholder="ex: 8 ou [level_creature]"
                                        input-class="input input-bordered w-full font-mono text-sm"
                                        @update:model-value="(v) => (entityRow(entityKey).default_value = v)"
                                    />
                                </div>
                            </div>

                            <!-- Conversion (même interface que Général) -->
                            <div class="pt-4 border-t border-base-300 space-y-4">
                                <h3 class="text-sm font-semibold text-base-content/80 mb-2">Conversion (surcharge pour cette entité)</h3>
                                <p class="text-xs text-base-content/70 mb-2">Si renseigné, remplace la conversion du groupe.</p>

                                <!-- Formule affichée (lecture seule) -->
                                <div>
                                    <span class="label-text text-base-content/70">Formule affichée</span>
                                    <p class="mt-0.5 font-mono text-sm">
                                        {{ entityRow(entityKey).conversion_formula?.trim() ? entityRow(entityKey).conversion_formula : '[vide]' }}
                                    </p>
                                </div>
                                <!-- Champ formule (sans graphique intégré) -->
                                <div>
                                    <label class="label">
                                        <span class="label-text flex items-center gap-1">
                                            Formule ou table par valeur (surcharge)
                                            <button type="button" class="btn btn-circle btn-ghost btn-xs cursor-pointer" aria-label="Aide formules" @click.stop="openFormulaHelp">?</button>
                                        </span>
                                    </label>
                                    <FormulaOrTableField
                                        :model-value="entityRow(entityKey).conversion_formula"
                                        @update:model-value="(v) => (entityRow(entityKey).conversion_formula = v)"
                                        :characteristic-options="conversionTableCharacteristicOptions"
                                        :characteristic-key-suggestions="allCharacteristicKeySuggestions"
                                        :autocomplete-use-brackets="true"
                                        placeholder="ex: [d]/10 (vide = même que le groupe)"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Syntaxe : [d], [level], floor(), ceil(), round(), sqrt(), pow(), min(), max(), etc. + - * /</p>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Fonction de conversion</span></label>
                                    <select
                                        v-model="entityRow(entityKey).conversion_function"
                                        class="select select-bordered w-full"
                                        aria-label="Choisir une fonction de conversion pour cette entité"
                                    >
                                        <option value="">Aucune</option>
                                        <option
                                            v-for="opt in conversionFunctionSelectOptions"
                                            :key="opt.value"
                                            :value="opt.value"
                                        >
                                            {{ opt.label }}
                                        </option>
                                    </select>
                                    <p class="mt-1 text-xs text-base-content/70">Surcharge pour cette entité. Appliquée après la formule.</p>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Formule (affichage)</span></label>
                                    <FormulaExpressionInput
                                        :model-value="entityRow(entityKey).formula_display"
                                        :suggestions="allCharacteristicKeySuggestions"
                                        :use-brackets="false"
                                        placeholder="ex: mod. Int + équip."
                                        input-class="input input-bordered w-full font-mono text-sm"
                                        @update:model-value="(v) => (entityRow(entityKey).formula_display = v)"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Texte joueur abrégé (ex. mod. Int + équip.), sans consommable ni sort ; vide = formule brute humanisée.</p>
                                </div>

                                <!-- Graphique en grand sous la formule -->
                                <ConversionChartBlock
                                    :formula="entityRow(entityKey).conversion_formula"
                                    :conversion-bounds="conversionBoundsFromRow(entityRow(entityKey))"
                                    :characteristic-key="selected.id"
                                    :entity-key="entityKey"
                                    chart-x-label="d (Dofus)"
                                    chart-y-label="k (JDR)"
                                    :chart-height="280"
                                />

                                <!-- Fonctions disponibles (masqué par défaut) -->
                                <div class="collapse collapse-arrow rounded-box border border-base-300 bg-base-200/50">
                                    <input type="checkbox" />
                                    <div class="collapse-title min-h-0 py-2 text-sm font-semibold text-base-content/80">
                                        Fonctions disponibles (Table, Linéaire, Carré, etc.)
                                    </div>
                                    <div class="collapse-content space-y-4">
                                        <span class="label-text block mb-2">Proposer une formule (à valider ensuite)</span>
                                        <div class="flex flex-wrap gap-2">
                                            <Tooltip :content="conversionSuggestionTooltips.table" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('table', entityKey)">Table</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.linear" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('linear', entityKey)">Linéaire</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.power" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('power', entityKey)">Carré</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.shifted_power" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('shifted_power', entityKey)">Carré décalé</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.exponential" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('exponential', entityKey)">Exponentielle</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.log" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('log', entityKey)">Logarithmique</button>
                                            </Tooltip>
                                            <Tooltip :content="conversionSuggestionTooltips.polynomial2" placement="top">
                                                <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('polynomial2', entityKey)">Polynôme 2</button>
                                            </Tooltip>
                                        </div>
                                        <p class="mt-1 text-xs text-base-content/60">Formules générées avec floor() pour le graphique et des entiers.</p>
                                        <p v-if="conversionSuggestionLoading && conversionSuggestionForEntity === entityKey" class="mt-2 text-sm text-info">Calcul en cours…</p>
                                        <p v-else-if="conversionSuggestionError && conversionSuggestionForEntity === entityKey" class="mt-2 text-sm text-error">{{ conversionSuggestionError }}</p>
                                        <div v-else-if="conversionSuggestionFormula && conversionSuggestionForEntity === entityKey" class="mt-3 p-3 rounded-box bg-base-300 border border-base-content/10">
                                            <p class="text-sm font-medium text-base-content/80 mb-1">Proposition :</p>
                                            <code class="block text-xs break-all mb-2">{{ conversionSuggestionFormula }}</code>
                                            <p v-if="conversionSuggestionR2 != null" class="text-xs text-base-content/70 mb-2">R² = {{ conversionSuggestionR2 }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" class="btn btn-sm btn-primary" @click="applyConversionSuggestion">Valider (remplacer le champ)</button>
                                                <button type="button" class="btn btn-sm btn-ghost" @click="clearConversionSuggestion">Annuler</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Échantillonnage (masqué par défaut) -->
                                <div class="collapse collapse-arrow rounded-box border border-base-300 bg-base-200/50">
                                    <input type="checkbox" />
                                    <div class="collapse-title min-h-0 py-2 text-sm font-semibold text-base-content/80">
                                        Échantillons (automatisation pour cette entité)
                                    </div>
                                    <div class="collapse-content space-y-4 pt-2">
                                        <p class="text-xs text-base-content/70">Même tableau Dofus/Krosmoz que pour le groupe : renseignez les valeurs puis proposez une formule pour cette surcharge.</p>
                                        <div class="overflow-x-auto">
                                            <table class="table table-sm table-zebra">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-base-300">Dofus (niv.)</th>
                                                        <th class="bg-base-300">Dofus (valeur)</th>
                                                        <th class="bg-base-300">Krosmoz (niv.)</th>
                                                        <th class="bg-base-300">Krosmoz (valeur)</th>
                                                        <th class="bg-base-300 w-10"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(sampleRow, idx) in (entityRow(entityKey)?.conversion_sample_rows || getDefaultConversionSampleRows())" :key="idx">
                                                        <td><input v-model.number="sampleRow.dofus_level" type="number" min="1" class="input input-bordered input-sm w-20" /></td>
                                                        <td><input v-model.number="sampleRow.dofus_value" type="number" step="any" class="input input-bordered input-sm w-24" placeholder="—" /></td>
                                                        <td><input v-model.number="sampleRow.krosmoz_level" type="number" min="1" class="input input-bordered input-sm w-20" /></td>
                                                        <td><input v-model.number="sampleRow.krosmoz_value" type="number" step="any" class="input input-bordered input-sm w-24" placeholder="—" /></td>
                                                        <td>
                                                            <button type="button" class="btn btn-ghost btn-xs btn-error" :disabled="(entityRow(entityKey)?.conversion_sample_rows?.length ?? 0) <= 1" :aria-label="'Supprimer la ligne ' + (idx + 1)" @click="removeConversionSampleRow(entityKey, idx)">−</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-sm btn-ghost mt-2" @click="addConversionSampleRow(entityKey)">+ Ajouter une ligne</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Normes (entité spécifique) -->
                            <div class="collapse collapse-arrow bg-base-200/30 mt-2">
                                <input type="checkbox" />
                                <div class="collapse-title text-sm font-semibold">Normes (Charte) — {{ entityLabels[entityKey] || entityKey }}</div>
                                <div class="collapse-content">
                                    <NormsPanel
                                        :model-value="entityRow(entityKey)"
                                        :characteristics-list="allCharacteristicsFlat"
                                        :characteristic-key="form.key || selected?.key || ''"
                                        @update:model-value="Object.assign(entityRow(entityKey), $event)"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    </section>

                    </fieldset>
                    <div
                        v-if="!selected.is_linked"
                        class="mt-4 flex flex-wrap items-center gap-2 border-t border-base-300 px-1 py-2"
                    >
                        <span class="label-text text-xs sm:text-sm">Spécifier pour une entité :</span>
                        <select class="select select-bordered select-xs h-7 min-h-7 max-w-48 sm:select-sm sm:h-8 sm:max-w-xs" aria-label="Spécifier pour une entité" @change="(e) => { const v = e.target.value; if (v) { addEntityOverride(v); e.target.value = ''; } }">
                            <option value="">— Choisir —</option>
                            <option v-for="ek in entitiesForSpecifyDropdown.filter(e => !selectedEntityOverrides.includes(e))" :key="ek" :value="ek">{{ entityLabels[ek] || ek }}</option>
                        </select>
                    </div>
                </form>
                </div>
            </template>
            <div v-else class="flex h-64 flex-col items-center justify-center gap-2 text-base-content/60">
                <p>Sélectionnez une caractéristique dans la liste à gauche pour l'éditer.</p>
                <p class="text-sm">Ou cliquez sur « Créer une caractéristique » pour en ajouter une.</p>
            </div>
        </main>

        <!-- Popover aide formules : Teleport vers body pour passer au-dessus du menu latéral -->
        <Teleport to="body">
            <div
                v-if="formulaHelpAnchor"
                ref="formulaHelpPopoverRef"
                class="fixed z-[1100] rounded-box bg-base-200 p-4 shadow-xl border border-base-300 w-[90vw] max-w-[90vw] md:w-[60vw] md:max-w-[60vw] lg:w-96 lg:max-w-md min-w-0 overflow-hidden overflow-y-auto max-h-[85vh] box-border"
                :style="{ left: formulaHelpAnchor.left + 'px', top: formulaHelpAnchor.top + 'px' }"
            >
                <div class="w-full min-w-0 max-w-full overflow-hidden box-border [overflow-wrap:anywhere]">
                    <h4 class="font-semibold mb-2 w-full max-w-full">{{ formulaHelpContent.title }}</h4>
                    <ul class="text-sm space-y-1 list-disc list-inside w-full max-w-full min-w-0 [overflow-wrap:anywhere]">
                        <li class="[overflow-wrap:anywhere]">{{ formulaHelpContent.variables }}</li>
                        <li class="[overflow-wrap:anywhere]">{{ formulaHelpContent.operators }}</li>
                        <li class="[overflow-wrap:anywhere]">{{ formulaHelpContent.funcs1 }}</li>
                        <li class="[overflow-wrap:anywhere]">{{ formulaHelpContent.funcs2 }}</li>
                    </ul>
                    <p class="text-sm mt-2 text-base-content/80 w-full max-w-full min-w-0 [overflow-wrap:anywhere]">{{ formulaHelpContent.examples }}</p>
                    <p class="text-sm mt-2 text-warning w-full max-w-full min-w-0 [overflow-wrap:anywhere]">{{ formulaHelpContent.equipment }}</p>
                </div>
            </div>
        </Teleport>
    </div>

    <ConfirmPasswordModal
        v-model:open="showAdminConfirmModal"
        title="Administration des caractéristiques"
        message="Cette section modifie les caractéristiques en base. Entre ton mot de passe pour confirmer ton identité."
        confirm-label="Accéder"
        @confirmed="onAdminPasswordConfirmed"
    />
</template>

<style scoped>
/* Thème couleur caractéristique : boutons, inputs, fonds (quand --color est défini) */
.has-characteristic-color .btn-primary,
.has-characteristic-color .btn.btn-primary {
    background-color: var(--color);
    border-color: var(--color);
}
.has-characteristic-color .btn-primary:hover,
.has-characteristic-color .btn.btn-primary:hover {
    background-color: color-mix(in srgb, var(--color) 85%, black);
    border-color: color-mix(in srgb, var(--color) 85%, black);
}
.has-characteristic-color .btn-ghost:hover {
    background-color: color-mix(in srgb, var(--color) 15%, transparent);
}
.has-characteristic-color .input:focus,
.has-characteristic-color input.input-bordered:focus,
.has-characteristic-color .select.select-bordered:focus,
.has-characteristic-color select.select-bordered:focus {
    border-color: var(--color);
    outline-color: var(--color);
}
.has-characteristic-color .input-primary {
    border-color: color-mix(in srgb, var(--color) 50%, transparent);
}
.has-characteristic-color .input-primary:focus {
    border-color: var(--color);
    outline-color: var(--color);
}
/* Fond très discret pour la zone formulaire */
.has-characteristic-color.characteristic-theme {
    background-color: color-mix(in srgb, var(--color) 5%, transparent);
    border-radius: var(--radius-box, 0.125rem);
    padding: 0.25rem;
}
</style>
