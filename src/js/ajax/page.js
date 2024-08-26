class Page extends Controller{

    static MODEL_NAME = "page";

    static SIZE_SM = 576;
    static SIZE_MD = 768;
    static SIZE_LG = 993;
    static SIZE_XL = 1200;
    static SIZE_XXL = 1400;
    static SIZE_FL = -1;

    static PLACEMENT_START = "start";
    static PLACEMENT_END = "end";
    static PLACEMENT_BOTTOM = "bottom";
    static PLACEMENT_TOP = "top";

    static RESPONSIVE = "responsive";
    
    constructor() {
        this.table_indexes_row_to_child = [];
    }

    // Function obsolète
    static buildOld(is_modal = true, title, content, size = Page.SIZE_MD, show = false, bubbleId = null, linkShare = "", bookmark = {}){
        if(show){
            $('#modal').modal('hide');
        }

        switch (size) {
            case "sm":
                size = Page.SIZE_SM;    
            break;
            case "md":
                size = Page.SIZE_MD;    
            break;
            case "lg":
                size = Page.SIZE_LG;    
            break;
            case "xl":
                size = Page.SIZE_XL;
            break;
            case "xxl":
                size = Page.SIZE_XXL;    
            break;
            case "fl":
                size = Page.SIZE_FL;    
            break;
        }

        if(size >= Page.SIZE_XXL && is_modal){size = Page.SIZE_XL;} // XXL is not supported by bootstrap modal

        let clone = null;
        if (content.jquery) {
            // La variable est un objet jQuery (sélecteur)
            clone = content.clone();
            clone.show();
          } else {
            // La variable ne contient pas un objet jQuery (probablement du code HTML)
            clone = content;
          }

        if(is_modal == Page.RESPONSIVE){
            if(getSizeScreen() <= Page.SIZE_MD) {
                is_modal = true;
                size = Page.SIZE_FL;
            } else {
                is_modal = true;
            }
        }
        
        if(is_modal){
            $("#modal .modal-dialog").removeClass("modal-xl");
            $("#modal .modal-dialog").removeClass("modal-lg");
            $("#modal .modal-dialog").removeClass("modal-sm");
            $("#modal .modal-dialog").removeClass("modal-fullscreen");
            switch (size) {
                case Page.SIZE_XL:
                    $("#modal .modal-dialog").addClass("modal-xl");
                break;
                case Page.SIZE_LG:
                    $("#modal .modal-dialog").addClass("modal-lg");
                break;
                case Page.SIZE_SM:
                    $("#modal .modal-dialog").addClass("modal-sm");
                break;
                case Page.SIZE_FL:
                    $("#modal .modal-dialog").addClass("modal-fullscreen");
                break;
            }
    
            $("#modal .modal-title").html("");
            $("#modal .modal-body").html("");
            $("#modal .modal-title").html(title);
            $("#modal .modal-body").html(clone);

            if(linkShare != ""){
                $("#modal .modal__share_object").attr("onclick", "copyToClipboard('"+document.location.href+linkShare+"')");
                $("#modal .modal__share_object").show();
            } else {
                $("#modal .modal__share_object").hide();
            }

            if(bubbleId != null){
                $(".modal__bubbleshortcut_toggle").show();

                if(Bubbleshortcut.existFromBubbleId(bubbleId)){
                    $(".modal__bubbleshortcut_toggle").addClass("listed");
                    $(".modal__bubbleshortcut_toggle").attr("onclick", "Bubbleshortcut.remove('"+bubbleId+"')");
                } else {
                    $(".modal__bubbleshortcut_toggle").removeClass("listed");
                    $(".modal__bubbleshortcut_toggle").attr("onclick", "Bubbleshortcut.update('"+bubbleId+"')");
                }
            } else {
                $(".modal__bubbleshortcut_toggle").hide();
            }
            
            let bookmark_obj = $(".modal__bookmark_toggle");
            if(bookmark != null){
                if( bookmark['classe'] !== "undefined" && bookmark['classe'] != "" &&
                    bookmark['uniqid'] !== "undefined" && bookmark['uniqid'] != "" &&
                    bookmark['active'] !== "undefined" 
                ){
                    bookmark_obj.show();
                    bookmark_obj.data('classe', bookmark.classe);
                    bookmark_obj.data('uniqid', bookmark.uniqid);
                    let i = bookmark_obj.find('i');
                    if(bookmark.active){
                        i.removeClass("far").removeClass("fa-regular");
                        i.addClass("fa-solid");
                        bookmark_obj.attr('title', "Retirer des favoris");
                    }else{
                        i.removeClass("fas").removeClass("fa-solid");
                        i.addClass("fa-regular");
                        bookmark_obj.attr('title', "Ajouter aux favoris");
                    }
                } else {
                    bookmark_obj.hide();
                }
            } else {
                bookmark_obj.hide();
            }

            if(show){
                $('#modal').modal('hide');
                $('#modal').modal('show');
                $('[data-toggle="tooltip"]').tooltip();
            } else {
                $('#modal').modal('hide');
                $('[data-toggle="tooltip"]').tooltip();
            }
        } else {
            let html = "<div class='undercontent'>";
                    html += "<div><button onclick='$(\".undercontent\").remove();' class='btn-text-main size-2-5 position-absolute' style='top:0px;right:0px;z-index:21'><i class='fa-solid fa-times-circle'></i></button></div>";
                    html += "<div>"+title+"</div>";
                    html+= "<div>"+clone+"</div>";
                html += "</div>";

            if(show){
                $(".app-content .undercontent").remove();
                $(".app-content").append(html);
                $('[data-toggle="tooltip"]').tooltip();
            }
        }
        ViewManager.initDisplay();
        Page.initPageNavigation();
    }

    /*
     option {
        bubbleId : null,
        linkShare : "",
        bookmark : {
        'classe' : null,
        'uniqid' : null,
        'state' : null
        },
        edit : false,
        remove : false,
    }    

    Les crochets {} sont obligatoires pour nommé les paramètres.
    */
    static build({target = "modal", title, content, options = {}, size = Page.SIZE_MD, show = false} = {}){
        // nettoyage de l'interface et des évenements
            Page.closeBuild();

        // Conversion de SIZE
            switch (size) {
                case "sm":
                    size = Page.SIZE_SM;    
                break;
                case "md":
                    size = Page.SIZE_MD;    
                break;
                case "lg":
                    size = Page.SIZE_LG;    
                break;
                case "xl":
                    size = Page.SIZE_XL;
                break;
                case "xxl":
                    size = Page.SIZE_XXL;    
                break;
                case "fl":
                    size = Page.SIZE_FL;    
                break;
                case "responsive":
                    size = Page.RESPONSIVE;
                break;
            }

        // Initialisation des constances
            const object_viewer_container = document.querySelector(".object__viewer__container.object__viewer__container--template");
            const obj_viewer_duplicate = object_viewer_container.cloneNode(true);
            obj_viewer_duplicate.classList.remove("object__viewer__container--template");
            obj_viewer_duplicate.classList.add("object__viewer__container--visible")

            const modal_obj_insert = document.querySelector("#modal .modal__obj-viewer-insert");

            const object_title = obj_viewer_duplicate.querySelector(".modal-title");
            const object_content = obj_viewer_duplicate.querySelector(".modal-body");
            const object_btn_close = obj_viewer_duplicate.querySelector(".btn-close");
            const option_share = obj_viewer_duplicate.querySelector(".modal__share_object");
            const option_remove = obj_viewer_duplicate.querySelector(".modal__remove_object");
            const option_edit = obj_viewer_duplicate.querySelector(".modal__edit_object");
            const option_bubble = obj_viewer_duplicate.querySelector(".modal__bubbleshortcut_toggle");
            const option_bookmark = obj_viewer_duplicate.querySelector(".modal__bookmark_toggle");

        // Récupération de la cible
            let targetElement = document.querySelector("#modal .modal__obj-viewer-insert");
            let is_modal = true;
            let is_fullscreen = false;
            if(target == null || target == ""){target = "modal";}

            if(target == "modal"){
                targetElement = document.querySelector("#modal .modal__obj-viewer-insert");
                is_modal = true;
                is_fullscreen = false;

            } else if(target == "fullscreen" || target == "full" || target == "fl"){
                document.querySelector(".app-content .undercontent").remove();
                let div = document.createElement("div");
                div.classList.add("undercontent");
                document.querySelector(".app-content").appendChild(div);
                targetElement = document.querySelector(".app-content .undercontent");
                is_modal = false;
                is_fullscreen = true;

            } else {
                targetElement = document.querySelector(target);
                is_modal = false;
                is_fullscreen = false;
            }

            if(targetElement.length == 0){ 
                MsgAlert("Erreur", "Impossible de trouver la cible", "danger", 5000);
                return; 
            }

            let clone = null;
            if (content.jquery) {
                // La variable est un objet jQuery (sélecteur)
                clone = content.clone();
                clone.show();
            } else {
                // La variable ne contient pas un objet jQuery (probablement du code HTML)
                clone = content;
            }

        // Ecriture des contenus
            object_title.innerHTML = title;
            object_content.innerHTML = clone;;
            object_btn_close.addEventListener('click', Page.closeBuild);
        
        // Gestion des options
            option_share.style.display = "none";
            option_remove.style.display = "none";
            option_edit.style.display = "none";
            option_bubble.style.display = "none";
            option_bookmark.style.display = "none";

            if(options != null && options != undefined){

                // Gestion de l'option remove
                    if(options.remove != null && options.remove != undefined && options.remove != ""){
                        option_remove.style.display = "block";
                        option_remove.addEventListener('click', () => {
                             let func = new Function(options.remove);
                             func();
                        });
                    }
                // Gestion de l'option edit
                    if(options.edit != null && options.edit != undefined && options.edit != ""){
                        if(options.edit.includes('EDITABLE')){
                            option_edit.setAttribute('title', "Modifier cette objet");
                            option_edit.querySelector('i').classList.add("fa-pen-to-square");
                            option_edit.querySelector('i').classList.remove("fa-eye");
                        } else {
                            option_edit.setAttribute('title', "Visualiser cette objet");
                            option_edit.querySelector('i').classList.add("fa-eye");
                            option_edit.querySelector('i').classList.remove("fa-pen-to-square");
                        }
                        option_edit.style.display = "block";
                        option_edit.addEventListener('click', () => {
                            let func = new Function(options.edit);
                            func();
                         });
                    }
                // Gestion de l'option share
                    if(options.linkshare != null && options.linkshare != undefined && options.linkshare != ""){
                        option_share.style.display = "block";
                        option_share.addEventListener('click', () => { copyToClipboard(document.location.href + options.linkshare) });
                    }
                // Gestion de l'option bubble
                    if(options.bubbleid != null && options.bubbleid != undefined && options.bubbleid != ""){
                        option_bubble.style.display = "block";
                        if(Bubbleshortcut.existFromBubbleId(options.bubbleid)){
                            option_bubble.classList.add("listed");
                            option_bubble.addEventListener('click', () => { Bubbleshortcut.remove(options.bubbleid) });
                        } else {
                            option_bubble.classList.remove("listed");
                            option_bubble.addEventListener('click', () => { Bubbleshortcut.update(options.bubbleid) });
                        }
                    }
                // Gestion de l'option bookmark
                    if(options.bookmark != null && options.bookmark != undefined){
                        if( options.bookmark['classe'] != "undefined" && options.bookmark['classe'] != "" &&
                            options.bookmark['uniqid'] != "undefined" && options.bookmark['uniqid'] != "" &&
                            options.bookmark['active'] != "undefined" 
                        ){
                            option_bookmark.style.display = "block";
                            option_bookmark.setAttribute('data-classe', options.bookmark.classe);
                            option_bookmark.setAttribute('data-uniqid', options.bookmark.uniqid);
                            let icon = option_bookmark.querySelector('i');
                            if(options.bookmark.active){
                                icon.classList.remove("far");
                                icon.classList.remove("fa-regular");
                                icon.classList.add("fa-solid");
                                option_bookmark.setAttribute('title', "Retirer des favoris");
                            }else{
                                icon.classList.remove("fas");
                                icon.classList.remove("fa-solid");
                                icon.classList.add("fa-regular");
                                option_bookmark.setAttribute('title', "Ajouter aux favoris");
                            }
                        }
                    }

            }

        // Affichage
            targetElement.appendChild(obj_viewer_duplicate)

            if(is_modal){ // MODAL

                if(size >= Page.SIZE_XXL && is_modal){size = Page.SIZE_XL;} // XXL is not supported by bootstrap modal
        
                modal_obj_insert.classList.remove("modal-xl");
                modal_obj_insert.classList.remove("modal-lg");
                modal_obj_insert.classList.remove("modal-sm");
                modal_obj_insert.classList.remove("modal-fullscreen");

                if(size == Page.RESPONSIVE){
                    if(getSizeScreen() <= Page.SIZE_MD) {
                        size = Page.SIZE_FL;
                    }
                }
                switch (size) {
                    case Page.SIZE_XL:
                        modal_obj_insert.classList.add("modal-xl");
                    break;
                    case Page.SIZE_LG:
                        modal_obj_insert.classList.add("modal-lg");
                    break;
                    case Page.SIZE_SM:
                        modal_obj_insert.classList.add("modal-sm");
                    break;
                    case Page.SIZE_FL:
                        modal_obj_insert.classList.add("modal-fullscreen");
                    break;
                }
                
                if(show){
                    $('#modal').modal('show');
                }

            } else { // FULLSCREEN

                if(show){
                    targetElement.style.display = "block";
                }

            }

            ViewManager.initDisplay();
            Page.initPageNavigation();
            DisplayUI.update();
    }

    static closeBuild(){
        const modal_obj_insert = document.querySelector("#modal .modal__obj-viewer-insert");
        modal_obj_insert.innerHTML = "";
  
        $('#modal').modal('hide');

        modal_obj_insert.classList.remove("modal-xl");
        modal_obj_insert.classList.remove("modal-lg");
        modal_obj_insert.classList.remove("modal-sm");
        modal_obj_insert.classList.remove("modal-fullscreen");
    }

    static buildOffcanvas(title, content, placement = Page.PLACEMENT_START, show = false, displayBack = false){

        $("#offcanvasbookmark").removeClass("offcanvas-start").removeClass("offcanvas-end").removeClass("offcanvas-bottom").removeClass("offcanvas-top");
        switch (placement) {
            case Page.PLACEMENT_START:
                $("#offcanvasbookmark").addClass("offcanvas-start");
            break;
            case Page.PLACEMENT_END:
                $("#offcanvasbookmark").addClass("offcanvas-end");
            break;
            case Page.PLACEMENT_BOTTOM:
                $("#offcanvasbookmark").addClass("offcanvas-bottom");
            break;
            case Page.PLACEMENT_TOP:
                $("#offcanvasbookmark").addClass("offcanvas-top");
            break;
            default:
                $("#offcanvasbookmark").addClass("offcanvas-start");
        }
        if(show){
            $('#offcanvasbookmark').offcanvas('show');
        }

        if(displayBack){
            $("#offcanvasbookmark #back-bookmark").show();
        } else {
            $("#offcanvasbookmark #back-bookmark").hide();
        }

        $("#offcanvasbookmark .offcanvas-title").html(title);
        $("#offcanvasbookmark .offcanvas-body #offcanvas-content").html(content);
        ViewManager.initDisplay();
    }

    static offCanvasFullscreen(){
        $("#offcanvasbookmark .offcanvas-body").css("width", "");
        $("#offcanvasbookmark .offcanvas-body").css("max-width", "");
        if($("#offcanvasbookmark").hasClass("offcanvas-fullscreen")){
            $("#offcanvasbookmark").removeClass("offcanvas-fullscreen");
            $("#offcanvasbookmark").css("width", "");
            $("#offcanvasbookmark").css("max-width", "");
            $("#offcanvasbookmark #btn-fullscreen i").removeClass("fa-compress").addClass("fa-expand");
            $("#offcanvasbookmark #btn-fullscreen i").attr("title", "Agrandir");
        } else {
            $("#offcanvasbookmark").addClass("offcanvas-fullscreen");
            $("#offcanvasbookmark").css("width", "100%");
            $("#offcanvasbookmark #btn-fullscreen i").removeClass("fa-expand").addClass("fa-compress");
            $("#offcanvasbookmark #btn-fullscreen i").attr("title", "Réduire");
        }
    }

    static show(url_name, settings){
        $("#onloadDisplay").show("slow");
        let spinner = "<div class='d-flex justify-content-center'><div class='text-main-d-2 spinner-border' role='status'><span class='visually-hidden'>Loading...</span></div></div>";
        $(".app-content #content").html(spinner);
        $(".app-toolbar #title").html("");

        const app = document.querySelector('.app');
        if (app.classList.contains('app-compacted')) {
            toggleMenu(true);
        }

        let URL = 'index.php?c=page&a=show';
        $.post(URL,
            {
                url_name:url_name,
                settings:settings,
                url_name:url_name
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    $(".app-content #content").hide();
                    $(".app-toolbar #title").hide();
                    $(".app-content #content").html("");
                    $(".app-toolbar #title").html("");
                    $(".app-content #content").html(data.html);
                    $(".app-toolbar #title").html(data.title);
                    document.title = data.title;
                    $(".app-content #content").show("fold");
                    $(".app-toolbar #title").show("fade");
                    Page.build({
                        target : "modal", 
                        title : data.modal_title,
                        content : data.modal_html,
                        options : {
                            remove : null,
                            edit : null,
                            linkShare : null,
                            bookmark : {
                                classe : null,
                                uniqid : null,
                                active : null
                            },
                            bubbleId : null
                        }, 
                        size : Page.RESPONSIVE, 
                        show : false
                    });
                    let url = url_name;
                    if(settings !="" && settings != undefined && settings != null){url += "/" + settings;}
                    window.history.pushState({path:url},'',url);
                    ViewManager.initDisplay();
                } else {
                    MsgAlert("Impossible d'afficher la page", data.error, "danger" , 7000);
                }
                $("#onloadDisplay").hide("slow");
            },
            "json"
        ); 
    }

    static add(){
        let URL = 'index.php?c=page&a=add';
        let name = $('.addpage #name').val();
        let category = $('.addpage #category').val();
        let is_dropdown = 0;
        let value = 0;
        if ($('.addpage #switchdropdownadd').is(":checked")) {
            value = 1;
        }
        let public_ = 0;
        if ($('.addpage #switchpublicadd').is(":checked")) {
            public_ = 1;
        }
        let is_editable = 0;
        if ($('.addpage #switcheditableadd').is(":checked")) {
            is_editable = 1;
        }
        
        $.post(URL,
            {
                name:name,
                category:category,
                is_dropdown:is_dropdown,
                public:public_,
                is_editable:is_editable
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout d'une page", 'La page ' + name + ' a bien été ajouté.', "green" , 3000);
                    $('.addpage #name').val("");
                    window.history.pushState({path:data.link},'',data.link);
                    location.reload();
                } else {
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }

    static updateOrder_num(){
        let arr = {};
        $('#sortable .sortables').each(function(index, value) {
            arr[$(this).data("uniqid")] = index;
        });

        let URL = 'index.php?c=page&a=updateorder';
        $.post(URL,
            {
                arr:arr
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data.state){
                    MsgAlert("L'ordre a été mise à jour","", "green" , 3000);
                    $('#btn-update-order').hide();
                } else {
                    MsgAlert("Echec de la mise à jour", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }

    showRow(uniqid) {
        let table = $('#table');
        let tr = $("#row-"+uniqid);
        let icon = $("#row-"+uniqid+" .dropdown-child i");
        
        let indexes = [];
        let option = "";

        if(typeof this.table_indexes_row_to_child[uniqid] === 'undefined'){
            this.table_indexes_row_to_child[uniqid] = 
            {
                'extended' : true,
                'index' : []
            };
        }

        document.querySelectorAll('.group-'+tr.data('uniqid')+'.row_edit_page_child').forEach(element => {
            indexes.push($(element).data('index'));
        });
        if(indexes == "" && this.table_indexes_row_to_child[uniqid]['index'] != ""){
            indexes = this.table_indexes_row_to_child[uniqid]['index'];
        }
        this.table_indexes_row_to_child[uniqid]['index'] = indexes;
        

        if(this.table_indexes_row_to_child[uniqid]['extended'] == false){
            // Va être étendu
            this.table_indexes_row_to_child[uniqid]['extended'] = true;
            option = 'showRow';
            icon.removeClass('fa-caret-right').addClass("fa-sort-down");
        } else {
            //  Va être replier
            this.table_indexes_row_to_child[uniqid]['extended'] = false;
            option = "hideRow";
            icon.removeClass('fa-sort-down').addClass("fa-caret-right");
        }
        indexes.forEach(element => {
            if(element != ""){
                table.bootstrapTable(option, {
                    index: element
                }) 
            }
        });
    }

    // Evenements et fonctions de la page-navigation
    static initPageNavigation() {
        const container = document.querySelector('.page-navigation');
        if (container) {
            const btnMinimize = document.querySelector('.page-navigation__top__minimize');
            const selectItemText = document.querySelector('.page-navigation__top__select-item');
            const titles = [...document.querySelectorAll('.section-title')];
            let posTitles = titles.map(title => title.offsetTop);
            const links = [...document.querySelectorAll('.page-navigation__menu__item')];
    
            let currentIndex = null;

            // SCROLL TO SECTION
            links.forEach((link, index) => {
                link.addEventListener('click', () => {
                    window.scrollTo({ top: posTitles[index], behavior: 'smooth' });
                });
            });
    
            // SCROLL
            window.addEventListener('scroll', () => {
                let index = null;
                const { scrollTop } = document.documentElement;
                if (scrollTop <= 3) {
                    index = -1;
                    if(index != currentIndex){
                        changeActiveItem(index);
                        currentIndex = index;
                    }
                } else {
                    posTitles.forEach(pos => {
                        if (scrollTop >= pos) {
                            index = posTitles.indexOf(pos);
                        }
                    });
                    if (index !== null && index != currentIndex) {
                        changeActiveItem(index);
                        if (container.classList.contains('page-navigation--minimized')) {
                            container.classList.remove('page-navigation--minimized');
                            setTimeout(() => {
                                container.classList.add('page-navigation--minimized');
                            }, 1000);
                        }
                        currentIndex = index;
                    }
                }
            });
    
            const changeActiveItem = function(index = -1) {
                clearActiveItem();
                let title = "Plan de la page";
                if (index > -1 && index < links.length) {
                    title = links[index].querySelector('.page-navigation__menu__item__link__text').innerText;
                    links[index].classList.add('page-navigation__menu__item--active');
                }
                selectItemText.textContent = title;
                selectItemText.classList.remove('page-navigation__top__select-item--transition');
                setTimeout(() => {
                    selectItemText.classList.add('page-navigation__top__select-item--transition');
                }, 50);
            };
    
            const clearActiveItem = function() {
                selectItemText.textContent = "";
                links.forEach(link => link.classList.remove('page-navigation__menu__item--active'));
            };
    
            // OPEN MENU
            let isMinimized = false;
            const toggleMenu = function() {
                if(container.classList.contains('page-navigation--minimized')) {
                    isMinimized = true;
                }

                container.classList.toggle('page-navigation--open');
                if (container.classList.contains('page-navigation--open')) { // Open
                    container.setAttribute('aria-label', 'Cacher la navigation');
                    container.setAttribute('title', 'Cacher la navigation');
                    if(isMinimized){
                        container.classList.remove('page-navigation--minimized');
                    }
                } else { // Close
                    container.setAttribute('aria-label', 'Afficher la navigation');
                    container.setAttribute('title', 'Afficher la navigation');
                    if(isMinimized){
                        container.classList.add('page-navigation--minimized');
                    }
                }
            };
            container.addEventListener('mouseenter', toggleMenu);
            container.addEventListener('mouseleave', toggleMenu);
    
            // MINIMIZE MENU
            const toggleMenuMinimize = function() {
                container.classList.toggle('page-navigation--minimized');
                if (container.classList.contains('page-navigation--minimized')) {
                    btnMinimize.setAttribute('aria-label', 'Agrandir le menu de navigation');
                    btnMinimize.setAttribute('title', 'Agrandir le menu de navigation');
                    btnMinimize.innerHTML = '<i class="fa-solid fa-up-right-and-down-left-from-center"></i>';
                    selectItemText.style.display = "block";
                } else {
                    btnMinimize.setAttribute('aria-label', 'Réduire le menu de navigation');
                    btnMinimize.setAttribute('title', 'Réduire le menu de navigation');
                    btnMinimize.innerHTML = '<i class="fa-solid fa-down-left-and-up-right-to-center"></i>';
                    selectItemText.style.display = "none";
                }
            };
            btnMinimize.addEventListener('click', toggleMenuMinimize);
        }
    }
    
}