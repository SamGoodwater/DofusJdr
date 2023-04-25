<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Spell
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
        "title" => "Sorts",
        "description" => "Section de gestion des sorts",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false
    );
    
if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    $usable = 1;
    $manager = new SpellManager();
    $total = $manager->countAll();

    ob_start(); ?>
        <div class="d-flex flex-row justify-content-between align-items-end" id='sortableItems'>
            <button type="button" class="me-2 btn-sm btn btn-back-secondary" onclick="Page.build(true, 'Création d\'un sort', $('#addSpell'), Page.SIZE_MD, true);">Ajouter un sort</button>
            <div class="dropdown">
                <a class="btn btn-sm btn-border-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Catégories des sorts</a>
                <ul class="dropdown-menu p-3" aria-labelledby="typesort">
                    <?php $checked = "";
                    foreach (Spell::CATEGORY as $key => $category_) { ?>
                        <li>
                            <div class="form-check form-check-inline">
                                <input onchange="refreshUsable('#sortableItems');" class="form-check-input selectorCategory" type="checkbox" id="CheckboxCategory<?=$category_?>" value="<?=$category_?>">
                                <label class="form-check-label badge back-<?=Style::getColorFromLetter($category_)?>-d-2 text-white" for="CheckboxCategory<?=$category_?>"><?=ucfirst($key)?></label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="dropdown mx-2">
                <a class="btn btn-sm btn-border-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Elements des sorts</a>
                <ul class="dropdown-menu p-3" aria-labelledby="typesort">
                    <?php $checked = "";
                    foreach (Spell::ELEMENT as $id_element => $element_) { ?>
                        <li>
                            <div class="form-check form-check-inline">
                                <input onchange="refreshUsable('#sortableItems');" class="form-check-input selectorElement" type="checkbox" id="CheckboxElement<?=$id_element?>" value="<?=$id_element?>">
                                <label class="form-check-label badge back-<?=$element_['color']?> text-white" for="CheckboxElement<?=$id_element?>"><?=ucfirst($element_['name'])?></label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            
            <div class="dropdown mx-2">
                <a class="btn btn-sm btn-border-secondary dropdown-toggle" type="button" id="levelsort" data-bs-toggle="dropdown" aria-expanded="false">Affichage par niveau</a>
                <ul class="dropdown-menu p-4" aria-labelledby="levelsort">
                    <?php $checked = "";
                    for ($i=1; $i <= 20 ; $i++) { ?>
                        <li>
                            <div class="form-check form-check-inline">
                                <input onchange="refreshUsable('#sortableItems');" class="form-check-input selectorLevel" type="checkbox" id="CheckboxLevel<?=$i?>" value="<?=$i?>">
                                <label class="form-check-label badge back-<?=Style::getColorFromLetter($i, true)?>-d-3" for="CheckboxLevel<?=$i?>"><?=$i?></label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="form-check form-switch">
                <input onchange="refreshUsable('#sortableItems');" class="form-check-input back-main-d-1 border-main-d-1" type="checkbox" role="switch" id="selectorUsable" checked>
                <label class="form-check-label" for="selectorUsable">Afficher seulement les sorts compatibles avec le JDR</label>
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
            data-url="index.php?c=spell&a=getAll&level=all&category=all&element=all&usable=<?=$usable?>"
            >
            
            <thead>
                <tr>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="edit"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="pdf"></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="id">ID</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="uniqid"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="bookmark"></th>
                    <th data-sortable="false" data-visible="true" data-field="path_img"><i class="fas fa-image"></i></th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="name">Nom</th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="resume"></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="is_magic"><i class="fas fa-fist-raised text-brown-d-2"></i> | <i class="fas fa-magic text-purple-d-2"></i></th>
                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="category">Catégorie</th>
                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="element">Elèment</th>
                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="powerful">Puissance</th>
                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="type">Type</th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="level"><span class="text-level">Niveau</span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="pa"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Coût en points d'action du sort"><img class='icon' src='medias/icons/pa.png'></span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="po"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Portée du sort"><img class='icon' src='medias/icons/po.png'></span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="cast_per_turn"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de fois que le sort peut-être lancer par tour"><img class='icon' src='medias/icons/cast_per_turn.png'></span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="po_editable"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Sort au corps à corps, à distance avec une portée modifiable ou non"><img class='icon' src='medias/icons/po_editable.png'></span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="sight_line"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Besoin de la ligne de vue pour lancer le sort"><i class='fas fa-eye-slash text-sight-line'></i></span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="number_between_two_cast"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de tour entre deux lancer de sort"><img class='icon' src='medias/icons/number_between_two_cast.png'></span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="id_invocation"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Créature attachée au sort">Invocation attachée au sort</th>
                    <th data-sortable="false" data-visible="false"  data-field="description">Description</th>
                    <th data-sortable="false" data-visible="false"  data-field="effect">Effets</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_add">Date de création</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_updated">Date de mise à jour</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="usable"><span data-bs-toggle='tooltip' data-bs-placement='top' title="L'objet est adapté au jdr"><i class='fas fa-check text-green-d-3'></i> JDR</span></th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
        <p class="mt-2"><i class="fas fa-info-circle"></i> Il y a <?=$total?> sorts. Le chargement du tableau peut prendre quelques minutes.</p>

        <!-- Modal ADD -->

        <div id="addSpell" style="display:none;">
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-main-focus" id="name" placeholder="Nom du sort">
                <label for="floatingInput">Nom du sort</label>
            </div>
            <div class="modal-footer d-flex flex-row justify-content-between">
                <button type="button" class="btn btn-sm btn-border-grey" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="Spell.add();" class="btn btn-sm btn-sm btn-back-secondary">Créer</button>
            </div>
        </div>

        <script>
            var total = <?=$total?>;

            $('#table').bootstrapTable({
                onDblClickRow: function (row, $element, field) {
                    Spell.open(row.uniqid);
                    $('#table').bootstrapTable('collapseAllRows');
                },
                exportTypes: ["pdf","doc","xlsx","xls","xml", "json", "png", "sql", "txt", "tsv"]
            });
            $('#table tbody').on('click', function (e) {
                if($(e.target).attr('class').includes("bootstrap-table-filter-control-")){
                    $(e.target).blur();
                }
            });

            function refreshUsable(globalSelector) {
                var selectorUsable = $(globalSelector + ' #selectorUsable');
                var selectorLevel = $(globalSelector + ' .selectorLevel');
                // var selectorType = $(globalSelector + ' .selectorType');
                var selectorElement = $(globalSelector + ' .selectorElement');
                var selectorCategory = $(globalSelector + ' .selectorCategory');

                var usable = 0;
                if (selectorUsable.is(":checked")) {
                    usable  = 1;
                }
                var level = "";
                selectorLevel.each(function( index ) {
                    if ($(this).is(":checked")) {
                        level += $(this).val() + ",";
                    }
                });
                if(level==""){level="all"}

                // var type = "";
                // selectorType.each(function( index ) {
                //     if ($(this).is(":checked")) {
                //         type += $(this).val() + ",";
                //     }
                // });
                // if(type==""){type="all"}

                var element = "";
                selectorElement.each(function( index ) {
                    if ($(this).is(":checked")) {
                        element += $(this).val() + ",";
                    }
                });
                if(element==""){element="all"}

                var category = "";
                selectorCategory.each(function( index ) {
                    if ($(this).is(":checked")) {
                        category += $(this).val() + ",";
                    }
                });
                if(category==""){category="all"}

                $('#table').bootstrapTable('refreshOptions', {
                    url: 'index.php?c=spell&a=getAll&category='+category+'&element='+element+'&level='+level+'&usable='+usable
                });
            }

        </script>
    <?php $template["content"] = ob_get_clean();

}