<?php
class ControllerCapability extends ControllerModule{

  public function count(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue'
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    
    if(!$currentUser->getRight('capability', User::RIGHT_READ)){
      $return["error"] = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new CapabilityManager();

      $usable = 0;
      if(isset($_REQUEST['usable'])){
        if($_REQUEST['usable'] == 1 || $_REQUEST['usable'] == 0){
          $usable = $_REQUEST['usable'];
        }
      }

      $element=[];
      if(isset($_REQUEST['element'])){
        if(empty($_REQUEST['element'])){$element = [];
        } else{
          foreach (array_filter(explode("|", $_REQUEST['element'])) as $value) {
            if(isset(Spell::ELEMENT[$value])){$element[] = $value;}
          }
        }
      } else {$element = [];}

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
        element:$element,
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
    if(!$currentUser->getRight('capability', User::RIGHT_READ)){
      $json = "Vous n'avez pas les droits pour lire cet objet";}else{
      
      $managerS = new CapabilityManager();
      $usable = 0;
      if(isset($_REQUEST['usable'])){
        if($_REQUEST['usable'] == 1 || $_REQUEST['usable'] == 0){
          $usable = $_REQUEST['usable'];
        }
      }

      $element=[];
      if(isset($_REQUEST['element'])){
        if(empty($_REQUEST['element'])){$element = [];
        } else{
          foreach (array_filter(explode("|", $_REQUEST['element'])) as $value) {
            if(isset(Spell::ELEMENT[$value])){$element[] = $value;}
          }
        }
      } else {$element = [];}

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
        element:$element, 
        level:$level, 
        usable:$usable,
        offset:$offset,
        limit:$limit
      );

      foreach ($objs as $obj) {
        ob_start();?>
          <div class="text-left">
            <?=$obj->getPowerful(Content::FORMAT_BADGE)?>
            <?=$obj->getIs_magic(Content::FORMAT_BADGE)?>
            <?=$obj->getRitual_available(Content::FORMAT_BADGE, true)?>
            <?=$obj->getElement(Content::FORMAT_BADGE)?>
          </div>
        <?php $resume1 = ob_get_clean();

        ob_start();?>
          <div class="text-left">
            <?=$obj->getTime_before_use_again(Content::FORMAT_BADGE)?>
            <?=$obj->getCasting_time(Content::FORMAT_BADGE)?>
            <?=$obj->getDuration(Content::FORMAT_BADGE)?>
            <?=$obj->getPa(Content::FORMAT_ICON, true)?>
            <?=$obj->getPo(Content::FORMAT_BADGE)?>
            <?=$obj->getPo_editable(Content::FORMAT_BADGE)?>
          </div>
        <?php $resume2 = ob_get_clean();
        
        $bookmark_icon = Style::ICON_REGULAR;
        if($currentUser->in_bookmark($obj)){
            $bookmark_icon = Style::ICON_SOLID;
        }

        $edit = "";
        if($currentUser->getRight('capability', User::RIGHT_WRITE)){
          $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Capability.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
        }

        $json[] = array(
          'id' => $obj->getId(Content::FORMAT_BADGE),
          'uniqid' => $obj->getUniqid(),
          'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
          'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
          'name' => $obj->getName(),
          'description' => $obj->getDescription(),
          'effect' => $obj->getEffect(),
          'level' => $obj->getLevel(Content::FORMAT_ICON),
          'pa' => $obj->getPa(Content::FORMAT_ICON),
          'po' => $obj->getPo(Content::FORMAT_ICON),
          'po_editable' => $obj->getPo_editable(Content::FORMAT_ICON),
          'time_before_use_again' => $obj->getTime_before_use_again(Content::FORMAT_ICON),
          'casting_time' => $obj->getCasting_time(Content::FORMAT_ICON),
          'duration' => $obj->getDuration(Content::FORMAT_ICON),
          'element' => $obj->getElement(Content::FORMAT_BADGE),
          'specialization' => $obj->getSpecialization(Content::FORMAT_BADGE),
          'ritual_available' => $obj->getRitual_available(Content::FORMAT_BADGE),
          'is_magic' => $obj->getIs_magic(Content::FORMAT_ICON),
          'powerful' => $obj->getPowerful(Content::FORMAT_BADGE),
          'path_img' => $obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
          'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='capability' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
          'usable' => $obj->getUsable(Content::FORMAT_ICON),
          'resume1' => $resume1,
          'resume2' => $resume2,
          'edit' => $edit,
          'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=capability&a=getPdf&uniqids=".$obj->getUniqid()."'><i class='fa-solid fa-file-pdf'></i></a>",
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
    if(!$currentUser->getRight('capability', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $managerS = new CapabilityManager();

        // Récupération de l'objet
          if($managerS->existsUniqid($_REQUEST['uniqid'])){
            $obj = $managerS->getFromUniqid($_REQUEST['uniqid']);
            ob_start();?>
              <div class="text-left">
                <?=$obj->getPowerful(Content::FORMAT_BADGE)?>
                <?=$obj->getIs_magic(Content::FORMAT_BADGE)?>
                <?=$obj->getRitual_available(Content::FORMAT_BADGE, true)?>
                <?=$obj->getElement(Content::FORMAT_BADGE)?>
              </div>
            <?php $resume1 = ob_get_clean();

            ob_start();?>
              <div class="text-left">
                <?=$obj->getTime_before_use_again(Content::FORMAT_BADGE)?>
                <?=$obj->getCasting_time(Content::FORMAT_BADGE)?>
                <?=$obj->getDuration(Content::FORMAT_BADGE)?>
                <?=$obj->getPa(Content::FORMAT_ICON, true)?>
                <?=$obj->getPo(Content::FORMAT_BADGE)?>
                <?=$obj->getPo_editable(Content::FORMAT_BADGE)?>
              </div>
            <?php $resume2 = ob_get_clean();

            $bookmark_icon = Style::ICON_REGULAR;
            if($currentUser->in_bookmark($obj)){
                $bookmark_icon = Style::ICON_SOLID;
            }

            $edit = "";
            if($currentUser->getRight('capability', User::RIGHT_WRITE)){
              $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Capability.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
            }

            $return["value"] = array(
              'id' => $obj->getId(Content::FORMAT_BADGE),
              'uniqid' => $obj->getUniqid(),
              'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
              'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
              'name' => $obj->getName(),
              'description' => $obj->getDescription(),
              'effect' => $obj->getEffect(),
              'level' => $obj->getLevel(Content::FORMAT_ICON),
              'pa' => $obj->getPa(Content::FORMAT_ICON),
              'po' => $obj->getPo(Content::FORMAT_ICON),
              'po_editable' => $obj->getPo_editable(Content::FORMAT_ICON),
              'time_before_use_again' => $obj->getTime_before_use_again(Content::FORMAT_ICON),
              'casting_time' => $obj->getCasting_time(Content::FORMAT_ICON),
              'duration' => $obj->getDuration(Content::FORMAT_ICON),
              'element' => $obj->getElement(Content::FORMAT_BADGE),
              'specialization' => $obj->getSpecialization(Content::FORMAT_BADGE),
              'ritual_available' => $obj->getRitual_available(Content::FORMAT_BADGE),
              'is_magic' => $obj->getIs_magic(Content::FORMAT_ICON),
              'powerful' => $obj->getPowerful(Content::FORMAT_BADGE),
              'path_img' => $obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='capability' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              'usable' => $obj->getUsable(Content::FORMAT_ICON),
              'resume1' => $resume1,
              'resume2' => $resume2,
              'edit' => $edit,
              'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=capability&a=getPdf&uniqids=".$obj->getUniqid()."'><i class='fa-solid fa-file-pdf'></i></a>",
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
    if(!$currentUser->getRight('capability', User::RIGHT_READ)){
      echo "Vous n'avez pas les droits pour générer un pdf";
      return "Vous n'avez pas les droits pour générer un pdf";}else{

      if(!isset($_REQUEST['uniqids'])){
        echo "Aucun uniqid n'a été envoyé";
        return "Aucun uniqid n'a été envoyé";}else{

        $manager = new CapabilityManager();
        // Récupération de l'objet

        $uniqids = explode("|", $_REQUEST['uniqids']);
        $uniqids = array_filter($uniqids);
        if(count($uniqids) <= 0){
          echo "Aucune aptitude n'a été sélectionné";
          return "Aucune aptitude n'a été sélectionné";

        }else{
          $capabilities = [];
          foreach($uniqids as $uniqid) {
            if($manager->existsUniqid($uniqid)){
              $capabilities[] = $manager->getFromUniqid($uniqid);    
            }
          }
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
            require "view/pdf/capability.php";
            $html .= $content . "</body></html>";
            $dompdf->loadHtml($html, 'UTF-8');
  
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
  
            // Render the HTML as PDF
            $dompdf->render();
  
            // Output the generated PDF to Browser
            $dompdf->stream("Aptitudes.pdf", array("Attachment" => false));
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
    if(!$currentUser->getRight('capability', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['name'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
        $manager = new CapabilityManager();

        if($manager->existsName($_REQUEST['name']) == false && !empty(trim($_REQUEST['name']))){
          $object = new Capability([
            'name' => trim($_REQUEST['name']),
            'level' => 1,
            'po' => "",
            'time_before_use_again' => "",
            'casting_time' => "",
            'duration' => "",
            "element" => Spell::ELEMENT_NEUTRE,
            'uniqid' => uniqid(),
            'powerful' => 3
          ]);
          $object->setTimestamp_add();
          $object->setTimestamp_updated();
            
            if($manager->add($object)){
              $return['state'] = true;
              $return['script'] = "Capability.open('".$object->getUniqid()."', Controller.DISPLAY_EDITABLE)";
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
    if(!$currentUser->getRight('capability', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {

        $array = [];
        $manager = new CapabilityManager;
        $objects = $manager->search($term, $limit,$only_usable);

        if(!empty($objects)){
            $array = array();
            foreach ($objects as $object) {
                $click_action = "";
                switch ($action) {
                  case ControllerModule::SEARCH_DONE_ADD_TO_BOOKMARK:
                    $click_action = "onclick=\"User.changeBookmark(this);\" data-classe=\"".strtolower(get_class($object))."\" data-uniqid=\"".$object->getUniqid()."\"";
                  break;
                  case ControllerModule::SEARCH_DONE_ADD_CAPABILITY_TO_MOB:
                    $click_action = "onclick=\"Mob.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'capability', IS_VALUE);\"";
                  break;
                  case ControllerModule::SEARCH_DONE_ADD_CAPABILITY_TO_CLASSE:
                    $click_action = "onclick=\"Classe.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'capability', IS_VALUE);\"";
                  break;
                  case ControllerModule::SEARCH_DONE_ADD_CAPABILITY_TO_NPC:
                    $click_action = "onclick=\"Npc.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'capability', IS_VALUE);\"";
                  break;
                  case ControllerModule::SEARCH_DONE_GET_CAPABILITY:
                    $click_action = "onclick=\"Capability.addToOptionSection('".$parameter."','".$object->getUniqid()."', '".$object->getName()."', '".$object->getElement(Content::FORMAT_COLOR_VERBALE)."');\"";
                  break;
                  default:
                    $click_action = "onclick=\"Capability.open('".$object->getUniqid()."')\"";
                  break;
                }

                $name = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getName());
                ob_start();   ?>
                  <a <?=$click_action?> class="d-flex justify-content-between align-items-baseline flex-nowrap">
                    <div class="d-flex justify-content-start align-item-baseline">
                      <div class="img-back-20 me-2" style="background-image:url(<?=$object->getFile('logo',new Style(['format' => Content::FORMAT_BRUT]))?>)"></div>
                      <?=$name?>
                    </div>
                    <p><small class='size-0-6 badge back-brown-l-1 mx-2'>Aptitudes</small></p>
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