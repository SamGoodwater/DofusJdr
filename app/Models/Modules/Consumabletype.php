<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperConsumabletype
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
