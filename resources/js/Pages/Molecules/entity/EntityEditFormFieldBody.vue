<script setup>
/* eslint-disable vue/no-mutating-props -- `form` est la même référence que `useForm()` du parent (objet réactif partagé). */
/**
 * Rendu d’un champ du formulaire d’entité (extrait pour réutilisation dans EntityEditForm).
 */
import { computed } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import FileField from '@/Pages/Molecules/data-input/FileField.vue';
import ToggleCore from '@/Pages/Atoms/data-input/ToggleCore.vue';
import SpellElementPrimariesField from '@/Pages/Molecules/entity/spell/SpellElementPrimariesField.vue';
import SpellTypesMultiField from '@/Pages/Molecules/entity/spell/SpellTypesMultiField.vue';
import AreaNotationField from '@/Pages/Molecules/data-input/AreaNotationField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import RichTextEditorField from '@/Pages/Molecules/data-input/RichTextEditorField.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import { getByDbColumn, getMonsterFieldMeta } from '@/Composables/store/useCharacteristicsStore';
import CharacteristicGmButton from '@/Pages/Molecules/data-display/CharacteristicGmButton.vue';
import { getCharacteristicColorStyle, getCharacteristicContainerStyle } from '@/Utils/color/Color';
import { resolveSpellUsageCharacteristicVisual } from '@/Utils/Entity/spellUsageCharacteristicVisual';
import { formatPoRange, parsePoRange } from '@/Utils/Entity/poRange';

const props = defineProps({
    field: { type: Object, required: true },
    form: { type: Object, required: true },
    isMultiEdit: { type: Boolean, default: false },
    differentFields: { type: Array, default: () => [] },
    fieldDirty: { type: Object, default: () => ({}) },
    checkboxDirty: { type: Object, default: () => ({}) },
    getFieldLabel: { type: Function, required: true },
    getFieldHelper: { type: Function, required: true },
    getFieldValidation: { type: Function, required: true },
    getFieldPlaceholder: { type: Function, required: true },
    getFieldRenderType: { type: Function, required: true },
    getFileCurrentPath: { type: Function, required: true },
    getFileAccept: { type: Function, required: true },
    formatDisplayValue: { type: Function, required: true },
    markDirty: { type: Function, required: true },
    resetFieldMultiEdit: { type: Function, required: true },
    resetBoolMultiEdit: { type: Function, required: true },
    /** @type {(fieldKey: string, value: boolean) => void} */
    onCheckboxUpdate: { type: Function, required: true },
    /**
     * Groupe Characteristics (ex. `spell`) pour icônes / couleurs BDD.
     * @type {string|null}
     */
    characteristicsGroup: {
        type: String,
        default: null,
    },
});

const charMeta = computed(() => {
    const fieldKey = props.field?.key;
    if (!fieldKey) {
        return null;
    }
    if (props.characteristicsGroup) {
        return getByDbColumn(props.characteristicsGroup, fieldKey);
    }
    return getMonsterFieldMeta()[fieldKey] ?? null;
});

/** Clé caractéristique pour le panneau MJ (ex. `monster_size`). */
const characteristicNormsKey = computed(() => charMeta.value?.key ?? null);

/** Groupe API (creature/object/spell…). */
const characteristicGmGroup = computed(() => {
    if (!characteristicNormsKey.value) {
        return '';
    }
    if (props.characteristicsGroup) {
        return props.characteristicsGroup;
    }
    if (getMonsterFieldMeta()[props.field?.key]) {
        return 'creature';
    }
    return '';
});

/** Entité pivot (`*`, monster…). Distinct du groupe. */
const characteristicGmEntity = computed(() => {
    if (!characteristicNormsKey.value) {
        return '*';
    }
    if (getMonsterFieldMeta()[props.field?.key]) {
        return 'monster';
    }
    return '*';
});

function updatePoRange(value) {
    const parsed = parsePoRange(value);
    props.form.po_min = parsed.po_min;
    props.form.po_max = parsed.po_max;
    props.markDirty('po_min');
}

/** Couleur hex caractéristique (BDD ou override formulaire). */
const characteristicHex = computed(() => {
    const fromDb = charMeta.value?.color;
    const fromCfg = props.field?.config?.uiColor;
    const raw = (typeof fromDb === 'string' && fromDb.startsWith('#') ? fromDb : null) || fromCfg || null;
    return raw;
});

