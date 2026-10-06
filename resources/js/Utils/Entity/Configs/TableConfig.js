/**
 * TableConfig — Classe pour configurer un tableau d'entités
 *
 * @description
 * Cette classe permet de configurer un tableau avec :
 * - Configuration actions (menu, permissions, visibilité responsive)
 * - Liste des colonnes
 * - Features du tableau (search, filters, pagination, selection, etc.)
 *
 * @example
 * const tableConfig = new TableConfig({
 *   id: "resources.index",
 *   entityType: "resource"
 * })
 *   .withActions({ enabled: true, permission: "view", available: ["view", "edit", "delete"] })
 *   .addColumn(new TableColumnConfig({ key: "name", label: "Nom", type: "route" }))
 *   .build();
 */

import { TableColumnConfig } from "./TableColumnConfig.js";
import { getCurrentScreenSize } from "../Helpers.js";
import { getEntityFieldTooltip } from "../entity-view-ui.js";

/**
 * Fonctions helper internes pour fromDescriptors()
 */

/**
 * Déduit le type de colonne depuis le format
 */
function inferTypeFromFormat(format) {
  if (!format || typeof format !== 'object') {
    return 'text';
  }

  const modes = Object.values(format)
    .map(config => config?.mode || config)
    .filter(Boolean);

  if (modes.length === 0) {
    return 'text';
  }

  const modeCounts = {};
  modes.forEach(mode => {
    modeCounts[mode] = (modeCounts[mode] || 0) + 1;
  });

  const mostCommonMode = Object.keys(modeCounts).reduce((a, b) =>
    modeCounts[a] > modeCounts[b] ? a : b
  );

  const modeToType = {
    'badge': 'badge',
    'text': 'text',
    'route': 'route',
    'routeExternal': 'routeExternal',
    'thumb': 'image',
    'image': 'image',
    'boolIcon': 'badge',
    'boolBadge': 'badge',
    'dateShort': 'date',
    'dateTime': 'date',
    'icon': 'badge',
  };

  return modeToType[mostCommonMode] || 'text';
}

/**
 * Convertit display.sizes (structure actuelle) en format (structure nouvelle)
 */
function convertDisplaySizesToFormat(sizes) {
  if (!sizes || typeof sizes !== 'object') {
    return {};
  }

  const format = {};
  for (const [size, config] of Object.entries(sizes)) {
    if (typeof config === 'object' && config !== null) {
      format[size] = {
        mode: config.mode || 'text',
        ...(config.truncate && { maxLength: config.truncate }),
      };
    } else if (typeof config === 'string') {
      format[size] = { mode: config };
    }
  }

  return format;
}

/**
 * Crée une TableColumnConfig depuis un descriptor
 */
