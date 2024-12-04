<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperNpc
 */
class Npc extends Creature
{
    use HasFactory, SoftDeletes;

    protected $fillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge(
            (new Creature())->getFillable(),
            [
                'classe_id',
                'story',
                'historical',
                'age',
                'size',
                'specialization_id'
            ]
        );
    }

    public function specialization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function classe(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function shops(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Shop::class);
    }
}
