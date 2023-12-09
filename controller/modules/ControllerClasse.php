<?php

class ControllerClasse extends ControllerModule{
  public function count(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue'
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    
    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $return["error"] = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new ClasseManager();

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

    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $json = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new ClasseManager();
      
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
      
      $objs = $manager->getAll(
        usable:$usable,
        offset:$offset,
        limit:$limit
      );

      foreach ($objs as $obj) {

        $bookmark_icon = Style::ICON_REGULAR;
        if($currentUser->in_bookmark($obj)){
            $bookmark_icon = Style::ICON_SOLID;
        }

        $edit = "";
        if($currentUser->getRight('classe', User::RIGHT_WRITE)){
          $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Classe.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
        }

        $json[] = array(
          'id' => $obj->getId(Content::FORMAT_BADGE),
          'uniqid' => $obj->getUniqid(),
          'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
          'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
          'name' => "<b>".ucfirst($obj->getName())."</b>",
          'description_fast' => $obj->getDescription_fast(),
          'description' => $obj->getDescription(),
          'life' => $obj->getLife(),
          'life_dice' => $obj->getLife_dice(Content::FORMAT_ICON),
          'specificity' => $obj->getSpecificity(),
          'weapons_of_choice' => "<div class='d-flex justify-content-center'>".$obj->getWeapons_of_choice(Content::FORMAT_ICON)."</div>",
          'trait' => $obj->getTrait(Content::FORMAT_BADGE),
          'path_img_logo' => $obj->getFile("logo", new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
          'path_img' => $obj->getFile("img", new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
          'usable' => $obj->getUsable(Content::FORMAT_ICON),
          'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='classe' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
          'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=classe&a=getPdf&uniqid=".$obj->getUniqid()."'><i class='fa-solid fa-file-pdf'></i></a>",
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

            $bookmark_icon = Style::ICON_REGULAR;
            if($currentUser->in_bookmark($obj)){
                $bookmark_icon = Style::ICON_SOLID;
            }

            $edit = "";
            if($currentUser->getRight('classe', User::RIGHT_WRITE)){
              $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Classe.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
            }

            $return["value"] = array(
              'id' => $obj->getId(Content::FORMAT_BADGE),
              'uniqid' => $obj->getUniqid(),
              'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
              'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
              'name' => "<b>".ucfirst($obj->getName())."</b>",
              'description_fast' => $obj->getDescription_fast(),
              'description' => $obj->getDescription(),
              'life' => $obj->getLife(),
              'life_dice' => $obj->getLife_dice(Content::FORMAT_ICON),
              'specificity' => $obj->getSpecificity(),
              'weapons_of_choice' => "<div class='d-flex justify-content-center'>".$obj->getWeapons_of_choice(Content::FORMAT_ICON)."</div>",
              'trait' => $obj->getTrait(Content::FORMAT_BADGE),
              'path_img_logo' => $obj->getFile("logo", new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
              'path_img' => $obj->getFile("img", new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
              'usable' => $obj->getUsable(Content::FORMAT_ICON),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='classe' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=classe&a=getPdf&uniqid=".$obj->getUniqid()."'><i class='fa-solid fa-file-pdf'></i></a>",
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
  public function getPdf(){
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight('classe', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(isset($_REQUEST['uniqid'])){
        $manager = new ClasseManager();
        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);
            $name = $obj->getName();

            // instantiate and use the dompdf class
            define('DOMPDF_MEMORY_LIMIT', '512M');
            define('DOMPDF_MAX_EXECUTION_TIME', 180); // 180 secondes (3 minutes)

            $options = new Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isFontSubsettingEnabled', true);
            $dompdf = new Dompdf\Dompdf($options);
            $dompdf->setBasePath($_SERVER["DOCUMENT_ROOT"]);

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
            $dompdf->stream($name.".pdf", array("Attachment" => false));
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

      // Récupération des objets
      $mS = new ClasseManager();

      if(in_array($_REQUEST['weapons_of_choice'], Classe::WEAPONS)){
        if($_REQUEST['name'] != ""){

            if($mS->existsName($_REQUEST['name']) == false && !empty(trim($_REQUEST['name']))){
                $classe = new Classe([
                  'name' => trim($_REQUEST['name']),
                  'weapons_of_choice' => $_REQUEST['weapons_of_choice'],
                  'life_dice' => 10,
                  'uniqid' => uniqid()
                ]);
                $classe->setTimestamp_add();
                $classe->setTimestamp_updated();
                $mS->add($classe);
                $return['script'] = "Classe.open('".$classe->getUniqid()."', Controller.DISPLAY_EDITABLE);";
                $return['state'] = true;

            } else {
              $return['error'] = 'Cette classe existe déjà.';
            }
        } else {
          $return['error'] = "Pas de classe envoyé";
        }
      } else {
        $return['error'] = "Pas de classe envoyé";
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
  public function search($term, $action = ControllerModule::SEARCH_DONE_REDIRECT, $parameter = "", $limit = null, $only_usable = false){
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
              case ControllerModule::SEARCH_DONE_ADD_TO_BOOKMARK:
                $click_action = "onclick=\"User.changeBookmark(this);\" data-classe=\"".strtolower(get_class($object))."\" data-uniqid=\"".$object->getUniqid()."\"";
              break;
              default:
                $click_action = "onclick=\"Classe.open('".$object->getUniqid()."')\"";
              break;
            }
            $name = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getName());
            ob_start();   ?>
              <a <?=$click_action?> class="d-flex justify-content-between align-items-baseline flex-nowrap">
                <div class="d-flex justify-content-start align-item-baseline">
                  <div class="img-back-20 me-2" style="background-image:url(<?=$object->getFile('logo',)?>)"></div>
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