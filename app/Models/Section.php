<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSection
 */
class Section extends Model
{
    protected $fillable = ['uniqid', 'component', 'title', 'content', 'order_num', 'visible', 'page_id'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
