// Exemple d'utilisation : copyToClipboard('<?=$_SERVER['SERVER_NAME'] . '/#'. $page->getUrl_name()?>')
// Si élément dom input copié, alors copie la valeur de l'input.
const copyToClipboard = str => {
    if(str == ''){
        MsgAlert("Le lien est vide","", "red" , 3000);
    } else if(typeof str == "string"){
        str = str.trim();
    } else if(str instanceof jQuery){
        str = str.val();
    } else if(str instanceof HTMLElement){
        str = str.innerText;
    } else if(str instanceof HTMLInputElement){
        str = str.value;
    } else {
        str = str.toString();
    }

    const el = document.createElement('textarea');  // Create a <textarea> element                     
    el.value = str;                                 // Set its value to the string that you want copied
    el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
    el.style.position = 'relative';                               
    el.style.left = '-99999px';                      // Move outside the screen to make it invisible
    $(document.activeElement).append(el); 
    const selected =            
      document.getSelection().rangeCount > 0        // Check if there is any content selected previously
        ? document.getSelection().getRangeAt(0)     // Store selection if found
        : false;                                    // Mark as false to know no selection existed before
    el.select();                                    // Select the <textarea> content
    document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
    $(el).remove();
    if (selected) {                                 // If a selection existed before copying
      document.getSelection().removeAllRanges();    // Unselect everything on the HTML document
      document.getSelection().addRange(selected);   // Restore the original selection
    }
    if(VERBAL_MODE){console.log('COPY : '+str);}
    MsgAlert("Le texte a bien été copié","", "green" , 3000);
  };
  
  // M E S S A G E   A L E R T
    // MsgAlert('Je suis un titre', 'Test essaie 1 2 1 2 un deux un dos one two', 'yellow-d-3', 1000);
    // MsgAlert('Je suis un titre', 'Test essaie 1 2 1 2 un deux un dos one two', 'red-d-3', 0, 'https://search.lilo.org/results.php?q=cr%C3%A9er%20chaine%20al%C3%A9atoir%20jquery&page=1');
    // MsgAlert('Je suis un titre', 'Test essaie 1 2 1 2 un deux un dos one two', 'teal-d-3', 2000);
    function MsgAlert(title, content, color, delay, link='') 
    {
      switch (color) {
        case "danger":
          color="red-d-3";
        break;
        case "warning":
          color="yellow-d-3";
        break;
        case "success":
          color="green-d-3";
        break;
        case "info":
          color="teal-d-3";
        break;
      }
    
        var liste = ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9"];
        var result = '';
        for (i = 0; i < 7; i++) {
            result += liste[Math.floor(Math.random() * liste.length)];
        }
        
        // var appendlink = '';
        // if(link !=''){
        //   appendlink = "<p class='text-center m-0 p-0 font-size-0-8'><a target='_blank' href='" + link + "'><i class='fa-solid fa-arrow-circle-right'></i></a></p>";
        // }
        var append = "<div id='"+result+"' class='notif border-"+color+"'><div class='toast-header'><div class='msgSquare back-"+color+"'></div><strong class='me-auto'>"+title+"</strong><small></small><button type='button' class='btn-close' onclick='$(this).parent().parent().remove();' aria-label='Close'></button></div><div class='toast-body'>"+content+"</div></div>";
    
        $("#MsgAlert").append(append);
    
        if(Number(delay) != 0){
          setTimeout(function(){
            $('#' + result).remove();
          }, delay);
        }
    
        $('#MsgAlert').show();
    }
  
// Load Slider
function loadSlider(idGroupSlider){
    let container_img = $(idGroupSlider + ' .img-slider');
    let slider_img = $(idGroupSlider + ' .slider-image');
    let slider_info = $(idGroupSlider + ' .slider-info');
    let slider_global = $(idGroupSlider);
    let container_title = $(idGroupSlider + ' .slider-title');
    let prev = $(idGroupSlider + ' .btn-prev');
    let next = $(idGroupSlider + ' .btn-next');

    let state = 0;
    let nbr_img = Number(container_img.length);
    const SNAP_X = 350;
    const SNAP_Y = 350;

    slider_info.hide();
    slider_global.css("overflow-y", "hidden");

    next.on('click', function() {
        state++;
        if(state >= nbr_img) {
            state = 0;
        }
        slider_img.animate({scrollLeft: Number(state * SNAP_X)}, 500, 'easeInOutQuint');
        refreshBtnSLider();
    })
    prev.on('click', function() {
        state--;
        if(state < 0) {
            state = nbr_img - 1;
        }
        slider_img.animate({scrollLeft: Number(state * SNAP_X)}, 500, 'easeInOutQuint');
        refreshBtnSLider();
    })
    container_title.on("click", function(){
    if(slider_info.hasClass("showed")){
        slider_info.removeClass("showed");
        slider_global.animate({scrollTop: 0}, 1000, 'easeInOutQuint');
        slider_global.css("overflow-y", "hidden");
        var delai = setTimeout(function(){
        slider_info.hide();
        }, 1000);
    }else{
        slider_info.show();
        slider_global.animate({scrollTop: SNAP_Y}, 1000, 'easeInOutQuint'); 
        slider_info.addClass("showed");
        slider_global.css("overflow-y", "scroll");
    }
    })

    $(idGroupSlider + " .slider-info span[style]").each(function( index ) {
    $(this).css("color", "white");
    });

    var timer = 900;
    slider_img.bind('scroll',function () {
        clearTimeout(timer);
        timer = setTimeout(snapImg , 150);
    });
    var snapImg = function () { 
        var scrollLeft = slider_img.scrollLeft();
        var snap = Math.round(scrollLeft / SNAP_X) * SNAP_X;
        slider_img.animate({scrollLeft: snap}, 200, 'easeInOutQuint');
        if(snap / SNAP_X > nbr_img - 1){
        state = nbr_img - 1;
        } else {
        state = snap / SNAP_X;
        }
        refreshBtnSLider();
    };

    refreshBtnSLider();

    function refreshBtnSLider(){
    if(state >= nbr_img - 1){
        next.hide();
    }else{
        next.show();
    }
    if(state == 0){
        prev.hide();
    }else{
        prev.show();
    }
    }
}

