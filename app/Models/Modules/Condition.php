<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * 
 *
 * @mixin IdeHelperCondition
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property int $is_unbewitchable
 * @property int $is_malus
 * @property int $usable
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIsMalus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIsUnbewitchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition withoutTrashed()
 * @mixin \Eloquent
 */
class Condition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_unbewitchable',
        'is_malus',
        'usable',
        'is_visible',
        'created_by',
        'image'
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    //

    public function imagePath(): string
    {
        return Storage::disk('modules')->url($this->image);
    }
}
