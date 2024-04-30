function initInterface() {
    $(window).resize(function() {
        compactMenu();
    });
    compactMenu();
    initGlobalSearch();

    // Lorque le menu est ouvert en mode tablette ou mobile, et que l'on clique à l'extérieur du menu, alors le menu se ferme
    // const container = document.querySelector('.app');
    // document.addEventListener('click', function(e) {
    //     if ($(window).width() <= BREAKPOINT_TABLET && !$(container).is(e.target) && $(container).has(e.target).length === 0) {
    //         if ($(".app").hasClass("app-extend")) {
    //             toogleMenu();
    //         }
    //     }
    // }, false);
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
                toogleMenu();
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

function switchConnectInscript(tab, btn){
    $(".user__modal_tab").hide();
    $(".user__modal_connexion .user__modal_btn-switch").css("border-bottom", "none");
    $(tab).show();
    $(btn).css("border-bottom", "solid 1px var(--main-d-2)");
}