/** Source {@link Image} (chemin logique `icons/caracteristics/…`). */
const characteristicImageSource = computed(() => {
    const icon = charMeta.value?.icon;
    if (!icon || typeof icon !== 'string') return null;
    if (icon.startsWith('fa-') || icon.includes('fa-')) return null;
    const s = icon.trim();
    if (s.startsWith('icons/caracteristics/')) {
        return s;
    }
    const name = s.includes('/') ? s.split('/').pop() : s;
    return name ? `icons/caracteristics/${name}` : null;
});

const checkboxFaSource = computed(() => {
    const fromDb = charMeta.value?.icon;
    if (typeof fromDb === 'string' && (fromDb.startsWith('fa-') || fromDb.includes('fa-solid'))) {
        return fromDb;
    }
    const fromCfg = props.field?.config?.uiIcon;
    if (typeof fromCfg === 'string' && fromCfg.includes('fa-')) {
        return fromCfg;
    }
    return 'fa-solid fa-toggle-on';
});

const checkboxContainerStyle = computed(() => {
    const hex = characteristicHex.value;
    if (!hex || !hex.startsWith('#')) return {};
    return getCharacteristicContainerStyle(hex);
});

const checkboxLabelStyle = computed(() => {
    const hex = characteristicHex.value;
    if (!hex) return {};
    return getCharacteristicColorStyle(hex) || {};
});

/** Bordure gauche teintée pour champs liés à une caractéristique (texte / nombre). */
const textFieldAccentClass = computed(() =>
    props.characteristicsGroup && characteristicHex.value?.startsWith('#')
        ? 'rounded-lg border border-base-300/55 bg-base-100/30 py-1 pl-2.5 pr-1'
        : '',
);

const textFieldAccentStyle = computed(() => {
    const hex = characteristicHex.value;
    if (!hex?.startsWith('#')) return {};
    return {
        borderLeftWidth: '3px',
        borderLeftColor: hex,
    };
});

const fieldLabelText = computed(() => props.getFieldLabel(props.field.key, props.field.config));
const fieldHelperText = computed(() => props.getFieldHelper(props.field.key, props.field.config) || '');

/** Présentation « carte image » : clé / label image ou accept image (évite accept vide côté FileField). */
const fileFieldPresentation = computed(() => {
    const key = String(props.field.key || '').toLowerCase();
    const lbl = String(props.field.config?.label || '').toLowerCase();
    const accept = String(props.getFileAccept(props.field.key, props.field.config) || '');
    const looksImage =
        accept.includes('image') ||
        key.includes('image') ||
        key.includes('thumbnail') ||
        lbl.includes('image');
    return looksImage ? 'imageHero' : 'default';
});

/** `is_magic` : conflit multi-édition (indéterminé tant que l’utilisateur n’a pas choisi). */
const isPhysiqueWakfuConflict = computed(
    () =>
        props.isMultiEdit &&
        props.differentFields.includes(props.field.key) &&
        props.field.key === 'is_magic' &&
        !props.checkboxDirty?.[props.field.key],
);

const physiqueWakfuVisual = computed(() => {
    if (props.field.key !== 'is_magic') {
        return resolveSpellUsageCharacteristicVisual('is_magic');
    }
    if (isPhysiqueWakfuConflict.value) {
        return resolveSpellUsageCharacteristicVisual('is_magic');
    }
    return resolveSpellUsageCharacteristicVisual('is_magic', Boolean(props.form[props.field.key]));
});

const physiqueWakfuImageSource = computed(() => {
    const raw = physiqueWakfuVisual.value.source;
    if (!raw || String(raw).startsWith('fa-')) return null;
    const s = String(raw).trim();
    if (s.startsWith('icons/caracteristics/')) return s;
    const name = s.includes('/') ? s.split('/').pop() : s;
    return name ? `icons/caracteristics/${name}` : null;
});

const physiqueWakfuFaIcon = computed(() => {
    if (isPhysiqueWakfuConflict.value) return 'fa-solid fa-layer-group';
    return props.form[props.field.key] ? 'fa-solid fa-wand-magic-sparkles' : 'fa-solid fa-hand-fist';
});

