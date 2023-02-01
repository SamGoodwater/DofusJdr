class User {

    static open(uniqid){
        var URL = 'index.php?c=user&a=getFromUniqid';
    
        $.post(URL,
            {
                uniqid:uniqid
            },
            function(data, status)
            {
                if(data.return == 'success'){
                    Page.build(Page.RESPONSIVE, data.value.title, data.value.visual, Page.SIZE_XL, true);
                } else {
                    MsgAlert("Impossible de récupérer la liste", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }  
    static add(){
        var URL = 'index.php?c=user&a=add';
        var email = $('#modalAddUser #email').val();
        var password = $('#modalAddUser #password').val();
        var password_repeat = $('#modalAddUser #password_repeat').val();
        var pseudo = $('#modalAddUser #pseudot').val();

        if(email == "" || password == "" || password_repeat	== ""){
            $('#display_error').text("Veuillez remplir tous les champs");
            return false;
        }

        $('#display_error').text("");
        
        $.post(URL,
            {
                email:email,
                password:password,
                password_repeat:password_repeat,
                pseudo:pseudo
            },
            function(data, status)
            {
                if(data['script'] != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data['return'] == 'success'){
                    MsgAlert("Ajout d'un·e utilisateur·trice", "L'utilisateur·trice a bien été ajouté.", "success" , 3000);
                    $('#modalAddUser #pseudo').val("");
                    $('#modalAddUser').modal("hide");
                    $('#table').bootstrapTable('refresh');
                } else {
                    $('#display_error').text(data['error']);
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    static update(uniqid, input, type, value_type = IS_INPUT, fct = ""){
        var URL = 'index.php?c=user&a=update';
        var value = 0;
    
        switch (value_type) {
            case IS_INPUT:
                var inp = $(input);
                if(inp.attr("required") && inp.val() == ""){
                    inp.addClass("is-invalid").removeClass('is-valid');
                    MsgAlert("Le champs est obligatoire.", '', "error" , 3000);
                    return false;
                } else {
                    inp.addClass("is-valid").removeClass('is-invalid');
                    value = inp.val();
                }
            break;
            case IS_VALUE:
                value = input;
            break;
            case IS_CHECKBOX:
                if ($(input).is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            break;
            case IS_CKEDITOR:
                value = CKEDITOR.instances[type+id].getData();
            break;
            case IS_PATH_FILE:
                if(file_select.extention != "dir" && file_select.path != ""){
                    value = file_select.dirname + file_select.name;
                    $("#showFile_"+type+" a").attr('href', value);
                    $("#showFile_"+type+" a div").css('background-image', "url('"+value+"')");
                } else {
                    alert('Aucun fichier sélectionné');
                }
            break;
            default:
                MsgAlert("Aucun type de valeur spécifié", '', "error" , 3000);
                return false;
        }
    
        if(fct !=""){
            value = fct(value);
            if(value == '***error***'){
                MsgAlert("Echec de la mise à jour", "Erreur d'éxécuttion de la fonction", "danger" , 4000);
                return false;
            }
            $(input).val(value);
        }
        
        $.post(URL,
            {
                uniqid:uniqid,
                value:value,
                type:type
            },
            function(data, status)
            {
                if(data['script'] != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data['return'] == "success"){
                    User.updateDisplayRow(uniqid);
                    $("#table").bootstrapTable("refresh");
                    MsgAlert("Mise à jour de l'utilisateur·trice ", '', "success" , 3000);
                } else {
                    MsgAlert("Echec de la mise à jour", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }  
    static remove(uniqid){
        var URL = 'index.php?c=shop&a=remove';
    
        if (confirm("Etes vous sûr de vouloir supprimer définitivement cet·te utisateur·trice ?")) {
            $.post(URL,
                {
                    uniqid:uniqid
                },
                function(data, status)
                {
                    if(data['script'] != ""){
                        $('body').append("<script>"+data['script']+"</script>");
                    }
                    if(data['return'] == 'success'){
                        MsgAlert("Suppression de l'utilisateur·trice", "L'utilisateur·trice de  a bien été supprimé·e.", "success" , 3000);
                        $('#table').bootstrapTable('refresh');
                    } else {
                        $('#display_error').text(data['error']);
                        MsgAlert("Echec de la suppresion", 'Erreur : ' + data['error'], "danger" , 4000);
                    }
                },
                "json"
            ); 
        }
    
    }

    static getBookmark(show = true){
        var URL = 'index.php?c=user&a=getBookmark';
        $.post(URL,
            {},
            function(data, status)
            {
                Page.buildOffcanvas("Grimoire", data.visual, Page.PLACEMENT_TOP, show);
            },
            "json"
        ); 
    }

    static updateDisplayRow(uniqid) {
        var indexCell = $("#"+uniqid).parent().parent().data("index");

        var URL = 'index.php?c=user&a=getArrayFromUniqid';
        $.post(URL,
            {
                uniqid:uniqid
            },
            function(data, status)
            {
                console.log(data);
                if(data.return == 'success'){
                    for (let [index, element] of Object.entries(data.value)) {
                        $("#table").bootstrapTable('updateCell', {index: indexCell, field: index, value: element});
                    }
                } else {
                    MsgAlert("Impossible de récupérer la liste", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }

    static changeBookmark(btn){
        let url = "";
        let sentence = "";

        var i = $(btn).find('i'); // REMOVE
        if(i.hasClass("fas") && i.hasClass("fa-bookmark")){
          i.removeClass("fas");
          i.addClass("far");
          url = "index.php?c=user&a=removeBookmark";
          sentence = "Le favoris a bien été enlevé du grimoire.";

        }else if(i.hasClass("far") && i.hasClass("fa-bookmark")){ // AJOUT
          i.removeClass("far");
          i.addClass("fas");
          url = "index.php?c=user&a=addBookmark";
          sentence = "Le favoris a bien été ajouté au grimoire.";
        }

        let classe = $(btn).data("classe");
        let uniqid = $(btn).data("uniqid");

        $.post(url,
            {
                uniqid:uniqid,
                classe:classe
            },
            function(data, status)
            {
                if(data['script'] != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data.state){
                    MsgAlert("Modification du Grimoire", sentence, "success" , 3000);
                    if(data.cookie.serial != ""){
                        var cookie = "bookmark="+data.cookie.serial+"; path=/; expires="+data.cookie.date+";"; 
                        document.cookie = cookie;
                    }
                    if($("#offcanvasbookmark").css("visibility") == 'visible'){
                        User.getBookmark(true);
                    }
                } else {
                    MsgAlert("Modification du Grimoire", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }

}