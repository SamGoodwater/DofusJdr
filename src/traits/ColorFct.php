<?php

trait ColorFct
{

    public function hex2rgb($color){
        if ($color[0] == '#')
            $color = substr($color, 1);

        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0].$color[1],
                                    $color[2].$color[3],
                                    $color[4].$color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        else
            return false;

        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

        return array(
            'red' => $r,
            'green' => $g,
            'blue' => $b
        );
    }

    public function rgb2hex($r, $g=-1, $b=-1){
        if (is_array($r) && sizeof($r) == 3)
            list($r, $g, $b) = $r;

        $r = intval($r); $g = intval($g);
        $b = intval($b);

        $r = dechex($r<0?0:($r>255?255:$r));
        $g = dechex($g<0?0:($g>255?255:$g));
        $b = dechex($b<0?0:($b>255?255:$b));

        $color = (strlen($r) < 2?'0':'').$r;
        $color .= (strlen($g) < 2?'0':'').$g;
        $color .= (strlen($b) < 2?'0':'').$b;
        return '#'.$color;
    }

    public function getColorContrast($color, $format = Content::FORMAT_COLOR_HEX){
        switch ($format) {
            case Content::FORMAT_COLOR_VERBALE:
                $white = 'white';
                $black = "black";
            break;
            default:
                $white = '#ffffff';
                $black = "#000000";
            break;
        }

        if (preg_match ("/^#(([a-f\d]{3,5}){1,2})$/i" , $color ) && !empty($color) ){

            $rgb = $this->hex2rgb($color);

            if($rgb['red'] + $rgb['blue'] + $rgb['green'] > 383){ // Si supérieur alors la couleur est plutôt blanche, sinon plutôt noir
                return $black;
            } else {
                return $white;
            }
        } else {
            return $black;
        }
    }
    
    static function getColorFromLetter($letter, $degraded = false){
        $letter = trim($letter);
        $letter = str_replace(" ", "", $letter);
        if(!is_numeric($letter)){
            if(strlen($letter) > 1){
                $letter = substr($letter,0,1);
            }
        }

        if($degraded){
            $array = [
                "a" => 'blue',
                "b" => 'light-blue',
                "c" => 'cyan',
                "d" => 'teal',
                "e" => 'green',
                "f" => 'light-green',
                "g" => 'lime',
                "h" => 'yellow',
                "i" => 'amber',
                "j" => 'orange',
                "k" => 'deep-orange',
                "l" => 'brown',
                "m" => 'red',
                "n" => 'pink',
                "o" => 'purple',
                "p" => 'deep-purple',
                "q" => 'indigo',
                "r" => 'blue-grey',
                "s" => 'blue',
                "t" => 'light-blue',
                "u" => 'cyan',
                "v" => 'teal',
                "w" => 'green',
                "x" => 'light-green',
                "y" => 'lime',
                "z" => 'yellow',
                "0" => 'amber',
                "1" => 'orange',
                "2" => 'deep-orange',
                "3" => 'brown',
                "4" => 'red',
                "5" => 'pink',
                "6" => 'purple',
                "7" => 'deep-purple',
                "8" => 'indigo',
                "9" => 'blue-grey',
                "10" => 'blue',
                "11" => 'light-blue',
                "12" => 'cyan',
                "13" => 'teal',
                "14" => 'green',
                "15" => 'light-green',
                "16" => 'lime',
                "17" => 'yellow',
                "18" => 'amber',
                "19" => 'orange',
                "20" => 'deep-orange',
                "21" => 'brown',
                "22" => 'red',
                "23" => 'pink',
                "24" => 'purple',
                "25" => 'deep-purple',
                "26" => 'indigo',
                "27" => 'blue-grey',
                "28" => 'blue',
                "29" => 'light-blue',
                "30" => 'cyan'
            ];
        } else {
            $array = [
                "a" => 'red',
                "b" => 'pink',
                "c" => 'purple',
                "d" => 'deep-purple',
                "e" => 'indigo',
                "f" => 'blue',
                "g" => 'light-blue',
                "h" => 'cyan',
                "i" => 'teal',
                "j" => 'green',
                "k" => 'light-green',
                "l" => 'lime',
                "m" => 'yellow',
                "n" => 'amber',
                "o" => 'orange',
                "p" => 'deep-orange',
                "q" => 'brown',
                "r" => 'grey',
                "s" => 'blue-grey',
                "t" => 'amber',
                "u" => 'cyan',
                "v" => 'deep-orange',
                "w" => 'brown',
                "x" => 'grey',
                "y" => 'blue-grey',
                "z" => 'red',
                "0" => 'red',
                "1" => 'cyan',
                "2" => 'purple',
                "3" => 'deep-orange',
                "4" => 'indigo',
                "5" => 'blue',
                "6" => 'lime',
                "7" => 'amber',
                "8" => 'teal',
                "9" => 'green',
                "10" => 'light-green',
                "11" => 'light-blue',
                "12" => 'yellow',
                "13" => 'cyan',
                "14" => 'orange',
                "15" => 'deep-purple',
                "16" => 'brown',
                "17" => 'grey',
                "18" => 'yellow',
                "19" => 'light-blue',
                "20" => 'cyan',
                "21" => 'teal',
                "22" => 'green',
                "23" => 'light-green',
                "24" => 'lime',
                "25" => 'blue-grey',
                "26" => 'amber',
                "27" => 'cyan',
                "28" => 'deep-orange',
                "29" => 'brown',
                "30" => 'grey'
            ];
        }

        if(array_key_exists(strtolower($letter), $array)){
            return $array[strtolower($letter)];
        }
    }

    static function isValidColor($color){
        $color = View::getColorWithoutShade($color);

        if(in_array($color, View::COLOR_ALLOWED) || isset(View::COLOR_CUSTOM[$color])){
            return true;
        } else {
            return false;
        }
    }

    static function getColorWithoutShade($color){
        $color = preg_replace('/-d-([0-9]{1,3})$/', '', $color);
        $color = preg_replace('/-l-([0-9]{1,3})$/', '', $color);
        return $color;
    }

}
  