function createColumnFromDescriptor(fieldKey, descriptor, ctx = {}) {
  if (!descriptor) {
    throw new Error(`Descriptor manquant pour ${fieldKey}`);
  }

  // Nouvelle structure : table.cell.sizes, general.label, general.icon, permissions.visibleIf
  // Rétrocompatibilité : display.sizes, label, icon, visibleIf
  const tableConfigDesc = descriptor.table || {};
  const cellConfig = tableConfigDesc.cell || {};
  const displayConfig = descriptor.display || {}; // Rétrocompatibilité
  const general = descriptor.general || {}; // Nouvelle structure
  const permissions = descriptor.permissions || {}; // Nouvelle structure

  let format = tableConfigDesc.format;
  if (!format && cellConfig.sizes) {
    format = convertDisplaySizesToFormat(cellConfig.sizes);
  }
  if (!format && displayConfig.sizes) { // Rétrocompatibilité
    format = convertDisplaySizesToFormat(displayConfig.sizes);
  }

  let type = tableConfigDesc.type;
  if (!type && format) {
    type = inferTypeFromFormat(format);
  }
  if (!type) {
    type = 'text';
  }

  const column = new TableColumnConfig({
    key: fieldKey,
    label: general.label || descriptor.label || fieldKey, // Nouvelle structure puis rétrocompatibilité
    type: type,
    icon: general.icon || descriptor.icon || null, // Nouvelle structure puis rétrocompatibilité
    tooltip: getEntityFieldTooltip(descriptor) || null,
  });

  if (tableConfigDesc.permission) {
    column.withPermission(tableConfigDesc.permission);
  }
  if (tableConfigDesc.order !== undefined) {
    column.withOrder(tableConfigDesc.order);
  }
  if (tableConfigDesc.isMain) {
    column.asMain(true);
  }
  if (tableConfigDesc.sortable) {
    column.withSort(true, tableConfigDesc.sortField || null);
  }
  if (tableConfigDesc.searchable) {
    column.withSearch(true);
  }
  if (tableConfigDesc.filterable) {
    // UI helpers: rendre les options de niveau lisibles et cohérentes (badges colorés)
    // sans obliger chaque entité à dupliquer la config.
    const f = { ...tableConfigDesc.filterable };
    const levelLike =
      fieldKey === "level" ||
      fieldKey === "min_level" ||
      fieldKey === "max_level" ||
      fieldKey === "creature_level" ||
      f?.id === "level" ||
      f?.id === "min_level" ||
      f?.id === "max_level" ||
      f?.id === "creature_level";
    if (levelLike) {
      const ui = (f.ui && typeof f.ui === 'object') ? { ...f.ui } : {};
      const optionBadge = (ui.optionBadge && typeof ui.optionBadge === 'object') ? { ...ui.optionBadge } : {};
      if (typeof optionBadge.enabled === 'undefined') optionBadge.enabled = true;
      if (!optionBadge.color) optionBadge.color = 'auto';
      if (!optionBadge.autoLabelFrom) optionBadge.autoLabelFrom = 'value';
      if (!optionBadge.autoScheme) optionBadge.autoScheme = 'level';
      if (!optionBadge.autoTone) optionBadge.autoTone = 'mid';
      if (!optionBadge.variant) optionBadge.variant = 'soft';
      ui.optionBadge = optionBadge;
      f.ui = ui;
    }

    const rarityLike = fieldKey === 'rarity' || f?.id === 'rarity';
    if (rarityLike) {
      const ui = (f.ui && typeof f.ui === 'object') ? { ...f.ui } : {};
      const optionBadge = (ui.optionBadge && typeof ui.optionBadge === 'object') ? { ...ui.optionBadge } : {};
      if (typeof optionBadge.enabled === 'undefined') optionBadge.enabled = true;
      if (!optionBadge.color) optionBadge.color = 'auto';
      if (!optionBadge.autoLabelFrom) optionBadge.autoLabelFrom = 'label';
      if (!optionBadge.autoScheme) optionBadge.autoScheme = 'rarity';
      if (!optionBadge.autoTone) optionBadge.autoTone = 'dark';
      if (!optionBadge.variant) optionBadge.variant = 'soft';
      ui.optionBadge = optionBadge;
      f.ui = ui;
    }

    const spellTypesFilterLike = fieldKey === 'spell_types' || f?.id === 'types';
    if (spellTypesFilterLike) {
      const ui = f.ui && typeof f.ui === 'object' ? { ...f.ui } : {};
      ui.spellTypeBadge = true;
      ui.optionBadge = { enabled: false };
      f.ui = ui;
    }

    const resourceTypeLike =
      fieldKey === "resource_type" ||
      fieldKey === "item_type" ||
      fieldKey === "consumable_type" ||
      fieldKey === "monster_race" ||
      fieldKey === "element" ||
      fieldKey === "spell_types" ||
      f?.id === "resource_type_id" ||
      f?.id === "item_type_id" ||
      f?.id === "consumable_type_id" ||
      f?.id === "monster_race_id" ||
      f?.id === "types";
    if (resourceTypeLike && !spellTypesFilterLike) {
      const ui = (f.ui && typeof f.ui === 'object') ? { ...f.ui } : {};
      const optionBadge = (ui.optionBadge && typeof ui.optionBadge === 'object') ? { ...ui.optionBadge } : {};
      if (typeof optionBadge.enabled === 'undefined') optionBadge.enabled = true;
      if (!optionBadge.color) optionBadge.color = 'auto';
      if (!optionBadge.autoLabelFrom) optionBadge.autoLabelFrom = 'label';
      if (!optionBadge.autoScheme) optionBadge.autoScheme = 'labelHash';
      if (!optionBadge.autoTone) optionBadge.autoTone = 'light';
      if (!optionBadge.variant) optionBadge.variant = 'soft';
      ui.optionBadge = optionBadge;
      f.ui = ui;
    }

    // Catégories de sort : couleurs DaisyUI portées par les options (pas de pastels labelHash).
    const spellCategoryLike = fieldKey === 'category' || f?.id === 'category';
    if (spellCategoryLike) {
      const ui = (f.ui && typeof f.ui === 'object') ? { ...f.ui } : {};
      const optionBadge = (ui.optionBadge && typeof ui.optionBadge === 'object') ? { ...ui.optionBadge } : {};
      if (typeof optionBadge.enabled === 'undefined') optionBadge.enabled = true;
      // Badge plein (fond coloré + texte *-content) pour le contraste thème sombre.
      optionBadge.variant = '';
      delete optionBadge.color;
      delete optionBadge.autoScheme;
      delete optionBadge.autoTone;
      ui.optionBadge = optionBadge;
      f.ui = ui;
    }

    const stateLike = fieldKey === 'state' || f?.id === 'state';
    if (stateLike) {
      const ui = (f.ui && typeof f.ui === 'object') ? { ...f.ui } : {};
      const optionBadge = (ui.optionBadge && typeof ui.optionBadge === 'object') ? { ...ui.optionBadge } : {};
      if (typeof optionBadge.enabled === 'undefined') optionBadge.enabled = true;
      if (!optionBadge.leadingDot) optionBadge.leadingDot = 'entity-state';
      if (!optionBadge.variant) optionBadge.variant = 'soft';
      ui.optionBadge = optionBadge;
      f.ui = ui;
    }

    column.withFilter(f);
  }
  // Visibilité par défaut depuis table.defaultVisible
  if (tableConfigDesc.defaultVisible) {
    column.withDefaultVisible(tableConfigDesc.defaultVisible);
  }
  if (format && Object.keys(format).length > 0) {
    column.withFormat(format);
  }

  // Permissions : vérifier table.visibleIf en priorité, puis permissions.visibleIf
  const tableVisibleIf = tableConfigDesc.visibleIf;
  const permissionsVisibleIf = permissions.visibleIf || descriptor.visibleIf;
  const visibleIf = tableVisibleIf || permissionsVisibleIf;

  if (visibleIf && typeof visibleIf === "function") {
    // Propager visibleIf pour filtrage runtime (EntityTanStackTable isColumnAllowed)
    column.withVisibleIf(visibleIf);
    const isVisible = visibleIf(ctx);
    if (!isVisible) {
      // Si la fonction retourne false au build, masquer la colonne par défaut
      column.withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false });
    }
  }

  return column;
}

