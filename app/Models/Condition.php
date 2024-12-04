<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperCondition
 */
class Condition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'is_unbewitchable', 'is_malus', 'usable'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'created_by'];

    //
}
