<?php
class Classe extends Content
{
    public function __construct(array $donnees){
        parent::__construct($donnees);

        if(file_exists("medias/classes/".strtolower($this->getName()).".png")){
            $this->setPath_image("medias/classes/".strtolower($this->getName()).".png");
        }
        if(file_exists("medias/classes/".strtolower($this->getName())."_logo.svg")){
            $this->setPath_image_logo("medias/classes/".strtolower($this->getName())."_logo.svg");
        }
    } 

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

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_description_fast='';
        private $_description='';
        private $_life='';
        private $_specificity='';
        private $_weapons_of_choice='';
        private $_trait="";
        private $_path_img_logo="medias/classe/default_logo.svg";
        private $_path_img="medias/classe/default.png";

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
                    return html_entity_decode($this->_life);
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
                    return html_entity_decode($this->_specificity);
            }
        }
        public function getWeapons_of_choice(int $format = Content::FORMAT_BRUT){
            $path = "medias/weapons/".array_search($this->_weapons_of_choice, Classe::WEAPONS).".svg";

            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    $items = [];
                    foreach (Classe::WEAPONS as $name => $weapon) {
                        $items[] = [
                            "label" => $name,
                            "display" => "<span><img class='icon me-1' src='medias/weapons/".array_search($weapon, Classe::WEAPONS).".svg'>". $name."</span>",
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
                                "content" => "<img class='icon me-1' src='".$path."'>" . array_search($this->_weapons_of_choice, Classe::WEAPONS),
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
                                "content" => "<img class='icon me-1' src='".$path."'>",
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
                            foreach (explode(",", $this->_trait) as $trait) { 
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
        public function getPath_img(int $format = Content::FORMAT_BRUT, $css = ""){
            if(file_exists("medias/classes/".strtolower($this->getName()).".png")){
                $this->setPath_image("medias/classes/".strtolower($this->getName()).".png");
            }
            
            if(!empty($this->_path_img) || file_exists($this->_path_img)){
                $path = $this->_path_img;
            } else {
                $path = "medias/mobs/default.png";
            }

            switch ($format) {
                case  Content::FORMAT_BRUT:
                    return $path;
                break;
                case  Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div class="text-center">
                            <div id='showFile_path_image' class='m-2 d-flex justify-content-center'><?=$this->getPath_img(Content::FORMAT_FANCY, 'img-back-180');?></div>
                            <a type="button" 
                                class="btn btn-border-secondary btn-sm me-2" 
                                onclick=''>
                                <i class="fas fa-cloud-upload-alt"></i> Modifier
                            </a>
                        </div>
                    <?php return ob_get_clean();
                break;
                case  Content::FORMAT_IMAGE:
                    if($css == "") { $css = "img-back-90 rounded-circle";}
                    ob_start(); ?>
                        <div class='<?=$css?>' style="background-image:url('<?=$path?>')"></div>
                    <?php return ob_get_clean();
                break;
                case Content::FORMAT_FANCY:
                    $svg_plugin = "";
                    if(strtolower(pathinfo($path)['extension']) == "svg"){
                        $svg_plugin = "data-type='iframe'";
                    }
                    if($css == "") { $css = "img-back-30 rounded-circle";}
                    ob_start(); ?>
                        <a data-fancybox="gallery" <?=$svg_plugin?> href='<?=$path?>' class="d-flex justify-content-center"><div class='<?=$css?>' style='background-image:url("<?=$path?>")'></div></a>
                    <?php return ob_get_clean();
                break;
                
                default:
                    return $path;
                break;
            }
        }
        public function getPath_img_logo(int $format = Content::FORMAT_BRUT, $css = ""){
            if(file_exists("medias/classes/".strtolower($this->getName())."_logo.svg")){
                $this->setPath_image("medias/classes/".strtolower($this->getName())."_logo.svg");
            }
            
            if(!empty($this->_path_img_logo) || file_exists($this->_path_img_logo)){
                $path = $this->_path_img_logo;
            } else {
                $path = "medias/mobs/default_logo.svg";
            }

            switch ($format) {
                case  Content::FORMAT_BRUT:
                    return $path;
                break;
                case  Content::FORMAT_EDITABLE:

                break;
                case  Content::FORMAT_IMAGE:
                    if($css == "") { $css = "img-back-90 rounded-circle";}
                    ob_start(); ?>
                        <div class='<?=$css?>' style="background-image:url('<?=$path?>')"></div>
                    <?php return ob_get_clean();
                break;
                case Content::FORMAT_FANCY:
                    $svg_plugin = "";
                    if(strtolower(pathinfo($path)['extension']) == "svg"){
                        $svg_plugin = "data-type='iframe'";
                    }
                    if($css == "") { $css = "img-back-30 rounded-circle";}
                    ob_start(); ?>
                        <a data-fancybox="gallery" <?=$svg_plugin?> href='<?=$path?>' class="d-flex justify-content-center"><div class='<?=$css?>' style='background-image:url("<?=$path?>")'></div></a>
                    <?php return ob_get_clean();
                break;
                
                default:
                    return $path;
                break;
            }
        }

        public function getSpell(int $format = Content::FORMAT_BRUT, bool $display_remove = false, $size = 300){
            $manager = new ClasseManager();
            $spells = $manager->getLinkSpell($this);
            
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View();
                    $html = $view->dispatch(
                        template_name : "input/search",
                        data : [
                            "id" => "addSpell" . $this->getUniqid(),
                            "title" => "Ajouter un sort",
                            "label" => "Rechercher un sort",
                            "placeholder" => "Rechercher un sort",
                            "search_in" => ControllerSearch::SEARCH_IN_SPELL,
                            "parameter" => $this->getUniqid(),
                            "action" => ControllerSearch::SEARCH_DONE_ADD_SPELL_TO_CLASSE,
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
                                "class_name" => "Classe",
                                "size" => $size
                            ], 
                            write: true);
                    }
                    return "";
                case Content::FORMAT_ARRAY:
                    return $spells;
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
        public function setWeapons_of_choice($data){
            $this->_weapons_of_choice = $data;
            return true;
        }
        public function setTrait($data){
            $this->_trait = $data;
            return true;
        }
        public function setPath_image($data){
            if(is_file($data)){
                $file = New File($data);
                if(FileManager::isImage($file)){
                    $this->_path_img = $data;
                    return true;
                } else {
                    return "Le fichier doit être une image.";
                }
            } else {
                return "Le fichier n'est pas valide.";
            }
        }
        public function setPath_image_logo($data){
            if(is_file($data)){
                $file = New File($data);
                if(FileManager::isImage($file)){
                    $this->_path_img_logo = $data;
                    return true;
                } else {
                    return "Le fichier doit être une image.";
                }
            } else {
                return "Le fichier n'est pas valide.";
            }
        }

        /* Data = array(
                        uniqid => id du spell
                    )
            Js : Classe.update(UniqidM,{action:'add|remove|update', uniqid:'uniqIdS'},'spell', IS_VALUE);
        */
        public function setSpell(array $data){ 
            $managerC = new ClasseManager;
            $managerS = new SpellManager;
            if(!isset($data['uniqid'])){return "L'uniqid du sort n'est pas défini";}
            if($managerS->existsUniqid($data['uniqid'])){
                $spell = $managerS->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            if($managerC->addLinkSpell($this, $spell)){
                                return true;
                            }else{
                                return "Erreur lors de l'ajout du sort";
                            }
               
                        case "remove":
                            if($managerC->removeLinkSpell($this, $spell)){
                                return true;
                            }else{
                                return "Erreur lors de la suppression du sort";
                            }

                        default:
                            return "L'action n'est pas valide";
                    }

                } else {
                    return "Une action est requise.";
                }

            }
        }
}
