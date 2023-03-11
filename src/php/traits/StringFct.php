<?php

trait StringFct
{
    static function isVowel($letter){
        $letter = trim($letter);
        $letter = str_replace(" ", "", $letter);
        if(!is_numeric($letter)){
            if(strlen($letter) > 1){
                $letter = substr($letter,0,1);
            }
        }
        $letter = strtolower($letter);
        $vowel = array("a","e","i","o","u","y");
        if(in_array($letter, $vowel)){ 
            return true;
        }
        return false;
    }

}
  