<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSpecialization
 */
class Specialization extends Model
{
    protected $fillable = ['uniqid', 'name', 'description'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function capabilities()
    {
        return $this->belongsToMany(Capability::class);
    }

    public function npcs()
    {
        return $this->hasMany(Npc::class);
    }
}
