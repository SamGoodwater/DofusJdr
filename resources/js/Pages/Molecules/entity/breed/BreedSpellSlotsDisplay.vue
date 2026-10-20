<script setup>
/**
 * Affichage des sorts par emplacement (lecture seule).
 *
 * @props {Object} breed - Instance Breed ou payload brut (spells, spell_slots)
 * @props {'comfortable'|'compact'|'minimal'} density - confortable = fiche large, compact = modal/tableau, minimal = une ligne/slot
 */
import { computed } from "vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import { splitBreedSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";

const props = defineProps({
    breed: {
        type: Object,
        required: true,
    },
    density: {
        type: String,
        default: "comfortable",
        validator: (v) => ["comfortable", "compact", "minimal"].includes(v),
    },
});

/** Emplacements de progression uniquement (pas le pivot 0/1 reliquat Dofus). */
const groups = computed(() => splitBreedSpellSlotGroups(props.breed).variantGroups);
const hasSlots = computed(() => groups.value.length > 0);
</script>

<template>
    <div v-if="hasSlots" class="breed-spell-slots" :data-density="density">
        <template v-if="density === 'comfortable'">
            <div class="space-y-4">
                <div
                    v-for="(group, gIdx) in groups"
                    :key="`slot-${group.character_level}-${group.slot_index}-${gIdx}`"
                    class="space-y-2 border-l-2 border-primary-500/50 pl-3"
                >
                    <div class="text-xs font-semibold text-primary-200">
                        Niveau {{ group.character_level }} · Emplacement {{ group.slot_index }}
                    </div>
                    <ul class="flex flex-wrap gap-x-3 gap-y-1.5">
                        <li v-for="s in group.spells" :key="s.id">
                            <Route
                                :href="route('entities.spells.show', { spell: s.id })"
                                color="neutral"
                                class="text-sm font-medium text-primary-200 hover:text-primary-100 inline-flex items-baseline gap-1"
                            >
                                <span>{{ s.name || `Sort #${s.id}` }}</span>
                                <span v-if="s.level != null" class="text-primary-400 font-normal text-xs">
                                    (nv. {{ s.level }})
                                </span>
                            </Route>
                        </li>
                    </ul>
                </div>
            </div>
        </template>

        <template v-else-if="density === 'compact'">
            <div class="rounded-box border border-base-300/80 bg-base-200/30 p-2 space-y-2">
                <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">
                    Sorts par emplacement
                </div>
                <div class="grid gap-2 sm:grid-cols-2">
                    <div
                        v-for="(group, gIdx) in groups"
                        :key="`c-${group.character_level}-${group.slot_index}-${gIdx}`"
                        class="rounded-md bg-base-100/60 border border-base-300/50 p-2 min-w-0"
                    >
                        <div class="text-[10px] font-semibold text-primary-300 mb-1.5 truncate">
                            Nv. {{ group.character_level }} · Empl. {{ group.slot_index }}
                        </div>
                        <ul class="space-y-1">
                            <li v-for="s in group.spells" :key="s.id" class="min-w-0">
                                <Route
                                    :href="route('entities.spells.show', { spell: s.id })"
                                    color="neutral"
                                    class="text-xs text-base-content hover:text-primary-100 block truncate"
                                    :title="s.name"
                                >
                                    {{ s.name || `#${s.id}` }}
                                    <span v-if="s.level != null" class="text-base-content/50 font-normal">
                                        · {{ s.level }}
                                    </span>
                                </Route>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <!-- minimal : lignes très compactes -->
            <div class="space-y-1 text-[11px] leading-snug text-base-content/85">
                <div
                    v-for="(group, gIdx) in groups"
                    :key="`m-${group.character_level}-${group.slot_index}-${gIdx}`"
                    class="flex flex-wrap gap-x-1 gap-y-0.5"
                >
                    <span class="text-base-content/55 shrink-0 font-medium">
                        {{ group.character_level }}/{{ group.slot_index }}
                    </span>
                    <span class="text-base-content/40">·</span>
                    <span class="min-w-0 flex flex-wrap gap-x-1">
                        <template v-for="(s, si) in group.spells" :key="s.id">
                            <Route
                                :href="route('entities.spells.show', { spell: s.id })"
                                color="neutral"
                                class="truncate max-w-[10rem] hover:underline"
                                :title="s.name"
                            >
                                {{ s.name || `#${s.id}` }}
                            </Route>
                            <span v-if="si < group.spells.length - 1" class="text-base-content/35">,</span>
                        </template>
                    </span>
                </div>
            </div>
        </template>
    </div>
</template>
