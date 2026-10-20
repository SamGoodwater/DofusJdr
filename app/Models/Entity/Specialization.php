<?php

namespace App\Models\Entity;

use App\Models\Concerns\HasEntityImageMedia;
use App\Models\Concerns\HasLeveledSections;
use App\Models\Concerns\VisibleToViewer;
use App\Models\Section;
use App\Models\User;
use App\Support\Profile\ProfileMasteryColumns;
use Database\Factories\Entity\SpecializationFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read Collection<int, Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @method static \Database\Factories\Entity\SpecializationFactory factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization withoutTrashed()
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property string|null $short_description
 * @property-read Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read Collection<int, CreatureTrait> $creatureTraits
 * @property-read int|null $creature_traits_count
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Collection<int, \App\Models\Entity\Resource> $resources
 * @property-read int|null $resources_count
 * @property-read Collection<int, Section> $sections
 * @property-read int|null $sections_count
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization visibleToUser(?\App\Models\User $user)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereAcrobaticsMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereAnimalHandlingMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereArcanaMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereAthleticsMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereDeceptionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereHistoryMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereInsightMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereIntimidationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereInvestigationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereMedicineMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereNatureMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization wherePerceptionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization wherePerformanceMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization wherePersuasionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereReligionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereSaveAgilityMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereSaveChanceMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereSaveIntelligenceMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereSaveStrengthMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereSaveVitalityMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereSaveWisdomMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereSleightOfHandMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereStealthMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereSurvivalMastery($value)
 * @mixin \Eloquent
 */
class Specialization extends Model implements HasMedia
{
    /** @use HasFactory<SpecializationFactory> */
    use HasEntityImageMedia, HasFactory, HasLeveledSections, SoftDeletes, VisibleToViewer;

    protected function sectionsPivotTable(): string
    {
        return 'section_specialization';
    }

    protected function sectionsPivotForeignKey(): string
    {
        return 'specialization_id';
    }

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/specializations';

    /** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */
    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'short_description',
        'description',
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
            ...ProfileMasteryColumns::all(),
        ]));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_level' => 'integer',
            'write_level' => 'integer',
            ...array_fill_keys(ProfileMasteryColumns::all(), 'integer'),
        ];
    }

    /**
     * Get the user that created the specialization.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les capacités associées à cette spécialisation.
     */
    public function capabilities(): BelongsToMany
    {
        return $this->belongsToMany(Capability::class, 'capability_specialization')
            ->withPivot('level')
            ->withTimestamps();
    }

    public function spells(): BelongsToMany
    {
        return $this->belongsToMany(Spell::class, 'specialization_spell')
            ->withPivot('level')
            ->withTimestamps();
    }

    public function creatureTraits(): BelongsToMany
    {
        return $this->belongsToMany(CreatureTrait::class, 'creature_trait_specialization')
            ->withPivot('level')
            ->withTimestamps();
    }

    public function consumables(): BelongsToMany
    {
        return $this->belongsToMany(Consumable::class, 'consumable_specialization')
            ->withPivot(['level', 'quantity'])
            ->withTimestamps();
    }

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'resource_specialization')
            ->withPivot(['level', 'quantity'])
            ->withTimestamps();
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_specialization')
            ->withPivot(['level', 'quantity'])
            ->withTimestamps();
    }

    /**
     * Les PNJ de cette spécialisation.
     */
    public function npcs(): HasMany
    {
        return $this->hasMany(Npc::class, 'specialization_id');
    }
}
