let COOKIE_RIQUISITE = true;
let COOKIE_CONNEXION = false;
let COOKIE_BOOKMARK = false;
class Connect {

    static setCookie(via_input = false, cookie = {"connexion" : false, "bookmark" : false }){
        if(via_input){
            if ($('.cookie-bar #cookie-connexion').is(":checked")) {
                COOKIE_CONNEXION = true;
            } else {
                COOKIE_CONNEXION = false;
            }
            if ($('.cookie-bar #cookie-bookmark').is(":checked")) {
                COOKIE_BOOKMARK = true;
            } else {
                COOKIE_BOOKMARK = false;
            }
        } else {
            if (typeof cookie["connexion"] == "boolean") {
                COOKIE_CONNEXION = cookie["connexion"];
            } else  {
                COOKIE_CONNEXION = false;
            }
            if (typeof cookie["bookmark"] == "boolean") {
                COOKIE_BOOKMARK = cookie["bookmark"];
            } else  {
                COOKIE_BOOKMARK = false;
            }
        }

        let URL = 'index.php?c=connect&a=setCookiePreference';
        $.post(URL,
            {
                connexion:COOKIE_CONNEXION,
                bookmark:COOKIE_BOOKMARK
            },
            function(data, status)
            {
                if(data.state){
                    MsgAlert("Préférences enregistrées", '', "green" , 4000);
                    let cookie = "cookie_preference="+data.cookie.preference+"; path=/; expires="+data.cookie.date+";"; 
                    document.cookie = cookie;
                    $(".cookie-bar").hide("drop", 100);
                } else {
                    MsgAlert("Préférences n'ont pas pu être enregistrées", 'Erreur : ' + data.error, "danger" , 7000);
                }
            },
            "json"
        ); 
    }

    static getHeader(show_modal = true){
        $("#onloadDisplay").show("slow");
        let URL = 'index.php?c=connect&a=getVisual';
        $.post(URL,
            {is_flush:true},
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }

                $("#userVisual").html(data.header);
                Page.build({
                    target : "modal", 
                    title : data.title,
                    content : data.modal,
                    options : {}, 
                    size : data.size, 
                    show : show_modal
                });
                $("#onloadDisplay").hide("slow");
            },
            "json"
        ); 
    }
    static connect(){
        $("#onloadDisplay").show("slow");
        let URL = 'index.php?c=connect&a=connexion';
        const email_element = document.querySelector('.user-connexion-container__tab-connexion #email');
        const password_element = document.querySelector('.user-connexion-container__tab-connexion #password');
        const email = email_element.value;
        const password = password_element.value;
        let remember = 0;
        if ($('#modalConnexionUser #remember').is(":checked") && COOKIE_CONNEXION) {
            remember = 1;
        }

        if(email == "" || password == ""){
            $('#display_error').text("Veuillez remplir tous les champs");
            return false;
        }

        $('#display_error').text("");
        
        $.post(URL,
            {
                email:email,
                password:password,
                remember:remember
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    $("#userVisual").html(data.value.header);
                    Page.build({
                        target : "modal", 
                        title : data.value.title,
                        content : data.value.modal,
                        size : data.value.size, 
                        show : false
                    });
                    email_element.value = "";
                    password_element.value = "";
                    if(data.cookie != ""){
                        let cookie = "connexion="+data.cookie.token+"; path=/; expires="+data.cookie.date+";"; 
                        document.cookie = cookie;
                    }
                    Connect.getHeader(false);
                } else {
                    $('#display_error').text(data.error);
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
                $("#onloadDisplay").hide("slow");
            },
            "json"
        ); 
    }
    static disconnect(){
        $("#onloadDisplay").show("slow");
        
        let URL = 'index.php?c=connect&a=disconnect';

        $('#display_error').text("");
        
        $.post(URL,
            {},
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    $("#userVisual").html(data.value.header);
                    Page.build({
                        target : "modal", 
                        title : data.value.title,
                        content : data.value.modal,
                        size : data.value.size, 
                        show : false
                    });
                    let cookie = "connexion="+data.cookie.token+"; path=/; expires="+data.cookie.date+";"; 
                    document.cookie = cookie;
                    location.reload();
                } else {
                    $('#display_error').text(data.error);
                }
                $("#onloadDisplay").hide("slow");
            },
            "json"
        ); 
    }
    static passwordForgotten(){
        $("#onloadDisplay").show("slow");
        
        let URL = 'index.php?c=connect&a=passwordForgotten';
        let mail = $('#modalPasswordForgotten #email').val();
        $('#display_error').text("");
        
        $.post(URL,
            {
                mail:mail
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    $("#userVisual").html(data.value.header);
                    Page.build({
                        target : "modal", 
                        title : data.value.title,
                        content : data.value.modal,
                        size : data.value.size, 
                        show : false
                    });
                    $('#modalPasswordForgotten #email').val("");
                    MsgAlert("Mot de passe oublié", 'Un mail vous a été envoyé', "green" , 4000);
                } else {
                    $('#display_error').text(data.error);
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
                $("#onloadDisplay").hide("slow");
            },
            "json"
        ); 
    }
}