<?php

trait CalcStats
{ 
    static function convertLevel($val){
      $val = trim($val);
      if(is_numeric($val)){
        if($val <= 0){
          return 0;
        } else {
          return round($val / 10);
        }
      } else {
        return -666;
      }  
    }
  
    static function convertStat($val){
      $val = trim($val);
      if(is_numeric($val)){
        if($val <= 0){
          return 0;
        } else {
          return round($val / 10);
        }
      } else {
        return -666;
      }  
    }
  
    // Rapporte la valeur d'initiative sur 20 en partant d'un max de 2000
    static function convertIni($val){
      $val = trim($val);
      if(is_numeric($val)){
        if($val <= 0){
          return 0;
        } else {
          return round($val / 100);
        }
      } else {
        return -666;
      }  
    }
  
    static function convertVitality($val){
      $val = trim($val);
      if(is_numeric($val)){
        if($val <= 0){
          return 1;
        }elseif($val > 0 && $val <= 100) {
          return round($val * 2 / 10 + 10 ); // 10 à 20
        }elseif($val > 100 && $val <= 500) {
          return round($val / 10 + 10); // 20 à 50
        }elseif($val > 500 && $val <= 1000) {
          return round($val / 10); // 50 à 100
        }elseif($val > 1000 && $val <= 5000) {
          return round($val / 20 + 50); // 100 à 300
        }elseif($val > 5000 && $val <= 10000) {
          return round($val / 16); // 312 à 625
        }elseif($val > 10000) {
          return round($val / 15); // 666
        }else{
          return -666;
        }
  
      } else {
        return -666;
      }  
    }
  
    // Met un pourcentage d'esquive sur un échelle de 20
    static function convertDodge($val){
      $val = trim($val);
      if(is_numeric($val)){
        if($val <= 0){
          return 0;
        } else {
          return round($val * 20 / 100);
        }
      } else {
        return -666;
      }  
    }
  
    // Met un pourcentage de résistance sur un échelle de 5
    static function convertRes($val){
      $val = trim($val);
      if(is_numeric($val)){
        if($val <= 0){
          return 0;
        } else {
          return round($val * 5 / 100);
        }
      } else {
        return -666;
      }  
    }
  
    // Permet de calculer une expression (exp) en changement les termes par des valeurs [name1 => value1, ...]
    static function calcExp($exp, $vars = []){
        if(!empty($vars)){
            foreach ($vars as $name => $value) {
                if(is_numeric($value)){
                    $exp = str_replace($name, $value, $exp);
                }
            }
        }
        $exp = str_replace(',', ".", $exp);
        $exp = str_replace(' ', "", $exp);
        try {
            $exp = eval("return ".$exp .";");
        } catch (\Throwable $th) {
        }
  
        if(is_numeric($exp)){
            return round($exp);
        } else {
            return "error";
        }
    }

    static function calcMaster_bonus($level){
      if(is_integer($level)){
        $master_bonus = round(1 + 0.25 * $level, 0, PHP_ROUND_HALF_DOWN);
        if($master_bonus >= 1 && $master_bonus <= 6){
          return $master_bonus;
        }
      }
        return 1;
    }
}
  