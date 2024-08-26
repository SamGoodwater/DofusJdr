<?php
class ControllerSpell extends ControllerModule{
  public function count(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue'
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    
    if(!$currentUser->getRight('spell', User::RIGHT_READ)){
      $return["error"] = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new SpellManager();

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

      $category=[];
      if(isset($_REQUEST['category'])){
        if(empty($_REQUEST['category'])){$category = [];
        } else{
          foreach (array_filter(explode("|", $_REQUEST['category'])) as $value) {
            if(in_array($value, Spell::CATEGORY)){$category[] = $value;}
          }
        }
      } else {$category = [];}

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
        category:$category,
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
        if(empty($_REQUEST['element'])){$element = [];
        } else{
          foreach (array_filter(explode("|", $_REQUEST['element'])) as $value) {
            if(isset(Spell::ELEMENT[$value])){$element[] = $value;}
          }
        }
      } else {$element = [];}

      $category=[];
      if(isset($_REQUEST['category'])){
        if(empty($_REQUEST['category'])){$category = [];
        } else{
          foreach (array_filter(explode("|", $_REQUEST['category'])) as $value) {
            if(in_array($value, Spell::CATEGORY)){$category[] = $value;}
          }
        }
      } else {$category = [];}

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
        category:$category, 
        level:$level, 
        usable:$usable,
        offset:$offset,
        limit:$limit
      );

