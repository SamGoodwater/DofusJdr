<?php
class Section extends Content
{
    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ CONTANTE  ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        const GET_SECTION_CONTENT = 0;
        const GET_SECTION_DESCRIPTION = 1;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_type ='';
        private $_uniqid_page ='';
        private $_title ='';
        private $_content ='';
        private $_order_num='';

        protected $_usable = true; // surcharge de la variable de Content

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
    public function getType(int $format = Content::FORMAT_BRUT){
        $manager = new SectionManager;

        switch ($format) {
            case Content::FORMAT_EDITABLE:
                ob_start(); ?>
                    <div class="dropdown">
                        <a class="" type="button" id="dropdownDisplay<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getType(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                        <div class="dropdown-menu" aria-labelledby="dropdownDisplay<?=$this->getId()?>">
                            <?php $template_vars = [
                                'get' => Section::GET_SECTION_DESCRIPTION,
                                'content' => $this->getContent(),
                                'uniqid' => $this->getUniqid(),
                                'uniqid_page' => $this->getUniqid_page(Content::FORMAT_OBJECT)->getUniqid()
                            ];
                            foreach ($manager->getAllTemplateFile() as $template) { 
                                include SectionManager::PATH_SECTION . $template; ?>
                                <a class="dropdown-item" onclick="Section.update('<?=$this->getUniqid()?>','<?=$template?>', 'type', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());">
                                    <?=$name?><br>
                                    <span class="size-0-7 text-grey-d-2"><?=$description?></span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php return ob_get_clean();

            case Content::FORMAT_BADGE:
                $template_vars = [
                    'get' => Section::GET_SECTION_DESCRIPTION,
                    'content' => $this->getContent(),
                    'uniqid' => $this->getUniqid(),
                    'uniqid_page' => $this->getUniqid_page(Content::FORMAT_OBJECT)->getUniqid()
                ];
                if(file_exists(SectionManager::PATH_SECTION . $template)){
                    include SectionManager::PATH_SECTION . $template;
                    return "<span class='badge back-main-d-2'>".$name."</span>";
                } else {
                    return "Aucun template associé à la section";
                }

            default:
                return $this->_type;
        }
    }
    public function getUniqid_page($format=NULL){
        switch ($format) {
            case Content::FORMAT_OBJECT:
                $manager = new PageManager();
                if($manager->existsUniqid($this->_uniqid_page)){
                    return $manager->getFromUniqid($this->_uniqid_page);
                } else {
                    return "La page n'existe pas";
                }
            
            default:
                return $this->_uniqid_page;
        }
    }
    public function getTitle(int $format = Content::FORMAT_BRUT){
        switch ($format) {
            case Content::FORMAT_EDITABLE:
                ob_start(); ?>
                    <input 
                        class='form-control form-control-main-focus' 
                        onchange="Section.update('<?=$this->getUniqid()?>', this, 'title');" 
                        placeholder="titre de la section" 
                        type="text"
                        maxlength="255" 
                        value="<?=$this->_title?>">
                <?php return ob_get_clean();
            
            default:
                return $this->_title;
        }
    }
    public function getContent(int $format = Content::FORMAT_BRUT){
        switch ($format) {
            case Content::FORMAT_EDITABLE:
                ob_start(); ?>
                    <div class="form-group">
                        <div  id="content<?=$this->getUniqid()?>"><?=html_entity_decode($this->_content)?></div>
                        <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' style="display: none;" onclick="Section.update('<?=$this->getUniqid()?>', CKEDITOR5['content<?=$this->getUniqid()?>'].getData(), 'content', <?=Controller::IS_VALUE?>)"><small><i class="fas fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
                    </div>
                <?php return ob_get_clean();
            
            default:
                return html_entity_decode($this->_content);
        }
    }
    public function getOrder_num(int $format = Content::FORMAT_BRUT){
        switch ($format) {
            case Content::FORMAT_EDITABLE:
                ob_start(); ?>
                    <input 
                        onchange="Section.update('<?=$this->getId();?>', this, 'order_num');"  
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
    
    public function getVisual(int $display = Content::DISPLAY_CARD, int $size = 300){

        switch ($display) {
            case Content::DISPLAY_EDITABLE:
                $template_vars = [
                    'get' => Section::GET_SECTION_CONTENT,
                    'content' => $this->getContent(),
                    'uniqid' => $this->getUniqid(),
                    'uniqid_page' => $this->getUniqid_page(Content::FORMAT_OBJECT)->getUniqid()
                ];
                if(file_exists(SectionManager::PATH_SECTION . $this->getType()) && is_file(SectionManager::PATH_SECTION . $this->getType())){
                    include SectionManager::PATH_SECTION . $this->getType();

                    $ondbldclick="";
                    if(isset($template['editOnDblClick'])){
                        if($template['editOnDblClick']){
                            $ondbldclick="ondblclick=\"Section.showEdit('".$this->getUniqid()."');\"";
                        }
                    }
                    ob_start(); ?>
                        <section id="section<?=$this->getUniqid()?>" class="sectionselector mb-4" data-editing="false" <?=$ondbldclick?> data-uniqid="<?=$this->getUniqid()?>">
                            <div class="section-title d-flex flex-row justify-content-between">
                                <h3 class="text-secondary-l-2" style="width:initial;"><?=$this->getTitle()?></h3>
                                <div class="section-options row justify-content-between text-right">
                                    <div>
                                        <a data-bs-toggle="tooltip" data-bs-placement="left" title="Modifier la section" onclick="Section.showEdit('<?=$this->getUniqid()?>');" class="text-main-d-3 text-main-d-1-hover"><i class="fas fa-edit"></i></a>
                                        <a data-bs-toggle="tooltip" data-bs-placement="left" title="Glisser à déposer pour trier les sections dans l'ordre southaiter." class="handleSection text-main-d-3 text-main-d-1-hover"><i class="fas fa-sort"></i></a>
                                        <a data-bs-toggle="tooltip" data-bs-placement="left" title="Supprimer la section" onclick="Section.remove('<?=$this->getUniqid()?>')" class="trash text-main-d-3 text-red-d-1-hover"><i class="fas fa-trash"></i></a> 
                                    </div>
                                </div>
                            </div>
                            <div class="sectionContentSelector">
                                <?=$template["content"]?>
                            </div>
                        </section>
                    <?php return ob_get_clean();
                } else {
                    ob_start(); ?>
                        <section id="section<?=$this->getUniqid()?>">
                            <p>Erreur : Aucun template associé à la section
                                <a class="btn btn-sm btn-text-red" onclick="Section.remove('<?=$this->getUniqid()?>');"><i class="fas fa-trash"></i> Supprimer la section</a>
                            </p>
                            <p><small class="text-grey-d-1">Impossible d'accéder au template lié à cette section. Le lien est manquant ou corrompu. Si l'erreur persiste après avoir ressayer de la recréer, veuillez contacter l'administrateur·trice du site.</small></p>
                        </section>
                    <?php return ob_get_clean();
                }

            default:
                $template_vars = [
                    'get' => Section::GET_SECTION_CONTENT,
                    'content' => $this->getContent(),
                    'uniqid' => $this->getUniqid(),
                    'uniqid_page' => $this->getUniqid_page(Content::FORMAT_OBJECT)->getUniqid()
                ];
                if(file_exists(SectionManager::PATH_SECTION . $this->getType()) && is_file(SectionManager::PATH_SECTION . $this->getType())){
                    include SectionManager::PATH_SECTION . $this->getType();
                    
                    ob_start(); ?>
                        <section>
                            <h3 class="text-main-d-4"><?=$this->getTitle()?></h3>
                            <div><?=$template["content"]?></div>
                        </section>
                    <?php return ob_get_clean();

                } else {
                    ob_start(); ?>
                        <section id="section<?=$this->getUniqid()?>">
                            <p>Erreur : Aucun template associé à la section
                                <a class="btn btn-sm btn-text-red" onclick="Section.remove('<?=$this->getUniqid()?>');"><i class="fas fa-trash"></i> Supprimer la section</a>
                            </p>
                            <p><small class="text-grey-d-1">Impossible d'accéder au template lié à cette section. Le lien est manquant ou corrompu. Si l'erreur persiste après avoir ressayer de la recréer, veuillez contacter l'administrateur·trice du site.</small></p>
                        </section>
                    <?php return ob_get_clean();
                }     
        }
    }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setType(string $data){
            if(file_exists(SectionManager::PATH_SECTION.$data)){
                $this->_type = $data;
                return true;
            } else {
                throw new Exception("Le type de section n'existe pas");
            }
        }
        public function setUniqid_page(string $data){
            $manager = new PageManager();
            if($manager->existsUniqid($data)){
                $this->_uniqid_page = $data;
                return true;
            } else {
                throw new Exception("La page n'existe pas");
            }
        }
        public function setTitle(string $data){
            $this->_title = $data;
            return true;
        }
        public function setContent($data){
            $this->_content = $data;
            return true;
        }
        public function setOrder_num(int $data){
            if($data >= 0){
                $this->_order_num = $data;
                return true;
            } else {
                $data = 0;
                return false;
            }
        }
}
