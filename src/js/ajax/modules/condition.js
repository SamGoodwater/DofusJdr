class Condition extends Controller{

    static MODEL_NAME = "condition";
    
    static add(){
        let URL = 'index.php?c=condition&a=add';
        let name = $('#modal #addCondition #name').val();
        
        $.post(URL,
            {
                name:name,
            },
            function(data, status)
            {
                if(data['script'] != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout de l'état", 'L\'état ' + name + ' a bien été ajouté.', "green" , 3000);
                    $('#modal #addCondition #name').val("");
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