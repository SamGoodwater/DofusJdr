<?php
class Mob extends Creature
{
    protected const VERBAL_NAME_OF_CLASSE = "de la créature";

    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/mobs/default.svg",
            "dir" => "medias/modules/mobs/",
            "preferential_format" => "svg",
            "naming" => "[uniqid]"
        ]
    ];

    const HOSTILITY = [
        "amicale" => 0,
        "currieux" => 1,
        "neutre" => 2,
        "perreux" => 3,
        "agressif" => 4,
        "hostile" => 5
    ];

    const SIZE = [
        "très petite" => 0,
        "petite" => 1,
        "moyenne" => 2,
        "grande" => 3,
        "très grande" => 4,
        "gigantesque" => 5
    ];


    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_race = null;
        private $_hostility="";
        private $_size=self::SIZE['moyenne'];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getRace(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $manager = New Mob_raceManager;
            $object = null;
            if(!empty($this->_race)){
                if($manager->existsUniqid($this->_race)){
                    $object = $manager->getFromUniqid($this->_race);
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    $races =  $manager->getAll();
                    $items = [];
                    if(empty($races)){ return ""; } 
                    foreach ($races as $race) {
                        $items[] = [
                            "onclick" => "Mob.update('".$this->getUniqid()."', '".$race->getUniqid()."', 'race', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-main-d-2'>" .ucfirst($race->getName())."</span>"
                        ];
                    }

                    $label = $this->getRace(Content::FORMAT_BADGE);
                    if(empty($label)) {$label = "Aucune race associée";}
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Race",
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
                            "tooltip" => "Race",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                default:
                    return $this->_race;
            }
        }
        public function getHostility(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Mob::HOSTILITY as $name => $value) {
                        $items[] = [
                            "onclick" => "Mob.update('".$this->getUniqid()."', ".$value.", 'hostility', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge-outline text-".Style::getColorFromLetter($value, true)."-d-4 border-".Style::getColorFromLetter($value, true)."-d-2'>" .ucfirst($name)."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Hostilité de la créature",
                            "label" => $this->getHostility(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "items" => $items,
                            "comment" => "<span class='mx-2'>Hostilité : " . implode(", ", array_keys(Mob::HOSTILITY))."</span>",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_hostility, Mob::HOSTILITY)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => array_search($this->_hostility, Mob::HOSTILITY),
                                "color" => Style::getColorFromLetter($this->_hostility, true)."-d-2",
                                "tooltip" => "Agressivité de la créature : " . implode(", ", array_keys(Mob::HOSTILITY)),
                                "style" => Style::STYLE_OUTLINE
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($this->_hostility, Mob::HOSTILITY)){
                        return array_search($this->_hostility, Mob::HOSTILITY);
                    } else  {
                        return "";
                    }

                default:
                    return $this->_hostility;
            }
        }
        public function getSize(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Mob::SIZE as $name => $value) {
                        $items[] = [
                            "onclick" => "Mob.update('".$this->getUniqid()."', ".$value.", 'size', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-".Style::getColorFromLetter($value, true)."-d-2'>" .ucfirst($name)."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Taille de la créature",
                            "label" => ucfirst($this->getSize(Content::FORMAT_BADGE)),
                            "size" => Style::SIZE_SM,
                            "items" => $items,
                            "comment" => "<span class='mx-2'>Taille : " . implode(", ", array_keys(Mob::SIZE))."</span>",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_size, Mob::SIZE)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_size, Mob::SIZE)),
                                "color" => Style::getColorFromLetter($this->_size, true)."-d-2",
                                "tooltip" => "Taille de la créature : " . array_search($this->_size, Mob::SIZE),
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }
                case Content::FORMAT_ICON:
                    if(in_array($this->_size, Mob::SIZE)){
                        $expr = '/(?<=\s|^)[a-z]/i'; preg_match_all($expr, array_search($this->_size, Mob::SIZE), $matches);
                        $short_name = strtoupper(implode('', $matches[0]));
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => $short_name,
                                "color" => Style::getColorFromLetter($this->_size, true)."-d-2",
                                "tooltip" => "Taille de la créature : " . array_search($this->_size, Mob::SIZE),
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                            
                    } else  {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($this->_size, Mob::SIZE)){
                        return array_search($this->_size, Mob::SIZE);
                    } else  {
                        return "";
                    }

                default:
                    return $this->_size;
            }
        }

        public function getPowerful(int $format = Content::FORMAT_BRUT){
            $powerful = 0;
            $powerful_spell = 0;
            $n = 0;
            if(!empty($this->getSpell())){
                foreach ($this->getSpell(Content::FORMAT_ARRAY) as $spell) {
                    $powerful_spell += $spell->getPowerful();
                    $n++;
                }
            }
            
            if($n > 0){
                $powerful = round($powerful_spell / $n);
            }

            if(is_numeric($this->getLevel())){
                $level = $this->getLevel();
            } else {
                $level_form = Content::getMinMaxFromFormule($this->getLevel());
                if(!is_numeric($level_form["min"]) || !is_numeric($level_form["max"])){
                    $level = ((int)$level_form["min"] + (int)$level_form["max"]) / 2;
                } else {
                    $level = 1;
                }
            }

            $factor = Controller::calcExp("0,157894737 * (level + 3)", ["level" => $level]);

            $pa_expected = Controller::calcExp(CREATURE::CARACTERISTICS["pa"]["balance"]['mob']["expression"], ["level" => $level]);
            if(is_numeric($this->getPa())){
                $pa = $this->getPa();
            } else {
                $pa_form = Content::getMinMaxFromFormule($this->getPa());
                if(!is_numeric($pa_form["min"]) || !is_numeric($pa_form["max"])){
                    $pa = ((int)$pa_form["min"] + (int)$pa_form["max"]) / 2;
                } else {
                    $pa = CREATURE::CARACTERISTICS["pa"]["balance"]['mob']["base"];
                }
            }
            if($pa > $pa_expected + $factor){
                $powerful += 1;
            }elseif($pa < $pa_expected - $factor){
                $powerful -= 1;
            }
            $pm_expected = Controller::calcExp(CREATURE::CARACTERISTICS["pm"]["balance"]['mob']["expression"], ["level" => $level]);
            if(is_numeric($this->getPm())){
                $pm = $this->getPm();
            } else {
                $pm_form = Content::getMinMaxFromFormule($this->getPm());
                if(!is_numeric($pm_form["min"]) || !is_numeric($pm_form["max"])){
                    $pm = ((int)$pm_form["min"] + (int)$pm_form["max"]) / 2;
                } else {
                    $pm = CREATURE::CARACTERISTICS["pm"]["balance"]['mob']["base"];
                }
            }
            if($pm > $pm_expected + $factor){
                $powerful += 1;
            }elseif($pm < $pm_expected - $factor){
                $powerful -= 1;
            }

            $factor = Controller::calcExp("2,631578947 * (level + 5)", ["level" => $level]);

            $life_expected = Controller::calcExp(CREATURE::CARACTERISTICS["life"]["balance"]['mob']["expression"], ["level" => $level]);
            if(is_numeric($this->getLife())){
                $life = $this->getLife();
            } else {
                $life_form = Content::getMinMaxFromFormule($this->getLife());
                if(!is_numeric($life_form["min"]) || !is_numeric($life_form["max"])){
                    $life = ((int)$life_form["min"] + (int)$life_form["max"]) / 2;
                } else {
                    $life = CREATURE::CARACTERISTICS["life"]["balance"]['mob']["base"];
                }
                
            }
            if($life > $life_expected + $factor){
                $powerful += 1;
            }elseif($life < $life_expected - $factor){
                $powerful -= 1;
            }

            if($powerful > 9){$powerful = 9;}
            if($powerful < 1){$powerful = 1;}
            
            $view = new View();
            switch ($format) {
                case Content::FORMAT_BADGE:
                    if(in_array($powerful, [1,2,3,4,5,6,7,8,9])){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "Puissance ".$powerful,
                                "color" => "deep-purple-d-3",
                                "tooltip" => "Puissance d'une créature sur 9 niveaux en fonction de ces sorts",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else {
                        return "";
                    }

                case Content::FORMAT_ICON:
                    if(in_array($powerful, [1,2,3,4,5,6,7,8,9])){
                        return $view->dispatch(
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_SOLID,
                                "icon" => "fist-raised",
                                "color" => "deep-purple-d-3",
                                "tooltip" => "Puissance d'une créature sur 9 niveaux en fonction de ces sorts",
                                "content" => $powerful,
                                "content_placement" => Style::POSITION_LEFT
                            ], 
                            write: false);

                    } else {
                        return "";
                    }

                case Content::FORMAT_TEXT:
                    if(in_array($powerful, [1,2,3,4,5,6,7,8,9])){
                        return $powerful;
                    } else {
                        return "";
                    }

                default:
                    return $powerful;
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setRace(string | int | null $data){
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
            $this->_race = $data;
            return true;
        }
        public function setHostility(int | null $data){
            if(in_array($data, Mob::HOSTILITY)){
                $this->_hostility = $data;
                return true;
            } else {
                $this->_hostility = Mob::HOSTILITY["neutre"];
                throw new Exception("Donnée non valide;");
            }
        }
        public function setSize(int | null $data){
            if(in_array($data, Mob::SIZE)){
                $this->_size = $data;
                return true;
            } else {
                $this->_size = Mob::SIZE["moyenne"];
                throw new Exception("Donnée non valide;");
            }
        }
}