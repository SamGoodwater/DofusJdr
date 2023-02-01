<?php

abstract class Manager{
    use CheckingFunctions, SecurityFct;

    protected $_bdd = array();

    public function __construct(){
        $this->_bdd = $this->dbConnect();    
    }

    protected function dbConnect(){
        try{
            $db = new PDO('mysql:host='. $GLOBALS["pdoHost"] .';dbname='. $GLOBALS["pdoName"] . ';charset=utf8', $GLOBALS["pdoLogin"] , $GLOBALS["pdoPassword"]);
            $db->exec("set names utf8");
            // $db->query("SET NAMES UTF8");
            return $db;
        } catch(Exception $e) {
            throw new Exception('Impossible de se connecter à la base de donnée' . $e);
        }
    }
    
    protected function securite($string){ // Permet de protéger toutes les données reçu depuis la base de donnée
        if(is_array($string)){
            foreach ($string as $key => $value) {
                $new_string[$key] =   $this->securite($value);
            }
            return $new_string;
        } else {
            return htmlspecialchars_decode($string);
        }
    }

    public function little_secure($text){

        // suppression des balises <script>
        $text = preg_replace('#<script(.*)/script>#Ui', '', $text);
        // suppression des balises <iframe>
        $text = preg_replace('#<iframe(.*)/iframe>#Ui', '', $text);
        // suppression des balises <form>
        $text = preg_replace('#<form(.*)/form>#Ui', '', $text);
        // suppression des attributs js
        $text = preg_replace('#on[a-zA-Z]*(=|:)#Ui', '', $text);

        return $text;
    }
}
