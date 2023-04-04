class Page extends Controller{

    static MODEL_NAME = "page";

    SIZE_SM = 576;
    SIZE_MD = 768;
    SIZE_LG = 993;
    SIZE_XL = 1200;
    SIZE_XXL = 1400;
    SIZE_FL = -1;

    PLACEMENT_START = "start";
    PLACEMENT_END = "end";
    PLACEMENT_BOTTOM = "bottom";
    PLACEMENT_TOP = "top";

    RESPONSIVE = "responsive";

    // Changement de Nom de build à build
    static build(is_modal = true, title, content, size = this.SIZE_MD, show = false){

        switch (size) {
            case "sm":
                size = this.SIZE_SM;    
            break;
            case "md":
                size = this.SIZE_MD;    
            break;
            case "lg":
                size = this.SIZE_lg;    
            break;
            case "xl":
                size = this.SIZE_XL;    
            break;
            case "xxl":
                size = this.SIZE_XXL;    
            break;
            case "FL":
                size = this.SIZE_FL;    
            break;
            default:
                size = this.SIZE_MD;
            break;
        }

        if(size == this.SIZE_XXL && is_modal){size = this.SIZE_XL;} // XXL is not supported by bootstrap modal

        if(is_modal == this.RESPONSIVE){
            if(getSizeScreen() <= this.SIZE_MD) {
                is_modal = false;
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
                case this.SIZE_XL:
                    $("#modal .modal-dialog").addClass("modal-xl");
                break;
                case this.SIZE_LG:
                    $("#modal .modal-dialog").addClass("modal-lg");
                break;
                case this.SIZE_SM:
                    $("#modal .modal-dialog").addClass("modal-sm");
                break;
                case this.SIZE_FL:
                    $("#modal .modal-dialog").addClass("modal-fullscreen");
                break;
            }
    
            $("#modal .modal-title").html("");
            $("#modal .modal-body").html("");
            $("#modal .modal-title").html(title);
            $("#modal .modal-body").html(content);

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
                    html += "<div><button onclick='$(\".undercontent\").remove();' class='btn-text-main size-2-5 position-absolute' style='top:0px;right:0px;z-index:21'><i class='fas fa-times-circle'></i></button></div>";
                    html += "<div>"+title+"</div>";
                    html+= "<div>"+content+"</div>";
                html += "</div>";

            if(show){
                $(".app-content").append(html);
                $('[data-toggle="tooltip"]').tooltip();
            }
        }
    }

    static buildOffcanvas(title, content, placement = this.PLACEMENT_START, show = false, displayBack = false){

        $("#offcanvasbookmark").removeClass("offcanvas-start").removeClass("offcanvas-end").removeClass("offcanvas-bottom").removeClass("offcanvas-top");
        switch (placement) {
            case this.PLACEMENT_START:
                $("#offcanvasbookmark").addClass("offcanvas-start");
            break;
            case this.PLACEMENT_END:
                $("#offcanvasbookmark").addClass("offcanvas-end");
            break;
            case this.PLACEMENT_BOTTOM:
                $("#offcanvasbookmark").addClass("offcanvas-bottom");
            break;
            case this.PLACEMENT_TOP:
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
        if($(".app").hasClass("app-compacted")){
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
        Page.build(Page.RESPONSIVE, "Recherche", html, this.SIZE_SM, true);
        autocomplete_load("#modal #globalsearch");
    }


    constructor() {
        this.table_indexes_row_to_child = [];
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