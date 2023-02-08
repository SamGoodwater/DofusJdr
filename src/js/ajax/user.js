class User extends Controller{

    static MODEL_NAME = "user";

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
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout d'un·e utilisateur·trice", "L'utilisateur·trice a bien été ajouté.", "green" , 3000);
                    $('#modalAddUser #pseudo').val("");
                    $('#modalAddUser').modal("hide");
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
                    MsgAlert("Modification du Grimoire", sentence, "green" , 3000);
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