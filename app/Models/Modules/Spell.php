<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperSpell
 */
class Spell extends Model
{
    use HasFactory, SoftDeletes;

    const EXPRESSION_CAC = ["1", "0", "1,5", "1.5", "1,5m", "1.5m", "1m5", "1 mètre 5", "1 mètre 50", "1m50", "1mètre 50", "1mètre 5"];

    const ELEMENT_NEUTRE = 0;
    const ELEMENT_VITALITY = 1;
    const ELEMENT_SAGESSE = 2;
    const ELEMENT_TERRE = 3;
    const ELEMENT_FEU = 4;
    const ELEMENT_AIR = 5;
    const ELEMENT_EAU = 6;

    const ELEMENT_TERRE_FEU = 7;
    const ELEMENT_TERRE_AIR = 8;
    const ELEMENT_TERRE_EAU = 9;

    const ELEMENT_FEU_AIR = 10;
    const ELEMENT_FEU_EAU = 11;

    const ELEMENT_AIR_EAU = 12;

    const ELEMENT_TERRE_FEU_AIR = 13;
    const ELEMENT_TERRE_FEU_EAU = 14;
    const ELEMENT_TERRE_AIR_EAU = 15;
    const ELEMENT_FEU_AIR_EAU = 16;

    const ELEMENT_TERRE_FEU_AIR_EAU = 17;

    const ELEMENTS = [
        "Neutre" => self::ELEMENT_NEUTRE,
        "Vitalité" => self::ELEMENT_VITALITY,
        "Sagesse" => self::ELEMENT_SAGESSE,
        "Terre" => self::ELEMENT_TERRE,
        "Feu" => self::ELEMENT_FEU,
        "Air" => self::ELEMENT_AIR,
        "Eau" => self::ELEMENT_EAU,
        "Terre et Feu" => self::ELEMENT_TERRE_FEU,
        "Terre et Air" => self::ELEMENT_TERRE_AIR,
        "Terre et Eau" => self::ELEMENT_TERRE_EAU,
        "Feu et Air" => self::ELEMENT_FEU_AIR,
        "Feu et Eau" => self::ELEMENT_FEU_EAU,
        "Air et Eau" => self::ELEMENT_AIR_EAU,
        "Terre, Feu et Air" => self::ELEMENT_TERRE_FEU_AIR,
        "Terre, Feu et Eau" => self::ELEMENT_TERRE_FEU_EAU,
        "Terre, Air et Eau" => self::ELEMENT_TERRE_AIR_EAU,
        "Feu, Air et Eau" => self::ELEMENT_FEU_AIR_EAU,
        "Terre, Feu, Air et Eau" => self::ELEMENT_TERRE_FEU_AIR_EAU,
    ];

    const CATEGORIES = [
        "Sort de classe" => 0,
        "Sort de créature" => 1,
        "Sort apprenable" => 2,
        "Sort consommable" => 3
    ];

