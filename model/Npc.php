<?php
class Npc extends Content
{

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='PNJ';
        private $_classe='';
        private $_story='';
        private $_historical='';
        private $_alignment='';
        private $_level=1;
        private $_trait='';
        private $_other_info='';
        
        private $_age='25';
        private $_size='1m70';
        private $_weight='70 kg';

        private $_life=30;
        private $_pa=6;
        private $_pm=3;
        private $_po=0;
        private $_ini=0;
        private $_invocation=0;
        private $_touch=0;
        private $_ca=0;
        private $_dodge_pa=0;
        private $_dodge_pm=0;
        private $_fuite=0;
        private $_tacle=0;
        private $_vitality=0;
        private $_sagesse=0;
        private $_strong=0;
        private $_intel=0;
        private $_agi=0;
        private $_chance=0;
        private $_res_neutre=0;
        private $_res_terre=0;
        private $_res_feu=0;
        private $_res_air=0;
        private $_res_eau=0;
        private $_acrobatie=0;
        private $_discretion=0;
        private $_escamotage=0;
        private $_athletisme=0;
        private $_intimidation=0;
        private $_arcane=0;
        private $_histoire=0;
        private $_investigation=0;
        private $_nature=0;
        private $_religion=0;
        private $_dressage=0;
        private $_medecine=0;
        private $_perception=0;
        private $_perspicacite=0;
        private $_survie=0;
        private $_persuasion=0;
        private $_representation=0;
        private $_supercherie=0;

        private $_kamas='';
        private $_drop_='';

        private $_other_equipment='';
        private $_other_consomable='';
        private $_other_spell='';
        
        protected $_usable = true; // surcharge de la variable de Content

        // équipement
        // consomable
        // Spell

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥

        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom",
                            "placeholder" => "Nom du ou de la PNJ",
                            "value" => $this->_name,
                            "style" => View::STYLE_INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
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
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach ($manager->getAll() as $classe) {
                        $items[] = [
                            "onclick" => "Npc.update('".$this->getUniqid()."', '".$classe->getUniqid()."', 'classe', ".Controller::IS_VALUE.");",
                            "display" => $classe,
                            "class" => "badge back-main-d-2",
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "tooltips" => "Classe du ou de la PNJ",
                            "label" => $this->getClasse(Content::FORMAT_BADGE),
                            "size" => View::SIZE_SM,
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
                            "style" => View::STYLE_BACK
                        ], 
                        write: false);

