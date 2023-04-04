<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Mob
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
        "title" => "Bestiaire",
        "description" => "Section de gestion des créatures.",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    $usable = 1;
    $manager = new MobManager();
    $total = $manager->countAll();

    ob_start(); ?>
        <div class="d-flex flex-row justify-content-between align-items-end">
            <button type="button" class="me-2 btn btn-sm btn-back-secondary" data-bs-toggle="modal" data-bs-target="#modalAddMob">Ajouter une créature</button>
            <div class="form-check form-switch">
                <input onchange="refreshUsable(this);" class="form-check-input back-main-d-1 border-main-d-1" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
                <label class="form-check-label" for="flexSwitchCheckChecked">Afficher seulement les créatures compatibles avec le JDR</label>
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
            data-url="index.php?c=mob&a=getAll&usable=<?=$usable?>"
            >
            
            <thead>
                <tr>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="edit"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="bookmark"></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="id">ID</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="uniqid"></th>
                    <th data-sortable="false" data-visible="true" data-field="path_img"><i class="fas fa-image"></i></th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-filter-control="input" data-field="name">Nom</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-filter-control="input" data-field="level"><span class="text-level">Niveau</span></th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="powerful"><span class="text-deep-purple-d-3">Puissance</span></th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="life"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Points de vie"><i class='fab fa-gratipay text-life'></i></span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="resume"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="resumeattack"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="resumedefend"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="resumeres"></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="pa"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de point d'action"><img class='icon' src='medias/icons/pa.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="pm"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de point de mouvement"><img class='icon' src='medias/icons/pm.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="po"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de portée"><img class='icon' src='medias/icons/po.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="ini"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus d'Initiative"><img class='icon' src='medias/icons/ini.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="touch"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de touche"><img class='icon' src='medias/icons/touch.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="hostility"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Agressivité"><i class='fas fa-tired text-main-d-2'></i></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="vitality"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur de Vitalité"><img class='icon' src='medias/icons/vitality.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="sagesse"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur de Sagesse"><img class='icon' src='medias/icons/sagesse.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="strong"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur de Force"><img class='icon' src='medias/icons/force.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="intel"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur d'Intelligence"><img class='icon' src='medias/icons/intel.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="agi"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur d'Agilité"><img class='icon' src='medias/icons/agi.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="chance"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modificateur de Chance"><img class='icon' src='medias/icons/chance.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="ca"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de classe d'armure"><img class='icon' src='medias/icons/ca.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="fuite"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de fuite"><img class='icon' src='medias/icons/fuite.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="tacle"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus de tacle"><img class='icon' src='medias/icons/tacle.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="dodge_pa"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus d'Esquive PA"><img class='icon' src='medias/icons/dodge_pa.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="dodge_pm"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Bonus d'Esquive PM"><img class='icon' src='medias/icons/dodge_pm.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="res_neutre"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance neutre"><img class='icon' src='medias/icons/res_neutre.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="res_terre"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance terre"><img class='icon' src='medias/icons/res_terre.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="res_feu"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance feu"><img class='icon' src='medias/icons/res_feu.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="res_air"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance air"><img class='icon' src='medias/icons/res_air.png'></span></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="res_eau"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Résistance eau"><img class='icon' src='medias/icons/res_eau.png'></span></th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="spell"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Sorts"><i class='fas fa-magic text-main-d-1'></i></span></th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="trait"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Traits">Traits</span></th>
                    <th data-sortable="false" data-visible="false" data-filter-control="input" data-field="description">Description</th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-filter-control="input" data-field="zone"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Zone de vie"><i class='fas fa-map-marker-alt text-main-d-2'></i></span></th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_add">Date de création</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_updated">Date de mise à jour</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="usable"><span data-bs-toggle='tooltip' data-bs-placement='top' title="L'objet est adapté au jdr"><i class='fas fa-check text-green-d-3'></i> JDR</span></th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
        <p class="mt-2"><i class="fas fa-info-circle"></i> Il y a <?=$total?> créatures. Le chargement du tableau peut prendre quelques minutes.</p>

        <!-- Modal ADD -->
        <div class="modal fade" id="modalAddMob" tabindex="-1" aria-labelledby="modalAddMob" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">Création d'une créature</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control form-control-main-focus" id="name" placeholder="nom">
                            <label for="name">Nom de la créature</label>
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
                        <div class="my-2">
                            <label for="powerful" class="form-label badge back-deep-purple-d-3">Puissance <span id="powerful_value">4</span></label>
                            <input onchange="$('#powerful_value').text($(this).val());" type="range" class="form-range" min="1" max="7" step="1" value="4" id="powerful">
                            <p><small>Sur une échelle de 7 valeurs, avec 1 étant une créature extrémement faible et 7 une créature extrément forte.</small></p>
                        </div>
                        <div class="my-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="intel">
                                <label class="form-check-label" for="intel">Intelligence <img class='icon-sm' src='medias/icons/intel.png'></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="strong">
                                <label class="form-check-label" for="strong">Force <img class='icon-sm' src='medias/icons/force.png'></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="chance">
                                <label class="form-check-label" for="chance">Chance <img class='icon-sm' src='medias/icons/chance.png'></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="agi">
                                <label class="form-check-label" for="agi">Agilité <img class='icon-sm' src='medias/icons/agi.png'></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="sagesse">
                                <label class="form-check-label" for="sagesse">Sagesse <img class='icon-sm' src='medias/icons/sagesse.png'></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="vitality">
                                <label class="form-check-label" for="vitality">Vitalité <img class='icon-sm' src='medias/icons/vitality.png'></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer d-flex flex-row justify-content-between">
                        <button type="button" class="btn-border-grey" data-bs-dismiss="modal">Close</button>
                        <button type="button" onclick="Mob.add();" class="btn btn-border-secondary">Créer</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            var total = <?=$total?>;

            $('#table').bootstrapTable({
                onDblClickRow: function (row, $element, field) {
                    Mob.open(row.uniqid);
                    $('#table').bootstrapTable('collapseAllRows');
                },
                exportTypes: ["pdf","doc","xlsx","xls","xml", "json", "png", "sql", "txt", "tsv"]
            });

            $('#table tbody').on('click', function (e) {
                if($(e.target).attr('class').includes("bootstrap-table-filter-control-")){
                    $(e.target).blur();
                }
            });

            function refreshUsable(input) {
                var usable = 0;
                if ($(input).is(":checked")) {
                    usable  = 1;
                }
                $('#table').bootstrapTable('refreshOptions', {
                    url: 'index.php?c=mob&a=getAll&usable='+usable
                });
            }

        </script>
    <?php $template["content"] = ob_get_clean();

}