    const PATH_ICON_AREA = "icons/modules/spell_zone/";
    const AREAS = [
        0 => [
            "name" => "Pas de cible",
            "description" => "Il n'est pas nécessaire de cibler une créature pour lancer le sort.",
            "size" => "",
            "icon" => self::PATH_ICON_AREA . "untargeted.svg"
        ],
        1 => [
            "name" => "Une cible",
            "description" => "Le sort doit cibler une créature pour être lancé.",
            "size" => "1x1",
            "icon" => self::PATH_ICON_AREA . "targeted.svg"
        ],
        50 => [
            "name" => "Au corps à corps",
            "description" => "Le sort affecte toutes les créatures présentes au corps à corps d'une cible.",
            "size" => "3x3",
            "icon" => self::PATH_ICON_AREA . "cac.svg"
        ],
        51 => [
            "name" => "En croix au corps à corps",
            "description" => "Le sort affecte toutes les créatures présentes au corps à corps d'une cible dans une croix de 1 case (soit les 4 cases en opposées).",
            "size" => "2x2",
            "icon" => self::PATH_ICON_AREA . "croix_cac.svg"
        ],
        2 => [
            "name" => "Croix de 1 case",
            "description" => "Le sort affecte les créatures dans une croix de 1 case.",
            "size" => "3x3",
            "icon" => self::PATH_ICON_AREA . "croix_1.svg"
        ],
        3 => [
            "name" => "Croix de 2 cases",
            "description" => "Le sort affecte les créatures dans une croix de 2 cases.",
            "size" => "5x5",
            "icon" => self::PATH_ICON_AREA . "croix_2.svg"
        ],
        4 => [
            "name" => "Croix de 3 cases",
            "description" => "Le sort affecte les créatures dans une croix de 3 cases.",
            "size" => "7x7",
            "icon" => self::PATH_ICON_AREA . "croix_3.svg"
        ],
        5 => [
            "name" => "Croix de 4 cases",
            "description" => "Le sort affecte les créatures dans une croix de 4 cases.",
            "size" => "9x9",
            "icon" => self::PATH_ICON_AREA . "croix_4.svg"
        ],
        6 => [
            "name" => "Croix de 5 cases",
            "description" => "Le sort affecte les créatures dans une croix de 5 cases.",
            "size" => "11x11",
            "icon" => self::PATH_ICON_AREA . "croix_5.svg"
        ],
        7 => [
            "name" => "Croix de 6 cases",
            "description" => "Le sort affecte les créatures dans une croix de 6 cases.",
            "size" => "13x13",
            "icon" => self::PATH_ICON_AREA . "croix_6.svg"
        ],
        8 => [
            "name" => "Zone de 1x2 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 1 par 2 cases.",
            "size" => "1x2",
            "icon" => self::PATH_ICON_AREA . "cube1x2.svg"
        ],
        9 => [
            "name" => "Zone de 1x3 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 1 par 3 cases.",
            "size" => "1x3",
            "icon" => self::PATH_ICON_AREA . "cube1x3.svg"
        ],
        10 => [
            "name" => "Zone de 1x4 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 1 par 4 cases.",
            "size" => "1x4",
            "icon" => self::PATH_ICON_AREA . "cube1x4.svg"
        ],
        11 => [
            "name" => "Zone de 1x5 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 1 par 5 cases.",
            "size" => "1x5",
            "icon" => self::PATH_ICON_AREA . "cube1x5.svg"
        ],
        12 => [
            "name" => "Zone de 1x6 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 1 par 6 cases.",
            "size" => "1x6",
            "icon" => self::PATH_ICON_AREA . "cube1x6.svg"
        ],
        13 => [
            "name" => "Zone de 1x7 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 1 par 7 cases.",
            "size" => "1x7",
            "icon" => self::PATH_ICON_AREA . "cube1x7.svg"
        ],
        14 => [
            "name" => "Zone de 1x8 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 1 par 8 cases.",
            "size" => "1x8",
            "icon" => self::PATH_ICON_AREA . "cube1x8.svg"
        ],
        15 => [
            "name" => "Zone de 2x2 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 2 par 2 cases.",
            "size" => "2x2",
            "icon" => self::PATH_ICON_AREA . "cube2x2.svg"
        ],
        16 => [
            "name" => "Zone de 2x3 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 2 par 3 cases.",
            "size" => "2x3",
            "icon" => self::PATH_ICON_AREA . "cube2x3.svg"
        ],
        17 => [
            "name" => "Zone de 2x4 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 2 par 4 cases.",
            "size" => "2x4",
            "icon" => self::PATH_ICON_AREA . "cube2x4.svg"
        ],
        18 => [
            "name" => "Zone de 2x5 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 2 par 5 cases.",
            "size" => "2x5",
            "icon" => self::PATH_ICON_AREA . "cube2x5.svg"
        ],
        19 => [
            "name" => "Zone de 2x6 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 2 par 6 cases.",
            "size" => "2x6",
            "icon" => self::PATH_ICON_AREA . "cube2x6.svg"
        ],
        20 => [
            "name" => "Zone de 2x7 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 2 par 7 cases.",
            "size" => "2x7",
            "icon" => self::PATH_ICON_AREA . "cube2x7.svg"
        ],
        21 => [
            "name" => "Zone de 2x8 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 2 par 8 cases.",
            "size" => "2x8",
            "icon" => self::PATH_ICON_AREA . "cube2x8.svg"
        ],
        22 => [
            "name" => "Zone de 3x3 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 3 par 3 cases.",
            "size" => "3x3",
            "icon" => self::PATH_ICON_AREA . "cube3x3.svg"
        ],
        23 => [
            "name" => "Zone de 3x4 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 3 par 4 cases.",
            "size" => "3x4",
            "icon" => self::PATH_ICON_AREA . "cube3x4.svg"
        ],
        24 => [
            "name" => "Zone de 3x5 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 3 par 5 cases.",
            "size" => "3x5",
            "icon" => self::PATH_ICON_AREA . "cube3x5.svg"
        ],
        25 => [
            "name" => "Zone de 3x6 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 3 par 6 cases.",
            "size" => "3x6",
            "icon" => self::PATH_ICON_AREA . "cube3x6.svg"
        ],
        26 => [
            "name" => "Zone de 3x7 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 3 par 7 cases.",
            "size" => "3x7",
            "icon" => self::PATH_ICON_AREA . "cube3x7.svg"
        ],
        27 => [
            "name" => "Zone de 3x8 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 3 par 8 cases.",
            "size" => "3x8",
            "icon" => self::PATH_ICON_AREA . "cube3x8.svg"
        ],
        28 => [
            "name" => "Zone de 4x4 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 4 par 4 cases.",
            "size" => "4x4",
            "icon" => self::PATH_ICON_AREA . "cube4x4.svg"
        ],
        29 => [
            "name" => "Zone de 4x5 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 4 par 5 cases.",
            "size" => "4x5",
            "icon" => self::PATH_ICON_AREA . "cube4x5.svg"
        ],
        30 => [
            "name" => "Zone de 4x6 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 4 par 6 cases.",
            "size" => "4x6",
            "icon" => self::PATH_ICON_AREA . "cube4x6.svg"
        ],
        31 => [
            "name" => "Zone de 4x7 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 4 par 7 cases.",
            "size" => "4x7",
            "icon" => self::PATH_ICON_AREA . "cube4x7.svg"
        ],
        32 => [
            "name" => "Zone de 4x8 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 4 par 8 cases.",
            "size" => "4x8",
            "icon" => self::PATH_ICON_AREA . "cube4x8.svg"
        ],
        33 => [
            "name" => "Zone de 5x5 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 5 par 5 cases.",
            "size" => "5x5",
            "icon" => self::PATH_ICON_AREA . "cube5x5.svg"
        ],
        34 => [
            "name" => "Zone de 5x6 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 5 par 6 cases.",
            "size" => "5x6",
            "icon" => self::PATH_ICON_AREA . "cube5x6.svg"
        ],
        35 => [
            "name" => "Zone de 5x7 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 5 par 7 cases.",
            "size" => "5x7",
            "icon" => self::PATH_ICON_AREA . "cube5x7.svg"
        ],
        36 => [
            "name" => "Zone de 5x8 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 5 par 8 cases.",
            "size" => "5x8",
            "icon" => self::PATH_ICON_AREA . "cube5x8.svg"
        ],
        37 => [
            "name" => "Zone de 6x6 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 6 par 6 cases.",
            "size" => "6x6",
            "icon" => self::PATH_ICON_AREA . "cube6x6.svg"
        ],
        38 => [
            "name" => "Zone de 6x7 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 6 par 7 cases.",
            "size" => "6x7",
            "icon" => self::PATH_ICON_AREA . "cube6x7.svg"
        ],
        39 => [
            "name" => "Zone de 6x8 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 6 par 8 cases.",
            "size" => "6x8",
            "icon" => self::PATH_ICON_AREA . "cube6x8.svg"
        ],
        40 => [
            "name" => "Zone de 7x7 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 7 par 7 cases.",
            "size" => "7x7",
            "icon" => self::PATH_ICON_AREA . "cube7x7.svg"
        ],
        41 => [
            "name" => "Zone de 7x8 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 7 par 8 cases.",
            "size" => "7x8",
            "icon" => self::PATH_ICON_AREA . "cube7x8.svg"
        ],
        42 => [
            "name" => "Zone de 8x8 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur une surface de 8 par 8 cases.",
            "size" => "8x8",
            "icon" => self::PATH_ICON_AREA . "cube8x8.svg"
        ],
        43 => [
            "name" => "Barrière de 3x3 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur les cases en périphérie dans un périmètre de 3 par 3 cases.",
            "size" => "3x3",
            "icon" => self::PATH_ICON_AREA . "emptycube3x3.svg"
        ],
        44 => [
            "name" => "Barrière de 4x4 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur les cases en périphérie dans un périmètre de 4 par 4 cases.",
            "size" => "4x4",
            "icon" => self::PATH_ICON_AREA . "emptycube4x4.svg"
        ],
        45 => [
            "name" => "Barrière de 5x5 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur les cases en périphérie dans un périmètre de 5 par 5 cases.",
            "size" => "5x5",
            "icon" => self::PATH_ICON_AREA . "emptycube5x5.svg"
        ],
        46 => [
            "name" => "Barrière de 6x6 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur les cases en périphérie dans un périmètre de 6 par 6 cases.",
            "size" => "6x6",
            "icon" => self::PATH_ICON_AREA . "emptycube6x6.svg"
        ],
        47 => [
            "name" => "Barrière de 7x7 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur les cases en périphérie dans un périmètre de 7 par 7 cases.",
            "size" => "7x7",
            "icon" => self::PATH_ICON_AREA . "emptycube7x7.svg"
        ],
        48 => [
            "name" => "Barrière de 8x8 cases",
            "description" => "Le sort affecte toutes les créatures présentes sur les cases en périphérie dans un périmètre de 8 par 8 cases.",
            "size" => "8x8",
            "icon" => self::PATH_ICON_AREA . "emptycube8x8.svg"
        ],
        49 => [
            "name" => "En ligne infinie",
            "description" => "Le sort affecte toutes les créatures présentes sur la ligne sur un nombre de case défini par la portée maximale du sort.",
            "size" => "infinie",
            "icon" => self::PATH_ICON_AREA . "ligne_infini.svg"
        ],
    ];

    protected $fillable = [
        'official_id',
        'dofusdb_id',
        'uniqid',
        'name',
        'description',
        'effect',
        'effect_array',
        'area',
        'level',
        'po',
        'po_editable',
        'pa',
        'cast_per_turn',
        'cast_per_target',
        'sight_line',
        'number_between_two_cast',
        'element',
        'category',
        'is_magic',
        'is_reaction',
        'powerful',
        'usable',
        'is_visible',
        'created_by',
        'image',
        'auto_update',
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function mobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mob::class);
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Npc::class);
    }

    public function spelltypes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Spelltype::class);
    }

    public function invocations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mob::class, 'spell_invocation');
    }

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function scenarios(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Scenario::class);
    }

    public function imagePath(): string
    {
        return Storage::disk('modules')->url($this->image);
    }
}