                default:
                    return $this->_classe;
            }
        }
        public function getStory(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
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
                    return html_entity_decode($this->_story);
            }
        }
        public function getHistorical(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
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
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "alignement",
                            "label" => "Alignement",
                            "placeholder" => "ALignement du ou de la PNJ",
                            "value" => $this->_name,
                            "style" => View::STYLE_INPUT_FLOATING
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_alignment,
                            "color" => "grey-d-2",
                            "tooltip" => "Alignement du ou de la PNJ",
                            "style" => View::STYLE_BACK
                        ], 
                        write: false);

                default:
                    return $this->_alignment;
            }
        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "level",
                            "label" => "Niveau",
                            "placeholder" => "Niveau",
                            "tooltip" => "Niveau du ou de la PNJ",
                            "value" => $this->_level,
                            "color" => View::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Niveau {$this->_level}",
                            "color" => View::getColorFromLetter($this->_level) . "-d-3",
                            "tooltip" => "Niveau du ou de la PNJ",
                            "style" => View::STYLE_OUTLINE
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_level,
                            "color" => "",
                            "tooltip" => "Niveau du ou de la PNJ",
                            "style" => View::STYLE_NONE,
                            "class" => "text-".View::getColorFromLetter($this->_level) . "-d-3"
                        ], 
                        write: false);
                
                default:
                    return $this->_level;
            }
        }
        public function getTrait(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $trait_classe = $this->getClasse(Content::FORMAT_OBJECT)->getTrait(Content::FORMAT_ARRAY);
            $trait_npc = explode(",", $this->_trait);
            $traits = array_combine($trait_classe, $trait_npc);
            switch ($format) {
                case Content::FORMAT_EDITABLE:
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
                                "style" => View::STYLE_INPUT_FLOATING,
                                "comment" => "Séparer les différents traits par des virgules."
                            ], 
                            write: true);

                        if(!empty($this->getClasse(Content::FORMAT_OBJECT)->getTrait())){ ?>
                            <p>Traits propre à la classe : <?=$this->getClasse(Content::FORMAT_OBJECT)->getTrait(Content::FORMAT_BADGE)?></p>
                        <?php }
                    return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-around"> <?php
                            foreach ($traits as $trait) { 
                                $view->dispatch(
                                    template_name : "badge",
                                    data : [
                                        "color" => View::getColorFromLetter($trait) . "-d-1",
                                        "content" => $trait,
                                        "style" => View::STYLE_BACK,
                                        "tooltip" => "Trait ".$trait,
                                        "tooltip_placement" => "top"
                                    ], 
                                    write: true);
                                ?>
                                <?php } ?>                            
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_VIEW:
                    return $this->getClasse(Content::FORMAT_OBJECT)->getTrait(Content::FORMAT_BADGE) . $this->getTrait(Content::FORMAT_BADGE);

                default:
                    return $this->_trait;
            }

        }
        public function getOther_info(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "npc",
                            "id" => "other_info".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "other_info",
                            "label" => "Caractères et autres informations",
                            "value" => $this->_other_info
                        ], 
                        write: false);
                
                default:
                    return html_entity_decode($this->_other_info);
            }
        }
        public function getAge(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
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
                            "style" => View::STYLE_INPUT_FLOATING
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Age : {$this->_age} ans",
                            "color" => "grey-d-1",
                            "tooltip" => "Age du ou de la PNJ",
                            "style" => View::STYLE_BACK
                        ], 
                        write: false);
                   
                default:
                    return $this->_age;
            }
        }
        public function getSize(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
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
                            "style" => View::STYLE_INPUT_FLOATING
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Taille : {$this->_size}",
                            "color" => "grey-d-1",
                            "tooltip" => "Taille du ou de la PNJ",
                            "style" => View::STYLE_BACK
                        ], 
                        write: false);
                   
                default:
                    return $this->_size;
            }
        }
        public function getWeight(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "weight",
                            "label" => "Poids",
                            "placeholder" => "Poids du ou de la PNJ",
                            "value" => $this->_weight,
                            "style" => View::STYLE_INPUT_FLOATING
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Poids : {$this->_age} kg",
                            "color" => "grey-d-1",
                            "tooltip" => "Poids du ou de la PNJ",
                            "style" => View::STYLE_BACK
                        ], 
                        write: false);
                   
                default:
                    return $this->_weight;
            }
        }

        public function getLife(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "life",
                            "label" => "Points de vie",
                            "placeholder" => "Points de vie du ou de la PNJ",
                            "tooltips" => "Calcul des points de vie",
                            "value" => $this->_life,
                            "color" => "life-d-2",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "life.svg",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_life} Points de vie",
                            "color" => "life-d-2",
                            "tooltip" => "Calcul des points de vie",
                            "style" => View::STYLE_BACK,
                            "id" => "life"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "life.svg",
                            "color" => "life-d-2",
                            "tooltip" => "Calcul des points de vie",
                            "content" => $this->_life,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_life} Points de vie",
                            "color" => "life-d-2",
                            "tooltip" => "Calcul des points de vie",
                            "comment" => "Dès de classe + mod. Vitalité * niveau + bonus d'équipement"
                        ], 
                        write: false);
                   
                default:
                    return $this->_life;
            }
        }
        public function getPa(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "pa",
                            "label" => "PA",
                            "placeholder" => "Points d'action du ou de la PNJ",
                            "tooltips" => "PA",
                            "value" => $this->_pa,
                            "color" => "pa",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "pa.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_pa} PA",
                            "color" => "pa",
                            "tooltip" => "PA",
                            "style" => View::STYLE_BACK,
                            "id" => "pa"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "pa.png",
                            "color" => "pa",
                            "tooltip" => "PA",
                            "content" => $this->_pm,
                            "content_placement" => "before"
                        ], 
                        write: false); 
                
                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_pa} PA",
                            "color" => "pa",
                            "tooltip" => "Points d'action",
                            "comment" => "Bonus d'équipement"
                        ], 
                        write: false);

                default:
                    return $this->_pa;
            }
        }
        public function getPm(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "pm",
                            "label" => "PM",
                            "placeholder" => "Points de mouvement du ou de la PNJ",
                            "tooltips" => "PM",
                            "value" => $this->_pm,
                            "color" => "pm",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "pm.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_pm} PM",
                            "color" => "pm",
                            "tooltip" => "PM",
                            "style" => View::STYLE_BACK,
                            "id" => "pm"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "pm.png",
                            "color" => "pm",
                            "tooltip" => "PM",
                            "content" => $this->_pm,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_pm} PM",
                            "color" => "pm",
                            "tooltip" => "Points de mouvement",
                            "comment" => "Bonus d'équipement"
                        ], 
                        write: false);

                default:
                    return $this->_pm;
            }
        }
        public function getPo(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "po",
                            "label" => "PO",
                            "placeholder" => "Portée du ou de la PNJ",
                            "tooltips" => "PO",
                            "value" => $this->_po,
                            "color" => "po",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "po.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_po} PO",
                            "color" => "po",
                            "tooltip" => "PO",
                            "style" => View::STYLE_BACK,
                            "id" => "po"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "po.png",
                            "color" => "po",
                            "tooltip" => "PO",
                            "content" => $this->_po,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_po} PO",
                            "color" => "po",
                            "tooltip" => "Portée",
                            "comment" => "Bonus d'équipement"
                        ], 
                        write: false);

                default:
                    return $this->_po;
            }
        }
        public function getIni(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_ini + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "ini",
                            "label" => "Initiative",
                            "placeholder" => "Initiative du ou de la PNJ",
                            "tooltips" => "Initiative",
                            "value" => $this->_ini,
                            "color" => "ini",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "ini.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_ini} Initiative",
                            "color" => "ini",
                            "tooltip" => "Bonus d'Initiative",
                            "style" => View::STYLE_BACK,
                            "id" => "ini"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "ini.png",
                            "color" => "ini",
                            "tooltip" => "Bonus d'Initiative",
                            "content" => $this->_ini,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Initiative",
                            "color" => "ini",
                            "tooltip" => "Initiative",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->getIni(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_ini;
            }
        }
        public function getInvocation(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_invocation + 1;
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "invocation",
                            "label" => "Nb d'invocation",
                            "placeholder" => "Portée du ou de la PNJ",
                            "tooltips" => "Nb d'invocation",
                            "value" => $this->_invocation,
                            "color" => "invocation",
                            "comment" => "1 + Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "invocation.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_invocation} Invocations",
                            "color" => "invocation",
                            "tooltip" => "Bonus d'Invocations",
                            "style" => View::STYLE_BACK,
                            "id" => "invocation"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "invocation.png",
                            "color" => "invocation",
                            "tooltip" => "Bonus d'Invocations",
                            "content" => $this->_invocation,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Invocations",
                            "color" => "invocation",
                            "tooltip" => "Nb d'Invocations",
                            "comment" => "1 + " . $this->getInvocation(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                 default:
                    return $this->_invocation;
            }
        }
        public function getTouch(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "touch",
                            "label" => "Bonus de touche",
                            "placeholder" => "Bonus de touche du ou de la PNJ",
                            "tooltips" => "Bonus de touche",
                            "value" => $this->_touch,
                            "color" => "touch",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "touch.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_touch} Touche",
                            "color" => "touch",
                            "tooltip" => "Bonus de touche",
                            "style" => View::STYLE_BACK,
                            "id" => "touch"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "touch.png",
                            "color" => "touch",
                            "tooltip" => "Bonus de touche",
                            "content" => $this->_touch,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_touch} Touche",
                            "color" => "touch",
                            "tooltip" => "Bonus de touche",
                            "comment" => $this->getTouch(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_touch;
            }
        }
        public function getCa(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = 10 + $this->_ca + $this->getVitality();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "ca",
                            "label" => "Bonus de CA",
                            "placeholder" => "Classe d'armure du ou de la PNJ",
                            "tooltips" => "Bonus de Classe d'armure",
                            "value" => $this->_ca,
                            "color" => "ca-d-4",
                            "comment" => $this->getVitality(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "ca.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_ca} CA",
                            "color" => "ca-d-4",
                            "tooltip" => "Bonus de Classe d'armure",
                            "style" => View::STYLE_BACK,
                            "id" => "ca"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "ca.png",
                            "color" => "ca-d-4",
                            "tooltip" => "Bonus de Classe d'armure",
                            "content" => $this->_ca,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} CA",
                            "color" => "ca-d-4",
                            "tooltip" => "Classe d'armure",
                            "comment" => $this->getVitality(Content::FORMAT_BADGE) . " + " . $this->getCa(Content::FORMAT_BADGE)
                        ], 
                        write: false);
                
                default:
                    return $this->_ca;
            }
        }
        public function getDodge_pa(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = 10 + $this->_dodge_pa + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "dodge_pa",
                            "label" => "Bonus d'esquive PA",
                            "placeholder" => "Bonus d'esquive PA du ou de la PNJ",
                            "tooltips" => "Bonus d'Esquive PA",
                            "value" => $this->_ca,
                            "color" => "pa",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "dodge_pa.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_dodge_pa} Esquive PA",
                            "color" => "pa",
                            "tooltip" => "Bonus d'Esquive PA",
                            "style" => View::STYLE_BACK,
                            "id" => "dodge_pa"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "dodge_pa.png",
                            "color" => "pa",
                            "tooltip" => "Bonus d'Esquive PA",
                            "content" => $this->_dodge_pa,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Esquive PA",
                            "color" => "pa",
                            "tooltip" => "Esquive PA",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getDodge_pa(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_dodge_pa;
            }
        }
        public function getDodge_pm(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = 10 + $this->_dodge_pm + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "dodge_pm",
                            "label" => "Bonus d'esquive PM",
                            "placeholder" => "Bonus d'esquive PM du ou de la PNJ",
                            "tooltips" => "Bonus d'Esquive PM",
                            "value" => $this->_ca,
                            "color" => "pm",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "dodge_pm.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_dodge_pm} Esquive PM",
                            "color" => "pm",
                            "tooltip" => "Bonus d'Esquive PM",
                            "style" => View::STYLE_BACK,
                            "id" => "dodge_pm"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "dodge_pm.png",
                            "color" => "pm",
                            "tooltip" => "Bonus d'Esquive PM",
                            "content" => $this->_dodge_pm,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Esquive PM",
                            "color" => "pm",
                            "tooltip" => "Esquive PM",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getDodge_pm(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_dodge_pm;
            }
        }
        public function getFuite(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_fuite + $this->getAgi();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "fuite",
                            "label" => "Bonus de Fuite",
                            "placeholder" => "Bonus de Fuite du ou de la PNJ",
                            "tooltips" => "Bonus de Fuite",
                            "value" => $this->_fuite,
                            "color" => "fuite",
                            "comment" => $this->getAgi(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "fuite.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_fuite} Fuite",
                            "color" => "fuite",
                            "tooltip" => "Bonus de Fuite",
                            "style" => View::STYLE_BACK,
                            "id" => "fuite"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "fuite.png",
                            "color" => "fuite",
                            "tooltip" => "Bonus de Fuite",
                            "content" => $this->_fuite,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Fuite",
                            "color" => "fuite",
                            "tooltip" => "Fuite",
                            "comment" => $this->getAgi(Content::FORMAT_BADGE) . " + " . $this->getFuite(Content::FORMAT_BADGE)
                        ], 
                        write: false);
                
                default:
                    return $this->_fuite;
            }
        }
        public function getTacle(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_tacle + $this->getChance();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "tacle",
                            "label" => "Bonus de Tacle",
                            "placeholder" => "Bonus de Tacle du ou de la PNJ",
                            "tooltips" => "Bonus de Tacle",
                            "value" => $this->_tacle,
                            "color" => "tacle",
                            "comment" => $this->getChance(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "tacle.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_tacle} Tacle",
                            "color" => "tacle",
                            "tooltip" => "Bonus de Tacle",
                            "style" => View::STYLE_BACK,
                            "id" => "tacle"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "tacle.png",
                            "color" => "tacle",
                            "tooltip" => "Bonus de Tacle",
                            "content" => $this->_tacle,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Tacle",
                            "color" => "tacle",
                            "tooltip" => "Tacle",
                            "comment" => $this->getChance(Content::FORMAT_BADGE) . " + " . $this->getTacle(Content::FORMAT_BADGE)
                        ], 
                        write: false);
                
                default:
                    return $this->_tacle;
            }
        }
        public function getVitality(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "vitality",
                            "label" => "Vitalité",
                            "placeholder" => "Vitalité du ou de la PNJ",
                            "tooltips" => "Vitalité",
                            "value" => $this->_vitality,
                            "color" => "vitality",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "vitality.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_vitality} Vitalité",
                            "color" => "vitality",
                            "tooltip" => "Vitalité",
                            "style" => View::STYLE_BACK,
                            "id" => "vitality"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "vitality.png",
                            "color" => "vitality",
                            "tooltip" => "Vitalité",
                            "content" => $this->_vitality,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_vitality} Vitalité",
                            "color" => "vitality",
                            "tooltip" => "Vitalité",
                            "comment" => $this->getVitality(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_vitality;
            }
        }
        public function getSagesse(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "sagesse",
                            "label" => "Sagesse",
                            "placeholder" => "Sagesse du ou de la PNJ",
                            "tooltips" => "Sagesse",
                            "value" => $this->_sagesse,
                            "color" => "sagesse",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "sagesse.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_sagesse} Sagesse",
                            "color" => "sagesse",
                            "tooltip" => "Sagesse",
                            "style" => View::STYLE_BACK,
                            "id" => "sagesse"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "sagesse.png",
                            "color" => "sagesse",
                            "tooltip" => "Sagesse",
                            "content" => $this->_sagesse,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_sagesse} Sagesse",
                            "color" => "sagesse",
                            "tooltip" => "Sagesse",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_sagesse;
            }
        }
        public function getStrong(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "strong",
                            "label" => "Force",
                            "placeholder" => "Force du ou de la PNJ",
                            "tooltips" => "Force",
                            "value" => $this->_strong,
                            "color" => "strong",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "strong.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_strong} Force",
                            "color" => "strong",
                            "tooltip" => "Force",
                            "style" => View::STYLE_BACK,
                            "id" => "strong"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "strong.png",
                            "color" => "strong",
                            "tooltip" => "Force",
                            "content" => $this->_strong,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_strong} Force",
                            "color" => "strong",
                            "tooltip" => "Force",
                            "comment" => $this->getStrong(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_strong;
            }
        }
        public function getIntel(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "intel",
                            "label" => "Intelligence",
                            "placeholder" => "Intelligence du ou de la PNJ",
                            "tooltips" => "Intelligence",
                            "value" => $this->_intel,
                            "color" => "intel",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "intel.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_intel} Intelligence",
                            "color" => "intel",
                            "tooltip" => "Intelligence",
                            "style" => View::STYLE_BACK,
                            "id" => "intel"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "intel.png",
                            "color" => "intel",
                            "tooltip" => "Intelligence",
                            "content" => $this->_intel,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_intel} Intelligence",
                            "color" => "intel",
                            "tooltip" => "Intelligence",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_intel;
            }
        }
        public function getAgi(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "agi",
                            "label" => "Agilité",
                            "placeholder" => "Agilité du ou de la PNJ",
                            "tooltips" => "Agilité",
                            "value" => $this->_agi,
                            "color" => "agi",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "agi.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_agi} Agilité",
                            "color" => "agi",
                            "tooltip" => "Agilité",
                            "style" => View::STYLE_BACK,
                            "id" => "agi"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "agi.png",
                            "color" => "agi",
                            "tooltip" => "Agilité",
                            "content" => $this->_agi,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_agi} Agilité",
                            "color" => "agi",
                            "tooltip" => "Agilité",
                            "comment" => $this->getAgi(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_agi;
            }
        }
        public function getChance(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "chance",
                            "label" => "Chance",
                            "placeholder" => "Chance du ou de la PNJ",
                            "tooltips" => "Chance",
                            "value" => $this->_chance,
                            "color" => "chance",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "chance.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_chance} Chance",
                            "color" => "chance",
                            "tooltip" => "Chance",
                            "style" => View::STYLE_BACK,
                            "id" => "chance"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "chance.png",
                            "color" => "chance",
                            "tooltip" => "Chance",
                            "content" => $this->_chance,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_chance} Chance",
                            "color" => "chance",
                            "tooltip" => "Chance",
                            "comment" => $this->getChance(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_chance;
            }
        }  
        public function getRes_neutre(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_neutre",
                            "label" => "Résistance neutre",
                            "placeholder" => "Résistance neutre du ou de la PNJ",
                            "tooltips" => "Résistance neutre",
                            "value" => $this->_res_neutre,
                            "color" => "neutre-d-2",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "res_neutre.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_res_neutre} Résistance neutre",
                            "color" => "neutre-d-2",
                            "tooltip" => "Résistance neutre",
                            "style" => View::STYLE_BACK,
                            "id" => "res_neutre"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "res_neutre.png",
                            "color" => "neutre-d-2",
                            "tooltip" => "Résistance neutre",
                            "content" => $this->_res_neutre,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_res_neutre} Résistance neutre",
                            "color" => "neutre",
                            "tooltip" => "Résistance neutre",
                            "comment" => $this->getRes_neutre(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_res_neutre;
            }
        }
        public function getRes_terre(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_terre",
                            "label" => "Résistance terre",
                            "placeholder" => "Résistance terre du ou de la PNJ",
                            "tooltips" => "Résistance terre",
                            "value" => $this->_res_terre,
                            "color" => "terre",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "res_terre.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_res_terre} Résistance terre",
                            "color" => "terre",
                            "tooltip" => "Résistance terre",
                            "style" => View::STYLE_BACK,
                            "id" => "res_terre"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "res_terre.png",
                            "color" => "terre",
                            "tooltip" => "Résistance terre",
                            "content" => $this->_res_terre,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_res_terre} Résistance terre",
                            "color" => "terre",
                            "tooltip" => "Résistance terre",
                            "comment" => $this->getRes_terre(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_res_terre;
            }
        }
        public function getRes_feu(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_feu",
                            "label" => "Résistance feu",
                            "placeholder" => "Résistance feu du ou de la PNJ",
                            "tooltips" => "Résistance feu",
                            "value" => $this->_res_feu,
                            "color" => "feu",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "res_feu.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_res_feu} Résistance feu",
                            "color" => "feu",
                            "tooltip" => "Résistance feu",
                            "style" => View::STYLE_BACK,
                            "id" => "res_feu"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "res_feu.png",
                            "color" => "feu",
                            "tooltip" => "Résistance feu",
                            "content" => $this->_res_feu,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_res_feu} Résistance feu",
                            "color" => "feu",
                            "tooltip" => "Résistance feu",
                            "comment" => $this->getRes_feu(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_res_feu;
            }
        }
        public function getRes_air(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_air",
                            "label" => "Résistance air",
                            "placeholder" => "Résistance air du ou de la PNJ",
                            "tooltips" => "Résistance air",
                            "value" => $this->_res_air,
                            "color" => "air",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "res_air.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_res_air} Résistance air",
                            "color" => "air",
                            "tooltip" => "Résistance air",
                            "style" => View::STYLE_BACK,
                            "id" => "res_air"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "res_air.png",
                            "color" => "air",
                            "tooltip" => "Résistance air",
                            "content" => $this->_res_air,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_res_air} Résistance air",
                            "color" => "air",
                            "tooltip" => "Résistance air",
                            "comment" => $this->getRes_air(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_res_air;
            }
        }
        public function getRes_eau(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_eau",
                            "label" => "Résistance eau",
                            "placeholder" => "Résistance eau du ou de la PNJ",
                            "tooltips" => "Résistance eau",
                            "value" => $this->_res_eau,
                            "color" => "eau",
                            "comment" => "Bonus d'équipement",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "res_eau.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_res_eau} Résistance eau",
                            "color" => "eau",
                            "tooltip" => "Résistance eau",
                            "style" => View::STYLE_BACK,
                            "id" => "res_eau"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "res_eau.png",
                            "color" => "eau",
                            "tooltip" => "Résistance eau",
                            "content" => $this->_res_eau,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$this->_res_eau} Résistance eau",
                            "color" => "eau",
                            "tooltip" => "Résistance eau",
                            "comment" => $this->getRes_eau(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_res_eau;
            }
        }
        public function getAcrobatie(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_acrobatie + $this->getAgi();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "acrobatie",
                            "label" => "Bonus d'Acrobatie",
                            "placeholder" => "Bonus d'Acrobatie du ou de la PNJ",
                            "tooltips" => "Bonus d'Acrobatie",
                            "value" => $this->_acrobatie,
                            "color" => "agi",
                            "comment" => $this->getAgi(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_acrobatie} Bonus d'Acrobatie",
                            "color" => "agi",
                            "tooltip" => "Bonus d'Acrobatie",
                            "style" => View::STYLE_BACK,
                            "id" => "acrobatie"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Acrobatie",
                            "color" => "agi",
                            "tooltip" => "Acrobatie",
                            "comment" => $this->getAgi(Content::FORMAT_BADGE) . " + " . $this->getAcrobatie(Content::FORMAT_BADGE)
                        ], 
                        write: false);
                
                default:
                    return $this->_acrobatie;
            }
        }
        public function getDiscretion(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_discretion + $this->getAgi();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "discretion",
                            "label" => "Bonus de Discrétion",
                            "placeholder" => "Bonus de Discrétion du ou de la PNJ",
                            "tooltips" => "Bonus de Discrétion",
                            "value" => $this->_discretion,
                            "color" => "agi",
                            "comment" => $this->getAgi(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_discretion} Bonus de Discrétion",
                            "color" => "agi",
                            "tooltip" => "Bonus de Discrétion",
                            "style" => View::STYLE_BACK,
                            "id" => "discretion"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Discrétion",
                            "color" => "agi",
                            "tooltip" => "Discrétion",
                            "comment" => $this->getAgi(Content::FORMAT_BADGE) . " + " . $this->getDiscretion(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_discretion;
            }
        }
        public function getEscamotage(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_escamotage + $this->getAgi();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "escamotage",
                            "label" => "Bonus d'Escamotage",
                            "placeholder" => "Bonus d'Escamotage du ou de la PNJ",
                            "tooltips" => "Bonus d'Escamotage",
                            "value" => $this->_escamotage,
                            "color" => "agi",
                            "comment" => $this->getAgi(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_escamotage} Bonus d'Escamotage",
                            "color" => "agi",
                            "tooltip" => "Bonus d'Escamotage",
                            "style" => View::STYLE_BACK,
                            "id" => "escamotage"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Escamotage",
                            "color" => "agi",
                            "tooltip" => "Escamotage",
                            "comment" => $this->getAgi(Content::FORMAT_BADGE) . " + " . $this->getEscamotage(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_escamotage;
            }
        }
        public function getAthletisme(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_athletisme + $this->getStrong();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "athletisme",
                            "label" => "Bonus d'Athletisme",
                            "placeholder" => "Bonus d'Athletisme du ou de la PNJ",
                            "tooltips" => "Bonus d'Athletisme",
                            "value" => $this->_athletisme,
                            "color" => "strong",
                            "comment" => $this->getStrong(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_athletisme} Bonus d'Athletisme",
                            "color" => "strong",
                            "tooltip" => "Bonus d'Athletisme",
                            "style" => View::STYLE_BACK,
                            "id" => "athletisme"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Athletisme",
                            "color" => "strong",
                            "tooltip" => "Athletisme",
                            "comment" => $this->getStrong(Content::FORMAT_BADGE) . " + " . $this->getAthletisme(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_athletisme;
            }
        }
        public function getIntimidation(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_intimidation + $this->getStrong();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "intimidation",
                            "label" => "Bonus d'Intimidation",
                            "placeholder" => "Bonus d'Intimidation du ou de la PNJ",
                            "tooltips" => "Bonus d'Intimidation",
                            "value" => $this->_intimidation,
                            "color" => "strong",
                            "comment" => $this->getStrong(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_intimidation} Bonus d'Intimidation",
                            "color" => "strong",
                            "tooltip" => "Bonus d'Intimidation",
                            "style" => View::STYLE_BACK,
                            "id" => "intimidation"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Intimidation",
                            "color" => "strong",
                            "tooltip" => "Intimidation",
                            "comment" => $this->getStrong(Content::FORMAT_BADGE) . " + " . $this->getIntimidation(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_intimidation;
            }
        }
        public function getArcane(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_arcane + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "arcane",
                            "label" => "Bonus d'Arcane",
                            "placeholder" => "Bonus d'Arcane du ou de la PNJ",
                            "tooltips" => "Bonus d'Arcane",
                            "value" => $this->_arcane,
                            "color" => "intel",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_arcane} Bonus d'Arcane",
                            "color" => "intel",
                            "tooltip" => "Bonus d'Arcane",
                            "style" => View::STYLE_BACK,
                            "id" => "arcane"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Arcane",
                            "color" => "intel",
                            "tooltip" => "Arcane",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->getArcane(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_arcane;
            }
        }
        public function getHistoire(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_histoire + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "histoire",
                            "label" => "Bonus d'Histoire",
                            "placeholder" => "Bonus d'Histoire du ou de la PNJ",
                            "tooltips" => "Bonus d'Histoire",
                            "value" => $this->_histoire,
                            "color" => "intel",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_histoire} Bonus d'Histoire",
                            "color" => "intel",
                            "tooltip" => "Bonus d'Histoire",
                            "style" => View::STYLE_BACK,
                            "id" => "histoire"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Histoire",
                            "color" => "intel",
                            "tooltip" => "Histoire",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->getHistoire(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_histoire;
            }
        }
        public function getInvestigation(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_investigation + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "investigation",
                            "label" => "Bonus d'Investigation",
                            "placeholder" => "Bonus d'Investigation du ou de la PNJ",
                            "tooltips" => "Bonus d'Investigation",
                            "value" => $this->_investigation,
                            "color" => "intel",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_investigation} Bonus d'Investigation",
                            "color" => "intel",
                            "tooltip" => "Bonus d'Investigation",
                            "style" => View::STYLE_BACK,
                            "id" => "investigation"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Investigation",
                            "color" => "intel",
                            "tooltip" => "Investigation",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->getInvestigation(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_investigation;
            }
        }
        public function getReligion(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_religion + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "religion",
                            "label" => "Bonus d'Religion",
                            "placeholder" => "Bonus d'Religion du ou de la PNJ",
                            "tooltips" => "Bonus d'Religion",
                            "value" => $this->_religion,
                            "color" => "intel",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_religion} Bonus d'Religion",
                            "color" => "intel",
                            "tooltip" => "Bonus d'Religion",
                            "style" => View::STYLE_BACK,
                            "id" => "religion"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Religion",
                            "color" => "intel",
                            "tooltip" => "Religion",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->getReligion(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_religion;
            }
        }
        public function getNature(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_nature + $this->getIntel();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "nature",
                            "label" => "Bonus de Nature",
                            "placeholder" => "Bonus de Nature du ou de la PNJ",
                            "tooltips" => "Bonus de Nature",
                            "value" => $this->_nature,
                            "color" => "intel",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_nature} Bonus de Nature",
                            "color" => "intel",
                            "tooltip" => "Bonus de Nature",
                            "style" => View::STYLE_BACK,
                            "id" => "nature"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Nature",
                            "color" => "intel",
                            "tooltip" => "Nature",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->getNature(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_nature;
            }
        }
        public function getDressage(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_dressage + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "dressage",
                            "label" => "Bonus de Dressage",
                            "placeholder" => "Bonus de Dressage du ou de la PNJ",
                            "tooltips" => "Bonus de Dressage",
                            "value" => $this->_dressage,
                            "color" => "sagesse",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_dressage} Bonus de Dressage",
                            "color" => "sagesse",
                            "tooltip" => "Bonus de Dressage",
                            "style" => View::STYLE_BACK,
                            "id" => "dressage"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Dressage",
                            "color" => "sagesse",
                            "tooltip" => "Dressage",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getDressage(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_dressage;
            } 
        }
        public function getMedecine(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_medecine + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "medecine",
                            "label" => "Bonus de Medecine",
                            "placeholder" => "Bonus de Medecine du ou de la PNJ",
                            "tooltips" => "Bonus de Medecine",
                            "value" => $this->_medecine,
                            "color" => "sagesse",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_medecine} Bonus de Medecine",
                            "color" => "sagesse",
                            "tooltip" => "Bonus de Medecine",
                            "style" => View::STYLE_BACK,
                            "id" => "medecine"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Medecine",
                            "color" => "sagesse",
                            "tooltip" => "Medecine",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getMedecine(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_medecine;
            }
        }
        public function getPerception(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_perception + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "perception",
                            "label" => "Bonus de Perception",
                            "placeholder" => "Bonus de Perception du ou de la PNJ",
                            "tooltips" => "Bonus de Perception",
                            "value" => $this->_perception,
                            "color" => "sagesse",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_perception} Bonus de Perception",
                            "color" => "sagesse",
                            "tooltip" => "Bonus de Perception",
                            "style" => View::STYLE_BACK,
                            "id" => "perception"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Perception",
                            "color" => "sagesse",
                            "tooltip" => "Perception",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getPerception(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_perception;
            }
        }
        public function getPerspicacite(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_perspicacite + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "perspicacite",
                            "label" => "Bonus de Perspicacité",
                            "placeholder" => "Bonus de Perspicacité du ou de la PNJ",
                            "tooltips" => "Bonus de Perspicacité",
                            "value" => $this->_perspicacite,
                            "color" => "sagesse",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_perspicacite} Bonus de Perspicacité",
                            "color" => "sagesse",
                            "tooltip" => "Bonus de Perspicacité",
                            "style" => View::STYLE_BACK,
                            "id" => "perspicacite"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Perspicacité",
                            "color" => "sagesse",
                            "tooltip" => "Perspicacité",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getPerspicacite(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_perspicacite;
            }
        }
        public function getSurvie(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_survie + $this->getSagesse();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "survie",
                            "label" => "Bonus de Survie",
                            "placeholder" => "Bonus de Survie du ou de la PNJ",
                            "tooltips" => "Bonus de Survie",
                            "value" => $this->_survie,
                            "color" => "sagesse",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_survie} Bonus de Survie",
                            "color" => "sagesse",
                            "tooltip" => "Bonus de Survie",
                            "style" => View::STYLE_BACK,
                            "id" => "survie"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Survie",
                            "color" => "sagesse",
                            "tooltip" => "Survie",
                            "comment" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getSurvie(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_survie;
            }
        }
        public function getPersuasion(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_persuasion + $this->getChance();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "persuasion",
                            "label" => "Bonus de Persuasion",
                            "placeholder" => "Bonus de Persuasion du ou de la PNJ",
                            "tooltips" => "Bonus de Persuasion",
                            "value" => $this->_persuasion,
                            "color" => "chance",
                            "comment" => $this->getChance(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_persuasion} Bonus de Persuasion",
                            "color" => "chance",
                            "tooltip" => "Bonus de Persuasion",
                            "style" => View::STYLE_BACK,
                            "id" => "persuasion"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Persuasion",
                            "color" => "chance",
                            "tooltip" => "Persuasion",
                            "comment" => $this->getChance(Content::FORMAT_BADGE) . " + " . $this->getPersuasion(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_persuasion;
            }
        }
        public function getRepresentation(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_representation + $this->getChance();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "representation",
                            "label" => "Bonus de Représentation",
                            "placeholder" => "Bonus de Représentation du ou de la PNJ",
                            "tooltips" => "Bonus de Représentation",
                            "value" => $this->_representation,
                            "color" => "chance",
                            "comment" => $this->getChance(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_representation} Bonus de Représentation",
                            "color" => "chance",
                            "tooltip" => "Bonus de Représentation",
                            "style" => View::STYLE_BACK,
                            "id" => "representation"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Représentation",
                            "color" => "chance",
                            "tooltip" => "Représentation",
                            "comment" => $this->getChance(Content::FORMAT_BADGE) . " + " . $this->getRepresentation(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_representation;

            }   
        }
        public function getSupercherie(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $total = $this->_supercherie + $this->getChance();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "supercherie",
                            "label" => "Bonus de Supercherie",
                            "placeholder" => "Bonus de Supercherie du ou de la PNJ",
                            "tooltips" => "Bonus de Supercherie",
                            "value" => $this->_supercherie,
                            "color" => "chance",
                            "comment" => $this->getChance(Content::FORMAT_BADGE) . " Bonus d'équipement",
                            "style" => View::STYLE_INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_supercherie} Bonus de Supercherie",
                            "color" => "chance",
                            "tooltip" => "Bonus de Supercherie",
                            "style" => View::STYLE_BACK,
                            "id" => "supercherie"
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => "{$total} Supercherie",
                            "color" => "chance",
                            "tooltip" => "Supercherie",
                            "comment" => $this->getChance(Content::FORMAT_BADGE) . " + " . $this->getSupercherie(Content::FORMAT_BADGE)
                        ], 
                        write: false);

                default:
                    return $this->_supercherie;

            }
        }
        public function getKamas(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "kamas",
                            "label" => "Kamas",
                            "placeholder" => "Nb de Kamas du ou de la PNJ",
                            "tooltips" => "Kamas",
                            "value" => $this->_kamas,
                            "color" => "kamas-d-3",
                            "style" => View::STYLE_INPUT_ICON,
                            "size" => View::SIZE_SM,
                            "icon" => "kamas.png",
                            "style_icon" => View::STYLE_ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_kamas} Kamas",
                            "color" => "kamas-d-3",
                            "tooltip" => "Nb de Kamas",
                            "style" => View::STYLE_BACK,
                            "id" => "kamas"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => View::STYLE_ICON_MEDIA,
                            "icon" => "kamas.png",
                            "color" => "kamas-d-3",
                            "tooltip" => "Kamas",
                            "content" => $this->_kamas,
                            "content_placement" => "before"
                        ], 
                        write: false); 

                default:
                    return $this->_kamas;
            }
        }
        public function getDrop_(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Npc",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "drop_",
                            "label" => "Objets Récupérables",
                            "placeholder" => "Objets récupérables du ou de la PNJ",
                            "value" => $this->_drop_,
                            "style" => View::STYLE_INPUT_FLOATING,
                            "comment" => "Objets récupérables du ou de la PNJ"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_drop_,
                            "color" => "grey-d-2",
                            "tooltip" => "Objets récupérables du ou de la PNJ",
                            "style" => View::STYLE_OUTLINE
                        ], 
                        write: false);

                default:
                    return $this->_drop_;

            }
        }

        public function getOther_equipment(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "npc",
                            "id" => "other_equipment".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "other_equipment",
                            "label" => "Autres équipements",
                            "value" => $this->_other_equipment
                        ], 
                        write: false);
                
                default:
                    return html_entity_decode($this->_other_equipment);
            }
        }
        public function getOther_spell(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "npc",
                            "id" => "other_spell".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "other_spell",
                            "label" => "Autres sorts",
                            "value" => $this->_other_spell
                        ], 
                        write: false);
                
                default:
                    return html_entity_decode($this->_other_spell);
            }
        }
        public function getOther_consomable(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "npc",
                            "id" => "other_consomable".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "other_consomable",
                            "label" => "Autres consomables",
                            "value" => $this->_other_consomable
                        ], 
                        write: false);
                
                default:
                    return html_entity_decode($this->_other_consomable);
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName($data){
            $this->_name = $data;
            return true;
        }
        public function setClasse($data){
            $this->_classe = $data;
            return true;
        }
        public function setStory($data){
            $this->_story = $data;
            return true;
        }
        public function setHistorical($data){
            $this->_historical = $data;
            return true;
        }
        public function setAlignment($data){
            $this->_alignment = $data;
            return true;
        }
        public function setLevel($data){
            $this->_level = $data;
            return true;
        }
        public function setTrait($data){
            $this->_trait = $data;
            return true;
        }
        public function setOther_info($data){
            $this->_other_info = $data;
            return true;
        }
        public function setAge($data){
            $this->_age = $data;
            return true;
        }
        public function setSize($data){
            $this->_size = $data;
            return true;
        }
        public function setWeight($data){
            $this->_weight = $data;
            return true;
        }
        public function setLife($data){
            $this->_life = $data;
            return true;
        }
        public function setPa($data){
            $this->_pa = $data;
            return true;
        }
        public function setPm($data){
            $this->_pm = $data;
            return true;
        }
        public function setPo($data){
            $this->_po = $data;
            return true;
        }
        public function setIni($data){
            $this->_ini = $data;
            return true;
        }
        public function setInvocation($data){
            $this->_invocation = $data;
            return true;
        }
        public function setTouch($data){
            $this->_touch = $data;
            return true;
        }
        public function setCa($data){
            $this->_ca = $data;
            return true;
        }
        public function setDodge_pa($data){
            $this->_dodge_pa = $data;
            return true;
        }
        public function setDodge_pm($data){
            $this->_dodge_pm = $data;
            return true;
        }
        public function setFuite($data){
            $this->_fuite = $data;
            return true;
        }
        public function setTacle($data){
            $this->_tacle = $data;
            return true;
        }
        public function setVitality($data){
            $this->_vitality = $data;
            return true;
        }
        public function setSagesse($data){
            $this->_sagesse = $data;
            return true;
        }
        public function setStrong($data){
            $this->_strong = $data;
            return true;
        }
        public function setIntel($data){
            $this->_intel = $data;
            return true;
        }
        public function setAgi($data){
            $this->_agi = $data;
            return true;
        }
        public function setChance($data){
            $this->_chance = $data;
            return true;
        }
        public function setRes_neutre($data){
            $this->_res_neutre = $data;
            return true;
        }
        public function setRes_terre($data){
            $this->_res_terre = $data;
            return true;
        }
        public function setRes_feu($data){
            $this->_res_feu = $data;
            return true;
        }
        public function setRes_air($data){
            $this->_res_air = $data;
            return true;
        }
        public function setRes_eau($data){
            $this->_res_eau = $data;
            return true;
        }
        public function setAcrobatie($data){
            $this->_acrobatie = $data;
            return true;
        }
        public function setDiscretion($data){
            $this->_discretion = $data;
            return true;
        }
        public function setEscamotage($data){
            $this->_escamotage = $data;
            return true;
        }
        public function setAthletisme($data){
            $this->_athletisme = $data;
            return true;
        }
        public function setIntimidation($data){
            $this->_intimidation = $data;
            return true;
        }
        public function setArcane($data){
            $this->_arcane = $data;
            return true;
        }
        public function setHistoire($data){
            $this->_histoire = $data;
            return true;
        }
        public function setInvestigation($data){
            $this->_investigation = $data;
            return true;
        }
        public function setNature($data){
            $this->_nature = $data;
            return true;
        }
        public function setReligion($data){
            $this->_religion = $data;
            return true;
        }
        public function setDressage($data){
            $this->_dressage = $data;
            return true;
        }
        public function setMedecine($data){
            $this->_medecine = $data;
            return true;
        }
        public function setPerception($data){
            $this->_perception = $data;
            return true;
        }
        public function setPerspicacite($data){
            $this->_perspicacite = $data;
            return true;
        }
        public function setSurvie($data){
            $this->_survie = $data;
            return true;
        }
        public function setPersuasion($data){
            $this->_persuasion = $data;
            return true;
        }
        public function setRepresentation($data){
            $this->_representation = $data;
            return true;
        }
        public function setSupercherie($data){
            $this->_supercherie = $data;
            return true;
        }
        public function setKamas($data){
            $this->_kamas = $data;
            return true;
        }
        public function setDrop_($data){
            $this->_drop_ = $data;
            return true;
        }
        public function setOther_equipement($data){
            $this->_other_consomable = $data;
            return true;
        }
        public function setOther_consomable($data){
            $this->_other_consomable = $data;
            return true;
        }
        public function setOther_spell($data){
            $this->_other_spell = $data;
            return true;
        }
}