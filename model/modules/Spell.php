<?php

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

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_description='';
        private $_effect="";
        private $_level=1;
        private $_po=1;
        private $_po_editable=true;
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
                    return html_entity_decode($this->_effect);
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
                                    "size" => Style::SIZE_LG,
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
                            "icon" => "cast_per_target.png",
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
                    for ($i=1; $i <= 7 ; $i++) { 
                        $items[] = [
                            "onclick" => "Spell.update('".$this->getUniqid()."', ".$i.", 'powerful', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-deep-purple-d-3'>Puissance " .$i."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Puissance d'un sort sur 7 niveaux",
                            "label" => $this->getPowerful(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items,
                            "comment" => "Puissance d'un sort sur 7 niveaux"
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_powerful,  [1,2,3,4,5,6,7])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "Puissance ".$this->_powerful,
                                "color" => "deep-purple-d-3",
                                "tooltip" => "Puissance d'un sort sur 7 niveaux",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($this->_powerful, [1,2,3,4,5,6,7])){
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
                            <div class="d-flex flex-row justify-content-around flex-wrap">
                                <?php foreach ($types as $type) {
                                    $view->dispatch(
                                        template_name : "badge",
                                        data : [
                                            "content" => array_search($type, Spell::TYPE),
                                            "color" => Style::getColorFromLetter($type) . "-d-4",
                                            "tooltip" => "Puissance d'un sort sur 7 niveaux",
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
            if(in_array($data, [1,2,3,4,5,6,7])){
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