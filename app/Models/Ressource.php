<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRessource
 */
class Ressource extends Model
{
    protected $fillable = ['official_id', 'dofusdb_id', 'uniqid', 'name', 'description', 'level', 'type', 'price', 'weight', 'rarity', 'usable', 'dofus_version'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function mobs()
    {
        return $this->belongsToMany(Mob::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function items()
    {
        return $this->belongsToMany(Item::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function consumables()
    {
        return $this->belongsToMany(Consumable::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function npcs()
    {
        return $this->belongsToMany(Npc::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
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
