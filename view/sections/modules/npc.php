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
        "title" => "PNJ",
        "description" => "Section de gestion des personnages non joueurs",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => false,
        "shownListAddInPage" => true
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    ob_start(); ?>
        <button type="button" class="btn btn-sm btn-animate btn-back-secondary me-2" onclick="Page.build(true, 'Création d\'un·e personnage non joueur·euse', $('#addNpc'), Page.SIZE_MD, true);">Nouveau / Nouvelle PNJ</button>

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
                    <th data-sortable="false" data-visible="true" data-field="logo"><i class="fa-solid fa-image"></i></th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-filter-control="select" data-field="classe">Classe</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-filter-control="select" data-field="level">Niveau</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="master_bonus">Bonus de maitrise</th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="name">Nom</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="story">Histoire<br>du ou de la PNJ</th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="historical">Historique</th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="alignment">Alignement</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="trait">Traits</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="other_info">Informations</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="age">Age</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="size">Taille</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="weight">Poids</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="life">Points de vie</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="pa"><img class='icon' src='medias/icons/modules/pa.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="pm"><img class='icon' src='medias/icons/modules/pm.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="po"><img class='icon' src='medias/icons/modules/po.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="ini"><img class='icon' src='medias/icons/modules/ini.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="invocation"><img class='icon' src='medias/icons/modules/invocation.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="touch"><img class='icon' src='medias/icons/modules/touch.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="ca"><img class='icon' src='medias/icons/modules/ca.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="dodge_pa"><img class='icon' src='medias/icons/modules/dodge_pa.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="dodge_pm"><img class='icon' src='medias/icons/modules/dodge_pm.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="fuite"><img class='icon' src='medias/icons/modules/fuite.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="tacle"><img class='icon' src='medias/icons/modules/tacle.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="vitality"><img class='icon' src='medias/icons/modules/vitality.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="sagesse"><img class='icon' src='medias/icons/modules/sagesse.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="strong"><img class='icon' src='medias/icons/modules/force.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="intel"><img class='icon' src='medias/icons/modules/intel.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="agi"><img class='icon' src='medias/icons/modules/agi.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="chance"><img class='icon' src='medias/icons/modules/chance.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="res_neutre"><img class='icon' src='medias/icons/modules/res_neutre.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="res_terre"><img class='icon' src='medias/icons/modules/res_terre.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="res_feu"><img class='icon' src='medias/icons/modules/res_feu.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="res_air"><img class='icon' src='medias/icons/modules/res_air.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="res_eau"><img class='icon' src='medias/icons/modules/res_eau.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="acrobatie">Acrobatie</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="discretion">Discrétion</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="escamotage">Escamotage</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="athletisme">Athlétisme</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="intimidation">Intimidation</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="arcane">Arcane</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="histoire">Histoire</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="investigation">Investigation</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="nature">Nature</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="religion">Religion</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="dressage">Dressage</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="medecine">Médecine</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="perception">Perception</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="perspicacite">Perspicacité</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="survie">Survie</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="persuasion">Persuasion</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="representation">Représentation</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="supercherie">Supercherie</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="kamas"><img class='icon' src='medias/icons/modules/kamas.png'></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="drop_">Drop</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="other_equipment">Autres équipements</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="other_consumable">Autres consommables</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="other_spell">Autres sorts</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_add">Date de création</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_updated">Date de mise à jour</th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
        <p class="mt-2"><i class="fa-solid fa-info-circle"></i> Il y a <span class="total_obj"></span> PNJ.</p>

        <!-- Modal ADD -->
        <div id="addNpc" style="display:none;">
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-main-focus" id="name" placeholder="Nom du ou de la PNJ">
                <label for="floatingInput">Nom du ou de la PNJ</label>
            </div>
            <div class="form-floating mb-2">
                <select class="form-select" id="classe" aria-label="Floating label select example">
                    <?php $classeManager = new ClasseManager();
                        foreach ($classeManager->getAll() as $classe) {
                            echo "<option value='".$classe->getUniqid()."'>".$classe->getName()."</option>";      
                        }
                    ?>
                </select>
                <label for="floatingSelect">Classe du ou de la PNJ</label>
            </div>
            <div class="form-floating mb-2">
                <select class="form-select" id="level" aria-label="Niveau du ou de la PNJ">
                    <?php 
                        for ($i=1; $i <= 20 ; $i++) {
                            echo "<option value='".$i."'>Niveau ".$i."</option>";      
                        }
                    ?>
                </select>
                <label for="level">Niveau du ou de la PNJ</label>
            </div>
            <div class="modal-footer d-flex flex-row justify-content-between">
                <button type="button" class="btn btn-sm btn-animate btn-border-grey" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="Npc.add();" class="btn btn-sm btn-animate btn-back-secondary">Créer</button>
            </div>
        </div>

        <script>
            Npc.createAndLoadDataBootstrapTable();
        </script>
    <?php $template["content"] = ob_get_clean();

}