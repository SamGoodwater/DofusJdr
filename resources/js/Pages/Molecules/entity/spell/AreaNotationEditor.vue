<script setup>
/**
 * Éditeur de zone : choix de forme par icône, puis nombres, aperçu et notation brute.
 *
 * @example
 * <AreaNotationEditor v-model="degree.area" name="area_d1" />
 */
import { computed, ref, watch } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SpellZonePreview from '@/Pages/Molecules/entity/spell/SpellZonePreview.vue';
import {
    AREA_SHAPE_ICONS,
    AREA_SHAPE_LABELS,
    AREA_SHAPES,
} from '@/Utils/Entity/Areas';
import {
    AREA_NOTATION_HELP,
    buildAreaNotation,
    getAreaShapeEditorHelp,
    isValidAreaNotation,
    normalizeAreaInput,
    parseAreaNotationParts,
} from '@/Utils/Entity/areaNotation.js';

const props = defineProps({
    modelValue: { type: String, default: '' },
    name: { type: String, default: 'area' },
    /** Validation externe (ex. depuis le parent). */
    validation: { type: Object, default: undefined },
});

const emit = defineEmits(['update:modelValue']);

const rawMode = ref(false);
const shape = ref('point');
const paramA = ref('');
const paramB = ref('');
const rawText = ref('');

function syncFromModel(value) {
    const parts = parseAreaNotationParts(value);
    rawText.value = parts.raw;
    if (parts.mode === 'shape' || parts.mode === 'invalid') {
        rawMode.value = true;
        shape.value = parts.shape || 'point';
        paramA.value = '';
        paramB.value = '';
        return;
    }
    rawMode.value = false;
    if (parts.mode === 'empty') {
        shape.value = 'point';
        paramA.value = '';
        paramB.value = '';
        return;
    }
    shape.value = parts.shape || 'point';
    paramA.value = parts.a == null ? '' : String(parts.a);
    paramB.value = parts.b == null ? '' : String(parts.b);
}

watch(
    () => props.modelValue,
    (value) => {
        const next = normalizeAreaInput(value);
        const currentBuilt = rawMode.value
            ? normalizeAreaInput(rawText.value)
            : buildAreaNotation(shape.value, paramA.value, paramB.value);
        if (next === currentBuilt) return;
        syncFromModel(value);
    },
    { immediate: true },
);

function emitBuilt() {
    if (rawMode.value) {
        emit('update:modelValue', normalizeAreaInput(rawText.value));
        return;
    }
    if (shape.value === 'point') {
        emit('update:modelValue', 'point');
        return;
    }
    const built = buildAreaNotation(shape.value, paramA.value, paramB.value);
    emit('update:modelValue', built);
}

function selectShape(nextShape) {
    rawMode.value = false;
    shape.value = nextShape;
    if (nextShape === 'point') {
        paramA.value = '';
        paramB.value = '';
    } else if (nextShape === 'line' && paramA.value === '') {
        paramA.value = '1';
        paramB.value = '';
    } else if ((nextShape === 'cross' || nextShape === 'circle') && (paramA.value === '' || paramB.value === '')) {
        if (paramA.value === '') paramA.value = '0';
        if (paramB.value === '') paramB.value = '1';
    } else if (nextShape === 'rect' && (paramA.value === '' || paramB.value === '')) {
        if (paramA.value === '') paramA.value = '1';
        if (paramB.value === '') paramB.value = '1';
    }
    emitBuilt();
}

function onParamChange() {
    emitBuilt();
}

function onRawChange() {
    emit('update:modelValue', normalizeAreaInput(rawText.value));
}

function enableRawMode() {
    rawMode.value = true;
    if (!rawText.value) {
        rawText.value = normalizeAreaInput(props.modelValue);
    }
}

function disableRawMode() {
    const parts = parseAreaNotationParts(rawText.value);
    if (parts.mode === 'known' || parts.mode === 'empty') {
        rawMode.value = false;
        syncFromModel(rawText.value);
        emitBuilt();
        return;
    }
    // Garde le mode brut si la valeur n’est pas une forme connue.
    rawMode.value = true;
}

