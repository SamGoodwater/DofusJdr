<script setup>
/**
 * Édition du template Barèmes de sorts : contenu dynamique (normes en base),
 * seul le mode compact est configurable ici.
 *
 * @example
 * <SectionSpellReferenceScalesEdit :section="section" :settings="settings" />
 */
import { nextTick, ref, watch } from 'vue';
import InlineSaveStatus from '@/Pages/Atoms/feedback/InlineSaveStatus.vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['data-updated']);
const { saveSection } = useSectionSave();

const syncFromProps = ref(false);
const lastPersistSignature = ref('');
const saveState = ref('idle');
let saveStateTimer = null;

const localSettings = ref({
    compact: false,
});

function setSaveState(state) {
    saveState.value = state;
    if (saveStateTimer) {
        clearTimeout(saveStateTimer);
        saveStateTimer = null;
    }
    if (state === 'saved') {
        saveStateTimer = setTimeout(() => {
            saveState.value = 'idle';
        }, 1600);
    }
}

function normalizeForPersist() {
    return {
        compact: Boolean(localSettings.value.compact),
    };
}

function safeStringify(obj) {
    try {
        return JSON.stringify(obj);
    } catch {
        return '';
    }
}

function persist() {
    const sectionId = props.section?.id;
    if (!sectionId || syncFromProps.value) {
        return;
    }

    const normalized = normalizeForPersist();
    const signature = safeStringify(normalized);
    if (signature === lastPersistSignature.value) {
        return;
    }
    lastPersistSignature.value = signature;

    saveSection(
        sectionId,
        {
            settings: {
                ...props.settings,
                ...normalized,
            },
        },
        {
            onQueued: () => setSaveState('saving'),
            onSuccess: () => {
                setSaveState('saved');
                emit('data-updated');
            },
            onError: () => setSaveState('error'),
        },
    );
}

watch(
    () => props.settings,
    async (s) => {
        if (!s) {
            return;
        }
        syncFromProps.value = true;
        localSettings.value = {
            compact: Boolean(s.compact ?? false),
        };
        await nextTick();
        lastPersistSignature.value = safeStringify(normalizeForPersist());
        syncFromProps.value = false;
    },
    { deep: true, immediate: true },
);

watch(localSettings, () => persist(), { deep: true });
</script>

<template>
    <div class="space-y-4">
        <div class="flex justify-end">
            <InlineSaveStatus :state="saveState" />
        </div>
        <p class="text-sm text-base-content/70 leading-snug">
            Les valeurs viennent des normes des caractéristiques de sort
            (admin → Caractéristiques → panneau Normes). Ici tu ne règles que l’affichage.
        </p>
        <label class="label cursor-pointer justify-start gap-3">
            <input
                v-model="localSettings.compact"
                type="checkbox"
                class="checkbox checkbox-sm"
            >
            <span class="label-text">Mode compact</span>
        </label>
    </div>
</template>
