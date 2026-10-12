<?php

namespace App\Enums;

/**
 * Enum pour les types de sections disponibles.
 *
 * Chaque type correspond à un template Vue dans le frontend.
 *
 * @method static self TEXT()
 * @method static self IMAGE()
 * @method static self GALLERY()
 * @method static self VIDEO()
 * @method static self ENTITY_TABLE()
 * @method static self LEGAL_MARKDOWN()
 */
enum SectionType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case GALLERY = 'gallery';
    case VIDEO = 'video';
    case ENTITY_TABLE = 'entity_table';
    case LEGAL_MARKDOWN = 'legal_markdown';
    case CHARACTERISTIC_NORMS = 'characteristic_norms';
    /** Catalogue de chartes (plusieurs caractéristiques en accordéon). */
    case CHARACTERISTIC_NORMS_CATALOG = 'characteristic_norms_catalog';
    /** Référentiel tabulaire des caractéristiques (formules, bornes, économie indicative). */
    case CHARACTERISTIC_REFERENCE_TABLE = 'characteristic_reference_table';
    /** Tableau vivant des plafonds de bonus d’équipement (slot × carac × bandes). */
    case EQUIPMENT_BONUS_TABLE = 'equipment_bonus_table';
    /** Tableau des runes de forgemagie (bonus max, prix, équipements autorisés). */
    case FORGEMAGIE_RUNE_TABLE = 'forgemagie_rune_table';
    /** Catalogue de fichiers téléchargeables (livre, fiches, logo). */
    case DOWNLOAD_CATALOG = 'download_catalog';
    /** Barèmes de sorts (dégâts / soin / bouclier…) + conversion en dés. */
    case SPELL_REFERENCE_SCALES = 'spell_reference_scales';

    /**
     * Retourne le label traduit du type.
     */
    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Texte',
            self::IMAGE => 'Image',
            self::GALLERY => 'Galerie',
            self::VIDEO => 'Vidéo',
            self::ENTITY_TABLE => 'Tableau d\'entités',
            self::LEGAL_MARKDOWN => 'Document légal (Markdown)',
            self::CHARACTERISTIC_NORMS => 'Charte caractéristique',
            self::CHARACTERISTIC_NORMS_CATALOG => 'Catalogue de chartes (normes)',
            self::CHARACTERISTIC_REFERENCE_TABLE => 'Référentiel des caractéristiques',
            self::EQUIPMENT_BONUS_TABLE => 'Tableau des bonus d’équipement',
            self::FORGEMAGIE_RUNE_TABLE => 'Tableau des runes de forgemagie',
            self::DOWNLOAD_CATALOG => 'Catalogue de téléchargements',
            self::SPELL_REFERENCE_SCALES => 'Barèmes de sorts',
        };
    }

    /**
     * Retourne l'icône FontAwesome associée au type.
     */
    public function icon(): string
    {
        return match ($this) {
            self::TEXT => 'fa-file-lines',
            self::IMAGE => 'fa-image',
            self::GALLERY => 'fa-images',
            self::VIDEO => 'fa-video',
            self::ENTITY_TABLE => 'fa-table',
            self::LEGAL_MARKDOWN => 'fa-scale-balanced',
            self::CHARACTERISTIC_NORMS => 'fa-chart-bar',
            self::CHARACTERISTIC_NORMS_CATALOG => 'fa-table-list',
            self::CHARACTERISTIC_REFERENCE_TABLE => 'fa-table-columns',
            self::EQUIPMENT_BONUS_TABLE => 'fa-table',
            self::FORGEMAGIE_RUNE_TABLE => 'fa-hammer',
            self::DOWNLOAD_CATALOG => 'fa-download',
            self::SPELL_REFERENCE_SCALES => 'fa-table',
        };
    }

    /**
     * Retourne la structure attendue des params pour ce type.
     *
     * @return array<string, mixed>
     */
    public function expectedParams(): array
    {
        return match ($this) {
            self::TEXT => [
                'content' => 'string (required)',
                'align' => 'string (optional: left|center|right)',
                'size' => 'string (optional: sm|md|lg|xl)',
            ],
            self::IMAGE => [
                'src' => 'string (required)',
                'alt' => 'string (required)',
                'caption' => 'string (optional)',
                'align' => 'string (optional: left|center|right)',
                'size' => 'string (optional: sm|md|lg|xl|full)',
            ],
            self::GALLERY => [
                'images' => 'array (required)',
                'columns' => 'integer (optional: 2|3|4)',
                'gap' => 'string (optional: sm|md|lg)',
            ],
            self::VIDEO => [
                'src' => 'string (required)',
                'type' => 'string (required: youtube|vimeo|direct)',
                'autoplay' => 'boolean (optional)',
                'controls' => 'boolean (optional)',
            ],
            self::ENTITY_TABLE => [
                'entity' => 'string (required)',
                'filters' => 'array (optional)',
                'columns' => 'array (optional)',
            ],
            self::LEGAL_MARKDOWN => [
                'sourceUrl' => 'string (required, URL du fichier markdown)',
                'title' => 'string (optional)',
            ],
            self::CHARACTERISTIC_NORMS => [
                'characteristic_key' => 'string (required, clé de la caractéristique)',
                'group' => 'string (required: creature|object|spell)',
                'entity' => 'string (optional, default: *)',
            ],
            self::CHARACTERISTIC_NORMS_CATALOG => [
                'group' => 'string (required: creature|object|spell)',
                'entity' => 'string (optional, default: *)',
                'characteristic_keys' => 'array (optional, filtre de clés)',
            ],
            self::CHARACTERISTIC_REFERENCE_TABLE => [
                'group' => 'string (optional: creature|object|spell|all, default: all)',
                'entity' => 'string (optional, default: *)',
                'search' => 'string (optional)',
                'sort_by' => 'string (optional: group|entity|name|key|equipment_max_bonus|forgemagie_max)',
                'sort_dir' => 'string (optional: asc|desc)',
                'show_prices' => 'boolean (optional, default: true)',
                'show_only_with_equipment' => 'boolean (optional, default: false)',
            ],
            self::EQUIPMENT_BONUS_TABLE => [],
            self::FORGEMAGIE_RUNE_TABLE => [
                'sort_by' => 'string (optional: name|rune_price|max_bonus, default: name)',
                'sort_dir' => 'string (optional: asc|desc)',
                'show_base_price' => 'boolean (optional, default: false)',
            ],
            self::DOWNLOAD_CATALOG => [
                'groups' => 'array (optional: clés de groupes du catalogue)',
            ],
            self::SPELL_REFERENCE_SCALES => [
                'compact' => 'boolean (optional, default: false)',
            ],
        };
    }

    /**
     * Retourne tous les types possibles.
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    /**
     * Retourne tous les types avec leurs labels.
     *
     * @return array<string, string>
     */
    public static function toArrayWithLabels(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }
}
