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

try
{
    if (isset($_GET['a'],$_GET['c'])){
      $controller = "Controller".ucfirst($_GET['c']);
      $action = $_GET['a'];

      if(method_exists($controller,$action)){
        $param = null;
        if(isset($_GET['param']))
        {
          $param = $_GET['param'];
        }
        $route = new $controller();
        $route->$action($param);
      }else {
        echo "Erreur de route";
      }

    } else {

      // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! ATTENTION .HTACCESS doit rediriger vers le bon index.php  !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
      // Décomposé URL
        $url = explode('/',$_SERVER['REQUEST_URI']); // 0 = rien, (si local) 1 = Nom de domaine, 1 (online) 2 (si local) = intérêt
        if($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost') {
          $nb = 2;
        } else {
          $nb = 1;
        }
        $url = explode('&',$url[$nb]);
        $page = "";
        if(isset($url[0])){
          $page = $url[0];
        }
        $settings = "";
        if(isset($url[1])){
          $settings = $url[1];
        }
        include_once "view/template.php";
    }

} catch(Exception $e) { // S'il y a eu une erreur, alors...

}
