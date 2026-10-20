<?php

namespace App\Models\Type;

use App\Models\Entity\Spell;
use App\Models\Type\Concerns\HasTypeRegistryFlags;
use App\Models\User;
use Database\Factories\SpellTypeFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $color
 * @property string|null $icon
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Database\Factories\Type\SpellTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType withoutTrashed()
 * @property bool|null $show_in_catalog
 * @property bool|null $allow_scrap
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType allowScrap()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType allowed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType visibleInCatalog()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereAllowScrap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereShowInCatalog($value)
 * @mixin \Eloquent
 */
class SpellType extends Model
{
    /** @use HasFactory<SpellTypeFactory> */
    use HasFactory, HasTypeRegistryFlags, SoftDeletes;

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'show_in_catalog' => false,
        'allow_scrap' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'state',
        'read_level',
        'write_level',
        'created_by',
        'show_in_catalog',
        'allow_scrap',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_level' => 'integer',
        'write_level' => 'integer',
        'show_in_catalog' => 'boolean',
        'allow_scrap' => 'boolean',
    ];

    /**
     * Anciens libellés seed → nom d’orientation (caractéristique `spell_type_spell`).
     *
     * @var array<string, string>
     */
    public const LEGACY_NAME_ALIASES = [
        'Offensif' => 'Dégâts',
        'Offensifs' => 'Dégâts',
        'Défensif' => 'Protection',
        'Defensif' => 'Protection',
        'Buff' => 'Amélioration',
        'Buffs' => 'Amélioration',
        'Debuff' => 'Entrave',
        'Debuffs' => 'Entrave',
        'Téléportation' => 'Placement',
        'Teleportation' => 'Placement',
        'Transformation' => 'Tank',
    ];

    /**
     * Get the user that created the spell type.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les sorts de ce type.
     */
    public function spells()
    {
        return $this->belongsToMany(Spell::class, 'spell_type');
    }

    /**
     * Résout un type par nom d’orientation ou ancien libellé (Buff, Offensif…).
     */
    public static function findByLabel(?string $label): ?self
    {
        $name = trim((string) $label);
        if ($name === '') {
            return null;
        }

        $canonical = self::LEGACY_NAME_ALIASES[$name] ?? $name;

        return static::query()
            ->where(function ($query) use ($canonical, $name): void {
                $query->where('name', $canonical);
                if ($canonical !== $name) {
                    $query->orWhere('name', $name);
                }
            })
            ->first();
    }
}
