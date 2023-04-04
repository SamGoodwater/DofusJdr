
class Controller {

    static DISPLAY_CARD = 100;
    static DISPLAY_RESUME = 101;
    static DISPLAY_EDITABLE = 102;
    static DISPLAY_FULL = 103;

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
                    Page.build(Page.RESPONSIVE, data.value.title, data.value.visual, Page.SIZE_XL, true);
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

}