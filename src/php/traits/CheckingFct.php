<?php

trait CheckingFct
{
    function returnBool($data, bool $default = false){
        if(is_bool($data)){
            if($data){
                return 1;
            } else {
                return 0;
            }
        }else{
            return $default;
        }
    }
    function isEmail($email){
        if(preg_match("#^[a-z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._\s-]+@[a-z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._\s-]{2,}\.[a-z]{2,4}$#", $email)){
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
    function isPhoneNumber($numero){
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
  