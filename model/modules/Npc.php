<?php

use Dompdf\Css\Color;

class Npc extends Creature
{
    protected const VERBAL_NAME_OF_CLASSE = "du ou de la PNJ";

    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/npc/default.svg",
            "dir" => "medias/modules/npc/",
            "naming" => "[uniqid]"
        ]
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_classe='';
        private $_story='';
        private $_historical='';
        private $_alignment='';
        
        private $_age='25';
        private $_size='1m70';
        private $_weight='70 kg';
        
        protected $_usable = true; // surcharge de la variable de Content

        public function __construct(array $data){
            parent::__construct($data);

            $classe = $this->getClasse(Content::FORMAT_OBJECT);
            $data = Npc::FILES['logo'];
            if(isset($data['naming']) && isset($data['dir']) && isset($data['type']) && isset($data['default']) && !empty($data['naming']) && !empty($data['dir']) && !empty($data['type']) && !empty($data['default'])){
                $path = FileManager::formatPath($data['dir']);
                preg_match_all("/\[(\w+)\]/", $data['naming'], $fcs_name);
                foreach ($fcs_name[1] as $fc_name) {
                    $method = "get".ucfirst($fc_name);
                    if(method_exists($this,$method)){
                        $data['naming'] = str_replace("[".$fc_name."]", $this->$method(), $data['naming']);
                    }
                }
                $path .= $data['naming']; 
                $path .= ".".FileManager::findExtention($data['dir'].$data['naming'], $data["type"]);
                if(file_exists($path)){
                    $this->_files['logo'] = new File($path);
                } elseif(file_exists($classe->getFile("img", new Style(['format' => Content::FORMAT_BRUT])))) {
                    $this->_files['logo']= new File($classe->getFile('img', new Style(['format' => Content::FORMAT_BRUT])));
                } elseif(file_exists($data['default'])) {
                    $this->_files['logo'] = new File($data['default']);
                } else {
                    $this->_files['logo'] = new File(Content::PATH_FILE_GENERAL_DEFAULT);
                }
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥

        public function getClasse(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $manager = New ClasseManager;
            if(!empty($this->_classe)){
                if($manager->existsUniqid($this->_classe)){
                    $object = $manager->getFromUniqid($this->_classe);
                } else {
                    $object = new Classe([]);
                }
            } else {
                $object = new Classe([]);
            }

            switch ($format) {
                case Content::DISPLAY_RESUME:
                    $items = [];
                    foreach ($manager->getAll() as $classe) {
                        $items[] = [
                            "onclick" => "Npc.update('".$this->getUniqid()."', '".$classe->getUniqid()."', 'classe', ".Controller::IS_VALUE.");",
                            "display" => "<span class='badge back-main-d-2'>" .ucfirst($classe->getName())."</span>"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltip" => "Classe du ou de la PNJ",
                            "label" => $this->getClasse(Content::FORMAT_BADGE),
                            "size" => Style::SIZE_SM,
                            "data" => $items
                        ], 
                        write: false);

                case Content::FORMAT_OBJECT:
                    return $object;

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $object->getName(),
                            "color" => "main-d-2",
                            "tooltip" => "Classe du ou de la PNJ",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                default:
                    return $this->_classe;
            }
        }
        public function getStory(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_RESUME:
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "npc",
                            "id" => "story".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "story",
                            "label" => "Histoire",
                            "value" => $this->_story
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_story)){return "";}
                    return html_entity_decode($this->_story);
            }
        }
        public function getHistorical(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_RESUME:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "historical",
                            "label" => "Historique",
                            "maxlenght" => "2000",
                            "placeholder" => "",
                            "value" => $this->_historical
                        ], 
                        write: false);
                
                default:
                    return $this->_historical;
            }
        }
        public function getAlignment(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_RESUME:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "alignment",
                            "label" => "Alignement",
                            "placeholder" => "ALignement du ou de la PNJ",
                            "value" => $this->_alignment,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_alignment,
                            "color" => "grey-d-2",
                            "tooltip" => "Alignement du ou de la PNJ",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                default:
                    return $this->_alignment;
            }
        }
        public function getTrait(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $classe = $this->getClasse(Content::FORMAT_OBJECT);
            $trait_classe = $classe->getTrait(Content::FORMAT_ARRAY);
            $trait_npc = explode(",", $this->_trait);
            $traits = array_combine($trait_classe, $trait_npc);
            if(!is_array($traits) || count($traits) == 0){
                $traits = array();
            }
            switch ($format) {
                case Content::DISPLAY_RESUME:
                    ob_start();
                        $view->dispatch(
                            template_name : "input/textarea",
                            data : [
                                "class_name" => "Npc",
                                "uniqid" => $this->getUniqid(),
                                "input_name" => "trait",
                                "label" => "Traits",
                                "value" => $this->_trait,
                                "placeholder" => "Traits",
                                "style" => Style::INPUT_FLOATING,
                                "comment" => "Séparer les différents traits par des virgules."
                            ], 
                            write: true);

                        if(!empty($classe->getTrait())){ ?>
                            <p>Traits propre à la classe : <?=$classe->getTrait(Content::FORMAT_BADGE)?></p>
                        <?php }
                    return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-around"> <?php
                            foreach ($traits as $trait) { 
                                $view->dispatch(
                                    template_name : "badge",
                                    data : [
                                        "color" => Style::getColorFromLetter($trait) . "-d-1",
                                        "content" => $trait,
                                        "style" => Style::STYLE_BACK,
                                        "tooltip" => "Trait ".$trait,
                                        "tooltip_placement" => "top"
                                    ], 
                                    write: true);
                                ?>
                                <?php } ?>                            
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_VIEW:
                    return $classe->getTrait(Content::FORMAT_BADGE) . $this->getTrait(Content::FORMAT_BADGE);

                default:
                    return $this->_trait;
            }

        }

        public function getAge(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_RESUME:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "age",
                            "label" => "Age",
                            "placeholder" => "Age du ou de la PNJ",
                            "maxlength" => "50",
                            "value" => $this->_age,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Age : {$this->_age} ans",
                            "color" => "grey-d-1",
                            "tooltip" => "Age du ou de la PNJ",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                default:
                    return $this->_age;
            }
        }
        public function getSize(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_RESUME:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "size",
                            "label" => "Taille",
                            "placeholder" => "Taille du ou de la PNJ",
                            "maxlength" => "50",
                            "value" => $this->_size,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Taille : {$this->_size}",
                            "color" => "grey-d-1",
                            "tooltip" => "Taille du ou de la PNJ",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                default:
                    return $this->_size;
            }
        }
        public function getWeight(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_RESUME:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "weight",
                            "label" => "Poids",
                            "placeholder" => "Poids du ou de la PNJ",
                            "value" => $this->_weight,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Poids : {$this->_age} kg",
                            "color" => "grey-d-1",
                            "tooltip" => "Poids du ou de la PNJ",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                default:
                    return $this->_weight;
            }
        }

        // Surchage de la fonction de Creature
        public function getSpell(int $format = Content::FORMAT_BRUT, $display_remove = false, $size = 300){
            $classe = $this->getClasse(Content::FORMAT_OBJECT);
            $manager = new NpcManager();
            $spells = $manager->getLinkSpell($this);
            if(is_array($spells) && !empty($spells)){
                $spell_classe = array();
                foreach ($classe->getSpell(Content::FORMAT_ARRAY)as $spell) {
                    $spell_classe[] = $spell['spell1'];
                }
                $spells = array_merge($spells, $spell_classe);
            } else {
                $spells = array();
                foreach ($classe->getSpell(Content::FORMAT_ARRAY)as $spell) {
                    $spells[] = $spell['spell1'];
                }
            }
            usort($spells, function($a, $b) {
                return $a->getLevel() <=> $b->getLevel();
            });
            
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    $view = new View();
                    $html = $view->dispatch(
                        template_name : "input/search",
                        data : [
                            "id" => "addSpell" . $this->getUniqid(),
                            "title" => "Ajouter un sort",
                            "label" => "Rechercher un sort",
                            "placeholder" => "Rechercher un sort",
                            "search_in" => ControllerModule::SEARCH_IN_SPELL,
                            "parameter" => $this->getUniqid(),
                            "action" => ControllerModule::SEARCH_DONE_ADD_SPELL_TO_NPC,
                        ], 
                        write: false);
                    return $html . $this->getSpell(Content::DISPLAY_RESUME, true);

                case Content::DISPLAY_RESUME:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($spells)){
                        return $view->dispatch(
                            template_name : "spell/list",
                            data : [
                                "spells" => $spells,
                                "is_removable" => $display_remove,
                                "uniqid" => $this->getUniqid(),
                                "class_name" => "Npc",
                                "size" => $size
                            ], 
                            write: false);
                    }
                    return "";

                case Content::DISPLAY_LIST:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($spells)){
                        ob_start();
                         $view->dispatch(
                            template_name : "spell/text",
                                data : [
                                    "spells" => $spells,
                                    "is_link" => true
                                ], 
                                write: true);
                        return ob_get_clean();
                    }
                    return "";

                case Content::FORMAT_ARRAY:
                    return $spells;
            }
        }
        public function getCapability(int $format = Content::FORMAT_BRUT, $display_remove = false, $size = 300){
            $classe = $this->getClasse(Content::FORMAT_OBJECT);
            $manager = new NpcManager();
            $capabilities = $manager->getLinkCapability($this);
            if(is_array($capabilities) && !empty($capabilities)){
                $capabilities_classe = $classe->getCapability(Content::FORMAT_ARRAY);
                if(is_array($capabilities_classe) && !empty($capabilities_classe)){
                    $capabilities = array_merge($capabilities, $capabilities_classe);
                }
            } else {
                $capabilities = $classe->getCapability(Content::FORMAT_ARRAY);
            }
            if(is_array($capabilities) && !empty($capabilities)){
                usort($capabilities, function($a, $b) {
                    return $a->getLevel() <=> $b->getLevel();
                });
            }
            
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    $view = new View();
                    $html = $view->dispatch(
                        template_name : "input/search",
                        data : [
                            "id" => "addCapability" . $this->getUniqid(),
                            "title" => "Ajouter une aptitude",
                            "label" => "Rechercher une aptitude",
                            "placeholder" => "Rechercher une aptitude",
                            "search_in" => ControllerModule::SEARCH_IN_CAPABILITY,
                            "parameter" => $this->getUniqid(),
                            "action" => ControllerModule::SEARCH_DONE_ADD_CAPABILITY_TO_NPC,
                        ], 
                        write: false);
                    return $html . $this->getCapability(Content::DISPLAY_RESUME, true);

                case Content::DISPLAY_RESUME:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($capabilities)){
                        return $view->dispatch(
                            template_name : "capability/list",
                            data : [
                                "capabilities" => $capabilities,
                                "is_removable" => $display_remove,
                                "uniqid" => $this->getUniqid(),
                                "class_name" => "Npc",
                                "size" => $size
                            ], 
                            write: false);
                    }
                    return "";

                case Content::DISPLAY_LIST:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($capabilities)){
                        ob_start();
                            ?> <ul class="list-unstyled"> <?php
                                foreach ($capabilities as $capability) {?>
                                    <li>
                                        <?php $view->dispatch(
                                            template_name : "capability/text",
                                            data : [
                                                "obj" => $capability,
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
                    return $capabilities;
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥

        public function setClasse(string | int $data){
            $this->_classe = $data;
            return true;
        }
        public function setStory(string | int | null  $data){
            $this->_story = $data;
            return true;
        }
        public function setHistorical(string | int | null  $data){
            $this->_historical = $data;
            return true;
        }
        public function setAlignment(string | int | null  $data){
            $this->_alignment = $data;
            return true;
        }
        public function setAge(string | int | null  $data){
            $this->_age = $data;
            return true;
        }
        public function setSize(string | int | null  $data){
            $this->_size = $data;
            return true;
        }
        public function setWeight(string | int | null  $data){
            $this->_weight = $data;
            return true;
        }
}