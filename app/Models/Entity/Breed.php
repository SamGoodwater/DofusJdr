<?php

namespace App\Models\Entity;

use App\Models\Concerns\HasEntityImageMedia;
use App\Models\Concerns\HasLeveledSections;
use App\Models\Concerns\VisibleToViewer;
use App\Models\Pivots\BreedSpellPivot;
use App\Models\Section;
use App\Models\User;
use App\Support\Profile\ProfileMasteryColumns;
use Database\Factories\Entity\BreedFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Entité Breed (affichée « Classe » côté utilisateur).
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property string|null $description_fast
 * @property string|null $description
 * @property string|null $evolution
 * @property string|null $life_dice
 * @property string|null $specificity
 * @property string|null $mechanics
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property string|null $icon
 * @property string|null $symbol_full
 * @property string|null $symbol_bw
 * @property string|null $logo_male
 * @property string|null $logo_female
 * @property string|null $image_full_male
 * @property string|null $image_full_female
 * @property bool $auto_update
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @property-read Collection<int, Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @method static \Database\Factories\Entity\BreedFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed query()
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereDescriptionFast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereLife($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereLifeDice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereSpecificity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed withoutTrashed()
 * @property string|null $life
 * @property-read Collection<int, BreedElementOrientation> $elementOrientations
 * @property-read int|null $element_orientations_count
 * @property-read BreedSpellPivot|null $pivot
 * @method static Builder<static>|Breed visibleToUser(?\App\Models\User $user)
 * @property-read Collection<int, Language> $languages
 * @property-read int|null $languages_count
 * @method static Builder<static>|Breed whereEvolution($value)
 * @property-read Collection<int, CreatureTrait> $creatureTraits
 * @property-read int|null $creature_traits_count
 * @property-read Collection<int, Section> $sections
 * @property-read int|null $sections_count
 * @method static Builder<static>|Breed whereImageFullFemale($value)
 * @method static Builder<static>|Breed whereImageFullMale($value)
 * @method static Builder<static>|Breed whereLogoFemale($value)
 * @method static Builder<static>|Breed whereLogoMale($value)
 * @method static Builder<static>|Breed whereSymbolBw($value)
 * @method static Builder<static>|Breed whereSymbolFull($value)
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
 * @method static Builder<static>|Breed whereAcrobaticsMastery($value)
 * @method static Builder<static>|Breed whereAnimalHandlingMastery($value)
 * @method static Builder<static>|Breed whereArcanaMastery($value)
 * @method static Builder<static>|Breed whereAthleticsMastery($value)
 * @method static Builder<static>|Breed whereDeceptionMastery($value)
 * @method static Builder<static>|Breed whereHistoryMastery($value)
 * @method static Builder<static>|Breed whereInsightMastery($value)
 * @method static Builder<static>|Breed whereIntimidationMastery($value)
 * @method static Builder<static>|Breed whereInvestigationMastery($value)
 * @method static Builder<static>|Breed whereMedicineMastery($value)
 * @method static Builder<static>|Breed whereNatureMastery($value)
 * @method static Builder<static>|Breed wherePerceptionMastery($value)
 * @method static Builder<static>|Breed wherePerformanceMastery($value)
 * @method static Builder<static>|Breed wherePersuasionMastery($value)
 * @method static Builder<static>|Breed whereReligionMastery($value)
 * @method static Builder<static>|Breed whereSaveAgilityMastery($value)
 * @method static Builder<static>|Breed whereSaveChanceMastery($value)
 * @method static Builder<static>|Breed whereSaveIntelligenceMastery($value)
 * @method static Builder<static>|Breed whereSaveStrengthMastery($value)
 * @method static Builder<static>|Breed whereSaveVitalityMastery($value)
 * @method static Builder<static>|Breed whereSaveWisdomMastery($value)
 * @method static Builder<static>|Breed whereSleightOfHandMastery($value)
 * @method static Builder<static>|Breed whereStealthMastery($value)
 * @method static Builder<static>|Breed whereSurvivalMastery($value)
 * @mixin \Eloquent
 */
class Breed extends Model implements HasMedia
{
    /** @use HasFactory<BreedFactory> */
    use HasEntityImageMedia, HasFactory, HasLeveledSections, SoftDeletes, VisibleToViewer;

    protected function sectionsPivotTable(): string
    {
        return 'section_breed';
    }

    protected function sectionsPivotForeignKey(): string
    {
        return 'breed_id';
    }

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/breeds';

    /** Motif de nommage pour la collection icons (placeholders: [name], [date], [id]). */
    public const MEDIA_FILE_PATTERN_ICONS = 'icon-[id]-[name]';

    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    protected $table = 'breeds';

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'official_id',
        'dofusdb_id',
        'name',
        'description_fast',
        'description',
        'evolution',
        'life_dice',
        'specificity',
        'mechanics',
        'dofus_version',
        'state',
        'read_level',
        'write_level',
        'image',
        'icon',
        'symbol_full',
        'symbol_bw',
        'logo_male',
        'logo_female',
        'image_full_male',
        'image_full_female',
        'auto_update',
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
            'auto_update' => 'boolean',
            ...array_fill_keys(ProfileMasteryColumns::all(), 'integer'),
        ];
    }

    /**
     * Filtre les classes visibles pour l'utilisateur (index Inertia, API tableau).
     *
     * Brouillon/raw : createur ou role >= MJ. Playable : role >= read_level. Pas d'archive pour non-admin.
     *
     * @param  Builder<static>  $query
     */
    public function scopeVisibleToUser(Builder $query, ?User $user): void
    {
        if ($user?->isAdmin()) {
            return;
        }

        $role = $user !== null ? (int) ($user->role ?? 0) : 0;

        $query->where('state', '!=', self::STATE_ARCHIVED);

        $query->where(function (Builder $outer) use ($user, $role) {
            $outer->where(function (Builder $q) use ($role) {
                $q->where('state', self::STATE_PLAYABLE)
                    ->whereRaw('CAST(read_level AS SIGNED) <= ?', [$role]);
            });

            if ($user !== null) {
                $outer->orWhere(function (Builder $q) use ($user) {
                    $q->whereIn('state', [self::STATE_RAW, self::STATE_DRAFT])
                        ->where(function (Builder $q2) use ($user) {
                            $q2->where('created_by', $user->id)
                                ->orWhereRaw('? >= ?', [(int) $user->role, User::ROLE_GAME_MASTER]);
                        });
                });
            }
        });
    }

    /**
     * Get the user that created the breed.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les PNJ associés à cette breed.
     */
    public function npcs(): HasMany
    {
        return $this->hasMany(Npc::class, 'breed_id');
    }

    /**
     * Les sorts associés à cette breed (pivot : niveau PJ, emplacement, ordre des choix).
     */
    public function spells(): BelongsToMany
    {
        return $this->belongsToMany(Spell::class, 'breed_spell', 'breed_id', 'spell_id')
            ->using(BreedSpellPivot::class)
            ->withPivot(['character_level', 'slot_index', 'choice_order']);
    }

    /**
     * Capacités associées à la classe (liste plate, sans emplacement).
     */
    public function capabilities(): BelongsToMany
    {
        return $this->belongsToMany(Capability::class, 'breed_capability', 'breed_id', 'capability_id')
            ->withTimestamps();
    }

    public function creatureTraits(): BelongsToMany
    {
        return $this->belongsToMany(CreatureTrait::class, 'breed_creature_trait')
            ->withPivot('level')
            ->withTimestamps();
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'breed_language')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * Orientations par voix élémentaire (air, terre, feu, eau).
     */
    public function elementOrientations(): HasMany
    {
        return $this->hasMany(BreedElementOrientation::class, 'breed_id');
    }

    /**
     * @return array<string, string|null> air|earth|fire|water => orientation_key|null
     */
    public function elementOrientationsMap(): array
    {
        $map = array_fill_keys(BreedElementOrientation::ELEMENTS, null);
        if (! $this->relationLoaded('elementOrientations')) {
            return $map;
        }
        foreach ($this->elementOrientations as $row) {
            $map[$row->element] = $row->orientation_key;
        }

        return $map;
    }

    /**
     * Regroupe les sorts chargés par (character_level, slot_index) pour l’API / la fiche.
     *
     * @return list<array{character_level: int, slot_index: int, spells: Collection<int, Spell>}>
     */
    public function getSpellSlotsGrouped(): array
    {
        if (! $this->relationLoaded('spells')) {
            return [];
        }

        /** @var Collection<string, array{character_level: int, slot_index: int, spells: Collection<int, Spell>}> $groups */
        $groups = collect();
        foreach ($this->spells as $spell) {
            $pivot = $spell->pivot;
            $level = (int) $pivot->character_level;
            $slot = (int) $pivot->slot_index;
            $key = $level.'|'.$slot;
            if (! $groups->has($key)) {
                $groups[$key] = [
                    'character_level' => $level,
                    'slot_index' => $slot,
                    'spells' => new Collection,
                ];
            }
            $groups[$key]['spells']->push($spell);
        }

        return $groups
            ->sort(function (array $a, array $b): int {
                $c = $a['character_level'] <=> $b['character_level'];

                return $c !== 0 ? $c : $a['slot_index'] <=> $b['slot_index'];
            })
            ->values()
            ->map(function (array $group): array {
                /** @var Collection<int, Spell> $spells */
                $spells = $group['spells'];
                $sorted = $spells->sort(function (Spell $a, Spell $b): int {
                    $oa = (int) $a->pivot->choice_order;
                    $ob = (int) $b->pivot->choice_order;
                    if ($oa !== $ob) {
                        return $oa <=> $ob;
                    }

                    return strcmp((string) ($a->name ?? ''), (string) ($b->name ?? ''));
                })->values();

                return [
                    'character_level' => $group['character_level'],
                    'slot_index' => $group['slot_index'],
                    'spells' => $sorted,
                ];
            })
            ->all();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->singleFile();
        $this->addMediaCollection('icons')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->registerEntityImageMediaConversions($media);
        $this->addMediaConversion('thumb')
            ->performOnCollections('icons')
            ->fit(Fit::Contain, 128, 128)
            ->format('webp')
            ->nonQueued();
        $this->addMediaConversion('webp')
            ->performOnCollections('icons')
            ->format('webp')
            ->nonQueued();
    }
}
