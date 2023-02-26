<?php
/* Configure le script en français */
setlocale (LC_TIME, 'fr_FR','fra');
//Définit le décalage horaire par défaut de toutes les fonctions date/heure
date_default_timezone_set("Europe/Paris");

require_once 'src/required.php'; // Inclu l'ensemble des fichiers utiles, settings, traits ...
if(session_status() == PHP_SESSION_NONE){session_start();}
ControllerConnect::connect();
$_SESSION['JqueryAppel'] = false;
$_SESSION['CSSAppel'] = false;
header('Access-Control-Allow-Origin: *');
$router = new Router($_SERVER['REQUEST_URI']);