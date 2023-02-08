<?php
    if($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'dofusjdr') { // LOCAL
        $conf_host = "localhost";
        $conf_login = "root";
        $conf_password = "";
        $conf_pdo_name = "goodwater_jdr";
    } else { // Serveur
        $conf_host = "localhost";
        $conf_login = "*************";
        $conf_password = "************";
        $conf_pdo_name = "goodwater_jdr";
    }
        
    if(!isset($GLOBALS['pdoHost'])){
        $GLOBALS['pdoHost'] = $conf_host;
    }
    if(!isset($GLOBALS['pdoName'])){
        $GLOBALS['pdoName'] = $conf_pdo_name;
    }
    if(!isset($GLOBALS['pdoLogin'])){
        $GLOBALS['pdoLogin'] = $conf_login;
    }
    if(!isset($GLOBALS['pdoPassword'])){
        $GLOBALS['pdoPassword'] = $conf_password ;
    }
