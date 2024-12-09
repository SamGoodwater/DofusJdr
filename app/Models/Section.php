<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperSection
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
