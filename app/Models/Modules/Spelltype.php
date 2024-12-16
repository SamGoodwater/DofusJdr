<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @mixin IdeHelperSpelltype
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property string $color
 * @property string|null $icon
 * @property int $is_visible
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype withoutTrashed()
 * @mixin \Eloquent
 */
class Spelltype extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'is_visible'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'created_by'];

    public function spells(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Spell::class);
    }
}
