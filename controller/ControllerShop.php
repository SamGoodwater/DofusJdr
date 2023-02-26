<?php
class ControllerShop extends Controller{

  public function getAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    

      $json = array();  
      if(!$currentUser->getRight('shop', User::RIGHT_READ)){
        $json = "Vous n'avez pas les droits pour lire cet objet";}else{

        $managerS = new ShopManager();
        $objs = $managerS->getAll();

        foreach ($objs AS $obj) {

          $bookmark_icon = View::STYLE_ICON_REGULAR;
          if($currentUser->in_bookmark($obj)){
              $bookmark_icon = View::STYLE_ICON_SOLID;
          }

          $edit = "";
          if($currentUser->getRight('shop', User::RIGHT_WRITE)){
            $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Shop.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='far fa-edit'></i></a>";
          }

          $json[] = array(
            'id' => $obj->getId(Content::FORMAT_BADGE),
            "uniqid" => $obj->getUniqid(),
            "timestamp_add" => $obj->getTimestamp_add(),
            "timestamp_updated" => $obj->getTimestamp_updated(),
            "name" => $obj->getName(),
            "description" => $obj->getDescription(),
            "location" => $obj->getLocation(Content::FORMAT_ICON),
            "price" => $obj->getPrice(Content::FORMAT_BADGE),
            "seller" => $obj->getId_seller(Content::FORMAT_BADGE),
            "logo" => $obj->getId_seller(Content::FORMAT_IMAGE),
            'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='shop' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
            'edit' => $edit,
            'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=shop&a=getPdf&uniqid=".$obj->getUniqid()."'><i class='fas fa-file-pdf'></i></a>",
            'detailView' => $obj->getVisual(Content::DISPLAY_CARD)
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
    if(!$currentUser->getRight('shop', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $manager = new ShopManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            $bookmark_icon = View::STYLE_ICON_REGULAR;
            if($currentUser->in_bookmark($obj)){
                $bookmark_icon = View::STYLE_ICON_SOLID;
            }

            $edit = "";
            if($currentUser->getRight('shop', User::RIGHT_WRITE)){
              $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Shop.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='far fa-edit'></i></a>";
            }

            $return["value"] = array(
              'id' => $obj->getId(Content::FORMAT_BADGE),
              "uniqid" => $obj->getUniqid(),
              "timestamp_add" => $obj->getTimestamp_add(),
              "timestamp_updated" => $obj->getTimestamp_updated(),
              "name" => $obj->getName(),
              "description" => $obj->getDescription(),
              "location" => $obj->getLocation(Content::FORMAT_ICON),
              "price" => $obj->getPrice(Content::FORMAT_BADGE),
              "seller" => $obj->getId_seller(Content::FORMAT_BADGE),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='shop' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              "logo" => $obj->getId_seller(Content::FORMAT_OBJECT)->getClasse(Content::FORMAT_OBJECT)->getPath_img_logo(Content::FORMAT_IMAGE, "img-back-30"),
              'edit' => $edit,
              'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=shop&a=getPdf&uniqid=".$obj->getUniqid()."'><i class='fas fa-file-pdf'></i></a>",
              'detailView' => $obj->getVisual(Content::DISPLAY_CARD)
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
      'state' => false,
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
              $return['state'] = true;
              $return['script'] = "Shop.open('".$object->getUniqid()."', Controller.DISPLAY_EDITABLE);";
            }else {
              $return['error'] = 'Impossible d\'ajouter l\'objet';
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