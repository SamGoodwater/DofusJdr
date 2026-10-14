<script setup>
/**
 * Bouton d’aide MJ : ouvre CharacteristicGmPanel (barème, calcul, conversion, propriétés).
 * Remplace CharacteristicNormsHelpButton — sépare clairement `group` et `entity`.
 */
import { computed, ref, watch } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import CharacteristicGmPanel from "@/Pages/Organismes/data-display/CharacteristicGmPanel.vue";
import { normalizeCharacteristicCardGroup } from "@/Composables/characteristic/useCharacteristicCard";

const props = defineProps({
    characteristicKey: {
        type: String,
        required: true,
    },
    /** Groupe API (creature/object/spell/consumable/profile). */
    group: {
        type: String,
        default: "",
    },
    /** Entité pivot (`*`, monster, item…). Ne pas y mettre le groupe. */
    entity: {
        type: String,
        default: "*",
    },
    label: {
        type: String,
        default: "Aide MJ",
    },
    runtimeModel: {
        type: Object,
        default: null,
    },
    highlightValue: {
        type: [Number, String, null],
        default: null,
    },
});

const open = ref(false);

const resolvedGroup = computed(() => normalizeCharacteristicCardGroup(props.group) || "");
const resolvedEntity = computed(() => {
    const e = String(props.entity || "*").trim();
    // Ancien bug : entity=spell/creature/object → traiter comme group.
    if (["creature", "object", "spell", "consumable", "profile"].includes(e)) {
        return "*";
    }
    return e || "*";
});
const resolvedGroupForPanel = computed(() => {
    if (resolvedGroup.value) return resolvedGroup.value;
    const e = String(props.entity || "").trim();
    return normalizeCharacteristicCardGroup(e) || "";
});

watch(
    () => [props.characteristicKey, props.group, props.entity],
    () => {
        // Force un rechargement au prochain open.
        if (!open.value) {
            /* noop — le panel se recharge via ses props */
        }
    },
);

function close() {
    open.value = false;
}
</script>

<template>
    <Btn
        type="button"
        size="xs"
        variant="ghost"
        class="btn-square shrink-0 text-info"
        :title="`${label} — barème, formule et propriétés`"
        :aria-label="`${label} pour ${characteristicKey}`"
        @click.stop="open = true"
    >
        <i class="fa-solid fa-circle-question" aria-hidden="true" />
    </Btn>

    <dialog class="modal" :class="{ 'modal-open': open }" @close="close">
        <div
            class="modal-box flex max-h-[calc(100vh-2rem)] w-[min(100vw-2rem,96rem)] max-w-[96rem] flex-col overflow-hidden"
            @click.stop
        >
            <div class="mb-2 flex shrink-0 justify-end">
                <form method="dialog">
                    <button type="submit" class="btn btn-sm btn-circle btn-ghost" aria-label="Fermer" @click="close">
                        <i class="fa-solid fa-xmark" />
                    </button>
                </form>
            </div>
            <div class="min-h-0 flex-1 overflow-y-auto">
                <CharacteristicGmPanel
                    v-if="open"
                    :characteristic-key="characteristicKey"
                    :group="resolvedGroupForPanel"
                    :entity="resolvedEntity"
                    :runtime-model="runtimeModel"
                    :highlight-value="highlightValue"
                />
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button type="submit" @click="close">Fermer</button>
        </form>
    </dialog>
</template>
