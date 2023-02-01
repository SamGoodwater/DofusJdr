class Classe {

    static open(uniqid)
    {
        var URL = 'index.php?c=classe&a=getFromUniqid';
    
        $.post(URL,
            {
                uniqid:uniqid
            },
            function(data, status)
            {
                if(data.return == 'success'){
                    Page.build(Page.RESPONSIVE, data.value.title, data.value.visual, Page.SIZE_XL, true);
                } else {
                    MsgAlert("Impossible de récupérer la classe", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    
    static add(){
        var URL = 'index.php?c=classe&a=add';
        var name = $('#modalAdd #name').val();
        var weapons = $('#modalAdd #weapons').val();
        
        $.post(URL,
            {
                name:name,
                weapons_of_choice:weapons
            },
            function(data, status)
            {
                if(data['script'] != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data['return'] == 'success'){
                    MsgAlert("Ajout de la classe", 'La classe ' + name + ' a bien été ajouté.', "success" , 3000);
                    $('#modalAdd #weapons').val("");
                    $('#modalAdd #name').val("");
                    $('#modalAdd').modal('hide');
                    $("#table").bootstrapTable("refresh");
                } else {
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    
    static update(uniqid, input, type, value_type = IS_INPUT, fct = ""){
        var URL = 'index.php?c=classe&a=update';
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
                    Classe.updateDisplayRow(uniqid);
                    MsgAlert("Mise à jour de la page", '', "success" , 3000);
                } else {
                    MsgAlert("Echec de la mise à jour", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    
    // static remove(uniqid){
    //     var URL = 'index.php?c=spell&a=remove';
    
    //     if (confirm("Etes vous sûr de vouloir supprimer définitivement cette page ?")) {
    //         $.post(URL,
    //             {
    //                 uniqid:uniqid
    //             },
    //             function(data, status)
    //             {
    //                 if(data['script'] != ""){
    //                     $('body').append("<script>"+data['script']+"</script>");
    //                 }
    //                 if(data['return'] == 'success'){
    //                     MsgAlert("Suppression de la page", 'La page a bien été supprimé.', "success" , 3000);
    //                     location.reload();
    //                 } else {
    //                     $('#display_error').text(data['error']);
    //                     MsgAlert("Echec de la suppresion", 'Erreur : ' + data['error'], "danger" , 4000);
    //                 }
    //             },
    //             "json"
    //         ); 
    //     }
    
    // }

    static updateDisplayRow(uniqid) {
        var indexCell = $("#"+uniqid).parent().parent().data("index");

        var URL = 'index.php?c=classe&a=getArrayFromUniqid';
        $.post(URL,
            {
                uniqid:uniqid
            },
            function(data, status)
            {
                if(data.return == 'success'){
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'timestamp_updated', value: data.value.timestamp_updated});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'name', value: data.value.name});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'description_fast', value: data.value.description_fast});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'description', value: data.value.description});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'life', value: data.value.life});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'specificity', value: data.value.specificity});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'weapons_of_choice', value: data.value.weapons_of_choice});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'trait', value: data.value.trait});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'usable', value: data.value.usable});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'detailView', value: data.value.detailView});
                } else {
                    MsgAlert("Impossible de récupérer la classe", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }

    static getSpellList(uniqid){
        let URL = 'index.php?c=classe&a=getSpellList';
        $.post(URL,{
                uniqid:uniqid
            },
            function(data, status){
                if(data.state){
                    Page.buildOffcanvas(data.value.title, data.value.content, Page.PLACEMENT_START, true, true);
                } else {
                    MsgAlert("Impossible de récupérer la liste des sorts", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }

}