<?php
class Consumable extends Content
{

    const TYPE_FOOD = 3;
    const TYPE_POTION = 1;
    const TYPE_TREAT = 2;
    const TYPE_PARCHMENT = 4;
    const TYPE_STONE= 5;

    const TYPES = [
        "nourriture" => Consumable::TYPE_FOOD,
        "potion" => Consumable::TYPE_POTION,
        "confiserie" => Consumable::TYPE_TREAT,
        "parchemin" => Consumable::TYPE_PARCHMENT,
        "pierre" => Consumable::TYPE_STONE
    ];

    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/consumables/default.svg",
            "dir" => "medias/modules/consumables/",
            "preferential_format" => "svg",
            "naming" => "[uniqid]"
        ]
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_type=Consumable::TYPE_FOOD;
        private $_name='';
        private $_description='';
        private $_effect="";
        private $_level=1;
        private $_recepe="";
        private $_price=0;
        private $_rarity=5;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getType(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Consumable::TYPES as $name => $type) {
                        $items[] = [
                            "display" => "<span class='badge back-".Style::getColorFromLetter($type)."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => "Consumable.update('".$this->getUniqid()."', '".$type."', 'type',".Controller::IS_VALUE.")"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getType(Content::FORMAT_BADGE),
                            "tooltip" => "Type de consommable",
                            "items" => $items,
                            "id" => "type_{$this->getUniqid()}",
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    if(in_array($this->_type, Consumable::TYPES)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_type, Consumable::TYPES)),
                                "color" => Style::getColorFromLetter($this->_type) . "-d-2",
                                "tooltip" => "Type de consommable",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_type;
            }
        }
        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Consumable",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom",
                            "placeholder" => "Nom du consommable",
                            "value" => $this->_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Consumable",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "description",
                            "label" => "Description",
                            "maxlenght" => "2000",
                            "placeholder" => "",
                            "value" => $this->_description
                        ], 
                        write: false);

                default:
                    return $this->_description;
            }
        }
        public function getEffect(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Consumable",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "effect",
                            "label" => "Effets",
                            "maxlenght" => "1000",
                            "tooltip" => "Effets du consommable",
                            "placeholder" => "",
                            "value" => $this->_effect
                        ], 
                        write: false);

                default:
                    return $this->_effect;
            }
        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Consumable",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "level",
                            "label" => "Niveau",
                            "placeholder" => "Niveau",
                            "tooltip" => "Niveau à partir duquel il est possible de fabriquer le consommable",
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
                            "tooltip" => "Niveau à partir duquel il est possible de fabriquer le consommable",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_level,
                            "color" => "",
                            "tooltip" => "Niveau à partir duquel il est possible de fabriquer le consommable",
                            "style" => Style::STYLE_NONE,
                            "class" => "text-".Style::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);
                
                default:
                    return $this->_level;
            }
        }
        public function getRecepe(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Consumable",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "recepe",
                            "label" => "Recette",
                            "maxlenght" => "1000",
                            "placeholder" => "",
                            "tooltip" => "Recette de fabrication du consommable",
                            "value" => $this->_recepe
                        ], 
                        write: false);

                default:
                    return $this->_recepe;
            }
        }
        public function getPrice(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Consumable",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "price",
                            "label" => "Prix estimé",
                            "placeholder" => "Prix estimé",
                            "tooltip" => "Prix estimé",
                            "value" => $this->_price,
                            "color" => "kamas-d-3",
                            "style" => Style::INPUT_ICON,
                            'icon' => "kamas.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_price} kamas",
                            "color" => "kamas-d-4",
                            "tooltip" => "Prix estimé du consommable",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "kamas.png",
                            "color" => "kamas-d-3",
                            "tooltip" => "Prix estimé du consommable",
                            "content" => $this->_price,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false); 
                
                default:
                    return $this->_price;
            }
        }
        public function getRarity(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Item::RARITY_LIST as $name => $rarity) {
                        $items[] = [
                            "display" => "<span class='badge back-".Style::getColorFromLetter($rarity, true)."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => "Consumable.update('{$this->getUniqid()}', {$rarity}, 'rarity', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getRarity(Content::FORMAT_BADGE),
                            "tooltip" => "Rareté du consommable",
                            "items" => $items,
                            "id" => "rarity_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_rarity, Item::RARITY_LIST)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_rarity, Item::RARITY_LIST)),
                                "color" => Style::getColorFromLetter($this->_rarity, true) . "-d-2",
                                "tooltip" => "Rareté du consommable",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_rarity;
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setType($data){
            if(in_array($data, Consumable::TYPES)){
                $this->_type = $data;
                return true;
            } else {
                throw new Exception("Type est incorrect");
            }
        }
        public function setName($data){
            $this->_name = $data;
            return true;
        }
        public function setDescription($data){
            $this->_description = $data;
            return true;
        }
        public function setEffect($data){
            $this->_effect = $data;
            return true;
        }
        public function setLevel(int $data){
            if(is_numeric($data)){
                $this->_level = $data;
                return true;
            } else {
                throw new Exception("Le niveau doit être un nombre");
            }
        }
        public function setRecepe($data){
            $this->_recepe = $data;
            return true;
        }
        public function setPrice($data){
            $this->_price = $data;
            return true;
        }
        public function setRarity($data){
            if(in_array($data, Item::RARITY_LIST)){
                $this->_rarity = $data;
                return true;
            } else {
                $this->_rarity = Item::RARITY_LIST["Très répandu"];
                throw new Exception("Rareté incorrecte");
            }
        }
}