const physiqueWakfuContainerStyle = computed(() => {
    if (isPhysiqueWakfuConflict.value) return {};
    const hex = physiqueWakfuVisual.value.color;
    if (!hex || !hex.startsWith('#')) return {};
    return getCharacteristicContainerStyle(hex);
});

const physiqueWakfuAccentStyle = computed(() => {
    if (isPhysiqueWakfuConflict.value) return {};
    const hex = physiqueWakfuVisual.value.color;
    if (!hex || !hex.startsWith('#')) return {};
    return getCharacteristicColorStyle(hex) || {};
});

/**
 * @param {boolean} wantsWakfu - `true` = Wakfu (magique), `false` = Physique
 */
function physiqueWakfuBtnClass(wantsWakfu) {
    if (isPhysiqueWakfuConflict.value) {
        return 'btn-outline border-base-300/60 bg-base-100/40 hover:bg-base-200/50';
    }
    const on = Boolean(props.form[props.field.key]);
    const selected = wantsWakfu ? on : !on;
    return selected ? 'btn-primary' : 'btn-ghost border border-base-300/50';
}

/**
 * @param {boolean} wantsWakfu
 */
function pickPhysiqueWakfu(wantsWakfu) {
    props.onCheckboxUpdate(props.field.key, wantsWakfu);
}

/** Options select : support `options` fonction (résolution lazy après chargement Inertia). */
const resolvedSelectOptions = computed(() => {
    const opts = props.field?.config?.options;
    if (typeof opts === 'function') {
        try {
            return opts() || [];
        } catch {
            return [];
        }
    }
    return Array.isArray(opts) ? opts : [];
});
</script>

