<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Shop
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
        "title" => "Réseaux sociaux",
        "description" => "Section de gestion des réseaux sociaux",
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
        <button type="button" class="btn-sm btn btn-border-secondary btn-animate" onclick="Page.build(true, 'Création d\'un réseau social', $('#addSocial'), Page.SIZE_MD, true);">Nouveau réseau</button>

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
            >
            
            <thead>
                <tr>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="edit"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="bookmark"></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="id">ID</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="uniqid"></th>
                    <th data-sortable="false" data-visible="true" data-field="logo"><i class="fa-solid fa-image"></i></th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="name">Nom</th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="text">Texte<br>associé</th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="description">Description</th>
                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="link">Lien</th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="visible">Visibilité</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_add">Date de création</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_updated">Date de mise à jour</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="usable"><span data-bs-toggle='tooltip' data-bs-placement='top' title="L'objet est utilisable"><i class='fa-solid fa-check text-green-d-3'></i></span></th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
        <p class="mt-2"><i class="fa-solid fa-info-circle"></i> Il y a <span class="total_obj"></span> réseaux.</p>

        <!-- Modal ADD -->

        <div id="addSocial" style="display:none;">
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-main-focus" id="name" placeholder="Nom du réseau social">
                <label for="floatingInput">Nom du réseau</label>
            </div>
            <div class="mb-3">
                <div class="form-floating">
                    <input type="text" class="form-control form-control-main-focus" id="text" placeholder="#moncompte">
                    <label for="floatingInput">Texte associé au lien</label>
                </div>
                <p><small>Texte associé au lien affiché à coté de l'icone du réseau social ou du si, par exemple : #moncompte</small></p>
            </div>
            <div class="mb-3">
                <div class="form-floating">
                    <input type="text" class="form-control form-control-main-focus" id="link" placeholder="https://www.reseau.com/moncompte">
                    <label for="floatingInput">Lien vers le réseau social ou le site</label>
                </div>
            </div>
            <div class="modal-footer d-flex flex-row justify-content-between">
                <button type="button" class="btn btn-sm btn-animate btn-border-grey" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="Social.add();" class="btn btn-sm btn-animate btn-back-secondary">Créer</button>
            </div>
        </div>

        <script>
            Social.createAndLoadDataBootstrapTable();
        </script>
    <?php $template["content"] = ob_get_clean();

}