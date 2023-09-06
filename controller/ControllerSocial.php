<?php
class ControllerSocial extends ControllerModule{
  public function count(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue'
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    
    if(!$currentUser->getRight('social', User::RIGHT_READ)){
      $return["error"] = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new SocialManager();

      $usable = 0;
      if(isset($_REQUEST['usable'])){
        if($_REQUEST['usable'] == 1 || $_REQUEST['usable'] == 0){
          $usable = $_REQUEST['usable'];
        }
      }
      $return['value'] = $manager->countAll(
        usable:$usable
      );
      $return['state'] = true;
    }
    echo json_encode($return);
    flush();
  }
  public function getAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    

      $json = array();  
      if(!$currentUser->getRight('social', User::RIGHT_READ)){
        $json = "Vous n'avez pas les droits pour lire cet objet";}else{

        $managerS = new SocialManager();

        $usable = 0;
        if(isset($_REQUEST['usable'])){
          if($_REQUEST['usable'] == 1 || $_REQUEST['usable'] == 0){
            $usable = $_REQUEST['usable'];
          }
        }

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
          usable:$usable,
          offset:$offset,
          limit:$limit
        );

        foreach ($objs AS $obj) {

          $bookmark_icon = Style::ICON_REGULAR;
          if($currentUser->in_bookmark($obj)){
              $bookmark_icon = Style::ICON_SOLID;
          }

          $edit = "";
          if($currentUser->getRight('social', User::RIGHT_WRITE)){
            $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Social.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
          }

          $json[] = array(
            'id' => $obj->getId(Content::FORMAT_BADGE),
            "uniqid" => $obj->getUniqid(),
            "timestamp_add" => $obj->getTimestamp_add(),
            "timestamp_updated" => $obj->getTimestamp_updated(),
            "name" => $obj->getName(),
            "text" => $obj->getText(),
            "link" => $obj->getLink(),
            "description" => $obj->getDescription(),
            "visible" => $obj->getVisible(Content::FORMAT_ICON),
            "usable" => $obj->getUsable(Content::FORMAT_ICON),
            "logo" => $obj->getFile('logo',new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_MD])),
            'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='social' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
            'edit' => $edit
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
    if(!$currentUser->getRight('social', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $manager = new SocialManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            $bookmark_icon = Style::ICON_REGULAR;
            if($currentUser->in_bookmark($obj)){
                $bookmark_icon = Style::ICON_SOLID;
            }

            $edit = "";
            if($currentUser->getRight('social', User::RIGHT_WRITE)){
              $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Social.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
            }

            $return["value"] = array(
              'id' => $obj->getId(Content::FORMAT_BADGE),
              "uniqid" => $obj->getUniqid(),
              "timestamp_add" => $obj->getTimestamp_add(),
              "timestamp_updated" => $obj->getTimestamp_updated(),
              "name" => $obj->getName(),
              "text" => $obj->getText(),
              "link" => $obj->getLink(),
              "description" => $obj->getDescription(),
              "visible" => $obj->getVisible(Content::FORMAT_ICON),
              "usable" => $obj->getUsable(Content::FORMAT_ICON),
              "logo" => $obj->getFile('logo',new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_MD])),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='social' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              'edit' => $edit
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
    if(!$currentUser->getRight('social', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['name'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

            $manager = new SocialManager();
            $object = new Social(array(
              'name' => $_REQUEST['name'],
              'text' => $_REQUEST['text'],
              'link' => $_REQUEST['link']
            ));
            $object->setUniqid(uniqid());
            $object->setTimestamp_add();
            $object->setTimestamp_updated();
            
            if($manager->add($object)){
              $return['state'] = true;
              $return['script'] = "Social.open('".$object->getUniqid()."', Controller.DISPLAY_EDITABLE);";
            }else {
              $return['error'] = 'Impossible d\'ajouter l\'objet';
            }
      }

    }

    echo json_encode($return);
    flush();
  }
  public function search($term, $action = ControllerModule::SEARCH_DONE_REDIRECT, $parameter = "", $limit = null, $only_usable = false){
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight('social', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {

        $array = [];
        $manager = new SocialManager;
        $objects = $manager->search($term, $limit,$only_usable);

        if(!empty($objects)){
            $array = array();
            foreach ($objects as $object) {
                $click_action = "";
                switch ($action) {
                  case ControllerModule::SEARCH_DONE_ADD_TO_BOOKMARK:
                    $click_action = "onclick=\"User.changeBookmark(this);\" data-classe=\"".strtolower(get_class($object))."\" data-uniqid=\"".$object->getUniqid()."\"";
                  break;
                  default:
                    $click_action = "onclick=\"Social.open('".$object->getUniqid()."');\"";
                  break;
                }

                preg_match_all("/(.{0,50}".$term.".{0,50})/i", strip_tags($object->getDescription()), $content, PREG_SET_ORDER);
                if(isset($content[0][0])){$content = $content[0][0];}else{$content = "";}
                $content = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $content);
                $name = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getName());
                ob_start(); ?>
                  <a <?=$click_action?> class="item-search">
                    <div class="d-flex justify-content-between align-items-baseline flex-nowrap">
                      <p><?=$name?> - <?=$object->getText()?></p>
                      <p><small class='size-0-6 badge back-brown-l-1 mx-2'>Réseau social</small></p>
                    </div>
                    <p class="size-0-8 text-grey"><?=$content?></p>
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