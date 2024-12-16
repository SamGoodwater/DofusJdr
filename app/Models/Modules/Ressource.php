<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * 
 *
 * @mixin IdeHelperRessource
 * @property int $id
 * @property string|null $dofusdb_id
 * @property int|null $official_id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property int $level
 * @property string|null $price
 * @property string|null $weight
 * @property int $rarity
 * @property int $usable
 * @property string $dofus_version
 * @property int $is_visible
 * @property string|null $image
 * @property int $auto_update
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $ressourcetype_id
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \App\Models\Modules\Ressourcetype|null $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereRessourcetypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource withoutTrashed()
 * @mixin \Eloquent
 */
class Ressource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'official_id',
        'dofusdb_id',
        'uniqid',
        'name',
        'description',
        'level',
        'price',
        'weight',
        'rarity',
        'usable',
        'dofus_version',
        'is_visible',
        'created_by',
        'image',
        'ressourcetpe_id',
        'auto_update',
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Ressourcetype::class);
    }
    public function mobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mob::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function consumables(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Consumable::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Npc::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function shops(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Shop::class)->withPivot(
            'quantity',
            'price',
            'comment'
        );
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
