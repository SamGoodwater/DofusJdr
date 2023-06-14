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

    static function removeSpecialCaractere(string | int $string){ // Enlève tout les accents et les caractères spéciaux
        $caracteres = array('à' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
        'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
        'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
        'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
        'Œ' => 'oe', 'œ' => 'oe',
        '$' => 's');
        $string = strtr($string, $caracteres);
        $string = preg_replace('#[^A-Za-z0-9]+#', '-', $string);
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        setlocale(LC_ALL, 'fr_FR');
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        $string = preg_replace('#[^0-9a-z]+#i', '_', $string);
        while(strpos($string, '--') !== false)
        {
            $string = str_replace('--', '_', $string);
        }
        $string = trim($string, '_');

        return $string;
    }
}
  