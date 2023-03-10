<?php
class Page extends Content
{
    const PATH_FILE = "medias/page";

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ CONTANTE  ♥♥♥♥♥♥♥♥♥♥♥♥♥♥

        const CATEGORY_STARING = "Débuter";
        const CATEGORY_LISTING = "Bibliothèques";
        const CATEGORY_TOOLS = "Outils";
        const CATEGORY_OTHER = "Autres";

        const CATEGORY = [
            self::CATEGORY_STARING => 0,
            self::CATEGORY_LISTING => 1,
            self::CATEGORY_TOOLS => 3,
            self::CATEGORY_OTHER => 2,
        ];

        // Les uniqid sont spéciales pour ces pages là. Pour rajouter des pages intouchables, il faut rajouter les uniqids dans le tableau suivant.
        const UNIQID_NO_EDIT = [
            "home" => "home",
            "cgu" => "cgu",
            "gestion_des_pages" => "page_manager"
        ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_url_name='';
        private $_order_num = 10;
        private $_category = 0; // Si 0 à 3 catégorie de base, si UNIQID sous catégorie d'une autre page si -1 non catégorisé
        private $_is_dropdown = 0;
        private $_public = 1;
        private $_is_editable = 1;

        protected $_usable = true; // surcharge de la variable de Content

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div class="input-field m-1">
                            <input 
                                class='form-control form-control-main-focus' 
                                onchange="Page.update(<?=$this->getUniqid()?>, this, 'name');" 
                                placeholder="Nom de la page" 
                                type="text"
                                maxlength="500" 
                                value="<?=$this->_name?>">
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_name;
            }
        }
        public function getUrl_name(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div class="input-field m-1">
                            <label>Nom de l'URL de la page</label>
                            <input 
                                class='form-control form-control-main-focus' 
                                onchange="Page.update(<?=$this->getUniqid()?>, this, 'url_name');" 
                                placeholder="Nom visible dans l'URL de la page" 
                                type="text" 
                                pattern = "/^([a-z]|[0-9]|_|-)*$/i"
                                maxlength="500" 
                                value="<?=$this->_url_name?>">
                                <small>L'URL de la page ne doit pas contenir d'espace, ni de caractères spéciaux à l'exception des tirés (_ -). Seul les chiffres et les lettres (sans accent) doivent être utilisés.</small>
                                <small>Le nom de l'URL correspond à la partie visible dans l'URL.</small>
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_url_name;
            }
        }
        public function getOrder_num(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <input 
                            onchange="Page.update(<?=$this->getUniqid();?>, this, 'order_num');"  
                            style="width:80px" 
                            class='form-control form-control-main-focus' 
                            type="number" 
                            min='0' 
                            value="<?=$this->_order_num?>">
                    <?php return ob_get_clean();
                
                default:
                    return $this->_order_num;
            }
        }
        public function getCategory(int $format = Content::FORMAT_BRUT){
            return $this->_category;
        }                                                               
        public function getIs_dropdown(int $format = Content::FORMAT_BRUT){
            return $this->_is_dropdown;
        }
        public function getPublic(int $format = Content::FORMAT_BRUT){
            return $this->_public;
        }
        public function getIs_editable(int $format = Content::FORMAT_BRUT){
            return $this->_is_editable;
        }

        public function getSection(){
            $managerS = new SectionManager();
            return $managerS->getAllFromPage($this);            
        }
        public function getVisual(int $display = Content::DISPLAY_CARD, int $size = 300){
            $user = ControllerConnect::getCurrentUser();
           
            switch ($display) {
                case Content::DISPLAY_EDITABLE:
                    ob_start(); ?>
                        <div class="sortablebis">

                            <?php if(empty($this->getSection())){ ?>
                                <p>La page est vide <i class="fas fa-sad-tear"></i></p>
                                <p><a data-bs-toggle="collapse" onclick="Section.getVisual('<?=$this->getUniqid()?>', true);">Ajouter une section pour commencer.</a></p>
                            <?php }

                            $format = Content::FORMAT_BRUT; if($this->getIs_editable()){$format = Content::FORMAT_EDITABLE;}
                            foreach ($this->getSection() as $section__) { ?>
                                <?= $section__->getVisual($format); ?>      
                            <?php } ?>

                        </div>
                        <?php if($user->IsConnect() && $this->getIs_editable()){ ?>
                                <div class="text-right">
                                    <a onclick="Section.getVisual('<?=$this->getUniqid()?>', true);" class="size-2 text-main-d-3 text-main-d-1-hover mx-3"><i class="far fa-plus-square"></i></a>
                                </div>   
                        <?php } ?>

                        <div class="size-0-9 text-main-l-3 m-3 text-center">
                            <p>Ces explications sont tirés en très grandes parties de <a class="text-main text-main-d-2-hover" href="https://solomonk.fr/fr/">Solomonk</a> pour la partie concernant Dofus et de <a class="text-main text-main-d-2-hover" href="https://5e-drs.fr/">5e-DRS</a> pour la partie concernant Donjon & Dragon.</p>
                            <p>Nous vous invitons à vous y référer si certains points ne vous parraissent pas claire ou pour plus de détails.</p>
                            <p>Vous êtes inviter à aider la développement du JDR. N'hésitez pas à modifier les différentes sections en respectant <a class="text-main text-main-d-2-hover" href="http://jdr.iota21.fr/#contribuer">la charte de modification.</a></p>
                        </div>

                        <script>
                            $( ".sortablebis" ).sortable({
                                handle: ".handleSection",
                                scrollSpeed: 200,
                                update: function( event, ui ) {
                                    Section.updateOrder_num();
                                },
                                start: function( event, ui ) {
                                    $('.sectionContentSelector').each(function(index, value) { 
                                        $(this).hide();
                                    });
                                },
                                stop: function( event, ui ) {
                                    $('.sectionContentSelector').each(function(index, value) { 
                                        $(this).show();
                                    });
                                },
                            });
                        </script>

                    <?php return ob_get_clean();
                
                default:
                    ob_start(); ?>

                        <?php foreach ($this->getSection() as $section__) { ?>
                            <?= $section__->getVisual(); ?>        
                        <?php } ?>

                    <?php return ob_get_clean();
            }
        }
        public function getModal(){
            $return__["title"] = "Ajouter une section";
            $manager__ = new SectionManager;
            $templates__ = $manager__->getAllTemplateFile();
            ob_start(); ?>
                <div>
                    <div class="form-floating">
                        <select onchange="showOptions();" class="form-select form-select-sm m-2" id="type">
                            <?php 
                            $template_vars = [
                                'get' => Section::GET_SECTION_DESCRIPTION,
                                'content' => "",
                                'uniqid' => "",
                                'uniqid_page' => $this->getUniqid()
                            ];
                            foreach ($templates__ as $file__) { 
                                include SectionManager::PATH_SECTION . $file__;?>
                                <option value="<?= $file__?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$template["description"]?>"><?=$template["title"]?></option>
                            <?php } ?>
                        </select>
                        <label >Sélectionner un type de section</label>
                    </div>
                    <div class="form-floating m-2">
                        <input id="title" placeholder="" type="text" class="form-control form-control-main-focus form-control form-control-main-focus-sm">
                        <label class="size-0-8">Titre de la section</label>
                    </div>

                    <?php 
                    $template_vars = [
                        'get' => Section::GET_SECTION_DESCRIPTION,
                        'content' => "",
                        'uniqid' => "",
                        'uniqid_page' => $this->getUniqid()
                    ];
                    foreach($templates__ as $file__) {
                        include SectionManager::PATH_SECTION . $file__; 
                        if(isset($template["option"])){ 
                            if(!empty($template["option"])) {?>
                                <div id="<?=substr($file__, 0, -4)?>" class="option">
                                    <?=$template["option"]?>
                                </div>
                        <?php } 
                        }
                    } ?>

                    <div id="addSection" class="text-right m-2">
                        <a onclick="Section.add('<?=$this->getUrl_name()?>')" class="btn btn-sm btn-back-secondary">Ajouter</a>
                    </div>
                </div>
                <script>
                    hideOptions();
                    function hideOptions(){
                        $('.option').each(function(index, value) { 
                            $(this).hide();
                        });
                    }
                    function showOptions(){
                        hideOptions();
                        if($("#"+$("#modal #type").val().slice(0,-4)).length){ //Si la valeur du select existe en id, alors c'est qu'il y a des options associés. Il faut les afficher et enlever les autres.
                            $("#"+$("#modal #type").val().slice(0,-4)).show("slow");
                        }
                    }
                </script>
            <?php $return__["html"] = ob_get_clean();
            return $return__;
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName($data){
            $this->_name = $data;
            return true;
        }
        public function setUrl_name($data = ""){
            if(empty($data)){
                foreach (str_split(str_replace(" ", "_", strtolower(Content::removeSpecialCaractere($this->getName())))) as $letter) {
                    if(preg_match("/([a-z]|[0-9]|_|-)/i", $letter)){
                        $data .= $letter;
                    }else{
                        $data .= "-";
                    }
                }
            }

            if(preg_match("/^([a-z]|[0-9]|_|-)*$/i", $data)){
                $this->_url_name = $data;
                return true;
            } else {
                return "Erreur : des caractères non pris en charge ont été trouvés.";
            }
        }
        public function setOrder_num($data){
            if($data >= 0){
                $this->_order_num = $data;
                return true;
            } else {
                return "Order_num de la page est incorrect";
            }
        }
        public function setSection($data){ // Data = array(x,y). Si x = add alors ajout section type y 

            $mS = New SectionManager();

            if(!is_array($data)){
                return "La valeur n'est pas correcte";
            }
            if(!isset($data[0])){
                return "La valeur X n'est pas correcte";
            }                

            if($data[0] == "add"){

                if(isset($data[1])){
                    if(!empty($data[1])){
                        $section = new Section(
                            array(
                                "uniqid_page" => $this->getId(),
                                "type" => $data[1],
                                "order_num" => 10,
                                "content" => $data[2]
                        ));
                        $mS->add($section);
                        return true;
                    }else {
                        return "La valeur ne doit pas être nulle.";
                    }
                }else {
                    return "La valeur Y n'est pas correcte";
                }

            } else {
                return "La valeur X n'est pas correcte";
            }
        }
        public function setCategory($data){
            $this->_category = $data;
            return true;
        }
        public function setIs_dropdown($data){
            $this->_is_dropdown = $data;
            return true;
        }
        public function setPublic($data){
            $this->_public = $data;
            return true;
        }
        public function setIs_editable($data){
            $this->_is_editable = $data;
            return true;
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ OTHERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function existsSection(){
            $managerS = new SectionManager();
            if($managerS->existsPage($this)){
                return true;
            } else {
                return false;
            }
        }
}
