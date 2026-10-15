<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Type\ItemType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Définition d’une caractéristique pour une entité du groupe objet (item, consumable, resource, panoply).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property int|null $dofusdb_characteristic_id Id DofusDB GET /characteristics (ex. item.effects[].characteristic)
 * @property string $entity
 * @property string|null $db_column
 * @property string|null $min Valeur fixe, formule ou table JSON
 * @property string|null $max Valeur fixe, formule ou table JSON
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property string|null $conversion_formula
 * @property string|null $conversion_function Identifiant d'une fonction de conversion enregistrée
 * @property array|null $conversion_dofus_sample Niveau → valeur Dofus (ex. {"1":1,"200":200})
 * @property array|null $conversion_krosmoz_sample Niveau → valeur Krosmoz (ex. {"1":1,"20":20})
 * @property int $forgemagie_max
 * @property float|null $base_price_per_unit
 * @property float|null $rune_price_per_unit
 * @property array|null $norms_grid Grille 5×20 : {power_level: [val_lvl1..val_lvl20]}
 * @property string|null $norms_formula Formule générant norms_grid ([level], [power], [clé])
 * @property array|null $norms_power_coefficients Coefs paliers {very_weak, weak, strong, very_strong} (neutral=1)
 * @property array|null $norms_conditions Conditions de lecture
 * @property string|null $norms_description Description libre de la norme
 * @property int|null $norms_help_section_id Section CMS (texte) affichée sous la charte
 * @property array|null $value_available
 * @property-read Collection<int, ItemType> $allowedItemTypes
 * @property array<array-key, mixed>|null $conversion_sample_rows Lignes [{dofus_level, dofus_value, krosmoz_level, krosmoz_value}, ...]
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $allowed_item_types_count
 * @property-read Characteristic $characteristic
 * @property-read Section|null $normsHelpSection
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereBasePricePerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionDofusSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionFunction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionKrosmozSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionSampleRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereDbColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereDofusdbCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereEntity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereForgemagieAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereForgemagieMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereFormulaDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereNormsConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereNormsDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereNormsGrid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereNormsHelpSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereRunePricePerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereValueAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereNormsFormula($value)
 *
 * @mixin \Eloquent
 */
class CharacteristicObject extends Model
{
    protected $table = 'characteristic_object';

    /** S'applique à toutes les entités du groupe (défaut). */
    public const ENTITY_ALL = '*';

    public const ENTITY_ITEM = 'item';

    /** @deprecated Utiliser le groupe CharacteristicConsumable */
    public const ENTITY_CONSUMABLE = 'consumable';

    /** @deprecated Les ressources ne font plus partie du système de caractéristiques */
    public const ENTITY_RESOURCE = 'resource';

    public const ENTITY_PANOPLY = 'panoply';

    /** Entités du groupe objet (équipements + panoplies). */
    /** @var list<string> */
    public const ENTITIES = [
        self::ENTITY_ITEM,
        self::ENTITY_PANOPLY,
    ];

    /** @var list<string> */
    protected $fillable = [
        'characteristic_id',
        'dofusdb_characteristic_id',
        'entity',
        'db_column',
        'min',
        'max',
        'formula',
        'formula_display',
        'default_value',
        'conversion_formula',
        'conversion_function',
        'conversion_dofus_sample',
        'conversion_krosmoz_sample',
        'conversion_sample_rows',
        'norms_grid',
        'norms_formula',
        'norms_power_coefficients',
        'norms_conditions',
        'norms_description',
        'norms_help_section_id',
        'forgemagie_max',
        'base_price_per_unit',
        'rune_price_per_unit',
        'value_available',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'dofusdb_characteristic_id' => 'integer',
        'conversion_dofus_sample' => 'array',
        'conversion_krosmoz_sample' => 'array',
        'conversion_sample_rows' => 'array',
        'norms_grid' => 'array',
        'norms_power_coefficients' => 'array',
        'norms_conditions' => 'array',
        'forgemagie_max' => 'integer',
        'base_price_per_unit' => 'decimal:2',
        'rune_price_per_unit' => 'decimal:2',
        'value_available' => 'array',
        'norms_help_section_id' => 'integer',
    ];

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }

    public function normsHelpSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'norms_help_section_id');
    }

    /**
     * Types d'équipement (item_types) pour lesquels cette caractéristique est autorisée.
     * Vide = tous les types ; sinon la caractéristique ne s'applique qu'aux types listés.
     *
     * @return BelongsToMany<ItemType, self>
     */
    public function allowedItemTypes(): BelongsToMany
    {
        return $this->belongsToMany(ItemType::class, 'characteristic_object_item_type')
            ->withTimestamps();
    }
}
