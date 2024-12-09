<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperMob
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
