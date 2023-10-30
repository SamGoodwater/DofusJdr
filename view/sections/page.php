<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Page
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
        "title" => "Pages",
        "description" => "Section de gestion des pages.",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => false,
        "shownListAddInPage" => false,
        "refStockDataOption" => "" // référence des données de l'option dans la page
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){
    $manager = new PageManager;


    $categories_available = [];
    foreach (Page::CATEGORY as $name_category => $number_category) {
        $categories_available[$number_category] = $name_category;
        foreach ($manager->getAllFromCategory($number_category) as $page){
            $categories_available[$page->getUniqid()] = $page->getName();
        }
    }

    ob_start(); ?>

        <h3  class="mb-3">Ajouter une page</h3>
        <div class="d-flex justify-content-between align-item-baseline addpage">
            <div class="align-self-end"> <!-- Nom -->
                <input 
                    id="name"
                    placeholder="Nom de la page" 
                    maxlength="300" 
                    type="text" 
                    class="form-control form-control-main-focus form-control form-control-main-focus-sm">
            </div>
            <div> <!-- Catégorie -->
                <p><small class="size-0-8 text-grey-d-1">Emplacement dans le menu</small></p>
                <select id="category" class="form-select form-select-sm" aria-label=".form-select-sm">
                    <option value="-1">Ne pas afficher</option>
                    <?php foreach ($categories_available as $number_category => $name_category) {?>
                        <option value="<?=$number_category?>"><?=$name_category?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="text-center"> <!-- Dropdown -->
                <p><small class="size-0-8 text-grey-d-1">Liste déroulante</small></p>
                <div class="form-check form-switch pe-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Si la case est coché, les pages filles seront sous forme de liste déroulante. Attention cette page n'est plus accessible directement depuis le menu.">
                    <input class="form-check-input back-main-d-2 border-main-d-2" type="checkbox" role="switch" id="switchdropdownadd">
                    <label class="form-check-label" for="switchdropdownadd"><i class="fa-solid fa-caret-square-down"></i></label>
                </div> 
            </div>
            <div class="text-center"> <!-- Public -->
                <p><small class="size-0-8 text-grey-d-1">Public</small></p>
                <div checked class="form-check form-switch pe-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Si la case est coché, la page est accessible même sans être connecté via un compte.">
                    <input class="form-check-input back-main-d-2 border-main-d-2" type="checkbox" role="switch" id="switchpublicadd">
                    <label class="form-check-label" for="switchpublicadd"><i class="fa-solid fa-low-vision"></i></label>
                </div>
            </div>
            <div class="text-center"> <!-- Is_editable -->
                <p><small class="size-0-8 text-grey-d-1">Modiable</small></p>
                <div class="form-check form-switch pe-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Si la case est coché, il est possible d'ajouter des sections à la page.">
                    <input checked class="form-check-input back-main-d-2 border-main-d-2" type="checkbox" role="switch" id="switcheditableadd">
                    <label class="form-check-label" for="switcheditableadd"><i class="fa-solid fa-pen-square"></i></label>
                </div>
            </div>
            <p><a class="btn btn-sm btn-animate btn-back-secondary mb-2" onclick="Page.add();">Ajouter la page</a></p>
        </div>

        <div class='item-divider-main'></div>

        <h3>Modifier les pages</h3>

        <table 
            id="table"
            class="table"
            data-bs-toggle="table" 
            data-classes="table-no-bordered" 
            data-remember-order='true'
            data-locale="fr-FR"
            data-show-export="true" 
            data-show-fullscreen="true"
            data-show-columns="true"
            data-pagination="true" 
            data-page-size="Tout" 
            data-page-list="[10, 25, 50, 100, 200, All]"
            data-search="true"
            data-search-accent-neutralise="true"
            data-search-align="left"
            data-search-on-enter-key="true"
            data-sort-reset="true"
        >   
            <thead>
                <tr>
                    <th></th>
                    <th class="text-center" data-sortable="true" data-field="name">Nom</th>
                    <th class="text-center" data-sortable="true" data-field="url_name">URL</th>
                    <th class="text-center" data-sortable="true" data-field="category">Emplacement</th>
                    <th class="text-center" data-sortable="false" data-field="is_dropdown"><p data-bs-toggle="tooltip" data-bs-placement="bottom" title="Si la case est coché, les pages filles seront sous forme de liste déroulante. Attention cette page n'est plus accessible directement depuis le menu."><i class="fa-solid fa-caret-square-down"></i></p></th>
                    <th class="text-center" data-sortable="false" data-field="public"><p data-bs-toggle="tooltip" data-bs-placement="bottom" title="Si la case est coché, la page est accessible même sans être connecté via un compte."><i class="fa-solid fa-low-vision"></i></th>
                    <th class="text-center" data-sortable="false" data-field="is_editable"><p data-bs-toggle="tooltip" data-bs-placement="bottom" title="Si la case est coché, il est possible d'ajouter des sections à la page."><i class="fa-solid fa-pen-square"></i></p></th>
                    <th class="text-center" data-sortable="false" data-field="remove"></th>
                </tr>
            </thead>
            <tbody id="sortable">
                <tr class="row_category_page back-main-d-2">
                    <td colspan="8" class="text-center size-1-2 text-white">Pages non catégorisées</td>
                </tr>
                <?php foreach ($manager->getAllNonCategory() as $page) {
                    $disabled = ""; if(in_array($page->getUniqid(), Page::UNIQID_NO_EDIT)){$disabled = "disabled";}
                    $checked_public="";if($page->getPublic()){$checked_public="checked";}
                    $checked_editable="";if($page->getIs_editable()){$checked_editable="checked";} ?>
                    <tr id="row-<?=$page->getUniqid()?>" data-uniqid="<?=$page->getUniqid()?>" data-uniqid-parent="<?=$page->getCategory()?>" class="row_edit_page back-main-l-4">
                        <td> 
                            <a onclick="Page.show('<?=$page->getUrl_name()?>');" data-bs-toggle="tooltip" data-bs-placement="top" title="Accéder à la page" class="text-main-d-3 text-secondary-d-2-hover pe-1"><i class="fa-solid fa-location-arrow"></i></a>
                            <a onclick="copyToClipboard('<?=$_SERVER['SERVER_NAME'] . '/#'. $page->getUrl_name()?>')" data-bs-toggle="tooltip" data-bs-placement="top" title="Copier le lien de la page" class="text-main-d-3 text-secondary-d-2-hover pe-1"><i class="fa-solid fa-copy"></i></a>
                        </td>
                        <td> <!-- Nom -->
                            <input 
                                onchange="Page.update('<?=$page->getUniqid();?>', this, 'name');" 
                                placeholder="Nom de la page" 
                                maxlength="300" 
                                type="text" 
                                class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                value="<?=$page->getName()?>">
                        </td>
                        <td> <!-- URL -->
                            <div>
                                <input 
                                    <?=$disabled?>
                                    onchange="Page.update('<?=$page->getUniqid();?>', this, 'url_name');" 
                                    placeholder="Nom de l'url" 
                                    maxlength="300" 
                                    type="text"
                                    pattern="^([a-zA-Z]|_|-|[0-9])*$" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$page->getUrl_name()?>">
                            </div>
                            <p><small class="size-0-8">Le nom doit ne doit pas contenir de caractérère spécial ni d'espace.</small></p>
                        </td>
                        <td> <!-- Catégorie -->
                            <select onchange="Page.update('<?=$page->getUniqid()?>', this, 'category');" class="form-select form-select-sm" aria-label=".form-select-sm">
                                <?php $selected = ""; if($page->getCategory() < 0){$selected = "selected";} ?>
                                <option <?=$selected?> value="-1">Ne pas afficher</option>
                                <?php foreach ($categories_available as $number_category => $name_category) {
                                    $selected = ""; if($page->getCategory() == $number_category){$selected = "selected";} ?>
                                    <option <?=$selected?> value="<?=$number_category?>"><?=$name_category?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td> <!-- Dropdown -->
                        </td>
                        <td> <!-- Public -->
                            <div class="form-check form-switch pe-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Si la case est coché, la page est accessible même sans être connecté via un compte.">
                                <input <?=$disabled?> onchange="Page.update('<?=$page->getUniqid();?>', this, 'public', <?=Controller::IS_CHECKBOX?>);" <?=$checked_public?> class="form-check-input back-main-d-2 border-main-d-2" type="checkbox" role="switch" id="switchpublic<?=$page->getUniqid()?>">
                                <label class="form-check-label" for="switchpublic<?=$page->getUniqid()?>"><i class="fa-solid fa-low-vision"></i></label>
                            </div>
                        </td>
                        <td> <!-- Is_editable -->
                            <div class="form-check form-switch pe-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Si la case est coché, il est possible d'ajouter des sections à la page.">
                                <input onchange="Page.update('<?=$page->getUniqid();?>', this, 'is_editable', <?=Controller::IS_CHECKBOX?>);" <?=$checked_editable?> class="form-check-input back-main-d-2 border-main-d-2" type="checkbox" role="switch" id="switcheditable<?=$page->getUniqid()?>">
                                <label class="form-check-label" for="switcheditable<?=$page->getUniqid()?>"><i class="fa-solid fa-pen-square"></i></label>
                            </div>
                        </td>
                        <td>
                            <?php if($disabled != "disabled"){ ?>
                                <a onclick="Page.remove('<?=$page->getUniqid()?>')" class="text-main-d-3 text-red-d-1-hover pe-1"><i class="fa-solid fa-trash"></i></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php foreach (Page::CATEGORY as $name_category => $number_category) { ?>
                    <tr class="row_category_page back-main-d-2">
                        <td colspan="8" class="text-center size-1-2 text-white"><?=$name_category?></td>
                    </tr>
                    <?php foreach ($manager->getAllFromCategory($number_category) as $page) { 
                        $disabled = ""; if(in_array($page->getUniqid(), Page::UNIQID_NO_EDIT)){$disabled = "disabled";}
                        $checked_dropdown="";if($page->getIs_dropdown()){$checked_dropdown="checked";}
                        $checked_public="";if($page->getPublic()){$checked_public="checked";}
                        $checked_editable="";if($page->getIs_editable()){$checked_editable="checked";} ?>
                        <tr data-uniqid-parent="<?=$number_category?>" class="group-<?=$page->getUniqid()?> row_name_page back-main-l-2">
                            <td colspan="8" class="text-center size-1 bold"><?=$page->getName()?></td>
                        </tr>
                        <tr id="row-<?=$page->getUniqid()?>" data-uniqid="<?=$page->getUniqid()?>" data-uniqid-parent="<?=$number_category?>" class="row_edit_page sortables back-main-l-4">
                            <td> 
                                <a onclick="Page.show('<?=$page->getUrl_name()?>');" data-bs-toggle="tooltip" data-bs-placement="top" title="Accéder à la page" class="text-main-d-3 text-secondary-d-2-hover pe-1"><i class="fa-solid fa-location-arrow"></i></a>
                                <a onclick="copyToClipboard('<?=$_SERVER['SERVER_NAME'] . '/#'. $page->getUrl_name()?>')" data-bs-toggle="tooltip" data-bs-placement="top" title="Copier le lien de la page" class="text-main-d-3 text-secondary-d-2-hover pe-1"><i class="fa-solid fa-copy"></i></a>
                                <a onclick="page.showRow('<?=$page->getUniqid()?>');" data-bs-toggle="tooltip" data-bs-placement="top" title="Afficher ou cacher les pages enfants" class="dropdown-child text-main-d-3 text-secondary-d-2-hover pe-1"><i class="fa-solid fa-sort-down"></i></a>
                            </td>
                            <td> <!-- Nom -->
                                <input 
                                    onchange="Page.update('<?=$page->getUniqid();?>', this, 'name');" 
                                    placeholder="Nom de la page" 
                                    maxlength="300" 
                                    type="text" 
                                    class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                    value="<?=$page->getName()?>">
                            </td>
                            <td> <!-- URL -->
                                <div>
                                    <input 
                                        <?=$disabled?>
                                        onchange="Page.update('<?=$page->getUniqid();?>', this, 'url_name');" 
                                        placeholder="Nom de l'url" 
                                        maxlength="300" 
                                        type="text"
                                        pattern="^([a-zA-Z]|_|-|[0-9])*$" 
                                        class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                        value="<?=$page->getUrl_name()?>">
                                </div>
                                <p><small class="size-0-8">Le nom doit ne doit pas contenir de caractérère spécial ni d'espace.</small></p>
                            </td>
                            <td> <!-- Catégorie -->
                                <select onchange="Page.update('<?=$page->getUniqid()?>', this, 'category');" class="form-select form-select-sm" aria-label=".form-select-sm">
                                    <?php $selected = ""; if($page->getCategory() < 0){$selected = "selected";} ?>
                                    <option <?=$selected?> value="-1">Ne pas afficher</option>
                                    <?php foreach ($categories_available as $number_category => $name_category) {
                                        $selected = ""; if($page->getCategory() == $number_category){$selected = "selected";} ?>
                                        <option <?=$selected?> value="<?=$number_category?>"><?=$name_category?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td> <!-- Dropdown -->
                                <div class="form-check form-switch pe-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Si la case est coché, les pages filles seront sous forme de liste déroulante. Attention cette page n'est plus accessible directement depuis le menu.">
                                    <input onchange="Page.update('<?=$page->getUniqid();?>', this, 'is_dropdown', <?=Controller::IS_CHECKBOX?>);" <?=$checked_dropdown?> class="form-check-input back-main-d-2 border-main-d-2" type="checkbox" role="switch" id="switchdropdown<?=$page->getUniqid()?>">
                                    <label class="form-check-label" for="switchdropdown<?=$page->getUniqid()?>"><i class="fa-solid fa-caret-square-down"></i></label>
                                </div>
                            </td>
                            <td> <!-- Public -->
                                <div class="form-check form-switch pe-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Si la case est coché, la page est accessible même sans être connecté via un compte.">
                                    <input <?=$disabled?> onchange="Page.update('<?=$page->getUniqid();?>', this, 'public', <?=Controller::IS_CHECKBOX?>);" <?=$checked_public?> class="form-check-input back-main-d-2 border-main-d-2" type="checkbox" role="switch" id="switchpublic<?=$page->getUniqid()?>">
                                    <label class="form-check-label" for="switchpublic<?=$page->getUniqid()?>"><i class="fa-solid fa-low-vision"></i></label>
                                </div>
                            </td>
                            <td> <!-- Is_editable -->
                                <div class="form-check form-switch pe-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Si la case est coché, il est possible d'ajouter des sections à la page.">
                                    <input onchange="Page.update('<?=$page->getUniqid();?>', this, 'is_editable', <?=Controller::IS_CHECKBOX?>);" <?=$checked_editable?> class="form-check-input back-main-d-2 border-main-d-2" type="checkbox" role="switch" id="switcheditable<?=$page->getUniqid()?>">
                                    <label class="form-check-label" for="switcheditable<?=$page->getUniqid()?>"><i class="fa-solid fa-pen-square"></i></label>
                                </div>
                            </td>
                            <td>
                                <?php if($disabled != "disabled"){ ?>
                                    <a onclick="Page.remove('<?=$page->getUniqid()?>')" class="text-main-d-3 text-red-d-1-hover pe-1"><i class="fa-solid fa-trash"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php foreach ($manager->getAllFromCategory($page->getUniqid()) as $page_child) { 
                            $disabled = ""; if(in_array($page_child->getUniqid(), Page::UNIQID_NO_EDIT)){$disabled = "disabled";} ?>
                            <tr id="row-<?=$page_child->getUniqid()?>" data-uniqid-parent="<?=$page->getUniqid()?>" class="group-<?=$page->getUniqid()?> row_edit_page_child sortables back-main-l-5" data-uniqid="<?=$page_child->getUniqid()?>">
                                <td>
                                    <a onclick="Page.show('<?=$page->getUrl_name()?>');" data-bs-toggle="tooltip" data-bs-placement="top" title="Accéder à la page" class="text-main-d-3 text-secondary-d-2-hover pe-1"><i class="fa-solid fa-location-arrow"></i></a>
                                    <a onclick="copyToClipboard('<?=$_SERVER['SERVER_NAME'] . '/#'. $page->getUrl_name()?>')" data-bs-toggle="tooltip" data-bs-placement="top" title="Copier le lien de la page" class="text-main-d-3 text-secondary-d-2-hover pe-1"><i class="fa-solid fa-copy"></i></a>
                                </td>
                                <td> <!-- Nom -->
                                    <input 
                                        onchange="Page.update('<?=$page_child->getUniqid();?>', this, 'name');" 
                                        placeholder="Nom de la page" 
                                        maxlength="300" 
                                        type="text" 
                                        class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                        value="<?=$page_child->getName()?>">
                                </td>
                                <td> <!-- URL -->
                                    <div>
                                        <input 
                                            <?=$disabled?>
                                            onchange="Page.update('<?=$page_child->getUniqid();?>', this, 'url_name');" 
                                            placeholder="Nom de l'url" 
                                            maxlength="300" 
                                            type="text"
                                            pattern="^([a-zA-Z]|_|-|[0-9])*$" 
                                            class="form-control form-control-main-focus form-control form-control-main-focus-sm" 
                                            value="<?=$page_child->getUrl_name()?>">
                                    </div>
                                    <p><small class="size-0-8">Le nom doit ne doit pas contenir de caractérère spécial ni d'espace.</small></p>
                                </td>
                                <td> <!-- Catégorie -->
                                    <select onchange="Page.update('<?=$page_child->getUniqid()?>', this, 'category');" class="form-select form-select-sm" aria-label=".form-select-sm">
                                        <?php $selected = ""; if($page_child->getCategory() < 0){$selected = "selected";} ?>
                                        <option <?=$selected?> value="-1">Ne pas afficher</option>
                                        <?php foreach ($categories_available as $number_category => $name_category) {
                                            $selected = ""; if($page_child->getCategory() == $number_category){$selected = "selected";} ?>
                                            <option <?=$selected?> value="<?=$number_category?>"><?=$name_category?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td></td>
                                <td> <!-- Public -->
                                    <div class="form-check form-switch pe-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Si la case est coché, la page est accessible même sans être connecté via un compte.">
                                        <input <?=$disabled?> onchange="Page.update('<?=$page_child->getUniqid();?>', this, 'public', <?=Controller::IS_CHECKBOX?>);" <?=$checked_public?> class="form-check-input back-main-d-2 border-main-d-2" type="checkbox" role="switch" id="switchpublic<?=$page_child->getUniqid()?>">
                                        <label class="form-check-label" for="switchpublic<?=$page_child->getUniqid()?>"><i class="fa-solid fa-low-vision"></i></label>
                                    </div>
                                </td>
                                <td> <!-- Is_editable -->
                                    <div class="form-check form-switch pe-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Si la case est coché, il est possible d'ajouter des sections à la page.">
                                        <input onchange="Page.update('<?=$page_child->getUniqid();?>', this, 'is_editable', <?=Controller::IS_CHECKBOX?>);" <?=$checked_editable?> class="form-check-input back-main-d-2 border-main-d-2" type="checkbox" role="switch" id="switcheditable<?=$page_child->getUniqid()?>">
                                        <label class="form-check-label" for="switcheditable<?=$page_child->getUniqid()?>"><i class="fa-solid fa-pen-square"></i></label>
                                    </div>
                                </td>
                                <td>
                                    <?php if($disabled != "disabled"){ ?>
                                        <a onclick="Page.remove('<?=$page_child->getUniqid()?>')" class="text-main-d-3 text-red-d-1-hover pe-1"><i class="fa-solid fa-trash"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } 
                    } 
                } ?>
            </tbody>
        </table>

        <div id="btn-update-order" class="fixed-bottom" style="right:20px;bottom:20px;left:auto;">
            <a onclick="Page.updateOrder_num();" class="btn btn-back-secondary">Mettre à jour l'ordre</a>
        </div>
        

        <script>
            $( function() {
                $('#btn-update-order').hide();
                $('#table').bootstrapTable({
                    exportTypes: ["pdf","doc","xlsx","xls","xml", "json", "png", "sql", "txt", "tsv"],
                });

                $( "#sortable" ).sortable({
                    axis: "y",
                    cursor: "move",
                    revert: true,
                    scrollSpeed: 40,
                    items: ".sortables",
                    start: function() {
                        $("#btn-update-order").show("slow");
                    }
                });
                
            } );
            var page = new Page();

        </script>

    <?php $template["content"] = ob_get_clean();

}