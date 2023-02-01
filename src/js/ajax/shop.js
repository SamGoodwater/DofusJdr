class Shop {

    static open(uniqid){
        var URL = 'index.php?c=shop&a=getFromUniqid';
    
        $.post(URL,
            {
                uniqid:uniqid
            },
            function(data, status)
            {
                if(data.return == 'success'){
                    Page.build(Page.RESPONSIVE, data.value.title, data.value.visual, Page.SIZE_XL, true);
                } else {
                    MsgAlert("Impossible de récupérer la liste", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }  
    static add(){
        var URL = 'index.php?c=shop&a=add';
        var name = $('#modalAddShop #name').val();
        
        $.post(URL,
            {
                name:name
            },
            function(data, status)
            {
                if(data['script'] != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data['return'] == 'success'){
                    MsgAlert("Ajout d'un hôtel de vente", "L'hôtel de vente " + name + " a bien été ajouté.", "success" , 3000);
                    $('#modalAddShop #name').val("");
                    $('#modalAddShop').modal("hide");
                    $('#table').bootstrapTable('refresh');
                } else {
                    $('#display_error').text(data['error']);
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    static update(uniqid, input, type, value_type = IS_INPUT, fct = ""){
        var URL = 'index.php?c=shop&a=update';
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
                if(data['script'] != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data['return'] == "success"){
                    Shop.updateDisplayRow(uniqid);
                    $("#table").bootstrapTable("refresh");
                    MsgAlert("Mise à jour de l'hôtel de vente", '', "success" , 3000);
                } else {
                    MsgAlert("Echec de la mise à jour", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }  
    static remove(uniqid){
        var URL = 'index.php?c=shop&a=remove';
    
        if (confirm("Etes vous sûr de vouloir supprimer définitivement cet hôtel de vente ?")) {
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
                        MsgAlert("Suppression de l'hôtel de vente", "L'hôtel de  a bien été supprimé.", "success" , 3000);
                        $('#table').bootstrapTable('refresh');
                    } else {
                        $('#display_error').text(data['error']);
                        MsgAlert("Echec de la suppresion", 'Erreur : ' + data['error'], "danger" , 4000);
                    }
                },
                "json"
            ); 
        }
    
    }

    static updateDisplayRow(uniqid) {
        var indexCell = $("#"+uniqid).parent().parent().data("index");

        var URL = 'index.php?c=shop&a=getArrayFromUniqid';
        $.post(URL,
            {
                uniqid:uniqid
            },
            function(data, status)
            {
                if(data.return == 'success'){
                    for (let [index, element] of Object.entries(data.value)) {
                        $("#table").bootstrapTable('updateCell', {index: indexCell, field: index, value: element});
                    }
                } else {
                    MsgAlert("Impossible de récupérer la liste", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }

}