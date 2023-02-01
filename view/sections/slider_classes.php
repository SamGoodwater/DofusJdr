<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : CardItem
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

    $template = array(
        "title" => "Slider des Classes",
        "description" => "Affichages des cartes de toutes les classes.",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false
    );
    $classeManager = new ClasseManager;


    ob_start(); ?>
        <div id="listcard<?=$this->getUniqid()?>">

            <div class="mb-3 d-flex">
                <input type="text" class="form-control form-control-sm" onchange="searchWithTerm();" id="search" placeholder="Rechercher">
                <button class="btn btn-border-grey btn-sm ms-1 my-1" onclick="resetSearch()" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Restorer la recherche"><i class="fas fa-trash-restore"></i></button>
            </div>

            <div class="nav-item-divider"></div>
            <div class="d-flex flew-row justify-content-between justify-content-sm-center flex-wrap">
                <?php foreach($classeManager->getAll() as $classe){ ?>
                    <div id="slider-<?=$classe->getUniqid()?>" data-name="<?=$classe->getName()?>" class="slider-custom">
                        <div class="slider-image d-flex">
                            <?php $n_img = 0; ?>
                            <a class="img-slider" data-fancybox='gallery' href='<?=$classe->getPath_img()?>'>
                                <div class='img-back-350 borderImage' style="background-image:url('<?=$classe->getPath_img()?>')"></div>
                            </a>
                        </div>
                        <?php if($n_img > 1){?> 
                            <button class="btn-prev"><i class="fas fa-chevron-left"></i></button>
                            <button class="btn-next"><i class="fas fa-chevron-right"></i></button>
                        <?php } ?>
                        <div class="slider-title">
                            <div id="arrow-show-info" class="text-center text-white"><i class="fas fa-chevron-up"></i></div>
                            <div><i class="name size-1-4 text-white"><?=$classe->getName()?></i></div>
                            <span class="position-absolute" style="right:20px;top:-10px;"><?=$classe->getWeapons_of_choice(Content::FORMAT_ICON)?></span>
                            <div class="description_fast size-0-8 text-grey"><?=$classe->getDescription_fast()?></div>
                            <div class="trait"><?= $classe->getTrait(Content::FORMAT_BADGE); ?></div>
                        </div>
                        <div class="slider-info" ondblclick="Classe.open('<?=$classe->getUniqid()?>')">
                            <p class="description size-0-8 text-white"><?=$classe->getDescription()?></p>
                            <div class="nav-item-divider"></div>
                            <p class="life size-0-8 text-white"><?=$classe->getLife()?></p>
                            <div class="nav-item-divider"></div>
                            <p class="specificity size-0-8 text-white"><?=$classe->getSpecificity()?></p>
                            <div class="d-flex justify-content-between">
                                <div class="d-flex justify-content-start">
                                    <div class="state"><?=$classe->getUsable(Content::FORMAT_MODIFY);?></div>
                                </div>
                                <p class="text-center size-0-6 text-grey-d-2">Ajouté le <?=$classe->getTimestamp_add(Content::DATE_FR)?><br>Mis à jour le <?=$classe->getTimestamp_updated(Content::DATE_FR)?></p>
                                <a class="btn btn-sm btn-back-main align-self-end" onclick="Classe.open('<?=$classe->getUniqid()?>')"><i class="fa-regular fa-pen-to-square"></i> Modifier</a>
                            </div>
                        </div>
                    </div>
                    <script>              
                        loadSlider("#slider-<?=$classe->getUniqid()?>");
                    </script>
                <?php } ?>
            </div>
        </div>

        <script>
            function searchWithTerm() {
                let term = $("#search").val().toLowerCase();
                if(term != "") {
                    let element;
                    var element_searchable = [];
                    $(".slider-custom").each(function(index, element) {
                        element = $(element);
                        element_searchable = [];
                        element_searchable.push(element.find(".name").text().toLowerCase());
                        element_searchable.push(element.find(".description_fast").text().toLowerCase());

                        if(element_searchable.length > 0){
                            if(element_searchable.join(" ").includes(term)) {
                                element.show("slow");
                            } else {
                                element.hide("slow");
                            }
                        }else{
                            console.log("Rien trouvé");
                        }
                    });
                } else {
                    resetSearch();
                }
            }

            function resetSearch() {
                $("#search").val("");
                $(".slider-custom").each(function(index, element) {
                    $(element).show('slow');
                });
            }

            
            $(function() {
                $("#search").off('keyup').on('keyup', function(e) {
                    if (e.which === 13) {
                        searchWithTerm();
                    }
                });
            });

        </script>

    <?php $template["content"] = ob_get_clean();