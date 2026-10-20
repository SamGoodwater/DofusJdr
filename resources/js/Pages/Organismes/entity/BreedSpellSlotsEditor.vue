<script setup>
/**
 * Édition des sorts par emplacement de progression (variantes).
 * Affichage des sorts via SpellViewText (aperçu minimal au survol).
 * Ajout via EntityPicker / api.tables.spells (pas de catalogue Inertia massif).
 * Les sorts pivot 0/1 (« hors emplacement ») sont ignorés à l’enregistrement (reliquat Dofus).
 */
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import EditActionDock from "@/Pages/Molecules/action/EditActionDock.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import SpellViewText from "@/Pages/Molecules/entity/spell/SpellViewText.vue";
import EntityPickerCore from "@/Pages/Organismes/entity/EntityPickerCore.vue";
import { Spell } from "@/Models/Entity/Spell";
import {
    getStandardBreedSlotDefinitions,
    isStandardSlot,
} from "@/Utils/entity/breedSpellSlots";
import { isBreedExtraSpellPivot } from "@/Utils/entity/breedSpellExtra";
import { warnDev } from "@/Utils/dev-logger";
import { BREED_MAX_SPELLS_PER_VARIANT_SLOT } from "@/Utils/entity/breedSpellVariantLimits";

const props = defineProps({
    relations: {
        type: Array,
        default: () => [],
    },
    /** @deprecated Seed local optionnel — recherche via EntityPicker. */
    availableItems: {
        type: Array,
        default: () => [],
    },
    entityId: {
        type: Number,
        required: true,
    },
});

const notificationStore = useNotificationStore();

const standardSlots = getStandardBreedSlotDefinitions();

/**
 * @param {object} raw
 * @returns {Spell}
 */
const asSpellModel = (raw) => (raw instanceof Spell ? raw : new Spell(raw));

/**
 * @param {object[]} relations
 * @returns {object[]}
 */
const withoutExtraSpells = (relations) =>
    (Array.isArray(relations) ? relations : []).filter((item) => !isBreedExtraSpellPivot(item?.pivot));

const localRelations = ref(withoutExtraSpells(props.relations));

const pivotDefaults = () => ({
    character_level: "1",
    slot_index: "1",
    choice_order: "0",
});

/** @type {Record<number, Record<string, string>>} */
const pivotValues = ref({});

/** Valeur EntityPicker par emplacement (reset après ajout). */
const slotPickerValues = ref({});

const initializePivotValues = () => {
    const pivots = {};
    for (const item of withoutExtraSpells(props.relations)) {
        pivots[item.id] = { ...pivotDefaults() };
        const p = item.pivot || {};
        if (p.character_level != null && p.character_level !== "") {
            pivots[item.id].character_level = String(p.character_level);
        }
        if (p.slot_index != null && p.slot_index !== "") {
            pivots[item.id].slot_index = String(p.slot_index);
        }
        if (p.choice_order != null && p.choice_order !== "") {
            pivots[item.id].choice_order = String(p.choice_order);
        }
    }
    pivotValues.value = pivots;
};

initializePivotValues();

watch(
    () => props.relations,
    (next) => {
        localRelations.value = withoutExtraSpells(next);
        initializePivotValues();
    },
    { deep: true }
);

const slotKey = (def) => `${def.character_level}|${def.slot_index}`;

const spellsInSlot = (def) => {
    return localRelations.value.filter((item) => {
        const pv = pivotValues.value[item.id];
        if (!pv) return false;
        return (
            Number(pv.character_level) === def.character_level &&
            Number(pv.slot_index) === def.slot_index
        );
    });
};

/** Sorts d’un emplacement triés par ordre de choix puis nom (affichage type « un coup d’œil »). */
const sortedSpellsInSlot = (def) => {
    const list = spellsInSlot(def);
    return [...list].sort((a, b) => {
        const oa = Number(pivotValues.value[a.id]?.choice_order ?? 0);
        const ob = Number(pivotValues.value[b.id]?.choice_order ?? 0);
        if (Number.isFinite(oa) && Number.isFinite(ob) && oa !== ob) {
            return oa - ob;
        }
        return String(a.name || "").localeCompare(String(b.name || ""));
    });
};

/** Emplacements présents en données mais hors grille standard */
const orphanRelationItems = computed(() =>
    localRelations.value.filter((item) => {
        const pv = pivotValues.value[item.id];
        if (!pv) return false;
        return !isStandardSlot(Number(pv.character_level), Number(pv.slot_index), standardSlots);
    })
);

const nextChoiceOrder = (def) => {
    const inSlot = spellsInSlot(def);
    let max = -1;
    for (const s of inSlot) {
        const o = Number(pivotValues.value[s.id]?.choice_order ?? 0);
        if (Number.isFinite(o)) max = Math.max(max, o);
    }
    return max + 1;
};

const isSpellInSlot = (spellId, def) => {
    return spellsInSlot(def).some((s) => Number(s.id) === Number(spellId));
};

const slotBlacklist = (def) => spellsInSlot(def).map((s) => s.id);

