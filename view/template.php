<!DOCTYPE html>
<html lang="fr" style="height:100%;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="description" content="">
    <link rel="icon" type="image/svg" href="medias/logos/logo_dice_20.svg"/>
    <!-- <link rel="shortcut icon" type="image/x-icon" href="medias/logos/logo_mini.ico" /> -->
    <title>JDR Dofus</title>
    <meta name="keywords" content=""/>

    <?php View::includeCss(); ?>

</head>

<body>
    <div id="MsgAlert" style='z-index:10000'></div>
    
    <div class='dashboard'>
        <div class="dashboard-nav">
            <?php include "menu.php"?>
        </div>

        <div class='dashboard-app'>
            <header class='dashboard-toolbar'> <!-- ENTETE -->
                <div class="row flex-nowrap align-items-center">
                    <a href="#!" class="menu-toggle" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"><i class="fas fa-bars"></i></a>
                    <h3 id="title" class="ps-3 text-main-d-1"></h3>
                </div>
                <div class="d-flex flex-row justify-content-between align-items-baseline">
                    <div class="d-flex flex-row justify-content-evenly align-items-baseline mb-3 position-relative">
                        <a onclick="User.getBookmark(true);" class="btn-text-secondary text-center mx-1" title="Ouvrir le grimoire (ctrl + b)" type="button">
                            <i class="fas fa-book size-1-2"></i>
                            <span class="size-0-8">Grimoire</span>
                        </a>
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
                    </div>
                    <script>
                        window.onload = function () {
                            autocomplete_load("#globalsearch");
                        };
                    </script>
                    <a class="size-1-4 btn-text-secondary ms-2" onclick="$('#diceroller').modal('show');"><i class="fas fa-dice"></i></a>
                    <div id="userVisual" class="ms-3"></div>
                </div>
            </header>
            <div class='dashboard-content'>
                <div id="content" class='container'>

                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-start back-main-l-4" data-bs-scroll="true" tabindex="-1" id="offcanvasbookmark" aria-labelledby="offcanvasbookmark">
            <div class="offcanvas-header">
                <a id="back-bookmark" class="btn-text-main size-1-4 me-2" onclick="User.getBookmark();"><i class="fas fa-chevron-circle-left"></i></a>
                <h4 class="offcanvas-title text-secondary-d-2"></h4>
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
                    <h5 class="modal-title w-100 me-3"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="cookie-bar">
        <div class="container d-flex flex-row align-items-center">
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
                            <label class="form-check-label" for="cookie-bookmark">Cookies du Bookmark</label>
                        </div>
                        <p><small class="size-0-7 text-grey-d-3">Ce sont les cookies permettant d'enregistrer (sans avoir de compte) différents élèments dans son bookmark.</small></p>
                    </li>
                </ul>
            </div>
            <div class="d-flex flex-column text-center align-items-center">
                <button onclick="Connect.setCookie(false,{'connexion':true, 'bookmark':true});" class="btn btn-sm btn-border-green my-2">Tout accepter</button>
                <button onclick="Connect.setCookie(true);" class="btn btn-sm btn-border-secondary my-2">Accepter les cookies cochés</button>
                <button onclick="Connect.setCookie(false,{'connexion':false, 'bookmark':false});" class="btn btn-sm btn-border-red my-2">Tout rejeter</button>
                <p><small class="size-0-7 text-grey-d-3 my-2">Aucun cookie n'est utilisé à des fins commerciales, statistiques ou pour récupérer quelconque information. <a href="" onclick="">En savoir plus</a></small></p>
            </div>
        </div>
    </div>

    <div id="diceroller" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 me-3"><i class="fas fa-dice"></i> Jet de dés</h5>
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


    <?php View::includeJS();?>

    <script>
        $('.cookie-bar').hide();
        <?php if(!isset($_COOKIE['cookie_preference'])){ ?>
            $('.cookie-bar').show("drop", 100);
        <?php } ?>

        $(document).ready(function(){ 
            // Renvoi vers la page en hash
            if(window.location.hash == ""){
                Page.show("home");
            } else {
                var hash = window.location.hash.substr(1).split('&');
                if(hash.length == 2){
                    Page.show(hash[0], hash[1]);
                } else {
                    Page.show(hash[0]);
                }
            }
            Connect.getHeader(false);
            
        });

        const mobileScreen = window.matchMedia("(max-width: 990px )"); // Pour le menu
        $(document).ready(function () {

            $(".dashboard-nav-dropdown-toggle").click(function () {
                $(this).closest(".dashboard-nav-dropdown")
                    .toggleClass("show")
                    .find(".dashboard-nav-dropdown")
                    .removeClass("show");
                $(this).parent()
                    .siblings()
                    .removeClass("show");
            });
            $(".menu-toggle").click(function () {
                if (mobileScreen.matches) {

                    $(".dashboard-nav").toggleClass("mobile-show");
                } else {
                    $(".dashboard").toggleClass("dashboard-compact");
                }
            });
        });

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




    </script>

</body>

</html>
