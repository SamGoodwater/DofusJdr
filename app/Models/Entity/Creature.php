<?php

namespace App\Models\Entity;

use App\Models\Concerns\HasEntityImageMedia;
use App\Models\Concerns\VisibleToViewer;
use App\Models\User;
use App\Support\Creature\CreatureComposableColumns;
use Database\Factories\CreatureFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $hostility
 * @property string|null $location
 * @property string $level
 * @property string|null $other_info
 * @property string $life
 * @property string $pa
 * @property string $pm
 * @property string $po
 * @property string $ini
 * @property string $invocation
 * @property string $touch
 * @property string $ca
 * @property string $dodge_pa
 * @property string $dodge_pm
 * @property string $fuite
 * @property string $tacle
 * @property string $vitality
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
 * @property string|null $do_fixe_multiple
<<<<<<< HEAD
 * @property string $res_fixe_neutre
 * @property string $res_fixe_terre
 * @property string $res_fixe_feu
 * @property string $res_fixe_air
 * @property string $res_fixe_eau
=======
 * @property string $do_sagesse
 * @property string $do_vitalite
 * @property string|null $res_fixe_neutre
 * @property string|null $res_fixe_terre
 * @property string|null $res_fixe_feu
 * @property string|null $res_fixe_air
 * @property string|null $res_fixe_eau
