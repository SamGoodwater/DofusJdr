/**
 * SharedConstants — Constantes partagées entre plusieurs entités
 *
 * @description
 * Centralise les constantes communes utilisées par plusieurs entités :
 * - Labels traduits (français)
 * - Couleurs et dégradés
 * - Icônes
 * - Options de sélection
 *
 * Ce fichier est la source de vérité unique pour les constantes partagées.
 * Facilement modifiable et accessible depuis n'importe où dans l'application.
 *
 * @example
 * import { FIELD_LABELS, LEVEL_COLORS, RARITY_GRADIENT } from '@/Utils/Entity/SharedConstants';
 * const label = FIELD_LABELS.level; // "Niveau"
 * const color = LEVEL_COLORS[15]; // Couleur pour le niveau 15
 */

/**
 * Textes d’aide courts pour les champs hors tableau `characteristics`
 * (type, rareté…). Les caractéristiques BDD exposent helper + limites à part.
 */
export const FIELD_HELPERS = Object.freeze({
  rarity: 'Rareté, de Commun (0) à Unique (5).',
  resource_type: 'Catégorie métier de la ressource (bois, minerai, plante…).',
  item_type: 'Emplacement (anneau, cape, arme…).',
  consumable_type: 'Catégorie du consommable (potion, nourriture, parchemin…).',
});

/**
 * Labels traduits en français pour les champs communs
 * Utilisé pour les headers de colonnes et les labels de formulaires
 */
export const FIELD_LABELS = Object.freeze({
  // Champs communs
  level: 'Niveau',
  min_level: 'Niveau minimum',
  max_level: 'Niveau maximum',
  rarity: 'Rareté',
  price: 'Prix',
  weight: 'Poids',
  state: 'État',
  progress_state: 'Progression',
  read_level: 'Lecture (min.)',
  write_level: 'Écriture (min.)',
  auto_update: 'Mise à jour automatique',
  dofusdb_id: 'ID DofusDB',
  official_id: 'ID Officiel',
  image: 'Image',
  name: 'Nom',
  description: 'Description',
  resource_type: 'Type de ressource',
  resource_type_id: 'Type de ressource',
  category: 'Catégorie',
  element: 'Élément',
  hostility: 'Hostilité',
});

/**
 * Icônes FontAwesome pour les champs communs
 */
export const FIELD_ICONS = Object.freeze({
  level: 'fa-solid fa-level-up-alt',
  min_level: 'fa-solid fa-level-up-alt',
  max_level: 'fa-solid fa-level-up-alt',
  rarity: 'fa-solid fa-gem',
  price: 'fa-solid fa-coins',
  weight: 'fa-solid fa-weight',
  state: 'fa-solid fa-circle-info',
  progress_state: 'fa-solid fa-list-check',
  read_level: 'fa-solid fa-eye',
  write_level: 'fa-solid fa-pen-to-square',
  auto_update: 'fa-solid fa-sync',
  dofusdb_id: 'fa-solid fa-database',
  official_id: 'fa-solid fa-id-card',
  image: 'fa-solid fa-image',
  name: 'fa-solid fa-font',
  description: 'fa-solid fa-align-left',
  resource_type: 'fa-solid fa-tag',
  resource_type_id: 'fa-solid fa-tag',
  category: 'fa-solid fa-folder',
  element: 'fa-solid fa-fire',
  hostility: 'fa-solid fa-shield-halved',
});

/**
 * Dégradé de couleurs pour les niveaux (1-30)
 * Version renforcée contraste: tons plus sombres pour garder une lecture nette.
 */
