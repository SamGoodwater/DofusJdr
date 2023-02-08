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
        "title" => "Visualiseur de fichier",
        "description" => "Insérer et visualiser un fichier (PDF, audio, image, vidéo, document)",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false
    );

    ob_start(); ?>
        <div class="mb-3">
            <div id="fileupload" class="fileupload m-2">
                <form 
                    data-url="index.php?c=section&a=upload" 
                    data-viewimgpath="medias/page/"
                    data-dropable="true" 
                    data-dropzone="#collapse"
                    accept=".<?=implode(",.", FileManager::getListeExtention(FileManager::FORMAT_IMG, FileManager::FORMAT_AUDIO, FileManager::FORMAT_VIDEO, FileManager::FORMAT_PDF, FileManager::FORMAT_DOCUMENT, FileManager::FORMAT_TABLEUR, FileManager::FORMAT_SLIDER))?>"
                    capture="environnement"> 
                    <p>Ajouter un fichier à cette page</p>
                    <input class="file-input form-control form-control-main-focus form-control form-control-main-focus-sm" name="file" type="file" hidden>
                    <input type="hidden" name="title" id="title_copy" value="">
                    <input type="hidden" name="uniqid" value="<?=$template_vars['uniqid_page']?>">
                </form>
                <section class="progress-area"></section>
                <section class="uploaded-area"></section>
            </div>
            <p class="text-main-l-1 size-0-8 mt-2"><span>Attention, la section est créé dès que le fichier est chargé. Il n'y a pas besoin de cliquer sur Ajouter</span></p>
            <script>
                File.loadFileUpload('#fileupload');
            </script>  
        </div> 
    <?php $template["option"] = ob_get_clean();

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    ob_start(); ?>
        <div class="sectionContentSelector">
            <?php if(file_exists($template_vars['content'])){
                $file = New File($template_vars['content']);
                echo $file->getVisual(Content::FORMAT_VIEW, "img-back-450H-allL");
            } else {
                echo "Le fichier n'existe pas;";
            }?>
        </div>
    <?php $template["content"] = ob_get_clean();

}