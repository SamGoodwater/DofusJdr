/**
 * Store Pinia — pile de surfaces entité (affichage / édition en modal).
 *
 * @description
 * Monté via {@link EntitySurfaceHost} dans le layout Main (persistant).
 * - Minimal / Line : `openView` / `openEdit` empilent.
 * - Modal affichage → Éditer : `replaceTopWithEdit`.
 * - Modal édition → Agrandir : `expandTopEditToPage`.
 */
import { defineStore } from "pinia";
import { router } from "@inertiajs/vue3";
import { normalizeEntityType } from "@/Entities/entity-registry";
import { resolveEntityEditHref } from "@/Utils/entity/resolveEntityEditHref";

let surfaceSeq = 0;

function nextSurfaceId() {
    surfaceSeq += 1;
    return `entity-surface-${surfaceSeq}`;
}

/**
 * @typedef {'view'|'edit'} EntitySurfaceMode
 *
 * @typedef {Object} EntitySurfaceEntry
 * @property {string} id
 * @property {EntitySurfaceMode} mode
 * @property {string} entityType - pluriel normalisé
 * @property {number|string} entityId
 * @property {object|null} entity
 * @property {object} tableMeta
 * @property {boolean} editLoading
 * @property {string} editError
 * @property {string|null} editComponent
 * @property {Record<string, unknown>|null} editProps
 */

export const useEntitySurfaceStore = defineStore("entitySurface", {
    state: () => ({
        /** @type {EntitySurfaceEntry[]} */
        stack: [],
    }),

    getters: {
        /** @returns {EntitySurfaceEntry|null} */
        top(state) {
            return state.stack.length ? state.stack[state.stack.length - 1] : null;
        },
        hasOpen: (state) => state.stack.length > 0,
    },

    actions: {
        /**
         * @param {{ entityType: string, entityId: number|string, entity?: object|null, tableMeta?: object }} payload
         */
        openView(payload) {
            const entityType = normalizeEntityType(payload.entityType);
            const entityId = payload.entityId ?? payload.entity?.id ?? payload.entity?._data?.id;
            if (!entityType || entityId == null || entityId === "") {
                return;
            }
            const top = this.top;
            if (
                top
                && top.mode === "view"
                && top.entityType === entityType
                && String(top.entityId) === String(entityId)
            ) {
                if (payload.entity && !top.entity) {
                    top.entity = payload.entity;
                }
                return;
            }
            /** @type {EntitySurfaceEntry} */
            const entry = {
                id: nextSurfaceId(),
                mode: "view",
                entityType,
                entityId,
                entity: payload.entity ?? null,
                tableMeta: payload.tableMeta || {},
                editLoading: false,
                editError: "",
                editComponent: null,
                editProps: null,
            };
            this.stack.push(entry);
        },

        /**
         * @param {{ entityType: string, entityId: number|string, entity?: object|null, tableMeta?: object }} payload
         */
        openEdit(payload) {
            const entityType = normalizeEntityType(payload.entityType);
            const entityId = payload.entityId ?? payload.entity?.id ?? payload.entity?._data?.id;
            if (!entityType || entityId == null || entityId === "") {
                return;
            }
            const top = this.top;
            if (
                top
                && top.mode === "edit"
                && top.entityType === entityType
                && String(top.entityId) === String(entityId)
            ) {
                return;
            }
            /** @type {EntitySurfaceEntry} */
            const entry = {
                id: nextSurfaceId(),
                mode: "edit",
                entityType,
                entityId,
                entity: payload.entity ?? null,
                tableMeta: payload.tableMeta || {},
                editLoading: true,
                editError: "",
                editComponent: null,
                editProps: null,
            };
            this.stack.push(entry);
        },

        /** Transforme le sommet (affichage) en édition. */
        replaceTopWithEdit() {
            const top = this.top;
            if (!top) {
                return;
            }
            top.mode = "edit";
            top.editLoading = true;
            top.editError = "";
            top.editComponent = null;
            top.editProps = null;
        },

        /**
         * @param {string} surfaceId
         * @param {{ component: string, props: Record<string, unknown> }} page
         */
        setEditPage(surfaceId, page) {
            const entry = this.stack.find((s) => s.id === surfaceId);
            if (!entry) {
                return;
            }
            entry.editComponent = page.component;
            entry.editProps = page.props || {};
            entry.editLoading = false;
            entry.editError = "";
            if (!entry.entity && page.props) {
                const guess =
                    page.props.spell
                    || page.props.capability
                    || page.props.breed
                    || page.props.monster
                    || page.props.item
                    || page.props.resource
                    || page.props.consumable
                    || page.props.npc
                    || page.props.entity
                    || null;
                if (guess) {
                    entry.entity = guess;
                }
            }
        },

        /**
         * @param {string} surfaceId
         * @param {string} message
         */
        setEditError(surfaceId, message) {
            const entry = this.stack.find((s) => s.id === surfaceId);
            if (!entry) {
                return;
            }
            entry.editLoading = false;
            entry.editError = message || "Impossible de charger l’éditeur.";
        },

        closeTop() {
            if (!this.stack.length) {
                return;
            }
            this.stack.pop();
        },

        /**
         * @param {string} surfaceId
         */
        closeById(surfaceId) {
            const idx = this.stack.findIndex((s) => s.id === surfaceId);
            if (idx >= 0) {
                this.stack.splice(idx, 1);
            }
        },

        closeAll() {
            this.stack = [];
        },

        /** Vide la pile et ouvre la page d’édition Inertia. */
        expandTopEditToPage() {
            const top = this.top;
            if (!top) {
                return;
            }
            const href = resolveEntityEditHref(top.entityType, top.entityId);
            this.closeAll();
            if (href) {
                router.visit(href);
            }
        },
    },
});
