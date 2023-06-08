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
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight($this->_model_name, User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
        $manager = new $this->_manager_name();

        $display = Content::DISPLAY_CARD;
        if(isset($_REQUEST['display'])){
          if(isset(Content::DISPLAY[$_REQUEST['display']])){
            $display = $_REQUEST['display'];
          }
        }
        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){

            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);
            $return['value'] = array(
              'visual' => $obj->getVisual($display),
              "title" => $obj->getName($display),
              "bubbleId" => strtolower(get_class($obj)) . $obj->getUniqid()
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

  // Constantes de calcul des stats et d'équilibrages - DEVRA PASSER DANS UN FICHIER JSON
    const BALANCE_PA = [
        "classe" => [
            "max_item" => 12,
            "max_base" => 6,
            "min" => 0,
            "base" => 6,
            "expression_item" => "6 + 0,315789474 * (level - 1)",
            "expression_base" => "6",
            "bonus_item_max" => 6
        ],
        "mob" => [
            "max" => 16,
            "min" => 0,
            "base" => 4,
            "expression" => "6 + 0,315789474 * (level - 1)",
            "bonus" => 10
        ]
    ];
    const BALANCE_PM = [
        "classe" => [
            "max_item" => 6,
            "max_base" => 3,
            "min" => 0,
            "base" => 3,
            "expression_item" => "3 + 0,157894737 * (level - 1)",
            "expression_base" => "3",
            "bonus_item_max" => 3
        ],
        "mob" => [
            "max" => 10,
            "min" => 0,
            "base" => 3,
            "expression" => "3 + 0,2 * (level - 1)",
            "bonus" => 5
        ]
    ];
    const BALANCE_PO = [
        "classe" => [
            "max_item" => 6,
            "max_base" => 0,
            "min" => 0,
            "base" => 0,
            "expression_item" => "0,315789474 * (level - 1)",
            "expression_base" => "0",
            "bonus_item_max" => 6
        ],
        "mob" => [
            "max" => 8,
            "min" => 0,
            "base" => 0,
            "expression" => "0,421052632 * (level - 1)",
            "bonus" => 8
        ]
    ];
    const BALANCE_TOUCH = [
        "classe" => [
            "max_item" => 7,
            "max_base" => 0,
            "min" => 0,
            "base" => 0,
            "expression_item" => "0,368421053 * (level - 1)",
            "expression_base" => "0",
            "bonus_item_max" => 7
        ],
        "mob" => [
            "max" => 8,
            "min" => 0,
            "base" => 0,
            "expression" => "0,421052632 * (level - 1)",
            "bonus" => 8
        ]
    ];
    const BALANCE_INVOCATION = [
        "classe" => [
            "max_item" => 6,
            "max_base" => 1,
            "min" => 1,
            "base" => 1,
            "expression_item" => "1 + 0,263157895 * (level - 1)",
            "expression_base" => "0",
            "bonus_item_max" => 5
        ],
        "mob" => [
            "max" => 6,
            "min" => 0,
            "base" => 0,
            "expression" => "0,263157895 * (level - 1)",
            "bonus" => 6
        ]
    ];
    const BALANCE_INI = [
        "classe" => [
            "max_item" => 30,
            "max_base" => 10,
            "min" => -2,
            "base" => -1,
            "expression_item" => "0,842105263 * (level - 1)",
            "expression_base" => "-1 + (level-3)/2 + 1/level",
            "bonus_item_max" => 20
        ],
        "mob" => [
            "max" => 30,
            "min" => -5,
            "base" => -1,
            "expression" => "0,842105263 * (level - 1)",
            "bonus" => 20
        ]
    ];
    const BALANCE_TACLE = [
      "classe" => [
          "max_item" => 15,
          "max_base" => 10,
          "min" => -2,
          "base" => -1,
          "expression_item" => "-1 + 0,842105263 * (level - 1)",
          "expression_base" => "(level-2)/2.4",
          "bonus_item_max" => 5
      ],
      "mob" => [
          "max" => 15,
          "min" => -5,
          "base" => -1,
          "expression" => "-1 + 0,842105263 * (level - 1)",
          "bonus" => 10
      ]
    ];
    const BALANCE_SPEFICIFIC_MAIN = [
        "classe" => [
            "max_item" => 10,
            "max_base" => 10,
            "min" => -2,
            "base" => -1,
            "expression_item" => "1 + (level-3)/2 + 1/level",
            "expression_base" => "1 + (level-3)/2 + 1/level",
            "bonus_item_max" => 5
        ],
        "mob" => [
            "max" => 20,
            "min" => -2,
            "base" => -1,
            "expression" => "-1 + 0,578947368 * (level + 2)",
            "bonus" => 10
        ]
    ];
    const BALANCE_SKILL = [
        "classe" => [
            "max_item" => 18,
            "max_base" => 10,
            "min" => -2,
            "base" => -1,
            "expression_item" => "-1 + 0,9 * (level - 1)",
            "expression_base" => "-1 + 0,421052632 * ((level - 1) - (0,4 * level / 24))",
            "bonus_item_max" => 8
        ]
    ];
    const BALANCE_RECHARGE_WAKFU = [
        "classe" => [
            "max_item" => 20,
            "max_base" => 10,
            "min" => 0,
            "base" => 1,
            "expression_item" => "1 + 1 * (level - 1)",
            "expression_base" => "1 + (level-3)/2 + 1/level",
            "bonus_item_max" => 10
        ]
    ];
    const BALANCE_RES = [
        "classe" => [
            "max_item" => 5,
            "max_base" => 0,
            "min" => 0,
            "base" => 0,
            "expression_item" => "0,263157895 * (level - 1)",
            "expression_base" => "0",
            "bonus_item_max" => 5
        ],
        "mob" => [
            "max" => 10,
            "min" => 0,
            "base" => 0,
            "expression" => "0,263157895 * (level - 1)",
            "bonus" => 10
        ]
    ];
    const BALANCE_CA = [
        "classe" => [
            "max_item" => 25,
            "max_base" => 20,
            "min" => 8,
            "base" => 9,
            "expression_item" => "9 + 0,842105263 * (level - 1)",
            "expression_base" => "9 + 0,578947368 * (level)",
            "bonus_item_max" => 5
        ],
        "mob" => [
            "max" => 30,
            "min" => 5,
            "base" => 8,
            "expression" => "9 + 0,842105263 * (level - 1)",
            "bonus" => 10
        ]
    ];
    const BALANCE_DODGE = [
        "classe" => [
            "max_item" => 25,
            "max_base" => 20,
            "min" => 8,
            "base" => 9,
            "expression_item" => "9 + 0,842105263 * (level - 1)",
            "expression_base" => "9 + 0,578947368 * (level)",
            "bonus_item_max" => 5
        ],
        "mob" => [
            "max" => 30,
            "min" => 5,
            "base" => 8,
            "expression" => "9 + 0,842105263 * (level - 1)",
            "bonus" => 10
        ]
    ];
    const BALANCE_LIFE = [
        "classe" => [
            "max_item" => 500,
            "max_base" => 450,
            "min" => 9,
            "base" => 16,
            "expression_item" => "10 + level + level * dice / 1.6",
            "expression_base" => "10 + level * dice / 2",
            "bonus_item_max" => 50
        ],
        "mob" => [
            "max" => 600,
            "min" => 1,
            "base" => 10,
            "expression" => "10 + 12,63157895 * (level - 1)",
            "bonus" => 100
        ]
    ];
  // END
}
