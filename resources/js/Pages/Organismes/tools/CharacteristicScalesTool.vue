<script setup>
/**
 * Outil Barème générique : sélectionne une caractéristique de n'importe quel groupe
 * et affiche CharacteristicGmPanel. Extras sort (dés, budget tour) si pertinent.
 *
 * Mémorise filtre + caractéristique (localStorage) pour restaurer à la réouverture du modal.
 */
import { computed, nextTick, onMounted, ref, watch } from "vue";
import axios from "axios";
import CharacteristicGmPanel from "@/Pages/Organismes/data-display/CharacteristicGmPanel.vue";
import SpellScaleExtras from "@/Pages/Organismes/tools/SpellScaleExtras.vue";
import { SPELL_SCALE_FAMILIES } from "@/Composables/tools/useSpellReferenceScales.js";

const STORAGE_KEY = "krosmoz-characteristic-scales-selection";

const props = defineProps({
    compact: { type: Boolean, default: false },
    /** Étire la liste gauche jusqu’en bas (modal wide). */
    fillHeight: { type: Boolean, default: false },
    initialGroup: { type: String, default: "all" },
    initialKey: { type: String, default: "" },
    /** Persiste la sélection (désactiver pour une présélection CMS figée). */
    rememberSelection: { type: Boolean, default: true },
});

const GROUPS = [
    { id: "all", label: "Tous" },
    { id: "creature", label: "Créature" },
    { id: "object", label: "Objet" },
    { id: "spell", label: "Sort" },
    { id: "consumable", label: "Consommable" },
    { id: "profile", label: "Profil" },
];

const VALID_FILTER_GROUPS = new Set(GROUPS.map((g) => g.id));

/**
 * @returns {{ filterGroup?: string, key?: string, itemGroup?: string, search?: string }|null}
 */
function readStoredSelection() {
    if (!props.rememberSelection || typeof localStorage === "undefined") {
        return null;
    }
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return null;
        const parsed = JSON.parse(raw);
        return parsed && typeof parsed === "object" ? parsed : null;
    } catch {
        return null;
    }
}

/**
 * @param {{ filterGroup: string, key: string, itemGroup: string, search: string }} payload
 */
function writeStoredSelection(payload) {
    if (!props.rememberSelection || typeof localStorage === "undefined") {
        return;
    }
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
    } catch {
        /* quota / private mode */
    }
}

const stored = readStoredSelection();
const hasExplicitKey = Boolean(String(props.initialKey || "").trim());
/** Présélection CMS (groupe ≠ all) : ne pas écraser par le souvenir du modal Outils. */
const hasForcedGroup = Boolean(props.initialGroup) && props.initialGroup !== "all";
const restoreFromStorage =
    props.rememberSelection && !hasExplicitKey && !hasForcedGroup && Boolean(stored);

const group = ref(
    restoreFromStorage && VALID_FILTER_GROUPS.has(stored.filterGroup)
        ? stored.filterGroup
        : props.initialGroup || "all",
);
const search = ref(
    restoreFromStorage && typeof stored.search === "string" ? stored.search : "",
);
const items = ref([]);
const loadingCatalog = ref(false);
const catalogError = ref("");
const selectedKey = ref(
    hasExplicitKey
        ? String(props.initialKey).trim()
        : restoreFromStorage
          ? String(stored.key || "").trim()
          : "",
);
const selectedGroup = ref(
    restoreFromStorage ? String(stored.itemGroup || "").trim() : "",
);
const listEl = ref(null);

const filteredItems = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return items.value;
    return items.value.filter(
        (item) =>
            String(item.name || "")
                .toLowerCase()
                .includes(q) ||
            String(item.key || "")
                .toLowerCase()
                .includes(q),
    );
});

const selectedItem = computed(() =>
    items.value.find((item) => item.key === selectedKey.value) || null,
);

const effectiveGroup = computed(
    () => selectedGroup.value || selectedItem.value?.group || (group.value !== "all" ? group.value : ""),
);

const showSpellExtras = computed(() =>
    SPELL_SCALE_FAMILIES.some((family) => family.key === selectedKey.value),
);

function persistSelection() {
    writeStoredSelection({
        filterGroup: group.value || "all",
        key: selectedKey.value || "",
        itemGroup: selectedGroup.value || "",
        search: search.value || "",
    });
}

async function scrollSelectedIntoView() {
    await nextTick();
    const root = listEl.value;
    if (!root || !selectedKey.value) return;
    const active = root.querySelector("[data-scales-selected='true']");
    active?.scrollIntoView?.({ block: "nearest" });
}

