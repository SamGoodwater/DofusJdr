<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : User
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
        "title" => "Utilisateur·trice ",
        "description" => "Section de gestion des utilisateur·trice ",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => true,
        "shownListAddInPage" => false,
        "refStockDataOption" => "" // référence des données de l'option dans la page
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    ob_start(); ?>
        <button type="button" class="user__add btn btn-sm btn-animate btn-back-secondary me-2">Nouvel·le Utilisateur·trice</button>

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
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="id">ID</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="uniqid"></th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="is_admin"></th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="pseudo">Pseudo</th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="email">Email</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="rights">Droits</th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="timestamp_add">Date de création</th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="last_connexion">Dernière connexion</th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
        <p class="mt-2"><i class="fa-solid fa-info-circle"></i> Il y a <span class="total_obj"></span> Utilisateur·trice.</p>

        <!-- Modal ADD -->
        <div id="addUser" style="display:none;">
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-main-focus" id="email" placeholder="Email">
                <label for="email">Email</label>
            </div>
            <div class="form-floating mb-3">
                <input onchange="verifPassword();" type="password" class="form-control form-control-main-focus" id="password" placeholder="Mot de passe ou Passe-phrase">
                <label for="password">Mot de passe ou Passe-phrase</label>
            </div>
            <div class="form-floating mb-3">
                <input onchange="verifPassword();" type="password" class="form-control form-control-main-focus" id="password_repeat" placeholder="Réécrire le mot de passe ou la passe-phrase">
                <label for="password_repeat">Réécrire le mot de passe ou la passe-phrase</label>
            </div>
            <p id="display_error" class="bold text-red-d-3 size-0-8"></p>
            <div class="modal-footer d-flex flex-row justify-content-between">
                <button type="button" class="btn btn-sm btn-animate btn-border-grey" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="User.add();" class="btn btn-sm btn-animate btn-back-secondary">Créer</button>
            </div>
        </div>

        <script>
            User.createAndLoadDataBootstrapTable();
            
            function verifPassword(){
                var password = $("#password").val();
                var password_repeat = $("#password_repeat").val();
                if(password != "" && password_repeat != ""){
                    if(password != password_repeat){
                        $("#display_error").html("Les mots de passe ne correspondent pas");
                    }else{
                        $("#display_error").html("");
                    }
                }
                
            }

            const user__add = document.querySelector('.user__add');
            user__add.addEventListener('click', function(){
                Page.build({
                        target : "modal", 
                        title : 'Création d\'un utilisateur·trice',
                        content :  $('#addUser'),
                        size : Page.SIZE_MD, 
                        show : true
                    });
            });

        </script>
    <?php $template["content"] = ob_get_clean();

}