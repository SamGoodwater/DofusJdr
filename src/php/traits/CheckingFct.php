<?php

trait CheckingFct
{
    function returnBool($data, bool $default = false){
        $true = [true, 1, "1", "o", "O", "y", "Y", "oui", "yes", "OUI", "YES"];
        $false = [false, 0, "0", "n", "N", "no", "NO", "non", "NON"];
        if(in_array($data, $true)){
            return true;
        }elseif(in_array($data, $false)){
            return false;
        }else{
            return $default;
        }
    }
    function isEmail($email){
        return preg_match("#^[a-z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._\s-]+@[a-z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._\s-]{2,}\.[a-z]{2,4}$#", $email);
    }
    function isDate($date){
        if(is_numeric($date)){
          return true;
        }
        try{
          $dt = new DateTime($date);
          return true;
        }catch(Exception $ex){
          return false;
        }

    }
    function isURL($lien){
        return preg_match("#^(http|https|ftp):#", $lien);
    }
    function isPhoneNumber($numero){
        return preg_match("#^\+[0-9]{6,20}$#", $numero);
    }
    function isCaractere($text, $regex="#~|\|#"){
        return preg_match($regex, $text);
    }
    function isColorHexa($couleur){
        return preg_match("/^#(?:(?:[a-f\d]{3,4}){1,2})$/i",$couleur);
    }
    function isTimestamp($time){
      return preg_match("/^-*[0-9]{1,19}$/", $time );
    }
    function includeHtmlTag($str){
        return preg_match("/<[^<]+>/",$str);
    }

    function isSerialized($var ) {
        if (!is_string($var) || $var == '') {
            return false;
        }
        set_error_handler(function ($errno, $errstr) {});
        $unserialized = unserialize($var);
        restore_error_handler();
        if ($var !== 'b:0;' && $unserialized === false) {
            return false;
        }
        return true;
    } 
    
  
}
  