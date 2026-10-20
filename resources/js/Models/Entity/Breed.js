/**
 * Modèle Breed pour le frontend
 * 
 * @description
 * Breed pour normaliser et manipuler les données de classe côté frontend.
 * 
 * @example
 * const classe = new Breed(props.classe);
 * console.log(classe.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';
import { normalizeElementOrientationMap } from '@/Utils/entity/breedOrientations';

export class Breed extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get officialId() {
        return this._data.official_id || null;
    }

    get dofusdbId() {
        return this._data.dofusdb_id || null;
    }

    get name() {
        return this._data.name || '';
    }

    get descriptionFast() {
        return this._data.description_fast || null;
    }

    get description() {
        return this._data.description || '';
    }

    /** HTML TipTap — évolution / progression de classe */
    get evolution() {
        return this._data.evolution ?? '';
    }

    get lifeDice() {
        return this._data.life_dice || null;
    }

    /** Métadonnées caractéristique « dé de vie » (icône, couleur, surcharges). */
    get lifeDiceMeta() {
        return this._data.life_dice_meta || null;
    }

    get specificity() {
        return this._data.specificity || null;
    }

    /** HTML TipTap — mécanique signature hors voies (ex. Pile/Face). */
    get mechanics() {
        return this._data.mechanics ?? '';
    }

    get dofusVersion() {
        return this._data.dofus_version || null;
    }

    get image() {
        return this._data.image || '';
    }

    get icon() {
        return this._data.icon || null;
    }

    get symbolFull() {
        return this._data.symbol_full || null;
    }

    get symbolBw() {
        return this._data.symbol_bw || null;
    }

    get logoMale() {
        return this._data.logo_male || null;
    }

    get logoFemale() {
        return this._data.logo_female || null;
    }

    get imageFullMale() {
        return this._data.image_full_male || null;
    }

    get imageFullFemale() {
        return this._data.image_full_female || null;
    }

    get autoUpdate() {
        return this._data.auto_update ?? false;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get npcs() {
        return this._data.npcs || [];
    }

    get spells() {
        return this._data.spells || [];
    }

    get capabilities() {
        return this._data.capabilities || [];
    }

    get creatureTraits() {
        return this._data.creatureTraits || [];
    }

    get languages() {
        return this._data.languages || [];
    }

    get sections() {
        return this._data.sections || [];
    }

    /** @returns {Array<{ character_level: number, slot_index: number, spells: object[] }>} */
    get spellSlots() {
        return this._data.spell_slots || [];
    }

    get spellsCount() {
        return Number(this._data.spells_count ?? this.spells.length ?? 0);
    }

    /**
     * Carte voix élémentaire → clé d’orientation (API `element_orientations`).
     *
     * @returns {Record<string, string|null>}
     */
    get elementOrientations() {
        return normalizeElementOrientationMap(this._data.element_orientations);
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Breed)
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

        // Sinon, gérer les champs spécifiques à Breed
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
            case 'description_fast':
                return this._toDescriptionCell(fieldKey, format, size, options);
            case 'life_dice':
                return this._toLifeDiceCell(format, size, options);
            case 'specificity':
                return this._toSpecificityCell(format, size, options);
            case 'image':
            case 'icon':
            case 'symbol_full':
            case 'symbol_bw':
            case 'logo_male':
            case 'logo_female':
            case 'image_full_male':
            case 'image_full_female':
                return this._toImageCell(fieldKey, format, size, options);
            case 'created_by':
                return this._toCreatedByCell(format, size, options);
            case 'breed_summary_relations':
                return this._toBreedSummaryRelationsCell(options);
            case 'created_at':
                return this._toCreatedAtCell(format, size, options);
            case 'updated_at':
                return this._toUpdatedAtCell(format, size, options);
            default:
                // Fallback vers la méthode de base
                return baseCell;
        }
    }

    /**
     * Génère une cellule pour le nom (lien vers la page de détail)
     * @private
     */
    _toNameCell(format, size, options) {
        const name = this.name || '-';
        const href =
            options.href ||
            (this.id && typeof route === 'function'
                ? route('entities.breeds.show', { breed: this.id })
                : '');
        
        return {
            type: 'route',
            value: name,
            params: {
                href,
                tooltip: name === '-' ? '' : name,
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 20 : null),
                searchValue: name === '-' ? '' : name,
                sortValue: name,
            },
        };
    }

    /**
     * Génère une cellule pour la description
     * @private
     */
    _toDescriptionCell(fieldKey, format, size, _options) {
        const description = fieldKey === 'description_fast' 
            ? (this.descriptionFast || this.description || '-')
            : (this.description || '-');
        
        return {
            type: 'text',
            value: description,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 30 : (size === 'md' ? 50 : null)),
                searchValue: description === '-' ? '' : description,
                sortValue: description,
            },
        };
    }

    /**
     * Cellule « dé de vie » : valeur + métadonnées caractéristique (affichage riche possible).
     * @private
     */
    _toLifeDiceCell(format, _size, _options) {
        const raw = this.lifeDice;
        const display =
            raw != null && String(raw).trim() !== '' ? String(raw).trim() : '-';

        return {
            type: 'text',
            value: display,
            params: {
                truncate: format.truncate || null,
                sortValue: display === '-' ? '' : display,
                searchValue: display === '-' ? '' : display,
                lifeDiceMeta: this.lifeDiceMeta,
                rawLifeDice: raw,
            },
        };
    }

    /**
     * Génère une cellule pour la spécificité
     * @private
     */
    _toSpecificityCell(format, size, _options) {
        const specificity = this.specificity || '-';
        
        return {
            type: 'text',
            value: specificity,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 30 : (size === 'md' ? 50 : null)),
                searchValue: specificity === '-' ? '' : specificity,
                sortValue: specificity,
            },
        };
    }

    /**
     * Génère une cellule pour une image/icône
     * @private
     */
    _toImageCell(fieldKey, _format, size, _options) {
        const urls = {
            icon: this.icon,
            symbol_full: this.symbolFull,
            symbol_bw: this.symbolBw,
            logo_male: this.logoMale,
            logo_female: this.logoFemale,
            image_full_male: this.imageFullMale,
            image_full_female: this.imageFullFemale,
            image: this.image,
        };
        const imageUrl = urls[fieldKey] || this.image;
        
        if (!imageUrl) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }
        
        return {
            type: 'image',
            value: imageUrl,
            params: {
                alt: this.name || fieldKey,
                size: size === 'xs' ? 'xs' : (size === 'sm' ? 'sm' : 'md'),
                sortValue: imageUrl,
                searchValue: imageUrl,
            },
        };
    }

    /**
     * Génère une cellule pour created_by
     * @private
     */
    _toCreatedByCell(format, size, options) {
        // Utiliser le UserFormatter via la méthode de base
        return super.toCell('created_by', options);
    }

    /**
     * Colonne résumée : relations gameplay de la classe.
     * @private
     */
    _toBreedSummaryRelationsCell(options) {
        if (options?.context === 'minimal' || options?.hideSpellCount) {
            return {
                type: 'chips',
                value: '',
                params: {
                    items: [],
                    sortValue: 0,
                    searchValue: '',
                    filterValue: '',
                },
            };
        }

        const items = [
            {
                icon: 'fa-solid fa-wand-magic-sparkles',
                value: this.spellsCount > 0 ? `${this.spellsCount} sort${this.spellsCount > 1 ? 's' : ''}` : null,
                tooltip: this.spellsCount > 0 ? `Sorts: ${this.spellsCount}` : '',
            },
        ].filter((it) => it.value !== null);

        const searchValue = items.map((it) => String(it.value)).join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                items,
                sortValue: this.spellsCount,
                searchValue,
                filterValue: searchValue,
            },
        };
    }

    /**
     * Génère une cellule pour la date de création
     * @private
     */
    _toCreatedAtCell(format, size, options) {
        // Utiliser le DateFormatter via la méthode de base
        return super.toCell('created_at', options);
    }

    /**
     * Génère une cellule pour la date de modification
     * @private
     */
    _toUpdatedAtCell(format, size, options) {
        // Utiliser le DateFormatter via la méthode de base
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
            official_id: this.officialId,
            dofusdb_id: this.dofusdbId,
            name: this.name,
            description_fast: this.descriptionFast,
            description: this.description,
            evolution: this.evolution,
            life_dice: this.lifeDice,
            specificity: this.specificity,
            dofus_version: this.dofusVersion,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            image: this.image,
            icon: this.icon,
            symbol_full: this.symbolFull,
            symbol_bw: this.symbolBw,
            logo_male: this.logoMale,
            logo_female: this.logoFemale,
            image_full_male: this.imageFullMale,
            image_full_female: this.imageFullFemale,
            auto_update: this.autoUpdate,
        };
    }
}

export default Breed;
