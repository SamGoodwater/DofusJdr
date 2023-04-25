class Consumable extends Controller{

    static MODEL_NAME = "consumable";
    
    static add(){
        var URL = 'index.php?c=consumable&a=add';
        var name = $('#modal #addConsumable #name').val();
        var type = $('#modal #addConsumable #type').val();
        
        $.post(URL,
            {
                name:name,
                type:type
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout du consommable", 'Le consommable ' + name + ' a bien été ajouté.', "green" , 3000);
                    $('#modal #addConsumable #type').val("");
                    $('#modal #addConsumable #name').val("");
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