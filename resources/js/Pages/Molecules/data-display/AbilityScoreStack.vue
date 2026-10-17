<script setup>
/**
 * AbilityScoreStack — Grille score → modificateur → sauvegarde → DD par caractéristique.
 *
 * @description
 * Affiche une colonne par stat (For, Int, …) : score en haut, modificateur mis en avant,
 * sauvegarde puis DD de sauvegarde en bas. Réutilise CharacteristicProperty pour icônes / popover.
 *
 * @props {Array<{stat: string, score: Object, modifier: Object, save: Object, saveDc?: Object}>} columns
 * @props {number|null} [levelEffective]
 * @props {'icon'|'labeled'|'spacious'} [density]
 * @props {Object|null} [runtime]
 *
 * @example
 * <AbilityScoreStack :columns="stackItems" density="icon" :runtime="runtime" />
 */
import { computed } from "vue";
import CharacteristicProperty from "@/Pages/Atoms/data-display/CharacteristicProperty.vue";
import {
    characteristicAtLevel,
    levelTableFromRuntime,
    mergeRuntimeIntoViewModel,
    viewModelFromFormulaGroupItem,
} from "@/Composables/entity/useCharacteristicViewModel";
import {
    CHARACTERISTIC_PROPERTY_BADGE,
    CHARACTERISTIC_PROPERTY_DENSITY,
    CHARACTERISTIC_PROPERTY_LAYOUT,
} from "@/Utils/Entity/Constants";
import { CHARACTERISTIC_CARD_DENSITY } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";

const props = defineProps({
    columns: { type: Array, default: () => [] },
    levelEffective: { type: [Number, String], default: null },
    density: {
        type: String,
        default: CHARACTERISTIC_CARD_DENSITY.labeled,
    },
    runtime: { type: Object, default: null },
});

const list = computed(() => (Array.isArray(props.columns) ? props.columns : []));

const gridClass = computed(() => {
    const d = props.density;
    if (d === CHARACTERISTIC_CARD_DENSITY.icon) {
        return "grid grid-cols-3 sm:grid-cols-6 gap-1.5";
    }
    if (d === CHARACTERISTIC_CARD_DENSITY.spacious) {
        return "grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3";
    }
    return "grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2";
});

const columnClass = computed(() => {
    const d = props.density;
    if (d === CHARACTERISTIC_CARD_DENSITY.icon) {
        return "ability-score-col flex flex-col items-center gap-0.5 rounded-md bg-base-200/40 px-1 py-1";
    }
    return "ability-score-col flex flex-col items-center gap-1 rounded-lg border border-base-300/70 bg-base-100/60 px-2 py-2";
});

const propertyDensity = computed(() => {
    const d = props.density;
    if (d === CHARACTERISTIC_CARD_DENSITY.icon) return CHARACTERISTIC_PROPERTY_DENSITY.iconOnly;
    if (d === CHARACTERISTIC_CARD_DENSITY.spacious) return CHARACTERISTIC_PROPERTY_DENSITY.full;
    return CHARACTERISTIC_PROPERTY_DENSITY.short;
});

/** Densité icon : tooltip au survol ; sinon popover de décomposition au clic. */
const useDecompositionPopover = computed(
    () => props.density !== CHARACTERISTIC_CARD_DENSITY.icon,
);

function itemContributionsForKey(key) {
    const lines = props.runtime?.items?.lines;
    if (!Array.isArray(lines) || !key) return [];
    const short = String(key).replace(/_creature$/, "");
    const out = [];
    for (const line of lines) {
        const bonuses = line?.bonuses && typeof line.bonuses === "object" ? line.bonuses : {};
        let amount = bonuses[short];
        if (amount == null && bonuses[key] != null) amount = bonuses[key];
        if (amount == null || Number(amount) === 0) continue;
        out.push({
            item_id: line.item_id,
            name: line.name,
            quantity: line.quantity,
            amount,
        });
    }
    return out;
}

function formulaViewModel(item) {
    let vm = viewModelFromFormulaGroupItem(item);
    const key = vm.key;
    const rc = characteristicAtLevel(props.runtime, props.levelEffective, key);
    vm = mergeRuntimeIntoViewModel(vm, rc);
    const table = levelTableFromRuntime(props.runtime, key);
    if (table.length > 0) {
        vm.levelTable = table;
    }
    vm.itemContributions = itemContributionsForKey(key);
    return vm;
}
</script>

<template>
    <div :class="gridClass" role="list" aria-label="Scores, modificateurs, sauvegardes et DD">
        <div
            v-for="col in list"
            :key="col.stat"
            :class="columnClass"
            role="listitem"
        >
            <CharacteristicProperty
                v-if="col.score"
                :view-model="formulaViewModel(col.score)"
                :density="propertyDensity"
                :layout="CHARACTERISTIC_PROPERTY_LAYOUT.inline"
                :badge="CHARACTERISTIC_PROPERTY_BADGE.none"
                size="sm"
                :prefer-decomposition-popover="useDecompositionPopover"
                class="opacity-80"
            />
            <CharacteristicProperty
                v-if="col.modifier"
                :view-model="formulaViewModel(col.modifier)"
                :density="propertyDensity"
                :layout="CHARACTERISTIC_PROPERTY_LAYOUT.inline"
                :badge="CHARACTERISTIC_PROPERTY_BADGE.none"
                size="sm"
                :prefer-decomposition-popover="useDecompositionPopover"
                class="font-semibold scale-110 origin-center"
            />
            <CharacteristicProperty
                v-if="col.save"
                :view-model="formulaViewModel(col.save)"
                :density="propertyDensity"
                :layout="CHARACTERISTIC_PROPERTY_LAYOUT.inline"
                :badge="CHARACTERISTIC_PROPERTY_BADGE.none"
                size="sm"
                :prefer-decomposition-popover="useDecompositionPopover"
                class="opacity-90 text-[0.9em]"
            />
            <CharacteristicProperty
                v-if="col.saveDc"
                :view-model="formulaViewModel(col.saveDc)"
                :density="propertyDensity"
                :layout="CHARACTERISTIC_PROPERTY_LAYOUT.inline"
                :badge="CHARACTERISTIC_PROPERTY_BADGE.none"
                size="sm"
                :prefer-decomposition-popover="useDecompositionPopover"
                class="opacity-90 text-[0.9em]"
            />
        </div>
    </div>
</template>