async function loadCatalog() {
    loadingCatalog.value = true;
    catalogError.value = "";
    try {
        const url = route("api.characteristics.norms-catalog", {
            group: group.value || "all",
            entity: "*",
        });
        const { data } = await axios.get(url);
        items.value = Array.isArray(data?.items) ? data.items : [];
        if (selectedKey.value && !items.value.some((i) => i.key === selectedKey.value)) {
            // Conserve la sélection même si absente du filtre courant (ex. group=all → creature).
        } else if (!selectedKey.value && items.value.length) {
            selectedKey.value = items.value[0].key;
            selectedGroup.value = items.value[0].group || "";
        } else if (selectedKey.value) {
            const match = items.value.find((i) => i.key === selectedKey.value);
            if (match) selectedGroup.value = match.group || selectedGroup.value;
        }
        persistSelection();
        await scrollSelectedIntoView();
    } catch (e) {
        catalogError.value = e?.response?.data?.error || "Impossible de charger le catalogue.";
        items.value = [];
    } finally {
        loadingCatalog.value = false;
    }
}

function selectItem(item) {
    selectedKey.value = item.key;
    selectedGroup.value = item.group || (group.value !== "all" ? group.value : "");
    persistSelection();
}

watch(group, () => {
    void loadCatalog();
});

watch(search, () => {
    persistSelection();
});

onMounted(() => {
    void loadCatalog();
});
</script>

<template>
    <div
        class="characteristic-scales-tool flex min-h-0 flex-col gap-3"
        :class="{
            'text-sm': compact,
            'h-full min-h-[70vh]': fillHeight,
        }"
    >
        <div class="alert alert-warning shrink-0 py-2 text-sm leading-snug" role="note">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true" />
            <span>
                Ces valeurs sont des <strong>repères indicatifs</strong>, pas une règle absolue.
            </span>
        </div>

        <div class="flex shrink-0 flex-wrap items-end gap-3">
            <label class="form-control w-40">
                <span class="label-text text-xs">Groupe</span>
                <select v-model="group" class="select select-bordered select-sm">
                    <option v-for="g in GROUPS" :key="g.id" :value="g.id">{{ g.label }}</option>
                </select>
            </label>
            <label class="form-control min-w-[12rem] flex-1">
                <span class="label-text text-xs">Recherche</span>
                <input
                    v-model="search"
                    type="search"
                    class="input input-bordered input-sm"
                    placeholder="Nom ou clé…"
                />
            </label>
        </div>

        <div v-if="loadingCatalog" class="flex items-center gap-2 text-base-content/70">
            <span class="loading loading-spinner loading-sm" />
            Chargement du catalogue…
        </div>
        <div v-else-if="catalogError" class="alert alert-error text-sm">
            <i class="fa-solid fa-circle-exclamation" />
            <span>{{ catalogError }}</span>
        </div>
        <div
            v-else
            class="grid min-h-0 flex-1 gap-4 lg:grid-cols-[16rem_minmax(0,1fr)]"
            :class="{ 'lg:items-stretch': fillHeight }"
        >
            <div
                ref="listEl"
                class="min-h-0 overflow-y-auto rounded-box border border-base-content/15"
                :class="fillHeight ? 'h-full max-h-none' : 'max-h-[28rem]'"
            >
                <button
                    v-for="item in filteredItems"
                    :key="`${item.group}|${item.key}`"
                    type="button"
                    class="flex w-full items-center gap-2 border-b border-base-content/10 px-2.5 py-2 text-left text-sm hover:bg-base-200/60"
                    :class="{ 'bg-primary/10 font-semibold': item.key === selectedKey }"
                    :data-scales-selected="item.key === selectedKey ? 'true' : undefined"
                    @click="selectItem(item)"
                >
                    <span class="min-w-0 flex-1 truncate">{{ item.name }}</span>
                    <span class="shrink-0 text-[10px] uppercase text-base-content/50">{{ item.group }}</span>
                </button>
                <p v-if="!filteredItems.length" class="p-3 text-xs text-base-content/60">
                    Aucune caractéristique avec normes dans ce filtre.
                </p>
            </div>

            <div class="min-h-0 min-w-0 space-y-4 overflow-y-auto">
                <CharacteristicGmPanel
                    v-if="selectedKey"
                    :characteristic-key="selectedKey"
                    :group="effectiveGroup"
                    entity="*"
                />
                <SpellScaleExtras
                    v-if="showSpellExtras"
                    :characteristic-key="selectedKey"
                />
            </div>
        </div>
    </div>
</template>
