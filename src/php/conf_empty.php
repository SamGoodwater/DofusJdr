<?php 
$GLOBALS['project'] = [
    "name" => "",
    "logo" => "medias/logos/logo.png",
    "background_img" => "medias/logos/background.png",
    "logo_mini" => "medias/logos/logo_mini.svg",
    "keywords" => "",
    "description" => "",
    "version" => "0",
    "bookmark_name" => "Signet",
    "stability" => "α", // β ou α ou S
    "stability_verbal" => "alpha", // béta, alpha ou stable
    "base_url" => stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://' . $_SERVER['SERVER_NAME'],
    "author" => "",
    "mail" => [
        "contact" => "contact@mail.fr",
        "admin" => "contact@mail.fr",
        "smtp_port" => "465",
        "smtp_host" => "mail.fr",
        "smtp_username" => "",
        "smtp_password" => "",
        "smtp_secure" => "ssl"
    ],
    "github" => "",
    "openia_api" => "",
    "var_globals_project_not_accessible_to_js" => 
    [
        "mail",
        "author",
        "github",
        "openia_api"
    ]
]; 

if($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '') { // LOCAL
    $conf_host = "localhost";
    $conf_login = "root";
    $conf_password = "";
    $conf_pdo_name = "";
} else { // Serveur
    $conf_host = "localhost";
    $conf_login = "";
    $conf_password = "*********";
    $conf_pdo_name = "";
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