export const LEVEL_COLORS = Object.freeze({
  // Niveau 0 : valeur “neutre” (fallback / non défini)
  0: 'slate-700',
  // Niveaux 1-5 : Gris soutenu
  1: 'slate-700',
  2: 'slate-700',
  3: 'slate-800',
  4: 'slate-800',
  5: 'slate-900',
  
  // Niveaux 6-10 : Bleu soutenu
  6: 'blue-700',
  7: 'blue-700',
  8: 'blue-800',
  9: 'blue-800',
  10: 'blue-900',
  
  // Niveaux 11-15 : Vert soutenu
  11: 'emerald-700',
  12: 'emerald-700',
  13: 'emerald-800',
  14: 'emerald-800',
  15: 'emerald-900',
  
  // Niveaux 16-20 : Orange soutenu
  16: 'amber-700',
  17: 'amber-700',
  18: 'amber-800',
  19: 'amber-800',
  20: 'amber-900',
  
  // Niveaux 21-25 : Rouge soutenu
  21: 'red-700',
  22: 'red-700',
  23: 'red-800',
  24: 'red-800',
  25: 'red-900',
  
  // Niveaux 26-30 : Violet / fuchsia foncé
  26: 'purple-700',
  27: 'purple-800',
  28: 'purple-900',
  29: 'fuchsia-800',
  30: 'fuchsia-900',
});

/**
 * Fonction pour obtenir la couleur d'un niveau
 * @param {number} level - Niveau (0-30, au-delà => noir)
 * @returns {string} Token couleur (Tailwind `color-shade` ou DaisyUI)
 */
export function getLevelColor(level) {
  if (typeof level !== 'number' || !Number.isFinite(level)) return 'neutral';

  // En dessous de 0 => clamp à 0 (évite les valeurs aberrantes)
  if (level <= 0) return LEVEL_COLORS[0] || 'neutral';

  // Au-delà de la plage définie => noir
  if (level > 30) return 'black';

  return LEVEL_COLORS[level] || 'neutral';
}

/**
 * Dégradé de couleurs pour la rareté (0-5)
 * Dégradé progressif : Gris → Bleu → Vert → Orange → Rouge → Rose → Violet foncé
 */
export const RARITY_GRADIENT = Object.freeze({
  0: {
    label: 'Commun',
    // Gradient stable (Tailwind token) — indépendant du thème DaisyUI
    color: 'gray-400',
    icon: 'fa-solid fa-circle',
    // Compat / fallback éventuel
    daisyColor: 'neutral',
  },
  1: {
    label: 'Peu commun',
    color: 'sky-400',
    icon: 'fa-solid fa-circle',
    daisyColor: 'info',
  },
  2: {
    label: 'Rare',
    color: 'emerald-500',
    icon: 'fa-solid fa-circle',
    daisyColor: 'success',
  },
  3: {
    label: 'Très rare',
    color: 'amber-500',
    icon: 'fa-solid fa-circle',
    daisyColor: 'warning',
  },
  4: {
    label: 'Légendaire',
    color: 'rose-600',
    icon: 'fa-solid fa-circle',
    daisyColor: 'error',
  },
  5: {
    label: 'Unique',
    color: 'fuchsia-700',
    icon: 'fa-solid fa-star',
    daisyColor: 'primary',
  },
});

/**
 * Fonction pour obtenir les options de rareté
 * Compatible avec l'ancien système RARITY_OPTIONS
 * @returns {Array<{value: number, label: string, color: string, icon: string}>}
 */
export function getRarityOptions() {
  return Object.entries(RARITY_GRADIENT).map(([value, config]) => ({
    value: parseInt(value, 10),
    label: config.label,
    color: config.color,
    icon: config.icon,
    daisyColor: config.daisyColor,
  }));
}

/**
 * Fonction pour obtenir la configuration de rareté pour une valeur
 * @param {number} rarity - Valeur de rareté (0-5)
 * @returns {Object|null} Configuration de rareté ou null
 */
export function getRarityConfig(rarity) {
  const n = typeof rarity === 'number' ? rarity : Number.parseInt(String(rarity ?? ''), 10);
  if (!Number.isFinite(n) || n < 0 || n > 5) {
    return null;
  }
  return RARITY_GRADIENT[n] || null;
}