      foreach ($objs as $obj) {
          ob_start();?>
              <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap">
                  <?=$obj->getPowerful(Content::FORMAT_BADGE)?>
                  <?=$obj->getIs_magic(Content::FORMAT_BADGE)?>
                  <?=$obj->getElement(Content::FORMAT_BADGE)?>
                  <?=$obj->getCategory(Content::FORMAT_BADGE)?>
                  <?=$obj->getType(Content::FORMAT_BADGE)?>
              </div>
          <?php $resume1 = ob_get_clean();

          ob_start();?>
            <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                <?=$obj->getPa(Content::FORMAT_ICON)?>
                <?=$obj->getPo(Content::FORMAT_ICON)?>
                <?=$obj->getPo_editable(Content::FORMAT_ICON)?>
                <?=$obj->getSight_line(Content::FORMAT_ICON)?>
                <?=$obj->getNumber_between_two_cast(Content::FORMAT_ICON)?>
                <?=$obj->getCast_per_turn(Content::FORMAT_ICON)?>
                <?=$obj->getCast_per_target(Content::FORMAT_ICON)?>
            </div>
          <?php $resume2 = ob_get_clean();
          
          $bookmark_icon = Style::ICON_REGULAR;
          if($currentUser->in_bookmark($obj)){
              $bookmark_icon = Style::ICON_SOLID;
          }

          $edit = "";
          if($currentUser->getRight('spell', User::RIGHT_WRITE)){
            $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Spell.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
          }

          $json_other = null; $json_base = null; $json_text = null; $json_icon = null; $json_badge = null;
          $json_other = array(
            'edit' => $edit,
            'resume1' => $resume1,
            'resume2' => $resume2,
            'logo' => $obj->getFile('logo',new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])), 
            'bookmark' => "<a onclick='User.toggleBookmark(this);' data-classe='spell' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
            'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=spell&a=getPdf&uniqids=".$obj->getUniqid()."'><i class='fa-solid fa-file-pdf'></i></a>",
            'color' => $obj->getElement(Content::FORMAT_COLOR_VERBALE)
          );

          $json_base = array(
            'id' => $obj->getId(),
            'uniqid' => $obj->getUniqid(),
            'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
            'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
            'name' => $obj->getName(),
            'description' => $obj->getDescription(),
            'effect' => $obj->getEffect(),
            'level' => $obj->getLevel(),
            'po' => $obj->getPo(),
            'po_editable' => $obj->getPo_editable(),
            'pa' => $obj->getPa(),
            'cast_per_turn' => $obj->getCast_per_turn(),
            'cast_per_target' => $obj->getCast_per_target(),
            'sight_line' => $obj->getSight_line(),
            'number_between_two_cast' => $obj->getNumber_between_two_cast(),
            'element' => $obj->getElement(),
            'category' => $obj->getCategory(),
            'invocation' => $obj->getInvocation(),
            'is_magic' => $obj->getIs_magic(),
            'powerful' => $obj->getPowerful(),
            'type' => $obj->getType(),
            'logo' => $obj->getFile('logo', new Style(['format' => Content::FORMAT_BRUT])),
            'bookmark' => $bookmark_icon == Style::ICON_SOLID ? true : false,
            'usable' => $obj->getUsable(),
          );

          $json_text = array(
            'name' => "<b>". $obj->getName()."</b>",
            'invocation' => $obj->getInvocation(Content::DISPLAY_LIST),
            'powerful' => $obj->getPowerful(Content::FORMAT_TEXT),
            'type' => $obj->getType(Content::FORMAT_TEXT),
          );

          $json_icon = array(
            'level' => $obj->getLevel(Content::FORMAT_ICON),
            'po' => $obj->getPo(Content::FORMAT_ICON),
            'po_editable' => $obj->getPo_editable(Content::FORMAT_ICON),
            'pa' => $obj->getPa(Content::FORMAT_ICON),
            'cast_per_turn' => $obj->getCast_per_turn(Content::FORMAT_ICON),
            'cast_per_target' => $obj->getCast_per_target(Content::FORMAT_ICON),
            'sight_line' => $obj->getSight_line(Content::FORMAT_ICON),
            'number_between_two_cast' => $obj->getNumber_between_two_cast(Content::FORMAT_ICON),
            'is_magic' => $obj->getIs_magic(Content::FORMAT_ICON),
            'powerful' => $obj->getPowerful(Content::FORMAT_ICON),
            'usable' => $obj->getUsable(Content::FORMAT_ICON),
          );

          $json_badge = array(
            'name' => $obj->getName(Content::FORMAT_BADGE),
            'level' => $obj->getLevel(Content::FORMAT_BADGE),
            'po' => $obj->getPo(Content::FORMAT_BADGE),
            'po_editable' => $obj->getPo_editable(Content::FORMAT_BADGE),
            'pa' => $obj->getPa(Content::FORMAT_BADGE),
            'cast_per_turn' => $obj->getCast_per_turn(Content::FORMAT_BADGE),
            'cast_per_target' => $obj->getCast_per_target(Content::FORMAT_BADGE),
            'sight_line' => $obj->getSight_line(Content::FORMAT_BADGE),
            'number_between_two_cast' => $obj->getNumber_between_two_cast(Content::FORMAT_BADGE),
            'category' => $obj->getCategory(Content::FORMAT_BADGE),
            'is_magic' => $obj->getIs_magic(Content::FORMAT_BADGE),
            'element' => $obj->getElement(Content::FORMAT_BADGE),
            'powerful' => $obj->getPowerful(Content::FORMAT_BADGE),
            'type' => $obj->getType(Content::FORMAT_BADGE),
            'usable' => $obj->getUsable(Content::FORMAT_BADGE),
          );

          $json[] = array(
            'base' => $json_base,
            'other' => $json_other,
            'text' => $json_text,
            'icon' => $json_icon,
            'badge' => $json_badge
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
    if(!$currentUser->getRight('spell', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $managerS = new SpellManager();

        // Récupération de l'objet
          if($managerS->existsUniqid($_REQUEST['uniqid'])){
            $obj = $managerS->getFromUniqid($_REQUEST['uniqid']);
            ob_start();?>
            <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap">
                <?=$obj->getPowerful(Content::FORMAT_BADGE)?>
                <?=$obj->getIs_magic(Content::FORMAT_BADGE)?>
                <?=$obj->getElement(Content::FORMAT_BADGE)?>
                <?=$obj->getCategory(Content::FORMAT_BADGE)?>
                <?=$obj->getType(Content::FORMAT_BADGE)?>
            </div>
        <?php $resume1 = ob_get_clean();

        ob_start();?>
          <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
              <?=$obj->getPa(Content::FORMAT_ICON)?>
              <?=$obj->getPo(Content::FORMAT_ICON)?>
              <?=$obj->getPo_editable(Content::FORMAT_ICON)?>
              <?=$obj->getSight_line(Content::FORMAT_ICON)?>
              <?=$obj->getNumber_between_two_cast(Content::FORMAT_ICON)?>
              <?=$obj->getCast_per_turn(Content::FORMAT_ICON)?>
              <?=$obj->getCast_per_target(Content::FORMAT_ICON)?>
          </div>
        <?php $resume2 = ob_get_clean();
        
        $bookmark_icon = Style::ICON_REGULAR;
        if($currentUser->in_bookmark($obj)){
            $bookmark_icon = Style::ICON_SOLID;
        }

        $edit = "";
        if($currentUser->getRight('spell', User::RIGHT_WRITE)){
          $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Spell.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
        }

            $json_other = null; $json_base = null; $json_text = null; $json_icon = null; $json_badge = null;
            $json_other = array(
              'edit' => $edit,
              'resume1' => $resume1,
              'resume2' => $resume2,
              'logo' => $obj->getFile('logo',new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])), 
              'bookmark' => "<a onclick='User.toggleBookmark(this);' data-classe='spell' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=spell&a=getPdf&uniqids=".$obj->getUniqid()."'><i class='fa-solid fa-file-pdf'></i></a>",
              'color' => $obj->getElement(Content::FORMAT_COLOR_VERBALE)
            );

            $json_base = array(
              'id' => $obj->getId(),
              'uniqid' => $obj->getUniqid(),
              'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
              'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
              'name' => $obj->getName(),
              'description' => $obj->getDescription(),
              'effect' => $obj->getEffect(),
              'level' => $obj->getLevel(),
              'po' => $obj->getPo(),
              'po_editable' => $obj->getPo_editable(),
              'pa' => $obj->getPa(),
              'cast_per_turn' => $obj->getCast_per_turn(),
              'cast_per_target' => $obj->getCast_per_target(),
              'sight_line' => $obj->getSight_line(),
              'number_between_two_cast' => $obj->getNumber_between_two_cast(),
              'element' => $obj->getElement(),
              'category' => $obj->getCategory(),
              'invocation' => $obj->getInvocation(),
              'is_magic' => $obj->getIs_magic(),
              'powerful' => $obj->getPowerful(),
              'type' => $obj->getType(),
              'logo' => $obj->getFile('logo', new Style(['format' => Content::FORMAT_BRUT])),
              'bookmark' => $bookmark_icon == Style::ICON_SOLID ? true : false,
              'usable' => $obj->getUsable(),
            );

            $json_text = array(
              'name' => "<b>". $obj->getName()."</b>",
              'invocation' => $obj->getInvocation(Content::DISPLAY_LIST),
              'powerful' => $obj->getPowerful(Content::FORMAT_TEXT),
              'type' => $obj->getType(Content::FORMAT_TEXT),
            );

            $json_icon = array(
              'level' => $obj->getLevel(Content::FORMAT_ICON),
              'po' => $obj->getPo(Content::FORMAT_ICON),
              'po_editable' => $obj->getPo_editable(Content::FORMAT_ICON),
              'pa' => $obj->getPa(Content::FORMAT_ICON),
              'cast_per_turn' => $obj->getCast_per_turn(Content::FORMAT_ICON),
              'cast_per_target' => $obj->getCast_per_target(Content::FORMAT_ICON),
              'sight_line' => $obj->getSight_line(Content::FORMAT_ICON),
              'number_between_two_cast' => $obj->getNumber_between_two_cast(Content::FORMAT_ICON),
              'is_magic' => $obj->getIs_magic(Content::FORMAT_ICON),
              'powerful' => $obj->getPowerful(Content::FORMAT_ICON),
              'usable' => $obj->getUsable(Content::FORMAT_ICON),
            );

            $json_badge = array(
              'name' => $obj->getName(Content::FORMAT_BADGE),
              'level' => $obj->getLevel(Content::FORMAT_BADGE),
              'po' => $obj->getPo(Content::FORMAT_BADGE),
              'po_editable' => $obj->getPo_editable(Content::FORMAT_BADGE),
              'pa' => $obj->getPa(Content::FORMAT_BADGE),
              'cast_per_turn' => $obj->getCast_per_turn(Content::FORMAT_BADGE),
              'cast_per_target' => $obj->getCast_per_target(Content::FORMAT_BADGE),
              'sight_line' => $obj->getSight_line(Content::FORMAT_BADGE),
              'number_between_two_cast' => $obj->getNumber_between_two_cast(Content::FORMAT_BADGE),
              'category' => $obj->getCategory(Content::FORMAT_BADGE),
              'is_magic' => $obj->getIs_magic(Content::FORMAT_BADGE),
              'powerful' => $obj->getPowerful(Content::FORMAT_BADGE),
              'element' => $obj->getElement(Content::FORMAT_BADGE),
              'type' => $obj->getType(Content::FORMAT_BADGE),
              'usable' => $obj->getUsable(Content::FORMAT_BADGE),
            );

            $return['value'] = array(
              'base' => $json_base,
              'other' => $json_other,
              'text' => $json_text,
              'icon' => $json_icon,
              'badge' => $json_badge
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
      'state' => false,
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

        if($manager->existsName($_REQUEST['name']) == false && !empty(trim($_REQUEST['name']))){
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
              $return['state'] = true;
              $return['script'] = "Spell.open('".$object->getUniqid()."', Controller.DISPLAY_EDITABLE)";
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
                  case ControllerModule::SEARCH_DONE_ADD_TO_BOOKMARK:
                    $click_action = "onclick=\"User.toggleBookmark(this);\" data-classe=\"".strtolower(get_class($object))."\" data-uniqid=\"".$object->getUniqid()."\"";
                  break;
                  case ControllerModule::SEARCH_DONE_ADD_SPELL_TO_MOB:
                    $click_action = "onclick=\"Mob.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'spell', IS_VALUE);\"";
                  break;
                  case ControllerModule::SEARCH_DONE_ADD_SPELL_TO_CLASSE:
                    $click_action = "onclick=\"Classe.updateSpell('".$parameter."', '".$object->getUniqid()."')\"";
                  break;
                  case ControllerModule::SEARCH_DONE_ADD_SPELL_TO_NPC:
                    $click_action = "onclick=\"Npc.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'spell', IS_VALUE);\"";
                  break;
                  case ControllerModule::SEARCH_DONE_GET_SPELL:
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
                      <div class="img-back-20 me-2" style="background-image:url(<?=$object->getFile('logo',new Style(['format' => Content::FORMAT_BRUT]))?>)"></div>
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