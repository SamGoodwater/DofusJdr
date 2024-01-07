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
function isMobileSize(){
    let x = window.matchMedia("(max-width: 768px)");
    if (x.matches) {
        return true;
    }
}