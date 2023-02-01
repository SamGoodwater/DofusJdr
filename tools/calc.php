<?php

$exp = "1 + 0,263157895 * (level - 1)";
$exp = str_replace('level', "3", $exp);
$exp = str_replace(',', ".", $exp);
$exp = str_replace(' ', "", $exp);
try {
    $exp = eval("return ".$exp .";");
} catch (\Throwable $th) {
}

if(is_numeric($exp)){
    echo round($exp);
} else {
    echo $exp;
}

?>