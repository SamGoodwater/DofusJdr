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
 * @property array|null $norms_conditions
 * @property string|null $norms_description
 * @property int|null $norms_help_section_id
 * @property array|null $value_available
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Characteristic $characteristic
 * @property-read Section|null $normsHelpSection
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
