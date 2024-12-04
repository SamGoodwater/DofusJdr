<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperClasse
 */
class Classe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['official_id', 'dofusdb_id', 'uniqid', 'name', 'description_fast', 'description', 'life', 'life_dice', 'specificity', 'weapons_of_choice', 'usable', 'dofus_version'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'created_by'];

    public function spells(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Spell::class);
    }

    public function capabilities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Capability::class);
    }

    public function attributes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Attribute::class);
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Npc::class);
    }
}
