<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\Item;

/**
 * @mixin IdeHelperItemtype
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
