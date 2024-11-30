<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperShop
 */
class Shop extends Model
{
    protected $fillable = ['uniqid', 'name', 'description', 'location', 'price', 'usable', 'npc_id'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function consumables()
    {
        return $this->belongsToMany(Consumable::class)->withPivot(
            'quantity',
            'price',
            'comment'
        );
    }

    public function items()
    {
        return $this->belongsToMany(Item::class)->withPivot(
            'quantity',
            'price',
            'comment'
        );
    }

    public function ressources()
    {
        return $this->belongsToMany(Ressource::class)->withPivot(
            'quantity',
            'price',
            'comment'
        );
    }

    public function npc()
    {
        return $this->belongsTo(Npc::class);
    }
}
