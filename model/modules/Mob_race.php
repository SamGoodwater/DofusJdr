<?php
class Mob_race extends Module
{

    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/races/default.svg",
            "dir" => "medias/modules/races/",
            "preferential_format" => "png",
            "naming" => "[uniqid]"
        ]
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name = null;
        private $_super_race = null;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Mob_race",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom",
                            "placeholder" => "Nom de la race",
                            "value" => $this->_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
        public function getSuper_race(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $manager = New Mob_raceManager;
            $object = null;
            if(!empty($this->_super_race)){
                if($manager->existsUniqid($this->_super_race)){
                    $object = $manager->getFromUniqid($this->_super_race);
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    $races =  $manager->getAll();
                    $items = [];
                    if(empty($races)){ return ""; } 
                    foreach ($races as $race) {
                        $items[] = [
                            "onclick" => "Mob_race.update('".$this->getUniqid()."', '".$race->getUniqid()."', 'super_race', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-main-d-2'>" .ucfirst($race->getName())."</span>"
                        ];
                    }

                    $label = $this->getSuper_race(Content::FORMAT_BADGE);
                    if(empty($label)) {$label = "Aucune race parente";}
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Race parente",
                            "label" => $label,
                            "size" => Style::SIZE_SM,
                            "items" => $items,
                            "is_search" => true
                        ], 
                        write: false);

                case Content::FORMAT_OBJECT:
                    if(empty($object)){return null;}

                    return $object;

                case Content::FORMAT_BADGE:
                    if(empty($object)){return "";}

                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $object->getName(),
                            "color" => "main-d-2",
                            "tooltip" => "Race parente",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                default:
                    return $this->_super_race;
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName(string $data){
            $this->_name = $data;
            return true;
        }
        public function setSuper_race(string | int | null $data){
            $manager = new Mob_raceManager;
            if(is_object($data)){
                $data = $data->getUniqid();
            }
            if(empty($data)){
                $data = null;
            }
            if(!$manager->existsUniqid($data) && !empty($data)){
                return false;
            }
            $this->_super_race = $data;
            return true;
        }

}