function getLinkIframe(value){
    try {
        return $(value).attr("src");
      } catch (error) {
        return "***error***";
      }
}

function array_unique(array) { //cleanArray removes all duplicated elements
  var i, j, len = array.length, out = [], obj = {};
  for (i = 0; i < len; i++) {
    obj[array[i]] = 0;
  }
  for (j in obj) {
    out.push(j);
  }
  return out;
}

// Exemple d'utilisation : copyToClipboard('<?=$_SERVER['SERVER_NAME'] . '/#'. $page->getUrl_name()?>')
const copyToClipboard = str => {
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
  MsgAlert("Le lien a bien été copié","", "green" , 3000);
};

function detailFormatter(index, row){ // Permet d'afficher les détails dans les tableaux
    var html = []
    $.each(row, function (key, value) {
        if(key == "detailView"){
            html.push(value)
        }
    })
    return html.join('')
}

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
        //   appendlink = "<p class='text-center m-0 p-0 font-size-0-8'><a target='_blank' href='" + link + "'><i class='fas fa-arrow-circle-right'></i></a></p>";
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

// Permet de modifier le badge associer à un élément lorsqu'un range est modifié
  function changeRangeText(input){
    var value = input.value;
    var span = $(input).parent().find("span");
    switch (value) {
        case '0':
            span.text("Aucun");
            span.attr("title","Aucun");
            span.removeClass("border-blue-d-3").removeClass("border-green-d-1").addClass("border-grey-d-3");
            span.removeClass("text-blue-d-3").removeClass("text-green-d-1").addClass("text-grey-d-3");
        break;
        case '1':
            span.text("Lecture");
            span.attr("title","Lecture");
            span.removeClass("border-grey-d-3").removeClass("border-green-d-3").addClass("border-blue-d-3");
            span.removeClass("text-grey-d-3").removeClass("text-green-d-3").addClass("text-blue-d-3");
        break;
        case '2':
            span.text("Écriture");
            span.attr("title","Écriture");
            span.removeClass("border-blue-d-3").removeClass("border-grey-d-3").addClass("border-green-d-3");
            span.removeClass("text-blue-d-3").removeClass("text-grey-d-3").addClass("text-green-d-3");
        break;
        default:
          console.log("Nda");
    }
  }

// Détection de Mobile
  function isMobile(){
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Kindle|Silk|Opera Mini/i.test(navigator.userAgent)) {
      return true;
    } else {
      return false;
    }
  }
  
  function getSizeScreen(){
    let width = $(window).width();
    if(width <= Page.SIZE_SM){
      return Page.SIZE_SM;
    } else if(width <= Page.SIZE_MD){
      return Page.SIZE_MD;
    } else if(width <= Page.SIZE_LG){
      return Page.SIZE_LG;
    } else if(width <= Page.SIZE_XL){
      return Page.SIZE_XL;
    } else {
      return Page.SIZE_XXL;
    }
  }

  function minmaxDice(){
    var number_dice = $("#number_dice").val();
    if(number_dice == 0 || number_dice == ""){
        number_dice = 1;
    }
    var type_dice = $("#type_dice").val();
    if(type_dice == 0 || type_dice == ""){
        type_dice = 6;
    }
    var add_int = $("#add_int").val();
    if(add_int == 0 || add_int == ""){
        add_int = 0;
    }
    var min = 0;
    var max = 0;
    for(var i = 0; i < number_dice; i++){
        max += parseInt(type_dice);
        min += parseInt(1);
    }
    min += parseInt(add_int);
    max += parseInt(add_int);
    $("#min-max").html("Minimum : " + min + " | Maximum : " + max);

}
function rollDice() {
    var number_dice = $("#number_dice").val();
    if(number_dice == 0 || number_dice == ""){
        number_dice = 1;
    }
    var type_dice = $("#type_dice").val();
    if(type_dice == 0 || type_dice == ""){
        type_dice = 6;
    }
    var add_int = $("#add_int").val();
    if(add_int == 0 || add_int == ""){
        add_int = 0;
    }
    var result = 0;
    for(var i = 0; i < number_dice; i++){
        result += Math.floor(Math.random() * type_dice) + 1;
    }
    result += parseInt(add_int);
    $("#result_dice").html(result);
}

// Met la première lettre en majuscule
function ucFirst(str) {return str && str[0].toUpperCase() + str.slice(1);}

let is_tooltipsCustom_triggerable = true;
function showTooltips(launcheur, target){
  if(is_tooltipsCustom_triggerable){
    // Il n'est plus possible de déclencher cette fonction avant 200ms
    is_tooltipsCustom_triggerable = false;
    setTimeout(function(){is_tooltipsCustom_triggerable = true;}, 200);

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
    
    let position_launcher = $(launcheur).offset();
    // var position_tooltip = tooltip.offset();

    let point_centered_launcher = {
      x: position_launcher.left + $(launcheur).width() / 2,
      y: position_launcher.top + $(launcheur).height() / 2
    };
    
    let direction = "";

    if($(window).width() / 2 > point_centered_launcher.x){
      // Le point est à gauche
      direction = "R";
    } else {
      // Le point est à droite
      direction = "L";
    }
    if($(window).height() / 2 > point_centered_launcher.y){
      // Le point est plus en haut
      direction += "B";
    } else {
      // Le point est plus en bas
      direction += "T";
    }

    let x = $(window).width() / 2 - $(launcheur).width() / 2;
    let y = $(window).height() / 2 - $(launcheur).height() / 2;

    if($(launcheur).width() < $(window).width() / 2 && $(launcheur).height() < $(window).height() / 2){
      console.log(direction);
      switch (direction) {
        case "LT":
          // le coin en bas à droite du tooltip doit être coller au coin en haut à gauche du launcher.
          x = position_launcher.left - $(launcheur).width();
          y = position_launcher.top - $(tooltip).height();
        break;
        case "LB":
          // le coin en haut à droite du tooltip doit être coller au coin en bas à gauche du launcher.
          x = position_launcher.left - $(launcheur).width();
          y = position_launcher.top + $(launcheur).height();
        break;
        case "RT":
          // le coin en bas à gauche du tooltip doit être coller au coin en haut à droite du launcher.
          x = position_launcher.left + $(launcheur).width();
          y = position_launcher.top - $(tooltip).height();
        break;
        case "RB":
          // le coin en haut à gauche du tooltip doit être coller au coin en bas à droite du launcher.
          x = position_launcher.left + $(launcheur).width();
          y = position_launcher.top + $(launcheur).height();
        break;
        default:
          x = $(window).width() / 2 - $(launcheur).width() / 2;
          y = $(window).height() / 2 - $(launcheur).height() / 2;
        break;
      }
    }

    tooltip.css('left', x);
    tooltip.css('top', y);

    tooltip.css('position', 'absolute');
    tooltip.show("clip", 100);
    tooltip.css('display', 'flex');

    $(launcheur).on("mouseleave", function(){
      tooltip.hide("clip", 100);
    });
  }
}