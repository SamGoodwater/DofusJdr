<?php

namespace App\Models\Entity;

use App\Models\Concerns\VisibleToViewer;
use App\Models\Type\MonsterRace;
use App\Support\Creature\CreatureSize;
use App\Support\Profile\ProfileMasteryColumns;
use Database\Factories\MonsterFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $creature_id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $dofus_version
 * @property bool $auto_update
 * @property int $size
 * @property int|null $monster_race_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Creature|null $creature
 * @property-read MonsterRace|null $monsterRace
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, Spell> $spellInvocations
 * @property-read int|null $spell_invocations_count
 * @method static \Database\Factories\Entity\MonsterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereCreatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereMonsterRaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereUpdatedAt($value)
 * @property int $is_boss
 * @property string $boss_pa
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereBossPa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereIsBoss($value)
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereWriteLevel($value)
 * @property-read Collection<int, Language> $languages
 * @property-read int|null $languages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster visibleToUser(?\App\Models\User $user)
 * @property int $acrobatics_mastery
 * @property int $animal_handling_mastery
 * @property int $arcana_mastery
 * @property int $athletics_mastery
 * @property int $deception_mastery
 * @property int $history_mastery
 * @property int $insight_mastery
 * @property int $intimidation_mastery
 * @property int $investigation_mastery
 * @property int $medicine_mastery
 * @property int $nature_mastery
 * @property int $perception_mastery
 * @property int $performance_mastery
 * @property int $persuasion_mastery
 * @property int $religion_mastery
 * @property int $sleight_of_hand_mastery
 * @property int $stealth_mastery
 * @property int $survival_mastery
 * @property int $save_strength_mastery
 * @property int $save_intelligence_mastery
 * @property int $save_agility_mastery
 * @property int $save_chance_mastery
 * @property int $save_vitality_mastery
 * @property int $save_wisdom_mastery
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereAcrobaticsMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereAnimalHandlingMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereArcanaMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereAthleticsMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereDeceptionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereHistoryMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereInsightMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereIntimidationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereInvestigationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereMedicineMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereNatureMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster wherePerceptionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster wherePerformanceMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster wherePersuasionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereReligionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSaveAgilityMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSaveChanceMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSaveIntelligenceMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSaveStrengthMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSaveVitalityMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSaveWisdomMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSleightOfHandMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereStealthMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSurvivalMastery($value)
 * @mixin \Eloquent
 */
class Monster extends Model
{
    /** @use HasFactory<MonsterFactory> */
    use HasFactory, VisibleToViewer;

    use SoftDeletes;

    const SIZE = CreatureSize::LABELS;

    const HOSTILITY = [
        0 => 'Amical',
        1 => 'Curieux',
        2 => 'Neutre',
        3 => 'Hostile',
        4 => 'Aggressif',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'creature_id',
        'official_id',
        'dofusdb_id',
        'dofus_version',
        'auto_update',
        'size',
        'is_boss',
        'boss_pa',
        'monster_race_id',
        'state',
        'read_level',
        'write_level',
    ];

    /**
     * @return list<string>
     */
    public function getFillable(): array
    {
        return array_values(array_unique([
            ...$this->fillable,
            ...ProfileMasteryColumns::all(),
        ]));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'auto_update' => 'boolean',
            'read_level' => 'integer',
            'write_level' => 'integer',
            ...array_fill_keys(ProfileMasteryColumns::all(), 'integer'),
        ];
    }

    /**
     * Get the creature associated with the monster.
     */
    public function creature()
    {
        return $this->belongsTo(Creature::class, 'creature_id');
    }

    /**
     * Get the race of the monster.
     */
    public function monsterRace()
    {
        return $this->belongsTo(MonsterRace::class, 'monster_race_id');
    }

    /**
     * Les scénarios associés à ce monstre.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'monster_scenario');
    }

    /**
     * Les campagnes associées à ce monstre.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'monster_campaign');
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class, 'monster_language')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * Les sorts d'invocation de ce monstre.
     */
    public function spellInvocations()
    {
        return $this->belongsToMany(Spell::class, 'spell_invocation');
    }
}
