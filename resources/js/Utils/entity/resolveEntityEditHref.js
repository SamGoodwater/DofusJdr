/**
 * Résout l’URL relative de la page d’édition d’une entité.
 *
 * @param {string} entityType - Type brut ou pluriel (`spell`, `spells`, …)
 * @param {number|string} entityId
 * @returns {string} href relatif ou chaîne vide
 */
import { normalizeEntityType } from "@/Entities/entity-registry";
import {
    getEntityRouteConfig,
    getEntitySingularRouteKey,
    resolveEntityRouteHref,
} from "@/Composables/entity/entityRouteRegistry";

export function resolveEntityEditHref(entityType, entityId) {
    if (entityId == null || entityId === "") {
        return "";
    }
    const plural = normalizeEntityType(entityType);
    const paramKey = getEntitySingularRouteKey(plural);
    const cfg = getEntityRouteConfig(plural);
    if (cfg?.edit) {
        return resolveEntityRouteHref(plural, "edit", entityId, cfg);
    }
    try {
        return route(`entities.${plural}.edit`, { [paramKey]: entityId }, false);
    } catch (e) {
        console.warn("[resolveEntityEditHref] route introuvable", plural, entityId, e);
        return "";
    }
}
