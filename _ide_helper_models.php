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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condition query()
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Consumable> $consumables
 * @property-read int|null $consumables_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumabletype query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConsumabletype {}
}

namespace App\Models\Modules{
/**
 * 
 *
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Item> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Itemtype query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperItemtype {}
}

namespace App\Models\Modules{
/**
 * 
 *
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Mob> $mobs
 * @property-read int|null $mobs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mobrace query()
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Ressource> $ressources
 * @property-read int|null $ressources_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ressourcetype query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRessourcetype {}
}

namespace App\Models\Modules{
/**
 * 
 *
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \App\Models\Page|null $page
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization query()
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spelltype query()
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

