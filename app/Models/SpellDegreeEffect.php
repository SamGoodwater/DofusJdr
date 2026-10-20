<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SpellDegreeEffectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Effet d’un degré de sort (catalogue SubEffect + paramètres).
 *
 * Vocabulaire UI : « effet » (anciennement sous-effet).
 *
 * @property int $id
 * @property int $spell_degree_id
 * @property int $sub_effect_id
 * @property int $order
 * @property string $scope
 * @property int|null $value_min
 * @property int|null $value_max
 * @property int|null $dice_num
 * @property int|null $dice_side
 * @property array<array-key, mixed>|null $params
 * @property bool $crit_only
 * @property string|null $duration_formula
 * @property string|null $logic_group
 * @property string|null $logic_operator
 * @property string|null $logic_condition
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SpellDegree $spellDegree
 * @property-read SubEffect $subEffect
 * @method static SpellDegreeEffectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereCritOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereDiceNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereDiceSide($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereDurationFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereLogicCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereLogicGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereLogicOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereSpellDegreeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereSubEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereValueMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellDegreeEffect whereValueMin($value)
 * @mixin \Eloquent
 */
class SpellDegreeEffect extends Model
{
    /** @use HasFactory<SpellDegreeEffectFactory> */
    use HasFactory;

    protected $table = 'spell_degree_effects';

    protected $fillable = [
        'spell_degree_id',
        'sub_effect_id',
        'order',
        'scope',
        'value_min',
        'value_max',
        'dice_num',
        'dice_side',
        'duration_formula',
        'logic_group',
        'logic_operator',
        'logic_condition',
        'params',
        'crit_only',
    ];

    protected $casts = [
        'spell_degree_id' => 'integer',
        'sub_effect_id' => 'integer',
        'order' => 'integer',
        'value_min' => 'integer',
        'value_max' => 'integer',
        'dice_num' => 'integer',
        'dice_side' => 'integer',
        'params' => 'array',
        'crit_only' => 'boolean',
    ];

    protected static function newFactory(): SpellDegreeEffectFactory
    {
        return SpellDegreeEffectFactory::new();
    }

    public function spellDegree(): BelongsTo
    {
        return $this->belongsTo(SpellDegree::class);
    }

    public function subEffect(): BelongsTo
    {
        return $this->belongsTo(SubEffect::class);
    }
}
