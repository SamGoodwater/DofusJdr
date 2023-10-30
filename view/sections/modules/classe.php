<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Classe
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
        "title" => "Classe",
        "description" => "Section de gestion des classes",
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
            <a class="btn btn-sm btn-animate back-grey text-d-2 mb-2" onclick="return false; Page.build(true, 'Création d\'une classe', $('#addClasse'), Page.SIZE_MD, true);">Ajouter une classe</a>
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
                data-detail-formatter="detailFormatter"
                >

                <thead>
                    <tr>
                        <th class="text-center" data-sortable="false" data-visible="true" data-field="edit"></th>
                        <th class="text-center" data-sortable="false" data-visible="true" data-field="pdf"></th>
                        <th class="text-center" data-sortable="false" data-visible="true" data-field="bookmark"></th>
                        <th class="text-center" data-sortable="true" data-visible="false" data-field="id">ID</th>
                        <th class="text-center" data-sortable="false" data-visible="false" data-field="uniqid"></th>
                        <th data-sortable="false" data-visible="true" data-field="path_img_logo"><i class="fa-solid fa-image"></i></th>
                        <th data-sortable="false" data-visible="false" data-field="path_img"><i class="fa-solid fa-image"></i></th>
                        <th class="text-center" data-sortable="true" data-visible="true" data-field="name">Nom</th>
                        <th class="text-center" data-sortable="false" data-visible="true" data-field="description_fast">Description<br>succinte</th>
                        <th class="text-center" data-sortable="true" data-visible="true" data-field="weapons_of_choice">Arme de prédilection</th>
                        <th class="text-center" data-sortable="false" data-visible="false" data-field="trait"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Traits du mod">Traits</span></th>
                        <th class="text-center" data-sortable="false" data-visible="false" data-field="description">Description</th>
                        <th class="text-center" data-sortable="false" data-visible="false" data-field="life">Gestion de la vitalité</th>
                        <th class="text-center" data-sortable="false" data-visible="true" data-field="life_dice"></th>
                        <th class="text-center" data-sortable="false" data-visible="false" data-field="specificity">Spécificités</th>
                        <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_add">Date de création</th>
                        <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_updated">Date de mise à jour</th>
                        <th class="text-center" data-sortable="true" data-visible="false" data-field="usable"><span data-bs-toggle='tooltip' data-bs-placement='top' title="L'objet est adapté au jdr"><i class='fa-solid fa-check text-green-d-3'></i> JDR</span></th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
            <p class="mt-2"><i class="fa-solid fa-info-circle"></i> Il y a <span class="total_obj"></span> classes.</p>

            <div id="addClasse" style="display:none;">
                <div class="form-floating mb-1">
                    <input 
                        id="name"
                        placeholder="Nom de la classe" 
                        maxlength="300" 
                        type="text" 
                        class="form-control form-control-main-focus" 
                        value="">
                    <label class="size-0-8">Nom</label>
                </div>
                <div class="form-floating">
                    <select class="form-select" id="weapons">
                        <?php foreach (Classe::WEAPONS as $name => $weapons) { ?>
                            <option value="<?=$weapons?>"><?=$name?></option>
                        <?php } ?>
                    </select>
                    <label >Arme de prédilection</label>
                </div>
                <div class="modal-footer d-flex flex-row justify-content-between">
                    <button type="button" class="btn btn-sm btn-animate btn-border-grey" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-animate btn-back-secondary" onclick="Classe.add();">Ajouter</button>
                </div>
            </div>

            <script>
                Classe.createAndLoadDataBootstrapTable();
            </script>
        <?php $template["content"] = ob_get_clean();
    }
