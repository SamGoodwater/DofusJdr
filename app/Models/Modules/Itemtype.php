<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\Item;

/**
 * 
 *
 * @mixin IdeHelperItemtype
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Itemtype extends Model
{
    protected $fillable = [
        'name',
        'uniqid',
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'itemtype_id', 'id');
    }
}
