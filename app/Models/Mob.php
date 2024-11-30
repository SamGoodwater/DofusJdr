<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMob
 */
class Mob extends Model
{
    protected $fillable = ['official_id', 'dofusdb_id', 'uniqid', 'name', 'description', 'level', 'life', 'vitality', 'pa', 'pm', 'po', 'ini', 'touch', 'sagesse', 'strong', 'intel', 'agi', 'chance', 'do_fixe_neutre', 'do_fixe_terre', 'do_fixe_feu', 'do_fixe_air', 'do_fixe_eau', 'do_fixe_multiple', 'ca', 'fuite', 'tacle', 'dodge_pa', 'dodge_pm', 'res_neutre', 'res_terre', 'res_feu', 'res_air', 'res_eau', 'acrobatie_bonus', 'discretion_bonus', 'escamotage_bonus', 'athletisme_bonus', 'intimidation_bonus', 'arcane_bonus', 'histoire_bonus', 'investigation_bonus', 'nature_bonus', 'religion_bonus', 'dressage_bonus', 'medecine_bonus', 'perception_bonus', 'perspicacite_bonus', 'survie_bonus', 'persuasion_bonus', 'representation_bonus', 'supercherie_bonus', 'acrobatie_mastery', 'discretion_mastery', 'escamotage_mastery', 'athletisme_mastery', 'intimidation_mastery', 'arcane_mastery', 'histoire_mastery', 'investigation_mastery', 'nature_mastery', 'religion_mastery', 'dressage_mastery', 'medecine_mastery', 'perception_mastery', 'perspicacite_mastery', 'survie_mastery', 'persuasion_mastery', 'representation_mastery', 'supercherie_mastery', 'location', 'hostility', 'size', 'trait', 'kamas', 'drop_', 'other_info', 'other_item', 'other_consumable', 'other_spell', 'usable', 'dofus_version'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function Ressources()
    {
        return $this->belongsToMany(Ressource::class)->withPivot('quantity'); // Voir si on garde withPivot (Set the columns on the pivot table to retrieve. : Définissez les colonnes du tableau croisé dynamique à récupérer.)
    }

    public function Capabilities()
    {
        return $this->belongsToMany(Capability::class);
    }

    public function Consumables()
    {
        return $this->belongsToMany(Consumable::class);
    }

    public function Items()
    {
        return $this->belongsToMany(Item::class);
    }

    public function Spells()
    {
        return $this->belongsToMany(Spell::class);
    }

    public function Invocation_spells()
    {
        return $this->belongsToMany(Spell::class, 'spell_invocation');
    }
}
