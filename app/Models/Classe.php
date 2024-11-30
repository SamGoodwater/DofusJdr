<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperClasse
 */
class Classe extends Model
{
    protected $fillable = ['official_id', 'dofusdb_id', 'uniqid', 'name', 'description_fast', 'description', 'life', 'life_dice', 'specificity', 'weapons_of_choice', 'trait', 'usable', 'dofus_version'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function spells()
    {
        return $this->belongsToMany(Spell::class);
    }

    public function capabilities()
    {
        return $this->belongsToMany(Capability::class);
    }
}