/**
 * Token Tailwind `color-shade` pour badges de filtre (contraste texte renforcé vs. RARITY_GRADIENT).
 *
 * @param {number|string|null|undefined} rarity
 * @returns {string|null}
 */
export function getRarityFilterBadgeTailwindColor(rarity) {
  const n = typeof rarity === "number" ? rarity : Number.parseInt(String(rarity ?? ""), 10);
  if (!Number.isFinite(n) || n < 0 || n > 5) return null;
  const byValue = {
    0: "slate-600",
    1: "sky-700",
    2: "emerald-700",
    3: "amber-700",
    4: "rose-700",
    5: "fuchsia-800",
  };
  return byValue[n] ?? null;
}

/**
 * Options de rareté (compatibilité avec l'ancien système)
 * @deprecated Utiliser getRarityOptions() à la place
 */
export const RARITY_OPTIONS = getRarityOptions();

/**
 * Rôles utilisateurs avec traductions et couleurs
 * Aligné avec app/Models/User.php
 */
export const USER_ROLES = Object.freeze({
  0: {
    key: 'guest',
    label: 'Invité·e',
    color: 'neutral',
    daisyColor: 'neutral',
    icon: 'fa-solid fa-user',
  },
  1: {
    key: 'user',
    label: 'Utilisateur·ice',
    color: 'info',
    daisyColor: 'info',
    icon: 'fa-solid fa-user',
  },
  2: {
    key: 'player',
    label: 'Joueur·euse',
    color: 'success',
    daisyColor: 'success',
    icon: 'fa-solid fa-dice',
  },
  3: {
    key: 'game_master',
    label: 'Maître de jeu',
    color: 'warning',
    daisyColor: 'warning',
    icon: 'fa-solid fa-crown',
  },
  4: {
    key: 'admin',
    label: 'Administrateur·ice',
    color: 'error',
    daisyColor: 'error',
    icon: 'fa-solid fa-shield-halved',
  },
  5: {
    key: 'super_admin',
    label: 'Super administrateur·ice',
    color: 'primary',
    daisyColor: 'primary',
    icon: 'fa-solid fa-shield',
  },
});

/**
 * Fonction pour obtenir la configuration d'un rôle
 * @param {number|string} role - Valeur du rôle (0-5) ou clé ('guest', 'user', etc.)
 * @returns {Object|null} Configuration du rôle ou null
 */
export function getRoleConfig(role) {
  if (typeof role === 'string') {
    // Chercher par clé
    const entry = Object.entries(USER_ROLES).find(([_, config]) => config.key === role);
    return entry ? USER_ROLES[entry[0]] : null;
  }
  if (typeof role === 'number' && role >= 0 && role <= 5) {
    return USER_ROLES[role] || null;
  }
  return null;
}

/**
 * Fonction pour obtenir le label traduit d'un rôle
 * @param {number|string} role - Valeur du rôle (0-5) ou clé ('guest', 'user', etc.)
 * @returns {string} Label traduit ou 'Inconnu'
 */
export function getRoleLabel(role) {
  const config = getRoleConfig(role);
  return config?.label || 'Inconnu';
}

/**
 * Fonction pour obtenir la couleur DaisyUI d'un rôle
 * @param {number|string} role - Valeur du rôle (0-5) ou clé ('guest', 'user', etc.)
 * @returns {string} Couleur DaisyUI ou 'neutral'
 */
export function getRoleColor(role) {
  const config = getRoleConfig(role);
  return config?.daisyColor || 'neutral';
}

/**
 * Fonction pour obtenir l'icône d'un rôle
 * @param {number|string} role - Valeur du rôle (0-5) ou clé ('guest', 'user', etc.)
 * @returns {string} Icône FontAwesome ou null
 */
