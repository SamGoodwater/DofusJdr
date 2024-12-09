<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Page;
use App\Models\User;

/**
 * @mixin IdeHelperScenario
 */
class Scenario extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'keyword',
        'slug',
        'state',
        'is_public',
        'uniqid',
        'is_visible',
        'created_by',
        'image'
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function getPathFiles()
    {
        return \DB::table('file_scenario')
            ->where('scenario_id', $this->id)
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
                'scenario_id' => $this->id,
                'file' => $file
            ];
        }, $files);

        \DB::table('file_scenario')->insert($data);
    }

    public function pages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Page::class);
    }

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function mobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mob::class);
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Npc::class);
    }

    public function spells(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Spell::class);
    }

    public function shops(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Shop::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }

    public function ressources(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Ressource::class);
    }

    public function consumables(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Consumable::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function panoplies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Panoply::class);
    }

    public function imagePath(): string
    {
        return Storage::disk('modules')->url($this->image);
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
