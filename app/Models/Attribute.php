<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperAttribute
 */
class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['uniqid', 'name', 'description'];
    protected $hidden = ["id", 'created_at', 'updated_at', 'deleted_at', 'created_by'];

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
