<script setup>
/**
 * Affichage des variantes de sorts (choix exclusifs par emplacement de progression).
 *
 * @props {Object} breed - Breed ou payload (_data, spells, spell_slots)
 * @props {'text'|'compact'|'full'} density - text = Minimal/Line, compact = modal dense, full = fiche
 * @description
 * Uniquement les emplacements de variantes (pas le pivot 0/1 « toujours disponibles », reliquat Dofus).
 * En densité `full` / `compact` : bascule Cartes (Minimal étendu) / Texte (icône+nom + popover Minimal).
 */
import { computed, markRaw, ref, watch } from "vue";
import SpellViewText from "@/Pages/Molecules/entity/spell/SpellViewText.vue";
import SpellViewMinimal from "@/Pages/Molecules/entity/spell/SpellViewMinimal.vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { Spell } from "@/Models/Entity/Spell";
import { breedVariantGroupTitle, splitBreedSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";

const SPELL_LAYOUT_STORAGE_KEY = "breed_spell_slots_layout";

/** @type {import('vue').Raw<typeof SpellViewMinimal>} */
const spellMinimalComponent = markRaw(SpellViewMinimal);

/**
 * @returns {'cards'|'text'}
 */
function readStoredSpellLayout() {
    try {
        const raw = localStorage.getItem(SPELL_LAYOUT_STORAGE_KEY);
        if (raw === "text" || raw === "cards") return raw;
    } catch {
        /* private mode / SSR */
    }
    return "cards";
}

const props = defineProps({
    breed: {
        type: Object,
        required: true,
    },
    density: {
        type: String,
        default: "full",
        validator: (v) => ["text", "compact", "full"].includes(v),
    },
    characteristicRuntime: {
        type: Object,
        default: null,
    },
    /** Afficher le paragraphe sur le temple et le temps de changement de variante */
    showTempleNote: {
        type: Boolean,
        default: true,
    },
    lightweight: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["open-spell"]);

const rawBreed = computed(() => props.breed?._data ?? props.breed);

const variantGroupsList = computed(() => splitBreedSpellSlotGroups(rawBreed.value).variantGroups);

const hasAny = computed(() => variantGroupsList.value.some((g) => (g.spells || []).length > 0));
const ICON_LIMIT = 12;

/** Bascule Cartes / Texte (fiche full + compact). */
const spellLayout = ref(readStoredSpellLayout());
const showLayoutToggle = computed(() => props.density === "full" || props.density === "compact");
const isTextLayout = computed(() => showLayoutToggle.value && spellLayout.value === "text");

watch(spellLayout, (value) => {
    try {
        localStorage.setItem(SPELL_LAYOUT_STORAGE_KEY, value);
    } catch {
        /* ignore */
    }
});

/**
 * @param {'cards'|'text'} layout
 */
function setSpellLayout(layout) {
    if (layout !== "cards" && layout !== "text") return;
    spellLayout.value = layout;
}

/**
 * @param {object} raw
 * @returns {Spell}
 */
const asSpellModel = (raw) => (raw instanceof Spell ? raw : new Spell(raw));

const allVariantSpells = computed(() => variantGroupsList.value.flatMap((g) => g.spells || []));

const variantPreview = computed(() => allVariantSpells.value.slice(0, ICON_LIMIT).map((s) => asSpellModel(s)));
const hiddenVariantCount = computed(() => Math.max(0, allVariantSpells.value.length - variantPreview.value.length));

const spellName = (spell) => spell?.name || spell?._data?.name || "Sort";
const spellImage = (spell) => spell?.image || spell?._data?.image || "";
const openSpell = (spell) => emit("open-spell", spell);

/**
 * @param {{ character_level: number, slot_index: number, spells?: object[] }} g
 */
const variantTitle = (g) => breedVariantGroupTitle(g);
</script>

<template>
    <div
        v-if="hasAny"
        class="breed-variants-display space-y-4"
        :data-density="density"
        :data-spell-layout="showLayoutToggle ? spellLayout : undefined"
    >
        <div
            v-if="showLayoutToggle"
            class="flex justify-end"
            role="group"
            aria-label="Mode d’affichage des sorts"
        >
            <div class="join">
                <Tooltip content="Cartes détaillées" placement="top">
                    <button
                        type="button"
                        class="btn btn-xs join-item"
                        :class="spellLayout === 'cards' ? 'btn-primary' : 'btn-ghost'"
                        aria-label="Affichage en cartes"
                        :aria-pressed="spellLayout === 'cards'"
                        @click="setSpellLayout('cards')"
                    >
                        <Icon source="fa-solid fa-table-cells-large" size="xs" />
                    </button>
                </Tooltip>
                <Tooltip content="Liste compacte (texte)" placement="top">
                    <button
                        type="button"
                        class="btn btn-xs join-item"
                        :class="spellLayout === 'text' ? 'btn-primary' : 'btn-ghost'"
                        aria-label="Affichage texte compact"
                        :aria-pressed="spellLayout === 'text'"
                        @click="setSpellLayout('text')"
                    >
                        <Icon source="fa-solid fa-align-left" size="xs" />
                    </button>
                </Tooltip>
            </div>
        </div>

        <template v-if="density === 'full'">
            <div class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-3">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Variantes de sorts</h3>
                    <p v-if="showTempleNote" class="text-xs text-primary-400/90 mt-1">
                        À chaque variante, un seul sort est retenu en jeu. Changer une variante se fait dans le temple de
                        la classe : compter 1 h par niveau du sort pour modifier un seul choix de variante.
                    </p>
                </div>
                <div
                    v-for="(group, gIdx) in variantGroupsList"
                    :key="`vg-${group.character_level}-${group.slot_index}-${gIdx}`"
                    class="space-y-2 border-l-2 border-primary-500/50 pl-3"
                >
                    <div class="text-sm font-bold tracking-wide text-primary-100">
                        {{ variantTitle(group) }}
                    </div>
                    <ul
                        v-if="isTextLayout"
                        class="flex list-none flex-wrap gap-x-3 gap-y-1.5 p-0 m-0"
                    >
                        <li
                            v-for="s in group.spells || []"
                            :key="`sg-t-${s.id}`"
                            class="min-w-0 max-w-full"
                        >
                            <EntityViewTextLink
                                :entity="asSpellModel(s)"
                                entity-prop="spell"
                                :minimal-component="spellMinimalComponent"
                                fallback-icon="fa-solid fa-wand-magic-sparkles"
                                ui-color="primary"
                                :show-actions-on-hover="true"
                                hydrate-type="spells"
                                hover-width-class="w-[min(92vw,36rem)] max-w-[36rem]"
                                :characteristic-runtime="characteristicRuntime"
                            />
                        </li>
                    </ul>
                    <ul
                        v-else
                        class="grid grid-cols-[repeat(auto-fill,minmax(22rem,1fr))] gap-3 items-start"
                    >
                        <li
                            v-for="s in group.spells || []"
                            :key="`sg-${s.id}`"
                            class="min-w-0 max-w-[22rem]"
                        >
                            <SpellViewMinimal
                                :spell="asSpellModel(s)"
                                :display-mode="'extended'"
                                :characteristic-runtime="characteristicRuntime"
                                :show-actions="true"
                            />
                        </li>
                    </ul>
                </div>
            </div>
        </template>

        <template v-else-if="density === 'compact'">
            <div class="rounded-box border border-base-300/80 bg-base-200/30 p-2 space-y-2">
                <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">Variantes de sorts</div>
                <div class="grid gap-2 sm:grid-cols-2">
                    <div
                        v-for="(group, gIdx) in variantGroupsList"
                        :key="`vc-${group.character_level}-${group.slot_index}-${gIdx}`"
                        class="rounded-md bg-base-100/60 border border-base-300/50 p-2 min-w-0"
                    >
                        <div class="text-[10px] font-semibold text-primary-300 mb-1.5 truncate">
                            {{ variantTitle(group) }}
                        </div>
                        <ul
                            v-if="isTextLayout"
                            class="flex list-none flex-wrap gap-x-2 gap-y-1 p-0 m-0"
                        >
                            <li
                                v-for="s in group.spells || []"
                                :key="`vc-sp-t-${s.id}`"
                                class="min-w-0"
                            >
                                <EntityViewTextLink
                                    :entity="asSpellModel(s)"
                                    entity-prop="spell"
                                    :minimal-component="spellMinimalComponent"
                                    fallback-icon="fa-solid fa-wand-magic-sparkles"
                                    ui-color="primary"
                                    :show-actions-on-hover="true"
                                    hydrate-type="spells"
                                    hover-width-class="w-[min(92vw,36rem)] max-w-[36rem]"
                                    :characteristic-runtime="characteristicRuntime"
                                />
                            </li>
                        </ul>
                        <ul v-else class="grid grid-cols-1 gap-2">
                            <li v-for="s in group.spells || []" :key="`vc-sp-${s.id}`" class="min-w-0">
                                <SpellViewMinimal
                                    :spell="asSpellModel(s)"
                                    :display-mode="'extended'"
                                    :characteristic-runtime="characteristicRuntime"
                                    :show-actions="true"
                                />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <!-- density === text : Minimal / Line -->
            <div class="space-y-1 text-[11px] leading-snug text-base-content/85">
                <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">Variantes</div>
                <div v-if="lightweight" class="flex flex-wrap items-center gap-1.5">
                    <Tooltip v-for="s in variantPreview" :key="`vt-i-${s.id}`" :content="spellName(s)" placement="top">
                        <button
                            type="button"
                            class="btn btn-ghost btn-xs btn-square"
                            :aria-label="`Ouvrir ${spellName(s)}`"
                            @click.stop="openSpell(s)"
                        >
                            <EntityThumb
                                size="table"
                                class="h-4 w-4"
                                :src="spellImage(s) || ''"
                                :label="spellName(s)"
                            />
                        </button>
                    </Tooltip>
                    <span v-if="hiddenVariantCount > 0" class="text-[10px] text-base-content/60">+{{ hiddenVariantCount }}</span>
                </div>
                <div
                    v-else
                    v-for="(group, gIdx) in variantGroupsList"
                    :key="`vt-${group.character_level}-${group.slot_index}-${gIdx}`"
                    class="flex flex-wrap gap-x-1 gap-y-0.5"
                >
                    <span class="text-base-content/55 shrink-0 font-medium" :title="variantTitle(group)">
                        {{ group.character_level }}/{{ group.slot_index }}
                    </span>
                    <span class="text-base-content/40">·</span>
                    <span class="min-w-0 flex flex-wrap items-center gap-x-1">
                        <template v-for="(s, si) in group.spells || []" :key="`vt-sp-${s.id}`">
                            <span class="spell-text-embed inline-flex min-w-0 max-w-full">
                                <SpellViewText :spell="asSpellModel(s)" />
                            </span>
                            <span v-if="si < (group.spells || []).length - 1" class="text-base-content/35">,</span>
                        </template>
                    </span>
                </div>
            </div>
        </template>
    </div>
</template>
