class Tools {

    static req(tool, token)
    {
        var URL = 'index.php?c=tools&a='+tool;
        $.ajax({
            type: "POST",
            url: URL,
            dataType: "json",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "Authorization": "Bearer " + token
            },
            success: function(data) {
                if(data.state){
                    MsgAlert("Succès",data.value, "green" , 0);
                } else {
                    MsgAlert("Erreur",data.value, "red" , 0);
                }
            }
        }); 
    }
    
}