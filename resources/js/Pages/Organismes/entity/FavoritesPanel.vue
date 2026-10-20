<script setup>
/**
 * FavoritesPanel — Liste des favoris (Minimal) + recherche (cards partagées).
 *
 * @description
 * Modal / page `/favoris`. Clic → modal full. Recherche : cards type recherche globale
 * (plus grandes) avec aperçu Minimal au survol.
 */
import { computed, onMounted, ref, shallowRef, watch } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import EntitySearchHitCard from "@/Pages/Molecules/entity/shared/EntitySearchHitCard.vue";
import { useEntitySurfaceStore } from "@/Composables/store/useEntitySurfaceStore";
import {
    FAVORITES_ACCESS_AUTH_REQUIRED_MESSAGE,
    FAVORITES_AUTH_REQUIRED_MESSAGE,
    fetchHydratedFavorites,
    isEntityFavorite,
    toggleEntityFavorite,
    useFavoriteEntityVersion,
} from "@/Composables/entity/useFavoriteEntityIds";
import {
    GLOBAL_SEARCH_TYPE_ORDER,
    useGlobalEntitySearch,
} from "@/Composables/entity/useGlobalEntitySearch";
import {
    entityViewPropName,
    fetchEntityModelById,
    fetchEntityModelsByIds,
    supportsEntityCatalogViews,
} from "@/Composables/entity/useEntityTableFetch";
import { resolveEntityViewComponent } from "@/Utils/entity/resolveEntityViewComponent";
import { resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useUxFeedback } from "@/Composables/utils/useUxFeedback";

