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
        "title" => "Aspirateur de données",
        "description" => "Section permettant de récupérer des données sur un site web",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => true,
        "shownListAddInPage" => true,
        "refStockDataOption" => "" // référence des données de l'option dans la page
    );
    
if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    ob_start();
        ?>
            <div class="p-2">
                <h2>Aspirateur de donnée</h2>

                <div class="m-2">
                    <p>Il y a <span id="data_total" data-total=0>0</span> objet(s) sélectionné(s)</p>
                    <p>Objet de départ (offset)</p>
                    <input id="data_offset" type="number" min=1 class="form-control" value="0">
                    <p>Nombre d'objet maximum (max)</p>
                    <input id="data_max" type="number" min=1 class="form-control" value="">
                    <p>Nombre d'objet aspirer à chaque requête (max 50) (limit)</p>
                    <input id="data_limit" type="number" min=1 max=50 class="form-control" value="50">
                </div>
                <div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="data_showItems" checked>
                        <label class="form-check-label" for="data_showItem">Afficher les équipements</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="data_showConsumables" checked>
                        <label class="form-check-label" for="data_showConsumables">Afficher les consommables</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="data_showRessources" checked>
                        <label class="form-check-label" for="data_showRessources">Afficher les ressources</label>
                    </div>
                </div>
                <div>
                    <p>Ecrire les données ? (sinon elles seront seulement affichées)</p>
                    <input id="data_write" type="checkbox" class="form-check-input">
                    <p id="data_info"></p>
                </div>

                <div>
                    <button id="data_gettotal" class="btn btn-back-green m-3">Récupérer le total</button>
                    <button id="data_submit" class="btn btn-back-green m-3 disabled">Aspirer</button>
                </div>
            </div>
            <div class="d-flex justify-content-around gap-2 m-4">
                <div id="super_category_list"></div>
                <div id="category_list"></div>
            </div>

            <div id="result"></div>

            <script>
                Tools.initVacum('<?=$this->generateAndSaveToken()?>');
            </script>
        <?php        

    $template["content"] = ob_get_clean();

}