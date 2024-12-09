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


namespace App\Models\Modules{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property string|null $description
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Classe> $classes
 * @property-read int|null $classes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAttribute {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property string|null $keyword
 * @property int $is_public
 * @property int $state
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Page> $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spell> $spells
 * @property-read int|null $spells_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCampaign {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Classe> $classes
 * @property-read int|null $classes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Specialization> $specializations
 * @property-read int|null $specializations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCapability {}
}

namespace App\Models\Modules{
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
 * @property int $usable
 * @property string $dofus_version
 * @property int $is_visible
 * @property string|null $image
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Attribute> $attributes
 * @property-read int|null $attributes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereDescriptionFast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereLife($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereLifeDice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereSpecificity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classe withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperClasse {}
}

namespace App\Models\Modules{
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
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIsMalus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIsUnbewitchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCondition {}
}

namespace App\Models\Modules{
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
 * @property string|null $effect
 * @property int|null $level
 * @property string|null $recepe
 * @property string|null $price
 * @property int $rarity
 * @property int $usable
 * @property string $dofus_version
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $consumabletype_id
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \App\Models\Modules\Consumabletype|null $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereConsumabletypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereRecepe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConsumable {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConsumabletype {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property int $hostility
 * @property string|null $location
 * @property int $level
 * @property string|null $other_info
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
 * @property string $res_fixe_neutre
 * @property string $res_fixe_terre
 * @property string $res_fixe_feu
 * @property string $res_fixe_air
 * @property string $res_fixe_eau
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
 * @property string|null $other_ressource
 * @property string|null $other_spell
 * @property int $usable
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \App\Models\Modules\Attribute|null $attributes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAcrobatieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAcrobatieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAgi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereArcaneBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereArcaneMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAthletismeBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAthletismeMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereCa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereChance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDiscretionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDiscretionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeEau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeFeu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeNeutre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeTerre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDodgePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDodgePm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDressageBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDressageMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDrop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereEscamotageBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereEscamotageMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereFuite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHistoireBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHistoireMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHostility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIntel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIntimidationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIntimidationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereInvestigationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereInvestigationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereInvocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereKamas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereLife($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereMedecineBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereMedecineMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereNatureBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereNatureMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherConsumable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherRessource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherSpell($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePerceptionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePerceptionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePerspicaciteBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePerspicaciteMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePersuasionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePersuasionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereReligionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereReligionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereRepresentationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereRepresentationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResEau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFeu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeEau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeFeu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeNeutre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeTerre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResNeutre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResTerre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSagesse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereStrong($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSupercherieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSupercherieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSurvieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSurvieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereTacle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereTouch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereVitality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCreature {}
}

namespace App\Models\Modules{
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
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $itemtype_id
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Panoply> $panoply
 * @property-read int|null $panoply_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \App\Models\Modules\Itemtype|null $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereItemtypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRecepe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereTwohands($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperItem {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperItemtype {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int|null $creature_id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $dofus_version
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
	#[\AllowDynamicProperties]
	class IdeHelperMob {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $name
 * @property string|null $super_race
 * @property int $is_visible
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereSuperRace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMobrace {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int|null $creature_id
 * @property string|null $story
 * @property string|null $historical
 * @property string|null $age
 * @property string|null $size
 * @property int|null $classe_id
 * @property int|null $specialization_id
 * @property-read \App\Models\Modules\Attribute|null $attributes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \App\Models\Modules\Classe|null $classe
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \App\Models\Modules\Specialization|null $specialization
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereClasseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereCreatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereHistorical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSpecializationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereStory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNpc {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property string|null $description
 * @property string|null $bonus
 * @property int $usable
 * @property int $is_visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPanoply {}
}

namespace App\Models\Modules{
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
 * @property string|null $price
 * @property string|null $weight
 * @property int $rarity
 * @property int $usable
 * @property string $dofus_version
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $ressourcetype_id
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \App\Models\Modules\Ressourcetype|null $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereRessourcetypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressource withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRessource {}
}

namespace App\Models\Modules{
/**
 * 
 *
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
	#[\AllowDynamicProperties]
	class IdeHelperRessourcetype {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property string|null $keyword
 * @property int $is_public
 * @property int $state
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Page> $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spell> $spells
 * @property-read int|null $spells_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperScenario {}
}

namespace App\Models\Modules{
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
 * @property int $usable
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $npc_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Modules\Npc|null $npc
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereNpcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereUsable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperShop {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property string|null $description
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $page_id
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \App\Models\Page|null $page
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSpecialization {}
}

namespace App\Models\Modules{
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
 * @property int $is_visible
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $invocations
 * @property-read int|null $invocations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spelltype> $spelltypes
 * @property-read int|null $spelltypes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastPerTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastPerTurn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereEffectArray($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereElement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereIsMagic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereIsVisible($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSpell {}
}

namespace App\Models\Modules{
/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property string $color
 * @property string|null $icon
 * @property int $is_visible
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSpelltype {}
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
 * @property int $is_public
 * @property int $is_visible
 * @property int $is_editable
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $page_id
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Page|null $page
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @property-read \App\Models\Modules\Specialization|null $specialization
 * @method static \Database\Factories\PageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsDropdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereOrderNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withoutTrashed()
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
 * @property string $uniqid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $component
 * @property string|null $title
 * @property string|null $content
 * @property int $order_num
 * @property int $is_visible
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $page_id
 * @property int|null $created_by
 * @property-read \App\Models\Page|null $page
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereComponent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereOrderNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section withoutTrashed()
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
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