const helpText = computed(() =>
    rawMode.value ? AREA_NOTATION_HELP : getAreaShapeEditorHelp(shape.value),
);

const previewArea = computed(() => normalizeAreaInput(props.modelValue));

const localValidation = computed(() => {
    if (props.validation) return props.validation;
    const raw = previewArea.value;
    if (raw === '') return undefined;
    return isValidAreaNotation(raw)
        ? undefined
        : { state: 'error', message: `Notation invalide. ${AREA_NOTATION_HELP}` };
});

const needsA = computed(() => shape.value !== 'point');
const needsB = computed(
    () => shape.value === 'cross' || shape.value === 'circle' || shape.value === 'rect',
);

const labelA = computed(() => {
    switch (shape.value) {
        case 'line':
            return 'Longueur (cases)';
        case 'cross':
            return 'Case la plus proche';
        case 'circle':
            return 'Rayon intérieur';
        case 'rect':
            return 'Largeur';
        default:
            return 'Paramètre';
    }
});

const labelB = computed(() => {
    switch (shape.value) {
        case 'cross':
            return 'Case la plus lointaine';
        case 'circle':
            return 'Rayon extérieur';
        case 'rect':
            return 'Hauteur';
        default:
            return 'Paramètre';
    }
});

const builtPreview = computed(() => {
    if (rawMode.value) return normalizeAreaInput(rawText.value);
    if (shape.value === 'point') return 'point';
    return buildAreaNotation(shape.value, paramA.value, paramB.value) || '…';
});
</script>

<template>
    <div class="space-y-3" data-cy="area-notation-editor">
        <div class="flex flex-wrap items-start gap-3">
            <div class="min-w-0 flex-1 space-y-2">
                <p class="text-xs font-medium text-base-content/70">Forme</p>
                <div class="flex flex-wrap gap-2" role="listbox" aria-label="Forme de zone">
                    <button
                        v-for="s in AREA_SHAPES"
                        :key="s"
                        type="button"
                        role="option"
                        class="btn btn-sm gap-1.5"
                        :class="!rawMode && shape === s ? 'btn-primary' : 'btn-outline'"
                        :aria-selected="!rawMode && shape === s"
                        :title="AREA_SHAPE_LABELS[s]"
                        @click="selectShape(s)"
                    >
                        <Icon :source="AREA_SHAPE_ICONS[s]" size="sm" class="opacity-90" />
                        <span class="hidden sm:inline">{{ AREA_SHAPE_LABELS[s] }}</span>
                    </button>
                </div>

                <div v-if="!rawMode && needsA" class="grid gap-3 sm:grid-cols-2 max-w-xl">
                    <InputField
                        v-model="paramA"
                        :label="labelA"
                        :name="name + '_a'"
                        type="number"
                        @update:model-value="onParamChange"
                    />
                    <InputField
                        v-if="needsB"
                        v-model="paramB"
                        :label="labelB"
                        :name="name + '_b'"
                        type="number"
                        @update:model-value="onParamChange"
                    />
                </div>

                <div v-if="rawMode" class="max-w-xl">
                    <InputField
                        v-model="rawText"
                        label="Notation brute"
                        :name="name"
                        :validation="localValidation"
                        helper="Forme Dofus non reconnue ou saisie libre."
                        @update:model-value="onRawChange"
                    />
                </div>

                <p class="text-xs text-base-content/70 leading-relaxed">
                    {{ helpText }}
                </p>
                <p class="text-[0.7rem] font-mono text-base-content/50">
                    Notation : {{ builtPreview || '—' }}
                </p>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-if="!rawMode"
                        type="button"
                        class="btn btn-ghost btn-xs"
                        @click="enableRawMode"
                    >
                        Saisie brute
                    </button>
                    <button
                        v-else
                        type="button"
                        class="btn btn-ghost btn-xs"
                        @click="disableRawMode"
                    >
                        Revenir aux icônes
                    </button>
                </div>
            </div>
            <div
                v-if="previewArea && isValidAreaNotation(previewArea)"
                class="shrink-0 rounded-box border border-base-300 bg-base-200/40 p-2"
            >
                <SpellZonePreview :area="previewArea" :max-viewport-px="120" />
            </div>
        </div>
    </div>
</template>
