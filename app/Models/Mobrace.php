<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperMobrace
 */
class Mobrace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'super_race',
        'uniqid',
        'is_visible',
        'created_by'
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function mobs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Mob::class);
    }
}
