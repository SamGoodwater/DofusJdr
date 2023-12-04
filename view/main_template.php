<!DOCTYPE html>
<html lang="fr" style="height:100%;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="description" content="<?=$GLOBALS['project']['description']?>">
    <?php $icon_write = false;
        if(isset($GLOBALS['project']['icon'])){
            if(!empty($GLOBALS['project']['icon'])){
                if(is_array($GLOBALS['project']['icon'])){
                    foreach($GLOBALS['project']['icon'] as $icon){
                        $file = new File($icon);
                        echo("<link rel='icon' type='image/".$file->getExtention()."' href='".$file->getPath()."'/>");
                    }
                    $icon_write = true;
                } else {
                    $file = new File($GLOBALS['project']['icon']);
                    echo("<link rel='icon' type='image/".$file->getExtention()."' href='".$file->getPath()."'/>");
                    $icon_write = true;
                }
            }
        }
        if(!$icon_write){
            if(isset($GLOBALS['project']['logo_mini'])){
                if(!empty($GLOBALS['project']['logo_mini'])){
                    $file = new File($GLOBALS['project']['logo_mini']);
                    echo("<link rel='icon' type='image/".$file->getExtention()."' href='".$file->getPath()."'/>");
                }
            } else {
                $file = new File($GLOBALS['project']['logo']);
                echo("<link rel='icon' type='image/".$file->getExtention()."' href='".$file->getPath()."'/>");
            }
        }

    ?>
    <title><?=$GLOBALS['project']['name']?></title>
    <meta name="keywords" content="<?=$GLOBALS['project']['keywords']?>"/>

    <?php Router::includeCss(); ?>

</head>