>>>>>>> 17efd2058 (fix: ne plus forcer les résistances fixes à 0 sur une fiche vide)
 * @property string $res_neutre
 * @property string $res_terre
 * @property string $res_feu
 * @property string $res_air
 * @property string $res_eau
 * @property string $acrobatie_bonus
 * @property string $discretion_bonus
 * @property string $escamotage_bonus
 * @property string $athletisme_bonus
 * @property string $intimidation_bonus
 * @property string $arcane_bonus
 * @property string $histoire_bonus
 * @property string $investigation_bonus
 * @property string $nature_bonus
 * @property string $religion_bonus
 * @property string $dressage_bonus
 * @property string $medecine_bonus
 * @property string $perception_bonus
 * @property string $perspicacite_bonus
 * @property string $survie_bonus
 * @property string $persuasion_bonus
 * @property string $representation_bonus
 * @property string $supercherie_bonus
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
 * @property int $save_vitality_bonus
 * @property int $save_wisdom_bonus
 * @property int $save_strength_bonus
 * @property int $save_intelligence_bonus
 * @property int $save_chance_bonus
 * @property int $save_agility_bonus
 * @property int $save_vitality_mastery
 * @property int $save_wisdom_mastery
 * @property int $save_strength_mastery
 * @property int $save_intelligence_mastery
 * @property int $save_chance_mastery
 * @property int $save_agility_mastery
 * @property string|null $kamas
 * @property string|null $drop_
 * @property string|null $other_item
 * @property string|null $other_consumable
 * @property string|null $other_resource
 * @property string|null $other_spell
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $created_by
 * @property-read Collection<int, Condition> $conditions
 * @property-read int|null $conditions_count
 * @property-read Collection<int, Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User $createdBy
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Monster|null $monster
 * @property-read Npc|null $npc
 * @property-read Collection<int, resource> $resources
 * @property-read int|null $resources_count
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Database\Factories\Entity\CreatureFactory factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereReadLevel($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherResource($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereVitality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature withoutTrashed()
 * @property string $critical_hit
 * @property string $heal_bonus
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereCriticalHit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoSagesse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoVitalite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHealBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResSagesse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResVitalite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveAgilityBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveAgilityMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveChanceBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveChanceMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveIntelligenceBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveIntelligenceMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveStrengthBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveStrengthMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveVitalityBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveVitalityMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveWisdomBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveWisdomMastery($value)
 * @property-read Collection<int, CreatureTrait> $creatureTraits
 * @property-read int|null $creature_traits_count
 * @property string|null $life_context
 * @property string|null $pa_context
 * @property string|null $pm_context
 * @property string|null $po_context
 * @property string|null $ini_context
 * @property string|null $invocation_context
 * @property string|null $touch_context
 * @property string|null $ca_context
 * @property string|null $dodge_pa_context
 * @property string|null $dodge_pm_context
 * @property string|null $fuite_context
 * @property string|null $tacle_context
 * @property string|null $critical_hit_context
 * @property string|null $heal_bonus_context
 * @property string|null $vitality_context
 * @property string|null $sagesse_context
 * @property string|null $strong_context
 * @property string|null $intel_context
 * @property string|null $agi_context
 * @property string|null $chance_context
 * @property string|null $do_fixe_neutre_context
 * @property string|null $do_fixe_terre_context
 * @property string|null $do_fixe_feu_context
 * @property string|null $do_fixe_air_context
 * @property string|null $do_fixe_eau_context
 * @property string|null $do_fixe_multiple_context
 * @property string|null $res_fixe_neutre_context
 * @property string|null $res_fixe_terre_context
 * @property string|null $res_fixe_feu_context
 * @property string|null $res_fixe_air_context
 * @property string|null $res_fixe_eau_context
 * @property string|null $res_neutre_context
 * @property string|null $res_terre_context
 * @property string|null $res_feu_context
 * @property string|null $res_air_context
 * @property string|null $res_eau_context
 * @property string|null $acrobatie_bonus_context
 * @property string|null $discretion_bonus_context
 * @property string|null $escamotage_bonus_context
 * @property string|null $athletisme_bonus_context
 * @property string|null $intimidation_bonus_context
 * @property string|null $arcane_bonus_context
 * @property string|null $histoire_bonus_context
 * @property string|null $investigation_bonus_context
 * @property string|null $nature_bonus_context
 * @property string|null $religion_bonus_context
 * @property string|null $dressage_bonus_context
 * @property string|null $medecine_bonus_context
 * @property string|null $perception_bonus_context
 * @property string|null $perspicacite_bonus_context
 * @property string|null $survie_bonus_context
 * @property string|null $persuasion_bonus_context
 * @property string|null $representation_bonus_context
 * @property string|null $supercherie_bonus_context
 * @property string|null $save_vitality_bonus_context
 * @property string|null $save_wisdom_bonus_context
 * @property string|null $save_strength_bonus_context
 * @property string|null $save_intelligence_bonus_context
 * @property string|null $save_chance_bonus_context
 * @property string|null $save_agility_bonus_context
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAcrobatieBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAgiContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereArcaneBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAthletismeBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereCaContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereChanceContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereCriticalHitContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDiscretionBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeAirContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeEauContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeFeuContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeNeutreContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeTerreContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoSagesseContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoVitaliteContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDodgePaContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDodgePmContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDressageBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereEscamotageBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereFuiteContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHealBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHistoireBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIniContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIntelContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIntimidationBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereInvestigationBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereInvocationContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereLifeContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereMedecineBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereNatureBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePaContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePerceptionBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePerspicaciteBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePersuasionBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePmContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePoContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereReligionBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereRepresentationBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResAirContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResEauContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFeuContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeAirContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeEauContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeFeuContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeNeutreContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeTerreContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResNeutreContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResSagesseContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResTerreContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResVitaliteContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSagesseContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveAgilityBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveChanceBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveIntelligenceBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveStrengthBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveVitalityBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSaveWisdomBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereStrongContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSupercherieBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSurvieBonusContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereTacleContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereTouchContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereVitalityContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature visibleToUser(?\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeMultiple($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeMultipleContext($value)
 * @property string $intimidation_ability
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereArtisanatBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereArtisanatMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereConnaissanceCreaturesBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereConnaissanceCreaturesMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHerbalisteBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHerbalisteMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIntimidationAbility($value)
 * @mixin \Eloquent
 */
class Creature extends Model implements HasMedia
{
    /** @use HasFactory<CreatureFactory> */
    use HasEntityImageMedia, HasFactory, SoftDeletes, VisibleToViewer;

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/creatures';

    /** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */
    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    /**
     * The conditions that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'hostility',
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
        'critical_hit',
        'heal_bonus',
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
        'do_fixe_multiple',
                        'res_fixe_neutre',
        'res_fixe_terre',
        'res_fixe_feu',
        'res_fixe_air',
        'res_fixe_eau',
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
        'save_vitality_bonus',
        'save_wisdom_bonus',
        'save_strength_bonus',
        'save_intelligence_bonus',
        'save_chance_bonus',
        'save_agility_bonus',
        'save_vitality_mastery',
        'save_wisdom_mastery',
        'save_strength_mastery',
        'save_intelligence_mastery',
        'save_chance_mastery',
        'save_agility_mastery',
        'kamas',
        'drop_',
        'other_item',
        'other_consumable',
        'other_resource',
        'other_spell',
        'state',
        'read_level',
        'write_level',
        'image',
        'created_by',
    ];

    /**
     * @return list<string>
     */
    public function getFillable(): array
    {
        return array_values(array_unique([
            ...$this->fillable,
            ...CreatureComposableColumns::contextColumns(),
        ]));
    }

    /**
     * Indique si une colonne de total explicite est renseignée (mode « total prioritaire »).
     */
    public function hasExplicitTotal(string $column): bool
    {
        if (! CreatureComposableColumns::isComposable($column)) {
            return false;
        }
        $value = $this->getAttribute($column);

        return $value !== null && $value !== '';
    }

    /**
     * Bonus contextuel brut (nombre ou formule) pour une colonne composable.
     */
    public function contextBonusRaw(string $column): ?string
    {
        if (! CreatureComposableColumns::isComposable($column)) {
            return null;
        }
        $value = $this->getAttribute(CreatureComposableColumns::contextColumn($column));
        if ($value === null || $value === '') {
            return null;
        }

        return is_string($value) ? $value : (string) $value;
    }

    /**
     * The conditions that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hostility' => 'integer',
        'read_level' => 'integer',
        'write_level' => 'integer',
    ];

    /**
     * Get the user that created the creature.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les attributs de la créature.
     */
    public function conditions()
    {
        return $this->belongsToMany(Condition::class, 'condition_creature');
    }

    public function creatureTraits()
    {
        return $this->belongsToMany(CreatureTrait::class, 'creature_creature_trait')
            ->withTimestamps();
    }

    /**
     * Les capacités de la créature.
     */
    public function capabilities()
    {
        return $this->belongsToMany(Capability::class, 'capability_creature');
    }

    /**
     * Les objets de la créature.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'creature_item')->withPivot('quantity');
    }

    /**
     * Les ressources de la créature.
     */
    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'creature_resource')->withPivot('quantity');
    }

    /**
     * Les sorts de la créature.
     */
    public function spells()
    {
        return $this->belongsToMany(Spell::class, 'creature_spell');
    }

    /**
     * Les consommables de la créature.
     */
    public function consumables()
    {
        return $this->belongsToMany(Consumable::class, 'consumable_creature')->withPivot('quantity');
    }

    /**
     * Le PNJ associé à la créature.
     */
    public function npc()
    {
        return $this->hasOne(Npc::class, 'creature_id');
    }

    /**
     * Le monstre associé à la créature.
     */
    public function monster()
    {
        return $this->hasOne(Monster::class, 'creature_id');
    }
}
