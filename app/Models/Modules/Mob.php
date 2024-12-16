<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @mixin IdeHelperMob
 * @property int|null $creature_id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $dofus_version
 * @property int $auto_update
 * @property int $size
 * @property int|null $mobrace_id
 * @property-read \App\Models\Modules\Attribute|null $attributes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spell> $invocation_spells
 * @property-read int|null $invocation_spells_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Modules\Mobrace|null $mobrace
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereCreatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereMobraceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob withoutTrashed()
 * @mixin \Eloquent
 */
class Mob extends Creature
{
    use HasFactory, SoftDeletes;

    const HOSTILITY = [
        "amicale" => 0,
        "currieux" => 1,
        "neutre" => 2,
        "perreux" => 3,
        "agressif" => 4,
        "hostile" => 5
    ];

    const SIZE = [
        "très petite" => 0,
        "petite" => 1,
        "moyenne" => 2,
        "grande" => 3,
        "très grande" => 4,
        "gigantesque" => 5
    ];

    protected $fillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge(
            (new Creature())->getFillable(),
            [
                'official_id',
                'dofusdb_id',
                'size',
                'dofus_version',
                'auto_update',
            ]
        );
    }

    public function invocation_spells(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Spell::class, 'spell_invocation');
    }

    public function mobrace(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MobRace::class);
    }
}
