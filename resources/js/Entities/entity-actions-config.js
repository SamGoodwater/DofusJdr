/**
 * Configuration des actions disponibles pour chaque type d'entité.
 *
 * @description
 * Définit les actions possibles, leurs permissions, icônes, labels, etc.
 * Centralise la configuration pour faciliter la maintenance et l'extension.
 *
 * @example
 * import { ENTITY_ACTIONS_CONFIG } from '@/Entities/entity-actions-config';
 * const viewAction = ENTITY_ACTIONS_CONFIG.common.view;
 */

import { getEntityDofusDbId } from '@/Utils/dofusdb/buildDofusDbEntityUrl';

/**
 * @typedef {Object} EntityActionConfig
 * @property {string} key - Identifiant unique de l'action
 * @property {string} label - Label affiché à l'utilisateur
 * @property {string} tooltip - Tooltip détaillé (optionnel, utilise label si non fourni)
 * @property {string} icon - Icône Font Awesome (ex: 'fa-solid fa-eye')
 * @property {string|null} permission - Permission requise (ex: 'canView', 'canUpdate', 'canDelete', 'canManage') ou null si toujours disponible
 * @property {boolean} requiresEntity - Si true, nécessite une entité (ne peut pas être utilisé sans entity)
 * @property {string} [color] - Couleur DaisyUI du bouton (ex: 'error' pour delete)
 * @property {string} [variant] - Variant DaisyUI (ghost, outline…) — historique : pouvait porter une couleur
 * @property {string} [group] - Groupe d'actions (pour séparateurs dans le menu)
 * @property {Function} [getLabel] - Fonction pour obtenir le label selon le contexte
 * @property {Function} [getTooltip] - Fonction pour obtenir le tooltip selon le contexte
 * @property {Function} [getIcon] - Fonction pour obtenir l'icône selon le contexte
 * @property {Function} [visibleIf] - Fonction pour déterminer si l'action est visible selon le contexte
 */

/**
 * Matrice centrale des actions selon le contexte d'affichage.
 */
export const SCRAPPABLE_ENTITY_TYPES = Object.freeze([
  "monsters",
  "breeds",
  "resources",
  "items",
  "consumables",
  "panoplies",
  "spells",
]);

/** Types branchés sur une spécialisation de conversion IA. */
export const AI_CONVERTIBLE_ENTITY_TYPES = Object.freeze([
  "monsters",
  "spells",
  "npcs",
  "items",
  "consumables",
]);

/**
 * Ordre d’actions par surface (du plus pertinent au moins).
 * Minimal déployé : état → pin → modal → DofusDB → refresh → favoris → lien → édition page.
 */
export const ENTITY_ACTION_CONTEXT_PRESETS = Object.freeze({
  minimalLine: [
    "state",
    "pin",
    "quick-view",
    "view-dofusdb",
    "refresh",
    "favorite",
    "copy-link",
    "edit",
  ],
  modalDetail: [
    "state",
    "favorite",
    "copy-link",
    "view",
    "view-dofusdb",
    "edit",
    "refresh",
    "delete",
  ],
  pageDetail: [
    "state",
    "favorite",
    "copy-link",
    "view-dofusdb",
    "edit",
    "refresh",
    "delete",
  ],
  /** Page Modifier : Afficher remplace Éditer ; DofusDB reste en tête des outils. */
  editPage: [
    "view",
    "view-dofusdb",
    "refresh",
    "state",
    "favorite",
    "copy-link",
    "delete",
  ],
  tableDropdown: [
    "state",
    "pin",
    "quick-view",
    "edit",
    "view-dofusdb",
    "refresh",
    "favorite",
    "copy-link",
  ],
});

/** Whitelist recommandée pour les cartes minimal (expanded). */
export const MINIMAL_EXPANDED_ACTION_KEYS = ENTITY_ACTION_CONTEXT_PRESETS.minimalLine;

/** Icône action « Voir sur DofusDB » — servi depuis `public/` (évite les 403 /storage). */
export const DOFUSDB_ACTION_ICON = "/images/logos/dofus.png";

export function normalizeActionEntityType(entityType = "") {
  const raw = String(entityType || "").trim();
  const map = {
    resource: "resources",
    item: "items",
    consumable: "consumables",
    spell: "spells",
    monster: "monsters",
    breed: "breeds",
    classe: "breeds",
    class: "breeds",
    panoply: "panoplies",
    condition: "conditions",
    capability: "capabilities",
    specialization: "specializations",
    "creature-trait": "creature-traits",
    creatureTrait: "creature-traits",
    shop: "shops",
    npc: "npcs",
    campaign: "campaigns",
    scenario: "scenarios",
    "resource-type": "resource-types",
    resourceType: "resource-types",
  };
  return map[raw] || raw;
}

export function isScrappableEntityType(entityType) {
  return SCRAPPABLE_ENTITY_TYPES.includes(normalizeActionEntityType(entityType));
}

