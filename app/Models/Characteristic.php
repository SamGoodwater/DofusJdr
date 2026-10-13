<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CharacteristicNature;
use App\Models\Concerns\HasMediaCustomNaming;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Caractéristique générale : propriétés communes et id unique.
 *
 * Une ligne = une caractéristique (ex. PA créature, PA sort, PA objet = 3 lignes).
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $short_name
 * @property string|null $helper
 * @property string|null $descriptions
 * @property string|null $icon
 * @property string|null $icon_false
 * @property string|null $color
 * @property array|null $value_overrides
 * @property bool $hide_when_empty
 * @property bool $hide_when_false
 * @property string|null $unit
 * @property string $type
 * @property string $status
 * @property int $sort_order
 * @property string|null $group
 * @property string|null $nature
 * @property int|null $linked_to_characteristic_id
 * @property int|null $intrinsic_characteristic_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, CharacteristicConsumable> $consumableRows
 * @property-read int|null $consumable_rows_count
 * @property-read Collection<int, CharacteristicCreature> $creatureRows
 * @property-read int|null $creature_rows_count
 * @property-read Characteristic|null $intrinsicCharacteristic
 * @property-read Collection<int, Characteristic> $linkedCharacteristics
 * @property-read int|null $linked_characteristics_count
 * @property-read Characteristic|null $masterCharacteristic
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, CharacteristicObject> $objectRows
 * @property-read int|null $object_rows_count
 * @property-read Collection<int, CharacteristicProfile> $profileRows
 * @property-read int|null $profile_rows_count
 * @property-read Collection<int, CharacteristicSpell> $spellRows
 * @property-read int|null $spell_rows_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereDescriptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereHelper($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereIconFalse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereLinkedToCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereValueOverrides($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereHideWhenEmpty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereHideWhenFalse($value)
 * @mixin \Eloquent
 */
class Characteristic extends Model implements HasMedia
{
    use HasMediaCustomNaming;
    use InteractsWithMedia;

    public const STATUS_A_VALIDER = 'a_valider';

    public const STATUS_EN_COURS_VALIDATION = 'en_cours_de_validation';

    public const STATUS_VALIDEE = 'validee';

    public const STATUSES = [
        self::STATUS_A_VALIDER,
        self::STATUS_EN_COURS_VALIDATION,
        self::STATUS_VALIDEE,
    ];

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/characteristics';

    /** Motif de nommage pour la collection icons (placeholders: [key], [id], [name], …). */
    public const MEDIA_FILE_PATTERN_ICONS = '[key]';

    protected $table = 'characteristics';

    /** @var list<string> */
    protected $fillable = [
        'key',
        'name',
        'short_name',
        'helper',
        'descriptions',
        'icon',
        'icon_false',
        'color',
        'value_overrides',
        'hide_when_empty',
        'hide_when_false',
        'unit',
        'type',
        'status',
        'sort_order',
        'group',
        'nature',
        'linked_to_characteristic_id',
        'intrinsic_characteristic_id',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'sort_order' => 'integer',
        'value_overrides' => 'array',
        'hide_when_empty' => 'boolean',
        'hide_when_false' => 'boolean',
        'nature' => CharacteristicNature::class,
    ];

    /**
     * Caractéristique maître si cette ligne est une caractéristique liée.
     */
    public function masterCharacteristic(): BelongsTo
    {
        return $this->belongsTo(self::class, 'linked_to_characteristic_id');
    }

    /**
     * Caractéristiques liées qui réutilisent cette ligne comme source de configuration.
     *
     * @return HasMany<self>
     */
    public function linkedCharacteristics(): HasMany
    {
        return $this->hasMany(self::class, 'linked_to_characteristic_id');
    }

    /**
     * Indique si la caractéristique est liée à une autre.
     */
    public function isLinked(): bool
    {
        return $this->linked_to_characteristic_id !== null;
    }

    /**
     * Retourne la caractéristique effective (maître si liée, sinon elle-même).
     */
    public function effectiveCharacteristic(): self
    {
        if (! $this->isLinked()) {
            return $this;
        }

        $master = $this->relationLoaded('masterCharacteristic') ? $this->masterCharacteristic : $this->masterCharacteristic()->first();

        return $master ?? $this;
    }

    /**
     * Intrinsèque créature modifiée par ce bonus/malus ou buff/débuff.
     */
    public function intrinsicCharacteristic(): BelongsTo
    {
        return $this->belongsTo(self::class, 'intrinsic_characteristic_id');
    }

    public function creatureRows(): HasMany
    {
        return $this->hasMany(CharacteristicCreature::class, 'characteristic_id');
    }

    public function objectRows(): HasMany
    {
        return $this->hasMany(CharacteristicObject::class, 'characteristic_id');
    }

    public function spellRows(): HasMany
    {
        return $this->hasMany(CharacteristicSpell::class, 'characteristic_id');
    }

    public function consumableRows(): HasMany
    {
        return $this->hasMany(CharacteristicConsumable::class, 'characteristic_id');
    }

    public function profileRows(): HasMany
    {
        return $this->hasMany(CharacteristicProfile::class, 'characteristic_id');
    }

    public function natureEnum(): ?CharacteristicNature
    {
        if ($this->nature instanceof CharacteristicNature) {
            return $this->nature;
        }

        return $this->nature !== null ? CharacteristicNature::tryFrom((string) $this->nature) : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icons')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
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
