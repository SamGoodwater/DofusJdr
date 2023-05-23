class Mob extends Controller{
    static MODEL_NAME = "mob";

    static add(){
        var URL = 'index.php?c=mob&a=add';
        var name = $('#modal #addMob #name').val();
        var level = $('#modal #addMob #level').val();
        var powerful = $('#modal #addMob #powerful').val();
        var intel = "";
        if($('#modal #ddMob #intel').is(':checked')){
            intel = true;
        }
        var strong = "";
        if($('#modal #addMob #strong').is(':checked')){
            strong = true;
        }
        var chance = "";
        if($('#modal #addMob #chance').is(':checked')){
            chance = true;
        }
        var agi = "";
        if($('#modal #addMob #agi').is(':checked')){
            agi = true;
        }
        var sagesse = "";
        if($('#modal #addMob #sagesse').is(':checked')){
            sagesse = true;
        }
        var vitality = "";
        if($('#modal #addMob #vitality').is(':checked')){
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
                    $('#modal #addMob #name').val("");
                    $('#modal #addMob #level').val(1);
                    $('#modal #addMob #powerful').val(4);
                    $('#modal #addMob #intel').attr('checked', false);
                    $('#modal #addMob #strong').attr('checked', false);
                    $('#modal #addMob #chance').attr('checked', false);
                    $('#modal #addMob #agi').attr('checked', false);
                    $('#modal #addMob #sagesse').attr('checked', false);
                    $('#modal #addMob #vitality').attr('checked', false);
                    $('#modal').modal("hide");
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