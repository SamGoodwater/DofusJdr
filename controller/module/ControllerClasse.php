<?php
class ControllerClasse extends Controller{

  public function getAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $json = array();  

    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $json = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new ClasseManager();
      $objs = $manager->getAll();

      foreach ($objs as $obj) {

        $bookmark_icon = Style::ICON_REGULAR;
        if($currentUser->in_bookmark($obj)){
            $bookmark_icon = Style::ICON_SOLID;
        }

        $edit = "";
        if($currentUser->getRight('classe', User::RIGHT_WRITE)){
          $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Classe.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='far fa-edit'></i></a>";
        }

        $json[] = array(
          'id' => $obj->getId(Content::FORMAT_BADGE),
          'uniqid' => $obj->getUniqid(),
          'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
          'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
          'name' => $obj->getName(),
          'description_fast' => $obj->getDescription_fast(),
          'description' => $obj->getDescription(),
          'life' => $obj->getLife(),
          'specificity' => $obj->getSpecificity(),
          'weapons_of_choice' => "<div class='d-flex justify-content-center'>".$obj->getWeapons_of_choice(Content::FORMAT_ICON)."</div>",
          'spell' => $obj->getSpell(),
          'trait' => $obj->getTrait(Content::FORMAT_BADGE),
          'path_img_logo' => $obj->getPath_img_logo(Content::FORMAT_IMAGE, "img-back-30"),
          'path_img' => $obj->getPath_img(Content::FORMAT_IMAGE, "img-back-30"),
          'usable' => $obj->getUsable(Content::FORMAT_ICON),
          'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='classe' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
          'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title)='G??n??rer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=classe&a=getPdf&uniqid=".$obj->getUniqid()."'><i class='fas fa-file-pdf'></i></a>",
          'edit' => $edit,
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
    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de r??cup??rer les donn??es';
      } else {

        $manager = new ClasseManager();

        // R??cup??ration de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            $bookmark_icon = Style::ICON_REGULAR;
            if($currentUser->in_bookmark($obj)){
                $bookmark_icon = Style::ICON_SOLID;
            }

            $edit = "";
            if($currentUser->getRight('classe', User::RIGHT_WRITE)){
              $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Classe.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='far fa-edit'></i></a>";
            }

            $return["value"] = array(
              'id' => $obj->getId(Content::FORMAT_BADGE),
              'uniqid' => $obj->getUniqid(),
              'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
              'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
              'name' => $obj->getName(),
              'description_fast' => $obj->getDescription_fast(),
              'description' => $obj->getDescription(),
              'life' => $obj->getLife(),
              'specificity' => $obj->getSpecificity(),
              'weapons_of_choice' => "<div class='d-flex justify-content-center'>".$obj->getWeapons_of_choice(Content::FORMAT_ICON)."</div>",
              'spell' => $obj->getSpell(),
              'trait' => $obj->getTrait(Content::FORMAT_BADGE),
              'path_img_logo' => $obj->getPath_img_logo(Content::FORMAT_IMAGE, "img-back-30"),
              'path_img' => $obj->getPath_img(Content::FORMAT_IMAGE, "img-back-30"),
              'usable' => $obj->getUsable(Content::FORMAT_ICON),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='classe' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title)='G??n??rer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=classe&a=getPdf&uniqid=".$obj->getUniqid()."'><i class='fas fa-file-pdf'></i></a>",
              'edit' => $edit,
              'detailView' => $obj->getVisual(Content::DISPLAY_CARD)
            );

            $return['state'] = true;
          }else {
            $return['error'] = 'Impossible de r??cup??rer les donn??es';
          }
      }

    }

    echo json_encode($return);
    flush();
  }
  public function getPdf(){
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(isset($_REQUEST['uniqid'])){
        $manager = new ClasseManager();
        // R??cup??ration de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            // instantiate and use the dompdf class
            $dompdf = new Dompdf\Dompdf();
            $dompdf->getOptions()->setChroot($_SERVER["DOCUMENT_ROOT"]);
            // $dompdf->getOptions()->setIsFontSubsettingEnabled(true);
            // $dompdf->getOptions()->setDpi(80);
            $html = "";
            require "view/pdf/header.php";
            $html .= $content;
            require "view/pdf/classe.php";
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
    if(!$currentUser->getRight('classe', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour modifier cet objet";} else {

      // R??cup??ration des objets
      $mS = new ClasseManager();

      if(in_array($_REQUEST['weapons_of_choice'], Classe::WEAPONS)){
        if($_REQUEST['name'] != ""){

            if($mS->existsName($_REQUEST['name']) == false){
                $classe = new Classe([
                  'name' => trim($_REQUEST['name']),
                  'weapons_of_choice' => $_REQUEST['weapons_of_choice'],
                  'uniqid' => uniqid()
                ]);
                $classe->setTimestamp_add();
                $classe->setTimestamp_updated();
                $mS->add($classe);
                $return['script'] = "Classe.open('".$classe->getUniqid()."', Controller.DISPLAY_EDITABLE);";
                $return['state'] = true;

            } else {
              $return['error'] = 'Cette classe existe d??j??.';
            }
        } else {
          $return['error'] = "Pas de classe envoy??";
        }
      } else {
        $return['error'] = "Pas de classe envoy??";
      }

    }

      echo json_encode($return);
      flush();
  }
  public function getSpellList(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];

    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour modifier cet objet";} else {
      
      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de r??cup??rer les donn??es';
      } else {

          // R??cup??ration des objets
            $manager = new ClasseManager();

          // R??cup??ration de l'objet
            if($manager->existsUniqid($_REQUEST['uniqid'])){

              $obj = $manager->getFromUniqid($_REQUEST['uniqid']);
              $return['state'] = true;
              
              $text = "Sort ";
              if(View::isVowel($obj->getName())){
                $text .= "de l'";
              } else {
                $text .= "du ";
              }
                
              $return['value'] = [
                "content" => $obj->getSpell(Content::DISPLAY_RESUME),
                "title" => $text .$obj->getName()
              ];

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
    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {
      $array = [];
      $manager = new ClasseManager;
      $objects = $manager->search($term, $limit,$only_usable);

      if(!empty($objects)){
        $array = array();
        foreach ($objects as $object) {
            $click_action = "";
            switch ($action) {
              default:
                $click_action = "onclick=\"Classe.open('".$object->getUniqid()."')\"";
              break;
            }
            $name = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getName());
            ob_start();   ?>
              <a <?=$click_action?> class="d-flex justify-content-between align-items-baseline flex-nowrap">
                <div class="d-flex justify-content-start align-item-baseline">
                  <div class="img-back-20 me-2" style="background-image:url(<?=$object->getPath_img()?>)"></div>
                  <?=$name?>
                </div>
                <p><small class='size-0-6 badge back-green-l-1 mx-2'>Classe</small></p>
              </a>
            <?php $visual = ob_get_clean();

            $array[] = [
              'error' => false,
              'visual' =>$visual,
              'label' => $object->getName()
            ];
        }
      }
      return $array;
    }
  }
 
}