class Classe extends Controller{

    static MODEL_NAME = "classe";
    
    static add(){
        let URL = 'index.php?c=classe&a=add';
        let name = $('#modal #addClasse #name').val();
        let weapons = $('#modal #addClasse #weapons').val();
        
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

    static initModalUpdateSpell(id_group, uniqid_old_spell = ""){
        if ($("#modalAddSpell #data-id_group").length === 0 || id_group == "") {
            MsgAlert("Impossible de récupérer les informations", "Veuillez réessayer", "danger", 4000);
            return false;
        }    
        if(uniqid_old_spell != null && uniqid_old_spell != "") {
            if($("#modalAddSpell #data-uniqid").length === 0){
                MsgAlert("Impossible de récupérer les informations", "Veuillez réessayer", "danger", 4000);
                return false;
            }
        }
        $("#modalAddSpell #data-uniqid").val(uniqid_old_spell);
        $("#modalAddSpell #data-id_group").val(id_group);
        $('#modalAddSpell').modal('show');
    }
    static updateSpell(uniqidClasse, uniqidSpellNew){
        let uniqid_old_spell = $("#modalAddSpell #data-uniqid").val();
        let id_group = $("#modalAddSpell #data-id_group").val();	
        if(id_group == "" || uniqidClasse == "" || uniqidSpellNew == ""){
            MsgAlert("Impossible de récupérer les informations", "Veuillez réessayer", "danger", 4000);
            return false;
        }
        
        let action = {};
        if(uniqid_old_spell == undefined || uniqid_old_spell == ""){
            if(VERBAL_MODE){
                console.log("add");
            }
            action = {
                action : "add",
                uniqid : uniqidSpellNew,
                id_group : id_group
            };
        } else {
            if(VERBAL_MODE){
                console.log("update");
            }
            action = {
                action : "update",
                uniqid : uniqid_old_spell,
                uniqidNew : uniqidSpellNew,
                id_group : id_group
            };
        }

        Classe.update(uniqidClasse,action,'spell', IS_VALUE, "", true);
        $("#modalAddSpell").modal("hide");
    }

}