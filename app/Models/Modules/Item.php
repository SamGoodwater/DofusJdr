<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Modules\Itemtype;

/**
 * @mixin IdeHelperItem
 */
class Item extends Model
{
    use HasFactory, SoftDeletes;

    const RARITIES = [
        "Unique" => 0,
        "Mythique" => 1,
        "Rare" => 2,
        "Inhabituel" => 3,
        "Commun" => 4,
        "Très répandu" => 5
    ];

    protected $fillable = [
        'official_id',
        'dofusdb_id',
        'uniqid',
        'name',
        'level',
        'description',
        'effect',
        'bonus',
        'recepe',
        'price',
        'rarity',
        'usable',
        'dofus_version',
        'is_visible',
        'created_by',
        'image',
        'itemtype_id'
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Itemtype::class);
    }

    public function ressources(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Ressource::class)->withPivot('quantity');  // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function mobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mob::class);
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Npc::class);
    }

    public function shops(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Shop::class)->withPivot(
            'quantity',
            'price',
            'comment'
        );
    }

    public function panoply(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Panoply::class);
    }

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function scenarios(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Scenario::class);
    }

    public function imagePath(): string
    {
        return Storage::disk('modules')->url($this->image);
    }
}
