<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperCreature
 */
class Creature extends Model
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
        'res_fixe_neutre',
        'res_fixe_terre',
        'res_fixe_feu',
        'res_fixe_air',
        'res_fixe_eau',
        'res_neutre', // 0 = 0%, 1 = 50%, 2 = 100%, -1 = -50%, -2 = -100%, -3 = -150%, -4 = -200%
        'res_terre', // 0 = 0%, 1 = 50%, 2 = 100%, -1 = -50%, -2 = -100%, -3 = -150%, -4 = -200%
        'res_feu', // 0 = 0%, 1 = 50%, 2 = 100%, -1 = -50%, -2 = -100%, -3 = -150%, -4 = -200%
        'res_air', // 0 = 0%, 1 = 50%, 2 = 100%, -1 = -50%, -2 = -100%, -3 = -150%, -4 = -200%
        'res_eau', // 0 = 0%, 1 = 50%, 2 = 100%, -1 = -50%, -2 = -100%, -3 = -150%, -4 = -200%
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
        'acrobatie_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'discretion_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'escamotage_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'athletisme_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'intimidation_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'arcane_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'histoire_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'investigation_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'nature_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'religion_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'dressage_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'medecine_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'perception_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'perspicacite_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'survie_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'persuasion_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'representation_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'supercherie_mastery', // 0 = pas de maitrise, 1 = maitrise, 2 = expertise
        'kamas',
        'drop_',
        'other_item',
        'other_consumable',
        'other_spell',
        'usable',
        'is_visible',
        'created_by',
        'image'
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
