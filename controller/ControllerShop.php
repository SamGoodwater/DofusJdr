<?php
class ControllerShop extends Controller{

  public function countAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'success',
      'value' => "",
      'error' => 'erreur inconnue',
    ];
    if(!$currentUser->getRight('shop', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{
      $manager = new ShopManager();
      $return["value"] = $manager->countAll();
    }
    echo json_encode($return);
    flush();
  }
  public function getAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $bookmarks = $currentUser->getBookmark();

      $json = array();  
      if(!$currentUser->getRight('shop', User::RIGHT_READ)){
        $json = "Vous n'avez pas les droits pour lire cet objet";}else{

        $managerS = new ShopManager();
        $objects = $managerS->getAll();

        foreach ($objects AS $object) {

          if(isset($bookmarks["Shop-".$object->getUniqid()])){
            $bookmark_icon = "fas";
          } else {
            $bookmark_icon = "far";
          }

          $json[] = array(
            'id' => $object->getId(Content::FORMAT_BADGE),
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "timestamp_updated" => $object->getTimestamp_updated(),
            "name" => $object->getName(),
            "description" => $object->getDescription(),
            "location" => $object->getLocation(Content::FORMAT_ICON),
            "price" => $object->getPrice(Content::FORMAT_BADGE),
            "seller" => $object->getId_seller(Content::FORMAT_BADGE),
            "logo" => $object->getId_seller(Content::FORMAT_IMAGE),
            'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='shop' data-uniqid='".$object->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
            'edit' => "<a class='text-main-d-2 text-main-l-3-hover' onclick=\"Shop.open('{$object->getUniqid()}')\"><i class='far fa-edit'></i></a>",
            'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title)='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=shop&a=getPdf&uniqid=".$object->getUniqid()."'><i class='fas fa-file-pdf'></i></a>",
            'detailView' => $object->getVisual(Content::FORMAT_CARD)
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
    if(!$currentUser->getRight('shop', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $manager = new ShopManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $object = $manager->getFromUniqid($_REQUEST['uniqid']);

            if(isset($bookmarks["Shop-".$object->getUniqid()])){
              $bookmark_icon = "fas";
            } else {
              $bookmark_icon = "far";
            }

            $return["value"] = array(
              'id' => $object->getId(Content::FORMAT_BADGE),
              "uniqid" => $object->getUniqid(),
              "timestamp_add" => $object->getTimestamp_add(),
              "timestamp_updated" => $object->getTimestamp_updated(),
              "name" => $object->getName(),
              "description" => $object->getDescription(),
              "location" => $object->getLocation(Content::FORMAT_ICON),
              "price" => $object->getPrice(Content::FORMAT_BADGE),
              "seller" => $object->getId_seller(Content::FORMAT_BADGE),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='shop' data-uniqid='".$object->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              "logo" => $object->getId_seller(Content::FORMAT_OBJECT)->getClasse(Content::FORMAT_OBJECT)->getPath_img_logo(Content::FORMAT_IMAGE, "img-back-30"),
              'edit' => "<a class='text-main-d-2 text-main-l-3-hover' onclick=\"Shop.open('{$object->getUniqid()}')\"><i class='far fa-edit'></i></a>",
              'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title)='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=shop&a=getPdf&uniqid=".$object->getUniqid()."'><i class='fas fa-file-pdf'></i></a>",
              'detailView' => $object->getVisual(Content::FORMAT_CARD)
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
    if(!$currentUser->getRight('shop', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $managerS = new ShopManager();

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

  public function getPdf(){
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight('shop', User::RIGHT_READ)){
      return "Vous n'avez pas les droits pour générer un pdf";}else{

      if(isset($_REQUEST['uniqid'])){
        $manager = new ShopManager();
        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $shop = $manager->getFromUniqid($_REQUEST['uniqid']);
            // instantiate and use the dompdf class
            $options = new Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf\Dompdf($options);
            $dompdf->getOptions()->setChroot($_SERVER["DOCUMENT_ROOT"]);
            $html = "";
            require "view/pdf/header.php";
            $html .= $content;
            require "view/pdf/shop.php";
            $html .= $content . "</body></html>";
            $dompdf->loadHtml($html, 'UTF-8');

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream($obj->getName().".pdf", array("Attachment" => false));
            return true;
          }
      }

    }
  }
  
  public function add(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'echec',
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('shop', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['name'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

            $manager = new ShopManager();
            $object = new Shop(array(
              'name' => $_REQUEST['name']
            ));
            $object->setUniqid(uniqid());
            $object->setTimestamp_add();
            $object->setTimestamp_updated();
            
            if($manager->add($object)){
              $return['return'] = "success";
              $return['script'] = "Shop.open('".$object->getUniqid()."')";
            }else {
              $return['error'] = 'Impossible d\'ajouter l\'objet';
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
    if(!$currentUser->getRight('shop', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['uniqid'], $_REQUEST['type'], $_REQUEST['value'])){
        $return['error'] = 'Impossible de récupérer les données';

      } else {

            $manager = new ShopManager(); // A modifier

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
    if(!$currentUser->getRight('shop', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          // Récupération des objets
            $manager = new ShopManager();

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
    if(!$currentUser->getRight('shop', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {

        $array = [];
        $manager = new ShopManager;
        $objects = $manager->search($term, $limit,$only_usable);

        if(!empty($objects)){
            $array = array();
            foreach ($objects as $object) {
                $click_action = "";
                switch ($action) {
                  default:
                    $click_action = "onclick=\"Shop.open('".$object->getUniqid()."');\"";
                  break;
                }

                preg_match_all("/(.{0,50}".$term.".{0,50})/i", strip_tags($object->getDescription()), $content, PREG_SET_ORDER);
                if(isset($content[0][0])){$content = $content[0][0];}else{$content = "";}
                $content = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $content);
                $name = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getName());
                ob_start(); ?>
                  <a <?=$click_action?> class="item-search">
                    <div class="d-flex justify-content-between align-items-baseline flex-nowrap">
                      <p><?=$name?></p>
                      <p><small class='size-0-6 badge back-grey-l-1 mx-2'>Hôtel de vente</small></p>
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
 