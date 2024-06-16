<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Spell listing
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
        "title" => "Liste des sorts",
        "description" => "Section de gestion des sorts",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => false,
        "shownListAddInPage" => true,
        "refStockDataOption" => "" // référence des données de l'option dans la page
    );
    
if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    $properties = [
        'resume' => [
            'name' => 'Résumé',
            "filter" => [
                'check' => true
            ]
        ],
        "name" => [
            'name' => 'Nom',
            "filter" => [
                'check' => true
            ],
            "sort" => [
                'major' => true,
                'isNumeric' => false
            ]
        ],
        'description' => [
            'name' => 'Description',
            "filter" => [
                'check' => true
            ]
        ],
        'effect' => [
            'name' => 'Effet',
            "filter" => [
                'check' => true
            ]
        ],
        'level' => [
            'name' => 'Niveau',
            "filter" => [
                'check' => true
            ],
            "sort" => [
                'major' => true,
                'isNumeric' => true
            ]
        ],
        'po' => [
            'name' => 'PO',
            "filter" => [
                'check' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => true
            ]
        ],
        'po_editable' => [
            'name' => 'PO éditable',
            "filter" => [
                'check' => true
            ]
        ],
        'pa' => [
            'name' => 'PA',
            "filter" => [
                'check' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => true
            ]
        ],
        'cast_per_turn' => [
            'name' => 'Lancer par tour',
            "filter" => [
                'check' => true
            ]
        ],
        'cast_per_target' => [
            'name' => 'Lancer par cible',
            "filter" => [
                'check' => true
            ]
        ],
        'sight_line' => [
            'name' => 'Ligne de vue',
            "filter" => [
                'check' => true
            ]
        ],
        'number_between_two_cast' => [
            'name' => 'Nombre tours entre deux lancers',
            "filter" => [
                'check' => true
            ]
        ],
        'element' => [
            'name' => 'Élément',
            "filter" => [
                'check' => true
            ],
            "sort" => [
                'major' => true,
                'isNumeric' => true
            ]
        ],
        'category' => [
            'name' => 'Catégorie',
            "filter" => [
                'check' => true
            ],
            "sort" => [
                'major' => true,
                'isNumeric' => true
            ]
        ],
        'type' => [
            'name' => 'Type',
            "filter" => [
                'check' => true
            ],
            "sort" => [
                'major' => true,
                'isNumeric' => true
            ]
        ],
        'invocation' => [
            'name' => 'Invocation',
            "filter" => [
                'check' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => false
            ]
        ],
        'is_magic' => [
            'name' => 'Physique / Magique',
            "filter" => [
                'check' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => false
            ]
        ],
        'powerful' => [
            'name' => 'Puissance',
            "filter" => [
                'check' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => true
            ]
        ],
        'usable' => [
            'name' => 'Utilisable',
            "filter" => [
                'check' => false
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => false
            ]
        ],
        'id' => [
            'name' => 'id',
            "filter" => [
                'check' => false
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => true
            ]
        ],
        'uniqid' => [
            'name' => 'uniqid',
            "filter" => [
                'check' => false
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => false
            ]
        ]
    ];

    ob_start(); ?>
        <div class="view-container">

            <!----------- TOP ----------->
            <div class="view-container__top">
                <div class="view-container__top__settings">
                    <!-- SEARCH -->
                        <input type="search" class="view-container__top__settings__search" id="search" title="Rechercher des sorts en fonction de leurs noms, leurs, descriptions, leurs effets  etc..." placeholder="Rechercher un sort">
                    <!-- CHOIX DE LA VUE -->
                        <select id="view-choice" class="view-container__top__settings__view-choice" title="Sélectionner une vue">
                            <option value="<?=ControllerView::VIEW_TABLE?>">Tableau</option>
                            <option value="<?=ControllerView::VIEW_MINIMAL_CARD?>">Cartes simplifiées</option>
                            <option value="<?=ControllerView::VIEW_DETAILED_CARD?>">Cartes détaillées</option>
                        </select>
                    <!-- Is Usable -->
                    <div class="view-container__top__settings__usable-box form-check form-switch">
                        <input class="view-container__top__settings__usable-box__checkbox form-check-input back-main-d-1 border-main-d-1" type="checkbox" role="switch" id="toggleUsableSwitch" checked>
                        <label class="view-container__top__settings__usable-box__label" for="toggleUsableSwitch">Afficher seulement les sorts<br>compatibles avec le JDR</label>
                    </div>
                    <!-- SORT -->
                        <div class="view-container__top__settings__sort-box">
                            <?php foreach ($properties as $property => $val) { 
                                if(isset($val['sort']['major'])){
                                    if($val{'sort'}['major']){ ?>
                                        <button class="view-container__top__settings__sort-box__btn" type="button"  data-order="1"><?=$val['name']?></button>
                                <?php }
                                }
                            } ?>
                        </div>
                    <!-- FILTRES BTN-->
                        <div class="view-container__top_settings__filter-box">
                            <button class="dropdown-toggle view-container__top_settings__filter-box__btn" type="button" data-type="drawer" data-target=".dropdown-menu dropdown-toggle view-container__top_settings__filter-box__menu" data-expanded="false">Filtre</button>
                            <div class="view-container__top__settings__filter-box__menu" data-expanded="false">
                            <?php foreach ($properties as $property => $val) { 
                                if(isset($val['filter'])){?>
                                    <div class="view-container__top_settings__filter-box__menu__item" data-property="<?=$property?>">
                                        <label for="filter-<?=$property?>" class="view-container__top_settings__filter-box__menu__item__label"><?=$val['name']?></label>
                                        <input id="filter-<?=$property?>" <?=$val['filter']['check'] ? "checked" : ""?> type="checkbox" class="view-container__top_settings__filter-box__menu__item__checkbox">
                                    </div>
                                <?php }
                            } ?>
                        </div>
                    <!-- BTN SHOW FILTRES AVANCÉS -->
                        <button class="view-container__top__settings__advanced-settings-btn" type="button">Filtre avancé</button>
                    </div>
                </div>

                <div class="view-container__select-interaction">
                    <button class="view-container__top__select-interaction__edit" disabled title="Modifier l'objet sélectionné"><i class='fa-regular fa-edit'></i> Éditer</button>
                    <button class="view-container__top__select-interaction__get-pdf" disabled title="Générer un pdf contenant les objets sélectionnés"><i class='fa-solid fa-file-pdf'></i> Générer un PDF</button>
                    <button class="view-container__top__select-interaction__bookmark-add" disabled title="Ajouter au grimpore les objets sélectionnés"><i class='fa-solid fa-bookmark'></i></button>
                    <button class="view-container__top__select-interaction__bookmard-remove" disabled title="Supprimer du grimoire les objets sélectionnés"><i class='fa-regular fa-bookmark'></i></button>
                    <button class="view-container__top__select-interaction__usable-true" disabled title="Marquer les objets sélectionnés comme adaptés au JDR"><i class='fa-solid fa-check'></i></button>
                    <button class="view-container__top__select-interaction__usable-false" disabled title="Marquer les objets sélectionnés comme non adaptés au JDR"><i class='fa-solid fa-check'></i></button>
                    <button class="view-container__top__select-interaction__remove" disabled title="Supprimer les objets sélectionnés"><i class='fa-solid fa-trash'></i></button>
                </div>
            </div>

            <!----------- DISPLAY ERROR ----------->
            <div class="view-container__error-box">
                <h4 class='view-container__error-box__title'></h4>
                <p class='view-container__error-box__description'></p>
            </div>

            <!----------- BOTTOM ----------->
            <div class="view-container__bottom">
                <div class="view-container__bottom__progress">
                    <div class="view-container__bottom__progress__bar"></div>
                    <div class="view-container__bottom__progress__text"></div>
                </div>
                <div class="view-container__bottom__list">
                    <div class="view_container_bottom__list__table-view">
                        <table class="table view_container_bottom__list__table-view__table">
                            <thead>
                                <tr>
                                    <th class="text-center" data-sortable="false" data-visible="true" data-field="edit"></th>
                                    <th class="text-center" data-sortable="false" data-visible="true" data-field="pdf"></th>
                                    <th class="text-center" data-sortable="true" data-visible="false" data-field="id">ID</th>
                                    <th class="text-center" data-sortable="false" data-visible="false" data-field="uniqid"></th>
                                    <th class="text-center" data-sortable="false" data-visible="true" data-field="bookmark"></th>
                                    <th data-sortable="false" data-visible="true" data-field="path_img"><i class="fa-solid fa-image"></i></th>
                                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="name_bold">Nom</th>
                                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="resume"></th>
                                    <th class="text-center" data-sortable="true" data-visible="false" data-field="is_magic"><i class="fa-solid fa-fist-raised text-brown-d-2"></i> | <i class="fa-solid fa-magic text-purple-d-2"></i></th>
                                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="category">Catégorie</th>
                                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="element">Elèment</th>
                                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="powerful">Puissance</th>
                                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="type">Type</th>
                                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="level"><span class="text-level">Niveau</span></th>
                                    <th class="text-center" data-sortable="false" data-visible="true" data-field="pa"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Coût en points d'action du sort"><img class='icon' src='medias/icons/modules/pa.png'></span></th>
                                    <th class="text-center" data-sortable="false" data-visible="true" data-field="po"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Portée du sort"><img class='icon' src='medias/icons/modules/po.png'></span></th>
                                    <th class="text-center" data-sortable="false" data-visible="true" data-field="cast_per_turn"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de fois que le sort peut-être lancer par tour"><img class='icon' src='medias/icons/modules/cast_per_turn.png'></span></th>
                                    <th class="text-center" data-sortable="false" data-visible="true" data-field="cast_per_target"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de fois que le sort peut-être lancer par tour sur une même cible"><img class='icon' src='medias/icons/modules/cast_per_target.png'></span></th>
                                    <th class="text-center" data-sortable="false" data-visible="true" data-field="po_editable"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Sort au corps à corps, à distance avec une portée modifiable ou non"><img class='icon' src='medias/icons/modules/po_editable.png'></span></th>
                                    <th class="text-center" data-sortable="false" data-visible="true" data-field="sight_line"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Besoin de la ligne de vue pour lancer le sort"><i class='fa-solid fa-eye-slash text-sight-line'></i></span></th>
                                    <th class="text-center" data-sortable="false" data-visible="true" data-field="number_between_two_cast"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de tour entre deux lancer de sort"><img class='icon' src='medias/icons/modules/number_between_two_cast.png'></span></th>
                                    <th class="text-left" data-sortable="false" data-visible="true" data-field="invocation"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Créature attachée au sort">Invocation(s) attachéer(s) au sort</th>
                                    <th data-sortable="false" data-visible="false"  data-field="description">Description</th>
                                    <th data-sortable="false" data-visible="false"  data-field="effect">Effets</th>
                                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_add">Date de création</th>
                                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_updated">Date de mise à jour</th>
                                    <th class="text-center" data-sortable="true" data-visible="true" data-field="usable"><span data-bs-toggle='tooltip' data-bs-placement='top' title="L'objet est adapté au jdr"><i class='fa-solid fa-check text-green-d-3'></i> JDR</span></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="view_container_bottom__list__detailled-card-view"></div>
                    <div class="view_container_bottom__list__minimal-card-view"></div>

                </div>

                <div class="view-container__bottom__advanced-settings">
                    <!-- TRIES -->
                        <div class="view-container__bottom__advanced-settings__sort-box">
                            <button class="view-container__bottom__advanced-settings__sort-box__btn" type="button" data-type="collapse" data-target=".view-container__bottom__advanced-settings__sort-box__menu" data-expanded="false">Trier par</button>
                            <div class="view-container__bottom__advanced-settings__sort-box__menu" data-expanded="false">
                                <?php foreach ($properties as $property => $val) { 
                                    if(isset($val['sort'])){ ?>
                                        <div class="view-container__bottom__advanced-settings__sort-box__menu__item" data-property="<?=$property?>" >
                                            <input autocomplete="off" type="number" min="1" max="99" step="1" class="view-container__bottom__advanced-settings__sort-box__menu__item__priority" title="Ordre de priorité du tri. Non prit en compte si vide" placeholder="1">
                                            <button class="view-container__bottom__advanced-settings__sort-box__menu__item__sort-btn" title="Trier par <?=$val['name']?>"><?=ucfirst($val['name'])?></button>
                                            <button class="view-container__bottom__advanced-settings__sort-box__menu__item__order-btn" data-order="" data-isnumeric="<?=$val['sort']['isNumeric']?>" data-type="sort-order" title="Trier par ordre croissant"><i class="fa-solid fa-arrow-up-<?=$val['sort']['isNumeric'] ? "1-9" : "a-z"?>"></i></button>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        </div>
                    <!-- FILTRES -->
                        <div class="view-container__bottom__advanced-settings__filter-box">
                            <button class="view-container__bottom__advanced-settings__filter-box__btn" type="button" data-type="collapse" data-target=".view-container__bottom__advanced-settings__filter-box__menu" data-expanded="false">Filtre avancé</button>
                            <p>
                                Ce système de filtre avancé permet de rechercher certains des sorts selon leurs caractéristiques.
                                <ul>
                                    <li>Si vous cliquez sur la caractéristique et que les champs sont vides, alors seront filtrés seulement les sorts qui sont cette caractéristique non nulle</li>
                                    <li>Si vous cliquez sur la caractéristique avec une valeur atrribué à un seul champ, alors seront filtrés seulement les sorts qui sont cette caractéristique supérieur ou égale à cette valeur</li>
                                    <li>Si vous cliquez sur la caractéristique et que les deux champs ont une valeur, alors seront filtrés les sorts dont la caractéristique est comprise (ou égale) entre ces deux valeurs.</li>
                                </ul>
                            </p>
                            <div class="view-container__bottom__advanced-settings__filter-box__menu" data-expanded="false">
                                <?php foreach ($properties as $property => $val) { 
                                    if(isset($val['filter'])){?>
                                        <div class="view-container__bottom__advanced-settings__filter-box__menu__item" data-property="<?=$property?>">
                                            <button class="view-container__bottom__advanced-settings__filter-box__menu_item_btn" type="button"><?=$val['name']?></button>
                                            <div>
                                                <label for="advancedfilter-min-<?=$property?>" class="view-container__bottom__advanced-settings__filter-box__menu__item__label-min">Minimum</label>
                                                <input id="advancedfilter-min-<?=$property?>" type="number" class="view-container__bottom__advanced-settings__filter-box__menu__item__input-min">
                                            </div>
                                            <div>
                                                <label for="advancedfilter-max-<?=$property?>" class="view-container__bottom__advanced-settings__filter-box__menu__item__label-max">Maximum</label>
                                                <input id="advancedfilter-max-<?=$property?>" type="number" class="view-container__bottom__advanced-settings__filter-box__menu__item__input-max">
                                            </div>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        </div>
                </div>
                
                <div class="view-container__preview">
                
                </div>
            </div>

        </div>
        <script>
            // Récupération des données
            let view = new View(
                fetchUrlCount = "index.php?c=spell&a=count",
                fetchUrlObj = "index.php?c=spell&a=getAll",
                fetchUrlTemplate = "index.php?c=view&a=getTemplate&obj_type=spell",
                limit = 100,
                offset = 0,
            );


            // Initialisation de la vue
            View.toogleSortOrderBtn();
        </script>

    <?php $template["content"] = ob_get_clean();
}