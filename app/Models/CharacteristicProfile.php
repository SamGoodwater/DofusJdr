<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Définition d'une caractéristique du groupe Profil (classe, spécialisation, monstre).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property int|null $dofusdb_characteristic_id
 * @property string $entity
 * @property string|null $db_column
 * @property string|null $min
 * @property string|null $max
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property string|null $conversion_formula
 * @property string|null $conversion_function
 * @property array|null $conversion_dofus_sample
 * @property array|null $conversion_krosmoz_sample
 * @property array|null $conversion_sample_rows
 * @property array|null $norms_grid
 * @property string|null $norms_formula
 * @property array|null $norms_power_coefficients Coefs paliers {very_weak, weak, strong, very_strong} (neutral=1)
 * @property array|null $norms_conditions
 * @property string|null $norms_description
 * @property int|null $norms_help_section_id
 * @property array|null $value_available
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Characteristic $characteristic
 * @property-read Section|null $normsHelpSection
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereConversionDofusSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereConversionFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereConversionFunction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereConversionKrosmozSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereConversionSampleRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereDbColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereDofusdbCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereEntity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereFormulaDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereNormsConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereNormsDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereNormsFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereNormsGrid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereNormsHelpSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicProfile whereValueAvailable($value)
 *
 * @mixin \Eloquent
 */
class CharacteristicProfile extends Model
{
    protected $table = 'characteristic_profile';

    public const ENTITY_ALL = '*';

    public const ENTITY_BREED = 'breed';

    public const ENTITY_SPECIALIZATION = 'specialization';

    public const ENTITY_MONSTER = 'monster';

    /** @var list<string> */
    public const ENTITIES = [
        self::ENTITY_BREED,
        self::ENTITY_SPECIALIZATION,
        self::ENTITY_MONSTER,
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
}
