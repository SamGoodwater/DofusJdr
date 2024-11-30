<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $description
 * @property string $effect
 * @property int $level
 * @property string|null $pa
 * @property string $po
 * @property int $po_editable
 * @property string|null $time_before_use_again
 * @property string|null $casting_time
 * @property string|null $duration
 * @property int $element
 * @property int $is_magic
 * @property int $ritual_available
 * @property int $powerful
 * @property int $usable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Classe> $classes
 * @property-read int|null $classes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Specialization> $specializations
 * @property-read int|null $specializations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereCastingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereElement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereIsMagic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability wherePo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability wherePoEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability wherePowerful($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereRitualAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereTimeBeforeUseAgain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereUsable($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCapability {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description_fast
 * @property string|null $description
 * @property string|null $life
 * @property int $life_dice
 * @property string|null $specificity
 * @property int|null $weapons_of_choice
 * @property string|null $trait
 * @property int $usable
 * @property string $dofus_version
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereDescriptionFast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereLife($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereLifeDice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereSpecificity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereTrait($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereWeaponsOfChoice($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperClasse {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property int $is_unbewitchable
 * @property int $is_malus
 * @property int $usable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIsMalus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIsUnbewitchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereUsable($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCondition {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $type
 * @property string $name
 * @property string|null $description
 * @property string|null $effect
 * @property int|null $level
 * @property string|null $recepe
 * @property string|null $price
 * @property int $rarity
 * @property int $usable
 * @property string $dofus_version
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereRecepe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereUsable($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConsumable {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property int $level
 * @property string|null $description
 * @property int $type
 * @property string $effect
 * @property string|null $bonus
 * @property string|null $recepe
 * @property string $actif
 * @property string $twohands
 * @property string $pa
 * @property string $po
 * @property string|null $price
 * @property int $rarity
 * @property int $usable
 * @property string $dofus_version
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRecepe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereTwohands($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUsable($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperItem {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property string $level
 * @property string $life
 * @property string $vitality
 * @property string $pa
 * @property string $pm
 * @property string $po
 * @property string $ini
 * @property string|null $touch
 * @property string $sagesse
 * @property string $strong
 * @property string $intel
 * @property string $agi
 * @property string $chance
 * @property string $do_fixe_neutre
 * @property string $do_fixe_terre
 * @property string $do_fixe_feu
 * @property string $do_fixe_air
 * @property string $do_fixe_eau
 * @property string $do_fixe_multiple
 * @property string $ca
 * @property string $fuite
 * @property string $tacle
 * @property string $dodge_pa
 * @property string $dodge_pm
 * @property string $res_neutre
 * @property string $res_terre
 * @property string $res_feu
 * @property string $res_air
 * @property string $res_eau
 * @property int $acrobatie_bonus
 * @property int $discretion_bonus
 * @property int $escamotage_bonus
 * @property int $athletisme_bonus
 * @property int $intimidation_bonus
 * @property int $arcane_bonus
 * @property int $histoire_bonus
 * @property int $investigation_bonus
 * @property int $nature_bonus
 * @property int $religion_bonus
 * @property int $dressage_bonus
 * @property int $medecine_bonus
 * @property int $perception_bonus
 * @property int $perspicacite_bonus
 * @property int $survie_bonus
 * @property int $persuasion_bonus
 * @property int $representation_bonus
 * @property int $supercherie_bonus
 * @property int $acrobatie_mastery
 * @property int $discretion_mastery
 * @property int $escamotage_mastery
 * @property int $athletisme_mastery
 * @property int $intimidation_mastery
 * @property int $arcane_mastery
 * @property int $histoire_mastery
 * @property int $investigation_mastery
 * @property int $nature_mastery
 * @property int $religion_mastery
 * @property int $dressage_mastery
 * @property int $medecine_mastery
 * @property int $perception_mastery
 * @property int $perspicacite_mastery
 * @property int $survie_mastery
 * @property int $persuasion_mastery
 * @property int $representation_mastery
 * @property int $supercherie_mastery
 * @property string|null $location
 * @property int $hostility
 * @property int $size
 * @property string|null $trait
 * @property string|null $kamas
 * @property string|null $drop_
 * @property string|null $other_info
 * @property string|null $other_item
 * @property string|null $other_consumable
 * @property string|null $other_spell
 * @property int $usable
 * @property string $dofus_version
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Capability> $Capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Consumable> $Consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $Items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ressource> $Ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Spell> $Spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereAcrobatieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereAcrobatieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereAgi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereArcaneBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereArcaneMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereAthletismeBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereAthletismeMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereCa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereChance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDiscretionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDiscretionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDoFixeAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDoFixeEau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDoFixeFeu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDoFixeMultiple($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDoFixeNeutre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDoFixeTerre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDodgePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDodgePm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDressageBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDressageMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDrop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereEscamotageBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereEscamotageMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereFuite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereHistoireBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereHistoireMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereHostility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereIni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereIntel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereIntimidationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereIntimidationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereInvestigationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereInvestigationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereKamas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereLife($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereMedecineBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereMedecineMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereNatureBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereNatureMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereOtherConsumable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereOtherInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereOtherItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereOtherSpell($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob wherePerceptionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob wherePerceptionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob wherePerspicaciteBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob wherePerspicaciteMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob wherePersuasionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob wherePersuasionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob wherePm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob wherePo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereReligionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereReligionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereRepresentationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereRepresentationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereResAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereResEau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereResFeu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereResNeutre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereResTerre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereSagesse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereStrong($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereSupercherieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereSupercherieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereSurvieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereSurvieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereTacle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereTouch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereTrait($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereVitality($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMob {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $classe
 * @property string|null $description
 * @property string|null $location
 * @property string|null $story
 * @property string|null $historical
 * @property int $level
 * @property string|null $trait
 * @property string|null $other_info
 * @property string|null $age
 * @property string|null $size
 * @property int $life
 * @property int $pa
 * @property int $pm
 * @property int $po
 * @property int $ini
 * @property int $invocation
 * @property int $touch
 * @property int $ca
 * @property int $dodge_pa
 * @property int $dodge_pm
 * @property int $fuite
 * @property int $tacle
 * @property int $vitality
 * @property int $sagesse
 * @property int $strong
 * @property int $intel
 * @property int $agi
 * @property int $chance
 * @property string $do_fixe_neutre
 * @property string $do_fixe_terre
 * @property string $do_fixe_feu
 * @property string $do_fixe_air
 * @property string $do_fixe_eau
 * @property int $res_neutre
 * @property int $res_terre
 * @property int $res_feu
 * @property int $res_air
 * @property int $res_eau
 * @property int $acrobatie_bonus
 * @property int $discretion_bonus
 * @property int $escamotage_bonus
 * @property int $athletisme_bonus
 * @property int $intimidation_bonus
 * @property int $arcane_bonus
 * @property int $histoire_bonus
 * @property int $investigation_bonus
 * @property int $nature_bonus
 * @property int $religion_bonus
 * @property int $dressage_bonus
 * @property int $medecine_bonus
 * @property int $perception_bonus
 * @property int $perspicacite_bonus
 * @property int $survie_bonus
 * @property int $persuasion_bonus
 * @property int $representation_bonus
 * @property int $supercherie_bonus
 * @property int $acrobatie_mastery
 * @property int $discretion_mastery
 * @property int $escamotage_mastery
 * @property int $athletisme_mastery
 * @property int $intimidation_mastery
 * @property int $arcane_mastery
 * @property int $histoire_mastery
 * @property int $investigation_mastery
 * @property int $nature_mastery
 * @property int $religion_mastery
 * @property int $dressage_mastery
 * @property int $medecine_mastery
 * @property int $perception_mastery
 * @property int $perspicacite_mastery
 * @property int $survie_mastery
 * @property int $persuasion_mastery
 * @property int $representation_mastery
 * @property int $supercherie_mastery
 * @property string|null $kamas
 * @property string|null $drop_
 * @property string|null $other_item
 * @property string|null $other_consumable
 * @property string|null $other_spell
 * @property int $usable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Capability> $Capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Consumable> $Consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $Items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ressource> $Ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shop> $Shops
 * @property-read int|null $shops_count
 * @property-read \App\Models\Specialization|null $Specialization
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Spell> $Spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereAcrobatieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereAcrobatieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereAgi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereArcaneBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereArcaneMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereAthletismeBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereAthletismeMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereCa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereChance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereClasse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDiscretionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDiscretionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDoFixeAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDoFixeEau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDoFixeFeu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDoFixeNeutre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDoFixeTerre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDodgePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDodgePm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDressageBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDressageMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereDrop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereEscamotageBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereEscamotageMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereFuite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereHistoireBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereHistoireMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereHistorical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereIni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereIntel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereIntimidationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereIntimidationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereInvestigationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereInvestigationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereInvocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereKamas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereLife($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereMedecineBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereMedecineMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereNatureBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereNatureMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereOtherConsumable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereOtherInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereOtherItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereOtherSpell($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc wherePerceptionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc wherePerceptionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc wherePerspicaciteBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc wherePerspicaciteMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc wherePersuasionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc wherePersuasionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc wherePm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc wherePo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereReligionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereReligionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereRepresentationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereRepresentationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereResAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereResEau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereResFeu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereResNeutre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereResTerre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSagesse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereStory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereStrong($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSupercherieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSupercherieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSurvieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSurvieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereTacle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereTouch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereTrait($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereVitality($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNpc {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $keyword
 * @property string $slug
 * @property int $order_num
 * @property int $is_dropdown
 * @property int $public
 * @property int $is_editable
 * @property int|null $page_id
 * @property-read Page|null $page
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsDropdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereOrderNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPage {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $dofusdb_id
 * @property int|null $official_id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property int $level
 * @property int $type
 * @property string|null $price
 * @property string|null $weight
 * @property int $rarity
 * @property int $usable
 * @property string $dofus_version
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereWeight($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRessource {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $component
 * @property string|null $title
 * @property string|null $content
 * @property int $order_num
 * @property int $visible
 * @property int|null $page_id
 * @property-read \App\Models\Page|null $page
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereComponent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereOrderNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereVisible($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSection {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property string|null $location
 * @property int $price
 * @property int|null $id_seller
 * @property int $usable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Npc|null $npc
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereIdSeller($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereUsable($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperShop {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Npc> $npcs
 * @property-read int|null $npcs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSpecialization {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $description
 * @property string $effect
 * @property string|null $effect_array
 * @property int $area
 * @property int $level
 * @property string $po
 * @property int $po_editable
 * @property string $pa
 * @property string $cast_per_turn
 * @property int $cast_per_target
 * @property int $sight_line
 * @property string $number_between_two_cast
 * @property int $element
 * @property int $category
 * @property int $is_magic
 * @property int $powerful
 * @property int $usable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Npc> $npcs
 * @property-read int|null $npcs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastPerTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastPerTurn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereEffectArray($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereElement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereIsMagic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereNumberBetweenTwoCast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePowerful($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSightLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereUsable($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSpell {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $rights
 * @property int $is_admin
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