const canAddToSlot = (def) => spellsInSlot(def).length < BREED_MAX_SPELLS_PER_VARIANT_SLOT;

const normalizePickedSpell = (raw) => {
    if (!raw || typeof raw !== "object") return null;
    const data = raw._data && typeof raw._data === "object" ? { ...raw._data, ...raw } : raw;
    const id = Number(data.id);
    if (!Number.isFinite(id)) return null;
    return {
        ...data,
        id,
        name: data.name ?? `#${id}`,
    };
};

const addToSlot = (spell, def) => {
    const normalized = normalizePickedSpell(spell);
    if (!normalized) return;
    if (isSpellInSlot(normalized.id, def)) return;
    if (!canAddToSlot(def)) {
        notificationStore.warning(
            `Maximum ${BREED_MAX_SPELLS_PER_VARIANT_SLOT} sorts par emplacement (variantes).`,
            { duration: 3500, placement: "top-right" }
        );
        return;
    }
    const existing = localRelations.value.find((r) => Number(r.id) === normalized.id);
    if (existing) {
        pivotValues.value[normalized.id] = {
            character_level: String(def.character_level),
            slot_index: String(def.slot_index),
            choice_order: String(nextChoiceOrder(def)),
        };
        slotPickerValues.value[slotKey(def)] = null;
        notificationStore.success(`« ${normalized.name || normalized.id} » déplacé vers cet emplacement.`, {
            duration: 2500,
            placement: "top-right",
        });
        return;
    }
    localRelations.value.push(normalized);
    pivotValues.value[normalized.id] = {
        character_level: String(def.character_level),
        slot_index: String(def.slot_index),
        choice_order: String(nextChoiceOrder(def)),
    };
    slotPickerValues.value[slotKey(def)] = null;
    notificationStore.success(`« ${normalized.name || normalized.id} » ajouté à l’emplacement.`, {
        duration: 2500,
        placement: "top-right",
    });
};

const onSlotPickerSelected = (entities, def) => {
    const entity = Array.isArray(entities) ? entities[0] : null;
    if (!entity) return;
    addToSlot(entity, def);
};

const removeItem = (spellId) => {
    localRelations.value = localRelations.value.filter((r) => r.id !== spellId);
    const next = { ...pivotValues.value };
    delete next[spellId];
    pivotValues.value = next;
};

const relationsForm = useForm({ spells: {} });

const hasUnsavedChanges = computed(() => {
    // Reliquats pivot 0/1 encore en base : un enregistrement les détache.
    if ((props.relations || []).some((r) => isBreedExtraSpellPivot(r?.pivot))) {
        return true;
    }
    const orig = withoutExtraSpells(props.relations);
    const origIds = [...orig].map((r) => r.id).sort((a, b) => a - b);
    const curIds = [...localRelations.value].map((r) => r.id).sort((a, b) => a - b);
    if (origIds.length !== curIds.length || origIds.some((id, i) => id !== curIds[i])) {
        return true;
    }
    for (const id of curIds) {
        const origItem = orig.find((r) => r.id === id);
        const pv = pivotValues.value[id] || {};
        const op = origItem?.pivot || {};
        if (
            String(pv.character_level ?? "") !== String(op.character_level ?? "") ||
            String(pv.slot_index ?? "") !== String(op.slot_index ?? "") ||
            String(pv.choice_order ?? "") !== String(op.choice_order ?? "")
        ) {
            return true;
        }
    }
    return false;
});

const save = () => {
    const dataWithPivots = {};
    for (const item of localRelations.value) {
        const pv = pivotValues.value[item.id] || pivotDefaults();
        if (isBreedExtraSpellPivot(pv)) continue;
        dataWithPivots[item.id] = {
            character_level: Number(pv.character_level) || 1,
            slot_index: Number(pv.slot_index) || 1,
            choice_order: Number(pv.choice_order) || 0,
        };
    }
    relationsForm.spells = dataWithPivots;
    relationsForm.patch(route("entities.breeds.updateSpells", { breed: props.entityId }), {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success("Sorts de la classe mis à jour.", {
                duration: 3000,
                placement: "top-right",
            });
        },
        onError: (errors) => {
            notificationStore.error("Erreur lors de la mise à jour des sorts.", {
                duration: 5000,
                placement: "top-center",
            });
            warnDev("[BreedSpellSlotsEditor] erreurs", errors);
        },
    });
};
</script>

