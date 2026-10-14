<script setup>
/**
 * Ligne d’édition : total explicite + bonus contextuel pour une colonne composable.
 */
import { computed } from "vue";
import CharacteristicFormulaField from "@/Pages/Molecules/data-input/CharacteristicFormulaField.vue";
import CharacteristicGmButton from "@/Pages/Molecules/data-display/CharacteristicGmButton.vue";
import { contextColumnForComposable } from "@/Support/Creature/creatureComposableColumns";
import { getByDbColumn } from "@/Composables/store/useCharacteristicsStore";

const props = defineProps({
    dbColumn: { type: String, required: true },
    form: { type: Object, required: true },
    previewVariables: { type: Object, default: () => ({}) },
    suggestions: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) },
    /** Entité pivot pour le panneau MJ (`*` ou `monster`). */
    entity: { type: String, default: "*" },
});

const def = computed(() => getByDbColumn("creature", props.dbColumn) || {});
const label = computed(() => def.value.short_name || def.value.name || props.dbColumn);
const characteristicKey = computed(() => def.value.key || "");
const contextKey = computed(() => contextColumnForComposable(props.dbColumn));

const totalHint =
    "Total explicite : s’il est renseigné, il est affiché tel quel (priorité sur base + objets + contexte). Laissez vide pour composer. Les bonus d’équipement ne s’écrivent pas ici.";
const contextHint =
    "Bonus contextuel propre à ce monstre / PNJ (nombre ou formule, sans fourchette ni dé). Pas de bonus d’équipement : ils s’ajoutent automatiquement.";
</script>

<template>
    <div
        class="rounded-lg border border-base-300/70 bg-base-100/40 p-3 space-y-3"
        :data-composable-column="dbColumn"
    >
        <div class="flex items-center justify-between gap-2">
            <p class="text-sm font-semibold text-base-content">{{ label }}</p>
            <CharacteristicGmButton
                v-if="characteristicKey"
                :characteristic-key="characteristicKey"
                group="creature"
                :entity="entity"
                :label="label"
                :highlight-value="form[dbColumn]"
            />
        </div>
        <div class="grid gap-3 md:grid-cols-2">
            <CharacteristicFormulaField
                v-model="form[dbColumn]"
                label="Total explicite"
                :hint="totalHint"
                :preview-variables="previewVariables"
                :suggestions="suggestions"
            />
            <CharacteristicFormulaField
                v-model="form[contextKey]"
                label="Bonus contextuel"
                :hint="contextHint"
                :preview-variables="previewVariables"
                :suggestions="suggestions"
            />
        </div>
        <p v-if="errors[dbColumn]" class="text-xs text-error">{{ errors[dbColumn] }}</p>
        <p v-if="errors[contextKey]" class="text-xs text-error">{{ errors[contextKey] }}</p>
    </div>
</template>
