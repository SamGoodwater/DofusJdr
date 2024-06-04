<?php

use SebastianBergmann\Type\VoidType;

class Capability extends Content
{
    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/capabilities/default.svg",
            "dir" => "medias/modules/capabilities/",
            "preferential_format" => "jpg",
            "naming" => "[uniqid]"
        ]
    ];

    const SPECIALIZATION = [
        1 => [
            "name" => "Érudit",
            "color" => "cyan"
        ],
        2 => [
            "name" => "Milicien·ne",
            "color" => "red"
        ],
        3 => [
            "name" => "Voleur·euse",
            "color" => "yellow"
        ],
        4 => [
            "name" => "Dévot",
            "color" => "blue"
        ],
        5 => [
            "name" => "Artiste",
            "color" => "purple"
        ],
        6 => [
            "name" => "Négociant·e",
            "color" => "orange"
        ],
        7 => [
            "name" => "Explorateur·rice",
            "color" => "brown"
        ],
        8 => [
            "name" => "Sylvain",
            "color" => "green"
        ],
        9 => [
            "name" => "Artisan·e",
            "color" => "lime"
        ]
    ];

    const ELEMENT_NEUTRE = 0;
    const ELEMENT_VITALITY = 1;
    const ELEMENT_SAGESSE = 2;
    const ELEMENT_FORCE = 3;
    const ELEMENT_INTEL = 4;
    const ELEMENT_AGI = 5;
    const ELEMENT_CHANCE = 6;

    const ELEMENT_FORCE_INTEL = 7;
    const ELEMENT_FORCE_AGI = 8;
    const ELEMENT_FORCE_CHANCE = 9;

    const ELEMENT_INTEL_AGI= 10;
    const ELEMENT_INTEL_CHANCE= 11;

    const ELEMENT_AGI_CHANCE= 12;

    const ELEMENT_FORCE_INTEL_AGI= 13;
    const ELEMENT_FORCE_INTEL_CHANCE= 14;
    const ELEMENT_FORCE_AGI_CHANCE= 15;
    const ELEMENT_INTEL_AGI_CHANCE= 16;

    const ELEMENT_FORCE_INTEL_AGI_CHANCE= 17;

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
        self::ELEMENT_FORCE => [
            "color" => "force",
            "name" => "Force"
        ],
        self::ELEMENT_INTEL => [
            "color" => "intel",
            "name" => "Intel"
        ],
        self::ELEMENT_AGI => [
            "color" => "agi",
            "name" => "Agi"
        ],
        self::ELEMENT_CHANCE => [
            "color" => "chance",
            "name" => "Chance"
        ],
        self::ELEMENT_FORCE_INTEL => [
            "color" => "terre-feu",
            "name" => "Force et Intel"
        ],
        self::ELEMENT_FORCE_AGI => [
            "color" => "terre-air",
            "name" => "Force et Agi"
        ],
        self::ELEMENT_FORCE_CHANCE => [
            "color" => "terre-eau",
            "name" => "Force et Chance"
        ],
        self::ELEMENT_INTEL_AGI => [
            "color" => "feu-air",
            "name" => "Intel et Agi"
        ],
        self::ELEMENT_INTEL_CHANCE => [
            "color" => "feu-eau",
            "name" => "Intel et Chance"
        ],
        self::ELEMENT_AGI_CHANCE => [
            "color" => "air-eau",
            "name" => "Agi et Chance"
        ],
        self::ELEMENT_FORCE_INTEL_AGI => [
            "color" => "terre-feu-air",
            "name" => "Force, Intel et Agi"
        ],
        self::ELEMENT_FORCE_INTEL_CHANCE => [
            "color" => "terre-feu-eau",
            "name" => "Force, Intel et Chance"
        ],
        self::ELEMENT_FORCE_AGI_CHANCE => [
            "color" => "terre-air-eau",
            "name" => "Force, Agi et Chance"
        ],
        self::ELEMENT_INTEL_AGI_CHANCE => [
            "color" => "feu-air-eau",
            "name" => "Intel, Agi et Chance"
        ],
        self::ELEMENT_FORCE_INTEL_AGI_CHANCE => [
            "color" => "terre-feu-air-eau",
            "name" => "Force, Intel, Agi et Chance"
        ]
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_description='';
        private $_effect="";
        private $_level=1;
        private $_pa=3;
        private $_po=1;
        private $_po_editable=true;
        private $_time_before_use_again=null;
        private $_casting_time = null;
        private $_duration=null;
        private $_element = Capability::ELEMENT_NEUTRE;
        private $_is_magic = true;
        private $_ritual_available = false;
        private $_powerful = 1;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Capability",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom",
                            "placeholder" => "Nom de l'aptitude",
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
                            "tooltip" => "Nom de l'aptitude",
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
                            "class_name" => "Capability",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "level",
                            "label" => "Niveau",
                            "placeholder" => "Niveau à partir duquel il est possible de maitriser l'aptitude",
                            "tooltip" => "Niveau à partir duquel il est possible de maitriser l'aptitude",
                            "value" => $this->_level,
                            "color" => Style::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    if(empty($this->_level) || $this->_level == 0){return '';}
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Niveau {$this->_level}",
                            "color" => Style::getColorFromLetter($this->_level, true) . "-d-3",
                            "tooltip" => "Niveau à partir duquel il est possible de maitriser l'aptitude",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);
                
                case Content::FORMAT_ICON:
                    if(empty($this->_level) || $this->_level == 0){return '';}
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_level,
                            "color" => "",
                            "tooltip" => "Niveau à partir duquel il est possible de maitriser l'aptitude",
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
                            "class_name" => "Capability",
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
                            "class_name" => "Capability",
                            "id" => "effect".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "effect",
                            "label" => "Effet de l'aptitude",
                            "value" => $this->_effect
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_effect)){return "";}
                    return html_entity_decode($this->_effect);
            }
        }
        public function getPa(int $format = Content::FORMAT_BRUT, bool $not_show_if_free = false){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Capability",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "pa",
                            "label" => "Points d'action",
                            "placeholder" => "Points d'action",
                            "tooltip" => "Coût en point d'action de l'aptitude",
                            "value" => $this->_pa,
                            "color" => "pa-d-2",
                            "style" => Style::INPUT_ICON,
                            "icon" => "pa.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "Laisser vide si gratuit"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    if((empty($this->_pa) || $this->_pa == 0) && $not_show_if_free){return "";}
                    $pa = $this->_pa;
                    if(empty($this->_pa) || $this->_pa == 0){$pa = "gratuit en ";}

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$pa} PA",
                            "color" => "pa-d-2",
                            "tooltip" => "Coût en point d'action de l'aptitude",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    if((empty($this->_pa) || $this->_pa == 0) && $not_show_if_free){return "";}
                    $pa = $this->_pa;
                    if(empty($this->_pa)){$pa = 0;}

                    ob_start();?>
                        <div class="move_icon_pa ms-2"><?php
                            $view->dispatch(
                                template_name : "icon",
                                data : [
                                    "style" => Style::ICON_MEDIA,
                                    "icon" => "pa.png",
                                    "size" => 35,
                                    "color" => "pa-d-2",
                                    "tooltip" => "Coût en point d'action de l'aptitude",
                                    "content" => $pa,
                                    "content_placement" => Style::POSITION_LEFT
                                ], 
                                write: true);
                            ?> </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_pa;
            }
        }
        public function getPo(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Capability",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "po",
                            "label" => "Portée",
                            "placeholder" => "Portée de l'aptitude",
                            "tooltip" => "Portée de l'aptitude (en case)",
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
                    if(empty($this->_po)){return "";}
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_po} PO",
                            "color" => "po-d-2",
                            "tooltip" => "Portée de l'aptitude (en case)",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    if(empty($this->_po)){return "";}
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "po.png",
                            "color" => "po-d-2",
                            "tooltip" => "Portée de l'aptitude (en case)",
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
                                    "tooltip" => "La portée de l'aptitude n'est pas modifiable",
                                    "tooltip_placement" => Style::DIRECTION_TOP,
                                    "class" => "me-3"
                                ], 
                                write: true);
                            
                            $view->dispatch(
                                template_name : "input/checkbox",
                                data : [
                                    "class_name" => "Capability",
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
                                    "tooltip" => "La portée de l'aptitude est modifiable",
                                    "tooltip_placement" => Style::DIRECTION_TOP,
                                    "css" => "me-1"
                                ], 
                                write: true);?>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if(empty($this->_po)){return "";}
                    if($this->_po_editable && !in_array($this->getPO(), Spell::EXPRESSION_CAC)){ // Aptitude à distance Avec portée modifiable
                        $content = "PO modifiable";
                        $tooltip = "La portée de l'aptitude est modifiable";
                        $color = "po_editable-d-2";
                    } elseif(in_array($this->getPO(), Spell::EXPRESSION_CAC)) { // Aptitude au CàC
                        $content = "CàC";
                        $tooltip = "L'aptitude est une aptitude de corps à corps - c'est à dire une aptitude avec un rayon d'action d'1m50 maximum.";
                        $color = "red-d-2";
                    }else{ // Aptitude à distane sans portée modifiable
                        $content = "PO non modifiable";
                        $tooltip = "La portée de l'aptitude n'est pas modifiable";
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
                    if(empty($this->_po)){return "";}
                    if($this->_po_editable && !in_array($this->getPO(), Spell::EXPRESSION_CAC)){ // Aptitude à distance Avec portée modifiable
                        $icon = "po_editable.png";
                        $tooltip = "La portée de l'aptitude est modifiable";
                    } elseif(in_array($this->getPO(), Spell::EXPRESSION_CAC)) { // Aptitude au CàC
                        $icon = "cac.png";
                        $tooltip = "L'aptitude est une aptitude de corps à corps - c'est à dire une aptitude avec un rayon d'action d'1m50 maximum.";
                    }else{ // Aptitude à distane sans portée modifiable
                        $icon = "po_no_editable.png";
                        $tooltip = "La portée de l'aptitude n'est pas modifiable";
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
                    if($this->_po_editable && !in_array($this->getPO(), Spell::EXPRESSION_CAC)){ // Aptitude à distance Avec portée modifiable
                        return "medias/icons/modules/po_editable.png";
                    } elseif(in_array($this->getPO(), Spell::EXPRESSION_CAC)) { // Aptitude au CàC
                        return "medias/icons/modules/cac.png";
                    } else { 
                        return "medias/icons/modules/po_no_editable.png";   
                    }
                    
                default:
                    return $this->_po_editable;
            }
        }
        public function getTime_before_use_again(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Capability",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "time_before_use_again",
                            "label" => "Durée avant réutilisation",
                            "placeholder" => "Durée avant réutilisation",
                            "tooltip" => "Durée avant réutilisation",
                            "value" => $this->_time_before_use_again,
                            "color" => "time_before_use_again-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "time_before_use_again.svg",
                            "style_icon" => Style::ICON_MEDIA,
                            "color_icon" => "time_before_use_again-d-4"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    if(empty($this->_time_before_use_again)){return "";}
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_time_before_use_again,
                            "color" => "time_before_use_again-d-2",
                            "tooltip" => "Durée avant réutilisation",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    if(empty($this->_time_before_use_again)){return "";}
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "time_before_use_again.svg",
                            "color" => "time_before_use_again-d-2",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Durée avant réutilisation",
                            "content" => $this->_time_before_use_again,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                default:
                    return $this->_time_before_use_again;
            }
        }
        public function getCasting_time(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Capability",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "casting_time",
                            "label" => "Durée d'incantation",
                            "placeholder" => "Durée d'incantation",
                            "tooltip" => "Durée d'incantation de l'aptitude",
                            "value" => $this->_casting_time,
                            "color" => "casting_time-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "casting_time.svg",
                            "style_icon" => Style::ICON_MEDIA,
                            "color_icon" => "casting_time-d-4"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    if(empty($this->_casting_time)){return "";}
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_casting_time,
                            "color" => "casting_time-d-2",
                            "tooltip" => "Durée d'incantation de l'aptitude",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    if(empty($this->_casting_time)){return "";}
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "casting_time.svg",
                            "color" => "casting_time-d-2",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Durée d'incantation de l'aptitude",
                            "content" => $this->_casting_time,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                default:
                    return $this->_casting_time;
            }
        }
        public function getDuration(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Capability",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "duration",
                            "label" => "Durée",
                            "placeholder" => "Durée",
                            "tooltip" => "Durée pendant laquelle les effets de l'aptitude sont actifs",
                            "value" => $this->_duration,
                            "color" => "duration-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "duration.svg",
                            "style_icon" => Style::ICON_MEDIA,
                            "color_icon" => "duration-d-4"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    if(empty($this->_duration)){return "";}
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_duration,
                            "color" => "duration-d-2",
                            "tooltip" => "Durée pendant laquelle les effets de l'aptitude sont actifs",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    if(empty($this->_duration)){return "";}
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "duration.svg",
                            "color" => "duration-d-2",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Durée pendant laquelle les effets de l'aptitude sont actifs",
                            "content" => $this->_duration,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                default:
                    return $this->_duration;
            }
        }
        public function getElement(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach(Capability::ELEMENT as $id_element => $element) { 
                        $items[] = [
                            "onclick" => "Capability.update('".$this->getUniqid()."', ".$id_element.", 'element', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-".$element['color']."-d-2'>" .ucfirst($element['name'])."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Element(s) de l'aptitude",
                            "label" => $this->getElement(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(isset(Capability::ELEMENT[$this->_element])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(Capability::ELEMENT[$this->_element]['name']),
                                "color" => Capability::ELEMENT[$this->_element]['color'] ."-d-2",
                                "tooltip" => "Element(s) de l'aptitude",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_COLOR_VERBALE:
                    if(isset(Capability::ELEMENT[$this->_element])){
                        return strtolower(Capability::ELEMENT[$this->_element]['color']);
                    } else {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(isset(Capability::ELEMENT[$this->_element])){
                        return strtolower(Capability::ELEMENT[$this->_element]['name']);
                    } else {
                        return "";
                    }

                default:
                    return $this->_element;
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
                                    "tooltip" => "L'aptitude est physique.",
                                    "tooltip_placement" => Style::DIRECTION_TOP,
                                    "class" => "me-1"
                                ], 
                                write: true);
                            
                            $view->dispatch(
                                template_name : "input/checkbox",
                                data : [
                                    "class_name" => "Capability",
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
                                    "tooltip" => "L'aptitude est magique.",
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
                        $tooltip = "L'aptitude est magique.";
                    } else {
                        $color = "brown-d-2";
                        $content = "Physique";
                        $tooltip = "L'aptitude est physique.";
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
                        $tooltip = "L'aptitude est magique.";
                    } else {
                        $color = "brown-d-2";
                        $icon = "fist-raised";
                        $tooltip = "L'aptitude est physique.";
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
        public function getRitual_available(int $format = Content::FORMAT_BRUT, bool $not_show_if_not_ritual = true){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-start align-items-center"><?php
                            $view->dispatch(
                                template_name : "badge",
                                data : [
                                    "content" => "Rituel Indisponible",
                                    "color" => "grey-d-2",
                                    "style" => Style::STYLE_OUTLINE,
                                    "tooltip" => "L'aptitude ne peut pas être lancer sous forme de rituel.",
                                    "tooltip_placement" => Style::DIRECTION_TOP,
                                    "class" => "me-1"
                                ], 
                                write: true);
                            
                            $view->dispatch(
                                template_name : "input/checkbox",
                                data : [
                                    "class_name" => "Capability",
                                    "uniqid" => $this->getUniqid(),
                                    "id" => "ritual_available_" . $this->getUniqid(),
                                    "input_name" => "ritual_available",
                                    "label" => "",
                                    "color" => "main",
                                    "checked" => $this->returnBool($this->_is_magic),
                                    "style" => Style::CHECK_SWITCH
                                ], 
                                write: true);

                            $view->dispatch(
                                template_name : "badge",
                                data : [
                                    "content" => "Rituel disponible",
                                    "color" => "cyan-d-2",
                                    "style" => Style::STYLE_OUTLINE,
                                    "tooltip" => "L'aptitude peut être lancer sous forme de rituel.",
                                    "tooltip_placement" => Style::DIRECTION_TOP,
                                    "css" => "me-1"
                                ], 
                                write: true);?>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if($not_show_if_not_ritual && !$this->_ritual_available){return "";}

                    if($this->_ritual_available){
                        $color = "cyan-d-2";
                        $content = "Rituel disponible";
                        $tooltip = "L'aptitude peut être lancer sous forme de rituel.";
                    } else {
                        $color = "grey-d-2";
                        $content = "Rituel indisponible";
                        $tooltip = "L'aptitude ne peut pas être lancer sous forme de rituel.";
                    }

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $content,
                            "color" => $color,
                            "style" => Style::STYLE_OUTLINE,
                            "tooltip" => $tooltip,
                            "tooltip_placement" => Style::DIRECTION_TOP
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    if($not_show_if_not_ritual && !$this->_ritual_available){return "";}

                    if($this->_ritual_available){
                        $color = "cyan-d-2";
                        $icon = "ritual.png";
                        $tooltip = "L'aptitude peut être lancer sous forme de rituel.";
                    } else {
                        $color = "grey-d-2";
                        $icon = "ritual_not.png";
                        $tooltip = "L'aptitude ne peut pas être lancer sous forme de rituel.";
                    }
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICONS_FILE,
                            "icon" => $icon,
                            "color" => $color,
                            "tooltip" => $tooltip
                        ], 
                        write: false); 
                    
                default:
                    return $this->_ritual_available;
            }
        }
        public function getPowerful(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    for ($i=1; $i <= 9 ; $i++) { 
                        $items[] = [
                            "onclick" => "Capability.update('".$this->getUniqid()."', ".$i.", 'powerful', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-deep-purple-d-3'>Puissance " .$i."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Puissance d'une aptitude sur 9 niveaux",
                            "label" => $this->getPowerful(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items,
                            "comment" => "Puissance d'une aptitude sur 9 niveaux"
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_powerful,  [1,2,3,4,5,6,7,8,9])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "Puissance ".$this->_powerful,
                                "color" => "deep-purple-d-3",
                                "tooltip" => "Puissance d'une aptitude sur 9 niveaux",
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

        public function getSpecialization(int $format = Content::FORMAT_BRUT, bool $is_remove = false){
            $view = new View();
            $manager = new CapabilityManager();
            $specializations = $manager->getLinkSpecialization($this);
            
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <h4>Spécialisations</h4>
                        <p><small>Choisissez les spécialisations pouvant utiliser cette aptitude.</small></p>
                        <div class="d-flex justify-content-start gap-1 align-baseline flex-wrap">
                            <?php foreach (self::SPECIALIZATION as $id => $specialization) { 
                                $checked = "";
                                $class="";
                                if($manager->existsLinkSpecialization($this, $id)) { $checked = "checked"; $class="bold text-white back-".$specialization['color']."-d-2"; } ?>
                                <div>
                                    <input <?=$checked?> onchange="checkboxButtonToggle(this, Capability, '<?=$this->getUniqid()?>', 'specialization', <?=$id?>, 'specialization');" data-color="<?=$specialization['color']?>" type="checkbox" class="btn-check" id="specialization-btn-check-<?=$id?>" autocomplete="off">
                                    <label class="btn btn-sm btn-outline <?=$class?> border-<?=$specialization['color']?>-d-2" for="specialization-btn-check-<?=$id?>"><?=ucfirst($specialization['name'])?></label>
                                </div>
                            <?php } ?>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); 
                        if(!empty($specializations)){?>
                            <h4>Disponible pour les Spécialisations suivantes :</h4>
                            <div class="d-flex flex-row justify-content-around flex-wrap gap-1">
                                <?php foreach ($specializations as $id => $specialization) {
                                    $view->dispatch(
                                        template_name : "badge",
                                        data : [
                                            "content" => self::SPECIALIZATION[$specialization]["name"],
                                            "color" => self::SPECIALIZATION[$specialization]["color"] . "-d-4",
                                            "tooltip" => "Aptitude utilisable par les " . self::SPECIALIZATION[$specialization]["name"],
                                            "style" => Style::STYLE_BACK
                                        ], 
                                        write: true);
                                } ?>
                            </div>
                        <?php }
                    return ob_get_clean();

                case Content::FORMAT_TEXT:
                    if(!empty($specializations)){
                        $array = [];
                        foreach ($specializations as $specialization) {
                            $array[$specialization] = [
                                "id" => $specialization,
                                "name" => self::SPECIALIZATION[$specialization]["name"],
                                "color" => self::SPECIALIZATION[$specialization]["color"]
                            ];
                        }
                        return $array;
                    } else {
                        return [];
                    }
                    
                case Content::FORMAT_ARRAY:
                    return $specializations;
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
        public function setLevel(int | null | string $data){
            if(is_numeric($data) || is_null($data) || $data == ""){
                $this->_level = $data;
                return true;
            } else {
                throw new Exception("La valeur doit être un nombre ou être null");
            }
        }
        public function setPa(string | int | null $data){
            $this->_pa = $data;
            return true;
        }
        public function setPo(string | int | null $data){
            $this->_po = $data;
            return true;
        }
        public function setPo_editable(bool | null $data){
            $this->_po_editable = $this->returnBool($data);
            return true;
        }
        public function setTime_before_use_again(string | int | null $data){
            $this->_time_before_use_again = $data;
            return true;
        }
        public function setCasting_time(string | int | null $data){
            $this->_casting_time = $data;
            return true;
        }
        public function setDuration(string | int | null $data){
            $this->_duration = $data;
            return true;
        }
        public function setElement(string | null $data){
            if(isset(Capability::ELEMENT[$this->_element])){
                $this->_element = $data;
                return true;
            } else {
                throw new Exception("Valeur incorrect");
            }
        }
        public function setIs_magic(bool | null $data){
            $this->_is_magic = $this->returnBool($data);
            return true;
        }
        public function setRitual_available(bool | null $data){
            $this->_ritual_available = $this->returnBool($data);
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
                        spesialization => numéro du spesialization de l'aptitude                        
                    )
            Js : Item.update(Uniqid,{action:'add|remove', spesialization:'spesialization'},'spesialization', IS_VALUE);
        */
        public function setSpecialization(array $data){ 
            if(is_array($data)){
                $manager = new CapabilityManager;
                if(!isset($data['specialization'])){throw new Exception("La spécialisation n'est pas défini");}
                if(isset(self::SPECIALIZATION[$data['specialization']])){
                    if(isset($data['action'])){
                        switch ($data['action']) {
                            case 'add':
  
                                if($manager->addLinkSpecialization($this, $data['specialization'])){
                                    return true;
                                }else{
                                    throw new Exception("Erreur lors de l'ajout de la spécialisation");
                                }
                   
                            case "remove":
                                if($manager->removeLinkSpecialization($this, $data['specialization'])){
                                    return true;
                                }else{
                                    throw new Exception("Erreur lors de la suppression de la spécialisation");
                                }
    
                            default:
                                throw new Exception("L'action n'est pas valide");
                        }
    
                    } else {
                        throw new Exception("Une action est requise.");
                    }
                } else {
                    throw new Exception("La spécialisation n'est pas valide");
                }
            } else {
                throw new Exception("La valeur doit être un tableau");
            }
        }
}