<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperConsumable
 */
class Consumable extends Model
{
    protected $fillable = ['official_id', 'dofusdb_id', 'uniqid', 'type', 'name', 'description', 'effect', 'level', 'recepe', 'price', 'rarity', 'usable', 'dofus_version'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function ressources()
    {
        return $this->belongsToMany(Ressource::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function mobs()
    {
        return $this->belongsToMany(Mob::class);
    }

    public function npcs()
    {
        return $this->belongsToMany(Npc::class);
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class)->withPivot(
            'quantity',
            'price',
            'comment'
        );
    }
}