/**
 * Classe TableConfig
 */
export class TableConfig {
  /**
   * @param {Object} base - Propriétés de base
   * @param {string} base.id - ID unique du tableau (obligatoire)
   * @param {string} base.entityType - Type d'entité (obligatoire)
   */
  constructor(base = {}) {
    if (!base.id) throw new Error("TableConfig: 'id' est obligatoire");
    if (!base.entityType) throw new Error("TableConfig: 'entityType' est obligatoire");

    this.id = base.id;
    this.entityType = base.entityType;

    // Configuration actions
    this.actions = {
      enabled: false,
      permission: null,
      available: [],
      defaultVisible: {
        xs: false,
        sm: true,
        md: true,
        lg: true,
        xl: true,
      },
    };

    // Colonnes
    this.columns = [];

    // Features du tableau
    this.features = {
      search: { enabled: true, placeholder: `Rechercher…`, debounceMs: 200 },
      filters: { enabled: true },
      pagination: { enabled: true, perPage: { default: 25, options: [10, 25, 50, 100] } },
      selection: { enabled: true, checkboxMode: "auto", clickToSelect: true },
      columnVisibility: { enabled: true, persist: true },
      export: { csv: false, filename: null },
    };

    // UI
    this.ui = {
      skeletonRows: 10,
    };
  }

  /**
   * Configure le menu actions.
   *
   * @param {Object} config - Configuration actions
   * @param {boolean} config.enabled - Activer le menu
   * @param {string} config.permission - Permission requise
   * @param {string[]} config.available - Actions disponibles
   * @param {Object} [config.defaultVisible] - Visibilité par taille d'écran
   * @returns {TableConfig} Instance pour chaînage
   */
  withActions(config) {
    this.actions = {
      enabled: Boolean(config.enabled),
      permission: config.permission || null,
      available: Array.isArray(config.available) ? config.available : [],
      defaultVisible: {
        ...this.actions.defaultVisible,
        ...(config.defaultVisible || {}),
      },
    };
    return this;
  }

  /**
   * Ajoute une colonne au tableau.
   *
   * @param {TableColumnConfig|Object} column - Colonne à ajouter
   * @returns {TableConfig} Instance pour chaînage
   */
  addColumn(column) {
    if (column instanceof TableColumnConfig) {
      this.columns.push(column);
    } else {
      // Si c'est un objet, créer une instance de TableColumnConfig
      const col = new TableColumnConfig(column);
      this.columns.push(col);
    }
    return this;
  }

  /**
   * Ajoute plusieurs colonnes.
   *
   * @param {Array<TableColumnConfig|Object>} columns - Colonnes à ajouter
   * @returns {TableConfig} Instance pour chaînage
   */
  addColumns(columns) {
    columns.forEach((col) => this.addColumn(col));
    return this;
  }

  /**
   * Configure les features du tableau.
   *
   * @param {Object} features - Configuration des features
   * @returns {TableConfig} Instance pour chaînage
   */
  withFeatures(features) {
    this.features = { ...this.features, ...features };
    return this;
  }

  /**
   * Configure l'UI du tableau.
   *
   * @param {Object} ui - Configuration UI
   * @returns {TableConfig} Instance pour chaînage
   */
  withUI(ui) {
    this.ui = { ...this.ui, ...ui };
    return this;
  }

