/**
 * Entity View UI helpers
 *
 * @description
 * Helpers partagés pour les vues d'entités (Large/Compact/Minimal) afin de :
 * - Centraliser la logique de tooltip (helper) à partir des descriptors
 * - Fournir des libellés courts (ex: "nvx") quand pertinent
 * - Décider si un champ peut s'afficher sans libellé dans les metas (ex: type)
 *
 * @example
 * import { getEntityFieldTooltip, getEntityFieldShortLabel } from "@/Utils/Entity/entity-view-ui";
 * const tooltip = getEntityFieldTooltip(descriptors.value?.level);
 * const label = getEntityFieldShortLabel("level", "Niveau"); // "nvx"
 */

import { getByDbColumnMap, getMonsterFieldMeta } from '@/Composables/store/useCharacteristicsStore';

/**
 * Retourne le tooltip le plus pertinent pour un champ.
 *
 * @param {Object} desc - Descriptor du champ (getXFieldDescriptors()[key])
 * @returns {string}
 */
export function getEntityFieldTooltip(desc) {
  if (!desc || typeof desc !== "object") return "";
  const candidates = [
    desc.display?.tooltip,
    desc.table?.header?.tooltip,
    desc.general?.tooltip,
    desc.general?.helper,
    desc.helper,
    desc.tooltip,
    desc.edition?.form?.help,
    desc.edit?.form?.help,
  ];
  for (const raw of candidates) {
    const text = String(raw ?? "").trim();
    if (text) return text;
  }
  return "";
}

/**
 * Libellé de limites min/max d’une caractéristique (valeurs numériques figées).
 *
 * @param {object|null|undefined} def
 * @returns {string}
 */
export function formatCharacteristicLimitLine(def) {
  if (!def || typeof def !== "object") return "";
  const min = def.limit_min ?? def.min;
  const max = def.limit_max ?? def.max;
  const hasMin = min !== null && typeof min !== "undefined" && String(min).trim() !== "";
  const hasMax = max !== null && typeof max !== "undefined" && String(max).trim() !== "";
  if (hasMin && hasMax) {
    return `Limites : ${String(min).trim()} à ${String(max).trim()}.`;
  }
  if (hasMin) return `Minimum : ${String(min).trim()}.`;
  if (hasMax) return `Maximum : ${String(max).trim()}.`;
  return "";
}

/**
 * Retourne un libellé "court" pour l'affichage compact/minimal.
 *
 * @param {string} fieldKey
 * @param {string} label - Libellé standard (souvent desc.general.label)
 * @returns {string}
 */
export function getEntityFieldShortLabel(fieldKey, label) {
  const key = String(fieldKey || "");
  const fallback = String(label || key);
  const map = {
    level: "nvx",
  };
  return map[key] || fallback;
}

/**
 * Champs dont la valeur est généralement compréhensible sans afficher le nom de propriété
 * dans une zone "meta" (ex: type).
 *
 * @param {string} fieldKey
 * @returns {boolean}
 */
export function shouldOmitLabelInMeta(fieldKey) {
  const key = String(fieldKey || "");
  return [
    "resource_type",
    "item_type",
    "monster_race",
    "spell_types",
    "category",
    "element",
  ].includes(key);
}

/**
 * Clés de champs "type/catégorie/race" : couleur dérivée du contenu (getTailwindTokenFromLabel)
 * pour un design cohérent et une identification visuelle rapide.
 */
export const TYPE_LIKE_FIELD_KEYS = Object.freeze([
  "resource_type",
  "item_type",
  "consumable_type",
  "monster_race",
  "category",
  "spell_category",
  "spell_types",
  "capability_type",
  "panoply_type",
  "element",
]);

/**
 * Mapping des aliases de groupes de caractéristiques (store ou tableMeta).
 * Permet de gérer les différences de nommage entre entité UI et payload API.
 *
 * @param {string} entityType
 * @returns {string[]}
 */
export function getCharacteristicsEntityAliases(entityType) {
  const key = String(entityType || "").toLowerCase();
  const aliases = {
    item: ["item", "object"],
    spell: ["spell"],
    capability: ["capability", "spell"],
    monster: ["creature", "monster"],
    resource: ["resource", "object"],
    consumable: ["consumable", "object"],
    panoply: ["panoply", "object"],
    npc: ["creature", "npc"],
    breed: ["creature", "breed"],
  };
  return aliases[key] || [key];
}

/**
 * Retourne le mapping `byDbColumn` correspondant à l'entité.
 * Priorité: store (Inertia share) puis tableMeta pour compat.
 *
 * @param {Object} [tableMeta]
 * @param {string} entityType
 * @returns {Record<string, any>}
 */
export function getEntityCharacteristicsByDbColumn(tableMeta, entityType) {
  const aliases = getCharacteristicsEntityAliases(entityType);
  let base = {};
  for (const alias of aliases) {
    const byDbColumn = getByDbColumnMap(alias);
    if (byDbColumn && Object.keys(byDbColumn).length > 0) {
      base = { ...byDbColumn };
      break;
    }
  }
  if (Object.keys(base).length === 0) {
    const characteristics = tableMeta?.characteristics || {};
    for (const alias of aliases) {
      const byDbColumn = characteristics?.[alias]?.byDbColumn;
      if (byDbColumn && typeof byDbColumn === "object") {
        base = { ...byDbColumn };
        break;
      }
    }
  }
  const et = String(entityType || "").toLowerCase();
  if (et === "monster" || aliases.includes("monster")) {
    const fromPage = getMonsterFieldMeta();
    const fromMeta = tableMeta?.characteristics?.creature?.byMonsterField;
    const monsterExtras = {
      ...(fromPage && typeof fromPage === "object" ? fromPage : {}),
      ...(fromMeta && typeof fromMeta === "object" ? fromMeta : {}),
    };
    if (Object.keys(monsterExtras).length > 0) {
      return { ...base, ...monsterExtras };
    }
  }
  return base;
}

