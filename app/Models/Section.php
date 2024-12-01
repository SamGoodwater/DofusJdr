<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperSection
 */
class Section extends Model
{
    protected $fillable = ['uniqid', 'component', 'title', 'content', 'order_num', 'visible', 'page_id'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
