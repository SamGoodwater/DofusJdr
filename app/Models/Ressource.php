<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperRessource
 */
class Ressource extends Model
{
    protected $fillable = ['official_id', 'dofusdb_id', 'uniqid', 'name', 'description', 'level', 'type', 'price', 'weight', 'rarity', 'usable', 'dofus_version'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'created_by'];

    public function mobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mob::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function consumables(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Consumable::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Npc::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function shops(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Shop::class)->withPivot(
            'quantity',
            'price',
            'comment'
        );
    }

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function scenarios(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Scenario::class);
    }
}
