<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @mixin IdeHelperConsumabletype
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Consumabletype extends Model
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

    public function consumables()
    {
        return $this->hasMany(Consumable::class, 'consumabletype_id', 'id');
    }
}
