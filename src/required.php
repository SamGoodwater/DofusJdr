<?php
// ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺ SETTINGs ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺
    require_once('src/conf.php');
    // Dompdf
    require_once 'src/php/dompdf/autoload.inc.php';

// ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺ TRAITs ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺
    foreach(scandir("src/traits/") as $file) {
        $path_parts = pathinfo($file);
        $file = "src/traits/" . $file;
        if(file_exists($file) && $path_parts['extension'] == "php"){
            require_once($file);
        }
    }

// ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺ FONCTION ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺
    function secureURL($string){
        $string = trim(str_replace("../","",str_replace(";","",str_replace("%","",$string))));
        return $string;
    }

// ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺ Chargement auto des classes ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺
    spl_autoload_register(function($class) {

        $dirs = array(
            "controller/",
            "model/",
            "view/"
        );
        foreach(scandir("controller/") as $file) {
            $path = "controller/" . $file . '/';
            if(is_dir($path) && $file != "." && $file != ".."){
                $dirs[] = $path;
            }
        }
        foreach(scandir("model/") as $file) {
            $path = "model/" . $file . '/';
            if(is_dir($path) && $file != "." && $file != ".."){
                $dirs[] = $path;
            }
        }
        foreach( $dirs as $dir ) {
            if (file_exists($dir.$class.'.php')) {
                require_once($dir.$class.'.php');
                return;
            }
        }
    });

// ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺ Chargement des classes ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺
    require_once('src/php/File.php');
    require_once('src/php/FileManager.php');
