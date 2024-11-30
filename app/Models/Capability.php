<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCapability
 */
class Capability extends Model
{
    protected $fillable = ['name', 'description', 'effect', 'level', 'pa', 'po', 'po_editable', 'time_before_use_again', 'casting_time', 'duration', 'element', 'is_magic', 'ritual_available', 'powerful', 'usable'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function specializations()
    {
        return $this->belongsToMany(Specialization::class);
    }

    public function classes()
    {
        return $this->belongsToMany(Classe::class);
    }

    public function mobs()
    {
        return $this->belongsToMany(Mob::class);
    }

    public function npcs()
    {
        return $this->belongsToMany(Npc::class);
    }
}
