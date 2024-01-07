class Mob extends Controller{
    static MODEL_NAME = "mob";

    static add(){
        let URL = 'index.php?c=mob&a=add';
        let name = $('#modal #addMob #name').val();
        let level = $('#modal #addMob #level').val();
        let powerful = $('#modal #addMob #powerful').val();
        let intel = "";
        if($('#modal #ddMob #intel').is(':checked')){
            intel = true;
        }
        let strong = "";
        if($('#modal #addMob #strong').is(':checked')){
            strong = true;
        }
        let chance = "";
        if($('#modal #addMob #chance').is(':checked')){
            chance = true;
        }
        let agi = "";
        if($('#modal #addMob #agi').is(':checked')){
            agi = true;
        }
        let sagesse = "";
        if($('#modal #addMob #sagesse').is(':checked')){
            sagesse = true;
        }
        let vitality = "";
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