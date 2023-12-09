<?php
class ControllerItem extends ControllerModule{

  public function count(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue'
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    
    if(!$currentUser->getRight('item', User::RIGHT_READ)){
      $return["error"] = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new ItemManager();
      $usable = 0;
      if(isset($_REQUEST['usable'])){
        if($_REQUEST['usable'] == 1 || $_REQUEST['usable'] == 0){
          $usable = $_REQUEST['usable'];
        }
      }

      $type=[];
      if(isset($_REQUEST['type'])){
        if(empty($_REQUEST['type'])){$type = [];
        } else{
          foreach (array_filter(explode("|", $_REQUEST['type'])) as $value) {
            if(in_array($value, Item::TYPES)){$type[] = $value;}
          }
        }
      } else {$type = [];}

      $level=[];
      if(isset($_REQUEST['level'])){
        if(empty($_REQUEST['level'])){$level = [];
        } else{
          foreach (array_filter(explode("|", $_REQUEST['level'])) as $value) {
            if($value > 0 && $value <= 20){$level[] = $value;}
          }
        }
      } else {$level = [];}

      $return['value'] = $manager->countAll(
        usable:$usable, 
        type:$type, 
        level:$level
      );
      $return['state'] = true;
    }
    echo json_encode($return);
    flush();
  }
  public function getAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    

    $json = array();  
    if(!$currentUser->getRight('item', User::RIGHT_READ)){
      $json = "Vous n'avez pas les droits pour lire cet objet";}else{

      $managerS = new ItemManager();
      $usable = 0;

      if(isset($_REQUEST['usable'])){
        if($_REQUEST['usable'] == 1 || $_REQUEST['usable'] == 0){
          $usable = $_REQUEST['usable'];
        }
      }

      $type=[];
      if(isset($_REQUEST['type'])){
        if(empty($_REQUEST['type'])){$type = [];
        } else{
          foreach (array_filter(explode("|", $_REQUEST['type'])) as $value) {
            if(in_array($value, Item::TYPES)){$type[] = $value;}
          }
        }
      } else {$type = [];}

      $level=[];
      if(isset($_REQUEST['level'])){
        if(empty($_REQUEST['level'])){$level = [];
        } else{
          foreach (array_filter(explode("|", $_REQUEST['level'])) as $value) {
            if($value > 0 && $value <= 20){$level[] = $value;}
          }
        }
      } else {$level = [];}

      $offset = -1;
      if(isset($_REQUEST['offset'])){
        if(is_numeric($_REQUEST['offset'])){
          $offset = $_REQUEST['offset'];
        }
      }
      $limit = -1;
      if(isset($_REQUEST['limit'])){
        if(is_numeric($_REQUEST['limit'])){
          $limit = $_REQUEST['limit'];
        }
      }

      $objs = $managerS->getAll(
        type:$type,
        level:$level, 
        usable:$usable,
        offset:$offset,
        limit:$limit
      );  

      foreach ($objs as $key => $obj) {

        $bookmark_icon = Style::ICON_REGULAR;
        if($currentUser->in_bookmark($obj)){
            $bookmark_icon = Style::ICON_SOLID;
        }

        $edit = "";
        if($currentUser->getRight('item', User::RIGHT_WRITE)){
          $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Item.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
        }

        $json[] = array(
          'id' => $obj->getId(Content::FORMAT_BADGE),
          'uniqid' => $obj->getUniqid(),
          'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
          'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
          'name' => "<b>". $obj->getName()."</b>",
          'level' => $obj->getLevel(),
          'description' => $obj->getDescription(),
          'effect' => $obj->getEffect(),
          'type' => $obj->gettype(),
          'recepe' => $obj->getRecepe(),
          'price' => $obj->getPrice(Content::FORMAT_ICON),
          'rarity' => $obj->getRarity(Content::FORMAT_BADGE),
          'path_img' => $obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
          'usable' => $obj->getUsable(Content::FORMAT_ICON),
          'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='item' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
          'edit' => $edit,
          'detailView' => $obj->getVisual(new Style(["display" => Content::DISPLAY_CARD]))
        );
      }

    }

