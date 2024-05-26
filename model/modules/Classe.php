<?php

class Classe extends Module
{
    const FECA = "62057f83d450d";
    const OSAMODAS = "62057fc39235f";
    const ENUTROF = "6205855578057";
    const SRAM = "6205857244fbd";
    const XELOR = "6205858631aa6";
    const ECAFLIP = "62058599d17f3";
    const ENIRIPSA = "620585bf304c8";
    const IOP = "620585d2cde72";
    const CRA = "620585e242898";
    const SADIDA = "6205864dd3d78";
    const SACRIER = "6205866608915";
    const PANDAWA = "6205867381210";

    const WEAPONS = [
        "hache" => 0,
        "arc" => 1,
        "dague" => 2,
        "marteau" => 3,
        "pelle" => 4,
        "baton" => 5,
        "épée" => 6,
        "baguette" => 7,
        "mains" => 8
    ];

    const FILES = [
        "img" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/classes/default.png",
            "dir" => "medias/modules/classes/",
            "preferential_format" => "png",
            'naming' => "[name]"
        ],
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/classes/default_logo.png",
            "dir" => "medias/modules/classes/",
            "preferential_format" => "svg",
            "naming" => "[name]_logo"
        ]
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_description_fast='';
        private $_description='';
        private $_life='';
        private $_life_dice=10;
        private $_specificity='';
        private $_weapons_of_choice='';
        private $_trait="";

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥

        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Classe",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom de la classe",
                            "placeholder" => "Nom",
                            "value" => $this->_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
        public function getDescription_fast(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Classe",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "description_fast",
                            "label" => "Description succincte",
                            "placeholder" => "",
                            "value" => $this->_description_fast
                        ], 
                        write: false);

                default:
                    return $this->_description_fast;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "Classe",
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
        public function getLife(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    
                    $view = new View(View::TEMPLATE_SNIPPET);
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "Classe",
                            "id" => "life_".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "life",
                            "label" => "Gestion de la vitalité",
                            "value" => $this->_life
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_life)){return "";}
                    return html_entity_decode($this->_life);
            }
        }
        public function getLife_dice(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Classe",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "life_dice",
                            "label" => "Valeur du dé de vie",
                            "placeholder" => "10",
                            "tooltip" => "Valeur du dé de vie",
                            "value" => $this->_life_dice,
                            "color" => "grey-l-4",
                            "style" => Style::INPUT_ICON,
                            "icon" => "dice12.svg",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Dé de vie : {$this->_life_dice}<img class='icon icon-15 ms-1 align-text-bottom' src='medias/icons/modules/dice12.svg' alt='Icône du dé de vie'>",
                            "color" => "grey-d-4",
                            "tooltip" => "Valeur du dé de vie",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "dice12.svg",
                            "color" => "grey-d-4",
                            "tooltip" => "Valeur du dé de vie",
                            "content" => $this->_life_dice,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false);
                
                default:
                    return $this->_life_dice;
            }
        }
        public function getSpecificity(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "Classe",
                            "id" => "specificity_".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "specificity",
                            "label" => "Spécificités de la classe",
                            "value" => $this->_specificity
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_specificity)){return "";}
                    return html_entity_decode($this->_specificity);
            }
        }
        public function getWeapons_of_choice(int $format = Content::FORMAT_BRUT){
            $path = "medias/modules/weapons/".array_search($this->_weapons_of_choice, Classe::WEAPONS).".svg";

            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    $items = [];
                    foreach (Classe::WEAPONS as $name => $weapon) {
                        $items[] = [
                            "label" => $name,
                            "display" => "<span><img class='icon me-1' src='medias/modules/weapons/".array_search($weapon, Classe::WEAPONS).".svg' alt='Icône de l\'arme de prédilection de la classe' >". $name."</span>",
                            "onclick" => "Classe.update('".$this->getUniqid()."', '".$weapon."', 'weapons_of_choice',".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getWeapons_of_choice(Content::FORMAT_BADGE),
                            "items" => $items,
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    if(in_array($this->_weapons_of_choice, Classe::WEAPONS)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "<img class='icon me-1' src='".$path."' alt='Icône de l\'arme de prédilection de la classe'>" . array_search($this->_weapons_of_choice, Classe::WEAPONS),
                                "color" => "",
                                "tooltip" => "Arme de prédilection",
                                "style" => Style::STYLE_NONE
                            ], 
                            write: false);
                    } else  {
                        return "";
                    }

                case Content::FORMAT_ICON:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    if(in_array($this->_weapons_of_choice, Classe::WEAPONS)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "<img class='icon me-1' src='".$path."' alt='Icône de l\'arme de prédilection de la classe'>",
                                "color" => "",
                                "tooltip" => "Arme de prédilection",
                                "style" => Style::STYLE_NONE
                            ], 
                            write: false);
                    } else  {
                        return "";
                    }

                case Content::FORMAT_PATH:
                    if(in_array($this->_weapons_of_choice, Classe::WEAPONS)){
                        return $path;
                    } else  {
                        return "";
                    }

                default:
                    return $this->_weapons_of_choice;
            }
        }
        public function getTrait(int $format = Content::FORMAT_BRUT){
            $view = new View();
            if(!empty($this->_trait)){
                $traits = explode(",", $this->_trait);
                if(!is_array($traits) || count($traits) == 0){
                    $traits = array();
                }
            } else {
                $traits = array();
            }
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Classe",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "trait",
                            "label" => "Traits",
                            "value" => $this->_trait,
                            "placeholder" => "Traits",
                            "style" => Style::INPUT_FLOATING,
                            "comment" => "Séparer les différents traits par des virgules."
                        ], 
                        write: false);

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

                case Content::FORMAT_ARRAY:
                    return explode(",", $this->_trait);

                default:
                    return $this->_trait;
            }

        }
        public function getSpell(int $format = Content::FORMAT_BRUT, bool $is_editable = false, $size = 300){
            $manager = new ClasseManager();
            $spells = $manager->getLinkSpell($this);
            // La liste de spell est composé de plusieurs sous tableau de sorts, la key de chaque sous tableau correspond à l'id du groupe.
            
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View();
                    ob_start();?>

                        <div class="">
                            <?=$this->getSpell(Content::DISPLAY_RESUME, true);?>

                            <div class="d-flex flex-colum align-items-center justify-content-center">
                                <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Ajouter un sort" class="ms-2 btn btn-sm btn-animate btn-back-main" onclick="Classe.initModalUpdateSpell('group_<?=uniqid()?>');"><i class="fa-regular fa-plus-square"></i> Ajouter un nouveau sort</a>
                            </div>
                        </div>

                        <div id="modalAddSpell" class="modal" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content back-main-l-5">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Ajouter un sort</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php $view->dispatch(
                                            template_name : "input/search",
                                            data : [
                                                "id" => "addSpell" . $this->getUniqid(),
                                                "title" => "Ajouter un sort",
                                                "label" => "Rechercher un sort",
                                                "placeholder" => "Rechercher un sort",
                                                "search_in" => ControllerModule::SEARCH_IN_SPELL,
                                                "parameter" => $this->getUniqid(),
                                                "action" => ControllerModule::SEARCH_DONE_ADD_SPELL_TO_CLASSE
                                            ], 
                                            write: true); ?>

                                            <input id="data-uniqid" type="hidden" value>                                                                                      
                                            <input id="data-id_group" type="hidden" value>                                                                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php return ob_get_clean();
                break;
        
                case Content::DISPLAY_RESUME:
                    $view = new View(View::TEMPLATE_DISPLAY);
                    if(!empty($spells)){
                        return $view->dispatch(
                            template_name : "spell/list",
                            data : [
                                "spells" => $spells,
                                "is_editable" => $is_editable,
                                "is_removable" => $is_editable,
                                "uniqid" => $this->getUniqid(),
                                "class_name" => "Classe",
                                "size" => $size,
                                'in_competition' => true
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
                                    "is_link" => true,
                                    "in_competition" => true
                                ], 
                                write: true);
                        return ob_get_clean();
                    }
                    return "";
        
                case Content::FORMAT_ARRAY:
                    return $spells;
            }
        }
        
        public function getCapability(int $format = Content::FORMAT_BRUT, bool $display_remove = false, $size = 300){
            $manager = new ClasseManager();
            $capabilities = $manager->getLinkCapability($this);
            
            switch ($format) {
                case Content::FORMAT_EDITABLE:
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
                            "action" => ControllerModule::SEARCH_DONE_ADD_CAPABILITY_TO_CLASSE,
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
                                "class_name" => "Classe",
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
        
    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥S
        public function setName($data){
            $this->_name = $data;
            return true;
        }
        public function setDescription_fast($data){
            $this->_description_fast = $data;
            return true;
        }
        public function setDescription($data){
            $this->_description = $data;
            return true;
        }
        public function setSpecificity($data){
            $this->_specificity = $data;
            return true;
        }
        public function setLife($data){
            $this->_life = $data;
            return true;
        }
        public function setlife_dice(string | int | null $data){
            if($data == null || $data == "" || $data == 0 || $data == "0"){
                $data = 10;
            }
            $this->_life_dice = $data;
            return true;
        }
        public function setWeapons_of_choice($data){
            $this->_weapons_of_choice = $data;
            return true;
        }
        public function setTrait($data){
            $this->_trait = $data;
            return true;
        }

        /* Data = array(
                        uniqid => id du spell
                    )
            Js : REMOVE : Classe.update(UniqidM,{action:'remove', uniqid:'uniqidS', group;'id_group'},'spell', IS_VALUE);
            Js : UPDATE : Classe.update(UniqidM,{action:'update', uniqid:'uniqidS', uniqidNew:'uniqidSNew', group;'id_group'},'spell', IS_VALUE);
            Js : ADD : Classe.update(UniqidM,{action:'add', uniqid:'uniqidS', group;'id_group'},'spell', IS_VALUE);
        */
        public function setSpell(array $data){ 
            $managerC = new ClasseManager;
            $managerS = new SpellManager;

            if(!isset($data['action'])) {
                throw new InvalidArgumentException("Une action est requise.");
            }
            if(!isset($data['uniqid'])) {
                throw new InvalidArgumentException("L'uniqid du sort et l'indicateur de sort sont requis pour ajouter un sort.");
            }
            if(!$managerS->existsUniqid($data['uniqid'])) {
                throw new InvalidArgumentException("Le sort spécifié n'existe pas.");
            }
            $spell = $managerS->getFromUniqid($data['uniqid']);
        
            switch ($data['action']) {
                case 'add':
                    if(isset($data['id_group'])) {
                        $groupID = $data['id_group'];
                    } else {
                        // Création d'un nouveau groupe si aucun id_group n'est fourni
                        $groupID = uniqid();
                    }
        
                    // Vérification si le lien existe déjà
                    if($managerC->existsLinkSpell($this, $spell, $groupID)) {
                        throw new RuntimeException("Ce lien entre la classe et le sort existe déjà.");
                    }
                    if($managerC->addLinkSpell($this, $spell, $groupID)) {
                        return true;
                    } else {
                        throw new RuntimeException("Erreur lors de l'ajout du sort à la classe.");
                    }
        
                case "update":
                    if(!isset($data['uniqidNew']) || !isset($data['id_group'])) {
                        throw new InvalidArgumentException("Le nouvel uniqid et l'identifiant du groupe sont requis pour mettre à jour un sort.");
                    }
        
                    if(!$managerS->existsUniqid($data['uniqidNew'])) {
                        throw new InvalidArgumentException("L'un des sorts spécifiés n'existe pas.");
                    }
                    $spellNew = $managerS->getFromUniqid($data['uniqidNew']);
                    $groupID = $data['id_group'];
        
                    if(!$managerC->existsLinkSpell($this, $spell, $groupID)) {
                        throw new RuntimeException("Ce lien entre la classe et le sort n'existe pas.");
                    }
        
                    if($managerC->updateLinkSpell($this, $spell, $spellNew, $groupID)) {
                        return true;
                    } else {
                        throw new RuntimeException("Erreur lors de la mise à jour du lien entre la classe et le sort.");
                    }
                
                case "remove":          
                    if(!isset($data['id_group'])) {
                        throw new InvalidArgumentException("L'identifiant du groupe et l'indicateur de sort sont requis pour supprimer un sort.");
                    }
        
                    $groupID = $data['id_group'];
        
                    if(!$managerC->removeLinkSpell($this, $spell, $groupID)) {
                        throw new RuntimeException("Erreur lors de la suppression du sort de la classe.");
                    }
        
                    return true;
        
                default:
                    throw new InvalidArgumentException("L'action spécifiée n'est pas valide.");
            }
        }
        

        /* Data = array(uniqid => id du capability)
            Js : Classe.update(UniqidM,{action:'add|remove|update', uniqid:'uniqIdS'},'capability', IS_VALUE);
        */
        public function setCapability(array $data){ 
            $manager = new ClasseManager;
            $managerS = new CapabilityManager;
            if(!isset($data['uniqid'])){throw new Exception("L'uniqid de l'aptitude n'est pas défini");}
            if($managerS->existsUniqid($data['uniqid'])){
                $capability = $managerS->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            if($manager->addLinkCapability($this, $capability)){
                                return true;
                            }else{
                                throw new Exception("Erreur lors de l'ajout de l'aptitude");
                            }
               
                        case "remove":
                            if($manager->removeLinkCapability($this, $capability)){
                                return true;
                            }else{
                                throw new Exception("Erreur lors de la suppression de l'aptitude");
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
