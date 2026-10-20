<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Entity\Spell;
use Database\Factories\SpellDegreeFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Degré d’un sort : niveau requis, propriétés de lancement, effets (hérités ou propres).
 *
 * @property int $id
 * @property int $spell_id
 * @property int $position
 * @property int|null $required_level
 * @property bool $inherits_effects
 * @property string $properties_source
 * @property string|null $pa
 * @property string|null $po_min
 * @property string|null $po_max
 * @property bool|null $po_editable
 * @property bool|null $sight_line
 * @property bool|null $cast_in_line
 * @property bool|null $cast_in_diagonal
 * @property string|null $area
 * @property string|null $cast_per_turn
 * @property string|null $cast_per_target
 * @property string|null $number_between_two_cast
 * @property int|null $global_cooldown
 * @property int|null $max_stack
 * @property string|null $duration
 * @property bool|null $allows_reaction
 * @property string|null $casting_time
 * @property string|null $resolution_mode
 * @property string|null $attack_characteristic_key
 * @property string|null $save_characteristic_key
 * @property string|null $save_dc_formula
 * @property string|null $save_success_note
 * @property bool|null $auto_success_if_willing_target
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Spell $spell
 * @property-read Collection<int, SpellDegreeEffect> $effects
 * @property-read int|null $effects_count
 * @method static SpellDegreeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereAllowsReaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereAttackCharacteristicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereAutoSuccessIfWillingTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereCastInDiagonal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereCastInLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereCastPerTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereCastPerTurn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereCastingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereGlobalCooldown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereInheritsEffects($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereMaxStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereNumberBetweenTwoCast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree wherePoEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree wherePoMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree wherePoMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree wherePropertiesSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereResolutionMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereSaveCharacteristicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereSaveDcFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereSaveSuccessNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereSightLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereSpellId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegree whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SpellDegree extends Model
{
    /** @use HasFactory<SpellDegreeFactory> */
    use HasFactory;

    protected $table = 'spell_degrees';

    /**
     * Clés de propriétés de lancement résolues avec repli sur le sort.
     *
     * @var list<string>
     */
    public const PROPERTY_KEYS = [
        'pa',
        'po_min',
        'po_max',
        'po_editable',
        'sight_line',
        'cast_in_line',
        'cast_in_diagonal',
        'area',
        'cast_per_turn',
        'cast_per_target',
        'number_between_two_cast',
        'global_cooldown',
        'max_stack',
        'duration',
        'casting_time',
    ];

    /** Sources du bloc de propriétés de lancement. */
    public const PROPERTIES_SOURCE_OWN = 'own';

    public const PROPERTIES_SOURCE_PREVIOUS = 'previous';

    public const PROPERTIES_SOURCE_SPELL = 'spell';

    /** @var list<string> */
    public const PROPERTIES_SOURCES = [
        self::PROPERTIES_SOURCE_OWN,
        self::PROPERTIES_SOURCE_PREVIOUS,
        self::PROPERTIES_SOURCE_SPELL,
    ];

    protected $fillable = [
        'spell_id',
        'position',
        'required_level',
        'inherits_effects',
        'properties_source',
        'pa',
        'po_min',
        'po_max',
        'po_editable',
        'sight_line',
        'cast_in_line',
        'cast_in_diagonal',
        'area',
        'cast_per_turn',
        'cast_per_target',
        'number_between_two_cast',
        'global_cooldown',
        'max_stack',
        'duration',
        'allows_reaction',
        'casting_time',
        'resolution_mode',
        'attack_characteristic_key',
        'save_characteristic_key',
        'save_dc_formula',
        'save_success_note',
        'auto_success_if_willing_target',
    ];

    protected $casts = [
        'spell_id' => 'integer',
        'position' => 'integer',
        'required_level' => 'integer',
        'inherits_effects' => 'boolean',
        'properties_source' => 'string',
        'po_editable' => 'boolean',
        'sight_line' => 'boolean',
        'cast_in_line' => 'boolean',
        'cast_in_diagonal' => 'boolean',
        'global_cooldown' => 'integer',
        'max_stack' => 'integer',
        'allows_reaction' => 'boolean',
        'auto_success_if_willing_target' => 'boolean',
    ];

    protected static function newFactory(): SpellDegreeFactory
    {
        return SpellDegreeFactory::new();
    }

    /**
     * @return BelongsTo<Spell, $this>
     */
    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class);
    }

    /**
     * Effets matérialisés sur ce degré (vides si inherits_effects).
     *
     * @return HasMany<SpellDegreeEffect, $this>
     */
    public function effects(): HasMany
    {
        return $this->hasMany(SpellDegreeEffect::class)->orderBy('order');
    }
}
