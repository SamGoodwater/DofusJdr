<script setup>
/**
 * EntitySurfaceHost — pile globale d’affichage / édition d’entités en modal.
 *
 * @description
 * Monté dans le layout Main. Affichage via {@link EntityModal} ; édition via
 * chargement Inertia de la page `Edit` embarquée (`entityEditEmbed`).
 */
import { computed, defineAsyncComponent, provide, reactive, shallowRef, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityModal from "@/Pages/Organismes/entity/EntityModal.vue";
import EntityEditEmbedBridge from "@/Pages/Organismes/entity/EntityEditEmbedBridge.vue";
import { useEntitySurfaceStore } from "@/Composables/store/useEntitySurfaceStore";
import { ENTITY_EDIT_EMBED_KEY } from "@/Composables/entity/useEntityEditEmbed";
import { resolveEntityEditHref } from "@/Utils/entity/resolveEntityEditHref";
import { fetchInertiaPage } from "@/Utils/entity/fetchInertiaPage";
import { loadInertiaPageComponent } from "@/Utils/entity/loadInertiaPageComponent";

const store = useEntitySurfaceStore();
const page = usePage();

const stack = computed(() => store.stack);

/** Composants Edit résolus par id de surface (éviter re-fetch au re-render). */
const editComponentById = shallowRef(/** @type {Record<string, object>} */ ({}));

/**
 * @param {string} surfaceId
 */
function embedApiFor(surfaceId) {
    return {
        embeddedInModal: true,
        onCancel: () => store.closeById(surfaceId),
        onSaved: () => store.closeById(surfaceId),
        onExpand: () => {
            const entry = store.stack.find((s) => s.id === surfaceId);
            if (!entry) {
                return;
            }
            while (store.top && store.top.id !== surfaceId) {
                store.closeTop();
            }
            store.expandTopEditToPage();
        },
    };
}

const embedState = reactive({
    embeddedInModal: false,
    onCancel: () => {},
    onSaved: () => {},
    onExpand: () => {},
});

watch(
    () => store.top,
    (top) => {
        if (top?.mode === "edit") {
            Object.assign(embedState, embedApiFor(top.id));
        } else {
            embedState.embeddedInModal = false;
            embedState.onCancel = () => {};
            embedState.onSaved = () => {};
            embedState.onExpand = () => {};
        }
    },
    { deep: true, immediate: true },
);

provide(ENTITY_EDIT_EMBED_KEY, embedState);

/**
 * @param {object} entry
 */
async function loadEditForEntry(entry) {
    if (!entry || entry.mode !== "edit") {
        return;
    }
    if (entry.editComponent && editComponentById.value[entry.id]) {
        return;
    }
    const href = resolveEntityEditHref(entry.entityType, entry.entityId);
    if (!href) {
        store.setEditError(entry.id, "Route d’édition introuvable pour cette entité.");
        return;
    }
    try {
        const data = await fetchInertiaPage(href, page.version);
        const component = await loadInertiaPageComponent(data.component);
        editComponentById.value = {
            ...editComponentById.value,
            [entry.id]: defineAsyncComponent(() => Promise.resolve(component)),
        };
        store.setEditPage(entry.id, {
            component: data.component,
            props: data.props || {},
        });
    } catch (e) {
        if (e?.code === "INERTIA_VERSION_MISMATCH") {
            window.location.href = href;
            return;
        }
        store.setEditError(
            entry.id,
            e?.response?.data?.message || e?.message || "Impossible de charger l’éditeur.",
        );
    }
}

watch(
    stack,
    (entries) => {
        for (const entry of entries) {
            if (entry.mode === "edit" && entry.editLoading && !entry.editError) {
                loadEditForEntry(entry);
            }
        }
        const ids = new Set(entries.map((e) => e.id));
        const next = { ...editComponentById.value };
        let changed = false;
        for (const id of Object.keys(next)) {
            if (!ids.has(id)) {
                delete next[id];
                changed = true;
            }
        }
        if (changed) {
            editComponentById.value = next;
        }
    },
    { deep: true, immediate: true },
);

function onViewClose(surfaceId) {
    store.closeById(surfaceId);
}

function onViewEdit(surfaceId) {
    const entry = store.stack.find((s) => s.id === surfaceId);
    if (!entry) {
        return;
    }
    while (store.top && store.top.id !== surfaceId) {
        store.closeTop();
    }
    store.replaceTopWithEdit();
}

function onViewExpand(surfaceId) {
    store.closeById(surfaceId);
}

function entityLabel(entry) {
    const e = entry?.entity;
    if (!e) {
        return "Entité";
    }
    return e.name || e.title || e._data?.name || e._data?.title || "Entité";
}
</script>

<template>
    <template v-for="(entry, index) in stack" :key="entry.id">
        <!-- Affichage -->
        <EntityModal
            v-if="entry.mode === 'view' && entry.entity"
            :entity="entry.entity"
            :entity-type="entry.entityType"
            view="full"
            :open="true"
            :use-stored-format="false"
            :table-meta="entry.tableMeta"
            @close="onViewClose(entry.id)"
            @edit="onViewEdit(entry.id)"
            @expand="onViewExpand(entry.id)"
        />

        <!-- Édition -->
        <Modal
            v-else-if="entry.mode === 'edit'"
            :open="true"
            size="full"
            placement="middle-center"
            close-on-esc
            :close-on-outside-click="false"
            :draggable="false"
            :style="{ zIndex: 80 + index }"
            @close="store.closeById(entry.id)"
        >
            <template #header>
                <div class="flex flex-wrap items-center justify-between gap-2 pr-8">
                    <h3 class="text-xl font-bold text-primary-100">
                        Modifier
                        <span class="text-base font-semibold text-base-content/80">
                            : {{ entityLabel(entry) }}
                        </span>
                    </h3>
                    <div class="flex items-center gap-2">
                        <Btn
                            color="primary"
                            variant="ghost"
                            size="sm"
                            type="button"
                            @click="embedApiFor(entry.id).onExpand()"
                        >
                            <i class="fa-solid fa-expand mr-1" aria-hidden="true" />
                            Agrandir
                        </Btn>
                        <Btn
                            color="neutral"
                            variant="ghost"
                            size="sm"
                            type="button"
                            @click="store.closeById(entry.id)"
                        >
                            Fermer
                        </Btn>
                    </div>
                </div>
            </template>

            <div class="max-h-[min(85vh,920px)] overflow-y-auto pr-1 pb-4 md:pb-6">
                <div
                    v-if="entry.editLoading"
                    class="py-12 text-center text-sm text-base-content/70"
                >
                    Chargement de l’éditeur…
                </div>
                <div
                    v-else-if="entry.editError"
                    class="rounded-lg border border-error/40 bg-error/10 px-4 py-3 text-sm text-error"
                >
                    {{ entry.editError }}
                </div>
                <EntityEditEmbedBridge
                    v-else-if="editComponentById[entry.id] && entry.editProps"
                    :on-cancel="embedApiFor(entry.id).onCancel"
                    :on-saved="embedApiFor(entry.id).onSaved"
                    :on-expand="embedApiFor(entry.id).onExpand"
                >
                    <component
                        :is="editComponentById[entry.id]"
                        v-bind="entry.editProps"
                        :embedded-in-modal="true"
                    />
                </EntityEditEmbedBridge>
            </div>
        </Modal>
    </template>
</template>
