class Npc extends Controller{

    static MODEL_NAME = "npc";
    
    static add(){
        let URL = 'index.php?c=npc&a=add';
        let name = $('#modal #addNpc #name').val();
        let classe = $('#modal #addNpc #classe').val();
        let level = $('#modal #addNpc #level').val();
        
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
                    $('#modal #addNpc #classe').val("");
                    $('#modal #addNpc #name').val("");
                    $('#modal #addNpc #level').val("");
                    $('#modal').modal("hide");
                    $('#table').bootstrapTable('refresh');
                } else {
                    $('#display_error').text(data['error']);
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }

    static getSpellList(uniqid){
        $("#onloadDisplay").show("slow");
        let URL = 'index.php?c=npc&a=getSpellList';
        $.post(URL,{
                uniqid:uniqid
            },
            function(data, status){
                if(data.state){
                    Page.buildOffcanvas(data.value.title, data.value.content, Page.PLACEMENT_START, true, true);
                } else {
                    MsgAlert("Impossible de récupérer la liste des sorts", 'Erreur : ' + data.error, "danger" , 4000);
                }
                $("#onloadDisplay").hide("slow");
            },
            "json"
        ); 
    }

}