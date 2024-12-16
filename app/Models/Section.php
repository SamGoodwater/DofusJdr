<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * 
 *
 * @mixin IdeHelperSection
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $component
 * @property string|null $title
 * @property string|null $content
 * @property int $order_num
 * @property int $is_visible
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $page_id
 * @property int|null $created_by
 * @property-read \App\Models\Page|null $page
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereComponent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereOrderNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section withoutTrashed()
 * @mixin \Eloquent
 */
class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uniqid',
        'component',
        'title',
        'content',
        'order_num',
        'is_visible',
        'page_id',
        'is_visible',
        'created_by'
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getPathFiles()
    {
        return \DB::table('file_section')
            ->where('section_id', $this->id)
            ->pluck('file');
    }

    public function setPathFiles(array|string|null $files): void
    {
        if (!$files) {
            return;
        }
        $files = is_array($files) ? $files : [$files];

        $data = array_map(function ($file) {
            return [
                'section_id' => $this->id,
                'file' => $file
            ];
        }, $files);

        \DB::table('file_section')->insert($data);
    }

    public function filesPath(): \Illuminate\Support\Collection
    {
        $files = $this->getPathFiles();
        $files = $files->map(function ($file) {
            return Storage::disk('modules')->url($file);
        });
        return $files;
    }
}