let is_tooltipsCustom_triggerable = true;
function showTooltips(launcher, target){
    if(is_tooltipsCustom_triggerable){
    // Il n'est plus possible de déclencher cette fonction avant 200ms
    is_tooltipsCustom_triggerable = false;
    setTimeout(function(){is_tooltipsCustom_triggerable = true;}, 250);

    if ($('.container-tooltips').length === 0) {
        // La div avec la classe "container-tooltips" n'existe pas dans la page
        // Création de la div
        let containerTooltips = $('<div>').addClass('container-tooltips');
        // Insertion de la div dans le corps de la page
        $('body').append(containerTooltips);
    }
    let tooltip = $('.container-tooltips');
    let clone = $(target).clone();
    clone.show();
    tooltip.html(clone);
    
    let position_launcher = $(launcher).offset();
    let direction = {
        x: "",
        y: ""
    };
    let point_centered_launcher = {
        x: position_launcher.left + $(launcher).width() / 2,
        y: position_launcher.top + $(launcher).height() / 2
    };

    let gap = 3; // Ecart entre le launcher et le tooltip

    if($(window).width() / 2 > point_centered_launcher.x){
        // Le point est à gauche
        direction.x = "L";
    } else {
        // Le point est à droite
        direction.x = "R";
    }
    if($(window).height() / 2 > point_centered_launcher.y){
        // Le point est plus en haut
        direction.y += "T";
    } else {
        // Le point est plus en bas
        direction.y += "B";
    }

    let x = $(window).width() / 2 - $(launcher).width() / 2;
    let y = $(window).height() / 2 - $(launcher).height() / 2;

    if($(launcher).width() < $(window).width() / 2 && $(launcher).height() < $(window).height() / 2){
        if(direction.x == "L"){
        // Le launcher est à gauche donc on met le tooltip à droite
        x = position_launcher.left + $(launcher).width() + gap;
        } else {
        // Le launcher est à droite donc on met le tooltip à gauche
        x = position_launcher.left - $(tooltip).width() - gap;
        }
        if(direction.y == "T"){
        // Le launcher est en haut donc on met le tooltip en bas
        y = position_launcher.top + $(launcher).height() / 2 - $(tooltip).height() / 2;
        if(y < 0){
            y = 0;
        }
        } else {
        // Le launcher est en bas donc on met le tooltip en haut
        y = position_launcher.top + $(launcher).height() / 2 - $(tooltip).height() / 2;
        if(y + $(tooltip).height() > $(window).height()){
            y = $(window).height() - $(tooltip).height();
        }
        }
    } else {
        // Le launcher est trop proche du bord donc on le met au centre
        x = $(window).width() / 2 - $(tooltip).width() / 2;
        y = $(window).height() / 2 - $(tooltip).height() / 2;
    }

    tooltip.css('left', x);
    tooltip.css('top', y);

    tooltip.css('position', 'absolute');
    tooltip.show("clip", 100);
    tooltip.css('display', 'flex');

    $(launcher).on("mouseleave", function(){
        tooltip.hide("clip", 100);
    });
    }
}

function toogleToolbar($forced_hidden = false){
    if($('.app-toolbar-content').hasClass('hidden') == false || $forced_hidden){
        $(".app-toolbar-content").addClass("hidden");
        $(".app-btn-show-toolbar-footer").show("blind", 300);
        $(".dropdown-item-toogletoolbar-button").text("Afficher la barre d'outils");
    } else {
        $(".app-toolbar-content").removeClass("hidden");
        $(".app-btn-show-toolbar-footer").hide("blind", 300);
        $(".dropdown-item-toogletoolbar-button").text("Masquer la barre d'outils");
    }
}
function toogleFooter($forced_hidden = false){
    if($('footer').hasClass('hidden') || $forced_hidden){
        $("footer").addClass("hidden");
        $("footer").hide("blind", 300);
    } else {
        $("footer").removeClass("hidden");
        $("footer").show("blind", 300);
    }
}
function toogleMenu($forced_closed = false){
    if($('.app').hasClass('app-extend') || $forced_closed){
        $(".app").removeClass("app-extend").addClass("app-fold");
        $(".app-nav").hide("drop", 300);
    } else {
        $(".app").removeClass("app-fold").addClass("app-extend");
        $(".app-nav").show("drop", 300);
    }
}

function checkboxButtonToogle(checkbox, classe, uniqid, nameofattr, valueofattr, inputname){
    if($(checkbox).is(":checked")){
        classe.update(uniqid, {action: "add", [nameofattr]: valueofattr}, inputname, IS_VALUE);
        if($(checkbox).data('color') != ''){
            $(checkbox).parent().find('label').addClass('back-'+$(checkbox).data('color')+'-d-2');
            $(checkbox).parent().find('label').addClass('text-white bold');
        }

    } else {
        classe.update(uniqid, {action: "remove", [nameofattr]: valueofattr}, inputname, IS_VALUE);
        if($(checkbox).data('color') != ''){
            $(checkbox).parent().find('label').removeClass('back-'+$(checkbox).data('color')+'-d-2');
            $(checkbox).parent().find('label').removeClass('text-white bold');
        }
    }
}