const props = defineProps({
    surface: {
        type: String,
        default: "modal",
        validator: (v) => ["modal", "page"].includes(v),
    },
    showOpenPageButton: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["close", "open-page"]);

const { isAuthenticated } = usePermissions();
const { notifySuccess, notifyInfo, notifyError } = useUxFeedback();
const favoriteVersion = useFavoriteEntityVersion();

const loadingFavorites = ref(false);
const favoriteItems = ref([]);
/** @type {import('vue').ShallowRef<Record<string, object>>} key = `${type}:${id}` */
const favoriteEntities = shallowRef({});
/** @type {import('vue').ShallowRef<Record<string, object|null>>} */
const minimalComponents = shallowRef({});
const loadError = ref(null);

const entityModalLoading = ref(false);

const {
    query,
    setQuery,
    groupedResults,
    loading: searchLoading,
    hasResults,
    clearResults,
} = useGlobalEntitySearch({ minQueryLength: 2, debounce: 250, pageSize: 40 });

const TYPE_LABELS = Object.freeze({
    breeds: "Classes",
    specializations: "Spécialisations",
    monsters: "Monstres",
    spells: "Sorts",
    capabilities: "Capacités",
    "creature-traits": "Traits de créature",
    conditions: "États",
    items: "Équipements",
    consumables: "Consommables",
    resources: "Ressources",
    panoplies: "Panoplies",
    campaigns: "Campagnes",
    scenarios: "Scénarios",
    npcs: "PNJ",
    shops: "Boutiques",
    pages: "Pages",
    sections: "Sections",
    "resource-types": "Types de ressource",
    "item-types": "Types d'équipement",
    "consumable-types": "Types de consommable",
    "spell-types": "Types de sort",
    "monster-races": "Races",
});

const favoriteGroups = computed(() => {
    favoriteVersion.value;
    /** @type {Map<string, typeof favoriteItems.value>} */
    const map = new Map();
    for (const item of favoriteItems.value) {
        const type = item.entityType || "unknown";
        if (!map.has(type)) map.set(type, []);
        map.get(type).push(item);
    }
    return GLOBAL_SEARCH_TYPE_ORDER.filter((t) => map.has(t))
        .concat([...map.keys()].filter((t) => !GLOBAL_SEARCH_TYPE_ORDER.includes(t)))
        .map((type) => ({
            entityType: type,
            label: TYPE_LABELS[type] || type,
            items: map.get(type) || [],
        }))
        .filter((g) => g.items.length > 0);
});

const showSearchResults = computed(() => String(query.value || "").trim().length >= 2);

function entityKey(type, id) {
    return `${type}:${id}`;
}

function onSearchInput(value) {
    setQuery(value);
}

async function hydrateFavoriteEntities(items) {
    const byType = {};
    for (const item of items) {
        if (!supportsEntityCatalogViews(item.entityType)) continue;
        byType[item.entityType] ??= [];
        byType[item.entityType].push(item.id);
    }

    const nextEntities = { ...favoriteEntities.value };
    const nextComponents = { ...minimalComponents.value };

    await Promise.all(
        Object.entries(byType).map(async ([type, ids]) => {
            if (!nextComponents[type]) {
                try {
                    nextComponents[type] = await resolveEntityViewComponent(type, "minimal");
                } catch {
                    nextComponents[type] = null;
                }
            }
            try {
                const models = await fetchEntityModelsByIds(type, ids);
                for (const model of models) {
                    const id = model?.id ?? model?._data?.id;
                    if (id == null) continue;
                    nextEntities[entityKey(type, id)] = model;
                }
            } catch {
                /* ignore per-type failures */
            }
        }),
    );

    favoriteEntities.value = nextEntities;
    minimalComponents.value = nextComponents;
}

async function reloadFavorites() {
    if (!isAuthenticated.value) {
        favoriteItems.value = [];
        favoriteEntities.value = {};
        return;
    }
    loadingFavorites.value = true;
    loadError.value = null;
    try {
        const data = await fetchHydratedFavorites();
        favoriteItems.value = data.items || [];
        await hydrateFavoriteEntities(favoriteItems.value);
    } catch {
        loadError.value = "Impossible de charger vos favoris.";
        favoriteItems.value = [];
    } finally {
        loadingFavorites.value = false;
    }
}

async function onToggleFavorite(row) {
    if (!isAuthenticated.value) {
        notifyInfo(FAVORITES_AUTH_REQUIRED_MESSAGE);
        return;
    }
    const type = row.entityType;
    const id = row.id;
    const result = await toggleEntityFavorite(type, id, { authenticated: true });
    if (!result.ok) {
        if (result.reason === "auth") notifyInfo(FAVORITES_AUTH_REQUIRED_MESSAGE);
        else notifyError("Impossible de mettre à jour vos favoris.");
        return;
    }
    notifySuccess(result.favorited ? "Ajouté aux favoris" : "Retiré des favoris");
    if (!result.favorited) {
        favoriteItems.value = favoriteItems.value.filter(
            (it) => !(String(it.entityType) === String(type) && String(it.id) === String(id)),
        );
        const next = { ...favoriteEntities.value };
        delete next[entityKey(type, id)];
        favoriteEntities.value = next;
    } else {
        const exists = favoriteItems.value.some(
            (it) => String(it.entityType) === String(type) && String(it.id) === String(id),
        );
        if (!exists) {
            favoriteItems.value = [{ ...row }, ...favoriteItems.value];
        }
        try {
            const model = await fetchEntityModelById(type, id);
            if (model) {
                favoriteEntities.value = {
                    ...favoriteEntities.value,
                    [entityKey(type, id)]: model,
                };
            }
            if (!minimalComponents.value[type]) {
                minimalComponents.value = {
                    ...minimalComponents.value,
                    [type]: await resolveEntityViewComponent(type, "minimal"),
                };
            }
        } catch {
            /* keep hit-only */
        }
    }
}

const entitySurface = useEntitySurfaceStore();

async function openEntityModal(payload) {
    const result = payload?.result || payload;
    const type = result?.entityType;
    const id = result?.id;
    if (!type || id == null) return;

    if (!supportsEntityCatalogViews(type)) {
        if (result.href) {
            emit("close");
            router.visit(result.href);
        }
        return;
    }

    entityModalLoading.value = true;
    try {
        let entity = payload?.entity || favoriteEntities.value[entityKey(type, id)] || null;
        if (!entity) {
            entity = await fetchEntityModelById(type, id);
        }
        if (!entity) {
            notifyError("Impossible d’ouvrir cette fiche.");
            return;
        }
        entitySurface.openView({
            entityType: type,
            entityId: id,
            entity,
        });
    } catch {
        notifyError("Impossible d’ouvrir cette fiche.");
    } finally {
        entityModalLoading.value = false;
    }
}

function onMinimalQuickView(entity, entityType) {
    openEntityModal({
        result: { entityType, id: entity?.id },
        entity,
    });
}

function openAsPage() {
    emit("open-page");
    emit("close");
    router.visit(route("favorites.index"));
}

function goLogin() {
    emit("close");
    router.visit(route("login"));
}

function isFav(row) {
    favoriteVersion.value;
    return isEntityFavorite(row.entityType, row.id);
}

function minimalBind(type, entity) {
    return { [entityViewPropName(type)]: entity };
}

onMounted(() => {
    reloadFavorites();
});

watch(isAuthenticated, (ok) => {
    if (ok) reloadFavorites();
    else {
        favoriteItems.value = [];
        favoriteEntities.value = {};
        clearResults();
    }
});
</script>

<template>
    <div class="favorites-panel flex min-h-0 flex-col gap-4" data-cy="favorites-panel">
        <header class="flex flex-wrap items-start justify-between gap-2">
            <div class="min-w-0">
                <component
                    :is="surface === 'page' ? 'h1' : 'h2'"
                    class="flex items-center gap-2 text-xl font-bold text-primary-100 sm:text-2xl"
                >
                    <Icon source="fa-heart" pack="solid" size="sm" class="text-primary" alt="" />
                    Mes favoris
                </component>
                <p class="mt-1 text-sm text-base-content/70">
                    Retrouvez vos fiches préférées, classées par type.
                </p>
                <button
                    v-if="isAuthenticated && showOpenPageButton && surface === 'modal'"
                    type="button"
                    class="mt-1 text-xs text-base-content/40 transition-colors duration-200 hover:text-base-content/85 hover:underline"
                    @click="openAsPage"
                >
                    Ouvrir en page
                </button>
            </div>
        </header>

        <div
            v-if="!isAuthenticated"
            class="rounded-box border border-primary/30 bg-primary/10 p-4 text-sm text-primary-100"
            role="status"
        >
            <p>{{ FAVORITES_ACCESS_AUTH_REQUIRED_MESSAGE }}</p>
            <Btn class="mt-3" size="sm" color="primary" variant="glass" @click="goLogin">
                Se connecter
            </Btn>
        </div>

        <template v-else>
            <div class="rounded-box border-glass-sm bg-base-200/40 p-2 bd-blur-sm">
                <InputField
                    :model-value="query"
                    type="search"
                    placeholder="Rechercher une fiche…"
                    :input-style="{ variant: 'glass', size: 'sm', color: 'neutral' }"
                    @update:model-value="onSearchInput"
                >
                    <template #labelInEnd>
                        <Icon source="fa-magnifying-glass" pack="solid" size="sm" class="opacity-40" alt="" />
                    </template>
                </InputField>
                <p class="mt-1.5 px-1 text-[11px] text-base-content/55">
                    Les favoris apparaissent en premier. Survolez un résultat pour l’aperçu ; cliquez pour la fiche.
                </p>
            </div>

            <p v-if="entityModalLoading" class="text-xs text-base-content/50">Ouverture de la fiche…</p>

            <div v-if="showSearchResults" class="min-h-0 flex-1 space-y-3 overflow-y-auto">
                <p v-if="searchLoading" class="text-sm text-base-content/60">Recherche…</p>
                <p v-else-if="!hasResults" class="text-sm text-base-content/60">Aucun résultat.</p>
                <section
                    v-for="group in groupedResults"
                    :key="group.entityType"
                    class="space-y-1"
                >
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300/90">
                        {{ TYPE_LABELS[group.entityType] || group.group || group.entityType }}
                    </h3>
                    <ul class="space-y-1">
                        <li
                            v-for="row in group.items"
                            :key="`${row.entityType}:${row.id}`"
                        >
                            <EntitySearchHitCard
                                :result="row"
                                density="comfortable"
                                :show-favorite-toggle="true"
                                :is-favorite="isFav(row)"
                                :preview-on-hover="true"
                                @open="openEntityModal"
                                @toggle-favorite="onToggleFavorite"
                            />
                        </li>
                    </ul>
                </section>
            </div>

            <div v-else class="min-h-0 flex-1 space-y-4 overflow-y-auto">
                <p v-if="loadingFavorites" class="text-sm text-base-content/60">Chargement…</p>
                <p v-else-if="loadError" class="text-sm text-error">{{ loadError }}</p>
                <p
                    v-else-if="favoriteGroups.length === 0"
                    class="rounded-box border border-base-300/70 bg-base-100/40 p-6 text-center text-sm text-base-content/65"
                >
                    Aucun favori pour le moment. Utilisez la recherche ci-dessus pour en ajouter.
                </p>
                <section
                    v-for="group in favoriteGroups"
                    :key="group.entityType"
                    class="space-y-2"
                >
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300/90">
                        {{ group.label }}
                        <span class="ml-1 font-normal text-base-content/50">({{ group.items.length }})</span>
                    </h3>

                    <div class="grid gap-2 sm:grid-cols-1">
                        <template v-for="row in group.items" :key="`${row.entityType}:${row.id}`">
                            <div
                                v-if="minimalComponents[row.entityType] && favoriteEntities[entityKey(row.entityType, row.id)]"
                                class="relative rounded-box border border-base-300/60 bg-base-100/40 p-1"
                            >
                                <button
                                    type="button"
                                    class="btn btn-ghost btn-square btn-xs absolute right-1 top-1 z-10"
                                    aria-label="Retirer des favoris"
                                    @click.stop="onToggleFavorite(row)"
                                >
                                    <Icon source="fa-heart" pack="solid" size="sm" class="text-primary" alt="" />
                                </button>
                                <component
                                    :is="minimalComponents[row.entityType]"
                                    v-bind="minimalBind(row.entityType, favoriteEntities[entityKey(row.entityType, row.id)])"
                                    :show-actions="false"
                                    display-mode="extended"
                                    @quick-view="(entity) => onMinimalQuickView(entity || favoriteEntities[entityKey(row.entityType, row.id)], row.entityType)"
                                    @action="(key, entity) => (key === 'quick-view' || key === 'view') && onMinimalQuickView(entity, row.entityType)"
                                />
                            </div>
                            <EntitySearchHitCard
                                v-else
                                :result="row"
                                density="comfortable"
                                :show-favorite-toggle="true"
                                :is-favorite="true"
                                :preview-on-hover="supportsEntityCatalogViews(row.entityType)"
                                @open="openEntityModal"
                                @toggle-favorite="onToggleFavorite"
                            />
                        </template>
                    </div>
                </section>
            </div>
        </template>

    </div>
</template>
