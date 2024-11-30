<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spelltype extends Model
{
    protected $fillable = ['name', 'description', "color"];
    protected $hidden = ['created_at', 'updated_at'];

    public function spells()
    {
        return $this->belongsToMany(Spell::class);
    }
}
