<?php 
$GLOBALS['project'] = [
    "name" => "Dofus Jdr",
    "logo" => "medias/logos/logo.png",
    "background_img" => "medias/logos/background.png",
    "logo_mini" => "medias/logos/logo_dice_20.svg",
    "description" => "Ce jeu de rôle est un mode de Donjon & Dragon où les règles ont été adapté au monde des douze, c'est à dire à l'univers du jeu vidéo Dofus.",
    "keywords" => "dofus, jdr, jeu de rôle, d&d, monde des douze",
    "version" => "1.3",
    "bookmark_name" => "Grimoire",
    "stability" => "α", // β ou α
    "stability_verbal" => "alpha",
    "author" => "Goodwater",
    "mail" => [
        "contact" => "contact@jdr.iota21.fr",
        "admin" => "contact@jdr.iota21.fr",
        "smtp_port" => "465",
        "smtp_host" => "jdr.iota21.fr",
        "smtp_username" => "contact@jdr.iota21.fr",
        "smtp_password" => "Ru4^jdriota21^Ru4",
        "smtp_secure" => "ssl"
    ],
    "github" => "https://github.com/SamGoodwater/DofusJdr",
    "var_globals_project_not_accessible_to_js" => 
    [
        "mail",
        "author",
        "github"
    ]
]; ?>