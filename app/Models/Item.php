<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperItem
 */
class Item extends Model
{
    protected $fillable = ['official_id', 'dofusdb_id', 'uniqid', 'name', 'level', 'description', 'type', 'effect', 'bonus', 'recepe', 'actif', 'twohands', 'pa', 'po', 'price', 'rarity', 'usable', 'dofus_version'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function ressources(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Ressource::class)->withPivot('quantity');  // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function mobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mob::class);
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Npc::class);
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
