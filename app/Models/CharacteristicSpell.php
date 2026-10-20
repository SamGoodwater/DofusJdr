<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Définition d’une caractéristique pour l’entité spell (groupe sort).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property int|null $dofusdb_characteristic_id Id DofusDB GET /characteristics
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
 * @property array|null $norms_grid Grille 5×20 : {power_level: [val_lvl1..val_lvl20]}
 * @property string|null $norms_formula Formule générant norms_grid ([level], [power], [clé])
 * @property array|null $norms_power_coefficients Coefs paliers {very_weak, weak, strong, very_strong} (neutral=1)
 * @property array|null $norms_conditions Conditions de lecture
 * @property string|null $norms_description Description libre de la norme
 * @property int|null $norms_help_section_id Section CMS (texte) affichée sous la charte
 * @property array|null $value_available
 * @property array<array-key, mixed>|null $conversion_sample_rows Lignes [{dofus_level, dofus_value, krosmoz_level, krosmoz_value}, ...]
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Characteristic $characteristic
 * @property-read Section|null $normsHelpSection
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionDofusSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionFunction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionKrosmozSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionSampleRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereDbColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereDofusdbCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereEntity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereFormulaDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereNormsConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereNormsDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereNormsGrid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereNormsHelpSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereValueAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereNormsFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereNormsPowerCoefficients($value)
 * @mixin \Eloquent
 */
class CharacteristicSpell extends Model
{
    protected $table = 'characteristic_spell';

    /** S'applique à toutes les entités du groupe (défaut). */
    public const ENTITY_ALL = '*';

    public const ENTITY_SPELL = 'spell';

    /** @var list<string> */
    public const ENTITIES = [self::ENTITY_SPELL];

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
