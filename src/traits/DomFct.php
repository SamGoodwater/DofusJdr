<?php

trait DomFct
{
    static function replaceAttr($string, $attribut, $value){
        if(empty($string)){ return "";}

        $pattern = '/'.$attribut.'="(|.+?)"/i';
        if(preg_match ($pattern , $string)){
            $replacement = $attribut . '="'. $value . '"';
            $string = preg_replace($pattern, $replacement, $string);
        } else {
            $chars = preg_split('/ /', $string, 2);
            if(is_array($chars) && !empty($chars)){
                $string = $chars[0] . ' ' . $attribut . '="' . $value . '" ' .  $chars[1];
            } else {
                $string = "Erreur";
            }
        }
        return $string;
    }

}
  