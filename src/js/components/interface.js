function initInterface() {
    $(window).resize(function() {
        compactMenu();
    });
    compactMenu();
    initGlobalSearch();
    const overlay = document.querySelector('.overlay');
    overlay.addEventListener('click', closeOverlay());

    let menuToggle = document.querySelector('.menu-toggle');
    menuToggle.addEventListener('click', toggleMenu);
}

function toggleToolbar(forced_hidden = false){
    if($('.app-toolbar-content').hasClass('hidden') == false || forced_hidden){
        $(".app-toolbar-content").addClass("hidden");
        $(".app-btn-show-toolbar-footer").show("blind", 300);
        $(".dropdown-item-toggletoolbar-button").text("Afficher la barre d'outils");
    } else {
        $(".app-toolbar-content").removeClass("hidden");
        $(".app-btn-show-toolbar-footer").hide("blind", 300);
        $(".dropdown-item-toggletoolbar-button").text("Masquer la barre d'outils");
    }
}
function toggleFooter(forced_hidden = false){
    if($('footer').hasClass('hidden') || forced_hidden){
        $("footer").addClass("hidden");
        $("footer").hide("blind", 300);
    } else {
        $("footer").removeClass("hidden");
        $("footer").show("blind", 300);
    }
}
function toggleMenu(forcedClosed = false){
    const app = document.querySelector('.app');
    if (app.classList.contains('app-extend') || forcedClosed) { // Fermeture du menu
        app.classList.remove('app-extend');
        app.classList.add('app-fold');
        closeOverlay();
    } else {                                                    // Ouverture du menu
        app.classList.remove('app-fold');
        app.classList.add('app-extend');
        
        // Lorque le menu est ouvert en mode tablette ou mobile, et que l'on clique à l'extérieur du menu (sur l'overlay), alors le menu se ferme
        if(app.classList.contains('app-compacted')){
            useOverlay(() => toggleMenu(true));
        }
    }
}

function compactMenu(){
    if ($(window).width() < BREAKPOINT_TABLET) {
        $(".app").addClass("app-compacted");
    } else {
        $(".app").removeClass("app-compacted");
    }
}

function initGlobalSearch() {
    const container = document.querySelector('.globalsearch-box-container');
    const input = document.querySelector('.globalsearch-box-container input');
    const button = document.querySelector('.globalsearch-toggle');

    const openInputToggle = () => {
        container.classList.toggle('active');
        if(container.classList.contains('active')){
            button.querySelector('span i').classList.remove('fa-search');
            button.querySelector('span i').classList.add('fa-times');
            input.focus();
            
            if($(window).width() <= BREAKPOINT_TABLET){
                toggleMenu();
            }
        } else {
            button.querySelector('span i').classList.remove('fa-times');
            button.querySelector('span i').classList.add('fa-search');
            input.blur();
        }
    }

    button.addEventListener('click', () => {
        openInputToggle();
    });
    
    input.addEventListener('focus', () => {
        if(!container.classList.contains('active')){    
            openInputToggle();
        }
    });

    autocomplete_load("#globalsearch");
}

function useOverlay(fct) {
    const overlay = document.querySelector("#overlay");
    if (typeof fct === 'function') {
        overlay.addEventListener('click', () => {
            fct();
            // Masque l'overlay après l'exécution de la fonction
            overlay.classList.remove('overlay--open');
        }, { once: true });
    }
    // Affiche l'overlay
    overlay.classList.add('overlay--visible');
}
function closeOverlay(){
    const overlay = document.querySelector("#overlay");
    overlay.classList.remove('overlay--visible');
}