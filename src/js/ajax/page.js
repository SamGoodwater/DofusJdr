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

    // Changement de Nom de build à build
    static build(is_modal = true, title, content, size = Page.SIZE_MD, show = false, bubbleId = null, linkShare = "", bookmark = {}){
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

        var clone = null;
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
            
            let bookmark_obj = $(".modal__bookmark_toogle");
            if(bookmark != null){
                if( bookmark['classe'] !== "undefined" && bookmark['classe'] != "" &&
                    bookmark['uniqid'] !== "undefined" && bookmark['uniqid'] != "" &&
                    bookmark['active'] !== "undefined" 
                ){
                    bookmark_obj.show();
                    bookmark_obj.data('classe', bookmark.classe);
                    bookmark_obj.data('uniqid', bookmark.uniqid);
                    var i = bookmark_obj.find('i');
                    if(bookmark.active){
                        i.removeClass("far");
                        i.addClass("fas");
                        bookmark_obj.attr('title', "Retirer des favoris");
                    }else{
                        i.removeClass("fas");
                        i.addClass("far");
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
        eventManager();
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
        eventManager();
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
        var spinner = "<div class='d-flex justify-content-center'><div class='text-main-d-2 spinner-border' role='status'><span class='visually-hidden'>Loading...</span></div></div>";
        $(".app-content #content").html(spinner);
        $(".app-toolbar #title").html("");

        if (isMobileSize()) {
            toogleMenu(true);
        }

        var URL = 'index.php?c=page&a=show';
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
                    Page.build(Page.RESPONSIVE, data.modal_title, data.modal_html);
                    var url = url_name;
                    if(settings !="" && settings != undefined && settings != null){url += "/" + settings;}
                    window.history.pushState({path:url},'',url);
                    eventManager();
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

    static showSearchbar(){
        var html = $("#globalsearch").parent().html();
        Page.build(Page.RESPONSIVE, "Recherche", html, Page.SIZE_SM, true);
        autocomplete_load("#modal #globalsearch");
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

}