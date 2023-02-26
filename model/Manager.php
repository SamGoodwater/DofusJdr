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
            if($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'dofus.jdr') { // LOCAL
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            $db->exec("set names utf8");
            return $db;
        } catch(Exception $e) {
            throw new Exception('Impossible de se connecter à la base de donnée' . $e);
        }
    }
}