export function getRoleIcon(role) {
  const config = getRoleConfig(role);
  return config?.icon || null;
}

/**
 * Créature / Monstre — icônes et libellés pour colonnes résumées (tableau).
 * Utilisé pour afficher CA + résistances, dommages, stats, PA/PM/Ini etc. avec tooltips.
 */
export const CREATURE_ELEMENT_ICONS = Object.freeze({
  neutre: { icon: 'fa-solid fa-circle', label: 'Neutre' },
  terre: { icon: 'fa-solid fa-mountain-sun', label: 'Terre' },
  feu: { icon: 'fa-solid fa-fire', label: 'Feu' },
  air: { icon: 'fa-solid fa-wind', label: 'Air' },
  eau: { icon: 'fa-solid fa-droplet', label: 'Eau' },
});

export const CREATURE_STAT_ICONS = Object.freeze({
  strong: { icon: 'fa-solid fa-dumbbell', label: 'Force' },
  intel: { icon: 'fa-solid fa-brain', label: 'Intelligence' },
  agi: { icon: 'fa-solid fa-wind', label: 'Agilité' },
  chance: { icon: 'fa-solid fa-clover', label: 'Chance' },
  vitality: { icon: 'fa-solid fa-heart-pulse', label: 'Vitalité' },
  sagesse: { icon: 'fa-solid fa-book-open-reader', label: 'Sagesse' },
});

export const CREATURE_COMBAT_ICONS = Object.freeze({
  pa: { icon: 'fa-solid fa-bolt', label: 'Points d\'action' },
  pm: { icon: 'fa-solid fa-shoe-prints', label: 'Points de mouvement' },
  po: { icon: 'fa-solid fa-crosshairs', label: 'Portée' },
  life: { icon: 'fa-solid fa-heart', label: 'Points de vie' },
  ini: { icon: 'fa-solid fa-clock', label: 'Initiative' },
  invocation: { icon: 'fa-solid fa-hand-sparkles', label: 'Invocation' },
  ca: { icon: 'fa-solid fa-shield-halved', label: 'Classe d\'armure' },
  touch: { icon: 'fa-solid fa-hand-back-fist', label: 'Bonus de touche' },
});

/**
 * États d'entité — source de vérité (aligné backend)
 */
export const ENTITY_STATE_OPTIONS = Object.freeze([
  { value: 'raw', label: 'Brut' },
  { value: 'draft', label: 'Brouillon' },
  { value: 'auto', label: 'Auto' },
  { value: 'playable', label: 'Jouable' },
  { value: 'archived', label: 'Archivé' },
]);

export function getEntityStateOptions() {
  return ENTITY_STATE_OPTIONS.map(({ value, label }) => ({ value, label }));
}

/**
 * Classes Tailwind pour un point de couleur d'état (raw/draft/auto/playable/archived).
 * Jetons `--color-state-*` (voir `_app.save.css` / `_theme-states.scss`).
 *
 * @param {string|null|undefined} state
 * @returns {string}
 */
export function getEntityStateDotClass(state) {
  switch (String(state ?? "")) {
    case "playable":
      return "bg-state-playable";
    case "draft":
      return "bg-state-draft";
    case "raw":
      return "bg-state-raw";
    case "auto":
      return "bg-state-auto";
    case "archived":
      return "bg-state-archived";
    default:
      return "bg-base-300";
  }
}

/**
 * Fond des pastilles de filtre d’état (recherche globale, tableaux).
 * Décoché : foncé. Coché : teinte claire de `--color-state-*` (le point reste plus saturé).
 *
 * @param {string|null|undefined} state
 * @param {boolean} selected
 * @returns {string}
 */
