<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSpell
 */
class Spell extends Model
{
    protected $fillable = ['official_id', 'dofusdb_id', 'uniqid', 'name', 'description', 'effect', 'effect_array', 'area', 'level', 'po', 'po_editable', 'pa', 'cast_per_turn', 'cast_per_target', 'sight_line', 'number_between_two_cast', 'element', 'category', 'is_magic', 'powerful', 'usable'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function mobs()
    {
        return $this->belongsToMany(Mob::class);
    }

    public function npcs()
    {
        return $this->belongsToMany(Npc::class);
    }

    public function spelltypes()
    {
        return $this->belongsToMany(Spelltype::class);
    }

    public function invocations()
    {
        return $this->belongsToMany(Mob::class, 'spell_invocation');
    }
}
