class User extends Controller{

    static MODEL_NAME = "user";

    static add(){
        let URL = 'index.php?c=user&a=add';
        let email = $('#modal #addUser #email').val();
        let password = $('#modal #addUser #password').val();
        let password_repeat = $('#modal #addUser #password_repeat').val();
        let pseudo = $('#modal #addUser #pseudot').val();

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
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout d'un·e utilisateur·trice", "L'utilisateur·trice a bien été ajouté.", "green" , 3000);
                    $('#modal #addUser #pseudo').val("");
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

    static getBookmark(show = true){
        $("#onloadDisplay").show("slow");

        let URL = 'index.php?c=user&a=getBookmark';
        $.post(URL,
            {},
            function(data, status)
            {
                Page.buildOffcanvas(ucFirst(globalThis.project.bookmark_name), data.visual, Page.PLACEMENT_START, show);
                $("#onloadDisplay").hide("slow");
            },
            "json"
        ); 
    }

    static toggleBookmark(btn){
        let url = "";
        let sentence = "";

        let i = $(btn).find('i'); // REMOVE
        if(i.hasClass("fas") && i.hasClass("fa-bookmark")){
            url = "index.php?c=user&a=removeBookmark";
            sentence = "Le favoris a bien été retiré du "+ucFirst(globalThis.project.bookmark_name)+".";

        }else if(i.hasClass("far") && i.hasClass("fa-bookmark")){ // AJOUT
            url = "index.php?c=user&a=addBookmark";
            sentence = "Le favoris a bien été ajouté au "+ucFirst(globalThis.project.bookmark_name)+".";
        }

        let classe = $(btn).data("classe");
        let uniqid = $(btn).data("uniqid");

        if(classe != "" && uniqid != ""){

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
                        MsgAlert("Modification du "+ucFirst(globalThis.project.bookmark_name)+".", sentence, "green" , 3000);
                        if(data.cookie.serial != ""){
                            let cookie = "bookmark="+data.cookie.serial+"; path=/; expires="+data.cookie.date+";"; 
                            document.cookie = cookie;
                        }
                        let i = $(btn).find('i'); // REMOVE
                        if(i.hasClass("fas") && i.hasClass("fa-bookmark")){
                            i.removeClass("fas");
                            i.addClass("far");
                            $(btn).attr('title', "Ajouter aux favoris");
                        }else if(i.hasClass("far") && i.hasClass("fa-bookmark")){ // AJOUT
                            i.removeClass("far");
                            i.addClass("fas");
                            $(btn).attr('title', "Retirer des favoris");
                        }
                        if($("#offcanvasbookmark").css("visibility") == 'visible'){
                            User.getBookmark(true);
                        }
                    } else {
                        MsgAlert("Modification du "+ucFirst(globalThis.project.bookmark_name)+".", 'Erreur : ' + data.error, "danger" , 4000);
                    }
                },
                "json"
            ); 

        }
    }

    static updatePassword(uniqid){
        let URL = 'index.php?c=user&a=updatePassword';
        let current_password = $('#modal #currentpassword').val();
        let new_password = $('#modal #newpassword').val();
        let password_repeat = $('#modal #repeatnewpassword').val();

        if(new_password == "" || password_repeat	== ""){
            $('#display_error').text("Veuillez remplir tous les champs");
            return false;
        }

        $('#display_error').text("");
        
        $.post(URL,
            {
                uniqid:uniqid,
                current_password:current_password,
                new_password:new_password,
                new_password_repeat:password_repeat
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Modification du mot de passe", "Le mot de passe a bien été modifié.", "green" , 3000);
                    $('#modal #currentpassword').val("");
                    $('#modal #repeatnewpassword').val("");
                    $('#modal').modal("hide");
                } else {
                    $('#display_error').text(data['error']);
                    MsgAlert("Echec de la modification", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }
}