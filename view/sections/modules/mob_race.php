<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Npc
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
        "title" => "Race",
        "description" => "Section de gestion des races des créatures",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => false,
        "shownListAddInPage" => true,
        "refStockDataOption" => "" // référence des données de l'option dans la page
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    ob_start(); ?>
    <div class="d-flex flex-row justify-content-between gap-1 align-item-center">
        <button type="button" class="mob__race__add btn btn-sm btn-animate btn-back-secondary me-2">Nouvelle Race</button>

        <div class="form-check form-switch">
            <input class="form-check-input back-main-d-1 border-main-d-1" type="checkbox" role="switch" id="toggleUsableSwitch" checked>
            <label class="form-check-label" for="toggleUsableSwitch">Afficher seulement les équipements compatibles avec le JDR</label>
        </div>
    </div>

        <table 
            id="table"
            class="table table-striped"
            data-bs-toggle="table" 
            data-classes="table-no-bordered" 
            data-remember-order='true'
            data-locale="fr-FR"
            data-show-export="true" 
            data-show-refresh="true"
            data-show-fullscreen="true"
            data-show-columns="true"
            data-show-multi-sort="true"
            data-pagination="true" 
            data-page-size="30" 
            data-page-list="[10, 25, 50, 100, 200, All]"
            data-search="true"
            data-search-accent-neutralise="true"
            data-search-align="left"
            data-search-highlight="true"
            data-search-on-enter-key="true"
            data-advanced-search="true"
            data-filter-control="true"
            data-sort-empty-last="true"
            data-sort-reset="true"
            data-toolbar="#toolbar"
            data-show-toggle="true"
            data-detail-view="true"
            data-detail-view-icon="false"
            data-detail-view-by-click="true"
            data-resizable="true"
            data-detail-formatter="false"
            >
            
            <thead>
                <tr>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="edit"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="bookmark"></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="id">ID</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="uniqid"></th>
                    <th data-sortable="false" data-visible="true" data-field="logo"><i class="fa-solid fa-image"></i></th>
                    <th data-sortable="true" data-visible="true" data-field="name"></th>
                    <th data-sortable="true" data-visible="true" data-field="super_race">Race parente</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_add">Date de création</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_updated">Date de mise à jour</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="usable"><span data-bs-toggle='tooltip' data-bs-placement='top' title="L'objet est adapté au jdr"><i class='fa-solid fa-check text-green-d-3'></i> JDR</span></th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
        <p class="mt-2"><i class="fa-solid fa-info-circle"></i> Il y a <span class="total_obj"></span> Races.</p>

        <!-- Modal ADD -->
        <div id="addMob_race" style="display:none;">
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-main-focus" id="name" placeholder="Nom de la race">
                <label for="floatingInput">Nom de la race</label>
            </div>
            <div class="form-floating mb-2">
                <select class="form-select" id="classe" aria-label="Floating label select example">
                    <option value="" selected>Aucune</option>
                    <?php $manager = new Mob_raceManager();
                        foreach ($manager->getAll() as $race) {
                            echo "<option value='".$race->getUniqid()."'>".$race->getName()."</option>";      
                        }
                    ?>
                </select>   
                <label for="floatingSelect">Super Race (facultatif)</label>
            </div>
            <div class="modal-footer d-flex flex-row justify-content-between">
                <button type="button" class="btn btn-animate btn-border-grey" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="Mob_race.add();" class="btn btn-animate btn-back-secondary">Créer</button>
            </div>
        </div>

        <script>
            Mob_race.createAndLoadDataBootstrapTable();

            const mob__race__add = document.querySelector('.mob__race__add');
            mob__race__add.addEventListener('click', function(){
                Page.build({
                        target : "modal", 
                        title : 'Création d\'une novelle race',
                        content :  $('#addMob_race'),
                        size : Page.SIZE_MD, 
                        show : true
                    });
            });
        </script>
    <?php $template["content"] = ob_get_clean();

}