<body>
    <div id="MsgAlert" style='z-index:10000'></div>
    <div id="onloadDisplay" style="z-index:10001">
        <div class='d-flex justify-content-center'><div class='spinner-border text-main-d-2' role='status'><span class='visually-hidden'>Loading...</span></div></div>
    </div>
    
    <div class='app app-extend'>
        <nav class="app-nav">
            <div class="app-nav-content" ><?php include "menu.php"?></div>
        </nav>

        <div class="app-toolbar-and-content">
            <div class="app-toolbar-content">
                <?php include_once "header.php";?>
            </div>
            <div class="app-btn-show-toolbar-footer" style="display: none;">
                <a onclick="toogleToolbar(false);toogleFooter(false)"><i class="fa-solid fa-caret-down"></i></a>
            </div>
            
            <main class='app-content'>
                <div id="content" class='container'>

                </div>
            </main>
            
            <?php include_once "footer.php";?>
        </div>
    </div>

    <!-- POPUP -->

        <div class="cookie-bar">
            <div class="mx-5 d-flex flex-column-reverse flex-md-row align-items-center">
                <div>
                    <p>Ce site recquière l'utilisation de certains cookies pour fonctionner, d'autres sont optionnelles. Lesquels acceptes-vous ?</p>
                    <ul class="list-unstyled">
                        <li>
                            <div class="form-check">
                                <input class="form-check-input form-control-main-focus back-main-l-2 border-main" type="checkbox" id="cookie-requisite" checked disabled>
                                <label class="form-check-label" for="cookie-mandatory">Cookies nécessaires</label>
                            </div>
                            <p><small class="size-0-7 text-grey-d-3">Ce sont les cookies permettant d'enregistrer les cookies acceptés ou non. Ils sont obligatoires.</small></p>
                        </li>
                        <li>
                            <div class="form-check">
                                <input class="form-check-input form-control-main-focus back-main-l-2 border-main" type="checkbox" checked id="cookie-connexion">
                                <label class="form-check-label" for="cookie-connexion">Cookie de connexion</label>
                            </div>
                            <p><small class="size-0-7 text-grey-d-3">C'est le cookie permettant d'enregistrer les paramètre de connexion et de reconnecter le compte automatiquement à la prochaine fois.</small></p>
                        </li>
                        <li>
                            <div class="form-check">
                                <input class="form-check-input form-control-main-focus back-main-l-2 border-main" type="checkbox" checked id="cookie-bookmark">
                                <label class="form-check-label" for="cookie-bookmark">Cookies du <?=ucfirst($GLOBALS['project']['bookmark_name'])?></label>
                            </div>
                            <p><small class="size-0-7 text-grey-d-3">Ce sont les cookies permettant d'enregistrer (sans avoir de compte) différents éléments dans son <?=ucfirst($GLOBALS['project']['bookmark_name'])?>.</small></p>
                        </li>
                    </ul>
                </div>
                <div class="d-flex flex-column text-center align-items-center">
                    <button onclick="Connect.setCookie(false,{'connexion':true, 'bookmark':true});" class="btn btn-sm btn-animate btn-border-green my-2">Tout accepter</button>
                    <button onclick="Connect.setCookie(true);" class="btn btn-sm btn-animate btn-border-secondary my-2">Accepter les cookies cochés</button>
                    <button onclick="Connect.setCookie(false,{'connexion':false, 'bookmark':false});" class="btn btn-sm btn-animate btn-border-red my-2">Tout rejeter</button>
                    <p><small class="size-0-7 text-grey-d-3 my-2">Aucun cookie n'est utilisé à des fins commerciales, statistiques ou pour récupérer quelconque information. <a onclick="Page.show('cgu');">En savoir plus</a></small></p>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-start back-main-l-4" data-bs-scroll="true" tabindex="-1" id="offcanvasbookmark" aria-labelledby="offcanvasbookmark">
            <div class="offcanvas-header">
                <a id="back-bookmark" class="btn-text-main size-1-4 me-2" onclick="User.getBookmark();"><i class="fa-solid fa-chevron-circle-left"></i></a>
                <h2 class="offcanvas-title text-secondary-d-2"></h2>
                <div>
                    <a id="btn-fullscreen" title="Agrandir" class="btn-text-main size-1-4" onclick="Page.offCanvasFullscreen();"><i class="fa-solid fa-expand"></i></a>
                    <button type="button" title="Fermer le <?=ucfirst($GLOBALS['project']['bookmark_name'])?> (Echap)" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
            </div>
            <div class="offcanvas-body row border-none border-solid border-main-l-2-hover border-right-5 border-main-l-3 p-2 m-0" id="offCanvas_zone_resizable">
                <div id="offcanvas-content" class="col">

                </div>
            </div>
        </div>

        <div id="bubbleshorcut" class="bubbleshorcut">
            <div class="bubbleshorcut_item show"></div>
            <a title="Cacher les bulles de raccourcis" class="bubbleshorcut__button_dropdown active" onclick="Bubbleshortcut.dropdownToogle();"><i class="fa-solid fa-caret-down"></i></a> 
        </div>

        <div id="modal" class="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title w-100"></h2>
                        <a class="modal__share_object btn-text-grey mx-2" title="Copier le lien vers cette objet" onclick=""><i class="fa-solid fa-share-alt"></i></a>
                        <a class="modal__bookmark_toogle btn-text-grey mx-2" title="Ajouter aux favoris" data-uniqid="" data-classe="" onclick="User.changeBookmark(this);"><i class="fa-regular fa-bookmark"></i></a>
                        <a class="modal__bubbleshortcut_toggle mx-2" title="Ajouter cette bulle de raccourcis" onclick=""></a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>

        <div id="diceroller" class="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title w-100"><i class="fa-solid fa-dice"></i> Outils</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body justify-content-center text-center">
                        <!-- JEt de Dés -->
                        <div class="mb-4">
                            <h2>Jet de dés</h2>
                            <div class="input-group mb-3">
                                <input onchange="minmaxDice()" type="text" class="form-control form-control-main-focus" placeholder="nombre de dé" id="number_dice">
                                <span class="input-group-text">D</span>
                                <input onchange="minmaxDice()" type="text" class="form-control form-control-main-focus" placeholder="Type du dé" id="type_dice">
                                <span class="input-group-text"> + </span>
                                <input onchange="minmaxDice()" type="text" class="form-control form-control-main-focus" placeholder="ajoute fixe" id="add_int">
                            </div>
                            <p class="text-grey-d-1 size-0-8" id="min-max"></p>
                            <a class="btn btn-sm btn-animate btn-back-secondary" onclick="rollDice();">Lancer les dés</a>
                            <p id="result_dice"></p>
                            <script>
                                $("#diceroller .modal-dialog").draggable({
                                    cursor: "move",
                                    handle: ".modal-header",
                                });
                            </script>
                        </div>

                        <!-- Récupérer des noms -->
                        <div class="mb-4" id="openai_getname_form">
                            <h2>Générateur de nom aléatoire</h2>
                            <p><small>Ce générateur utilise de l'intelligence artificielle et notamment ChatGPT (d'OpenAI).
                                Cela permet d'avoir des résultats pertinents et originaux. Cependant cet outil a un coût (environnemental et monétaire), merci de ne pas en abuser.</small></p>
                            <div class="d-flex justify-content-around flex-wrap gap-3">
                                <div class="my-2">
                                    <label for="openai_classe" class="form-label">Choisissez une classe</label>
                                    <input id="openai_classe" class="form-control form-control-sm" list="OpenAIdatalistClasseOptions" id="openai_classe" placeholder="Choisissez une classe">
                                    <datalist id="OpenAIdatalistClasseOptions">
                                        <option value="Non générique">
                                        <?php $manager = new ClasseManager;
                                            $classes = $manager->getAll();
                                            foreach($classes as $classe){
                                                echo("<option value='".$classe->getName()."'>");
                                            }
                                        ?>
                                    </datalist>
                                </div>
                                <div>
                                    <label class="form-label">Choisissez le genre</label>
                                    <select id="openai_genre" class="form-select form-select-sm" aria-label="liste des genres">
                                        <option value="nb" selected>Non-binaire</option>
                                        <option value="f">Féminin</option>
                                        <option value="m">Masculin</option>
                                        <option value="">Ne pas préciser</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <p>Facultatif - Vous pouvez préciser une culture pour inspirer le nom généré.</p>
                                    <p><small>Cette culture peut être issu de la fiction mais aussi du réel.</small></p>
                                    <p><input id="openai_inspiration_culturel" class="form-control form-control-sm" type="text" placeholder="indienne, malgache, bretonne, etc" aria-label="Inspiration culturel"></p>
                                </div>
                            </div>
                            <p class="my-2">
                                <label for="openai_result" class="form-label">Nom généré</label>
                                <p class="d-flex">
                                    <input class="form-control-sm form-control form-control-main text-center" type="text" id="openai_result" readonly value="">
                                    <a onclick="copyToClipboard($('#openai_result').val());"><i class="mx-2 btn-text-main fa-solid fa-copy"></i></a>
                                </p>
                            </p>
                            <div class="d-flex justify-content-center align-items-baseline gap-1">
                                <button class="btn btn-sm btn-back-secondary mt-2" onclick="generateName();">Générer</button>
                                <div id="openia_loading" class="spinner-border text-gray spinner-border-sm" role="status" style="display: none;"><span class="visually-hidden">Loading...</span></div>
                            </div>
                            <script>

                                function generateName(){
                                    $("#openia_loading").show();
                                    let URL = 'index.php?c=openai&a=call';
                                    let classe = $('#openai_getname_form #openai_classe').val();
                                    let genre = $('#openai_getname_form #openai_genre').val();
                                    let inspiration_culturel = $('#openai_getname_form #openai_inspiration_culturel').val();

                                    let data_post = {
                                        classe:classe,
                                        genre:genre,
                                        inspiration_culturel:inspiration_culturel
                                    };
                                    
                                    $.post(URL,
                                        {
                                            template:'modules/getname',
                                            data:data_post
                                        },
                                        function(data, status)
                                        {
                                            $("#openia_loading").hide();
                                            if(data.script != ""){
                                                $('body').append('<script>'+ data.script +'<//script>');
                                            }
                                            if(data.state){
                                                $("#openai_result").val(data.value);
                                                MsgAlert("Génération du nom", 'Le nom est ' + data.value, "green" , 3000);
                                            } else {
                                                MsgAlert("Échec de le la génération", 'Erreur : ' + data.error, "danger" , 4000);
                                            }
                                        },
                                        "json"
                                    ); 
                                }
                            </script>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

    <!-- END POPUP -->

    <?php Router::includeJS();?>

    <script>
        $('.cookie-bar').hide();
        <?php if(!isset($_COOKIE['cookie_preference'])){ ?>
            $('.cookie-bar').show("drop", 100);
        <?php } ?>

        // Globals variables
        globalThis.project = {};
        <?php foreach ($GLOBALS["project"] as $key => $value) {
            if(!in_array($key, ($GLOBALS["project"]["var_globals_project_not_accessible_to_js"]))){ 
                if(is_array($value)){
                    $value = json_encode($value);
                } else if(is_bool($value)){
                    $value = $value ? 'true' : 'false';
                } else if(is_string($value)){
                    $value = "\"".addslashes($value)."\"";
                } else if(is_int($value)){
                    $value = $value;
                } else {
                    $value = "'".$value."'";
                } ?>
                    globalThis.project.<?=$key?> = <?=$value?>;
            <?php }
        } ?>

        $(document).ready(function(){ 
            // Renvoi vers la page demandée
            let settings_modal = window.location.pathname.split('@').filter(function(value) {
                return value !== '' && value !== null && typeof value !== 'undefined';
            });

            let parts = settings_modal[0].split('/').filter(function(value) {
                return value !== '' && value !== null && typeof value !== 'undefined';
            });

            if(parts.length = 1){
                Page.show(parts[0]);
            } else if(parts.length == 2) {
                Page.show(parts[0], parts[1]);
            } else {
                Page.show("home");
            }
            Connect.getHeader(false);

            if(settings_modal.length == 2){
                settings_modal = settings_modal[1].split('~').filter(function(value) {
                    return value !== '' && value !== null && typeof value !== 'undefined';
                });
                if(settings_modal.length == 2){
                    let modelClassName = ucFirst(settings_modal[0]);
                    let model_uniqid = settings_modal[1];
                    // Vérifie que modelClassName ne contient que des lettres
                    if (/^[a-zA-Z]+$/.test(modelClassName)) {
                        // Vérifie que model_uniqid contient exactement 13 lettres et chiffres
                        if (/^[a-zA-Z0-9]{13}$/.test(model_uniqid)) {
                            eval(`${modelClassName}.open('${model_uniqid}')`);
                        }
                    }   
                }
            }
        });

    </script>

</body>
</html>
