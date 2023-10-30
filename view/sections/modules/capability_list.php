<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Capability_list
    Version 1
    
    Une section est une partie d'une page.
    $titre : Aide à l'utilisateur pour choisir quel section utilisé
    $description : Aide à l'utilisateur pour choisir quel section utilisé
    $content : contenu affiché dans la page. La variable $this->getContent(), fait référence à l'enregistrement des options dans la base de donnée.
    $option : input permettant d'envoyer des données lors de la création de la section. L'input name doit être "option"
    $editable : booléen permettant de savoir si la section est éditable ou non.
    
    $template_vars est la valeur contenant les options de la section. Elle peut être utilisé dans la variable $content.
    Elle se décompose : 
    $template_vars['content'] : contenu de la section
    $template_vars['uniqid'] : identifiant de la section
*/
if(!isset($template_vars) || !is_array($template_vars)){$template_vars = array();}
if(!isset($template_vars['content'])){ $template_vars['content'] = "";}
if(!isset($template_vars['uniqid'])){ $template_vars['uniqid'] = "";}
if(!isset($template_vars['uniqid_page'])){ $template_vars['uniqid_page'] = "";}
if(!isset($template_vars['get'])){ $template_vars['get'] = Section::GET_SECTION_DESCRIPTION;}

    $template = array(
        "title" => "Liste d'Aptitudes",
        "description" => "Section d'affichages de certaines aptitudes",
        "content" => "",
        "option" => "",
        "editable" => true,
        "editOnDblClick" => false,
        "onlyForAdmin" => false,
        "shownListAddInPage" => true,
        "refStockDataOption" => "#capacityListAdd".$this->getUniqid() // référence des données de l'option dans la page
    );
    
ob_start(); ?>
    <div class="mb-3">
        <p>Sélectionner les aptitudes à insérer</p>
        <div class="d-block">
            <div>
                <div class="d-flex flex-row justify-content-evenly align-items-baseline mb-3 position-relative">
                    <div class="form-floating w-100">
                        <input  type="text" 
                                data-url = "index.php?c=search&a=search"
                                data-search_in = <?=ControllerModule::SEARCH_IN_CAPABILITY?>
                                data-minlenght = 3
                                data-parameter = "<?=$this->getUniqid()?>"
                                data-action = <?=ControllerModule::SEARCH_DONE_GET_CAPABILITY?>
                                data-limit = 10
                                data-only_usable = true
                                class="form-control form-control-main-focus" 
                                id="getCapability<?=$this->getUniqid()?>" 
                                placeholder="Rechercher une aptitude">
                        <label for="getCapability<?=$this->getUniqid()?>">Rechercher une aptitude</label>
                    </div>
                    <span id="search-sign"></span>
                </div>
                <script>autocomplete_load("#getCapability<?=$this->getUniqid()?>");</script>
            </div>
        </div>
        <input id="capacityListAdd<?=$this->getUniqid()?>" type="hidden" value="">
        <div id="showResume<?=$this->getUniqid()?>" class="d-flex flex-row justify-content-start gap-2">
            <?php if(isset($template_vars['content']) && !empty($template_vars['content'])){
                $capabilities = explode(";", $template_vars['content']);
                if(!empty($capabilities) && is_array($capabilities)){
                    foreach($capabilities as $key => $value){
                        if($value instanceof Capability){
                           echo $value->getName(Content::FORMAT_BADGE);
                        }
                    }
                }
            }?>
        </div>
    </div>
<?php $template["option"] = ob_get_clean();

if($template_vars['get'] == Section::GET_SECTION_CONTENT){
    $manager = new CapabilityManager();

    ob_start();?>
         <div class="d-flex flex-row justify-content-start gap-2">
            <?php if(isset($template_vars['content']) && !empty($template_vars['content'])){
                $capabilities = explode(";", $template_vars['content']);
                if(!empty($capabilities) && is_array($capabilities)){
                    foreach($capabilities as $uniqid){
                        if($manager->existsUniqid($uniqid)){
                            $capability = $manager->getFromUniqid($uniqid);
                            echo $capability->getVisual(new Style(["display" => Content::DISPLAY_RESUME]));
                        }
                    }
                }
            }?>
    <?php $template["content"] = ob_get_clean();

}