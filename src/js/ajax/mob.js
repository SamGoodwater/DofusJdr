class Mob {

    static open(uniqid)
    {
        var URL = 'index.php?c=mob&a=getFromUniqid';
    
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
        var URL = 'index.php?c=mob&a=add';
        var name = $('#modalAddMob #name').val();
        var level = $('#modalAddMob #level').val();
        var powerful = $('#modalAddMob #powerful').val();
        var intel = "";
        if($('#modalAddMob #intel').is(':checked')){
            intel = true;
        }
        var strong = "";
        if($('#modalAddMob #strong').is(':checked')){
            strong = true;
        }
        var chance = "";
        if($('#modalAddMob #chance').is(':checked')){
            chance = true;
        }
        var agi = "";
        if($('#modalAddMob #agi').is(':checked')){
            agi = true;
        }
        var sagesse = "";
        if($('#modalAddMob #sagesse').is(':checked')){
            sagesse = true;
        }
        var vitality = "";
        if($('#modalAddMob #vitality').is(':checked')){
            vitality = true;
        }
        
        $.post(URL,
            {
                name:name,
                level:level,
                powerful:powerful,
                intel:intel,
                strong:strong,
                chance:chance,
                agi:agi,
                sagesse:sagesse,
                vitality:vitality
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.return == 'success'){
                    MsgAlert("Ajout de la créature", 'La créature ' + name + ' a bien été ajouté.', "success" , 3000);
                    $('#modalAddMob #name').val("");
                    $('#modalAddMob #level').val(1);
                    $('#modalAddMob #powerful').val(4);
                    $('#modalAddMob #intel').attr('checked', false);
                    $('#modalAddMob #strong').attr('checked', false);
                    $('#modalAddMob #chance').attr('checked', false);
                    $('#modalAddMob #agi').attr('checked', false);
                    $('#modalAddMob #sagesse').attr('checked', false);
                    $('#modalAddMob #vitality').attr('checked', false);
                    $('#modalAddMob').modal("hide");
                    $('#table').bootstrapTable('refresh');
                    Mob.updateDisplayRow(data.value.uniqid);
                } else {
                    $('#display_error').text(data.refresh);
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }

    static update(uniqid, input, type, value_type = IS_INPUT, fct = ""){
        var URL = 'index.php?c=mob&a=update';
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
                    Mob.updateDisplayRow(uniqid);
                    MsgAlert("Mise à jour de la créature", '', "success" , 3000);
                } else {
                    MsgAlert("Echec de la mise à jour", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    
    static remove(uniqid){
        var URL = 'index.php?c=mob&a=remove';
    
        if (confirm("Etes vous sûr de vouloir supprimer définitivement cette créature ?")) {
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
                        MsgAlert("Suppression de la créature", "La créature a bien été supprimé.", "success" , 3000);
                        $('#table').bootstrapTable('refresh');
                        Mob.updateDisplayRow(uniqid);
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

        var URL = 'index.php?c=mob&a=getArrayFromUniqid';
        $.post(URL,
            {
                uniqid:uniqid
            },
            function(data, status)
            {
                if(data.return == 'success'){
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'timestamp_updated', value: data.value.timestamp_updated});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'name', value: data.value.name});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'level', value: data.value.level});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'vitality', value: data.value.vitality});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'pa', value: data.value.pa});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'pm', value: data.value.pm});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'ini', value: data.value.ini});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'touch', value: data.value.touch});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'life', value: data.value.life});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'sagesse', value: data.value.sagesse});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'strong', value: data.value.strong});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'intel', value: data.value.intel});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'agi', value: data.value.agi});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'chance', value: data.value.chance});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'ca', value: data.value.ca});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'fuite', value: data.value.fuite});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'tacle', value: data.value.tacle});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'dodge_pa', value: data.value.dodge_pa});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'dodge_pm', value: data.value.dodge_pm});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'res_neutre', value: data.value.res_neutre});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'res_terre', value: data.value.res_terre});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'res_feu', value: data.value.res_feu});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'res_air', value: data.value.res_air});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'res_eau', value: data.value.res_eau});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'zone', value: data.value.zone});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'hostility', value: data.value.hostility});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'spell', value: data.value.spell});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'trait', value: data.value.trait});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'usable', value: data.value.usable});
                    $("#table").bootstrapTable('updateCell', {index: indexCell, field: 'detailView', value: data.value.detailView});
                } else {
                    MsgAlert("Impossible de récupérer la liste", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }

    static updateDisplayCaracteristics(uniqid, lvl) {
        if(lvl == 0){
            $("#mob"+uniqid+" #vitality").text($("#mob"+uniqid+" #vitality").data("formule") + " Mod. Vitalité");
            $("#mob"+uniqid+" #pa").text($("#mob"+uniqid+" #pa").data("formule") + " PA");
            $("#mob"+uniqid+" #pm").text($("#mob"+uniqid+" #pm").data("formule") + " PM");
            $("#mob"+uniqid+" #po").text($("#mob"+uniqid+" #po").data("formule") + " PO");
            $("#mob"+uniqid+" #ini").text($("#mob"+uniqid+" #ini").data("formule") + " Initiative");
            $("#mob"+uniqid+" #touch").text($("#mob"+uniqid+" #touch").data("formule") + " Bonus de touche");
            $("#mob"+uniqid+" #life").text($("#mob"+uniqid+" #life").data("formule") + " Points de vie");
            $("#mob"+uniqid+" #sagesse").text($("#mob"+uniqid+" #sagesse").data("formule") + " Mod. Sagesse");
            $("#mob"+uniqid+" #strong").text($("#mob"+uniqid+" #strong").data("formule") + " Mod. Force");
            $("#mob"+uniqid+" #intel").text($("#mob"+uniqid+" #intel").data("formule") + " Mod. Intel");
            $("#mob"+uniqid+" #agi").text($("#mob"+uniqid+" #agi").data("formule") + " Mod. Agi");
            $("#mob"+uniqid+" #chance").text($("#mob"+uniqid+" #chance").data("formule") + " Mod. Chance");
            $("#mob"+uniqid+" #ca").text($("#mob"+uniqid+" #ca").data("formule") + " CA");
            $("#mob"+uniqid+" #fuite").text($("#mob"+uniqid+" #fuite").data("formule") + " Fuite");
            $("#mob"+uniqid+" #tacle").text($("#mob"+uniqid+" #tacle").data("formule") + " Tacle");
            $("#mob"+uniqid+" #dodge_pa").text($("#mob"+uniqid+" #dodge_pa").data("formule") + " Esquive PA");
            $("#mob"+uniqid+" #dodge_pm").text($("#mob"+uniqid+" #dodge_pm").data("formule") + " Esquive PM");
            $("#mob"+uniqid+" #res_neutre").text($("#mob"+uniqid+" #res_neutre").data("formule") + " Résistance Neutre");
            $("#mob"+uniqid+" #res_terre").text($("#mob"+uniqid+" #res_terre").data("formule") + " Résistance Terre");
            $("#mob"+uniqid+" #res_feu").text($("#mob"+uniqid+" #res_feu").data("formule") + " Résistance Feu");
            $("#mob"+uniqid+" #res_air").text($("#mob"+uniqid+" #res_air").data("formule") + " Résistance Air");
            $("#mob"+uniqid+" #res_eau").text($("#mob"+uniqid+" #res_eau").data("formule") + " Résistance Eau");
        } else {
            $("#mob"+uniqid+" #vitality").text($("#mob"+uniqid+" #vitality").data("level"+lvl) + " Mod. Vitalité");
            $("#mob"+uniqid+" #pa").text($("#mob"+uniqid+" #pa").data("level"+lvl) + " PA");
            $("#mob"+uniqid+" #pm").text($("#mob"+uniqid+" #pm").data("level"+lvl) + " PM");
            $("#mob"+uniqid+" #po").text($("#mob"+uniqid+" #po").data("level"+lvl) + " PO");
            $("#mob"+uniqid+" #ini").text($("#mob"+uniqid+" #ini").data("level"+lvl) + " Initiative");
            $("#mob"+uniqid+" #touch").text($("#mob"+uniqid+" #touch").data("level"+lvl) + " Bonus de touche");
            $("#mob"+uniqid+" #life").text($("#mob"+uniqid+" #life").data("level"+lvl) + " Points de vie");
            $("#mob"+uniqid+" #sagesse").text($("#mob"+uniqid+" #sagesse").data("level"+lvl) + " Mod. Sagesse");
            $("#mob"+uniqid+" #strong").text($("#mob"+uniqid+" #strong").data("level"+lvl) + " Mod. Force");
            $("#mob"+uniqid+" #intel").text($("#mob"+uniqid+" #intel").data("level"+lvl) + " Mod. Intel");
            $("#mob"+uniqid+" #agi").text($("#mob"+uniqid+" #agi").data("level"+lvl) + " Mod. Agi");
            $("#mob"+uniqid+" #chance").text($("#mob"+uniqid+" #chance").data("level"+lvl) + " Mod. Chance");
            $("#mob"+uniqid+" #ca").text($("#mob"+uniqid+" #ca").data("level"+lvl) + " CA");
            $("#mob"+uniqid+" #fuite").text($("#mob"+uniqid+" #fuite").data("level"+lvl) + " Fuite");
            $("#mob"+uniqid+" #tacle").text($("#mob"+uniqid+" #tacle").data("level"+lvl) + " Tacle");
            $("#mob"+uniqid+" #dodge_pa").text($("#mob"+uniqid+" #dodge_pa").data("level"+lvl) + " Esquive PA");
            $("#mob"+uniqid+" #dodge_pm").text($("#mob"+uniqid+" #dodge_pm").data("level"+lvl) + " Esquive PM");
            $("#mob"+uniqid+" #res_neutre").text($("#mob"+uniqid+" #res_neutre").data("level"+lvl) + " Résistance Neutre");
            $("#mob"+uniqid+" #res_terre").text($("#mob"+uniqid+" #res_terre").data("level"+lvl) + " Résistance Terre");
            $("#mob"+uniqid+" #res_feu").text($("#mob"+uniqid+" #res_feu").data("level"+lvl) + " Résistance Feu");
            $("#mob"+uniqid+" #res_air").text($("#mob"+uniqid+" #res_air").data("level"+lvl) + " Résistance Air");
            $("#mob"+uniqid+" #res_eau").text($("#mob"+uniqid+" #res_eau").data("level"+lvl) + " Résistance Eau");
        }
    }

}