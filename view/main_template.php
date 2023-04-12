<!DOCTYPE html>
<html lang="fr" style="height:100%;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="description" content="<?=$GLOBALS['project']['description']?>">
    <link rel="icon" type="image/svg" href="<?=$GLOBALS['project']['logo_mini']?>"/>
    <!-- <link rel="shortcut icon" type="image/x-icon" href="medias/logos/logo_mini.ico" /> -->
    <title><?=$GLOBALS['project']['name']?></title>
    <meta name="keywords" content=""/>

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
                <header class='app-toolbar'> <!-- ENTETE -->
                    <div class="app-toolbar-title">
                        <a onclick="toogleMenu();" class="menu-toggle"><i class="fas fa-bars"></i></a>
                        <h1 id="title"></h1>
                    </div>
                    <div class="app-toolbar-nav">
                        <div class="mx-2">
                            <input type="text" 
                                style="max-width: 300px;"
                                data-url = "index.php?c=search&a=search"
                                data-search_in = <?=ControllerSearch::SEARCH_IN_ALL?>
                                data-minlenght = 3
                                data-action = <?=ControllerSearch::SEARCH_DONE_REDIRECT?>
                                data-limit = 5
                                data-only_usable = true
                                class="form-control text-main-d-3 form-control-sm" 
                                id="globalsearch" 
                                placeholder="Recherche ...">
                            <span id="search-sign"></span>
                            <script>
                                window.onload = function () {
                                    autocomplete_load("#globalsearch");
                                };
                            </script>
                        </div>
                        <a class="mx-2"><?php
                            View::shortcutDispatch(
                                template_type: View::TEMPLATE_SNIPPET,
                                template_name : "icon",
                                data : [
                                    "style" => Style::ICON_SOLID,
                                    "icon" => "book",
                                    "color" => "secondary",
                                    "is_btn" => true,
                                    "btn_type" => Style::STYLE_TEXT,
                                    "size" => "size-1-3",
                                    "tooltip" => "Ouvrir le grimoire (ctrl + b)",
                                    "onclick" => "User.getBookmark(true);",
                                    "content" => "Grimoire",
                                    "content_placement" => Style::POSITION_BOTTOM
                                ], 
                                write: true);
                        ?></a>
                        <a class="mx-2"><?php
                            View::shortcutDispatch(
                                template_type: View::TEMPLATE_SNIPPET,
                                template_name : "icon",
                                data : [
                                    "style" => Style::ICON_SOLID,
                                    "icon" => "dice",
                                    "color" => "secondary",
                                    "is_btn" => true,
                                    "btn_type" => Style::STYLE_TEXT,
                                    "size" => "size-1-3",
                                    "tooltip" => "Lanceur de dé",
                                    "onclick" => "$('#diceroller').modal('show');"
                                ], 
                                write: true);
                        ?></a>
                        <div id="userVisual" class="ms-3"></div>
                    </div>
                </header>
                <div class="app-toolbar-mobile">
                    <a class="mx-2"><?php
                        View::shortcutDispatch(
                            template_type: View::TEMPLATE_SNIPPET,
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_SOLID,
                                "icon" => "bars",
                                "color" => "secondary",
                                "is_btn" => true,
                                "btn_type" => Style::STYLE_TEXT,
                                "size" => "size-1-3",
                                "tooltip" => "Ouvrir le Menu",
                                "onclick" => "toogleMenu()",
                                "content" => "Menu",
                                "content_placement" => Style::POSITION_BOTTOM
                            ], 
                            write: true);
                    ?></a>
                    <a class="mx-2"><?php
                        View::shortcutDispatch(
                            template_type: View::TEMPLATE_SNIPPET,
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_SOLID,
                                "icon" => "book",
                                "color" => "secondary",
                                "is_btn" => true,
                                "btn_type" => Style::STYLE_TEXT,
                                "size" => "size-1-3",
                                "tooltip" => "Ouvrir le grimoire (ctrl + b)",
                                "onclick" => "User.getBookmark(true)",
                                "content" => "Grimoire",
                                "content_placement" => Style::POSITION_BOTTOM
                            ], 
                            write: true);
                    ?></a>
                    <a class="mx-2"><?php
                        View::shortcutDispatch(
                            template_type: View::TEMPLATE_SNIPPET,
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_SOLID,
                                "icon" => "search",
                                "color" => "secondary",
                                "is_btn" => true,
                                "btn_type" => Style::STYLE_TEXT,
                                "size" => "size-1-3",
                                "tooltip" => "Rechercher sur le site",
                                "onclick" => "Page.showSearchbar();",
                                "content" => "Rechercher",
                                "content_placement" => Style::POSITION_BOTTOM
                            ], 
                            write: true);
                    ?></a>
                    <a id="account_btn_toolbar_mobile" class="mx-2"></a>
                </div>
            </div>
            <div class="app-btn-show-toolbar-footer" style="display: none;">
                <a onclick="toogleToolbar(false);toogleFooter(false)"><i class="fas fa-caret-down"></i></a>
            </div>

            <div class='app-content'>
                <div id="content" class='container'>

                </div>
            </div>
            <footer>
                <div >
                    <p>Ces explications sont tirés en très grandes parties de <a class="text-main text-main-d-2-hover" href="https://solomonk.fr/fr/">Solomonk</a> pour la partie concernant Dofus et de <a class="text-main text-main-d-2-hover" href="https://5e-drs.fr/">5e-DRS</a> pour la partie concernant Donjon & Dragon.</p>
                    <p>Nous vous invitons à vous y référer si certains points ne vous parraissent pas claire ou pour plus de détails.</p>
                    <p>Vous êtes inviter à aider la développement du JDR. N'hésitez pas à modifier les différentes sections en respectant <a class="text-main text-main-d-2-hover" href="http://jdr.iota21.fr/#contribuer">la charte de modification.</a></p>
                </div>
            </footer>
        </div>
    </div>

    <div class="offcanvas offcanvas-start back-main-l-4" data-bs-scroll="true" tabindex="-1" id="offcanvasbookmark" aria-labelledby="offcanvasbookmark">
            <div class="offcanvas-header">
                <a id="back-bookmark" class="btn-text-main size-1-4 me-2" onclick="User.getBookmark();"><i class="fas fa-chevron-circle-left"></i></a>
                <h2 class="offcanvas-title text-secondary-d-2"></h2>
                <div>
                    <a id="btn-fullscreen" title="Agrandir" class="btn-text-main size-1-4" onclick="Page.offCanvasFullscreen();"><i class="fas fa-expand"></i></a>
                    <button type="button" title="Fermer le Grimoire (Echap)" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
            </div>
            <div class="offcanvas-body row border-none border-solid border-main-l-2-hover border-right-5 border-main-l-3 p-2 m-0" id="offCanvas_zone_resizable">
                <div id="offcanvas-content" class="col">

                </div>
            </div>
    </div>

    <div id="modal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title w-100"></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="cookie-bar">
        <div class="mx-5 d-flex flex-column-reverse flex-md-row align-items-center">
            <div>
                <p>Ce site recquière l'utilisation de certains cookies pour fonctionner, d'autres sont optionnelles. Lesquels acceptes-vous ?</p>
                <ul>
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
                            <label class="form-check-label" for="cookie-bookmark">Cookies du Grimoire</label>
                        </div>
                        <p><small class="size-0-7 text-grey-d-3">Ce sont les cookies permettant d'enregistrer (sans avoir de compte) différents élèments dans son bookmark.</small></p>
                    </li>
                </ul>
            </div>
            <div class="d-flex flex-column text-center align-items-center">
                <button onclick="Connect.setCookie(false,{'connexion':true, 'bookmark':true});" class="btn btn-sm btn-border-green my-2">Tout accepter</button>
                <button onclick="Connect.setCookie(true);" class="btn btn-sm btn-border-secondary my-2">Accepter les cookies cochés</button>
                <button onclick="Connect.setCookie(false,{'connexion':false, 'bookmark':false});" class="btn btn-sm btn-border-red my-2">Tout rejeter</button>
                <p><small class="size-0-7 text-grey-d-3 my-2">Aucun cookie n'est utilisé à des fins commerciales, statistiques ou pour récupérer quelconque information. <a onclick="Page.show('cgu');">En savoir plus</a></small></p>
            </div>
        </div>
    </div>

    <div id="diceroller" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title w-100"><i class="fas fa-dice"></i> Jet de dés</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body justify-content-center text-center">
                    <p>
                        <div class="input-group mb-3">
                            <input onchange="minmaxDice()" type="text" class="form-control form-control-main-focus" placeholder="nombre de dé" id="number_dice">
                            <span class="input-group-text">D</span>
                            <input onchange="minmaxDice()" type="text" class="form-control form-control-main-focus" placeholder="Type du dé" id="type_dice">
                            <span class="input-group-text"> + </span>
                            <input onchange="minmaxDice()" type="text" class="form-control form-control-main-focus" placeholder="ajoute fixe" id="add_int">
                        </div>
                    </p>
                    <p class="text-grey-d-1 size-0-8" id="min-max"></p>
                    <a class="btn btn-sm btn-back-secondary" onclick="rollDice();">Lancer les dés</a>
                    <p id="result_dice"></p>
                </div>
            </div>
        </div>
    </div>


    <?php Router::includeJS();?>

    <script>
        $('.cookie-bar').hide();
        <?php if(!isset($_COOKIE['cookie_preference'])){ ?>
            $('.cookie-bar').show("drop", 100);
        <?php } ?>

        $(document).ready(function(){ 
            // Renvoi vers la page demandée
            let parts = window.location.pathname.split('/').filter(function(value) {
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
            
            // Redimensionnement de la zone de bookmark #offcanvasbookmark grâce à la souris et au clic et à la zone #offCanvas_zone_resizable avec un min de 350px et un max de la largeur de l'écran
            $("#offCanvas_zone_resizable").resizable({
                handles: "e",
                minWidth: 350,
                maxWidth: $(window).width() - 50,
                resize: function (event, ui) {
                    $("#offcanvasbookmark").css("width", ui.size.width);
                    $("#offcanvasbookmark").css("max-width", ui.size.width);
                }
            });

        });

    </script>

</body>
</html>