<template>
    <!-- Lecture seule -->
    <div
        v-if="field?.config && getFieldRenderType(field.key, field.config) === 'display'"
        class="rounded-(--radius-field) border border-base-300/60 bg-base-200/20 px-3 py-2"
    >
        <div class="text-xs font-medium uppercase tracking-wide opacity-60">
            {{ fieldLabelText }}
        </div>
        <div class="text-sm mt-1">
            {{ formatDisplayValue(form[field.key]) }}
        </div>
    </div>

    <!-- Sort `is_magic` : Physique vs Wakfu (booléen), icône / teinte selon la valeur -->
    <div
        v-else-if="field?.config && getFieldRenderType(field.key, field.config) === 'physiqueWakfu'"
        class="w-full"
    >
        <div
            class="flex gap-2 rounded-lg border border-base-300/70 p-2 sm:p-2.5"
            :style="physiqueWakfuContainerStyle"
        >
            <Image
                v-if="physiqueWakfuImageSource"
                :source="physiqueWakfuImageSource"
                :alt="isPhysiqueWakfuConflict ? fieldLabelText : Boolean(form[field.key]) ? 'Wakfu' : 'Physique'"
                width="28"
                height="28"
                fit="contain"
                class="mt-0.5 h-7 w-7 shrink-0"
            />
            <Icon
                v-else
                :source="physiqueWakfuFaIcon"
                :alt="isPhysiqueWakfuConflict ? fieldLabelText : Boolean(form[field.key]) ? 'Wakfu' : 'Physique'"
                size="md"
                class="mt-0.5 shrink-0 self-start opacity-90"
                :style="physiqueWakfuAccentStyle"
            />
            <div class="min-w-0 flex-1">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                    <div class="min-w-0 flex-1">
                        <div
                            class="text-sm font-semibold leading-snug text-base-content"
                            :style="physiqueWakfuAccentStyle"
                        >
                            {{ fieldLabelText }}
                        </div>
                        <p
                            v-if="isPhysiqueWakfuConflict"
                            class="mt-0.5 flex items-center gap-1 text-xs font-semibold text-warning"
                        >
                            <Icon source="fa-solid fa-exclamation-triangle" alt="" size="xs" />
                            Valeurs différentes — choisir Physique ou Wakfu
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <CharacteristicGmButton
                            v-if="characteristicNormsKey"
                            :characteristic-key="characteristicNormsKey"
                            :group="characteristicGmGroup"
                            :entity="characteristicGmEntity"
                            :label="fieldLabelText"
                        />
                        <div class="join join-horizontal">
                            <button
                                type="button"
                                class="btn btn-sm join-item min-w-21"
                                :class="physiqueWakfuBtnClass(false)"
                                @click="pickPhysiqueWakfu(false)"
                            >
                                Physique
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm join-item min-w-21"
                                :class="physiqueWakfuBtnClass(true)"
                                @click="pickPhysiqueWakfu(true)"
                            >
                                Wakfu
                            </button>
                        </div>
                        <Btn
                            v-if="isMultiEdit && differentFields.includes(field.key) && checkboxDirty?.[field.key]"
                            size="xs"
                            variant="ghost"
                            title="Annuler la modification (ne pas modifier ce champ)"
                            @click.stop="resetBoolMultiEdit(field.key)"
                        >
                            <i class="fa-solid fa-rotate-left"></i>
                        </Btn>
                    </div>
                </div>
                <p v-if="fieldHelperText" class="mt-1 text-xs leading-snug text-base-content/75">
                    {{ fieldHelperText }}
                </p>
            </div>
        </div>
    </div>

    <!-- Booléen : libellé visible + métadonnées Characteristics -->
    <div
        v-else-if="field?.config && getFieldRenderType(field.key, field.config) === 'checkbox'"
        class="w-full"
    >
        <div
            class="flex gap-2 rounded-lg border border-base-300/70 p-2 sm:p-2.5"
            :style="checkboxContainerStyle"
        >
            <Image
                v-if="characteristicImageSource"
                :source="characteristicImageSource"
                :alt="fieldLabelText"
                width="28"
                height="28"
                fit="contain"
                class="mt-0.5 h-7 w-7 shrink-0"
            />
            <Icon
                v-else
                :source="checkboxFaSource"
                :alt="fieldLabelText"
                size="md"
                class="mt-0.5 shrink-0 self-start opacity-90"
                :style="checkboxLabelStyle"
            />
            <div class="min-w-0 flex-1">
                <div class="flex items-start gap-2">
                    <div
                        class="min-w-0 flex-1 text-sm font-semibold leading-snug text-base-content"
                        :style="checkboxLabelStyle"
                    >
                        {{ fieldLabelText }}
                    </div>
                    <div class="flex shrink-0 items-center gap-2 pt-0.5 sm:gap-2.5">
                        <CharacteristicGmButton
                            v-if="characteristicNormsKey"
                            :characteristic-key="characteristicNormsKey"
                            :group="characteristicGmGroup"
                            :entity="characteristicGmEntity"
                            :label="fieldLabelText"
                        />
                        <ToggleCore
                            variant="glass"
                            size="sm"
                            color="primary"
                            :model-value="Boolean(form[field.key])"
                            :indeterminate="differentFields.includes(field.key) && !checkboxDirty?.[field.key]"
                            @update:model-value="(v) => onCheckboxUpdate(field.key, v)"
                        />
                        <span
                            class="flex min-w-10 items-center gap-1 text-sm transition-colors duration-200"
                            :class="{
                                'opacity-80': !(differentFields.includes(field.key) && !checkboxDirty?.[field.key]),
                                'text-warning font-semibold':
                                    differentFields.includes(field.key) && !checkboxDirty?.[field.key],
                            }"
                        >
                            <template v-if="differentFields.includes(field.key) && !checkboxDirty?.[field.key]">
                                <Icon source="fa-solid fa-exclamation-triangle" alt="Valeurs différentes" size="xs" />
                                Valeurs différentes
                            </template>
                            <template v-else>
                                {{ Boolean(form[field.key]) ? 'Oui' : 'Non' }}
                            </template>
                        </span>
                        <Btn
                            v-if="differentFields.includes(field.key) && checkboxDirty?.[field.key]"
                            size="xs"
                            variant="ghost"
                            title="Annuler la modification (ne pas modifier ce champ)"
                            @click.stop="resetBoolMultiEdit(field.key)"
                        >
                            <i class="fa-solid fa-rotate-left"></i>
                        </Btn>
                    </div>
                </div>
                <p
                    v-if="fieldHelperText"
                    class="mt-1 text-xs leading-snug text-base-content/75"
                >
                    {{ fieldHelperText }}
                </p>
            </div>
        </div>
    </div>

    <!-- Pas de Tooltip DaisyUI ici : data-tip/::before recouvrait les textarea et bloquait saisie/focus. -->
    <div v-else-if="field?.config" class="w-full min-w-0">
        <!-- Éléments primaires (sort) : multi + valeur encodée 0–29 -->
        <div
            v-if="getFieldRenderType(field.key, field.config) === 'elementPrimaries'"
            class="flex w-full items-start gap-2"
        >
            <SpellElementPrimariesField
                class="min-w-0 flex-1"
                v-model="form[field.key]"
                @update:model-value="() => markDirty(field.key)"
                :label="getFieldLabel(field.key, field.config)"
                :helper="getFieldHelper(field.key, field.config)"
                :validation="getFieldValidation(field.key)"
            />
            <Btn
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                size="xs"
                variant="ghost"
                class="mt-6 shrink-0"
                title="Annuler la modification (ne pas modifier ce champ)"
                @click.stop="resetFieldMultiEdit(field.key, 'elementPrimaries')"
            >
                <i class="fa-solid fa-rotate-left"></i>
            </Btn>
        </div>

        <!-- Types de sort (multi) -->
        <div
            v-else-if="getFieldRenderType(field.key, field.config) === 'spellTypesMulti'"
            class="flex w-full items-start gap-2"
        >
            <SpellTypesMultiField
                class="min-w-0 flex-1"
                v-model="form[field.key]"
                @update:model-value="() => markDirty(field.key)"
                :label="getFieldLabel(field.key, field.config)"
                :helper="getFieldHelper(field.key, field.config)"
                :options="field.config.options || []"
                :validation="getFieldValidation(field.key)"
            />
            <Btn
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                size="xs"
                variant="ghost"
                class="mt-6 shrink-0"
                title="Annuler la modification (ne pas modifier ce champ)"
                @click.stop="resetFieldMultiEdit(field.key, 'spellTypesMulti')"
            >
                <i class="fa-solid fa-rotate-left"></i>
            </Btn>
        </div>

        <InputField
            v-else-if="getFieldRenderType(field.key, field.config) === 'poRange'"
            :model-value="formatPoRange(form.po_min, form.po_max)"
            :label="getFieldLabel(field.key, field.config)"
            :helper="getFieldHelper(field.key, field.config)"
            :validation="getFieldValidation(field.key)"
            :placeholder="getFieldPlaceholder(field.key, field.config) || '4 ou 2-8'"
            @update:model-value="updatePoRange"
        />

        <AreaNotationField
            v-else-if="getFieldRenderType(field.key, field.config) === 'areaNotation'"
            v-model="form[field.key]"
            :label="getFieldLabel(field.key, field.config)"
            :helper="getFieldHelper(field.key, field.config)"
            @update:model-value="() => markDirty(field.key)"
        />

        <!-- InputField -->
        <div
            v-else-if="
                getFieldRenderType(field.key, field.config) === 'text' ||
                ![
                    'textarea',
                    'richtext',
                    'select',
                    'file',
                    'number',
                    'checkbox',
                    'physiqueWakfu',
                    'display',
                    'elementPrimaries',
                    'spellTypesMulti',
                    'poRange',
                    'areaNotation',
                ].includes(getFieldRenderType(field.key, field.config))
            "
            class="w-full min-w-0"
            :class="textFieldAccentClass"
            :style="textFieldAccentStyle"
        >
            <InputField
                v-model="form[field.key]"
                @update:model-value="() => markDirty(field.key)"
                :label="getFieldLabel(field.key, field.config)"
                :type="field.config.type || 'text'"
                :required="field.config.required"
                :helper="getFieldHelper(field.key, field.config)"
                :validation="getFieldValidation(field.key)"
                :placeholder="getFieldPlaceholder(field.key, field.config)"
            >
                <template
                    v-if="characteristicNormsKey || (isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key])"
                    #overEnd
                >
                    <CharacteristicGmButton
                        v-if="characteristicNormsKey"
                        :characteristic-key="characteristicNormsKey"
                        :group="characteristicGmGroup"
                            :entity="characteristicGmEntity"
                        :label="fieldLabelText"
                    />
                    <Btn
                        v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                        size="xs"
                        variant="ghost"
                        title="Annuler la modification (ne pas modifier ce champ)"
                        @click.stop="resetFieldMultiEdit(field.key, field.config.type || 'text')"
                    >
                        <i class="fa-solid fa-rotate-left"></i>
                    </Btn>
                </template>
            </InputField>
        </div>

        <div v-else-if="getFieldRenderType(field.key, field.config) === 'richtext'" class="w-full min-w-0">
            <RichTextEditorField
                v-model="form[field.key]"
                @update:model-value="() => markDirty(field.key)"
                :label="getFieldLabel(field.key, field.config)"
                :helper="getFieldHelper(field.key, field.config)"
                :validation="getFieldValidation(field.key)"
                :placeholder="getFieldPlaceholder(field.key, field.config) || 'Décrivez les effets…'"
                :height="field.config.richEditorHeight || 'min-h-[220px]'"
            />
            <Btn
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                size="xs"
                variant="ghost"
                class="mt-2"
                title="Annuler la modification (ne pas modifier ce champ)"
                @click.stop="resetFieldMultiEdit(field.key, 'richtext')"
            >
                <i class="fa-solid fa-rotate-left"></i>
            </Btn>
        </div>

        <TextareaField
            v-else-if="getFieldRenderType(field.key, field.config) === 'textarea'"
            v-model="form[field.key]"
            @update:model-value="() => markDirty(field.key)"
            :label="getFieldLabel(field.key, field.config)"
            :required="field.config.required"
            :helper="getFieldHelper(field.key, field.config)"
            :validation="getFieldValidation(field.key)"
            :placeholder="getFieldPlaceholder(field.key, field.config)"
        >
            <template
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                #overEnd
            >
                <Btn
                    size="xs"
                    variant="ghost"
                    title="Annuler la modification (ne pas modifier ce champ)"
                    @click.stop="resetFieldMultiEdit(field.key, 'textarea')"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </Btn>
            </template>
        </TextareaField>

        <SelectField
            v-else-if="getFieldRenderType(field.key, field.config) === 'select'"
            v-model="form[field.key]"
            @update:model-value="() => markDirty(field.key)"
            :label="getFieldLabel(field.key, field.config)"
            :options="resolvedSelectOptions"
            :option-badge="field.config.optionBadge || null"
            :required="field.config.required"
            :helper="getFieldHelper(field.key, field.config)"
            :validation="getFieldValidation(field.key)"
            :placeholder="getFieldPlaceholder(field.key, field.config)"
        >
            <template
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                #overEnd
            >
                <Btn
                    size="xs"
                    variant="ghost"
                    title="Annuler la modification (ne pas modifier ce champ)"
                    @click.stop="resetFieldMultiEdit(field.key, 'select')"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </Btn>
            </template>
        </SelectField>

        <FileField
            v-else-if="getFieldRenderType(field.key, field.config) === 'file'"
            v-model="form[field.key]"
            :current-path="getFileCurrentPath(field.key)"
            :accept="getFileAccept(field.key, field.config)"
            :label="getFieldLabel(field.key, field.config)"
            :required="field.config.required"
            :helper="getFieldHelper(field.key, field.config)"
            :validation="getFieldValidation(field.key)"
            :presentation="fileFieldPresentation"
        />

        <div
            v-else-if="getFieldRenderType(field.key, field.config) === 'number'"
            class="w-full min-w-0"
            :class="textFieldAccentClass"
            :style="textFieldAccentStyle"
        >
            <InputField
                v-model="form[field.key]"
                @update:model-value="() => markDirty(field.key)"
                :label="getFieldLabel(field.key, field.config)"
                type="number"
                :required="field.config.required"
                :helper="getFieldHelper(field.key, field.config)"
                :validation="getFieldValidation(field.key)"
                :placeholder="getFieldPlaceholder(field.key, field.config)"
            >
                <template
                    v-if="characteristicNormsKey || (isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key])"
                    #overEnd
                >
                    <CharacteristicGmButton
                        v-if="characteristicNormsKey"
                        :characteristic-key="characteristicNormsKey"
                        :group="characteristicGmGroup"
                            :entity="characteristicGmEntity"
                        :label="fieldLabelText"
                    />
                    <Btn
                        v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                        size="xs"
                        variant="ghost"
                        title="Annuler la modification (ne pas modifier ce champ)"
                        @click.stop="resetFieldMultiEdit(field.key, 'number')"
                    >
                        <i class="fa-solid fa-rotate-left"></i>
                    </Btn>
                </template>
            </InputField>
        </div>
    </div>
</template>
