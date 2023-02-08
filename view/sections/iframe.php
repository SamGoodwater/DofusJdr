<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Iframe
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
        "title" => "IFRAME",
        "description" => "Section permettant l'ajout d'Iframe",
        "content" => "",
        "option" => "",
        "editable" => true,
        "editOnDblClick" => false
    );



    ob_start(); ?>
        <div class="form-floating m-2">
            <input id="option" placeholder="" type="text" class="form-control form-control-main-focus form-control form-control-main-focus-sm" >
            <label class="size-0-8">Indiquer du site vers lequel pointera l'IFRAME</label>
        </div>
    <?php $template["option"] = ob_get_clean();

if($template_vars['get'] == Section::GET_SECTION_CONTENT){
    
    ob_start(); ?>
        <div class="sectionContentSelector">
            <p class="text-main-d-3 text-main-l-1-hover"><a href="<?=$template_vars['content']?>" data-fancybox data-type="iframe">Plein écran</a></p>
            <iframe src="<?=$template_vars['content']?>" width="100%" height="600px"><p>Votre navigateur ne supporte aucune iframe.</p></iframe>
        </div>
    <?php $template["content"] = ob_get_clean();

}