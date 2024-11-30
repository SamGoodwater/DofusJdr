<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperNpc
 */
class Npc extends Model
{
    protected $fillable = ['uniqid', 'name', 'classe', 'description', 'location', 'story', 'historical', 'level', 'trait', 'other_info', 'age', 'size', 'life', 'pa', 'pm', 'po', 'ini', 'invocation', 'touch', 'ca', 'dodge_pa', 'dodge_pm', 'fuite', 'tacle', 'vitality', 'sagesse', 'strong', 'intel', 'agi', 'chance', 'do_fixe_neutre', 'do_fixe_terre', 'do_fixe_feu', 'do_fixe_air', 'do_fixe_eau', 'res_neutre', 'res_terre', 'res_feu', 'res_air', 'res_eau', 'acrobatie_bonus', 'discretion_bonus', 'escamotage_bonus', 'athletisme_bonus', 'intimidation_bonus', 'arcane_bonus', 'histoire_bonus', 'investigation_bonus', 'nature_bonus', 'religion_bonus', 'dressage_bonus', 'medecine_bonus', 'perception_bonus', 'perspicacite_bonus', 'survie_bonus', 'persuasion_bonus', 'representation_bonus', 'supercherie_bonus', 'acrobatie_mastery', 'discretion_mastery', 'escamotage_mastery', 'athletisme_mastery', 'intimidation_mastery', 'arcane_mastery', 'histoire_mastery', 'investigation_mastery', 'nature_mastery', 'religion_mastery', 'dressage_mastery', 'medecine_mastery', 'perception_mastery', 'perspicacite_mastery', 'survie_mastery', 'persuasion_mastery', 'representation_mastery', 'supercherie_mastery', 'kamas', 'drop_', 'other_item', 'other_consumable', 'other_spell', 'usable', 'specialization_id'];
    protected $hidden = ['id', 'created_at', 'updated_at'];


    public function Items()
    {
        return $this->belongsToMany(Item::class);
    }

    public function Consumables()
    {
        return $this->belongsToMany(Consumable::class);
    }

    public function Capabilities()
    {
        return $this->belongsToMany(Capability::class);
    }

    public function Ressources()
    {
        return $this->belongsToMany(Ressource::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function Spells()
    {
        return $this->belongsToMany(Spell::class);
    }

    public function Specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function Shops()
    {
        return $this->hasMany(Shop::class);
    }
}
