<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRessourcetype
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
