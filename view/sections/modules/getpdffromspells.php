<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Listes de sorts
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
        "title" => "PDF de sorts",
        "description" => "Créer un pdf avec des sorts sélectionnés",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => false,
        "shownListAddInPage" => true,
        "refStockDataOption" => "" // référence des données de l'option dans la page
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    $manager = new ClasseManager();
    ob_start(); ?>
        <p>Sélectionner les sorts à insérer dans le PDF finale.</p>
        <div class="d-block">
            <div>
                <div class="d-flex flex-row justify-content-evenly align-items-baseline mb-3 position-relative">
                    <div class="form-floating w-100">
                        <input  type="text" 
                                data-url = "index.php?c=search&a=search"
                                data-search_in = <?=ControllerModule::SEARCH_IN_SPELL?>
                                data-minlenght = 3
                                data-parameter = "showResume<?=$this->getUniqid()?>"
                                data-action = <?=ControllerModule::SEARCH_DONE_GET_SPELL?>
                                data-limit = 10
                                data-only_usable = true
                                class="form-control form-control-main-focus" 
                                id="getSpell<?=$this->getUniqid()?>" 
                                placeholder="Rechercher un sort">
                        <label for="getSpell<?=$this->getUniqid()?>">Rechercher un sort</label>
                    </div>
                    <span id="search-sign"></span>
                </div>
                <script>autocomplete_load("#getSpell<?=$this->getUniqid()?>");</script>
            </div>
            <button class="btn btn-sm btn-animate btn-back-secondary" onclick="getPdfFromListUniqidsSpells()">Générer le PDF</button>
        </div>
        <div id="showResume<?=$this->getUniqid()?>" class="d-flex flex-row justify-content-start">

        </div>
        <script>
            function getPdfFromListUniqidsSpells(){
                var uniqids = "";
                document.querySelectorAll("#showResume<?=$this->getUniqid()?> [data-uniqid]").forEach(function(element){
                    uniqids += element.getAttribute("data-uniqid") + "|";
                });
                if(uniqids != ""){
                    window.open('index.php?c=spell&a=getPdf&uniqids='+uniqids, '_blank')
                } else {
                    MsgAlert("Veuillez sélectionner au moins un sort.", '', "red" , 4000);
                }
            }
        </script>
    <?php $template["content"] = ob_get_clean();

}