class Capability extends Controller{

    static MODEL_NAME = "capability";
    
    static add(){
        let URL = 'index.php?c=capability&a=add';
        let name = $('#modal #addCapability #name').val();
        
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

    static addToOptionSection(uniqidS, uniqidC, name, color) {
        if($("#modal #capacityListAdd"+uniqidS).length > 0){
            let list = $("#modal #capacityListAdd"+uniqidS).val();
            if(list == undefined || list == null || list == ""){
                list = [];
            } else {
                if(list.includes(";")){
                    list = list.split(";")
                } else {
                    list = [list];
                }
            }
            if(list.indexOf(uniqidC) == -1){
                list.push(uniqidC);
                list = list.join(";")
                $("#modal #capacityListAdd"+uniqidS).val(list);
            } else {
                MsgAlert("Echec de l'ajout", "L'aptitude est déjà présente dans la liste", "danger" , 4000);
                return false;
            }
        } else {
            MsgAlert("Echec de l'ajout", "Impossible d'accéder à l'input", "danger" , 4000);
            return false;
        }

        if($("#modal #showResume"+uniqidS).length > 0){
            if(uniqidC != ""){
                    let realcolor = color;
                    if(color == '') {
                        realcolor = "grey";
                    }
                    if(name != "") {
                        $("#showResume"+uniqidS).append("<div class='badge back-"+realcolor+"-d-2'>"+name+"</div>");
                    } else {
                        $("#showResume"+uniqidS).append("<div class='badge back-grey'>"+uniqidC+"</div>");
                    }
            } else {
                $("#showResume"+uniqidS).append("<div class='badge back-grey'>Erreur de référence</div>");
            }
        } else {
            MsgAlert("Echec de l'ajout", "Impossible d'accéder à la liste", "danger" , 4000);
            return false;
        }
    }

}