<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Map
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
        "title" => "Cartes",
        "description" => "Section de gestion des cartes",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => false,
        "shownListAddInPage" => true
    );
    
    ob_start(); ?>
        <div class="m-2">
            <p>Sélectionner la région</p>
            <select id="option" class="form-select form-select-sm mb-2">
                <?php foreach (scandir('medias/maps/') as $value) {
                    if(is_dir("medias/maps/".$value) && $value != ".." && $value != "."){?>
                    <option value="<?=$value?>"><?=$value?></option>
                <?php } } ?>
            </select>
        </div>
    <?php $template["option"] = ob_get_clean();

    
if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    $img_global = [];
    $img = [];
    $path = "medias/maps/" . $template_vars['content'] ."/";
    if(is_dir($path)){
        foreach (scandir($path) as $file_name) {
            $path_file = $path . $file_name;
            $obj = new File($path_file);
    
            if(is_file($path_file) && FileManager::isImage($obj)){
                $img_global[] = $obj;
            } elseif(is_dir($path_file) && $file_name != ".." && $file_name != "."){
    
                foreach (scandir($path_file) as $file_child) {
                    $path_file_child = $path_file . "/" . $file_child;
                    $obj_child = new File($path_file_child);
            
                    if(is_file($path_file_child) && FileManager::isImage($obj_child)){
                        $img[$file_name][] = $obj_child;
                    }
    
                }
            }
        }
    
        ob_start(); ?>
    
            <h4 class="text-center">Cartes générales de la région</h4>
            <div>
                <div id="mainCarousel" class="carousel w-10/12 max-w-5xl mx-auto">
                    <?php foreach ($img_global as $file_name) { ?>
                        <div class="carousel__slide" data-src="<?=$file_name->getPath()?>" data-fancybox="gallery" data-caption="<?=$file_name->getName(Content::FORMAT_BRUT, false);?>">
                            <div class="img-back-300H-allL" style="background-image:url(<?=$file_name->getPath()?>)"></div>
                        </div>
                    <?php } ?>
    
                </div>
                <div id="thumbCarousel" class="carousel max-w-xl mx-auto">
                    <?php foreach ($img_global as $file_name) { ?>
                        <div>
                            <div class="carousel__slide">
                                <img class="panzoom__content" src="<?=$file_name->getPath()?>" />
                            </div>
                            <p class="text-center">
                                <?=ucfirst($file_name->getName(Content::FORMAT_BRUT, false))?>
                                <a href="<?=$file_name->getPath()?>" download="<?=$file_name->getName().'.'.substr(strrchr($file_name->getPath(),'.'),1);?>"><i class="fa-solid fa-download text-main-d-3 text-main-d-1-hover"></i></a>  
                            </p>
                        </div>
                    <?php } ?>
    
                </div>
            </div>
    
            <h4 class="text-center mt-4">Cartes spécifiques</h4>
            <div>
                <button onclick="checkAll();" class="btn btn-sm btn-animate border-main-d-1 text-white back-main-d-1 text-main-d-1-hover back-white-hover form-control-main-focus">Tout afficher</button>
                <button onclick="discheckAll();" class="btn btn-sm btn-animate border-main-d-1 text-white back-main-d-1 text-main-d-1-hover back-white-hover form-control-main-focus">Rien afficher</button>
                
                <?php foreach ($img AS $key => $list) { ?>
    
                    <button id="cat" onclick="checkCat(this);" data-catselect="<?=$key?>" data-check="1" data-color="<?=Style::getColorFromLetter($key)?>" class="btn btn-sm btn-animate border-<?=Style::getColorFromLetter($key)?>-d-4 form-control-<?=Style::getColorFromLetter($key)?>-focus"><?=ucfirst($key)?></button>
                
                <?php } ?>
                
            </div>
            <div class="d-flex flex-row flex-wrap justify-content-around">
                <?php foreach ($img AS $key => $list) { 
                    foreach ($list as $file_name) { ?>
                        
                        <div class="card m-1" data-cat="<?=$key?>" style="width: 15rem;">
                            <a data-fancybox='gallery' href='<?=$file_name->getPath()?>'><img src="<?=$file_name->getPath()?>" class="card-img-top" alt="<?=ucfirst($file_name->getName(Content::FORMAT_BRUT, false))?>"></a>
                            <span style="position:absolute;bottom:3px;right:3px;" class="badge back-<?=Style::getColorFromLetter($key)?>-d-4 text-white"><?=ucfirst($key)?></span>
                            <a style="position:absolute;top:5px;left:5px;" href="<?=$file_name->getPath()?>" download="<?=$file_name->getName().'.'.substr(strrchr($file_name->getPath(),'.'),1);?>"><i class=" p-1 fa-solid fa-download text-white text-main-l-1-hover shadow-text-3"></i></a>  
                        </div>
    
                    <?php }?>
                <?php } ?>
            </div>
    
            <script>
                // Initialise Carousel
                mainCarousel = new Carousel(document.querySelector("#mainCarousel"), {
                    Dots: false,
                });
    
                // Thumbnails
                thumbCarousel = new Carousel(document.querySelector("#thumbCarousel"), {
                    Sync: {
                        target: mainCarousel,
                        friction: 0,
                    },
                    Dots: false,
                    Navigation: false,
                    center: true,
                    slidesPerPage: 1,
                    infinite: false
                });
    
                    // Customize Fancybox
                Fancybox.bind('[data-fancybox="gallery"]', {
                    Image: {
                        zoom: true,
                    },
                    Carousel: {
                        on: {
                            change: (that) => {
                                mainCarousel.slideTo(mainCarousel.findPageForSlide(that.page), {
                                    friction: 0,
                                });
                            },
                        },
                    },
                });
    
                function showCatMap() {
                    document.querySelectorAll("#cat").forEach(function(cat) {
                        
                        var selector = $(cat).attr('data-catselect');
                        if($(cat).attr('data-check') == "1"){
                            document.querySelectorAll("[data-cat=\""+selector+"\"]").forEach(function(item) {
                                $(item).show("slow");
                            });
                        } else {
                            document.querySelectorAll("[data-cat=\""+selector+"\"]").forEach(function(item) {
                                $(item).hide("slow");
                            });    
                        }
    
                    });
                }
    
                checkAll();
    
                function checkCat(btn, forced = -1){
                    btn = $(btn);
                    color = btn.data("color");
    
                    if(btn.attr('data-check') == "1" && forced != 1 || forced == 0){ // déchecker
                        btn.removeClass("text-white").removeClass("text-"+color+"-d-4-hover").removeClass("back-"+color+"-d-4").removeClass("back-"+color+"-l-4-hover");
                        btn.addClass("text-"+color+"-d-4").addClass("text-white-hover").addClass("back-white").addClass("back-"+color+"-d-3-hover");
                        btn.attr('data-check', '0');
                    } else if( btn.attr('data-check') == "0" && forced != 0 || forced == 1) { // checker
                        btn.removeClass("text-"+color+"-d-4").removeClass("text-white-hover").removeClass("back-white").removeClass("back-"+color+"-d-3-hover");
                        btn.addClass("text-white").addClass("text-"+color+"-d-4-hover").addClass("back-"+color+"-d-4").addClass("back-"+color+"-l-4-hover");
                        btn.attr('data-check', '1');
                    }
                    showCatMap();
                }
    
                function checkAll(){
                    document.querySelectorAll("#cat").forEach(function(item) {
                        checkCat(item, 1);
                    });
                }
    
                function discheckAll(){
                    document.querySelectorAll("#cat").forEach(function(item) {
                        checkCat(item, 0);
                    });
                }
    
            </script>
    
        <?php $template["content"] = ob_get_clean();
    } else {
        $template["content"] = "Erreur de chargement des fichiers";
    }

}