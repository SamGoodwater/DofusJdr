class Item extends Controller{
    
    static MODEL_NAME = "item";

    static add(){
        var URL = 'index.php?c=item&a=add';
        var name = $('#modalAddItem #name').val();
        var type = $('#modalAddItem #type').val();
        
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
                    MsgAlert("Ajout de l'équipement", 'L\'équipement ' + name + ' a bien été ajouté.', "green" , 3000);
                    $('#modalAddItem #type').val("");
                    $('#modalAddItem #name').val("");
                    $('#modalAddItem').modal("hide");
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