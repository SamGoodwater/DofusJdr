<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCapability
 */
class Capability extends Model
{
    protected $fillable = ['name', 'description', 'effect', 'level', 'pa', 'po', 'po_editable', 'time_before_use_again', 'casting_time', 'duration', 'element', 'is_magic', 'ritual_available', 'powerful', 'usable'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'created_by'];

    public function specializations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Specialization::class);
    }

    public function classes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Classe::class);
    }

    public function mobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mob::class);
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Npc::class);
    }
}
