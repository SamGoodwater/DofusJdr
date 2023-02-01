<?php
class ControllerClasse extends Controller{

  public function getAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $bookmarks = $currentUser->getBookmark();
    $json = array();  

    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $json = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new ClasseManager();
      $objs = $manager->getAll();

      foreach ($objs as $obj) {

        if(isset($bookmarks["Classe-".$obj->getUniqid()])){
          $bookmark_icon = "fas";
        } else {
          $bookmark_icon = "far";
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
          'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title)='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=classe&a=getPdf&uniqid=".$obj->getUniqid()."'><i class='fas fa-file-pdf'></i></a>",
          'edit' => "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Classe.open('{$obj->getUniqid()}')\"><i class='far fa-edit'></i></a>",
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
    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $manager = new ClasseManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            if(isset($bookmarks["Classe-".$obj->getUniqid()])){
              $bookmark_icon = "fas";
            } else {
              $bookmark_icon = "far";
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
              'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title)='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=classe&a=getPdf&uniqid=".$obj->getUniqid()."'><i class='fas fa-file-pdf'></i></a>",
              'edit' => "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Classe.open('{$obj->getUniqid()}')\"><i class='far fa-edit'></i></a>",
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
    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $managerS = new ClasseManager();

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
    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(isset($_REQUEST['uniqid'])){
        $manager = new ClasseManager();
        // Récupération de l'objet
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
      'return' => 'echec',
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('classe', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour modifier cet objet";} else {

      // Récupération des objets
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

              $return['return'] = "success";

            } else {
              $return['error'] = 'Cette classe existe déjà.';
            }
        } else {
          $return['return'] = "Pas de classe envoyé";
        }
      } else {
        $return['error'] = "Pas de classe envoyé";
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
    if(!$currentUser->getRight('classe', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour modifier cet objet";} else {

      if(!isset($_REQUEST['uniqid'], $_REQUEST['type'], $_REQUEST['value'])){
        $return['error'] = 'Impossible de récupérer les données';

      } else {

            $manager = new ClasseManager(); // A modifier

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

    if(!$currentUser->getRight('classe', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour modifier cet objet";} else {
      
      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          // Récupération des objets
            $manager = new ClasseManager();

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
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          // Récupération des objets
            $manager = new ClasseManager();

          // Récupération de l'objet
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
                "content" => $obj->getSpell(Content::FORMAT_RESUME),
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