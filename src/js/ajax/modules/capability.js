class Capability extends Controller{

    static MODEL_NAME = "capability";
    
    static add(){
        var URL = 'index.php?c=capability&a=add';
        var name = $('#modal #addCapability #name').val();
        
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
                    MsgAlert("Ajout de l'aptitude", 'L\'aptitude ' + name + ' a bien été ajouté.', "green" , 3000);
                    $('#modal #addCapability #name').val("");
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

    // static showResume(uniqid, dest, format = DISPLAY_CARD, ecrase = false) {
    //     var URL = 'index.php?c=capability&a=getResume';
    
    //     $.post(URL,
    //         {
    //             uniqid:uniqid,
    //             format:format
    //         },
    //         function(data, status)
    //         {
    //             if(data.state){
    //                 if(ecrase){
    //                     $(dest).html("");
    //                     $(dest).html(data.return);
    //                     $(dest).show("slow");
    //                 } else {
    //                     $(dest).append(data.return);
    //                 }
    //                 $('[data-toggle="tooltip"]').tooltip();
    //             } else {
    //                 MsgAlert("Impossible de récupérer le sort", 'Erreur : ' + data.error, "danger" , 4000);
    //             }
    //         },
    //         "json"
    //     ); 
    // }

}