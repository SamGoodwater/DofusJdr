<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCondition
 */
class Condition extends Model
{
    protected $fillable = ['name', 'description', 'is_unbewitchable', 'is_malus', 'usable'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    //
}
