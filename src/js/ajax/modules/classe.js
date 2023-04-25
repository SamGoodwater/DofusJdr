class Classe extends Controller{

    static MODEL_NAME = "classe";
    
    static add(){
        var URL = 'index.php?c=classe&a=add';
        var name = $('#modal #addClasse #name').val();
        var weapons = $('#modal #addClasse #weapons').val();
        
        $.post(URL,
            {
                name:name,
                weapons_of_choice:weapons
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout de la classe", 'La classe ' + name + ' a bien été ajouté.', "green" , 3000);
                    $('#modal #addClasse #weapons').val("");
                    $('#modal #addClasse #name').val("");
                    $('#modal').modal('hide');
                    $("#table").bootstrapTable("refresh");
                } else {
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }

    static getSpellList(uniqid){
        $("#onloadDisplay").show("slow");
        let URL = 'index.php?c=classe&a=getSpellList';
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