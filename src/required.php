<?php

// ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺ AUTRES ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺
    // Ajouter des dossiers ou des fichiers au tableau
    $to_require = [
        "src/php/traits/",
        "src/php/traits/modules/",
        "src/php/conf.php",
        "src/php/settings.php"
    ];

    foreach ($to_require as $dir) {
        if(is_dir($dir) && file_exists($dir)){
            foreach(scandir($dir) as $file) {
                if($file != "." && $file != ".."){
                    if(file_exists($dir.$file) && pathinfo($dir.$file, PATHINFO_EXTENSION) == "php"){
                        require_once $dir.$file;
                    }
                }
            }
        }elseif(is_file($dir) && file_exists($dir) && pathinfo($dir, PATHINFO_EXTENSION) == "php"){
            require_once $dir;
        }
    }

// ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺ AUTOLOAD des CLASSES via Composer ☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺
    require_once "vendor/autoload.php";