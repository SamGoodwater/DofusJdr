<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCreature
 */
class Creature extends Model
{
    const HOSTILITY = [
        "amicale" => 0,
        "currieux" => 1,
        "neutre" => 2,
        "perreux" => 3,
        "agressif" => 4,
        "hostile" => 5
    ];

    protected $fillable = [
        'uniqid',
        'name',
        'description',
        'location',
        'level',
        'other_info',
        'life',
        'pa',
        'pm',
        'po',
        'ini',
        'invocation',
        'touch',
        'ca',
        'dodge_pa',
        'dodge_pm',
        'fuite',
        'tacle',
        'vitality',
        'sagesse',
        'strong',
        'intel',
        'agi',
        'chance',
        'do_fixe_neutre',
        'do_fixe_terre',
        'do_fixe_feu',
        'do_fixe_air',
        'do_fixe_eau',
        'res_neutre',
        'res_terre',
        'res_feu',
        'res_air',
        'res_eau',
        'acrobatie_bonus',
        'discretion_bonus',
        'escamotage_bonus',
        'athletisme_bonus',
        'intimidation_bonus',
        'arcane_bonus',
        'histoire_bonus',
        'investigation_bonus',
        'nature_bonus',
        'religion_bonus',
        'dressage_bonus',
        'medecine_bonus',
        'perception_bonus',
        'perspicacite_bonus',
        'survie_bonus',
        'persuasion_bonus',
        'representation_bonus',
        'supercherie_bonus',
        'acrobatie_mastery',
        'discretion_mastery',
        'escamotage_mastery',
        'athletisme_mastery',
        'intimidation_mastery',
        'arcane_mastery',
        'histoire_mastery',
        'investigation_mastery',
        'nature_mastery',
        'religion_mastery',
        'dressage_mastery',
        'medecine_mastery',
        'perception_mastery',
        'perspicacite_mastery',
        'survie_mastery',
        'persuasion_mastery',
        'representation_mastery',
        'supercherie_mastery',
        'kamas',
        'drop_',
        'other_item',
        'other_consumable',
        'other_spell',
        'usable'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function ressources(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Ressource::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function capabilities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Capability::class);
    }

    public function consumables(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Consumable::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }

    public function spells(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Spell::class);
    }

    public function attributes(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function scenarios(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Scenario::class);
    }
}
