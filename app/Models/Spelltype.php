<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperSpelltype
 */
class Spelltype extends Model
{
    protected $fillable = ['name', 'description', "color", "icon"];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'created_by'];

    public function spells(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Spell::class);
    }
}
