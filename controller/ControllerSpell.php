<?php
class ControllerSpell extends Controller{

  public function countAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'success',
      'value' => "",
      'error' => 'erreur inconnue',
    ];
    if(!$currentUser->getRight('spell', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{
      $manager = new SpellManager();
      $return["value"] = $manager->countAll();
    }
    echo json_encode($return);
    flush();
  }
  public function getAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $bookmarks = $currentUser->getBookmark();

    $json = array();  
    if(!$currentUser->getRight('spell', User::RIGHT_READ)){
      $json = "Vous n'avez pas les droits pour lire cet objet";}else{
      
      $managerS = new SpellManager();
      $usable = 0;

      if(isset($_REQUEST['usable'])){
        if($_REQUEST['usable'] == 1 || $_REQUEST['usable'] == 0){
          $usable = $_REQUEST['usable'];
        }
      }

      $element=[];
      if(isset($_REQUEST['element'])){
        if(empty($_REQUEST['element'])){$element = ["all"];
        } else{
          foreach (array_filter(explode(",", $_REQUEST['element'])) as $value) {
            if(in_array($value, Spell::ELEMENT)){$element[] = $value;}
          }
        }
      } else {$element = ["all"];}

      $category=[];
      if(isset($_REQUEST['category'])){
        if(empty($_REQUEST['category'])){$category = ["all"];
        } else{
          foreach (array_filter(explode(",", $_REQUEST['category'])) as $value) {
            if(in_array($value, Spell::CATEGORY)){$category[] = $value;}
          }
        }
      } else {$category = ["all"];}

      $level=[];
      if(isset($_REQUEST['level'])){
        if(empty($_REQUEST['level'])){$level = ["all"];
        } else{
          foreach (array_filter(explode(",", $_REQUEST['level'])) as $value) {
            if($value > 0 && $value <= 20){$level[] = $value;}
          }
        }
      } else {$level = ["all"];}

      $spells = $managerS->getAll($element, $category, $level, $usable);

      foreach ($spells as $spell) {
        ob_start();?>
          <div class="text-left">
              <?=$spell->getPowerful(Content::FORMAT_BADGE)?>
              <?=$spell->getIs_magic(Content::FORMAT_BADGE)?>
              <?=$spell->getElement(Content::FORMAT_BADGE)?>
              <?=$spell->getCategory(Content::FORMAT_BADGE)?>
              <?=$spell->getType(Content::FORMAT_BADGE)?>
          </div>
        <?php $resume = ob_get_clean();
        
        if(isset($bookmarks["Spell-".$spell->getUniqid()])){
          $bookmark_icon = "fas";
        } else {
          $bookmark_icon = "far";
        }

        $json[] = array(
          'id' => $spell->getId(Content::FORMAT_BADGE),
          'uniqid' => $spell->getUniqid(),
          'timestamp_add' => $spell->getTimestamp_add(Content::DATE_FR),
          'timestamp_updated' => $spell->getTimestamp_updated(Content::DATE_FR),
          'name' => $spell->getName(),
          'description' => $spell->getDescription(),
          'effect' => $spell->getEffect(),
          'level' => $spell->getLevel(Content::FORMAT_ICON),
          'po' => $spell->getPo(Content::FORMAT_ICON),
          'po_editable' => $spell->getPo_editable(Content::FORMAT_ICON),
          'pa' => $spell->getPa(Content::FORMAT_ICON),
          'cast_per_turn' => $spell->getCast_per_turn(Content::FORMAT_ICON),
          'sight_line' => $spell->getSight_line(Content::FORMAT_ICON),
          'number_between_two_cast' => $spell->getNumber_between_two_cast(Content::FORMAT_ICON),
          'element' => $spell->getElement(Content::FORMAT_BADGE),
          'category' => $spell->getCategory(Content::FORMAT_BADGE),
          'type' => $spell->getType(Content::FORMAT_BADGE),
          'id_invocation' => $spell->getId_invocation(Content::FORMAT_RESUME),
          'is_magic' => $spell->getIs_magic(Content::FORMAT_ICON),
          'powerful' => $spell->getPowerful(Content::FORMAT_BADGE),
          'path_img' => $spell->getPath_img(Content::FORMAT_IMAGE, "img-back-30"),
          'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='spell' data-uniqid='".$spell->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
          'usable' => $spell->getUsable(Content::FORMAT_ICON),
          'resume' => $resume,
          'edit' => "<a id='{$spell->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Spell.open('{$spell->getUniqid()}')\"><i class='far fa-edit'></i></a>",
          'detailView' => $spell->getVisual(Content::FORMAT_CARD)
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
    if(!$currentUser->getRight('spell', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $managerS = new SpellManager();

        // Récupération de l'objet
          if($managerS->existsUniqid($_REQUEST['uniqid'])){
            $spell = $managerS->getFromUniqid($_REQUEST['uniqid']);
            ob_start();?>
              <div class="text-left">
                  <?=$spell->getPowerful(Content::FORMAT_BADGE)?>
                  <?=$spell->getIs_magic(Content::FORMAT_BADGE)?>
                  <?=$spell->getElement(Content::FORMAT_BADGE)?>
                  <?=$spell->getCategory(Content::FORMAT_BADGE)?>
                  <?=$spell->getType(Content::FORMAT_BADGE)?>
              </div>
            <?php $resume = ob_get_clean();

            if(isset($bookmarks["Spell-".$spell->getUniqid()])){
              $bookmark_icon = "fas";
            } else {
              $bookmark_icon = "far";
            }

            $return["value"] = array(
              'id' => $spell->getId(),
              'uniqid' => $spell->getUniqid(),
              'timestamp_add' => $spell->getTimestamp_add(Content::DATE_FR),
              'timestamp_updated' => $spell->getTimestamp_updated(Content::DATE_FR),
              'name' => $spell->getName(),
              'description' => $spell->getDescription(),
              'effect' => $spell->getEffect(),
              'level' => $spell->getLevel(Content::FORMAT_ICON),
              'po' => $spell->getPo(Content::FORMAT_ICON),
              'po_editable' => $spell->getPo_editable(Content::FORMAT_ICON),
              'pa' => $spell->getPa(Content::FORMAT_ICON),
              'cast_per_turn' => $spell->getCast_per_turn(Content::FORMAT_ICON),
              'sight_line' => $spell->getSight_line(Content::FORMAT_ICON),
              'number_between_two_cast' => $spell->getNumber_between_two_cast(Content::FORMAT_ICON),
              'element' => $spell->getElement(Content::FORMAT_BADGE),
              'category' => $spell->getCategory(Content::FORMAT_BADGE),
              'id_invocation' => $spell->getId_invocation(Content::FORMAT_RESUME),
              'is_magic' => $spell->getIs_magic(Content::FORMAT_ICON),
              'powerful' => $spell->getPowerful(Content::FORMAT_BADGE),
              'type' => $spell->getType(Content::FORMAT_BADGE),
              'path_img' => $spell->getPath_img(Content::FORMAT_IMAGE, "img-back-30"),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='spell' data-uniqid='".$spell->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              'usable' => $spell->getUsable(Content::FORMAT_ICON),
              'resume' => $resume,
              'edit' => "<a class='text-main-d-2 text-main-l-3-hover' onclick=\"Spell.open('{$spell->getUniqid()}')\"><i class='far fa-edit'></i></a>",
              'detailView' => $spell->getVisual(Content::FORMAT_CARD)
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
    if(!$currentUser->getRight('spell', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $managerS = new SpellManager();

        // Récupération de l'objet
          if($managerS->existsUniqid($_REQUEST['uniqid'])){

            $spell = $managerS->getFromUniqid($_REQUEST['uniqid']);
            $return['value'] = array(
              'visual' => $spell->getVisual(Content::FORMAT_MODIFY),
              "title" => $spell->getName(Content::FORMAT_MODIFY)
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
  public function getResume(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => 'echec',
      'return' => "",
      'error' => 'erreur inconnue'
    ];
    if(!$currentUser->getRight('spell', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $managerS = new SpellManager();

        // Récupération de l'objet
          if($managerS->existsUniqid($_REQUEST['uniqid'])){

            $spell = $managerS->getFromUniqid($_REQUEST['uniqid']);
            $format = ""; if(isset($_REQUEST['format'])){$format = $_REQUEST['format'];}else{$format = Content::FORMAT_CARD;}
            $return["return"] = $spell->getVisual($format);
            $return['state'] = "success";
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
    if(!$currentUser->getRight('spell', User::RIGHT_READ)){
      echo "Vous n'avez pas les droits pour générer un pdf";
      return "Vous n'avez pas les droits pour générer un pdf";}else{

      if(!isset($_REQUEST['uniqids'])){
        echo "Aucun uniqid n'a été envoyé";
        return "Aucun uniqid n'a été envoyé";}else{

        $manager = new SpellManager();
        // Récupération de l'objet

        $uniqids = explode("|", $_REQUEST['uniqids']);
        $uniqids = array_filter($uniqids);
        if(count($uniqids) <= 0){
          echo "Aucun sort n'a été sélectionné";
          return "Aucun sort n'a été sélectionné";

        }else{
          $spells = [];
          foreach($uniqids as $uniqid) {
            if($manager->existsUniqid($uniqid)){
              $spells[] = $manager->getFromUniqid($uniqid);
            }
          }

            // instantiate and use the dompdf class
            $options = new Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf\Dompdf($options);
            $dompdf->getOptions()->setChroot($_SERVER["DOCUMENT_ROOT"]);
            $html = "";
            require "view/pdf/header.php";
            $html .= $content;
            require "view/pdf/spell.php";
            $html .= $content . "</body></html>";
            $dompdf->loadHtml($html, 'UTF-8');
  
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
  
            // Render the HTML as PDF
            $dompdf->render();
  
            // Output the generated PDF to Browser
            $dompdf->stream("Sorts.pdf", array("Attachment" => false));
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
    if(!$currentUser->getRight('spell', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['name'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
        $manager = new SpellManager();

        if($manager->existsName($_REQUEST['name']) == false){
          $object = new Spell([
            'name' => trim($_REQUEST['name']),
            'level' => 1,
            'po' => 1,
            'pa' => 3,
            'cast_per_turn' => 1,
            'sight_line' => true,
            'number_between_two_cast' => 0,
            "element" => Spell::ELEMENT_NEUTRE,
            'uniqid' => uniqid(),
            'powerful' => 1
          ]);
          $object->setTimestamp_add();
          $object->setTimestamp_updated();
            
            if($manager->add($object)){
              $return['return'] = "success";
              $return['script'] = "Spell.open('".$object->getUniqid()."')";
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
    if(!$currentUser->getRight('spell', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['uniqid'], $_REQUEST['type'], $_REQUEST['value'])){
        $return['error'] = 'Impossible de récupérer les données';

      } else {

            $manager = new SpellManager(); // A modifier

            if($manager->existsUniqid($_REQUEST['uniqid'])){

              $obj = $manager->getFromUniqid($_REQUEST['uniqid']);
                $method = "set".ucfirst($_REQUEST['type']);

                if(method_exists($obj,$method)){
                    $result = $obj->$method($_REQUEST['value']);
                    if($result == "success"){
                      $obj->setTimestamp_updated(time());
                      $manager->update($obj);
                      switch ($_REQUEST['type']) {
                        case 'type':
                          $return['script'] = "";  
                        break;
                      }
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
    if(!$currentUser->getRight('spell', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          // Récupération des objets
            $managerS = new SpellManager();

          // Récupération de l'objet
            if($managerS->existsUniqid($_REQUEST['uniqid'])){

              $spell = $managerS->getFromUniqid($_REQUEST['uniqid']);
              $managerS->delete($spell);
              $return['return'] = "success";

            } else {
              $return['error'] = 'Ce sort n\'existe pas.';
            }
      }

    }

    echo json_encode($return);
    flush();
  }

  public const SEARCH_DONE_REDIRECT = 0;

  public function search($term, $action = ControllerSearch::SEARCH_DONE_REDIRECT, $parameter = "", $limit = null, $only_usable = false){
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight('spell', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {

        $array = [];
        $manager = new SpellManager;
        $objects = $manager->search($term, $limit,$only_usable);

        if(!empty($objects)){
            $array = array();
            foreach ($objects as $object) {
                $click_action = "";
                switch ($action) {
                  case ControllerSearch::SEARCH_DONE_ADD_SPELL_TO_MOB:
                    $click_action = "onclick=\"Mob.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'spell', IS_VALUE);\"";
                  break;
                  case ControllerSearch::SEARCH_DONE_ADD_SPELL_TO_CLASSE:
                    $click_action = "onclick=\"Classe.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'spell', IS_VALUE);\"";
                  break;
                  case ControllerSearch::SEARCH_DONE_GET_SPELL:
                    $click_action = "onclick=\"Spell.showResume('".$object->getUniqid()."','#".$parameter."', ".Content::FORMAT_BADGE.", false);\"";
                  break;
                  default:
                    $click_action = "onclick=\"Spell.open('".$object->getUniqid()."')\"";
                  break;
                }

                $name = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getName());
                ob_start();   ?>
                  <a <?=$click_action?> class="d-flex justify-content-between align-items-baseline flex-nowrap">
                    <div class="d-flex justify-content-start align-item-baseline">
                      <div class="img-back-20 me-2" style="background-image:url(<?=$object->getPath_img()?>)"></div>
                      <?=$name?>
                    </div>
                    <p><small class='size-0-6 badge back-deep-purple-l-1 mx-2'>Sort</small></p>
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