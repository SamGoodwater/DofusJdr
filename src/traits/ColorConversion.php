<?php

trait ColorConversion
{
    function hex2rgb($color){
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

    function rgb2hex($r, $g=-1, $b=-1){
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

}
  