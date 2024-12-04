<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperSpecialization
 */
class Specialization extends Model
{
    protected $fillable = ['uniqid', 'name', 'description'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'created_by'];

    public function capabilities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Capability::class);
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Npc::class);
    }
}
