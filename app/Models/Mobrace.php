<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMobrace
 */
class Mobrace extends Model
{
    protected $fillable = [
        'name',
        'super_race',
        'uniqid'
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function mobs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Mob::class);
    }
}
