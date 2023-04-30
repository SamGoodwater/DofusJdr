<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Capability
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
        "title" => "Arbres d'aptitudes",
        "description" => "Affichage des arbres d'aptitudes",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false
    );

ob_start(); ?>
    <div class="form-floating mb-2">
        <select class="form-select" id="option" aria-label="Sélectionner un arbre d'aptitudes">
            <?php 
                foreach (Capability::CATEGORY as $name => $tree) {
                    if($tree != Capability::CATEGORY_HISTORICAL){
                        echo "<option value='".$tree."'>".$name."</option>";  
                    }    
                }
            ?>
        </select>
        <label for="option">Sélectionner un arbre d'aptitudes</label>
    </div>
<?php $template["option"] = ob_get_clean();

    
if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    $manager = new CapabilityManager();

    if(in_array($template_vars['content'], Capability::CATEGORY)){
        $capabilities = $manager->getAll(category : [$template_vars['content']], usable : true);
        
        if(!empty($capabilities)){
            
            ob_start(); ?>
                <?php
                    function compareByPowerful($capability1, $capability2){
                        return $capability1->getPowerful() - $capability2->getPowerful();
                    }
                    usort($capabilities, 'compareByPowerful');
                    $capabilities_sort_by_powerful = array();
                    foreach ($capabilities as $capabilitY) {
                        $powerful = $capabilitY->getPowerful();
                        if (!array_key_exists($powerful, $capabilities_sort_by_powerful)) {
                            $capabilities_sort_by_powerful[$powerful] = array();
                        }
                        array_push($capabilities_sort_by_powerful[$powerful], $capabilitY);
                    }
                ?>

                <div id="tree" class="tree" style="width:100%; height:100%;">
                    <?php $i=1; foreach ($capabilities_sort_by_powerful as $key => $powerful_objs) { ?>
                        <div class="tree-line">
                            <div>
                                <h3 class="bold">Palier <?= $i ?></h3>	
                            </div>
                            <div class='tree-line-node-content'>
                                <div class='tree-line-node'>
                                    <?php foreach ($powerful_objs as $key2 => $obj) { ?>
                                        <div class='tree-node-item border-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>'>
                                            <?= $obj->getVisual(Content::DISPLAY_RESUME)?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php $i++; } ?>
                </div>

            <?php $template["content"] = ob_get_clean();

        } else {
            $template["content"] = "Aucun arbre d'aptitudes n'a été trouvé";
        }

    } else {
        $template["content"] = "Aucun arbre d'aptitudes n'a été trouvé";
    }

}