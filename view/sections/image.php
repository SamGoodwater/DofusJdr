<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Image
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
        "title" => "Image",
        "description" => "Section d'ajout d'image",
        "content" => "",
        "option" => "",
        "editable" => true,
        "editOnDblClick" => false
    );

    ob_start(); ?>
        <div class="mb-3 text-center text-white no-border border-bottom-3-hover border-solid border-blue-grey-d-3-hover">
            <input id='fileupload' name="file" class="form-control form-control-main-focus form-control form-control-main-focus-sm inputfile m-0 p-0" accept="image/*" type="file">
            <label for="fileupload" class="m-0 p-0 text-blue-grey-d-3"><i style="padding:10px 12px;" class="fas fa-file-upload font-size-1-3 circle text-white-hover back-blue-grey-d-3-hover back-white text-blue-grey-d-3"></i><br><span class='font-size-0-7'>Ajouter une image</span></label>
            <p class="text-main-l-1 size-0-8 mt-2"><span>Attention, la section est créé dès que l'image est chargé. Il n'y a pas besoin de cliquer sur Ajouter</span></p>
            <div class="progress"><div id="progressBar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div>
        </div>
    <?php $template["option"] = ob_get_clean();

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    ob_start(); ?>
        <div class="sectionContentSelector">
            <?php $svg_plugin = "";
            if(strtolower(pathinfo($template_vars['content'])['extension']) == "svg"){
                $svg_plugin = "data-type='iframe'";
            } ?>
            <a data-fancybox='gallery' <?=$svg_plugin?> href="<?=$template_vars['content']?>"><div class='img-back-450H-allL' style="background-image:url('<?=$template_vars['content']?>')"></div></a>
        </div>
    <?php $template["content"] = ob_get_clean();

}