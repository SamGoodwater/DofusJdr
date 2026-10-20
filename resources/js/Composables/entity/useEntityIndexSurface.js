/**
 * Helpers d’index d’entité pour ouvrir affichage / édition via EntitySurfaceHost.
 *
 * @example
 * const { openFullModal, openEdit } = useEntityIndexSurface('spells', { getTableMeta: () => tableMeta.value });
 */
import { useEntitySurfaceStore } from "@/Composables/store/useEntitySurfaceStore";

/**
 * @param {string} entityType
 * @param {{ getTableMeta?: () => object }} [options]
 */
export function useEntityIndexSurface(entityType, options = {}) {
    const surface = useEntitySurfaceStore();

    function resolveMeta() {
        return typeof options.getTableMeta === "function" ? options.getTableMeta() || {} : {};
    }

    /**
     * @param {object} model
     */
    function openFullModal(model) {
        const id = model?.id ?? model?._data?.id;
        if (id == null) return;
        surface.openView({
            entityType,
            entityId: id,
            entity: model,
            tableMeta: resolveMeta(),
        });
    }

    /**
     * @param {object} model
     */
    function openEdit(model) {
        const id = model?.id ?? model?._data?.id;
        if (id == null) return;
        surface.openEdit({
            entityType,
            entityId: id,
            entity: model,
            tableMeta: resolveMeta(),
        });
    }

    return {
        openFullModal,
        openEdit,
        surface,
    };
}
