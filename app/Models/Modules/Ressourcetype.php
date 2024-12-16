<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @mixin IdeHelperRessourcetype
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ressourcetype extends Model
{
    protected $fillable = [
        'name',
        'uniqid',
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function ressources()
    {
        return $this->hasMany(Ressource::class, 'ressourcetpe_id', 'id');
    }
}
