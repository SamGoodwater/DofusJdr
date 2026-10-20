/**
 * Shell commun des vues Minimal — parcours et actions.
 *
 * @description
 * Centralise whitelist, contexte et handlers d’ouverture (modal vs page)
 * pour toutes les `*ViewMinimal` alignées sur le parcours
 * compact → overlay → modal → page.
 * Afficher (`view` / `quick-view`) ouvre la modal full via EntitySurfaceHost ;
 * Éditer ouvre la modal d’édition (même hôte).
 *
 * @example
 * const { minimalActionsContext, minimalActionWhitelist, createMinimalActionHandler } =
 *   useEntityMinimalShell({ entityTypePlural: 'spells', routeParam: 'spell', showRoute: 'entities.spells.show', editRoute: 'entities.spells.edit', emit });
 */
import {
    MINIMAL_EXPANDED_ACTION_KEYS,
} from "@/Entities/entity-actions-config";
import { useEntitySurfaceStore } from "@/Composables/store/useEntitySurfaceStore";

export const MINIMAL_ACTIONS_CONTEXT = Object.freeze({
    inMinimal: true,
    viewMode: "minimal",
});

/**
 * @param {Object} options
 * @param {string} options.entityTypePlural - ex. `spells`, `monsters`
 * @param {string} options.showRoute - nom de route Ziggy show
 * @param {string} options.editRoute - nom de route Ziggy edit
 * @param {string} options.routeParam - paramètre d’id (ex. `spell`)
 * @param {(event: string, ...args: unknown[]) => void} options.emit
 * @param {() => Object|null} options.getEntity - getter de l’entité affichée
 * @returns {{
 *   minimalActionsContext: typeof MINIMAL_ACTIONS_CONTEXT,
 *   minimalActionWhitelist: readonly string[],
 *   openQuickView: () => void,
 *   handleMinimalAction: (actionKey: string) => void|Promise<void>,
 * }}
 */
export function useEntityMinimalShell(options) {
    const {
        entityTypePlural,
        emit,
        getEntity,
    } = options;

    const minimalActionWhitelist = MINIMAL_EXPANDED_ACTION_KEYS;
    const surface = useEntitySurfaceStore();

    function openQuickView() {
        const entity = getEntity();
        if (!entity?.id) return;
        surface.openView({
            entityType: entityTypePlural,
            entityId: entity.id,
            entity,
        });
        emit("quick-view", entity);
        emit("action", "quick-view", entity);
    }

    async function handleMinimalAction(actionKey) {
        const entity = getEntity();
        const id = entity?.id;
        if (!id) return;

        switch (actionKey) {
            case "view":
            case "quick-view":
                openQuickView();
                break;
            case "edit":
                surface.openEdit({
                    entityType: entityTypePlural,
                    entityId: id,
                    entity,
                });
                emit("edit", entity);
                break;
            case "delete":
                emit("delete", entity);
                break;
            default:
                emit("action", actionKey, entity);
        }
    }

    return {
        minimalActionsContext: MINIMAL_ACTIONS_CONTEXT,
        minimalActionWhitelist,
        openQuickView,
        handleMinimalAction,
    };
}