    echo json_encode($json);
    flush();
  }
  public function getArrayFromUniqid(){
    $currentUser = ControllerConnect::getCurrentUser();
    

    $return = [
      'state' => false,
      'value' => [],
      'error' => 'erreur inconnue'
    ];
    if(!$currentUser->getRight('item', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $manager = new ItemManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            $bookmark_icon = Style::ICON_REGULAR;
            if($currentUser->in_bookmark($obj)){
                $bookmark_icon = Style::ICON_SOLID;
            }

            $edit = "";
            if($currentUser->getRight('item', User::RIGHT_WRITE)){
              $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Item.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
            }

            $return["value"] = array(
              'id' => $obj->getId(Content::FORMAT_BADGE),
              'uniqid' => $obj->getUniqid(),
              'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
              'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
              'name' => "<b>". $obj->getName()."</b>",
              'level' => $obj->getLevel(),
              'description' => $obj->getDescription(),
              'effect' => $obj->getEffect(),
              'type' => $obj->gettype(),
              'recepe' => $obj->getRecepe(),
              'price' => $obj->getPrice(Content::FORMAT_ICON),
              'rarity' => $obj->getRarity(Content::FORMAT_BADGE),
              'path_img' => $obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
              'usable' => $obj->getUsable(Content::FORMAT_ICON),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='item' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              'edit' => $edit,
              'detailView' => $obj->getVisual(new Style(["display" => Content::DISPLAY_CARD]))
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

  public function add(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('item', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['name'], $_REQUEST['type'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
        $manager = new ItemManager();

        if($manager->existsName($_REQUEST['name']) == false && !empty(trim($_REQUEST['name']))){

          $type = Item::TYPE_CHAPEAU ;
          if(in_array($_REQUEST['type'], Item::TYPES)){
            $type = $_REQUEST['type'];
          }

          $object = new Item([
            'name' => trim($_REQUEST['name']),
            'level' => 1,
            "type" => $type,
            'uniqid' => uniqid()
          ]);
          $object->setTimestamp_add();
          $object->setTimestamp_updated();
            
            if($manager->add($object)){
              $return['state'] = true;
              $return['script'] = "Item.open('".$object->getUniqid()."', Controller.DISPLAY_EDITABLE)";
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

  public function search($term, $action = ControllerModule::SEARCH_DONE_REDIRECT, $parameter = "", $limit = null, $only_usable = false){
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight('item', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {

        $array = [];
        $manager = new ItemManager;
        $items = $manager->search($term, $limit,$only_usable);

        if(!empty($items )){
            $array = array();
            foreach ($items  as $object) {
                $click_action = "";
                switch ($action) {
                  case ControllerModule::SEARCH_DONE_ADD_TO_BOOKMARK:
                    $click_action = "onclick=\"User.changeBookmark(this);\" data-classe=\"".strtolower(get_class($object))."\" data-uniqid=\"".$object->getUniqid()."\"";
                  break;
                  case ControllerModule::SEARCH_DONE_ADD_ITEM_TO_SHOP:
                    $click_action = "onclick=\"Shop.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'item', IS_VALUE);\"";
                  break;
                  case ControllerModule::SEARCH_DONE_ADD_ITEM_TO_NPC:
                    $click_action = "onclick=\"Npc.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'item', IS_VALUE);\"";
                  break;
                  default:
                    $click_action = "onclick=\"Item.open('".$object->getUniqid()."', Controller.DISPLAY_CARD)\"";
                  break;
                }

                $name = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getName());
                ob_start();   ?>
                  <a <?=$click_action?> class="d-flex justify-content-between align-items-baseline flex-nowrap">
                    <div class="d-flex justify-content-start align-item-baseline">
                      <div class="img-back-20 me-2" style="background-image:url(<?=$object->getFile('logo', new Style(['format' => Content::FORMAT_BRUT]))?>)"></div>
                      <?=$name?>
                    </div>
                    <p><small class='size-0-6 badge back-orange-l-1 mx-2'>Equipement</small></p>
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