export function isAiConvertibleEntityType(entityType) {
  return AI_CONVERTIBLE_ENTITY_TYPES.includes(normalizeActionEntityType(entityType));
}

/**
 * Actions communes à toutes les entités.
 */
export const ENTITY_ACTIONS_COMMON = Object.freeze({
  view: {
    key: "view",
    label: "Afficher",
    tooltip: "Afficher",
    icon: "fa-solid fa-eye",
    permission: "canView",
    requiresEntity: true,
    group: "navigation",
    getLabel: (context) => (context?.inModal ? "Agrandir" : "Afficher"),
    getTooltip: (context) => (context?.inModal ? "Agrandir" : "Afficher"),
    getIcon: (context) => (context?.inModal ? "fa-solid fa-expand" : "fa-solid fa-eye"),
    visibleIf: (context) => {
      // Line / Minimal : Afficher = modal (`quick-view`). La page s’ouvre depuis la modal (Agrandir).
      if (context?.inLine || context?.viewMode === "line") return false;
      if (context?.inMinimal || context?.viewMode === "minimal") return false;
      // Sur une page d'édition, on garde l'action "Afficher".
      if (context?.inPage) return context?.pageMode === "edit";
      return true;
    },
  },
  "quick-view": {
    key: "quick-view",
    label: "Afficher",
    tooltip: "Afficher",
    icon: "fa-solid fa-eye",
    permission: "canView",
    requiresEntity: true,
    group: "navigation",
    getLabel: () => "Afficher",
    getTooltip: () => "Afficher",
    visibleIf: (context) => {
      // En modal, `view` devient Agrandir vers la page.
      if (context?.inModal) return false;
      // Sur la page de l'entité, on n'affiche pas "quick-view" (on est déjà sur la page)
      if (context?.inPage) return false;
      return true;
    },
  },
  edit: {
    key: "edit",
    label: "Éditer",
    tooltip: "Éditer",
    icon: "fa-solid fa-pen-to-square",
    permission: "canUpdate",
    requiresEntity: true,
    group: "edition",
    getLabel: (context) => (context?.inModal && context?.modalMode === "edit" ? "Agrandir" : "Éditer"),
    getTooltip: (context) => (context?.inModal && context?.modalMode === "edit" ? "Agrandir" : "Éditer"),
    getIcon: (context) => (context?.inModal && context?.modalMode === "edit" ? "fa-solid fa-expand" : "fa-solid fa-pen-to-square"),
    visibleIf: (context) => {
      // Sur une page d'édition, l'action utile devient "Afficher".
      if (context?.inPage) return context?.pageMode !== "edit";
      return true;
    },
  },
  expand: {
    key: "expand",
    label: "Agrandir",
    tooltip: "Ouvrir dans une page complète",
    icon: "fa-solid fa-expand",
    permission: null, // Hérite de la permission de l'action d'origine
    requiresEntity: true,
    group: "navigation",
    getLabel: (context) => {
      if (context?.modalMode === "view") return "Agrandir";
      if (context?.modalMode === "edit") return "Agrandir";
      return "Agrandir";
    },
    getTooltip: (context) => {
      if (context?.modalMode === "view") return "Ouvrir dans une page complète";
      if (context?.modalMode === "edit") return "Modifier dans une page complète";
      return "Ouvrir dans une page complète";
    },
    visibleIf: (_context) => {
      // Action historique remplacée par `view`.
      return false;
    },
  },
  "copy-link": {
    key: "copy-link",
    label: "Copier",
    tooltip: "Copier l'URL de l'entité dans le presse-papiers",
    icon: "fa-solid fa-link",
    permission: null, // Toujours disponible
    requiresEntity: true,
    group: "tools",
  },
  favorite: {
    key: "favorite",
    label: "Ajouter aux favoris",
    tooltip: "Ajouter cette fiche à vos favoris",
    icon: "fa-regular fa-heart",
    permission: null,
    requiresEntity: true,
    group: "tools",
  },
  state: {
    key: "state",
    label: "État",
    tooltip: "Voir ou modifier l'état de l'entité",
    icon: "fa-solid fa-circle",
    permission: null,
    requiresEntity: true,
    group: "status",
    visibleIf: (_context, entity) => {
      const raw = entity?._data ?? entity;
      return Object.prototype.hasOwnProperty.call(raw || {}, "state");
    },
  },
  pin: {
    key: "pin",
    label: "Épingler",
    tooltip: "Épingler cette fiche (mémorisé sur cet appareil)",
    icon: "fa-solid fa-thumbtack",
    permission: null,
    requiresEntity: true,
    group: "tools",
  },
  "download-pdf": {
    key: "download-pdf",
    label: "Télécharger PDF",
    tooltip: "Télécharger l'entité au format PDF",
    icon: "fa-solid fa-file-pdf",
    permission: null, // Toujours disponible
    requiresEntity: true,
    group: "tools",
    visibleIf: () => false,
  },
  "view-dofusdb": {
    key: "view-dofusdb",
    label: "Ouvrir sur DofusDB",
    tooltip: "Ouvrir la fiche du jeu sur DofusDB",
    icon: DOFUSDB_ACTION_ICON,
    permission: null,
    requiresEntity: true,
    group: "tools",
    visibleIf: (_context, entity) => Boolean(getEntityDofusDbId(entity)),
  },
  refresh: {
    key: "refresh",
    label: "Sources",
    tooltip: "Mettre à jour depuis DofusDB, convertir avec l’IA ou injecter un JSON",
    icon: "fa-solid fa-arrow-rotate-right",
    permission: null,
    requiresEntity: true,
    group: "tools",
    visibleIf: (context) => {
      if (context?.inLine || context?.viewMode === "line") return false;
      return Boolean(
        context?.inModal ||
          context?.inPage ||
          context?.inMinimal ||
          context?.viewMode === "minimal" ||
          context?.preset === "tableDropdown" ||
          (!context?.inModal && !context?.inPage && !context?.inMinimal && !context?.inLine)
      );
    },
  },
  minimize: {
    key: "minimize",
    label: "Minimiser",
    tooltip: "Minimiser le modal (fonctionnalité future)",
    icon: "fa-solid fa-window-minimize",
    permission: null, // Toujours disponible
    requiresEntity: false, // Peut être utilisé sans entité (dans un panel)
    group: "tools",
    note: "Fonctionnalité à implémenter : permet de fermer un modal en gardant l'état, avec raccourci sticky/absolute en bas",
  },
  delete: {
    key: "delete",
    label: "Supprimer",
    tooltip: "Supprimer définitivement l'entité",
    icon: "fa-solid fa-trash",
    permission: "canDelete",
    requiresEntity: true,
    color: "error",
    group: "destructive",
    visibleIf: (context) => Boolean(context?.inModal || context?.inPage),
  },
});

