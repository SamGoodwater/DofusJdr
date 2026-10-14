/**
 * Modèle Monster pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de monster côté frontend.
 * 
 * @example
 * const monster = new Monster(props.monster);
 * console.log(monster.creature?.name); // Accès via la relation creature
 */
import { BaseModel } from '../BaseModel';
import CharacteristicsCard from '@/Pages/Organismes/data-display/CharacteristicsCard.vue';
import { buildCreatureCharacteristicGroups } from '@/Utils/Entity/buildCreatureCharacteristicGroups';
import { getByDbColumnMap, getMonsterFieldMeta } from '@/Composables/store/useCharacteristicsStore';
export class Monster extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get creatureId() {
        return this._data.creature_id || null;
    }

    get name() {
        if (this._data.name) {
            return String(this._data.name);
        }
        return this.creature?.name ? String(this.creature.name) : '';
    }

    get level() {
        const v = this._data.level;
        if (v !== null && v !== undefined && v !== '') {
            const n = Number(v);
            return Number.isFinite(n) ? n : null;
        }
        const c = this._data.creature?.level;
        if (c === null || c === undefined || c === '') {
            return null;
        }
        const n = Number(c);
        return Number.isFinite(n) ? n : null;
    }

    get officialId() {
        return this._data.official_id || null;
    }

    get dofusdbId() {
        return this._data.dofusdb_id || null;
    }

    get dofusVersion() {
        return this._data.dofus_version || null;
    }

    get autoUpdate() {
        return this._data.auto_update ?? false;
    }

    get size() {
        // 0 = Minuscule : ne pas utiliser || (0 serait assimilé à « vide »).
        const raw = this._data.size;
        return raw === null || raw === undefined ? null : raw;
    }

    get monsterRaceId() {
        return this._data.monster_race_id || null;
    }

    get isBoss() {
        return this._data.is_boss ?? false;
    }

    get bossPa() {
        return this._data.boss_pa || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get creature() {
        return this._data.creature || null;
    }

    get creatureTraits() {
        const c = this.creature;
        if (!c) {
            return [];
        }
        if (Array.isArray(c.creatureTraits)) {
            return c.creatureTraits;
        }
        if (Array.isArray(c.creature_traits)) {
            return c.creature_traits;
        }

        return [];
    }

    get monsterRace() {
        return this._data.monsterRace || null;
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get languages() {
        return this._data.languages || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    get spellInvocations() {
        return this._data.spellInvocations || [];
    }

    get spellInvocationsCount() {
        return Number(this._data.spell_invocations_count ?? this.spellInvocations.length ?? 0);
    }

    get resourcesCount() {
        return Number(this._data.resources_count ?? this.creature?.resources_count ?? 0);
    }

    get itemsCount() {
        return Number(this._data.items_count ?? this.creature?.items_count ?? 0);
    }

    get consumablesCount() {
        return Number(this._data.consumables_count ?? this.creature?.consumables_count ?? 0);
    }

    get campaignsCount() {
        return Number(this._data.campaigns_count ?? this.campaigns.length ?? 0);
    }

    get scenariosCount() {
        return Number(this._data.scenarios_count ?? this.scenarios.length ?? 0);
    }

    /**
     * Retourne la map des caractéristiques créature indexées par db_column.
     * Source: store (Inertia share) ; fallback ctx pour compat.
     * @private
     */
    _getCreatureCharacteristicsByColumn(options = {}) {
        const fromStore = getByDbColumnMap('creature');
        if (fromStore && Object.keys(fromStore).length > 0) return fromStore;
        return options?.ctx?.characteristics?.creature?.byDbColumn || {};
    }

    /**
     * Résout une caractéristique par ses colonnes candidates (ex: pa, po, life).
     * @private
     */
    _getCreatureCharacteristicDef(options = {}, candidates = []) {
        const byColumn = this._getCreatureCharacteristicsByColumn(options);
        for (const key of candidates) {
            const found = byColumn?.[key];
            if (found) return found;
        }
        return null;
    }

    /**
     * Métadonnées service (taille, race) — `creature.byMonsterField` ou store Inertia.
     *
     * @param {Object} options
     * @param {'size'|'monster_race'} fieldKey
     * @returns {object|null}
     */
    _getMonsterDisplayFieldDef(options = {}, fieldKey) {
        const ctx = options?.ctx?.characteristics?.creature?.byMonsterField;
        if (ctx && ctx[fieldKey]) {
            return ctx[fieldKey];
        }
        const map = getMonsterFieldMeta();
        return map?.[fieldKey] ?? null;
    }

    /**
     * Affichage chips pour taille / hostilité : vue minimal ou ligne → icône + valeur uniquement ;
     * tableau, compact, étendu → icône + libellé caractéristique + valeur.
     *
     * @param {Object} options - Options `toCell` (context: minimal | line | table | compact | extended, …)
     * @returns {{ labelMode: 'icon-only' | 'full' }}
     * @private
     */
    _resolveChipsLayoutForTraitDisplay(options = {}) {
        const ctx = String(options?.context || 'table');
        if (ctx === 'minimal' || ctx === 'line') {
            return { labelMode: 'icon-only' };
        }
        return { labelMode: 'full' };
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Monster)
     * @param {string} fieldKey - Clé du champ
     * @param {Object} [options={}] - Options (size, context, config, ctx)
     * @returns {Object|null} Cell object ou null si valeur invalide
     */
    toCell(fieldKey, options = {}) {
        // D'abord, essayer la méthode de base (gère les formatters automatiquement)
        const baseCell = super.toCell(fieldKey, options);
        
        // Si la méthode de base a trouvé quelque chose (formatter ou valeur par défaut valide), l'utiliser
        if (baseCell && (baseCell.type !== 'text' || (baseCell.value && baseCell.value !== '-'))) {
            return baseCell;
        }

        // Sinon, gérer les champs spécifiques à Monster
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'creature_name':
            case 'creatureName':
                return this._toCreatureNameCell(format, size, options);
            case 'monster_race':
            case 'monsterRace':
                return this._toMonsterRaceCell(format, size, options);
            case 'size':
                return this._toSizeCell(format, size, options);
            case 'is_boss':
            case 'isBoss':
                return this._toIsBossCell(format, size, options);
            case 'boss_pa':
            case 'bossPa':
                return this._toBossPaCell(format, size, options);
            case 'created_at':
                return this._toCreatedAtCell(format, size, options);
            case 'updated_at':
                return this._toUpdatedAtCell(format, size, options);
            case 'creature_characteristics':
                return this._toCreatureCharacteristicsCell(options);
            case 'creature_summary_resistance':
                return this._toSummaryResistanceCell(options);
            case 'creature_summary_damage':
                return this._toSummaryDamageCell(options);
            case 'creature_summary_stats':
                return this._toSummaryStatsCell(options);
            case 'creature_summary_combat':
                return this._toSummaryCombatCell(options);
            case 'creature_summary_control':
                return this._toSummaryControlCell(options);
            case 'monster_summary_relations':
                return this._toMonsterSummaryRelationsCell(options);
            default:
                if (fieldKey.startsWith('creature_')) {
                    const creatureKey = fieldKey.slice(9);
                    return this._toCreatureFieldCell(creatureKey, options);
                }
                return baseCell;
        }
    }

    /**
     * Génère une cellule pour un champ de la créature (level, life, pa, etc.)
     * @private
     * @param {string} creatureKey - Clé du champ sur l'objet creature (ex: level, life, pa)
     * @param {Object} [_options] - Options passées à toCell
     * @returns {Object} Cell object
     */
    _toCreatureFieldCell(creatureKey, _options) {
        const creature = this.creature;
        if (!creature || typeof creature !== 'object') {
            return creatureKey === 'image'
                ? { type: 'image', value: '', params: { sortValue: '', searchValue: '' } }
                : { type: 'text', value: '-', params: { sortValue: '', searchValue: '', filterValue: null } };
        }
        let raw = creature[creatureKey];
        if (creatureKey === 'image') {
            const url = raw && String(raw).trim() ? String(raw) : '';
            return {
                type: 'image',
                value: url,
                params: { sortValue: url ? 1 : 0, searchValue: '', alt: this.creature?.name || 'Créature' },
            };
        }
        const rawForFilter = raw !== null && raw !== undefined && raw !== '' ? String(raw) : null;
        if (raw === null || raw === undefined || raw === '') {
            return {
                type: 'text',
                value: '-',
                params: { sortValue: '', searchValue: '', filterValue: null },
            };
        }
        if (creatureKey === 'hostility') {
            const labels = { 0: 'Amical', 1: 'Curieux', 2: 'Neutre', 3: 'Hostile', 4: 'Agressif' };
            const displayRaw = labels[Number(raw)] ?? String(raw);
            const def = this._getCreatureCharacteristicDef(_options, ['hostility']);
            if (def) {
                return {
                    type: 'chips',
                    value: '',
                    params: {
                        items: [
                            {
                                icon: def.icon || null,
                                color: def.color || null,
                                value: displayRaw,
                                name: def.name,
                                shortLabel: def.short_name,
                                helper: def.helper,
                                descriptions: def.descriptions,
                            },
                        ],
                        chipsLayout: this._resolveChipsLayoutForTraitDisplay(_options),
                        sortValue: Number(raw),
                        searchValue: displayRaw,
                        filterValue: String(raw),
                    },
                };
            }
            return {
                type: 'text',
                value: displayRaw,
                params: {
                    sortValue: Number(raw),
                    searchValue: displayRaw,
                    filterValue: String(raw),
                    tooltip: String(def?.helper || def?.name || displayRaw),
                },
            };
        }

        const characteristicDef = this._getCreatureCharacteristicDef(_options, [creatureKey]);
        if (characteristicDef) {
            const value = String(raw);
            const numericSort = Number(raw);
            return {
                type: 'chips',
                value: '',
                params: {
                    items: [
                        {
                            icon: characteristicDef.icon || null,
                            color: characteristicDef.color || null,
                            value,
                            name: characteristicDef.name,
                            shortLabel: characteristicDef.short_name,
                            helper: characteristicDef.helper,
                            descriptions: characteristicDef.descriptions,
                        },
                    ],
                    sortValue: Number.isFinite(numericSort) ? numericSort : value,
                    searchValue: value,
                    filterValue: rawForFilter ?? value,
                },
            };
        }

        const value = String(raw);
        const sortValue = Number(raw);
        return {
            type: 'text',
            value,
            params: {
                sortValue: Number.isFinite(sortValue) ? sortValue : value,
                searchValue: value,
                filterValue: rawForFilter ?? value,
            },
        };
    }

    /**
     * Colonne unique : carte de caractéristiques (remplace les 5 colonnes résumé).
     * @private
     */
    _toCreatureCharacteristicsCell(_options) {
        const groups = buildCreatureCharacteristicGroups(this.creature);

        const c = this.creature;
        const elements = ["neutre", "terre", "feu", "air", "eau"];
        const filterParts = [];
        if (c && typeof c === "object") {
            for (const db of ["pa", "pm", "po", "life", "ini", "invocation", "strong", "intel", "agi", "chance", "vitality", "sagesse", "ca", "dodge_pa", "dodge_pm", "fuite", "tacle", "touch"]) {
                const v = c[db];
                if (v !== null && typeof v !== "undefined" && String(v) !== "") filterParts.push(String(v));
            }
            for (const el of elements) {
                ["res_fixe_" + el, "res_" + el, "do_fixe_" + el].forEach((db) => {
                    const v = c[db];
                    if (v !== null && typeof v !== "undefined" && String(v) !== "") filterParts.push(String(v));
                });
            }
            for (const db of ["do_fixe_multiple"]) {
                const v = c[db];
                if (v !== null && typeof v !== "undefined" && String(v) !== "") filterParts.push(String(v));
            }
        }
        const filterValue = filterParts.join(" ");

        return {
            type: "chips",
            value: "",
            params: {
                component: CharacteristicsCard,
                componentProps: {
                    entity: this.creature || null,
                    groups,
                    dense: true,
                    passValue: false,
                },
                passValue: false,
                sortValue: 0,
                searchValue: filterValue,
                filterValue,
            },
        };
    }

    /**
     * Colonne résumée : résistances fixes + % (neutre, terre, feu, air, eau) — groupe Résistances.
     * @private
     */
    _toSummaryResistanceCell(_options) {
        return this._toSummaryGroupCell(_options, 'Résistances', ['res_fixe_neutre', 'res_neutre', 'res_fixe_terre', 'res_terre', 'res_fixe_feu', 'res_feu', 'res_fixe_air', 'res_air', 'res_fixe_eau', 'res_eau']);
    }

    /**
     * Colonne résumée : bonus de touche + dommages fixes (neutre, terre, feu, air, eau) — groupe Dommages.
     * @private
     */
    _toSummaryDamageCell(_options) {
        return this._toSummaryGroupCell(_options, 'Dommages', ['touch', 'do_fixe_neutre', 'do_fixe_terre', 'do_fixe_feu', 'do_fixe_air', 'do_fixe_eau', 'do_fixe_multiple']);
    }

    /**
     * Colonne résumée : scores / mods / sauvegardes — groupe Caractéristiques (abilityStack).
     * @private
     */
    _toSummaryStatsCell(_options) {
        return this._toSummaryGroupCell(_options, 'Caractéristiques', ['strong', 'intel', 'agi', 'chance', 'vitality', 'sagesse']);
    }

    /**
     * Colonne résumée : PA, PM, PO, PV, Initiative, Invocation, CA — groupe Combat.
     * @private
     */
    _toSummaryCombatCell(_options) {
        return this._toSummaryGroupCell(_options, 'Combat', ['pa', 'pm', 'po', 'life', 'ini', 'invocation', 'ca']);
    }

    /**
     * Colonne résumée : esquive PA/PM, fuite, tacle — groupe Contrôle.
     * @private
     */
    _toSummaryControlCell(_options) {
        return this._toSummaryGroupCell(_options, 'Contrôle', ['dodge_pa', 'dodge_pm', 'fuite', 'tacle']);
    }

    /**
     * Colonne résumée : relations gameplay du monstre.
     * @private
     */
    _toMonsterSummaryRelationsCell(_options) {
        const items = [
            {
                icon: 'fa-solid fa-wand-magic-sparkles',
                value: this.spellInvocationsCount > 0 ? `${this.spellInvocationsCount} invocation${this.spellInvocationsCount > 1 ? 's' : ''}` : null,
                tooltip: this.spellInvocationsCount > 0 ? `Sorts d'invocation: ${this.spellInvocationsCount}` : '',
            },
            {
                icon: 'fa-solid fa-flask',
                value: this.resourcesCount > 0 ? `${this.resourcesCount} ressource${this.resourcesCount > 1 ? 's' : ''}` : null,
                tooltip: this.resourcesCount > 0 ? `Ressources: ${this.resourcesCount}` : '',
            },
            {
                icon: 'fa-solid fa-sword',
                value: this.itemsCount > 0 ? `${this.itemsCount} équipement${this.itemsCount > 1 ? 's' : ''}` : null,
                tooltip: this.itemsCount > 0 ? `Équipements: ${this.itemsCount}` : '',
            },
            {
                icon: 'fa-solid fa-mug-hot',
                value: this.consumablesCount > 0 ? `${this.consumablesCount} consommable${this.consumablesCount > 1 ? 's' : ''}` : null,
                tooltip: this.consumablesCount > 0 ? `Consommables: ${this.consumablesCount}` : '',
            },
            {
                icon: 'fa-solid fa-flag',
                value: this.campaignsCount > 0 ? `${this.campaignsCount} campagne${this.campaignsCount > 1 ? 's' : ''}` : null,
                tooltip: this.campaignsCount > 0 ? `Campagnes: ${this.campaignsCount}` : '',
            },
            {
                icon: 'fa-solid fa-scroll',
                value: this.scenariosCount > 0 ? `${this.scenariosCount} scénario${this.scenariosCount > 1 ? 's' : ''}` : null,
                tooltip: this.scenariosCount > 0 ? `Scénarios: ${this.scenariosCount}` : '',
            },
        ].filter((it) => it.value !== null);

        const searchValue = items.map((it) => String(it.value)).join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                items,
                sortValue:
                    this.spellInvocationsCount +
                    this.resourcesCount +
                    this.itemsCount +
                    this.consumablesCount +
                    this.campaignsCount +
                    this.scenariosCount,
                searchValue,
                filterValue: searchValue,
            },
        };
    }

    /**
     * Génère une cellule résumé avec CharacteristicsCard pour un seul groupe (Combat, Stats, Résistances, Dommages, Contrôle).
     * @private
     * @param {Object} _options - Options (ctx pour byDbColumn)
     * @param {string} groupTitle - Titre du groupe tel que retourné par buildCreatureCharacteristicGroups
     * @param {string[]} dbColumnsForFilter - Clés creature pour construire filterValue (recherche)
     */
    _toSummaryGroupCell(_options, groupTitle, dbColumnsForFilter) {
        const allGroups = buildCreatureCharacteristicGroups(this.creature);
        const groups = allGroups.filter((g) => g.title === groupTitle);

        const c = this.creature;
        const filterParts = [];
        if (c && typeof c === 'object') {
            for (const db of dbColumnsForFilter) {
                const v = c[db];
                if (v !== null && typeof v !== 'undefined' && String(v) !== '') {
                    filterParts.push(String(v));
                }
            }
        }
        const filterValue = filterParts.join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                component: CharacteristicsCard,
                componentProps: {
                    entity: this.creature || null,
                    groups,
                    dense: true,
                    passValue: false,
                },
                passValue: false,
                sortValue: 0,
                searchValue: filterValue,
                filterValue,
            },
        };
    }

    /**
     * Génère une cellule pour le nom de la créature (lien vers la page de détail)
     * @private
     */
    _toCreatureNameCell(format, size, _options) {
        const creature = this.creature;
        if (!creature) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const name = creature.name || '-';
        const truncate = format.truncate || (size === 'xs' || size === 'sm' ? 20 : null);

        return {
            type: 'text',
            value: name,
            params: {
                tooltip: name === '-' ? '' : name,
                truncate,
                searchValue: name === '-' ? '' : name,
                sortValue: name,
            },
        };
    }

    /**
     * Génère une cellule pour la race du monstre
     * @private
     */
    _toMonsterRaceCell(_format, _size, _options) {
        const monsterRace = this.monsterRace;
        
        if (!monsterRace) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const raceName = monsterRace.name || monsterRace.label || '-';
        const def = this._getMonsterDisplayFieldDef(_options, 'monster_race');
        const tooltip =
            raceName === '-'
                ? ''
                : String(def?.helper || def?.name || raceName);

        return {
            type: 'badge',
            value: raceName,
            params: {
                color: 'auto',
                autoLabel: raceName,
                autoScheme: 'labelHash',
                autoTone: 'light',
                tooltip,
                sortValue: raceName,
                searchValue: raceName,
            },
        };
    }

    /**
     * Génère une cellule pour la taille
     * @private
     */
    _toSizeCell(_format, _size, _options) {
        const sizeValue = this.size ?? null;
        const sizeLabels = {
            0: 'Minuscule',
            1: 'Petit',
            2: 'Moyen',
            3: 'Grand',
            4: 'Colossal',
            5: 'Gigantesque',
        };
        const label = sizeValue !== null && sizeLabels[sizeValue] ? sizeLabels[sizeValue] : (sizeValue !== null ? String(sizeValue) : '-');
        const def = this._getMonsterDisplayFieldDef(_options, 'size');

        if (label === '-') {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                    filterValue: null,
                },
            };
        }

        if (def) {
            return {
                type: 'chips',
                value: '',
                params: {
                    items: [
                        {
                            icon: def.icon || null,
                            color: def.color || null,
                            value: label,
                            name: def.name,
                            shortLabel: def.short_name,
                            helper: def.helper,
                            descriptions: def.descriptions,
                        },
                    ],
                    chipsLayout: this._resolveChipsLayoutForTraitDisplay(_options),
                    sortValue: sizeValue ?? 0,
                    searchValue: label,
                    filterValue: sizeValue !== null ? String(sizeValue) : null,
                },
            };
        }

        const tooltip =
            String(def?.helper || def?.name || '') || (sizeValue !== null ? `Taille : ${label}` : '');

        return {
            type: 'text',
            value: label,
            params: {
                sortValue: sizeValue ?? 0,
                searchValue: label,
                filterValue: sizeValue !== null ? String(sizeValue) : null,
                tooltip,
            },
        };
    }

    /**
     * Génère une cellule pour is_boss
     * @private
     */
    _toIsBossCell(_format, _size, _options) {
        const isBoss = !!this.isBoss;
        const rawPa = this.bossPa;
        const paNumber = rawPa !== null && rawPa !== undefined && rawPa !== '' ? Number(rawPa) : null;

        let label = '';
        if (isBoss) {
            if (paNumber !== null && !Number.isNaN(paNumber) && paNumber > 0) {
                label = `Boss +${paNumber}`;
            } else if (rawPa && String(rawPa) !== '0') {
                label = `Boss +${rawPa}`;
            } else {
                label = 'Boss';
            }
        }

        const tooltip =
            "Boss : PA légendaires utilisables entre deux tours d'autres créatures, autant que le pool restant, en plus du tour. Le pool se recharge à la fin du tour du boss.";

        return {
            type: 'badge',
            value: label,
            params: {
                color: isBoss ? 'error' : 'base',
                sortValue: isBoss ? (paNumber || 0) + 1 : 0,
                searchValue: label,
                filterValue: isBoss ? '1' : '0',
                tooltip: isBoss ? tooltip : '',
            },
        };
    }

    /**
     * Génère une cellule pour boss_pa
     * @private
     */
    _toBossPaCell(_format, _size, _options) {
        const rawPa = this.bossPa;
        const paNumber = rawPa !== null && Number(rawPa) > 0 && rawPa !== undefined && rawPa !== '' ? Number(rawPa) : null;
        const paDef = this._getCreatureCharacteristicDef(_options, ['pa']);

        let display = '-';
        if (paNumber !== null && !Number.isNaN(paNumber) && paNumber > 0) {
            display = `${paNumber} PA`;
        } else if (rawPa && String(rawPa) !== '0') {
            display = `${rawPa} PA`;
        }

        if (display !== '-' && paDef) {
            const paLabel = paDef?.short_name || paDef?.name || 'PA';
            return {
                type: 'chips',
                value: '',
                params: {
                    items: [
                        {
                            icon: paDef.icon || 'fa-solid fa-bolt',
                            color: paDef.color || null,
                            value: String(paNumber ?? rawPa),
                            tooltip: `${paLabel}: ${paNumber ?? rawPa}`,
                        },
                    ],
                    sortValue: paNumber && !Number.isNaN(paNumber) ? paNumber : Number(rawPa) || 0,
                    searchValue: String(display),
                    filterValue: String(paNumber ?? rawPa),
                },
            };
        }

        return {
            type: 'text',
            value: display,
            params: {
                sortValue: paNumber && !Number.isNaN(paNumber) ? paNumber : 0,
                searchValue: display === '-' ? '' : display,
            },
        };
    }

    /**
     * Génère une cellule pour la date de création
     * @private
     */
    _toCreatedAtCell(_format, _size, options) {
        return super.toCell('created_at', options);
    }

    /**
     * Génère une cellule pour la date de modification
     * @private
     */
    _toUpdatedAtCell(_format, _size, options) {
        return super.toCell('updated_at', options);
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Retourne les données pour un formulaire
     * @returns {Object}
     */
    toFormData() {
        return {
            creature_id: this.creatureId,
            official_id: this.officialId,
            dofusdb_id: this.dofusdbId,
            dofus_version: this.dofusVersion,
            auto_update: this.autoUpdate,
            size: this.size,
            monster_race_id: this.monsterRaceId,
            is_boss: this.isBoss,
            boss_pa: this.bossPa,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
        };
    }
}

export default Monster;
