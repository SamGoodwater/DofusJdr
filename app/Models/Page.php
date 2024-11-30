<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPage
 */
class Page extends Model
{
    protected $fillable = ['name', 'keyword', 'slug', 'order_num', "page_id", 'is_dropdown', 'public', 'is_editable', "page_id", "uniqid"];
    protected $hidden = ['id', 'created_at', 'updated_at'];


    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
