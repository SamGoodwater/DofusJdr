<?php

abstract class Controller{

  use CheckingFct, SecurityFct, CalcStats;

  const IS_INPUT = 0;
  const IS_VALUE = 1;
  const IS_CHECKBOX = 2;
  const IS_CKEDITOR = 3;
  const IS_FILE = 4;

  const SIZE_SM = 576;
  const SIZE_MD = 768;
  const SIZE_LG = 993;
  const SIZE_XL = 1200;
  const SIZE_XXL = 1400;
  const SIZE_FL = -1;
  const RESPONSIVE = "responsive";

  const DIR_IMG = "";

  protected $obj = null;
  protected $obj_old = null;

  protected $_model_name = "";
  protected $_manager_name = "";
  public function __construct(){
    $this->_model_name = strtolower(substr(get_class($this), 10));
    $this->_manager_name = ucfirst($this->_model_name)."Manager";
  }

  public function countAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => true,
      'value' => "",
      'error' => 'erreur inconnue',
    ];
    if(!$currentUser->getRight($this->_model_name, User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{
        $manager = new $this->_manager_name();

      $return["value"] = $manager->countAll();
    }
    echo json_encode($return);
    flush();
  }
  public function getFromUniqid(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => [
        'visual' => "",
        'title' => "",
        "options" => [
          "remove" => "",
          'edit' => "",
          'linkshare' => "",
          'bubbleid' => "",
          'bookmark' => [
            'uniqid' => "",
            'classe' => "",
            'active' => false
          ]
        ]
      ],
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight($this->_model_name, User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
        $manager = new $this->_manager_name();
        $user = ControllerConnect::getCurrentUser();

        $display = Content::DISPLAY_CARD;
        if(isset($_REQUEST['display'])){
          if(isset(Content::DISPLAY[$_REQUEST['display']])){
            $display = $_REQUEST['display'];
          }
        }

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){

            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);
            $title = $obj->getName($display);
            if(!$this->includeHtmlTag($title)){
              $title = ucfirst($title);
            }

            $edit = ucfirst(get_class($obj)).".open('".$obj->getUniqid()."', Controller.DISPLAY_CARD);";

            if($user->getRight($this->_model_name, User::RIGHT_WRITE)){
              $remove = ucfirst(get_class($obj)).".remove('".$obj->getUniqid()."');";
              if($_REQUEST['display'] != Content::DISPLAY_EDITABLE){
                $edit = ucfirst(get_class($obj)).".open('".$obj->getUniqid()."', Controller.DISPLAY_EDITABLE);";
              }
            } else {
              $remove = "";
              $edit = "";
            }

            $return['value'] = array(
              'visual' => $obj->getVisual(new Style(["display" => $display])),
              "title" => $obj->getName($display),
              'options' => [
                "remove" => $remove,
                'edit' => $edit,
                'linkshare' => "@" . get_class($obj) . "~" . $obj->getUniqid(),
                'bubbleid' => $obj->getUniqid(), 
                'bookmark' => [
                  'uniqid' => $obj->getUniqid(),
                  'classe' => strtolower(get_class($obj)),
                  'active' => $user->in_bookmark($obj)
                ],
              ]
            );
            $return['state'] = true;
          }else {
            $return['error'] = 'Impossible de récupérer les données';
          }
      }
    }

    echo json_encode($return);
    flush();
  }
  public function update(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight($this->_model_name, User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['uniqid'], $_REQUEST['type'], $_REQUEST['value'])){
        $return['error'] = 'Impossible de récupérer les données';

      } else {

            $manager = new $this->_manager_name();

            if($manager->existsUniqid($_REQUEST['uniqid'])){

              $this->obj = $manager->getFromUniqid($_REQUEST['uniqid']);
                $method = "set".ucfirst($_REQUEST['type']);

                if(method_exists($this->obj,$method)){
                    $this->obj_old = clone $this->obj;
                    // Avant avoir terminé la mise à jour, vérifiez si la classe fille a un événement spécifique à déclencher
                    if(method_exists($this, 'eventBeforeUpdate')) {
                      $this->eventBeforeUpdate();
                    }

                    $result = $this->obj->$method($_REQUEST['value']);
                    if($result){
                      $this->obj->setTimestamp_updated(time());
                      $manager->update($this->obj);
                      $return['state'] = true;

                      // Après avoir terminé la mise à jour, vérifiez si la classe fille a un événement spécifique à déclencher
                      if(method_exists($this, 'eventAfterUpdate')) {
                          $this->eventAfterUpdate();
                      }

                    } else {
                      $return['error'] = $result;
                    }

                } else {
                  $return['error'] = "Aucun type correspondant dans l'objet";
                }

            } else {
              $return['error'] = 'Impossible de récupérer l\'objet.';
            }

      }

    }

    echo json_encode($return);
    flush();
  }
  public function upload(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight($this->_model_name, User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

        if(isset($_REQUEST['name_file'], $_REQUEST['uniqid'], $_FILES['file'])){
          $manager = new $this->_manager_name();

          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            if(isset(ucfirst($this->_model_name)::FILES[$_REQUEST['name_file']])){
              $data = ucfirst($this->_model_name)::FILES[$_REQUEST['name_file']];

              if(isset($data['naming']) && isset($data['dir']) && isset($data['type']) && isset($data['default']) && !empty($data['naming']) && !empty($data['dir']) && !empty($data['type']) && !empty($data['default'])){
                  $dirname = FileManager::formatPath($data['dir'], true, true);
                  $name = FileManager::solveNameFromPaternAndObject($obj, $data['naming']);
                  
                  $fileManager = new FileManager(array(
                    "dirname" => $dirname,
                    "file" => $_FILES["file"],
                    "name" => $name
                  ));
                  $fileManager->setFormatallowed(FileManager::getListeExtention(FileManager::FORMAT_IMG));
                  $result = $fileManager->upload();
                  
                  if($result["state"]){
                      $return['value'] = $result["path"];
                      $obj->setTimestamp_add();
                      $obj->setTimestamp_updated();
                      $manager->update($obj);
                      $return['state'] = true;
                      $return['script'] = ucfirst($this->_model_name) . ".open('".$obj->getUniqid()."', Controller.DISPLAY_EDITABLE);";

                  } else {
                    $return['error'] = $result['error'];
                  }

              } else {
                $return['error'] = "Impossible de récupérer les données de l'objet.";
              }

            } else {  
              $return['error'] = "Aucun fichier correspondant dans l'objet";
            }

          }else {
            $return['error'] = "Impossible de récupérer la référence de l'object.";
          }

        } else {
          $return['error'] = "Pas de fichier envoyé";
        }
    }
    
    echo json_encode($return);
    flush();
  }
  public function remove(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight($this->_model_name, User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          // Récupération des objets
            $manager = new $this->_manager_name();

          // Récupération de l'objet
            if($manager->existsUniqid($_REQUEST['uniqid'])){

              $obj = $manager->getFromUniqid($_REQUEST['uniqid']);
              $manager->delete($obj);
              $return['state'] = true;

            } else {
              $return['error'] = 'Cet objet n\'existe pas.';
            }
      }
    
    }

    echo json_encode($return);
    flush();
  }
}
