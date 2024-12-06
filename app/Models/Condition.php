<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperCondition
 */
class Condition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'is_unbewitchable', 'is_malus', 'usable', 'is_visible', 'created_by', 'image'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    //

    public function imagePath(): string
    {
        return Storage::disk('modules')->url($this->image);
    }
}
