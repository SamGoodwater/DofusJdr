<?php
class ControllerConsumable extends Controller{

  public function countAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'success',
      'value' => "",
      'error' => 'erreur inconnue',
    ];
    if(!$currentUser->getRight('consumable', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new ConsumableManager();
      $return["value"] = $manager->countAll();

    }
    echo json_encode($return);
    flush();
  }
  public function getAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $bookmarks = $currentUser->getBookmark();

    if(!$currentUser->getRight('consumable', User::RIGHT_READ)){
      $json = "Vous n'avez pas les droits pour lire cet objet";}else{

        $json = array();  
        $managerS = new ConsumableManager();
        $usable = 0;

        if(isset($_REQUEST['usable'])){
          if($_REQUEST['usable'] == 1 || $_REQUEST['usable'] == 0){
            $usable = $_REQUEST['usable'];
          }
        }

        $objs = $managerS->getAll($usable);

        foreach ($objs as $obj) {

          if(isset($bookmarks["Consumable-".$obj->getUniqid()])){
            $bookmark_icon = "fas";
          } else {
            $bookmark_icon = "far";
          }

          $json[] = array(
            'id' => $obj->getId(Content::FORMAT_BADGE),
            'uniqid' => $obj->getUniqid(),
            'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
            'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
            'type' => $obj->getType(Content::FORMAT_BADGE),
            'name' => $obj->getName(),
            'description' => $obj->getDescription(),
            'effect' => $obj->getEffect(),
            'level' => $obj->getLevel(),
            'recepe' => $obj->getRecepe(),
            'price' => $obj->getPrice(Content::FORMAT_ICON),
            'rarity' => $obj->getRarity(Content::FORMAT_BADGE),
            'path_img' => $obj->getPath_img(Content::FORMAT_IMAGE, "img-back-30"),
            'usable' => $obj->getUsable(Content::FORMAT_ICON),
            'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='consumable' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
            'edit' => "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Consumable.open('{$obj->getUniqid()}')\"><i class='far fa-edit'></i></a>",
            'detailView' => $obj->getVisual(Content::FORMAT_CARD)
          );
        }

      }

    echo json_encode($json);
    flush();
  }
  public function getArrayFromUniqid(){
    $currentUser = ControllerConnect::getCurrentUser();
    $bookmarks = $currentUser->getBookmark();
    $return = [
      'return' => 'echec',
      'value' => [],
      'error' => 'erreur inconnue'
    ];
    if(!$currentUser->getRight('consumable', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $manager = new ConsumableManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            if(isset($bookmarks["Consumable-".$obj->getUniqid()])){
              $bookmark_icon = "fas";
            } else {
              $bookmark_icon = "far";
            }

            $return["value"] = array(
              'id' => $obj->getId(Content::FORMAT_BADGE),
              'uniqid' => $obj->getUniqid(),
              'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
              'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
              'type' => $obj->getType(Content::FORMAT_BADGE),
              'name' => $obj->getName(),
              'description' => $obj->getDescription(),
              'effect' => $obj->getEffect(),
              'level' => $obj->getLevel(),
              'recepe' => $obj->getRecepe(),
              'price' => $obj->getPrice(Content::FORMAT_ICON),
              'rarity' => $obj->getRarity(Content::FORMAT_BADGE),
              'path_img' => $obj->getPath_img(Content::FORMAT_IMAGE, "img-back-30"),
              'usable' => $obj->getUsable(Content::FORMAT_ICON),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='consumable' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              'edit' => "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Consumable.open('{$obj->getUniqid()}')\"><i class='far fa-edit'></i></a>",
              'detailView' => $obj->getVisual(Content::FORMAT_CARD)
            );

            $return['return'] = "success";
          }else {
            $return['error'] = 'Impossible de récupérer les données';
          }
      }

    }

    echo json_encode($return);
    flush();
  }
  public function getFromUniqid(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'echec',
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('consumable', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $managerS = new ConsumableManager();

        // Récupération de l'objet
          if($managerS->existsUniqid($_REQUEST['uniqid'])){

            $obj = $managerS->getFromUniqid($_REQUEST['uniqid']);
            $return['value'] = array(
              'visual' => $obj->getVisual(Content::FORMAT_MODIFY),
              "title" => $obj->getName(Content::FORMAT_MODIFY)
            );
            $return['return'] = "success";
          }else {
            $return['error'] = 'Impossible de récupérer les données';
          }
      }
    
    }

    echo json_encode($return);
    flush();
  }
  public function add(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'echec',
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('consumable', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour modifier cet objet";} else {

      if(!isset($_REQUEST['name'], $_REQUEST['type'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
        $manager = new ConsumableManager();

        $type = Consumable::TYPE_POTION;
        if(in_array($_REQUEST['type'], Consumable::TYPE_LIST)){
          $type = $_REQUEST["type"];
        }

        if($manager->existsName($_REQUEST['name']) == false){
          $object = new Consumable([
            'name' => trim($_REQUEST['name']),
            'level' => 1,
            "type" => $type,
            'uniqid' => uniqid()
          ]);
          $object->setTimestamp_add();
          $object->setTimestamp_updated();
            
            if($manager->add($object)){
              $return['return'] = "success";
              $return['script'] = "Consumable.open('".$object->getUniqid()."')";
            }else {
              $return['error'] = 'Impossible d\'ajouter l\'objet';
            }

          } else {
            $return['error'] = "Ce nom est déjà utilisé";
          }
      }
    
    }

    echo json_encode($return);
    flush();
  }
  public function update(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'echec',
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('consumable', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour modifier cet objet";} else {

      if(!isset($_REQUEST['uniqid'], $_REQUEST['type'], $_REQUEST['value'])){
        $return['error'] = 'Impossible de récupérer les données';

      } else {

            $manager = new ConsumableManager(); // A modifier

            if($manager->existsUniqid($_REQUEST['uniqid'])){

              $obj = $manager->getFromUniqid($_REQUEST['uniqid']);
                $method = "set".ucfirst($_REQUEST['type']);

                if(method_exists($obj,$method)){
                    $result = $obj->$method($_REQUEST['value']);
                    if($result == "success"){
                      $obj->setTimestamp_updated(time());
                      $manager->update($obj);
                      $return['return'] = "success";
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
  public function remove(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'echec',
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('consumable', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour modifier cet objet";} else {

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          // Récupération des objets
            $manager = new ConsumableManager();

          // Récupération de l'objet
            if($manager->existsUniqid($_REQUEST['uniqid'])){

              $obj = $manager->getFromUniqid($_REQUEST['uniqid']);
              $manager->delete($obj);
              $return['return'] = "success";

            } else {
              $return['error'] = 'Cet objet n\'existe pas.';
            }
      }
    
    }

    echo json_encode($return);
    flush();
  }

  public function search($term, $action = ControllerSearch::SEARCH_DONE_REDIRECT, $parameter = "", $limit = null, $only_usable = false){
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight('consumable', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {

        $array = [];
        $manager = new ConsumableManager;
        $consumables = $manager->search($term, $limit, $only_usable);

        if(!empty($consumables )){
            $array = array();
            foreach ($consumables  as $object) {
                $click_action = "";
                switch ($action) {
                  case ControllerSearch::SEARCH_DONE_ADD_CONSUMABLE_TO_SHOP:
                    $click_action = "onclick=\"Shop.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'consumable', IS_VALUE);\"";
                  break;
                  default:
                    $click_action = "onclick=\"Consumable.open('".$object->getUniqid()."')\"";
                  break;
                }

                $name = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getName());
                ob_start();   ?>
                  <a <?=$click_action?> class="d-flex justify-content-between align-items-baseline flex-nowrap">
                    <div class="d-flex justify-content-start align-item-baseline">
                      <div class="img-back-20 me-2" style="background-image:url(<?=$object->getPath_img()?>)"></div>
                      <?=$name?>
                    </div>
                    <p><small class='size-0-6 badge back-teal-l-1 mx-2'>Consommable</small></p>
                  </a>
                <?php $visual = ob_get_clean();
                $array[] = [
                  'error' => false,
                  'visual' =>$visual,
                  'label' => $object->getName()
                ];
            }
        }
    }    
    return $array;
  }

}