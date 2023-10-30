<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Text
    Version 1.0

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
        "title" => "Texte",
        "description" => "Section de texte libre",
        "content" => "",
        "option" => "",
        "editable" => true,
        "editOnDblClick" => true,
        "onlyForAdmin" => false,
        "shownListAddInPage" => true,
        "refStockDataOption" => "" // référence des données de l'option dans la page
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    ob_start(); ?>
        <div class="form-group">
            <div  id="content<?=$template_vars['uniqid']?>"><?=html_entity_decode($template_vars['content'])?></div>
            <a id="saveCkeditor" class='p-1 back-grey-l-2-hover' style="display: none;" onclick="Section.update('<?=$template_vars['uniqid']?>', CKEDITOR5['content<?=$template_vars['uniqid']?>'].getData(), 'content', <?=Controller::IS_VALUE?>)"><small><i class="fa-solid fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
        </div>
    <?php $template["content"] = ob_get_clean();

}