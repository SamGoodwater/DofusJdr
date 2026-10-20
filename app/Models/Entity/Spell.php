<?php

namespace App\Models\Entity;

use App\Models\Concerns\HasEntityImageMedia;
use App\Models\Concerns\VisibleToViewer;
use App\Models\Effect;
use App\Models\Pivots\BreedSpellPivot;
use App\Models\SpellDegree;
use App\Models\Type\SpellType;
use App\Models\User;
use App\Support\AreaConstants;
use Database\Factories\Entity\SpellFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string $description Toujours non null en base ; une valeur API `null` est normalisée en chaîne vide.
 * @property string|null $effect
 * @property string $level
 * @property string|null $po_min Portée min (valeur ou formule, ex. "0", "[level]")
 * @property string|null $po_max Portée max (valeur ou formule, ex. "1", "6")
 * @property string|null $area Zone par défaut si le sort ne possède aucun degré
 * @property bool $po_editable
 * @property string $pa
 * @property string $cast_per_turn
 * @property string $cast_per_target
 * @property bool $sight_line
 * @property bool $cast_in_line Lancer uniquement en ligne
 * @property bool $cast_in_diagonal Lancer autorisé en diagonale
 * @property string|null $target_type Mode de pose global : direct, trap ou glyph
 * @property int $max_stack Cumul maximal, 0 = non limité
 * @property int $global_cooldown Temps de relance global en tours
 * @property string $number_between_two_cast
 * @property int $element
 * @property int $category
 * @property bool $is_magic
 * @property int $powerful
 * @property string $resolution_mode
 * @property string|null $attack_characteristic_key
 * @property string|null $save_characteristic_key
 * @property string|null $save_dc_formula
 * @property string|null $save_success_note
 * @property bool $auto_success_if_willing_target Réussite auto si la cible est consentante
 * @property bool $allows_reaction Utilisable comme réaction de combat (PA non récupérés au tour suivant)
 * @property string|null $casting_time Temps d'incantation (texte libre)
 * @property bool|null $ritual_available Utilisable en mode rituel (null = non renseigné)
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property bool $auto_update
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, SpellType> $spellTypes
 * @property-read int|null $spell_types_count
 * @method static \Database\Factories\Entity\SpellFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell query()
 * @method static Builder<static>|Spell visibleToUser(?User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastPerTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastPerTurn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereElement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereIsMagic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereNumberBetweenTwoCast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePowerful($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSightLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell withoutTrashed()
 * @property string|null $duration
 * @property-read Collection<int, Breed> $breeds
 * @property-read int|null $breeds_count
 * @property-read Collection<int, Effect> $effects
 * @property-read int|null $effects_count
 * @property-read string|null $area
 * @property-read string $po_display
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, SpellEffect> $spellEffects
 * @property-read int|null $spell_effects_count
 * @property-read Collection<int, Condition> $conditions
 * @property-read int|null $conditions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereAllowsReaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereAttackCharacteristicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereAutoSuccessIfWillingTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereResolutionMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereRitualAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSaveCharacteristicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSaveDcFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSaveSuccessNote($value)
 * @property-read BreedSpellPivot|null $pivot
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastInDiagonal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastInLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereGlobalCooldown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereMaxStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereTargetType($value)
 * @property-read Collection<int, SpellDegree> $degrees
 * @property-read int|null $degrees_count
 * @mixin \Eloquent
 */
class Spell extends Model implements HasMedia
{
    /** @use HasFactory<SpellFactory> */
    use HasEntityImageMedia, HasFactory, SoftDeletes, VisibleToViewer;

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    public const CATEGORY_CLASS = 0;

    public const CATEGORY_CREATURE = 1;

    public const CATEGORY_LEARNABLE = 2;

    public const CATEGORY_CONSUMABLE = 3;

    public const RESOLUTION_ATTACK_ROLL = 'attack_roll';

    public const RESOLUTION_SAVING_THROW = 'saving_throw';

    public const RESOLUTION_AUTO_SUCCESS = 'auto_success';

    /** @deprecated Utiliser AreaConstants::SHAPE_ID_MAP */
    public const AREAS_SHAPE = AreaConstants::SHAPE_ID_MAP;

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/spells';

    /** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */
    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    /**
     * Colonnes NOT NULL : les FormRequest acceptent souvent `nullable` alors que MySQL refuse NULL.
     * Valeurs alignées sur les défauts du schéma (migrations `entity_spells` et colonnes ajoutées ensuite).
     *
     * @var array<string, bool|int|string>
     */
    private const ATTRIBUTE_FALLBACK_WHEN_NULL = [
        'description' => '',
        'level' => '1',
        'po_min' => '1',
        'po_max' => '1',
        'pa' => '3',
        'cast_per_turn' => '1',
        'cast_per_target' => '0',
        'number_between_two_cast' => '0',
        'po_editable' => true,
        'sight_line' => true,
        'category' => 0,
        'is_magic' => true,
        'powerful' => 0,
        'resolution_mode' => self::RESOLUTION_ATTACK_ROLL,
        'state' => self::STATE_DRAFT,
        'read_level' => 0,
        'write_level' => 3,
        'auto_update' => true,
        'auto_success_if_willing_target' => false,
        'allows_reaction' => false,
        'cast_in_line' => false,
        'cast_in_diagonal' => false,
        'max_stack' => 0,
        'global_cooldown' => 0,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'official_id',
        'dofusdb_id',
        'name',
        'description',
        'effect',
        'level',
        'po_min',
        'po_max',
        'area',
        'po_editable',
        'pa',
        'casting_time',
        'ritual_available',
        'cast_per_turn',
        'cast_per_target',
        'sight_line',
        'cast_in_line',
        'cast_in_diagonal',
        'target_type',
        'max_stack',
        'global_cooldown',
        'number_between_two_cast',
        'duration',
        'element',
        'category',
        'is_magic',
        'powerful',
        'resolution_mode',
        'attack_characteristic_key',
        'save_characteristic_key',
        'save_dc_formula',
        'save_success_note',
        'auto_success_if_willing_target',
        'allows_reaction',
        'state',
        'read_level',
        'write_level',
        'image',
        'auto_update',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'element' => 'integer',
        'category' => 'integer',
        'powerful' => 'integer',
        'read_level' => 'integer',
        'write_level' => 'integer',
        'po_editable' => 'boolean',
        'sight_line' => 'boolean',
        'cast_in_line' => 'boolean',
        'cast_in_diagonal' => 'boolean',
        'max_stack' => 'integer',
        'global_cooldown' => 'integer',
        'is_magic' => 'boolean',
        'auto_update' => 'boolean',
        'auto_success_if_willing_target' => 'boolean',
        'allows_reaction' => 'boolean',
        'ritual_available' => 'boolean',
    ];

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($value === null && array_key_exists($key, self::ATTRIBUTE_FALLBACK_WHEN_NULL)) {
            $value = self::ATTRIBUTE_FALLBACK_WHEN_NULL[$key];
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Stockage texte : évite les types non string et les avertissements PHP 8.4+ sur (string) null.
     */
    public function setDescriptionAttribute(mixed $value): void
    {
        $this->attributes['description'] = $value === null ? '' : (string) $value;
    }

    /**
     * Get the user that created the spell.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les breeds (affichées « Classes ») associées à ce sort.
     */
    public function breeds()
    {
        return $this->belongsToMany(Breed::class, 'breed_spell', 'spell_id', 'breed_id')
            ->using(BreedSpellPivot::class)
            ->withPivot(['character_level', 'slot_index', 'choice_order']);
    }

    /**
     * Les créatures associées à ce sort.
     */
    public function creatures()
    {
        return $this->belongsToMany(Creature::class, 'creature_spell');
    }

    /**
     * Les scénarios associés à ce sort.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'scenario_spell');
    }

    /**
     * Les campagnes associées à ce sort.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_spell');
    }

    /**
     * Les types de ce sort.
     */
    public function spellTypes()
    {
        return $this->belongsToMany(SpellType::class, 'spell_type');
    }

    /**
     * Les monstres invoqués par ce sort.
     */
    public function monsters()
    {
        return $this->belongsToMany(Monster::class, 'spell_invocation');
    }

    /**
     * Les états que ce sort peut appliquer (sur cible ou lanceur).
     */
    public function conditions()
    {
        return $this->belongsToMany(Condition::class, 'condition_spell')
            ->withPivot(['application_mode', 'dofus_effect_id', 'duration', 'dispellable', 'target_mask'])
            ->withTimestamps();
    }

    /**
     * Définitions d’effets liées à ce sort (pivot effect_spell) — legacy, remplacé par {@see degrees()}.
     *
     * @return BelongsToMany<Effect, $this>
     */
    public function effects(): BelongsToMany
    {
        return $this->belongsToMany(Effect::class, 'effect_spell');
    }

    /**
     * Progression native des degrés du sort (propriétés + effets par niveau).
     *
     * @return HasMany<SpellDegree, $this>
     */
    public function degrees()
    {
        return $this->hasMany(SpellDegree::class)->orderBy('position');
    }

    /**
     * Portée affichable à partir de po_min / po_max : une seule valeur si une borne est vide ou si min = max ;
     * sinon « min - max » (espaces). Chaîne vide si les deux sont vides. 1 seul (ou 1 et 1) = CàC côté UI.
     */
    public function getPoDisplayAttribute(): string
    {
        $trimPart = static function ($value): ?string {
            if ($value === null) {
                return null;
            }
            $s = trim((string) $value);

            return $s === '' ? null : $s;
        };

        $min = $trimPart($this->po_min);
        $max = $trimPart($this->po_max);

        if ($min === null && $max === null) {
            return '';
        }

        if ($min !== null && $max !== null) {
            return $min === $max ? $min : $min.' - '.$max;
        }

        return $min ?? $max;
    }

    /**
     * Zone d’impact affichée : premier degré natif, sinon premier degré du premier effet legacy.
     *
     * @return string|null Notation zone (point, line-1x9, …) ou null
     */
    public function getAreaAttribute(): ?string
    {
        if ($this->relationLoaded('degrees') && $this->degrees->isNotEmpty()) {
            $degreeArea = $this->degrees->sortBy('position')->first()?->area;
            if ($degreeArea !== null && $degreeArea !== '') {
                return $degreeArea;
            }
        }

        $storedArea = $this->attributes['area'] ?? null;
        if (is_string($storedArea) && trim($storedArea) !== '') {
            return $storedArea;
        }

        if ($this->relationLoaded('effects')) {
            $effect = $this->effects->first();
            if ($effect !== null && ! $effect->relationLoaded('degrees')) {
                $effect->load('degrees');
            }
            $legacyDegrees = $effect?->degrees;
            if ($legacyDegrees !== null && $legacyDegrees->isNotEmpty()) {
                return $legacyDegrees->sortBy('degree')->first()?->area;
            }
            // Effets chargés mais vides : tenter le canal natif une fois.
            if (! $this->relationLoaded('degrees')) {
                return $this->degrees()->orderBy('position')->value('area');
            }

            return is_string($storedArea) ? $storedArea : null;
        }

        $nativeArea = $this->degrees()->orderBy('position')->value('area');
        if ($nativeArea) {
            return $nativeArea;
        }

        $effect = $this->effects()->with('degrees')->first();
        $legacyDegrees = $effect?->degrees;
        if ($legacyDegrees === null || $legacyDegrees->isEmpty()) {
            return is_string($storedArea) ? $storedArea : null;
        }

        return $legacyDegrees->sortBy('degree')->first()?->area;
    }
}