  /**
   * Obtient les colonnes visibles selon la taille d'écran actuelle.
   *
   * @param {string} [size="auto"] - Taille d'écran
   * @param {Object} [ctx] - Contexte (permissions, etc.)
   * @returns {Array<Object>} Colonnes visibles
   */
  getVisibleColumns(size = "auto", ctx = {}) {
    const actualSize = size === "auto" ? getCurrentScreenSize() : size;
    const can = ctx?.capabilities || ctx?.meta?.capabilities || {};

    return this.columns
      .map((col) => {
        const config = col instanceof TableColumnConfig ? col.build() : col;
        return { config, column: col instanceof TableColumnConfig ? col : null };
      })
      .filter(({ config, column }) => {
        // Vérifier la permission
        if (config.permissions?.ability) {
          if (!can[config.permissions.ability]) return false;
        }

        // Vérifier la visibilité selon la taille
        if (column) {
          return column.isVisible(actualSize);
        }
        return config.defaultVisible?.[actualSize] ?? true;
      })
      .sort((a, b) => {
        // Trier par ordre (si défini)
        const orderA = a.column?.order ?? a.config.order ?? 999;
        const orderB = b.column?.order ?? b.config.order ?? 999;
        return orderA - orderB;
      })
      .map(({ config }) => config);
  }

  /**
   * Retourne l'objet de configuration final.
   *
   * @param {Object} [ctx] - Contexte (permissions, etc.)
   * @returns {Object} Configuration du tableau
   */
  build(ctx = {}) {
    const can = ctx?.capabilities || ctx?.meta?.capabilities || {};

    // Vérifier si actions est activé et si la permission est présente
    const actionsEnabled = this.actions.enabled && (!this.actions.permission || can[this.actions.permission]);
    
    // Construire les colonnes
    const columns = this.columns.map((col) => (col instanceof TableColumnConfig ? col.build() : col));
    
    // Trier les colonnes par ordre
    columns.sort((a, b) => {
      const orderA = a.order ?? 999;
      const orderB = b.order ?? 999;
      return orderA - orderB;
    });
    
    return {
      id: this.id,
      ui: { ...this.ui },
      features: {
        ...this.features,
        selection: {
          ...this.features.selection,
        },
      },
      columns,
      // Métadonnées pour le système
      _metadata: {
        entityType: this.entityType,
        actions: {
          enabled: actionsEnabled,
          permission: this.actions.permission,
          available: this.actions.available,
          defaultVisible: this.actions.defaultVisible,
        },
        // Stocker le contexte complet pour la récupération des descriptors
        context: {
          capabilities: ctx?.capabilities || ctx?.meta?.capabilities || {},
          ...ctx, // Inclure toutes les autres propriétés du contexte (resourceTypes, etc.)
        },
      },
    };
  }

  /**
   * Génère un TableConfig complet depuis les descriptors
   *
   * @static
   * @param {Object} descriptors - Descriptors de l'entité (retournés par getResourceFieldDescriptors, etc.)
   * @param {Object} [ctx] - Contexte (permissions, etc.)
   * @returns {TableConfig} Configuration du tableau
   *
   * @example
   * const descriptors = getResourceFieldDescriptors(ctx);
   * const tableConfig = TableConfig.fromDescriptors(descriptors, ctx);
   */
  static fromDescriptors(descriptors, ctx = {}) {
    if (!descriptors || typeof descriptors !== 'object') {
      throw new Error('Descriptors invalides');
    }

    const tableConfig = descriptors._tableConfig || {};

    if (!tableConfig.id) {
      throw new Error('_tableConfig.id est obligatoire dans les descriptors');
    }
    if (!tableConfig.entityType) {
      throw new Error('_tableConfig.entityType est obligatoire dans les descriptors');
    }

    const config = new TableConfig({
      id: tableConfig.id,
      entityType: tableConfig.entityType,
    });

    if (tableConfig.features) {
      config.withFeatures(tableConfig.features);
    }
    if (tableConfig.actions) {
      const actionsConfig = { ...tableConfig.actions };
      if (tableConfig.actions.access?.button?.defaultVisible) {
        actionsConfig.defaultVisible = tableConfig.actions.access.button.defaultVisible;
      }
      config.withActions(actionsConfig);
    }
    if (tableConfig.ui) {
      config.withUI(tableConfig.ui);
    }

    const fieldKeys = Object.keys(descriptors).filter(key => !key.startsWith('_'));

    for (const key of fieldKeys) {
      const descriptor = descriptors[key];
      
      if (!descriptor.table && !descriptor.display) {
        continue;
      }

      try {
        const column = createColumnFromDescriptor(key, descriptor, ctx);
        config.addColumn(column);
      } catch (error) {
        console.warn(`[TableConfig.fromDescriptors] Erreur lors de la création de la colonne ${key}:`, error);
      }
    }

    return config;
  }
}
