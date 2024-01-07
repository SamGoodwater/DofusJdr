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

    static initModalUpdateSpell(btn, targetSpellToKeep){
        let $inputdata = $("#modalAddSpell #data-hidden");
        let $parent = $(btn).closest('.resume_in_competition');
        let $spell = $parent.find(targetSpellToKeep);
        $inputdata.attr('data-uniqid', $spell.attr('data-uniqid'));
        $('#modalAddSpell').modal('show');
    }
    static updateSpell(uniqidClasse, uniqidSpellNew){
        let uniqidSpell = $("#modalAddSpell #data-hidden").attr('data-uniqid');
        let action = {};
        if(uniqidSpell == undefined || uniqidSpell == ""){
            if(VERBAL_MODE){
                console.log("add");
            }
            action = {
                action : "add",
                uniqid : uniqidSpellNew
            };
        } else {
            if(VERBAL_MODE){
                console.log("update");
            }
            action = {
                action : "update",
                uniqid : uniqidSpell,
                uniqidNew : uniqidSpellNew
            };
        }

        Classe.update(uniqidClasse,action,'spell', IS_VALUE, "", true);
        $("#modalAddSpell").modal("hide");
    }

}