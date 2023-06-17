
class Controller {

    static DISPLAY_CARD = 100;
    static DISPLAY_RESUME = 101;
    static DISPLAY_EDITABLE = 102;
    static DISPLAY_FULL = 103;

    static TABLE_OFFSET = 0;
    static TABLE_LIMIT = 50;

    static MODEL_NAME = "";

    static open(uniqid, display = Controller.DISPLAY_CARD){
        $("#onloadDisplay").show("slow");

        var URL = 'index.php?c='+this.MODEL_NAME+'&a=getFromUniqid';
        $.post(URL,
            {
                uniqid:uniqid,
                display:display
            },
            function(data, status)
            {
                if(data.state){
                    let bubbleId = ""; 
                    if(data.value.bubbleId != ""){
                        bubbleId = data.value.bubbleId;
                    }
                    Page.build(
                        Page.RESPONSIVE,
                        data.value.title, 
                        data.value.visual, 
                        Page.SIZE_XL, 
                        true, 
                        bubbleId, 
                        data.value.linkshare, 
                        {
                            active : data.value.bookmark_active,
                            uniqid : uniqid,
                            classe : data.value.classe
                        }
                    );
                } else {
                    MsgAlert("Impossible de récupérer l'élément", 'Erreur : ' + data.error, "danger" , 4000);
                }

                $("#onloadDisplay").hide("slow");
            },
            "json"
        ); 
    }
    
