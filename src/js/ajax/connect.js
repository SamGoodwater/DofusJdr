let COOKIE_RIQUISITE = true;
let COOKIE_CONNEXION = false;
let COOKIE_GRIMOIRE = false;
class Connect {

    static setCookie(via_input = false, cookie = {"connexion" : false, "bookmark" : false }){
        if(via_input){
            if ($('.cookie-bar #cookie-connexion').is(":checked")) {
                COOKIE_CONNEXION = true;
            } else {
                COOKIE_CONNEXION = false;
            }
            if ($('.cookie-bar #cookie-bookmark').is(":checked")) {
                COOKIE_GRIMOIRE = true;
            } else {
                COOKIE_GRIMOIRE = false;
            }
        } else {
            if (typeof cookie["connexion"] == "boolean") {
                COOKIE_CONNEXION = cookie["connexion"];
            } else  {
                COOKIE_CONNEXION = false;
            }
            if (typeof cookie["bookmark"] == "boolean") {
                COOKIE_GRIMOIRE = cookie["bookmark"];
            } else  {
                COOKIE_GRIMOIRE = false;
            }
        }

        var URL = 'index.php?c=connect&a=setCookiePreference';
        $.post(URL,
            {
                connexion:COOKIE_CONNEXION,
                bookmark:COOKIE_GRIMOIRE
            },
            function(data, status)
            {
                if(data.state){
                    MsgAlert("Préférences enregistrées", '', "green" , 4000);
                    var cookie = "cookie_preference="+data.cookie.preference+"; path=/; expires="+data.cookie.date+";"; 
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
        var URL = 'index.php?c=connect&a=getVisual';
        $.post(URL,
            {is_flush:true},
            function(data, status)
            {
                $("#userVisual").html(data.header);
                Page.build(Page.RESPONSIVE, data.title, data.modal, data.size, show_modal)
            },
            "json"
        ); 
    }
    static connect(){
        var URL = 'index.php?c=connect&a=connexion';
        var email = $('#modalConnexionUser #email').val();
        var password = $('#modalConnexionUser #password').val();
        var remember = 0;
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
                    Page.build(Page.RESPONSIVE, data.value.title,  data.value.modal, data.value.size, false);
                    $('#modalConnexionUser #email').val("");
                    $('#modalConnexionUser #password').val("");
                    $('#table').bootstrapTable('refresh');
                    if(data.cookie != ""){
                        var cookie = "connexion="+data.cookie.token+"; path=/; expires="+data.cookie.date+";"; 
                        document.cookie = cookie;
                    }
                } else {
                    $('#display_error').text(data.error);
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    static disconnect(){
        var URL = 'index.php?c=connect&a=disconnect';

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
                    Page.build(Page.RESPONSIVE, data.value.title,  data.value.modal, data.value.size, false);
                    var cookie = "connexion="+data.cookie.token+"; path=/; expires="+data.cookie.date+";"; 
                    document.cookie = cookie;
                } else {
                    $('#display_error').text(data.error);
                }
            },
            "json"
        ); 
    }
}