class Page {

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
                $(".dashboard-content").append(html);
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
        $("#offcanvasbookmark .offcanvas-body").html(content);
    }

    static offCanvasFullscreen(){
        if($("#offcanvasbookmark").hasClass("offcanvas-fullscreen")){
            $("#offcanvasbookmark").removeClass("offcanvas-fullscreen");
            $("#offcanvasbookmark #btn-fullscreen i").removeClass("fa-compress").addClass("fa-expand")
        } else {
            $("#offcanvasbookmark").addClass("offcanvas-fullscreen");
            $("#offcanvasbookmark #btn-fullscreen i").removeClass("fa-expand").addClass("fa-compress")
        }
    }

    static show(url_name, settings){
        var spinner = "<div class='d-flex justify-content-center'><div class='spinner-border' role='status'><span class='visually-hidden'>Loading...</span></div></div>";
        $(".dashboard-app #content").html(spinner);
        $(".dashboard-app #title").html("");
        $(".dashboard-nav").removeClass("mobile-show");

        var URL = 'index.php?c=page&a=show';
        $.post(URL,
            {
                url_name:url_name,
                settings:settings,
                url_name:url_name
            },
            function(data, status)
            {
                if(data['script'] != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data.return == 'success'){
                    $(".dashboard-app #content").hide();
                    $(".dashboard-app #title").hide();
                    $(".dashboard-app #content").html("");
                    $(".dashboard-app #title").html("");
                    $(".dashboard-app #content").html(data.html);
                    $(".dashboard-app #title").html(data.title);
                    document.title = data.title;
                    $(".dashboard-app #content").show("fold");
                    $(".dashboard-app #title").show("fade");
                    if(data)
                    Page.build(Page.RESPONSIVE, data.modal_title, data.modal_html);
                    var hash = url_name;
                    if(settings !="" && settings != undefined && settings != null){hash += "&" + settings;}
                    window.location.hash = hash;
                } else {
                    MsgAlert("Impossible d'afficher la page", data.error, "danger" , 7000);
                }
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
                    MsgAlert("Ajout d'une page", 'La page ' + name + ' a bien été ajouté.', "success" , 3000);
                    $('.addpage #name').val("");
                    window.location.hash = data.link;
                    location.reload();
                } else {
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    
    static update(uniqid, input, type, value_type = IS_INPUT, fct = ""){
        var URL = 'index.php?c=page&a=update';
        var value = 0;
    
        switch (value_type) {
            case IS_INPUT:
                var inp = $(input);
                if(inp.attr("required") && inp.val() == ""){
                    inp.addClass("is-invalid").removeClass('is-valid');
                    $('#PageModalModify #display_error').text("Le champs est obligatoire");
                    MsgAlert("Le champs est obligatoire.", '', "error" , 3000);
                    return false;
                } else {
                    inp.addClass("is-valid").removeClass('is-invalid');
                    $('#PageModalModify #display_error').text("");
                    value = inp.val();
                }
            break;
            case IS_VALUE:
                value = input;
            break;
            case IS_CHECKBOX:
                if ($(input).is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            break;
            case IS_CKEDITOR:
                value = CKEDITOR.instances[type+id].getData();
            break;
            case IS_PATH_FILE:
                if(file_select.extention != "dir" && file_select.path != ""){
                    value = file_select.dirname + file_select.name;
                    $("#showFile_"+type+" a").attr('href', value);
                    $("#showFile_"+type+" a div").css('background-image', "url('"+value+"')");
                } else {
                    alert('Aucun fichier sélectionné');
                }
            break;
            default:
                MsgAlert("Aucun type de valeur spécifié", '', "error" , 3000);
                return false;
        }
    
        if(fct !=""){
            value = fct(value);
            if(value == '***error***'){
                MsgAlert("Echec de la mise à jour", "Erreur d'éxécuttion de la fonction", "danger" , 4000);
                $('#PageModalModify #display_error').text("Erreur d'éxécuttion de la fonction");
                return false;
            }
            $(input).val(value);
        }
    
        $.post(URL,
            {
                uniqid:uniqid,
                value:value,
                type:type
            },
            function(data, status)
            {
                if(data['script'] != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data['return'] == "success"){
                    location.reload();
                    MsgAlert("Mise à jour de la page", '', "success" , 3000);
                } else {
                    MsgAlert("Echec de la mise à jour", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    
    static remove(uniqid){
        var URL = 'index.php?c=page&a=remove';
    
        if (confirm("Etes vous sûr de vouloir supprimer définitivement cette page ?")) {
            $.post(URL,
                {
                    uniqid:uniqid
                },
                function(data, status)
                {
                    if(data['script'] != ""){
                        $('body').append("<script>"+data['script']+"</script>");
                    }
                    if(data['return'] == 'success'){
                        MsgAlert("Suppression de la page", 'La page a bien été supprimé.', "success" , 3000);
                        location.reload();
                    } else {
                        $('#display_error').text(data['error']);
                        MsgAlert("Echec de la suppresion", 'Erreur : ' + data['error'], "danger" , 4000);
                    }
                },
                "json"
            ); 
        }
    
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
                    MsgAlert("L'ordre a été mise à jour","", "success" , 3000);
                    $('#btn-update-order').hide();
                } else {
                    MsgAlert("Echec de la mise à jour", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
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