<template>
    <Container class="space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
                <h3 class="text-lg font-semibold">Sorts de classe</h3>
                <p class="text-sm text-base-content/70 max-w-3xl mt-1">
                    Emplacements de progression (niveau PJ / index) et variantes. Recherche via le catalogue sorts
                    (api.tables.spells).
                </p>
            </div>
            <Badge color="neutral" size="sm" class="shrink-0">
                {{ localRelations.length }} sort{{ localRelations.length === 1 ? "" : "s" }}
            </Badge>
        </div>

        <div class="breed-spell-slots-edit space-y-3 text-[13px] leading-snug text-base-content/90">
            <div
                v-for="def in standardSlots"
                :key="slotKey(def)"
                class="border-b border-base-300/40 pb-3 last:border-0"
            >
                <div class="flex flex-wrap items-baseline gap-x-1 gap-y-1.5">
                    <span
                        class="text-[11px] tabular-nums text-base-content/55 font-medium shrink-0"
                        :title="def.label"
                    >
                        {{ def.character_level }}/{{ def.slot_index }}
                    </span>
                    <span class="text-base-content/35 shrink-0">·</span>
                    <template v-if="sortedSpellsInSlot(def).length">
                        <template v-for="(s, si) in sortedSpellsInSlot(def)" :key="s.id">
                            <span class="inline-flex flex-wrap items-start gap-x-2 gap-y-1 max-w-full">
                                <span class="min-w-0 flex-1 spell-text-embed">
                                    <SpellViewText :spell="asSpellModel(s)" />
                                </span>
                                <span class="inline-flex items-center gap-0.5 text-[11px] text-base-content/55 shrink-0">
                                    <span class="sr-only">Ordre</span>
                                    <input
                                        v-model="pivotValues[s.id].choice_order"
                                        type="number"
                                        min="0"
                                        max="255"
                                        class="input input-bordered input-xs w-11 px-1 text-center"
                                        :aria-label="`Ordre pour ${s.name || s.id}`"
                                    />
                                </span>
                                <Tooltip content="Retirer de la classe" placement="top">
                                    <button
                                        type="button"
                                        class="btn btn-ghost btn-xs h-6 min-h-0 px-1 text-error shrink-0"
                                        @click="removeItem(s.id)"
                                    >
                                        <Icon source="fa-solid fa-times" size="xs" />
                                    </button>
                                </Tooltip>
                            </span>
                            <span
                                v-if="si < sortedSpellsInSlot(def).length - 1"
                                class="text-base-content/30 select-none"
                            >
                                ,
                            </span>
                        </template>
                    </template>
                    <span v-else class="text-[11px] text-base-content/45 italic">—</span>
                </div>

                <div
                    v-if="canAddToSlot(def)"
                    class="mt-2 ml-0 sm:ml-6 sm:border-l sm:border-base-300/50 sm:pl-3"
                >
                    <p class="text-xs text-base-content/60 mb-1">
                        Ajouter ({{ def.character_level }}/{{ def.slot_index }})
                    </p>
                    <EntityPickerCore
                        :model-value="slotPickerValues[slotKey(def)] ?? null"
                        entity-type="spells"
                        :multiple="false"
                        variant="extended"
                        :blacklist="slotBlacklist(def)"
                        placeholder="Rechercher un sort…"
                        size="sm"
                        @update:model-value="slotPickerValues[slotKey(def)] = $event"
                        @update:selected-entities="onSlotPickerSelected($event, def)"
                    />
                </div>
            </div>
        </div>

        <div
            v-if="orphanRelationItems.length"
            class="border border-warning/35 bg-warning/5 rounded-md p-3 space-y-2 text-[13px]"
        >
            <h4 class="text-sm font-semibold text-warning-content">Autres emplacements</h4>
            <p class="text-xs text-base-content/70">
                Ces groupes ne correspondent pas à la grille 3×niv.1 + niv. impairs. Ajustez les valeurs ou déplacez les
                sorts vers une ligne standard ci-dessus.
            </p>
            <ul class="space-y-2 border-t border-warning/20 pt-2">
                <li
                    v-for="item in orphanRelationItems"
                    :key="`orphan-${item.id}`"
                    class="flex flex-wrap items-start gap-x-2 gap-y-2"
                >
                    <div class="min-w-0 flex-1 max-w-md spell-text-embed">
                        <SpellViewText :spell="asSpellModel(item)" />
                    </div>
                    <span class="text-base-content/45 hidden sm:inline">·</span>
                    <label class="text-xs inline-flex items-center gap-1">
                        Nv. PJ
                        <input
                            v-model="pivotValues[item.id].character_level"
                            type="number"
                            min="1"
                            class="input input-bordered input-xs w-12"
                        />
                    </label>
                    <label class="text-xs inline-flex items-center gap-1">
                        Empl.
                        <input
                            v-model="pivotValues[item.id].slot_index"
                            type="number"
                            min="1"
                            class="input input-bordered input-xs w-12"
                        />
                    </label>
                    <label class="text-xs inline-flex items-center gap-1">
                        Ordre
                        <input
                            v-model="pivotValues[item.id].choice_order"
                            type="number"
                            min="0"
                            class="input input-bordered input-xs w-12"
                        />
                    </label>
                    <button type="button" class="btn btn-ghost btn-xs h-6 min-h-0 px-1 text-error" @click="removeItem(item.id)">
                        <Icon source="fa-solid fa-times" size="xs" />
                    </button>
                </li>
            </ul>
        </div>

        <div class="flex justify-end border-t border-base-300 pt-4">
            <EditActionDock
                :processing="relationsForm.processing"
                :disabled="!hasUnsavedChanges"
                :show-secondary="false"
                :secondary-actions="[]"
                :fixed-on-desktop="false"
                @primary="save"
            />
        </div>
    </Container>
</template>
