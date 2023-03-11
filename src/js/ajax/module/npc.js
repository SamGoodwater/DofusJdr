class Npc extends Controller{

    static MODEL_NAME = "npc";
    
    static add(){
        var URL = 'index.php?c=npc&a=add';
        var name = $('#modalAddNpc #name').val();
        var classe = $('#modalAddNpc #classe').val();
        var level = $('#modalAddNpc #level').val();
        
        $.post(URL,
            {
                name:name,
                classe:classe,
                level:level
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout d'un ou d'une PNJ", 'Le ou la PNJ ' + name + ' a bien été ajouté.', "green" , 3000);
                    $('#modalAddNpc #classe').val("");
                    $('#modalAddNpc #name').val("");
                    $('#modalAddNpc #level').val("");
                    $('#modalAddNpc').modal("hide");
                    $('#table').bootstrapTable('refresh');
                } else {
                    $('#display_error').text(data['error']);
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }

}