/**
 * Configuration complète des actions par type d'entité.
 * Les entités héritent des actions communes et peuvent en ajouter/surcharger.
 */
export const ENTITY_ACTIONS_CONFIG = Object.freeze({
  common: ENTITY_ACTIONS_COMMON,

  conditions: {},
  
  // Actions spécifiques par entité (exemple pour resource)
  resource: {
    // Actions spécifiques aux ressources (si nécessaire)
  },

  /** Monstres : modal unique DofusDB | conversion IA. */
  monsters: {
    refresh: {
      ...ENTITY_ACTIONS_COMMON.refresh,
    },
  },

  /** Sorts : modal unique DofusDB | conversion IA. */
  spells: {
    refresh: {
      ...ENTITY_ACTIONS_COMMON.refresh,
    },
  },

  /** Breeds (affichées « Classes ») : DofusDB uniquement. */
  breeds: {
    refresh: {
      ...ENTITY_ACTIONS_COMMON.refresh,
    },
  },

  /** Panoplies : DofusDB uniquement. */
  panoplies: {
    refresh: {
      ...ENTITY_ACTIONS_COMMON.refresh,
    },
  },

  /** Items : modal unique DofusDB | conversion IA. */
  items: {
    refresh: {
      ...ENTITY_ACTIONS_COMMON.refresh,
    },
  },

  /** Ressources : DofusDB uniquement. */
  resources: {
    refresh: {
      ...ENTITY_ACTIONS_COMMON.refresh,
    },
  },

  /** Consommables : modal unique DofusDB | conversion IA. */
  consumables: {
    refresh: {
      ...ENTITY_ACTIONS_COMMON.refresh,
    },
  },
});

/**
 * Ordre d'affichage recommandé des groupes d'actions.
 */
export const ACTION_GROUPS_ORDER = Object.freeze([
  "status",
  "navigation",
  "edition",
  "tools",
  "destructive",
]);

/**
 * Retourne la configuration d'une action spécifique.
 *
 * @param {string} actionKey - Clé de l'action (ex: 'view', 'edit')
 * @param {string} [entityType] - Type d'entité (pour actions spécifiques)
 * @returns {EntityActionConfig|null}
 */
export function getActionConfig(actionKey, entityType = null) {
  const common = ENTITY_ACTIONS_COMMON[actionKey];
  if (common) return common;
  
  if (entityType && ENTITY_ACTIONS_CONFIG[entityType]?.[actionKey]) {
    return ENTITY_ACTIONS_CONFIG[entityType][actionKey];
  }
  
  return null;
}

/**
 * Retourne toutes les actions disponibles pour un type d'entité.
 *
 * @param {string} [entityType] - Type d'entité
 * @returns {Record<string, EntityActionConfig>}
 */
export function getActionsForEntityType(entityType = null) {
  const common = { ...ENTITY_ACTIONS_COMMON };
  
  if (entityType && ENTITY_ACTIONS_CONFIG[entityType]) {
    return { ...common, ...ENTITY_ACTIONS_CONFIG[entityType] };
  }
  
  return common;
}

