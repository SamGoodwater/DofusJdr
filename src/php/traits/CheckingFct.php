<?php

trait CheckingFct
{
    function returnBool($data, $default = false){
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
        if (preg_match("#^[a-z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._\s-]+@[a-z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._\s-]{2,}\.[a-z]{2,4}$#", $email)){
            return true;
        }else{
            return false;
        }
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
        if (preg_match("#^(http|https|ftp):#", $lien))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    function isNumeroTel($numero){
        if (preg_match("#^\+[0-9]{6,20}$#", $numero)){
            return true;
        }else{
            return false;
        }
    }
    function isCaractere($text, $regex="#~|\|#"){
        if (preg_match($regex, $text)){
            return true;
        } else {
            return false;
        }
    }
    function isColorHexa($couleur){
        return preg_match("/^#(?:(?:[a-f\d]{3,4}){1,2})$/i",$couleur);
    }
    function isTimestamp($time){
      if(preg_match("/^-*[0-9]{1,19}$/", $time )){
        return true;
      }else {
        return false;
      }
    }

    function is_serialized($var ) {
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
  