/**
 * Résout les métadonnées UI d'un champ en priorisant:
 * 1) caractéristiques store (Inertia share)
 * 2) descriptors frontend
 * 3) fallback brut
 *
 * @param {Object} options
 * @param {string} options.fieldKey
 * @param {Object} options.descriptors
 * @param {Object} options.tableMeta
 * @param {string} options.entityType
 * @returns {{label:string, shortLabel:string, icon:string, tooltip:string, color:string, characteristic:any}}
 */
export function resolveEntityFieldUi(options = {}) {
  const fieldKey = String(options.fieldKey || "");
  const descriptors = options.descriptors || {};
  const tableMeta = options.tableMeta || {};
  const entityType = String(options.entityType || "");

  const desc = descriptors?.[fieldKey] || {};
  const byDbColumn = getEntityCharacteristicsByDbColumn(tableMeta, entityType);
  const characteristic = byDbColumn?.[fieldKey] || null;

  const descriptorLabel = String(
    desc?.general?.label || desc?.label || fieldKey
  );
  const characteristicLabel = String(
    characteristic?.short_name || characteristic?.name || ""
  );
  const label = characteristicLabel || descriptorLabel;

  const descriptorTooltip = getEntityFieldTooltip(desc);
  const characteristicTooltip = String(
    characteristic?.helper ||
      (Array.isArray(characteristic?.descriptions)
        ? characteristic.descriptions.join(" ")
        : characteristic?.descriptions || "") ||
      ""
  ).trim();
  const limitLine = formatCharacteristicLimitLine(characteristic);
  const tooltip = [characteristicTooltip || descriptorTooltip, limitLine]
    .filter(Boolean)
    .join(" ")
    .trim();

  const iconResolved =
    characteristic?.icon ?? desc?.general?.icon ?? desc?.icon;
  const icon =
    iconResolved === undefined || iconResolved === null
      ? "fa-solid fa-info-circle"
      : String(iconResolved);
  const color = String(characteristic?.color || "");

  return {
    label,
    shortLabel: getEntityFieldShortLabel(fieldKey, label),
    icon,
    tooltip,
    color,
    characteristic,
  };
}

/**
 * Indique si une cellule tableau a du contenu affichable (`CellRenderer`).
 * Les cellules `chips` ont souvent `value === ''` (données dans `params.items`).
 *
 * @param {object|null|undefined} cell
 * @returns {boolean}
 */
export function cellHasRenderableContent(cell) {
  if (!cell || typeof cell !== "object") return false;
  const t = String(cell.type || "");
  if (t === "chips") {
    const items = cell.params?.items;
    return Array.isArray(items) && items.length > 0;
  }
  const v = cell.value;
  if (v === null || typeof v === "undefined") return false;
  const s = String(v).trim();
  if (s === "" || s === "-" || s === "—") return false;
  return true;
}

/**
 * Détermine le style de badge d'un champ (couleur + auto params).
 * Priorise:
 * 1) couleur de cellule (`cell.params.color`)
 * 2) couleur issue des caractéristiques BDD
 * 3) mapping local fourni par la vue
 *
 * @param {Object} options
 * @param {string} options.fieldKey
 * @param {Object} options.cell
 * @param {{color?:string}} options.fieldUi
 * @param {Record<string,string>} [options.localColorMap]
 * @returns {{color:string, autoLabel?:string, autoScheme?:string, autoTone?:string}}
 */
export function resolveEntityBadgeUi(options = {}) {
  const fieldKey = String(options.fieldKey || "");
  const cell = options.cell || {};
  const fieldUi = options.fieldUi || {};
  const localColorMap = options.localColorMap || {};

  let color = "neutral";
  if (cell?.params?.color) {
    color = String(cell.params.color);
  } else if (fieldUi?.color) {
    const c = String(fieldUi.color).trim();
    if (c.startsWith("#") || /^[a-z]+-\d+$/.test(c)) {
      color = c;
    } else {
      color = "primary";
    }
  } else if (localColorMap[fieldKey]) {
    color = String(localColorMap[fieldKey]);
  }

  if (fieldKey === "rarity" && cell?.value) {
    return {
      color,
      autoLabel: String(cell.value),
      autoScheme: "rarity",
      autoTone: "mid",
    };
  }
  if (fieldKey === "level" && cell?.value) {
    return {
      color,
      autoLabel: String(cell.value),
      autoScheme: "level",
      autoTone: "mid",
    };
  }

  // Type/catégorie/race : couleur dérivée du contenu (même type = même couleur)
  if (TYPE_LIKE_FIELD_KEYS.includes(fieldKey)) {
    const val = cell?.value;
    const label =
      val == null || val === ""
        ? ""
        : typeof val === "object" && val !== null
          ? String(val?.name ?? val?.label ?? val?.value ?? "").trim()
          : String(val).trim();
    if (label) {
      return {
        color: "auto",
        autoLabel: label,
        autoScheme: "labelHash",
        autoTone: "light",
      };
    }
  }

  return { color };
}

