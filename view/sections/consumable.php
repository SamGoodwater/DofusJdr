<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Consumables
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
        "title" => "Consommables",
        "description" => "Section de gestion des consommables",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){
    
    $usable = 1;
    $managerTemplate = new ConsumableManager();
    $total = $managerTemplate->countAll();

    ob_start(); ?>
        <div class="d-flex flex-row justify-content-between align-items-end">
            <button type="button" class="me-2 btn btn-back-secondary" data-bs-toggle="modal" data-bs-target="#modalAddConsumable">Ajouter un consommable</button>
            <div class="form-check form-switch">
                <input onchange="refreshUsable(this);" class="form-check-input back-main-d-1 border-main-d-1" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
                <label class="form-check-label" for="flexSwitchCheckChecked">Afficher seulement les consommables compatibles avec le JDR</label>
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
            data-url="index.php?c=consumable&a=getAll&usable=<?=$usable?>"
            >

            <thead>
                <tr>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="edit"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="bookmark"></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="id">ID</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="uniqid"></th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-filter-control="input" data-field="type">Catégorie</th>
                    <th data-sortable="false" data-visible="true" data-field="path_img"><i class="fas fa-image"></i></th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-filter-control="input" data-field="name">Nom</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-filter-control="select" data-field="level"><span class="text-level">Niveau</span></th>
                    <th data-sortable="true" data-visible="false" data-filter-control="input" data-field="description">Description</th>
                    <th data-sortable="true" data-visible="false" data-filter-control="input" data-field="effect">Effet</th>
                    <th data-sortable="true" data-visible="false" data-filter-control="input" data-field="recepe">Recette</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="rarity"><span class="text-grey-d-2" data-bs-toggle='tooltip' data-bs-placement='bottom' title="Rareté du consommable">Rareté</span></th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="price"><span class="text-kamas" data-bs-toggle='tooltip' data-bs-placement='bottom' title="Prix estimé du consommable"><img class='icon' src='medias/icons/kamas.png'></span></th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_add">Date de création</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_updated">Date de mise à jour</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="usable"><span data-bs-toggle='tooltip' data-bs-placement='top' title="L'objet est adapté au jdr"><i class='fas fa-check text-green-d-3'></i> JDR</span></th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
        <p class="mt-2"><i class="fas fa-info-circle"></i> Il y a <?=$total?> consommables. Le chargement du tableau peut prendre quelques minutes.</p>

        <!-- Modal ADD -->
        <div class="modal fade" id="modalAddConsumable" tabindex="-1" aria-labelledby="modalAddConsumable" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Création d'un consommable</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-2">
                            <select class="form-select" id="type" aria-label="Floating label select example">
                                <?php foreach (Consumable::TYPE_LIST as $key => $type) {
                                        echo "<option value='".$type."'>".ucfirst($key)."</option>";      
                                    } ?>
                            </select>
                            <label for="floatingSelect">Catérogie du consommable</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" placeholder="Nom du consommable">
                            <label for="floatingInput">Nom du consommable</label>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer d-flex flex-row justify-content-between">
                        <button type="button" class="<?=View::getCss(View::TYPE_BTN_BORDER, "grey", false)?>" data-bs-dismiss="modal">Close</button>
                        <button type="button" onclick="Consumable.add();" class="btn btn-border-secondary">Créer</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            var total = <?=$total?>;

            $('#table').bootstrapTable({
                onDblClickRow: function (row, $element, field) {
                    Consumable.open(row.uniqid);
                    $('#table').bootstrapTable('collapseAllRows');
                },
                exportTypes: ['pdf','excel','xlsx','doc','png','csv','xml','json','sql','txt']
            });
            $('#table tbody').on('click', function () {
                if($(document.activeElement).attr('class').includes("bootstrap-table-filter-control-")){
                    $(document.activeElement).blur();
                }
            });

            function refreshUsable(input) {
                var usable = 0;
                if ($(input).is(":checked")) {
                    usable  = 1;
                }
                $('#table').bootstrapTable('refreshOptions', {
                    url: 'index.php?c=consumable&a=getAll&usable='+usable
                });
            }

        </script>
    <?php $template["content"] = ob_get_clean();

}