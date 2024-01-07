class Shop extends Controller{

    static MODEL_NAME = "shop";

    static add(){
        let URL = 'index.php?c=shop&a=add';
        let name = $('#modal #addShop #name').val();
        
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
                    MsgAlert("Ajout d'un hôtel de vente", "L'hôtel de vente " + name + " a bien été ajouté.", "green" , 3000);
                    $('#modal #addShop #name').val("");
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