    static update(uniqid, input, type, value_type = IS_INPUT, fct = "", reopen_modal = false){
        let this_ = this;
        var URL = 'index.php?c='+this.MODEL_NAME+'&a=update';
        var value = 0;
    
        switch (value_type) {
            case IS_INPUT:
                var inp = $(input);
                if(inp.attr("required") && inp.val() == ""){
                    inp.addClass("is-invalid").removeClass('is-valid');
                    MsgAlert("Le champs est obligatoire.", '', "error" , 3000);
                    return false;
                } else {
                    inp.addClass("is-valid").removeClass('is-invalid');
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
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    this_.updateDisplayRow(uniqid);
                    if($("#modal").hasClass("show") && reopen_modal){
                        this_.open(uniqid, Controller.DISPLAY_EDITABLE);
                    }
                    MsgAlert("Mise à jour de l'objet", '', "green" , 3000);
                } else {
                    MsgAlert("Echec de la mise à jour", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    
    static remove(uniqid){
        var URL = 'index.php?c='+this.MODEL_NAME+'&a=remove';
    
        if (confirm("Etes vous sûr de vouloir supprimer définitivement cet objet ?")) {
            $.post(URL,
                {
                    uniqid:uniqid
                },
                function(data, status)
                {
                    if(data.script != ""){
                        $('body').append("<script>"+data.script+"</script>");
                    }
                    if(data.state){
                        MsgAlert("Suppression de l'objet", "L'objet a bien été supprimé.", "green" , 3000);
                        $('#table').bootstrapTable('refresh');
                    } else {
                        $('#display_error').text(data.error);
                        MsgAlert("Echec de la suppresion", 'Erreur : ' + data.error, "danger" , 4000);
                    }
                },
                "json"
            ); 
        }
    
    }

    static updateDisplayRow(uniqid) {
        var indexCell = $("#"+uniqid).parent().parent().data("index");

        var URL = 'index.php?c='+this.MODEL_NAME+'&a=getArrayFromUniqid';
        $.post(URL,{
                uniqid:uniqid
            },
            function(data, status)
            {
                if(data.state){
                    for (let [index, element] of Object.entries(data.value)) {
                        $("#table").bootstrapTable('updateCell', {index: indexCell, field: index, value: element});
                    }
                }
            },
            "json"
        ); 
    }

    static createAndLoadDataBootstrapTable(selectorOptions = []){
        let this_ = this;
        var total = 0;
        var optionurl = "";
        var url = 'index.php?c='+this.MODEL_NAME+'&a=getAll';
        var offset = this.TABLE_OFFSET;
        var limit = this.TABLE_LIMIT;
        var loadedRows = 0;
        let currentRequest = null;

        let spinner = "<div class='spinner-border spinner-border-sm' role='status'><span class='visually-hidden'>Loading...</span></div> Chargement du tableau ";

        let refresh = function refresh() {
            if (currentRequest != null) {
                currentRequest.abort(); // Annuler la requête AJAX en cours
                currentRequest = null; // Réinitialisez la variable currentRequest
            }
            optionurl = "";
            loadedRows = 0;
            offset = 0;
            $('#table').bootstrapTable('removeAll');

            selectorOptions.forEach(element => {
                if($(element.selector).length > 0){
                    switch (element.type) {
                        case IS_CHECKBOX:
                            if ($(element.selector).is(":checked")) {
                                optionurl += '&'+element.name+'=1';
                            }
                        break;
                        case IS_SELECT:
                            if ($(element.selector).val() != "") {
                                optionurl += '&'+element.name+'='+$(element.selector).val();
                            }
                        break;
                        case IS_INPUT:
                            if ($(element.selector).val() != "") {
                                optionurl += '&'+element.name+'='+$(element.selector).val();
                            }
                        break;
                        case IS_LIST_OF_CHECKBOX:
                            var value = "";
                            $(element.selector + " input").each(function(){
                                if ($(this).is(":checked")) {
                                    value += $(this).val() + "|";
                                }
                            });
                            if (value != "") {
                                optionurl += '&'+element.name+'='+value;
                            }
                        break;
                    }
                }
            });

            $.post('index.php?c='+this_.MODEL_NAME+'&a=count'+optionurl,
                {},
                function(data, status){
                    if(data.state){
                        total = data.value;
                        $('.total_obj').text(data.value);
                        loadData();
                    } else {
                        MsgAlert("Impossible d'afficher la page", data.error, "danger" , 7000);
                    }
                },
                "json"
            );
        }


        let loadData = function loadData() {
            $(".fixed-table-toolbar .loading-spinner").html(spinner + Math.round(100 * (loadedRows / total)) + "%");
            var nextUrl = url + optionurl + '&offset=' + offset + '&limit=' + limit;
            $.post(nextUrl,
                {},
                function(data, status){
                    if(data.length == 0){
                        $(".fixed-table-toolbar .loading-spinner").html('');
                        currentRequest = null; // Réinitialisez la variable currentRequest une fois le chargement terminé
                        return;
                    }
                    if($('#table').length > 0){
                        $('#table').bootstrapTable('append', data);
                        loadedRows += data.length;
                        if (loadedRows < total) {
                            offset += limit;
                            loadData();
                        } else {
                            $(".fixed-table-toolbar .loading-spinner").html('');
                            currentRequest = null; // Réinitialisez la variable currentRequest une fois le chargement terminé
                        }
                    } else {
                        $(".fixed-table-toolbar .loading-spinner").html('');
                        currentRequest.abort(); // Annuler la requête AJAX en cours
                        currentRequest = null; // Réinitialisez la variable currentRequest une fois le chargement terminé
                    }
                },
                "json"
            );
        }
        function createTable(){
            $('#table').bootstrapTable({
                onDblClickRow: function (row, $element, field) {
                    this_.open(row.uniqid);
                    $('#table').bootstrapTable('collapseAllRows');
                },
                onLoadSuccess:refresh(),
                exportTypes: ["pdf","doc","xlsx","xls","xml", "json", "png", "sql", "txt", "tsv"]
            });

            $('#table tbody').on('click', function (e) {
                if($(e.target).attr('class').includes("bootstrap-table-filter-control-")){
                    $(e.target).blur();
                }
            });
            $(".bootstrap-table .fixed-table-toolbar [name='refresh']").click(function () {
                refresh();
            });
            selectorOptions.forEach(element => {
                if($(element.selector).length > 0){
                    $(element.selector).change(function () {
                        refresh();
                    });
                }
            });
            $(".fixed-table-toolbar").append("<div class='loading-spinner text-grey-d-2' style='top:15px;left:15px;position:relative;z-index:-1;'></div>");
        }
        
        createTable();
    }

}