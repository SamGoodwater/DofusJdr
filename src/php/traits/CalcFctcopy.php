<?php

trait CalcFct
{
    static function getMinMaxFromFormule(string | int $formule){
        $count_min = 0;
        $count_max = 0;
        $same = false;
        if(!preg_match_all("/\[(.*)\]/i", $formule, $matches, PREG_SET_ORDER)){
            $count_min = $formule;
            $count_max = $formule;
            $same = true;
        }else{
            $formule = str_replace(' ', '', $matches[0][1]);
                if(preg_match_all("/(\d{1,}d\d{1,})/i", $formule, $dices, PREG_SET_ORDER)){
                    foreach ($dices as $dice) {
                        $dice = explode('d', $dice[0]);
                        $count_min += $dice[0];
                        $count_max += $dice[0] * $dice[1];
                    }
                }
                if(preg_match_all("/(\d{1,}-\d{1,})/i", $formule, $values, PREG_SET_ORDER)){
                    foreach ($values as $value) {
                        $value = explode('-', $value[0]);
                        $count_min += $value[0];
                        $count_max += $value[1];
                    }
                }
                if(preg_match_all("/(\+\d{1,}|\d{1,}\+)/i", $formule, $additions, PREG_SET_ORDER)){
                    foreach ($additions as $addition) {
                        $addition = str_replace('+', '', $addition[0]);
                        $count_min += $addition;
                        $count_max += $addition;
                    }
                }
                if(preg_match_all("/(Niveau.*(\/|\*)(\d{1,}))/i", $formule, $level, PREG_SET_ORDER)){
                    $level = $level[0];
                    if(is_integer(intval($level[3]))){
                        if($level[2] == "/"){
                            $count_min += round(1 / intval($level[3]));
                            $count_max += round(20 / intval($level[3]));
                        } elseif($level[2] == "*"){
                            $count_min += round(1 * intval($level[3]));
                            $count_max += round(20 * intval($level[3]));
                        }
                    }
                } else {
                    if(preg_match_all("/(Niveau.*)/i", $formule, $level, PREG_SET_ORDER)){
                        $count_min += 1;
                        $count_max += 20;
                    }
                }
        }
        
        return [
            'min' => $count_min,
            'max' => $count_max,
            "same" => $same
        ];
    }
    static function getValueFromFormule(string | int $formule, string | int $var){
        $var = intval(str_replace(' ', '', $var));
        $val = 0;
        if(!preg_match_all("/\[(.*)\]/i", $formule, $matches, PREG_SET_ORDER)){
            $val = $formule;
        }else{
            $formule = str_replace(' ', '', $matches[0][1]);
                if(preg_match_all("/niveau|level/i", $formule, $level, PREG_SET_ORDER)){
                    $val += $var;
                }
                if(preg_match_all("/((\*|\/)(\d{1,})|(\d{1,})(\*|\/))/i", $formule, $multiplications, PREG_SET_ORDER)){
                    foreach ($multiplications as $multiplication) {
                        $multiplication = array_filter($multiplication);
                        if(isset($multiplication[4]) && !isset($multiplication[2])){
                            $multiplication[2] = $multiplication[4];
                        }
                        if(isset($multiplication[5]) && !isset($multiplication[3])){
                            $multiplication[3] = $multiplication[5];
                        }
                        if($multiplication[2] == "*"){
                            $val = $val * intval($multiplication[3]);
                        } elseif($multiplication[2] == "/"){
                            $val = $val / intval($multiplication[3]);
                        } elseif($multiplication[3] == "*"){
                            $val = $val * intval($multiplication[2]);
                        } elseif($multiplication[3] == "/"){
                            $val = $val / intval($multiplication[2]);
                        }
                    }
                }
                if(preg_match_all("/([^*\/](\+|\-)(\d{1,})|(\d{1,})(\+|\-))/i", $formule, $additions, PREG_SET_ORDER)){
                    foreach ($additions as $addition) {
                        $addition = array_filter($addition);
                        if(isset($addition[4]) && !isset($addition[2])){
                            $addition[2] = $addition[4];
                        }
                        if(isset($addition[5]) && !isset($addition[3])){
                            $addition[3] = $addition[5];
                        }
                        if($addition[2] == "+"){
                            $val += intval($addition[3]);
                        } elseif($addition[2] == "-"){
                            $val -= intval($addition[3]);
                        } elseif($addition[3] == "+"){
                            $val += intval($addition[2]);
                        } elseif($addition[3] == "-"){
                            $val -= intval($addition[2]);
                        }
                    }
                }
        }
        return round(intval($val), 0, PHP_ROUND_HALF_DOWN);
    }
}
  