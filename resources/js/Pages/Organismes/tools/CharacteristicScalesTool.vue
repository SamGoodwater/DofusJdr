<script setup>
/**
 * Outil Barème générique : sélectionne une caractéristique de n'importe quel groupe
 * et affiche CharacteristicGmPanel. Extras sort (dés, budget tour) si pertinent.
 */
import { computed, onMounted, ref, watch } from "vue";
import axios from "axios";
import CharacteristicGmPanel from "@/Pages/Organismes/data-display/CharacteristicGmPanel.vue";
import SpellScaleExtras from "@/Pages/Organismes/tools/SpellScaleExtras.vue";
import { SPELL_SCALE_FAMILIES } from "@/Composables/tools/useSpellReferenceScales.js";

const props = defineProps({
    compact: { type: Boolean, default: false },
    initialGroup: { type: String, default: "all" },
    initialKey: { type: String, default: "" },
});

const GROUPS = [
    { id: "all", label: "Tous" },
    { id: "creature", label: "Créature" },
    { id: "object", label: "Objet" },
    { id: "spell", label: "Sort" },
    { id: "consumable", label: "Consommable" },
    { id: "profile", label: "Profil" },
];

const group = ref(props.initialGroup || "all");
const search = ref("");
const items = ref([]);
const loadingCatalog = ref(false);
const catalogError = ref("");
const selectedKey = ref(props.initialKey || "");
const selectedGroup = ref("");

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
            // Conserve la sélection si elle existe encore dans un autre filtre.
        } else if (!selectedKey.value && items.value.length) {
            selectedKey.value = items.value[0].key;
            selectedGroup.value = items.value[0].group || "";
        } else if (selectedKey.value) {
            const match = items.value.find((i) => i.key === selectedKey.value);
            if (match) selectedGroup.value = match.group || selectedGroup.value;
        }
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
}

watch(group, () => {
    void loadCatalog();
});

onMounted(() => {
    void loadCatalog();
});
</script>

<template>
    <div class="characteristic-scales-tool space-y-4" :class="{ 'text-sm': compact }">
        <div class="alert alert-warning py-2 text-sm leading-snug" role="note">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true" />
            <span>
                Ces valeurs sont des <strong>repères indicatifs</strong>, pas une règle absolue.
            </span>
        </div>

        <div class="flex flex-wrap items-end gap-3">
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
        <div v-else class="grid gap-4 lg:grid-cols-[16rem_minmax(0,1fr)]">
            <div class="max-h-[28rem] overflow-y-auto rounded-box border border-base-content/15">
                <button
                    v-for="item in filteredItems"
                    :key="`${item.group}|${item.key}`"
                    type="button"
                    class="flex w-full items-center gap-2 border-b border-base-content/10 px-2.5 py-2 text-left text-sm hover:bg-base-200/60"
                    :class="{ 'bg-primary/10 font-semibold': item.key === selectedKey }"
                    @click="selectItem(item)"
                >
                    <span class="min-w-0 flex-1 truncate">{{ item.name }}</span>
                    <span class="shrink-0 text-[10px] uppercase text-base-content/50">{{ item.group }}</span>
                </button>
                <p v-if="!filteredItems.length" class="p-3 text-xs text-base-content/60">
                    Aucune caractéristique avec normes dans ce filtre.
                </p>
            </div>

            <div class="min-w-0 space-y-4">
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
