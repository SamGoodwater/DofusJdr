<?php

use function PHPUnit\Framework\isJson;

class Spell extends Content
{
    const EXPRESSION_CAC = ["1","0", "1,5", "1.5", "1,5m", "1.5m", "1m5", "1 mètre 5", "1 mètre 50", "1m50", "1mètre 50", "1mètre 5"];
    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/spells/default.svg",
            "dir" => "medias/modules/spells/",
            "preferential_format" => "svg",
            "naming" => "[uniqid]"
        ]
    ];

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

    const ELEMENT_FEU_AIR= 10;
    const ELEMENT_FEU_EAU= 11;

    const ELEMENT_AIR_EAU= 12;

    const ELEMENT_TERRE_FEU_AIR= 13;
    const ELEMENT_TERRE_FEU_EAU= 14;
    const ELEMENT_TERRE_AIR_EAU= 15;
    const ELEMENT_FEU_AIR_EAU= 16;

    const ELEMENT_TERRE_FEU_AIR_EAU= 17;

    const ELEMENT = [
        self::ELEMENT_NEUTRE => [
            "color" => "neutre",
            "name" => "Neutre"
        ],
        self::ELEMENT_VITALITY => [
            "color" => "vitality",
            "name" => "Vitalité"
        ],
        self::ELEMENT_SAGESSE => [
            "color" => "sagesse",
            "name" => "Sagesse"
        ],
        self::ELEMENT_TERRE => [
            "color" => "terre",
            "name" => "Terre"
        ],
        self::ELEMENT_FEU => [
            "color" => "feu",
            "name" => "Feu"
        ],
        self::ELEMENT_AIR => [
            "color" => "air",
            "name" => "Air"
        ],
        self::ELEMENT_EAU => [
            "color" => "eau",
            "name" => "Eau"
        ],
        self::ELEMENT_TERRE_FEU => [
            "color" => "terre-feu",
            "name" => "Terre et Feu"
        ],
        self::ELEMENT_TERRE_AIR => [
            "color" => "terre-air",
            "name" => "Terre et Air"
        ],
        self::ELEMENT_TERRE_EAU => [
            "color" => "terre-eau",
            "name" => "Terre et Eau"
        ],
        self::ELEMENT_FEU_AIR => [
            "color" => "feu-air",
            "name" => "Feu et Air"
        ],
        self::ELEMENT_FEU_EAU => [
            "color" => "feu-eau",
            "name" => "Feu et Eau"
        ],
        self::ELEMENT_AIR_EAU => [
            "color" => "air-eau",
            "name" => "Air et Eau"
        ],
        self::ELEMENT_TERRE_FEU_AIR => [
            "color" => "terre-feu-air",
            "name" => "Terre, Feu et Air"
        ],
        self::ELEMENT_TERRE_FEU_EAU => [
            "color" => "terre-feu-eau",
            "name" => "Terre, Feu et Eau"
        ],
        self::ELEMENT_TERRE_AIR_EAU => [
            "color" => "terre-air-eau",
            "name" => "Terre, Air et Eau"
        ],
        self::ELEMENT_FEU_AIR_EAU => [
            "color" => "feu-air-eau",
            "name" => "Feu, Air et Eau"
        ],
        self::ELEMENT_TERRE_FEU_AIR_EAU => [
            "color" => "terre-feu-air-eau",
            "name" => "Terre, Feu, Air et Eau"
        ]
    ];

    const TYPE_DAMAGE = 0;
    const TYPE_PROTECT = 1;
    const TYPE_BUFF = 2;
    const TYPE_DEBUFF = 3;
    const TYPE_INVOCATION = 4;   
    const TYPE_PLACEMENT = 5;   
    const TYPE_HEAL = 6;   
    const TYPE_TANK = 7;   

    const TYPE = [
        "Dommage" => self::TYPE_DAMAGE,
        "Protection" => self::TYPE_PROTECT,
        "Amélioration" => self::TYPE_BUFF,
        "Entrave" => self::TYPE_DEBUFF,
        "Invocation" => self::TYPE_INVOCATION,
        "Placement" => self::TYPE_PLACEMENT,
        "Soin" => self::TYPE_HEAL,
        "Tank" => self::TYPE_TANK
    ];

    const CATEGORY_CLASS = 1;
    const CATEGORY_MOB = 0;
    const CATEGORY_LEARNABLE = 2;
    const CATEGORY_CONSUMABLE = 3;

    const CATEGORY = [
        "Sort de classe" => self::CATEGORY_CLASS,
        "Sort de créature" => self::CATEGORY_MOB,
        "Sort apprenable" => self::CATEGORY_LEARNABLE,
        "Sort consommable" => self::CATEGORY_CONSUMABLE
    ];

    const PATH_ICON_AREA = "medias/icons/modules/spell_zone/";
    const AREA = [
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
    ];

    // ----------- EFFECTS
    // Variety
    const VARIETY_ATTACK = 0;
    const VARIETY_SAVE = 1;
    // Cible
    const CIBLE_ALL = 0;
    const CIBLE_ALLY = 1;
    const CIBLE_ENEMY = 2;
    const CIBLE_SELF = 3;
    // Type
    const EFFECT_TYPE = [
        'variety' => 0, // attack ou jet de sauvegarde
        'touch' => 1,
        'dd' => 2,
        'damage' => 3,
        'health' => 4,
        'lifesteal' => 5,
        'effect' => 6,
        'pa' => 7,
        'state' => 8,
        'malus_pa' => 9,
        'malus_pm' => 10,
        'malus_po' => 11,
        'malus_ca' => 12,
        'malus_touch' => 13,
        'malus_dodge_pa' => 14,
        'malus_dodge_pm' => 15,
        'vulnerability' => 16,
        'malus_damage' => 17,
        'bonus_pa' => 18,
        'bonus_pm' => 19,
        'bonus_po' => 20,
        'bonus_ca' => 21,
        'bonus_touch' => 22,
        'bonus_dodge_pa' => 23,
        'bonus_dodge_pm' => 24,
        'resistance' => 25,
        'bonus_damage' => 26,
        'area' => 27,
        'po' => 28,
        'po_editable' => 29,
        'sight_line' => 30,
        'cast_per_turn' => 31,
        'cast_per_target' => 32,
        'number_between_two_cast' => 33
    ];
    const OCCURENCE_TYPE_SEPARATOR = "||";

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_description='';
        private $_effect="";
        private $_effect_array="";
        private $_level=1;
        private $_po=1;
        private $_po_editable=true;
        private $_area = "";
        private $_pa=1;
        private $_cast_per_turn=1;
        private $_cast_per_target=1;
        private $_sight_line=false;
        private $_number_between_two_cast=0;
        private $_element = Spell::ELEMENT_NEUTRE;
        private $_category = Spell::CATEGORY_MOB;
        private $_is_magic = true;
        private $_powerful = 1;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Spell",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom",
                            "placeholder" => "Nom du sort",
                            "value" => $this->_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_name,
                            "color" => $this->getElement(Content::FORMAT_COLOR_VERBALE)."-d-2",
                            "tooltip" => "Nom du sort",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Spell",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "level",
                            "label" => "Niveau",
                            "placeholder" => "Niveau à partir duquel il est possible d'apprendre le sort",
                            "tooltip" => "Niveau à partir duquel il est possible d'apprendre le sort",
                            "value" => $this->_level,
                            "color" => Style::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Niveau {$this->_level}",
                            "color" => Style::getColorFromLetter($this->_level, true) . "-d-3",
                            "tooltip" => "Niveau à partir duquel il est possible d'apprendre le sort",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);
                
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_level,
                            "color" => "",
                            "tooltip" => "Niveau à partir duquel il est possible d'apprendre le sort",
                            "style" => Style::STYLE_NONE,
                            "class" => "text-".Style::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);
                
                default:
                    return $this->_level;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "Spell",
                            "id" => "description".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "description",
                            "label" => "Description",
                            "value" => $this->_description
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_description)){return "";}
                    return html_entity_decode($this->_description);
            }
        }
        public function getEffect(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "Spell",
                            "id" => "effect".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "effect",
                            "label" => "Effet du sort",
                            "value" => $this->_effect
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_effect)){return "";}
                    return html_entity_decode($this->_effect);
            }
        }
        public function getEffect_array(int $format = Content::FORMAT_BRUT){
            $test_tableau_type = [
                3 => [ // level
                    self::EFFECT_TYPE["variety"] => self::VARIETY_ATTACK,
                    self::EFFECT_TYPE['touch'] => [
                        "value" => "1d20 + [force] + [touch]", // Auto possible
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['dd'] => [
                        "value" => "[ca]", // ca par défault
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['damage'] => [
                        "value" => "1d6 + [force]",
                        "element" => self::ELEMENT_TERRE,
                        "cible" => self::CIBLE_ALL, // allies, ennemies, all
                        "duration" => 0, // Si dégât sur plusieurs tours comme un poison, si 0 ou rien alors dégât instantané
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['health'] => [
                        "value" => "1d6 + [intel]",
                        "element" => self::ELEMENT_FEU,
                        "cible" => self::CIBLE_ALL, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['lifesteal'] => [
                        "value" => "1d6 + [intel]",
                        "element" => self::ELEMENT_FEU,
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['effect'] => [
                        "value" => "Description de l'effet, avantage bonus ou malus ou autre",
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],  
                    self::EFFECT_TYPE['state'] => [
                        "value" => "Etat à appliquer",
                        "cible" => self::CIBLE_SELF, // soit même, allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['malus_pa'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['malus_pm'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['malus_po'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['malus_ca'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['malus_touch'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['malus_dodge_pa'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['malus_dodge_pm'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['vulnerability'] => [
                        "value" => "1",
                        "element" => self::ELEMENT_FEU,
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['malus_damage'] => [
                        "value" => "1",
                        "element" => self::ELEMENT_FEU, // Si rien, à tout les dégâts
                        "cible" => self::CIBLE_ENEMY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['bonus_pa'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ALLY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['bonus_pm'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ALLY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['bonus_po'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ALLY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['bonus_ca'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ALLY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['bonus_touch'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ALLY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['bonus_dodge_pa'] => [
                        "value" => "[Sagesse>1=1:Sagesse>5=5]",
                        "cible" => self::CIBLE_ALLY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['bonus_dodge_pm'] => [
                        "value" => "1",
                        "cible" => self::CIBLE_ALL, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['resistance'] => [
                        "value" => "1",
                        "element" => self::ELEMENT_FEU, // Si rien, à tout les dégâts
                        "cible" => self::CIBLE_ALLY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['bonus_damage'] => [
                        "value" => "1",
                        "element" => self::ELEMENT_FEU, // Si rien, à tout les dégâts
                        "cible" => self::CIBLE_ALLY, // allies, ennemies, all
                        "duration" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['area'] => [
                        "value" => self::AREA[3],
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['po'] => [
                        "value" => "1 + [bonus mastery]",
                        "comment" => ""
                    ],
                    self::EFFECT_TYPE['po_editable'] => [
                        "value" => false,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['sight_line'] => [
                        "value" => true,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['pa'] => [
                        "value" => 4,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['cast_per_turn'] => [
                        "value" => 1,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['cast_per_target'] => [
                        "value" => 1,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                    self::EFFECT_TYPE['number_between_two_cast'] => [
                        "value" => 0,
                        "comment" => "",
                        "critical" => "",
                        "occurence" => 0
                    ],
                ]
            ];

            $view = new View();
            $effects = [];
           
            if(isJson($this->_effect_array) && !empty($this->_effect_array)){
                $effects = json_decode($this->_effect_array, true);
            } elseif(is_array($this->_effect_array) && !empty($this->_effect_array)) {
                $effects = $this->_effect_array;
            } else {
                $effects_new = null;
            }
            $effects = $test_tableau_type;

            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div id="effect_array<?=$this->getUniqid()?>" class="m-2">
                            <button class="btn btn-sm btn-text-grey">Afficher l'éditeur en mode texte</button>
                            <input type="hidden" value="<?=$this->_effect_array?>">
                            <div class="edit_text">
                                <textarea class="form-control" rows="10"><?=$this->_effect_array?></textarea>
                                <button class="btn btn-sm btn-back-main"><i class="fa-solid fa-arrows-rotate"></i></button>
                            </div>
                            <div class="edit_gui">

                            </div>
                            <div class="d-flex justify-content-end align-item-baseline">
                                <button class="btn btn-sm btn-back-green"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
                                <div class="form-check form-check-inline ms-2">
                                    <input class="form-check-input" type="checkbox" id="checkboxAutoSave<?=$this->getUniqid()?>" value="1">
                                    <label class="form-check-label text-grey size-0-7" for="checkboxAutoSave<?=$this->getUniqid()?>">Auto enregistrement</label>
                                </div>
                            </div>
                        </div>
                    <?php return ob_get_clean();


                case Content::FORMAT_VIEW:
                    if(empty($effects)){return "";}
                    $variety = self::VARIETY_ATTACK;
                    $propsSortByLevelAndType = [];

                    foreach ($effects as $lvl => $val) { 
                        if(isset($val[self::EFFECT_TYPE['variety']])){ 
                            if(in_array($val[self::EFFECT_TYPE['variety']], [self::VARIETY_ATTACK, self::VARIETY_SAVE])){
                                $variety = $val[self::EFFECT_TYPE['variety']];
                            } else {
                                $variety = self::VARIETY_ATTACK;
                            }
                        }
                        foreach ($val as $type => $prop) {
                            switch ($type) {
                                // touch : type | value (défault = réussite automatique) | critical | comment | occurrence : type obligatoire
                                case self::EFFECT_TYPE['touch']:
                                    if(empty($prop['value'])){ $prop['value'] = "réussite automatique"; }
                                    $title = "Touche"; $tooltip = "Jet d'attaque pour toucher votre ou vos cible(s)";
                                    if($variety == self::VARIETY_SAVE){
                                        $title = "DD du jet de sauvegarde"; $tooltip = "Degré de difficulté que la cible doit réussir pour résister à votre sort";
                                    }
                                    $value = "";
                                    if(isset($prop['value'])){ 
                                        if(!empty($prop['value'])){
                                            $value = $this->formatEffectProp($prop['value']);
                                        }
                                    }
                                    $critical = "";
                                    if(isset($prop['critical'])){ 
                                        if(!empty($prop['critical'])){
                                            $critical = $this->formatEffectProp($prop['critical']);
                                        }
                                    }
                                    $comment = "";
                                    if(isset($prop['comment'])){ 
                                        if(!empty($prop['comment'])){
                                            $comment = trim($prop['comment']);
                                        }
                                    }
                                    $name_type_occurence = "";
                                    if(isset($prop['occurence'])){ 
                                        if(!empty($prop['occurence'])){
                                            if(is_numeric($prop['occurence'])){
                                                if($prop['occurence'] > 0){
                                                    $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                }elseif($prop['occurence'] < 0){
                                                    // Supprimer toutes les occurences de ce type
                                                    self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                    $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                }else{
                                                    // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                    $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                    $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                }
                                            }
                                        }
                                    }
                    
                                    $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                    $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                        template_name : "spell/effect/prop",
                                        data : [
                                            "title" => $title,
                                            "description" => $tooltip,
                                            "value" => $value,
                                            "critical" => $critical,
                                            "color" => "touch",
                                            "comment" => $comment
                                        ], 
                                        write: false);
                                break;
                                // DD : type | value (default = CA) | critical | comment | occurrence
                                case self::EFFECT_TYPE['dd']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Degré de difficulté"; $tooltip = "Valeur que vous devez atteindre pour que votre sort touche.";
                                        if($variety == self::VARIETY_SAVE){
                                            $title = "DD du Jet de sauvegarde"; $tooltip = "Degré de difficulté du jet de sauvegarde que la cible doit réussir pour résister à votre sort";
                                        }
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                        
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "color" => "grey",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;
                                // DÉGÂTS : type | value | element (default = neutre) | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['damage']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Dégâts"; $tooltip = "Dégâts infligés à la cible";
                                        $element = self::ELEMENT_NEUTRE;
                                        if(isset($prop['element'])){
                                            if(!empty($prop['element']) && isset(self::ELEMENT[$prop['element']])){
                                                $element = $prop['element'];
                                            }
                                        }
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = "";
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "element" => $element,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;
                                // SOIN : type | value | element (default = feu) | cible (default = alliés) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['health']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Soins"; $tooltip = "Soins prodigués à la cible";
                                        $element = self::ELEMENT_NEUTRE;
                                        if(isset($prop['element'])){
                                            if(!empty($prop['element']) && isset(self::ELEMENT[$prop['element']])){
                                                $element = $prop['element'];
                                            }
                                        }
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = "";
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "element" => $element,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;
                                // VOL DE VIE : type | value | element (default = feu) | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['lifesteal']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Vol de vie"; $tooltip = "Nombre de points de vie volés à la cible";
                                        $element = self::ELEMENT_NEUTRE;
                                        if(isset($prop['element'])){
                                            if(!empty($prop['element']) && isset(self::ELEMENT[$prop['element']])){
                                                $element = $prop['element'];
                                            }
                                        }
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = "";
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "element" => $element,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break; 
                                // EFFETS : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['effect']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Effets"; $tooltip = "Effets appliqués à la cible";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "grey",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break; 
                                // ÉTAT : type | value | cible (default = self) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['state']:
                                    if(!empty($prop['value'])){ 
                                        $title = "État"; $tooltip = "État appliqué à la cible";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "deep-purple",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break; 
                                // RETRAIT PA : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['malus_pa']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Retrait PA"; $tooltip = "Nombre de PA retiré à la cible";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "pa",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break; 
                                // RETRAIT PM : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['malus_pm']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Retrait PM"; $tooltip = "Nombre de PM retiré à la cible";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "pm",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }                                                     
                                break; 
                                // RETRAIT PO : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['malus_po']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Retrait PO"; $tooltip = "Nombre de PO retiré à la cible";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "po",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }  
                                break; 
                                // MALUS CA : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['malus_ca']:
                                    if(!empty($prop['value'])){ 
                                        $title = "malus CA"; $tooltip = "Malus infligé à la classe d'armure";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "ca",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }  
                                break;     
                                // MALUS TOUCHE : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence                                      
                                case self::EFFECT_TYPE['malus_touch']:                                                                       
                                    if(!empty($prop['value'])){ 
                                        $title = "malus de touche"; $tooltip = "Malus infligé à la touche";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "touch",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;
                                // MALUS ESQUIVE PA : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence                                      
                                case self::EFFECT_TYPE['malus_dodge_pa']:                                                           
                                    if(!empty($prop['value'])){ 
                                        $title = "malus à l'esquive PA"; $tooltip = "Malus infligé à la l'esquive PA";  
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "dodge_pa",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }  
                                break;
                                // MALUS ESQUIVE PM : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['malus_dodge_pm']:
                                    if(!empty($prop['value'])){ 
                                        $title = "malus à l'esquive PM"; $tooltip = "Malus infligé à la l'esquive PM";  
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "dodge_pm",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    } 
                                break;
                                // VULNERABILITE : type | value | element (default = neutre) | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['vulnerability']:  
                                    if(!empty($prop['value'])){ 
                                        $title = "Vulnérabilités"; $tooltip = "Nombre de dommage supplémentaire que la cible perd lors ce qu'on l'attaque";
                                        $element = self::ELEMENT_NEUTRE;
                                        if(isset($prop['element'])){
                                            if(!empty($prop['element']) && isset(self::ELEMENT[$prop['element']])){
                                                $element = $prop['element'];
                                            }
                                        }
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = "";
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "element" => $element,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;                                                    
                                // malus dégâts : type | value | element (default = neutre) | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence                                          
                                case self::EFFECT_TYPE['malus_damage']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Malus aux dégâts"; $tooltip = "Nombre de dommage que la cible perd lors de ces attaques. Ce malus peut-être lié à un élément.";
                                        $element = self::ELEMENT_NEUTRE;
                                        if(isset($prop['element'])){
                                            if(!empty($prop['element']) && isset(self::ELEMENT[$prop['element']])){
                                                $element = $prop['element'];
                                            }
                                        }
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = "";
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "element" => $element,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;
                                // BONUS PA : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence                                      
                                case self::EFFECT_TYPE['bonus_pa']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Bonus PA"; $tooltip = "Nombre de PA ajouté à la cible";     
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "pa",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }  
                                break;
                                // BONUS PM : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['bonus_pm']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Bonus PM"; $tooltip = "Nombre de PM ajouté à la cible";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "pm",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }  
                                break;
                                // BONUS PO : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['bonus_po']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Bonus PO"; $tooltip = "Nombre de PO ajouté à la cible";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "po",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;
                                // BONUS CA : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['bonus_ca']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Bonus CA"; $tooltip = "Bonus de classe d'armure ajouté à la cible";  
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "ca",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;
                                // BONUS TOUCHE : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['bonus_touch']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Bonus de touche"; $tooltip = "Bonus de touche aux attaques ajouté à la cible";  
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "touch",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }  
                                break;
                                // BONUS ESQUIVE PA : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence                                      
                                case self::EFFECT_TYPE['bonus_dodge_pa']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Bonus d'esquive PA"; $tooltip = "Bonus d'esquive PA ajouté à la cible"; 
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "dodge_pa",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }  
                                break;
                                // BONUS ESQUIVE PA : type | value | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence                                      
                                case self::EFFECT_TYPE['bonus_dodge_pm']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Bonus d'esquive PM"; $tooltip = "Bonus d'esquive Pm ajouté à la cible";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "dodge_pm",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;                                                    
                                // RESISTANCE : type | value | element (default = neutre) | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['resistance']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Résistance"; $tooltip = "Nombre de dommage que la cible évite lors ce qu'on l'attaque";
                                        $element = self::ELEMENT_NEUTRE;
                                        if(isset($prop['element'])){
                                            if(!empty($prop['element']) && isset(self::ELEMENT[$prop['element']])){
                                                $element = $prop['element'];
                                            }
                                        }
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = "";
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "element" => $element,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break; 
                                // BONUS DEGATS : type | value | element (default = neutre) | cible (default = ennemies) | duration (default = 0) | critical | comment | occurrence
                                case self::EFFECT_TYPE['bonus_damage']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Bonus de dégâts"; $tooltip = "Bonus de dégâts que la cible ajoute à ces propres dommages lors de ces attaques.";      
                                        $element = self::ELEMENT_NEUTRE;
                                        if(isset($prop['element'])){
                                            if(!empty($prop['element']) && isset(self::ELEMENT[$prop['element']])){
                                                $element = $prop['element'];
                                            }
                                        }
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                $value = $this->formatEffectProp($prop['value']);
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = "";
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "element" => $element,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;
                                // ZONE : type | (int) value (default = self::AREA[3]) | critical | comment | occurrence
                                case self::EFFECT_TYPE['area']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Zone"; $tooltip = "Zone d'effet du sort"; 
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                if(is_numeric($prop['value'])){
                                                    if(isset(self::AREA[$prop['value']])){
                                                        $spell_temp = new Spell();
                                                        $spell_temp->setArea($prop['value']);
                                                        echo $spell_temp->getArea(Content::FORMAT_BADGE);
                                                        unset($spell_temp);
                                                    } 
                                                }
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "main",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }  
                                break;
                                // PO : type | (int) value (default = self::AREA[3]) | critical | comment | occurrence
                                case self::EFFECT_TYPE['po']:
                                    if(!empty($prop['value'])){ 
                                        $title = "PO"; $tooltip = "Distance maximale jusqu'à laquelle le sort peut-être lancer";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                if(is_numeric($prop['value'])){
                                                    $spell_temp = new Spell();
                                                    $spell_temp->setPo($prop['value']);
                                                    echo $spell_temp->getPo(Content::FORMAT_BADGE);
                                                    unset($spell_temp);
                                                }
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "po",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    } 
                                break;   
                                // PO MODIFIABLE : type | (bool) value (default = self::AREA[3]) | critical | comment | occurrence
                                case self::EFFECT_TYPE['po_editable']: 
                                    if(!empty($prop['value'])){ 
                                        $title = "PO Modifiable"; $tooltip = "Défini si la portée du sort est modifiable ou non";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                if(is_bool($prop['value'])){
                                                    $spell_temp = new Spell();
                                                    $spell_temp->setPo_editable($prop['value']);
                                                    echo $spell_temp->getPo_editable(Content::FORMAT_BADGE);
                                                    unset($spell_temp);
                                                }
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "po_editable",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    } 
                                break;  
                                // LIGNE DE VUE : type | (bool) value (default = self::AREA[3]) | critical | comment | occurrence
                                case self::EFFECT_TYPE['sight_line']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Ligne de vue"; $tooltip = "Défini si la ligne de vue sur la zone d'effet est obligatoire pour lancer le sort";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                if(is_bool($prop['value'])){
                                                    $spell_temp = new Spell();
                                                    $spell_temp->setSight_line($prop['value']);
                                                    echo $spell_temp->getSight_line(Content::FORMAT_BADGE);
                                                    unset($spell_temp);
                                                }
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "sight_line",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;  
                                // PA : type | (bool) value (default = self::AREA[3]) | critical | comment | occurrence
                                case self::EFFECT_TYPE['pa']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Coût en PA"; $tooltip = "Défini le coût en PA du sort";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                if(is_numeric($prop['value'])){
                                                    $spell_temp = new Spell();
                                                    $spell_temp->setPa($prop['value']);
                                                    echo $spell_temp->getPa(Content::FORMAT_BADGE);
                                                    unset($spell_temp);
                                                }
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "pa",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }  
                                break;
                                // Nombre de lancer par tour : type | value (default = self::AREA[3]) | critical | comment | occurrence
                                case self::EFFECT_TYPE['cast_per_turn']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Nombre de lancer par tour"; $tooltip = "Défini le nombre de lancer du sort par tour";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                if(is_numeric($prop['value'])){
                                                    $spell_temp = new Spell();
                                                    $spell_temp->setCast_per_turn($prop['value']);
                                                    echo $spell_temp->getCast_per_turn(Content::FORMAT_BADGE);
                                                    unset($spell_temp);
                                                }
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "cast_per_turn",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    }
                                break;
                                // Utilisation par cible : type | value (default = self::AREA[3]) | critical | comment | occurrence
                                case self::EFFECT_TYPE['cast_per_target']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Nombre de lancer par cible"; $tooltip = "Défini le nombre de lancer du sort une cible pendant un tour";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                if(is_numeric($prop['value'])){
                                                    $spell_temp = new Spell();
                                                    $spell_temp->setCast_per_target($prop['value']);
                                                    echo $spell_temp->getCast_per_target(Content::FORMAT_BADGE);
                                                    unset($spell_temp);
                                                }
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "cast_per_target",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    } 
                                break;
                                // nombre de tour entre deux lancer : type | value (default = self::AREA[3]) | critical | comment | occurrence
                                case self::EFFECT_TYPE['number_between_two_cast']:
                                    if(!empty($prop['value'])){ 
                                        $title = "Nombre de lancer par cible"; $tooltip = "Défini le nombre de lancer du sort une cible pendant un tour";
                                        $value = "";
                                        if(isset($prop['value'])){ 
                                            if(!empty($prop['value'])){
                                                if(is_numeric($prop['value'])){
                                                    $spell_temp = new Spell();
                                                    $spell_temp->setNumber_between_two_cast($prop['value']);
                                                    echo $spell_temp->getNumber_between_two_cast(Content::FORMAT_BADGE);
                                                    unset($spell_temp);
                                                }
                                            }
                                        }
                                        $critical = "";
                                        if(isset($prop['critical'])){ 
                                            if(!empty($prop['critical'])){
                                                $critical = $this->formatEffectProp($prop['critical']);
                                            }
                                        }
                                        $duration = 1;
                                        if(isset($prop['duration'])){ 
                                            if(!empty($prop['duration'])){
                                                $duration = $prop['duration'];
                                            }
                                        }
                                        $cible = "";
                                        if(isset($prop['cible'])){ 
                                            if(!empty($prop['cible'])){
                                                $cible = $prop['cible'];
                                            }
                                        }
                                        $comment = "";
                                        if(isset($prop['comment'])){ 
                                            if(!empty($prop['comment'])){
                                                $comment = trim($prop['comment']);
                                            }
                                        }
                                        $name_type_occurence = "";
                                        if(isset($prop['occurence'])){ 
                                            if(!empty($prop['occurence'])){
                                                if(is_numeric($prop['occurence'])){
                                                    if($prop['occurence'] > 0){
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.$prop['occurence'];
                                                    }elseif($prop['occurence'] < 0){
                                                        // Supprimer toutes les occurences de ce type
                                                        self::deleteOccurrencesOfType($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR."1";
                                                    }else{
                                                        // Ajouter une nouvelle occurence de ce type en ajouter +1 à la dernière occurence
                                                        $lastOccurence = self::getLastOccurrence($type, $propsSortByLevelAndType[$lvl]);
                                                        $name_type_occurence = $type.Spell::OCCURENCE_TYPE_SEPARATOR.($lastOccurence + 1);
                                                    }
                                                }
                                            }
                                        }
                            
                                        $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                                        $propsSortByLevelAndType[$lvl][$name_type_occurence] = $view->dispatch(
                                            template_name : "spell/effect/prop",
                                            data : [
                                                "title" => $title,
                                                "description" => $tooltip,
                                                "value" => $value,
                                                "critical" => $critical,
                                                "duration" => $duration,
                                                "cible" => $cible,
                                                "color" => "number_between_two_cast",
                                                "comment" => $comment
                                            ], 
                                            write: false);
                                    } 
                                break;
                            }
                        }
                    }

                    $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                    return $view->dispatch(
                        template_name : "spell/effect/list",
                        data : [
                            "propsSortByLevelAndType" => $propsSortByLevelAndType,
                            "spell" => $this
                        ], 
                        write: false);

                case Content::FORMAT_ARRAY:
                    return $effects;

                default:
                    return $this->_effect_array;
            }
        }
        public function getPo(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Spell",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "po",
                            "label" => "Portée",
                            "placeholder" => "Portée du sort",
                            "tooltip" => "Portée du sort (en case)",
                            "value" => $this->_po,
                            "color" => "po-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "po.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "color_icon" => "po-d-4"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_po} PO",
                            "color" => "po-d-2",
                            "tooltip" => "Portée du sort (en case)",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "po.png",
                            "color" => "po-d-2",
                            "tooltip" => "Portée du sort (en case)",
                            "content" => $this->_po,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);

                default:
                    return $this->_po;
            }
        }
        public function getPo_editable(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-start align-items-center"><?php
                            $view->dispatch(
                                template_name : "badge",
                                data : [
                                    "content" => "Portée Non modifiable",
                                    "color" => "grey-d-2",
                                    "style" => Style::STYLE_BACK,
                                    "tooltip" => "La portée du sort n'est pas modifiable",
                                    "tooltip_placement" => Style::DIRECTION_TOP,
                                    "class" => "me-3"
                                ], 
                                write: true);
                            
                            $view->dispatch(
                                template_name : "input/checkbox",
                                data : [
                                    "class_name" => "Spell",
                                    "uniqid" => $this->getUniqid(),
                                    "id" => "po_editable_" . $this->getUniqid(),
                                    "input_name" => "po_editable",
                                    "label" => "",
                                    "color" => "po_editable-d-2",
                                    "checked" => $this->returnBool($this->_po_editable),
                                    "style" => Style::CHECK_SWITCH
                                ], 
                                write: true);

                            $view->dispatch(
                                template_name : "badge",
                                data : [
                                    "content" => "Portée modifiable",
                                    "color" => "po_editable-d-2",
                                    "style" => Style::STYLE_BACK,
                                    "tooltip" => "La portée du sort est modifiable",
                                    "tooltip_placement" => Style::DIRECTION_TOP,
                                    "css" => "me-1"
                                ], 
                                write: true);?>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if($this->_po_editable && !in_array($this->getPO(), Spell::EXPRESSION_CAC)){ // Sort à distance Avec portée modifiable
                        $content = "PO modifiable";
                        $tooltip = "La portée du sort est modifiable";
                        $color = "po_editable-d-2";
                    } elseif(in_array($this->getPO(), Spell::EXPRESSION_CAC)) { // Sort au CàC
                        $content = "CàC";
                        $tooltip = "Le sort est un sort de corps à corps - c'est à dire un sort avec un rayon d'action d'1m50 maximum.";
                        $color = "red-d-2";
                    }else{ // Sort à distane sans portée modifiable
                        $content = "PO non modifiable";
                        $tooltip = "La portée du sort n'est pas modifiable";
                        $color = "grey-d-2";
                    }
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $content,
                            "color" => $color,
                            "style" => Style::STYLE_OUTLINE,
                            "tooltip" => $tooltip
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    if($this->_po_editable && !in_array($this->getPO(), Spell::EXPRESSION_CAC)){ // Sort à distance Avec portée modifiable
                        $icon = "po_editable.png";
                        $tooltip = "La portée du sort est modifiable";
                    } elseif(in_array($this->getPO(), Spell::EXPRESSION_CAC)) { // Sort au CàC
                        $icon = "cac.png";
                        $tooltip = "Le sort est un sort de corps à corps - c'est à dire un sort avec un rayon d'action d'1m50 maximum.";
                    }else{ // Sort à distane sans portée modifiable
                        $icon = "po_no_editable.png";
                        $tooltip = "La portée du sort n'est pas modifiable";
                    }
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => $icon,
                            "tooltip" => $tooltip
                        ], 
                        write: false); 

                case Content::FORMAT_PATH:
                    if($this->_po_editable && !in_array($this->getPO(), Spell::EXPRESSION_CAC)){ // Sort à distance Avec portée modifiable
                        return "medias/icons/modules/po_editable.png";
                    } elseif(in_array($this->getPO(), Spell::EXPRESSION_CAC)) { // Sort au CàC
                        return "medias/icons/modules/cac.png";
                    } else { 
                        return "medias/icons/modules/po_no_editable.png";   
                    }
                    
                default:
                    return $this->_po_editable;
            }
        }
        public function getArea(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach(self::AREA as $id => $area) { 
                        ob_start() ?>
                            <p data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$area['description']?>"><span><img class="icon icon-25 me-2" src="<?=$area['icon']?>" alt=""><?=$area['name']?></span><br><span class='text-grey size-0-7'><?=$area['description']?></span></p>
                        <?php $visual = ob_get_clean();
                        $items[] = [
                            "onclick" => "Spell.update('".$this->getUniqid()."', ".$id.", 'area', ".Controller::IS_VALUE.");",
                            "display" => $visual
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Zone d'effet du sort",
                            "label" => $this->getArea(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items
                        ], 
                        write: false);
                    
                case Content::FORMAT_BADGE:
                    if(empty($this->_area)){return "";}
                    if(isset(self::AREA[$this->_area])){
                        $name = self::AREA[$this->_area]['name'];
                        $description = self::AREA[$this->_area]['description'];
                        $icon = self::AREA[$this->_area]['icon'];
                    } else {
                        $name = self::AREA[1]['name'];
                        $description = self::AREA[1]['description'];
                        $icon = self::AREA[1]['icon'];
                    }
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "icon" => $icon,
                            "size" => 40,
                            "style" => Style::ICON_MEDIA,
                            "dirfile" => "",
                            "content_placement" => Style::POSITION_LEFT,
                            "content" => $name,
                            "tooltip" => $description
                        ],
                        write: false);

                case Content::FORMAT_ICON:
                    if(empty($this->_area)){return "";}
                    if(isset(self::AREA[$this->_area])){
                        $name = self::AREA[$this->_area]['name'];
                        $description = self::AREA[$this->_area]['description'];
                        $icon = self::AREA[$this->_area]['icon'];
                    } else {
                        $name = self::AREA[1]['name'];
                        $description = self::AREA[1]['description'];
                        $icon = self::AREA[1]['icon'];
                    }
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "icon" => $icon,
                            "size" => 40,
                            "style" => Style::ICON_MEDIA,
                            "dirfile" => "",
                            "content_placement" => Style::POSITION_LEFT,
                            "tooltip" => $description
                        ],
                        write: false);

                case Content::FORMAT_PATH:
                    if(empty($this->_area)){return "";}
                    if(isset(self::AREA[$this->_area])){
                        $icon = self::AREA[$this->_area]['icon'];
                    } else {
                        $icon = self::AREA[1]['icon'];
                    }
                    return $icon;

                case Content::FORMAT_ARRAY:
                    if(empty($this->_area)){return "";}
                    if(isset(self::AREA[$this->_area])){
                        return self::AREA[$this->_area];
                    } else {
                        return self::AREA[1];
                    }

                default:
                    return $this->_area;
            }
        }
        public function getPa(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Spell",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "pa",
                            "label" => "Points d'action",
                            "placeholder" => "Points d'action",
                            "tooltip" => "Coût en point d'action du sort",
                            "value" => $this->_pa,
                            "color" => "pa-d-2",
                            "style" => Style::INPUT_ICON,
                            "icon" => "pa.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_pa} PA",
                            "color" => "pa-d-2",
                            "tooltip" => "Coût en point d'action du sort",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    ob_start();?>
                        <div class="move_icon_pa ms-2"><?php
                            $view->dispatch(
                                template_name : "icon",
                                data : [
                                    "style" => Style::ICON_MEDIA,
                                    "icon" => "pa.png",
                                    "size" => 35,
                                    "color" => "pa-d-2",
                                    "tooltip" => "Coût en point d'action du sort",
                                    "content" => $this->_pa,
                                    "content_placement" => Style::POSITION_LEFT
                                ], 
                                write: true);
                            ?> </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_pa;
            }
        }
        public function getCast_per_turn(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Spell",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "cast_per_turn",
                            "label" => "Nombre de lancer par tour",
                            "placeholder" => "1",
                            "tooltip" => "Nombre de fois que le sort peut-être lancer par tour",
                            "comment" => "Nombre de fois que le sort peut-être lancer par tour",
                            "value" => $this->_cast_per_turn,
                            "color" => "cast_per_turn-d-2",
                            "style" => Style::INPUT_ICON,
                            "icon" => "cast_per_turn.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_cast_per_turn} fois / tour",
                            "color" => "cast_per_turn-d-2",
                            "tooltip" => "Nombre de fois que le sort peut-être lancer par tour",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "cast_per_turn.png",
                            "color" => "cast_per_turn-d-2",
                            "tooltip" => "Nombre de fois que le sort peut-être lancer par tour",
                            "content" => $this->_cast_per_turn,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);
                
                default:
                    return $this->_cast_per_turn;
            }
        }
        public function getCast_per_target(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Spell",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "cast_per_target",
                            "label" => "Utilisation par cible",
                            "placeholder" => "1",
                            "tooltip" => "Nombre de fois que le sort peut-être lancer par tour sur une même cible",
                            "comment" => "Nombre de fois que le sort peut-être lancer par tour sur une même cible",
                            "value" => $this->_cast_per_target,
                            "color" => "cast_per_target-d-2",
                            "style" => Style::INPUT_ICON,
                            "icon" => "²",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_cast_per_target} fois / cible / tour",
                            "color" => "cast_per_target-d-2",
                            "tooltip" => "Nombre de fois que le sort peut-être lancer par tour sur une même cible",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "cast_per_target.png",
                            "color" => "cast_per_target-d-2",
                            "tooltip" => "Nombre de fois que le sort peut-être lancer par tour sur une même cible",
                            "content" => $this->_cast_per_target,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);
                
                default:
                    return $this->_cast_per_target;
            }
        }
        public function getSight_line(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/checkbox",
                        data : [
                            "class_name" => "Spell",
                            "uniqid" => $this->getUniqid(),
                            "id" => "sight_line" . $this->getUniqid(),
                            "input_name" => "sight_line",
                            "label" => $this->getSight_line(Content::FORMAT_BADGE),
                            "color" => "sight_line-d-2",
                            "checked" => $this->returnBool($this->_sight_line),
                            "style" => Style::CHECK_SWITCH,
                            "tooltip" => "Permet de choisir si le sort necessite d'avoir la ligne de vue sur la cible pour pouvoir être lancer.",
                            "content" => "Permet de choisir si le sort necessite d'avoir la ligne de vue sur la cible pour pouvoir être lancer."
                        ], 
                        write: false);
                    
                case Content::FORMAT_BADGE:
                    if($this->_sight_line){
                        $style = Style::STYLE_BACK;
                        $content = "Ligne de vue";
                        $tooltip = "Ligne de vue obligatoire pour lancer le sort";
                    } else {
                        $style = Style::STYLE_OUTLINE;
                        $content = "Pas de ligne de vue";
                        $tooltip = "Pas besoin de ligne de vue pour lancer le sort";
                    }
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $content,
                            "color" => "sight-line-d-2",
                            "style" => $style,
                            "tooltip" => $tooltip
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    if($this->_sight_line){
                        $icon = "sight_line.png";
                        $tooltip = "Ligne de vue obligatoire pour lancer le sort";
                    } else {
                        $icon = "no_sight_line.png";
                        $tooltip = "Pas besoin de ligne de vue pour lancer le sort";
                    }
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => $icon,
                            "tooltip" => $tooltip
                        ], 
                        write: false); 

                case Content::FORMAT_PATH:
                    if($this->_sight_line){ 
                        return "medias/icons/modules/sight_line.png";
                    } else { 
                        return "medias/icons/modules/no_sight_line.png";
                    }
                    
                default:
                    return $this->_sight_line;
            }
        }
        public function getNumber_between_two_cast(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Spell",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "number_between_two_cast",
                            "label" => "Nombre de tour entre deux lancer de sort",
                            "placeholder" => "0",
                            "tooltip" => "Nombre de fois que le sort peut-être lancer par tour",
                            "comment" => "Nombre de fois que le sort peut-être lancer par tour",
                            "value" => $this->_number_between_two_cast,
                            "color" => "number_between_two_cast-d-2",
                            "style" => Style::INPUT_ICON,
                            "icon" => "number_between_two_cast.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $number_between_two_cast = $this->_number_between_two_cast; $s = "(s)"; if($this->_number_between_two_cast <= 1){$number_between_two_cast = ""; $s = ""; }
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "n fois / {$number_between_two_cast} tour".$s,
                            "color" => "number_between_two_cast-d-2",
                            "tooltip" => "Nombre de tour entre deux lancer de sort",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "number_between_two_cast.png",
                            "color" => "number_between_two_cast-d-2",
                            "tooltip" => "Nombre de tour entre deux lancer de sort",
                            "content" => $this->_number_between_two_cast,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);
                
                default:
                    return $this->_number_between_two_cast;
            }
        }
        public function getElement(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach(self::ELEMENT as $id_element => $element) { 
                        $items[] = [
                            "onclick" => "Spell.update('".$this->getUniqid()."', ".$id_element.", 'element', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-".$element['color']."-d-2'>" .ucfirst($element['name'])."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Element(s) du sort",
                            "label" => $this->getElement(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(isset(self::ELEMENT[$this->_element])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(self::ELEMENT[$this->_element]['name']),
                                "color" => self::ELEMENT[$this->_element]['color'] ."-d-2",
                                "tooltip" => "Element(s) du sort",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_COLOR_VERBALE:
                    if(isset(self::ELEMENT[$this->_element])){
                        return strtolower(self::ELEMENT[$this->_element]['color']);
                    } else {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(isset(self::ELEMENT[$this->_element])){
                        return strtolower(self::ELEMENT[$this->_element]['name']);
                    } else {
                        return "";
                    }

                default:
                    return $this->_element;
            }

        }
        public function getCategory(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach(self::CATEGORY as $name => $category) { 
                        $items[] = [
                            "onclick" => "Spell.update('".$this->getUniqid()."', ".$category.", 'category', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-".Style::getColorFromLetter($category)."-d-2'>" .ucfirst($name)."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Catégorie du sort",
                            "label" => $this->getCategory(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_category,  self::CATEGORY)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => array_search($this->_category, self::CATEGORY),
                                "color" => Style::getColorFromLetter($this->_category)."-d-2",
                                "tooltip" => "Catégorie du sort",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($this->_category, self::CATEGORY)){
                        return array_search($this->_category, self::CATEGORY);
                    } else {
                        return "";
                    }

                default:
                    return $this->_category;
            }
        }
        public function getIs_magic(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-start align-items-center"><?php
                            $view->dispatch(
                                template_name : "badge",
                                data : [
                                    "content" => "Physique",
                                    "color" => "brown-d-2",
                                    "style" => Style::STYLE_BACK,
                                    "tooltip" => "Le sort est physique.",
                                    "tooltip_placement" => Style::DIRECTION_TOP,
                                    "class" => "me-1"
                                ], 
                                write: true);
                            
                            $view->dispatch(
                                template_name : "input/checkbox",
                                data : [
                                    "class_name" => "Spell",
                                    "uniqid" => $this->getUniqid(),
                                    "id" => "is_magic_" . $this->getUniqid(),
                                    "input_name" => "is_magic",
                                    "label" => "",
                                    "color" => "main",
                                    "checked" => $this->returnBool($this->_is_magic),
                                    "style" => Style::CHECK_SWITCH
                                ], 
                                write: true);

                            $view->dispatch(
                                template_name : "badge",
                                data : [
                                    "content" => "Wakfu",
                                    "color" => "purple-d-2",
                                    "style" => Style::STYLE_BACK,
                                    "tooltip" => "Le sort est magique.",
                                    "tooltip_placement" => Style::DIRECTION_TOP,
                                    "css" => "me-1"
                                ], 
                                write: true);?>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if($this->_is_magic){
                        $color = "purple-d-2";
                        $content = "Wakfu";
                        $tooltip = "Le sort est magique.";
                    } else {
                        $color = "brown-d-2";
                        $content = "Physique";
                        $tooltip = "Le sort est physique.";
                    }
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $content,
                            "color" => $color,
                            "style" => Style::STYLE_BACK,
                            "tooltip" => $tooltip,
                            "tooltip_placement" => Style::DIRECTION_TOP
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    if($this->_is_magic){
                        $color = "purple-d-2";
                        $icon = "magic";
                        $tooltip = "Le sort est magique.";
                    } else {
                        $color = "brown-d-2";
                        $icon = "fist-raised";
                        $tooltip = "Le sort est physique.";
                    }
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_SOLID,
                            "icon" => $icon,
                            "color" => $color,
                            "tooltip" => $tooltip
                        ], 
                        write: false); 
                    
                default:
                    return $this->_is_magic;
            }
        }
        public function getPowerful(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    for ($i=1; $i <= 9 ; $i++) { 
                        $items[] = [
                            "onclick" => "Spell.update('".$this->getUniqid()."', ".$i.", 'powerful', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-deep-purple-d-3'>Puissance " .$i."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Puissance d'un sort sur 9 niveaux",
                            "label" => $this->getPowerful(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items,
                            "comment" => "Puissance d'un sort sur 9 niveaux"
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_powerful,  [1,2,3,4,5,6,7,8,9])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "Puissance ".$this->_powerful,
                                "color" => "deep-purple-d-3",
                                "tooltip" => "Puissance d'un sort sur 9 niveaux",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($this->_powerful, [1,2,3,4,5,6,7,8,9])){
                        return "Puissance " . $this->_powerful;
                    } else {
                        return "";
                    }

                default:
                    return $this->_powerful;
            }
        }

        public function getType(int $format = Content::FORMAT_BRUT, bool $is_remove = false){
            $view = new View();
            $manager = new SpellManager();
            $types = $manager->getLinkType($this);
            
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach(self::TYPE as $name => $type) { 
                        $items[] = [
                            "onclick" => "Spell.update('".$this->getUniqid()."',{action:'add', type:'".$type."'},'type', IS_VALUE);",
                            "display" => "<span class='w-100 btn border-0 border-bottom-1-hover border-".Style::getColorFromLetter($type)."-d-4'>" .ucfirst($name)."</span>"
                        ];
                    }

                    ob_start();
                        $view->dispatch(
                            template_name : "dropdown",
                            data : [
                                "tooltip" => "Types du sort",
                                "label" => $this->getType(Content::FORMAT_BADGE),
                                "size" => Style::SIZE_SM,
                                "items" => $items
                            ], 
                            write: true);

                        echo $this->getType(Content::FORMAT_BADGE, true);

                    return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); 
                        if(!empty($types)){?>
                            <div class="d-flex flex-row justify-content-around flex-wrap gap-1 p-1">
                                <?php foreach ($types as $type) {
                                    $view->dispatch(
                                        template_name : "badge",
                                        data : [
                                            "content" => array_search($type, Spell::TYPE),
                                            "color" => Style::getColorFromLetter($type) . "-d-4",
                                            "tooltip" => "Puissance d'un sort sur 9 niveaux",
                                            "style" => Style::STYLE_OUTLINE,
                                            "onclick" => "Spell.update('".$this->getUniqid()."',{action:'remove', type:'".$type."'},'type', IS_VALUE);$(this).remove();"
                                        ], 
                                        write: true);
                                } ?>
                            </div>
                        <?php }
                    return ob_get_clean();

                case Content::FORMAT_TEXT:
                    if(!empty($types)){
                        $array = [];
                        foreach ($types as $type) {
                            $array[$type] = array_search($type, Spell::TYPE);
                        }
                        return $array;
                    } else {
                        return [];
                    }
                    
                case Content::FORMAT_ARRAY:
                    return $types;
                
            }
        }

        public function getMob(int $format = Content::FORMAT_BRUT, bool $display_remove = false, $size = 300){return $this->getInvocation($format, $display_remove, $size);}
        public function getInvocation(int $format = Content::FORMAT_BRUT, bool $display_remove = false, $size = 300){
            $manager = new SpellManager();
            $mobs = $manager->getLinkMob($this);
            
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View();
                    $html = $view->dispatch(
                        template_name : "input/search",
                        data : [
                            "id" => "addMob" . $this->getUniqid(),
                            "title" => "Ajouter une invocation",
                            "label" => "Rechercher une créature",
                            "placeholder" => "Rechercher une créature",
                            "search_in" => ControllerModule::SEARCH_IN_MOB,
                            "parameter" => $this->getUniqid(),
                            "action" => ControllerModule::SEARCH_DONE_ADD_MOB_TO_SPELL,
                        ], 
                        write: false);

                    return $html . $this->getMob(Content::DISPLAY_RESUME, true);

                case Content::DISPLAY_RESUME:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($mobs)){
                        return $view->dispatch(
                            template_name : "mob/list",
                            data : [
                                "mobs" => $mobs,
                                "is_removable" => $display_remove,
                                "uniqid" => $this->getUniqid(),
                                "class_name" => "Spell",
                                "size" => $size
                            ], 
                            write: false);
                    }
                    return "";

                case Content::DISPLAY_LIST:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($mobs)){
                        ob_start();
                            ?> <ul class="list-unstyled"> <?php
                                foreach ($mobs as $mob) {?>
                                    <li>
                                        <?php $view->dispatch(
                                            template_name : "mob/text",
                                            data : [
                                                "obj" => $mob,
                                                "is_link" => true
                                            ], 
                                            write: true); ?>
                                    </li> <?php
                                }
                            ?> </ul> <?php
                        return ob_get_clean();
                    }
                    return "";

                case Content::FORMAT_ARRAY:
                    return $mobs;
            }
        }

        public function getFrequency(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $text = $tooltips = "Le sort peut être lancer " . $this->getCast_per_turn() . " tout les " . $this->getNumber_between_two_cast() . ' tour(s)';
            $badge_target = "";
            if($this->getCast_per_target() < $this->getCast_per_turn() && $this->getCast_per_target() != 0){
                $tooltips = "Le sort peut être lancer " . $this->getCast_per_target() . " sur la même cible et " . $this->getCast_per_turn() . " fois toutes cibles confondus, tout les " . $this->getNumber_between_two_cast() . ' tour(s)';
                $badge_target = $view->dispatch(
                    template_name : "badge",
                    data : [
                        "content" => $this->getCast_per_target() . " fois / cible",
                        "color" => "main-d-2",
                        "class" => "me-1",
                        "tooltip" => $tooltips,
                        "style" => Style::STYLE_OUTLINE
                    ], 
                    write: false);
            }

            if((int) $this->getNumber_between_two_cast() <= 1){
                $text = $this->getCast_per_turn() . " fois / tour";
            }else{
                $text = $this->getCast_per_turn() . " fois / {$this->getNumber_between_two_cast()} tour(s)";
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    ob_start(); ?>

                        <div class="d-flex justify-content-center m-1 align-items-center"> <?php
                            if(!empty($badge_target)){echo($badge_target);}

                            $view->dispatch(
                                template_name : "badge",
                                data : [
                                    "content" => $text,
                                    "color" => "main-d-2",
                                    "tooltip" => $tooltips,
                                    "style" => Style::STYLE_OUTLINE
                                ], 
                                write: true);
                        ?> </div> <?php

                    return ob_get_clean();
                   
                case Content::FORMAT_TEXT:
                    return $this->getFrequency();
                
                default:
                    return $text;
            }
        }

        private function formatEffectProp($prop){
            $pattern = '/^\[([a-zA-Z]+(?:\s*[a-zA-Z]+)*)\s*(?:[><]=?)\s*\d+\s*=\s*\d+(?::[a-zA-Z]+(?:\s*[a-zA-Z]+)*\s*(?:[><]=?)\s*\d+\s*=\s*\d+)*\]$/';
            if (preg_match($pattern, $prop)) { // Système de condition de caractéristiques supérieurs ou inférieurs
                $prop = preg_replace('/[\[\]]/', '', $prop);
                $elements = explode(':', $prop);
                ob_start();?>
                <div> <?php 
                    foreach ($elements as $element) {
                        $matches = [];
                        preg_match('/([a-zA-Z]+(?:\s*[a-zA-Z]+)*)\s*([><]=?)\s*(\d+)\s*=\s*(\d+)/', $element, $matches);
                        $word = trim($matches[1]);
                        $firstNumber = trim($matches[3]);
                        $sign = trim($matches[2]);
                        $secondNumber = trim($matches[4]); ?>

                        <p> <?=$word?>
                            <?php switch ($sign) {
                                case '>':
                                    echo "supérieur à";
                                    break;
                                case '>=':
                                    echo "supérieur ou égal à";
                                    break;
                                case '<':
                                    echo "inférieur à";
                                    break;
                                case '<=':
                                    echo "inférieur ou égal à";
                                    break;
                            }?> <?=$firstNumber?> = <?=$secondNumber?>
                        </p>
                                                
                    <?php }
                ?> </div>
                <?php return ob_get_clean();
            } else {
                $prop = preg_replace('/[\[\]]/', '', $prop);
                return trim($prop);
            }
        }
        private static function getLastOccurrence($type, $properties) {
            $lastOccurrence = 0;
            foreach ($properties as $propertyName => $propertyValue) {
                // Sépare le nom de propriété en type et numéro d'occurrence
                list($propType, $occurrence) = explode(self::OCCURENCE_TYPE_SEPARATOR, $propertyName);
                // Vérifie si le type correspond et si l'occurrence est un nombre
                if ($propType == $type && is_numeric($occurrence)) {
                    $lastOccurrence = max($lastOccurrence, (int)$occurrence);
                }
            }
            return $lastOccurrence;
        }
        private static function deleteOccurrencesOfType($type, &$properties) {
            foreach ($properties as $propertyName => $propertyValue) {
                // Sépare le nom de propriété en type et numéro d'occurrence
                list($propType, $occurrence) = explode(self::OCCURENCE_TYPE_SEPARATOR, $propertyName);
                // Vérifie si le type correspond
                if ($propType == $type) {
                    // Supprime la propriété
                    unset($properties[$propertyName]);
                }
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName(string | int | null $data){
            $this->_name = $data;
            return true;
        }
        public function setDescription(string | null $data){
            $this->_description = $data;
            return true;
        }
        public function setEffect(string | null $data){
            $this->_effect = $data;
            return true;
        }
        public function setEffect_array($data){
            if(empty($data)){
                $this->_effect_array = null;
                return true;
            }
            if(isJson($data)){
                $this->_effect_array = $data;
            } elseif(is_array($data)) {
                $this->_effect_array = json_encode($data);
            } else {
                throw new Exception("La valeur doit être un tableau ou un json");
            }
            return true;
        }
        public function setLevel(int | null $data){
            if(is_numeric($data)){
                $this->_level = $data;
                return true;
            } else {
                throw new Exception("La valeur doit être un nombre");
            }
        }
        public function setPo(string | int | null $data){
            $this->_po = $data;
            return true;
        }
        public function setPo_editable(bool | null $data){
            $this->_po_editable = $this->returnBool($data);
            return true;
        }
        public function setArea(int | null $data){
            if(isset(self::AREA[$data]) || $data == null){
                $this->_area = $data;
                return true;
            } else {
                throw new Exception("La valeur doit être comprise dans la liste des zones");
            }
        }
        public function setPa(string | int | null $data){
            $this->_pa = $data;
            return true;
        }
        public function setCast_per_turn(string | int | null $data){
            $this->_cast_per_turn = $data;
            return true;
        }
        public function setCast_per_target(string | int | null $data){
            if($data > $this->getCast_per_turn() || $data == 0){
                $this->_cast_per_target = $this->getCast_per_turn();
            } else {
                $this->_cast_per_target = $data;
            }
            return true;
        }
        public function setSight_line(bool | null $data){
            $this->_sight_line = $this->returnBool($data);
            return true;
        }
        public function setNumber_between_two_cast(string | int | null $data){
            $this->_number_between_two_cast = $data;
            return true;
        }
        public function setElement(string | null $data){
            if(isset(self::ELEMENT[$this->_element])){
                $this->_element = $data;
                return true;
            } else {
                throw new Exception("Valeur incorrect");
            }
        }
        public function setCategory(int | null $data){
            if(in_array($data, self::CATEGORY)){
                $this->_category = $data;
                return true;
            } else {
                throw new Exception("Valeur incorrect");
            }
        }
        public function setIs_magic(bool | null $data){
            $this->_is_magic = $this->returnBool($data);
            return true;
        }
        public function setPowerful(int | null $data){
            if(in_array($data, [1,2,3,4,5,6,7,8,9])){
                $this->_powerful = $data;
                return true;
            } else {
                throw new Exception("Valeur incorrect");
            }
        }

        /* Data = array(
                        action => add ou remove,
                        type => numéro du type du sort                        
                    )
            Js : Item.update(Uniqid,{action:'add|remove', type:'type'},'type', IS_VALUE);
        */
        public function setType(array $data){ 
            if(is_array($data)){
                $manager = new SpellManager;
                if(!isset($data['type'])){throw new Exception("Le type n'est pas défini");}
                if(in_array($data['type'], Spell::TYPE)){
    
                    if(isset($data['action'])){
                        switch ($data['action']) {
                            case 'add':
                                if($manager->addLinkType($this, $data['type'])){
                                    return true;
                                }else{
                                    throw new Exception("Erreur lors de l'ajout du type");
                                }
                   
                            case "remove":
                                if($manager->removeLinkType($this, $data['type'])){
                                    return true;
                                }else{
                                    throw new Exception("Erreur lors de la suppression du type");
                                }
    
                            default:
                                throw new Exception("L'action n'est pas valide");
                        }
    
                    } else {
                        throw new Exception("Une action est requise.");
                    }
                }
            }
        }

        /* Data = array(uniqid => id du mob)
            Js : Spell.update(UniqidM,{action:'add|remove', uniqid:'uniqIdM'},'mob', IS_VALUE);
        */
        public function setInvocation(array $data){return $this->setMob($data);}
        public function setMob(array $data){ 
            $manager = new SpellManager;
            $managerM = new MobManager;
            if(!isset($data['uniqid'])){throw new Exception("L'uniqid de la créature n'est pas défini");}
            if($managerM->existsUniqid($data['uniqid'])){
                $mob = $managerM->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            if($manager->addLinkMob($this, $mob)){
                                return true;
                            }else{
                                throw new Exception("Erreur lors de l'ajout de la créature");
                            }
               
                        case "remove":
                            if($manager->removeLinkMob($this, $mob)){
                                return true;
                            }else{
                                throw new Exception("Erreur lors de la suppression de la créature");
                            }

                        default:
                            throw new Exception("L'action n'est pas valide");
                    }

                } else {
                    throw new Exception("Une action est requise.");
                }

            }
        }
}