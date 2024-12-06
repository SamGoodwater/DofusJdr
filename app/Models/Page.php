<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperPage
 */
class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'keyword',
        'slug',
        'order_num',
        "page_id",
        'is_dropdown',
        'is_public',
        "is_visible",
        'is_editable',
        "page_id",
        "uniqid",
        'is_visible',
        'created_by',

    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];


    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function sections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function scenarios(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Scenario::class);
    }

    public function specialization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
