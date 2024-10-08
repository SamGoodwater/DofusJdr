<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Items
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
        "title" => "Équipements",
        "description" => "Section de gestion des équipements",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => false,
        "shownListAddInPage" => true,
        "refStockDataOption" => "#itemListAdd"  // référence des données de l'option dans la page
    );

    ob_start(); ?>
        <a class="btn btn-sm btn-animate btn-underline-main" onclick="selectAllCategoryItem();">Tout sélectionner</a>
        <a class="btn btn-sm btn-animate btn-underline-main" onclick="deselectAllCategoryItem();">Tout désélectionner</a>
        <input id="itemListAdd" type="hidden" value="">
        <div class="m-2">
            <p>Sélectionner une catégorie d'équipement</p>
            <div id="optionitem" class="mb-2 item">
                <?php foreach (Item::TYPES as $name => $value) {?>
                    <div class="form-check form-check-inline ms-3">
                        <input onchange="Item.setDataInOptionInput();" class="form-check-input" type="checkbox" value="<?=$value?>" id="flexCheck<?=$name?>">
                        <label class="form-check-label" for="flexCheck<?=$name?>"><?=$name?></label>
                    </div>
                <?php } ?>
            </div>
        </div>
        <script>
            function selectAllCategoryItem(){
                var checkboxes = document.querySelectorAll('#option.item input[type="checkbox"]');
                for (var checkbox of checkboxes) {
                    checkbox.checked = true;
                }
            }
            function deselectAllCategoryItem(){
                var checkboxes = document.querySelectorAll('#option.item input[type="checkbox"]');
                for (var checkbox of checkboxes) {
                    checkbox.checked = false;
                }
            }
        </script>
    <?php $template["option"] = ob_get_clean();

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    $manager = new ItemManager;
    $type = ""; $array_type = []; $array_level = []; $level ="";
    if(preg_match('/^[0-9]*\|[0-9]{1,}/m', $template_vars['content'])){
        $type = $template_vars['content'];
        foreach (array_filter(explode("|", $type)) as $type_val) {
            if(in_array($type_val, Item::TYPES)){
                $array_type[] = $type_val;
            }
        }
    } elseif(preg_match('/[0-9]{1,}/m', $template_vars['content'])) {
        if(in_array($template_vars['content'], Item::TYPES)){
            $type = $template_vars['content'];
            $array_type[] = $template_vars['content'];
        }
    }

    ob_start(); ?>
        <div class="d-flex flex-row justify-content-between align-items-end flex-wrap gap-1" id="sortableItems">
            <button type="button" class="item_add me-2 btn-sm btn btn-back-secondary btn-animate">Ajouter un équipement</button>
            <div id="selectorTypeListCheckbox" class="dropdown">
                <a class="btn btn-sm btn-border-secondary dropdown-toggle btn-animate" type="button" id="typesort" data-bs-toggle="dropdown" aria-expanded="false">Catégorie d'équipement</a>
                <ul class="dropdown-menu p-3" aria-labelledby="typesort">
                    <?php $checked = "";
                    foreach (Item::TYPES as $key => $type_) { 
                        if(in_array($type_, $array_type)){$checked = "checked";}else{$checked="";}?>
                        <li>
                            <div class="form-check form-check-inline ms-3">
                                <input <?=$checked?> class="form-check-input selectorType" type="checkbox" id="CheckboxType<?=$type_?>" value="<?=$type_?>">
                                <label class="form-check-label badge back-<?=Style::getColorFromLetter($type_)?>-d-2 text-white" for="CheckboxType<?=$type_?>"><?=ucfirst($key)?></label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="dropdown" id="selectorLevelListCheckbox">
                <a class="btn btn-sm btn-border-secondary dropdown-toggle btn-animate" type="button" id="levelsort" data-bs-toggle="dropdown" aria-expanded="false">Affichage par niveau</a>
                <ul class="dropdown-menu p-4" aria-labelledby="levelsort">
                    <?php $checked = "";
                    for ($i=1; $i <= 20 ; $i++) {
                        if(in_array($type_,$array_level)){$checked = "checked";}else{$checked="";}?>
                        <li>
                            <div class="form-check form-check-inline ms-3">
                                <input <?=$checked?> class="form-check-input selectorLevel" type="checkbox" id="CheckboxLevel<?=$i?>" value="<?=$i?>">
                                <label class="form-check-label badge back-<?=Style::getColorFromLetter($i, true)?>-d-3" for="CheckboxLevel<?=$i?>">Niveau <?=$i?></label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input back-main-d-1 border-main-d-1" type="checkbox" role="switch" id="toggleUsableSwitch" checked>
                <label class="form-check-label" for="toggleUsableSwitch">Afficher seulement les équipements compatibles avec le JDR</label>
            </div>
        </div>
        <table 
            id="table"
            class="table table-striped"
            data-bs-toggle="table" 
            data-virtual-scroll="true"
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
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="bookmark"></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="id">ID</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="uniqid"></th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="type">Catégorie</th>
                    <th data-sortable="false" data-visible="true" data-field="path_img"><i class="fa-solid fa-image"></i></th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="name">Nom</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-filter-control="select" data-field="level"><span class="text-level">Niveau</span></th>
                    <th data-sortable="true" data-visible="false"  data-field="description">Description</th>
                    <th data-sortable="true" data-visible="false"  data-field="effect">Effet</th>
                    <th data-sortable="true" data-visible="false"  data-field="recepe">Recette</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="rarity"><span class="text-grey" data-bs-toggle='tooltip' data-bs-placement='bottom' title="Rareté de l'équipement">Rareté</span></th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="price"><span class="text-kamas" data-bs-toggle='tooltip' data-bs-placement='bottom' title="Prix estimé de l'équipement"><img class='icon' src='medias/icons/modules/kamas.png'></span></th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_add">Date de création</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_updated">Date de mise à jour</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="usable"><span data-bs-toggle='tooltip' data-bs-placement='top' title="L'objet est adapté au jdr"><i class='fa-solid fa-check text-green-d-3'></i> JDR</span></th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
        <p class="mt-2"><i class="fa-solid fa-info-circle"></i> Il y a <span class="total_obj"></span> équipements.</p>

        <!-- Modal ADD -->
        <div id="addItem" style="display:none;">
            <div class="form-floating mb-2">
                <select class="form-select" id="type" aria-label="Floating label select example">
                    <?php foreach (Item::TYPES as $key => $type) {
                            echo "<option value='".$type."'>".ucfirst($key)."</option>";      
                        } ?>
                </select>
                <label for="floatingSelect">Catérogie de l'équipement</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-main-focus" id="name" placeholder="Nom de l'équipement">
                <label for="floatingInput">Nom de l'équipement</label>
            </div>
            <div class="modal-footer d-flex flex-row justify-content-between">
                <button type="button" class="btn btn-sm btn-animate btn-border-grey" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="Item.add();" class="btn btn-sm btn-animate btn-back-secondary">Créer</button>
            </div>
        </div>

        <script>
            Item.createAndLoadDataBootstrapTable(
                [
                    {
                        selector:"#toggleUsableSwitch",
                        name:"usable",
                        type:IS_CHECKBOX
                    },
                    {
                        selector:"#selectorLevelListCheckbox",
                        name:"level",
                        type:IS_LIST_OF_CHECKBOX
                    },
                    {
                        selector:"#selectorTypeListCheckbox",
                        name:"type",
                        type:IS_LIST_OF_CHECKBOX
                    }
                ]
            );

            const item__add = document.querySelector('.item__add');
            item__add.addEventListener('click', function(){
                Page.build({
                        target : "modal", 
                        title : 'Création d\'un équipement',
                        content :  $('#addItem'),
                        size : Page.SIZE_MD, 
                        show : true
                    });
            });
        </script>
    <?php $template["content"] = ob_get_clean();

}