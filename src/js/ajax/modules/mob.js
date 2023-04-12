class Mob extends Controller{
    static MODEL_NAME = "mob";
    
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
                if(data.state){
                    MsgAlert("Ajout de la créature", 'La créature ' + name + ' a bien été ajouté.', "green" , 3000);
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

    static updateDisplayCaracteristics(uniqid, lvl) {
        var elementsToUpdate = $("#"+this.MODEL_NAME+uniqid).find("[data-formule]");

        for (var i = 0; i < elementsToUpdate.length; i++) {
            var dataKey = (lvl == 0) ? "formule" : "level" + lvl;
            var newData = $(elementsToUpdate[i]).data(dataKey);
            if($(elementsToUpdate[i]).data("text") != undefined && $(elementsToUpdate[i]).data("text") != ""){
                newData += " " + $(elementsToUpdate[i]).data("text");
            }
            $(elementsToUpdate[i]).text(newData);
        }
    }

    // static updateDisplayCaracteristics(uniqid, lvl) {
    //     if(lvl == 0){
    //         $("#mob"+uniqid+" #vitality").text($("#mob"+uniqid+" #vitality").data("formule") + " Mod. Vitalité");
    //         $("#mob"+uniqid+" #pa").text($("#mob"+uniqid+" #pa").data("formule") + " PA");
    //         $("#mob"+uniqid+" #pm").text($("#mob"+uniqid+" #pm").data("formule") + " PM");
    //         $("#mob"+uniqid+" #po").text($("#mob"+uniqid+" #po").data("formule") + " PO");
    //         $("#mob"+uniqid+" #ini").text($("#mob"+uniqid+" #ini").data("formule") + " Initiative");
    //         $("#mob"+uniqid+" #touch").text($("#mob"+uniqid+" #touch").data("formule") + " Bonus de touche");
    //         $("#mob"+uniqid+" #life").text($("#mob"+uniqid+" #life").data("formule") + " Points de vie");
    //         $("#mob"+uniqid+" #sagesse").text($("#mob"+uniqid+" #sagesse").data("formule") + " Mod. Sagesse");
    //         $("#mob"+uniqid+" #strong").text($("#mob"+uniqid+" #strong").data("formule") + " Mod. Force");
    //         $("#mob"+uniqid+" #intel").text($("#mob"+uniqid+" #intel").data("formule") + " Mod. Intel");
    //         $("#mob"+uniqid+" #agi").text($("#mob"+uniqid+" #agi").data("formule") + " Mod. Agi");
    //         $("#mob"+uniqid+" #chance").text($("#mob"+uniqid+" #chance").data("formule") + " Mod. Chance");
    //         $("#mob"+uniqid+" #ca").text($("#mob"+uniqid+" #ca").data("formule") + " CA");
    //         $("#mob"+uniqid+" #fuite").text($("#mob"+uniqid+" #fuite").data("formule") + " Fuite");
    //         $("#mob"+uniqid+" #tacle").text($("#mob"+uniqid+" #tacle").data("formule") + " Tacle");
    //         $("#mob"+uniqid+" #dodge_pa").text($("#mob"+uniqid+" #dodge_pa").data("formule") + " Esquive PA");
    //         $("#mob"+uniqid+" #dodge_pm").text($("#mob"+uniqid+" #dodge_pm").data("formule") + " Esquive PM");
    //         $("#mob"+uniqid+" #res_neutre").text($("#mob"+uniqid+" #res_neutre").data("formule") + " Résistance Neutre");
    //         $("#mob"+uniqid+" #res_terre").text($("#mob"+uniqid+" #res_terre").data("formule") + " Résistance Terre");
    //         $("#mob"+uniqid+" #res_feu").text($("#mob"+uniqid+" #res_feu").data("formule") + " Résistance Feu");
    //         $("#mob"+uniqid+" #res_air").text($("#mob"+uniqid+" #res_air").data("formule") + " Résistance Air");
    //         $("#mob"+uniqid+" #res_eau").text($("#mob"+uniqid+" #res_eau").data("formule") + " Résistance Eau");
    //     } else {
    //         $("#mob"+uniqid+" #vitality").text($("#mob"+uniqid+" #vitality").data("level"+lvl) + " Mod. Vitalité");
    //         $("#mob"+uniqid+" #pa").text($("#mob"+uniqid+" #pa").data("level"+lvl) + " PA");
    //         $("#mob"+uniqid+" #pm").text($("#mob"+uniqid+" #pm").data("level"+lvl) + " PM");
    //         $("#mob"+uniqid+" #po").text($("#mob"+uniqid+" #po").data("level"+lvl) + " PO");
    //         $("#mob"+uniqid+" #ini").text($("#mob"+uniqid+" #ini").data("level"+lvl) + " Initiative");
    //         $("#mob"+uniqid+" #touch").text($("#mob"+uniqid+" #touch").data("level"+lvl) + " Bonus de touche");
    //         $("#mob"+uniqid+" #life").text($("#mob"+uniqid+" #life").data("level"+lvl) + " Points de vie");
    //         $("#mob"+uniqid+" #sagesse").text($("#mob"+uniqid+" #sagesse").data("level"+lvl) + " Mod. Sagesse");
    //         $("#mob"+uniqid+" #strong").text($("#mob"+uniqid+" #strong").data("level"+lvl) + " Mod. Force");
    //         $("#mob"+uniqid+" #intel").text($("#mob"+uniqid+" #intel").data("level"+lvl) + " Mod. Intel");
    //         $("#mob"+uniqid+" #agi").text($("#mob"+uniqid+" #agi").data("level"+lvl) + " Mod. Agi");
    //         $("#mob"+uniqid+" #chance").text($("#mob"+uniqid+" #chance").data("level"+lvl) + " Mod. Chance");
    //         $("#mob"+uniqid+" #ca").text($("#mob"+uniqid+" #ca").data("level"+lvl) + " CA");
    //         $("#mob"+uniqid+" #fuite").text($("#mob"+uniqid+" #fuite").data("level"+lvl) + " Fuite");
    //         $("#mob"+uniqid+" #tacle").text($("#mob"+uniqid+" #tacle").data("level"+lvl) + " Tacle");
    //         $("#mob"+uniqid+" #dodge_pa").text($("#mob"+uniqid+" #dodge_pa").data("level"+lvl) + " Esquive PA");
    //         $("#mob"+uniqid+" #dodge_pm").text($("#mob"+uniqid+" #dodge_pm").data("level"+lvl) + " Esquive PM");
    //         $("#mob"+uniqid+" #res_neutre").text($("#mob"+uniqid+" #res_neutre").data("level"+lvl) + " Résistance Neutre");
    //         $("#mob"+uniqid+" #res_terre").text($("#mob"+uniqid+" #res_terre").data("level"+lvl) + " Résistance Terre");
    //         $("#mob"+uniqid+" #res_feu").text($("#mob"+uniqid+" #res_feu").data("level"+lvl) + " Résistance Feu");
    //         $("#mob"+uniqid+" #res_air").text($("#mob"+uniqid+" #res_air").data("level"+lvl) + " Résistance Air");
    //         $("#mob"+uniqid+" #res_eau").text($("#mob"+uniqid+" #res_eau").data("level"+lvl) + " Résistance Eau");
    //     }
    // }

    static getSpellList(uniqid){
        let URL = 'index.php?c=mob&a=getSpellList';
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