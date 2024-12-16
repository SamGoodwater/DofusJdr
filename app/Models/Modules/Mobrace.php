<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @mixin IdeHelperMobrace
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $name
 * @property string|null $super_race
 * @property int $is_visible
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereSuperRace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace withoutTrashed()
 * @mixin \Eloquent
 */
class Mobrace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'super_race',
        'uniqid',
        'is_visible',
        'created_by'
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function mobs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Mob::class);
    }
}
