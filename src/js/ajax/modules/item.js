class Item extends Controller{
    
    static MODEL_NAME = "item";

    static add(){
        let URL = 'index.php?c=item&a=add';
        let name = $('#modal #addItem #name').val();
        let type = $('#modal #addItem #type').val();
        
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
                    $('#modal #addItem #type').val("");
                    $('#modal #addItem #name').val("");
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

    static setDataInOptionInput(){
        let option = "";
        let checkboxes = document.querySelectorAll('#optionitem.item input[type="checkbox"]');
        for (let checkbox of checkboxes) {
            if(checkbox.checked){
                option += checkbox.value + "|";
            }
            checkbox.checked = false;
        }
        let input = $('#modal #itemListAdd');
        if(input.length > 0){
            input.val(option);
        } else {
            MsgAlert("Echec de l'ajout des options", "Impossible d'accéder aux options", "danger" , 4000);
        }
    }

}