export function getEntityStateChipClass(state, selected) {
  if (!selected) {
    return "bg-base-300/55 text-base-content/75 hover:bg-base-300/80";
  }

  switch (String(state ?? "")) {
    case "playable":
      return "bg-state-playable/35 text-base-content";
    case "draft":
      return "bg-state-draft/35 text-base-content";
    case "raw":
      return "bg-state-raw/35 text-base-content";
    case "auto":
      return "bg-state-auto/35 text-base-content";
    case "archived":
      return "bg-state-archived/35 text-base-content";
    default:
      return "bg-base-content/20 text-base-content";
  }
}

/** Couleurs Badge : DaisyUI pour brut/jouable/archivé ; tokens Tailwind pour brouillon (umber) et auto (indigo). */
export const ENTITY_STATE_BADGE_COLORS = Object.freeze({
  raw: "error",
  draft: "umber-500",
  auto: "indigo-500",
  playable: "success",
  archived: "info",
});

/**
 * Couleur badge pour un état d'entité.
 *
 * @param {string|null|undefined} state
 * @returns {string}
 */
export function getEntityStateBadgeColor(state) {
  const k = String(state ?? "");
  return ENTITY_STATE_BADGE_COLORS[k] ?? "neutral";
}

const DAISY_TOOLTIP_COLORS = new Set([
  "",
  "neutral",
  "primary",
  "secondary",
  "accent",
  "info",
  "success",
  "warning",
  "error",
]);

/**
 * Couleur Tooltip DaisyUI (jetons DaisyUI seulement ; umber/indigo → neutre).
 *
 * @param {string|null|undefined} state
 * @returns {string}
 */
export function getEntityStateTooltipColor(state) {
  const c = getEntityStateBadgeColor(state);
  return DAISY_TOOLTIP_COLORS.has(c) ? c : "";
}

/**
 * Libellé français pour la valeur d'état (table des options entité).
 *
 * @param {string|null|undefined} state
 * @returns {string}
 */
export function getEntityStateDisplayLabel(state) {
  if (state == null || state === "") {
    return "—";
  }
  const opts = getEntityStateOptions();
  const hit = opts.find((o) => o.value === state);
  return hit?.label ?? String(state);
}

/**
 * Libellé de l'action d'état (même vocabulaire que l'affichage : Jouable, pas Actif).
 *
 * @param {string|null|undefined} state
 * @returns {string}
 */
export function getEntityStateActionLabel(state) {
  return getEntityStateDisplayLabel(state);
}

/**
 * Options de rôles (0..5) pour selects (read_level / write_level).
 */
export function getUserRoleOptions() {
  return Object.entries(USER_ROLES).map(([value, config]) => ({
    value: Number(value),
    label: config.label,
  }));
}

/**
 * Options d'éléments pour les sorts.
 * @deprecated Utiliser getElementOptions depuis @/Utils/Entity/Elements
 */
export { ELEMENT_OPTIONS as SPELL_ELEMENT_OPTIONS, getElementOptions as getSpellElementOptions } from './Elements.js';

/**
 * Options de catégorie de sort (fallback UX).
 * `color` = token Tailwind `color-shade` (fond saturé + texte lisible via Badge).
 */
export const SPELL_CATEGORY_OPTIONS = Object.freeze([
  { value: 0, label: 'Sort de classe', color: 'sky-600' },
  { value: 1, label: 'Sort de créature', color: 'amber-600' },
  { value: 2, label: 'Sort apprenable', color: 'violet-600' },
  { value: 3, label: 'Sort consommable', color: 'emerald-600' },
]);

export function getSpellCategoryOptions() {
  return SPELL_CATEGORY_OPTIONS.map(({ value, label, color }) => ({ value, label, color }));
}

/**
 * Couleur badge d’une catégorie de sort (0–3).
 *
 * @param {number|string|null|undefined} value
 * @returns {string}
 */
export function getSpellCategoryColor(value) {
  const num = typeof value === 'string' ? parseInt(value, 10) : Number(value);
  const option = SPELL_CATEGORY_OPTIONS.find((opt) => opt.value === num);
  return option?.color || 'neutral';
}
