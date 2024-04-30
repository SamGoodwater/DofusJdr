class Mob_race extends Controller{
    static MODEL_NAME = "mob_race";

    static add(){
        let URL = 'index.php?c=mob_race&a=add';
        let name = $('#modal #addMob_race #name').val();
        
        $.post(URL,
            {
                name:name
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout d'une race", 'La race ' + name + ' a bien été ajoutée.', "green" , 3000);
                    $('#modal #addMob_race #name').val("");
                    $('#modal').modal("hide");
                    $('#table').bootstrapTable('refresh');
                    Mob_race.updateDisplayRow(data.value.uniqid);
                } else {
                    $('#display_error').text(data.refresh);
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }
}