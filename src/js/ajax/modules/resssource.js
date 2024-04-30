class Ressource extends Controller{

    static MODEL_NAME = "ressource";
    
    static add(){
        let URL = 'index.php?c=ressource&a=add';
        let name = $('#modal #addRessource #name').val();
        let level = $('#modal #addRessource #level').val();
        let price = $('#modal #addRessource #price').val();
        let weight = $('#modal #addRessource #weight').val();
        let type = $('#modal #addRessource #type').val();
        
        $.post(URL,
            {
                name:name,
                level:level,
                price:price,
                weight:weight,
                type:type
            },
            function(data, status)
            {
                if(data['script'] != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout de la ressource", 'La ressource ' + name + ' a bien été ajouté.', "green" , 3000);
                    $('#modal #addRessource #name').val("");
                    $('#modal #addRessource #price').val("");
                    $('#modal #addRessource #weight').val("");
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
}