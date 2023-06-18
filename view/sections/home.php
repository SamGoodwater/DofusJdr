<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Home
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
        "title" => "Accueil",
        "description" => "Section d'accueil",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => true,
        "onlyForAdmin" => false,
        "shownListAddInPage" => true
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    $pageTemp = new Page(array(
        'uniqid' => "home"
    ));
    $manager = new SectionManager;
    $sections = $manager->getAllFromPage($pageTemp);
    ob_start(); ?>

        <div class="sortablebis">

            <?php if(empty($sections)){ ?>
                <p>La page est vide <i class="fas fa-sad-tear"></i></p>
                <p><a data-bs-toggle="collapse" onclick="Section.getVisual('<?=$pageTemp->getUniqid()?>',true);" >Ajouter un paragraphe pour commencer.</a></p>
            <?php }
            
            foreach ($sections as $section) { ?>
                <?= $section->getVisual(Content::FORMAT_EDITABLE); ?>      
            <?php } ?>

        </div>

        <div class="text-right">
            <a onclick="Section.getVisual('<?=$pageTemp->getUniqid()?>',true);" class="size-2 text-main-d-3 text-main-d-1-hover mx-3"><i class="far fa-plus-square"></i></a>
        </div>

        <script>
            $(document).ready(function(){ 
                var containtModal = "<div><input type=\"hidden\" id=\"type\" value=\"text.php\"><div class=\"form-floating m-2\"><input id=\"title\" placeholder='' type='text' class='form-control form-control-main-focus form-control form-control-main-focus-sm'><label class='size-0-8'>Titre du nouveau paragraphe</label></div><div class='text-right m-2'><a onclick=\"Section.add('home')\" class=\"btn btn-sm btn-border-secondary\">Ajouter</a></div></div>";
                Page.build(Page.RESPONSIVE, "Ajouter un paragraphe", containtModal);

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
            });
        </script>
    <?php $template["content"] = ob_get_clean();

}