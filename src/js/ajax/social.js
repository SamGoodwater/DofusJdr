class Social extends Controller{

    static MODEL_NAME = "social";

    static add(){
        let URL = 'index.php?c=social&a=add';
        let name = $('#modal #addSocial #name').val();
        let text = $('#modal #addSocial #text').val();
        let link = $('#modal #addSocial #link').val();
        
        $.post(URL,
            {
                name:name,
                text:text,
                link:link
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout d'un réseau", "Le réseau " + name + " a bien été ajouté.", "green" , 3000);
                    $('#modal #addSocial #name').val("");
                    $('#modal #addSocial #text').val("");
                    $('#modal #addSocial #link').val("");
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