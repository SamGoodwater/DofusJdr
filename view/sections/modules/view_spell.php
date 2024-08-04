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
        'resume1' => [
            'name' => 'Résumé',
            "filter" => [
                'table' => true,
                'minimal_card' => false,
                'detailed_card' => false
            ],
            "search" => false
        ],
        'resume2' => [
            'name' => 'Résumé²',
            "filter" => [
                'table' => true,
                'minimal_card' => false,
                'detailed_card' => false
            ],
            "search" => false
        ],
        "name" => [
            'name' => 'Nom',
            "filter" => [
                'table' => true,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => true,
                'isNumeric' => false
            ],
            "search" => true
        ],
        'description' => [
            'name' => 'Description',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "search" => true
        ],
        'effect' => [
            'name' => 'Effet',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "search" => true
        ],
        'level' => [
            'name' => 'Niveau',
            "filter" => [
                'table' => true,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => true,
                'isNumeric' => true,
                'default' => 1
            ],
            "search" => false
        ],
        'po' => [
            'name' => 'PO',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => true
            ],
            "search" => false
        ],
        'po_editable' => [
            'name' => 'PO éditable',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "search" => false
        ],
        'pa' => [
            'name' => 'PA',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => true
            ],
            "search" => false
        ],
        'cast_per_turn' => [
            'name' => 'Lancer par tour',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "search" => false
        ],
        'cast_per_target' => [
            'name' => 'Lancer par cible',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "search" => false
        ],
        'sight_line' => [
            'name' => 'Ligne de vue',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "search" => false
        ],
        'number_between_two_cast' => [
            'name' => 'Nombre tours entre deux lancers',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "search" => false
        ],
        'element' => [
            'name' => 'Élément',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => true,
                'isNumeric' => true
            ],
            "search" => false
        ],
        'category' => [
            'name' => 'Catégorie',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => true,
                'isNumeric' => true
            ],
            "search" => false
        ],
        'type' => [
            'name' => 'Type',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => true,
                'isNumeric' => true
            ],
            "search" => false
        ],
        'invocation' => [
            'name' => 'Invocation',
            "filter" => [
                'table' => true,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => false
            ],
            "search" => false
        ],
        'is_magic' => [
            'name' => 'Physique / Magique',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => false
            ],
            "search" => false
        ],
        'powerful' => [
            'name' => 'Puissance',
            "filter" => [
                'table' => false,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => true
            ],
            "search" => false
        ],
        'usable' => [
            'name' => 'Utilisable',
            "filter" => [
                'table' => true,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => false
            ],
            "search" => false
        ],
        'id' => [
            'name' => 'id',
            "filter" => [
                'table' => false,
                'minimal_card' => false,
                'detailed_card' => false
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => true
            ],
            "search" => false
        ],
        'uniqid' => [
            'name' => 'uniqid',
            "filter" => [
                'table' => false,
                'minimal_card' => false,
                'detailed_card' => false
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => false
            ],
            "search" => false
        ],
        'timestamp_add' => [
            'name' => 'date de création',
            "filter" => [
                'table' => false,
                'minimal_card' => false,
                'detailed_card' => false
            ],
            "search" => false
        ],
        'timestamp_updated' => [
            'name' => 'date de mise à jour',
            "filter" => [
                'table' => false,
                'minimal_card' => false,
                'detailed_card' => false
            ],
            "search" => false
        ],
        'frequency' => [
            'name' => 'Fréquence de lancer',
            "filter" => [
                'table' => false,
                'minimal_card' => false,
                'detailed_card' => false
            ],
            "sort" => [
                'major' => false,
                'isNumeric' => false
            ],
            "search" => false
        ],
        'logo' => [
            'name' => 'Fréquence de lancer',
            "filter" => [
                'table' => true,
                'minimal_card' => true,
                'detailed_card' => true
            ],
            "search" => false
        ],
        'bookmark' => [
            'name' => 'Affichage dans le grimoire',
            "filter" => [
                                'table' => false,
                'minimal_card' => false,
                'detailed_card' => false
            ],
            "search" => false
        ],
        'edit' => [
            'name' => 'Editer l\'objet',
            "filter" => [
                'table' => false,
                'minimal_card' => false,
                'detailed_card' => false
            ],
            "search" => false
        ],
        'pdf' => [
            'name' => 'Créer un pdf',
            "filter" => [
                'table' => false,
                'minimal_card' => false,
                'detailed_card' => false
            ],
            "search" => false
        ],

    ];

    ob_start(); ?>
        <div class="view-container">

            <!----------- TOP ----------->
            <div class="view-container__top">
                <div class="view-container__top__settings">
                    <!-- SEARCH -->
                     <div class="view-container__top__settings__search-container">
                        <input type="search" class="view-container__top__settings__search-container__input" id="search" title="Rechercher des sorts en fonction de leurs noms, leurs, descriptions, leurs effets  etc..." placeholder="Rechercher un sort">
                        <div class="view-container__top__settings__search-container__search-box">
                            <button class="view-container__top__settings__search-container__search-box__btn" type="button" title="Modifier les propriétés prises en compte pour la recherche"><i class="fa-solid fa-magnifying-glass-arrow-right"></i></button>
                            <div class="view-container__top__settings__search-container__search-box__menu">
                                <div class="view-container__top__settings__search-container__search-box__menu__header">
                                    <h4 class="view-container__top__settings__search-container__search-box__menu__header__title">Cocher les propriétés prise en compte pour la recherche</h4>
                                    <button class="view-container__top__settings__search-container__search-box__menu__header__close" type="button" aria-label='Fermer le menu de filtre'><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="view-container__top__settings__search-container__search-box__menu__container">
                                    <?php foreach ($properties as $property => $val) { 
                                        if(isset($val['search'])){?>
                                            <div class="view-container__top__settings__search-container__search-box__menu__container__item">
                                                <input data-checked="<?=$val['search'] ? "true" : "false"?>" type="checkbox" id="search-<?=$property?>" <?=$val['search'] ? "checked" : ""?> data-property="<?=$property?>" autocomplete="off" class="btn-check view-container__top__settings__search-container__search-box__menu__container__item__checkbox">
                                                <label data-property="<?=$property?>" for="search-<?=$property?>" class="btn btn-sm btn-outline-secondary view-container__top__settings__search-container__search-box__menu__container__item__label"><?=$val['name']?></label>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                     </div>
                    <!-- CHOIX DE LA VUE -->
                        <select id="view-choice" class="view-container__top__settings__view-choice" title="En cours de développement" disabled>
                            <option value="<?=ControllerView::VIEW_TABLE?>">Tableau</option>
                            <option value="<?=ControllerView::VIEW_MINIMAL_CARD?>">Cartes simplifiées</option>
                            <option value="<?=ControllerView::VIEW_DETAILED_CARD?>">Cartes détaillées</option>
                        </select>
                        <button class="view-container__top__settings__btn-refresh" type="button"><i class="fa-solid fa-arrows-rotate"></i></button>
                    <!-- Is Usable -->
                        <div class="view-container__top__settings__usable-box form-check form-switch">
                            <input class="view-container__top__settings__usable-box__checkbox form-check-input back-main-d-1 border-main-d-1" type="checkbox" role="switch" id="toggleUsableSwitch" checked>
                            <label class="view-container__top__settings__usable-box__label" title="Afficher seulement les sorts<br>compatibles avec le JDR" for="toggleUsableSwitch">Afficher seulement les sorts<br>compatibles avec le JDR</label>
                        </div>
                    <!-- SORT -->
                        <div data-type='dropdown' data-trigger-target=".view-container__top__settings__sort-box__trigger" data-expanded="false" class="view-container__top__settings__sort-box" >
                            <button class="view-container__top__settings__sort-box__trigger"><span>Trier par </span><i class="fa-solid fa-chevron-down"></i></button>
                            <div class="view-container__top__settings__sort-box__menu">
                                <?php foreach ($properties as $property => $val) { 
                                    if(isset($val['sort']['major'])){
                                        if($val{'sort'}['major']){ 
                                            $order = 0;
                                            if(isset($val['sort']['default'])){ 
                                                if($val['sort']['default'] == 1 || $val['sort']['default'] == -1 ){ $order = $val['sort']['default']; }
                                             }?>
                                            <button class="view-container__top__settings__sort-box__menu__option" type="button" data-property="<?=$property?>"  data-order="<?=$order?>"><?=$val['name']?></button>
                                    <?php }
                                    }
                                } ?>
                            </div>
                        </div>
                    <!-- FILTRES BTN-->
                        <div class="view-container__top__settings__filter-box">
                            <button class="view-container__top__settings__filter-box__btn" type="button">Filtre</button>
                            <div class="view-container__top__settings__filter-box__menu">
                                <div class="view-container__top__settings__filter-box__menu__header">
                                    <h4 class="view-container__top__settings__filter-box__menu__header__title">Cocher les propriétés à afficher</h4>
                                    <button class="view-container__top__settings__filter-box__menu__header__close" type="button" aria-label='Fermer le menu de filtre'><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="view-container__top__settings__filter-box__menu__container">
                                    <?php foreach ($properties as $property => $val) { 
                                        if(isset($val['filter'])){?>
                                            <div class="view-container__top__settings__filter-box__menu__container__item">
                                                <input id="filter-<?=$property?>" <?=$val['filter']['minimal_card'] ? "checked" : ""?> data-property="<?=$property?>" type="checkbox" class="btn-check view-container__top__settings__filter-box__menu__container__item__checkbox">
                                                <label for="filter-<?=$property?>" data-property="<?=$property?>" class="btn btn-sm btn-outline-secondary view-container__top__settings__filter-box__menu__container__item__label"><?=$val['name']?></label>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    <!-- BTN SHOW FILTRES AVANCÉS -->
                        <button class="view-container__top__settings__advanced-settings-btn" type="button" disabled title="En cours de développement">Filtre avancé</button>
                </div>

                <div class="view-container__top__select-interaction">
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

                <div class="view-container__bottom__list">
                    <div class="view-container__bottom__list__progress">
                        <div class="view-container__bottom__list__progress__bar progress" role="progressbar" aria-label="Chargenement des données" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <div class="view-container__bottom__list__progress__label progress-bar progress-bar-striped progress-bar-animated" style="width: 75%"></div>
                        </div>
                    </div>

                    <div class="view-container__bottom__list__table-view">
                        <table class="view-container__bottom__list__table-view__table">
                            <thead>
                                <tr>
                                    <th data-idcol='2' data-format="base" data-property="id">ID</th>
                                    <th data-idcol='3' data-format="base" data-property="uniqid"></th>
                                    <th data-idcol='5' data-format="other" data-property="logo"><i class="fa-solid fa-image"></i></th>
                                    <th data-idcol='6' data-format="text" data-property="name">Nom</th>
                                    <th data-idcol='14' data-format="base" data-property="level">Niveau</th>
                                    <th data-idcol='7' data-format="other" data-property="resume1"></th>
                                    <th data-idcol='8' data-format="other" data-property="resume2"></th>
                                    <th data-idcol='9' data-format="icon" data-property="is_magic"></th>
                                    <th data-idcol='10' data-format="badge" data-property="category">Catégorie</th>
                                    <th data-idcol='11' data-format="badge" data-property="element">Elèment</th>
                                    <th data-idcol='12' data-format="icon" data-property="powerful">Puissance</th>
                                    <th data-idcol='13' data-format="badge" data-property="type">Type</th>
                                    <th data-idcol='15' data-format="icon" data-property="pa"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Coût en points d'action du sort"><img class='icon' src='medias/icons/modules/pa.png'></span></th>
                                    <th data-idcol='16' data-format="icon" data-property="po"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Portée du sort"><img class='icon' src='medias/icons/modules/po.png'></span></th>
                                    <th data-idcol='17' data-format="icon" data-property="cast_per_turn"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de fois que le sort peut-être lancer par tour"><img class='icon' src='medias/icons/modules/cast_per_turn.png'></span></th>
                                    <th data-idcol='18' data-format="icon" data-property="cast_per_target"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de fois que le sort peut-être lancer par tour sur une même cible"><img class='icon' src='medias/icons/modules/cast_per_target.png'></span></th>
                                    <th data-idcol='19' data-format="icon" data-property="po_editable"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Sort au corps à corps, à distance avec une portée modifiable ou non"><img class='icon' src='medias/icons/modules/po_editable.png'></span></th>
                                    <th data-idcol='20' data-format="icon" data-property="sight_line"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Besoin de la ligne de vue pour lancer le sort"><i class='fa-solid fa-eye-slash text-sight-line'></i></span></th>
                                    <th data-idcol='21' data-format="icon" data-property="number_between_two_cast"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Nombre de tour entre deux lancer de sort"><img class='icon' src='medias/icons/modules/number_between_two_cast.png'></span></th>
                                    <th data-idcol='22' data-format="text" data-property="invocation"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Créature attachée au sort">Invocation(s) attachéer(s) au sort</th>
                                    <th data-idcol='23' data-format="base" data-property="description">Description</th>
                                    <th data-idcol='24' data-format="base" data-property="effect">Effets</th>
                                    <th data-idcol='25' data-format="base" data-property="timestamp_add">Date de création</th>
                                    <th data-idcol='26' data-format="base" data-property="timestamp_updated">Date de mise à jour</th>
                                    <th data-idcol='27' data-format="icon" data-property="usable"><span data-bs-toggle='tooltip' data-bs-placement='top' title="L'objet est adapté au jdr"><i class='fa-solid fa-check text-green-d-3'></i> JDR</span></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="view-container__bottom__list__detailled-card-view"></div>
                    <div class="view-container__bottom__list__minimal-card-view"></div>

                    <div class="view-container__bottom__list__pagination">
                        <div class="view-container__bottom__list__pagination__controls">
                            <label class="view-container__bottom__list__pagination__controls__label" for="items-per-page">Objets par page:</label>
                            <select class="view-container__bottom__list__pagination__controls__select" id="items-per-page">
                                <option value="10">10</option>
                                <option selected value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="all">Tous</option>
                            </select>
                        </div>
                        
                        <div class="view-container__bottom__list__pagination__number-box">
                            <button class="view-container__bottom__list__pagination__number-box__previous">Précédent</button>
                            <div class="view-container__bottom__list__pagination__number-box__container-pages-btn"></div>
                            <button class="view-container__bottom__list__pagination__number-box__next">Suivant</button>
                        </div>
                    </div>
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

            let properties = <?=json_encode($properties)?>;

            // Récupération des données
            let view = new View(
                classReference = Spell,
                properties = properties
            );


            // Initialisation de la vue
            View.toogleSortOrderBtn();
        </script>

    <?php $template["content"] = ob_get_clean();
}
?>
