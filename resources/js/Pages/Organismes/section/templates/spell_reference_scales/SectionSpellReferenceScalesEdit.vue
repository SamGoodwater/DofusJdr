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
    group: 'spell',
    characteristic_key: '',
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
        group: String(localSettings.value.group || 'spell'),
        characteristic_key: String(localSettings.value.characteristic_key || ''),
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
            group: String(s.group || 'spell'),
            characteristic_key: String(s.characteristic_key || ''),
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
            Les valeurs viennent des normes des caractéristiques
            (admin → Caractéristiques). Ici tu règles l’affichage et la présélection.
        </p>
        <label class="label cursor-pointer justify-start gap-3">
            <input
                v-model="localSettings.compact"
                type="checkbox"
                class="checkbox checkbox-sm"
            >
            <span class="label-text">Mode compact</span>
        </label>
        <label class="form-control max-w-xs">
            <span class="label-text text-xs">Groupe initial</span>
            <select v-model="localSettings.group" class="select select-bordered select-sm">
                <option value="all">Tous</option>
                <option value="creature">Créature</option>
                <option value="object">Objet</option>
                <option value="spell">Sort</option>
                <option value="consumable">Consommable</option>
                <option value="profile">Profil</option>
            </select>
        </label>
        <label class="form-control max-w-xs">
            <span class="label-text text-xs">Clé caractéristique (optionnel)</span>
            <input
                v-model="localSettings.characteristic_key"
                type="text"
                class="input input-bordered input-sm"
                placeholder="ex. dommages_spell"
            >
        </label>
    </div>
</template>
