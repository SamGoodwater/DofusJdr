<?php
class ControllerNpc extends ControllerModule{
  public function count(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue'
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    
    if(!$currentUser->getRight('npc', User::RIGHT_READ)){
      $return["error"] = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new NpcManager();

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
    if(!$currentUser->getRight('npc', User::RIGHT_READ)){
      $json = "Vous n'avez pas les droits pour lire cet objet";}else{

      $managerS = new NpcManager();
      
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
        if($currentUser->getRight('npc', User::RIGHT_WRITE)){
          $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Npc.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
        }

        $json[] = array(
          'id' => $obj->getId(Content::FORMAT_BADGE),
          "uniqid" => $obj->getUniqid(),
          "timestamp_add" => $obj->getTimestamp_add(),
          "timestamp_updated" => $obj->getTimestamp_updated(),
          "name" => $obj->getName(),
          "classe" => $obj->getClasse(Content::FORMAT_OBJECT)->getName(),
          "story" => $obj->getStory(),
          "historical" => $obj->getHistorical(),
          "alignment" => $obj->getAlignment(),
          "level" => $obj->getLevel(),
          "master_bonus" => $obj->getMaster_bonus(Content::FORMAT_ICON),
          "trait" => $obj->getTrait(Content::FORMAT_BADGE),
          "other_info" => $obj->getOther_info(),
          "age" => $obj->getAge(Content::FORMAT_VIEW),
          "size" => $obj->getSize(Content::FORMAT_VIEW),
          "weight" => $obj->getWeight(Content::FORMAT_VIEW),
          "life" => $obj->getLife(Content::FORMAT_VIEW),
          "pa" => $obj->getPa(Content::FORMAT_VIEW),
          "pm" => $obj->getPm(Content::FORMAT_VIEW),
          "po" => $obj->getPo(Content::FORMAT_VIEW),
          "ini" => $obj->getIni(Content::FORMAT_VIEW),
          "invocation" => $obj->getInvocation(Content::FORMAT_VIEW),
          "touch" => $obj->getTouch(Content::FORMAT_VIEW),
          "ca" => $obj->getCa(Content::FORMAT_VIEW),
          "dodge_pa" => $obj->getDodge_pa(Content::FORMAT_VIEW),
          "dodge_pm" => $obj->getDodge_pm(Content::FORMAT_VIEW),
          "fuite" => $obj->getFuite(Content::FORMAT_VIEW),
          "tacle" => $obj->getTacle(Content::FORMAT_VIEW),
          "vitality" => $obj->getVitality(Content::FORMAT_VIEW),
          "sagesse" => $obj->getSagesse(Content::FORMAT_VIEW),
          "strong" => $obj->getStrong(Content::FORMAT_VIEW),
          "intel" => $obj->getIntel(Content::FORMAT_VIEW),
          "agi" => $obj->getAgi(Content::FORMAT_VIEW),
          "chance" => $obj->getChance(Content::FORMAT_VIEW),
          "res_neutre" => $obj->getRes_neutre(Content::FORMAT_VIEW),
          "res_terre" => $obj->getRes_terre(Content::FORMAT_VIEW),
          "res_feu" => $obj->getRes_feu(Content::FORMAT_VIEW),
          "res_air" => $obj->getRes_air(Content::FORMAT_VIEW),
          "res_eau" => $obj->getRes_eau(Content::FORMAT_VIEW),
          "acrobatie" => $obj->getAcrobatie(Content::FORMAT_VIEW),
          "discretion" => $obj->getDiscretion(Content::FORMAT_VIEW),
          "escamotage" => $obj->getEscamotage(Content::FORMAT_VIEW),
          "athletisme" => $obj->getAthletisme(Content::FORMAT_VIEW),
          "intimidation" => $obj->getIntimidation(Content::FORMAT_VIEW),
          "arcane" => $obj->getArcane(Content::FORMAT_VIEW),
          "histoire" => $obj->getHistoire(Content::FORMAT_VIEW),
          "investigation" => $obj->getInvestigation(Content::FORMAT_VIEW),
          "nature" => $obj->getNature(Content::FORMAT_VIEW),
          "religion" => $obj->getReligion(Content::FORMAT_VIEW),
          "dressage" => $obj->getDressage(Content::FORMAT_VIEW),
          "medecine" => $obj->getMedecine(Content::FORMAT_VIEW),
          "perception" => $obj->getPerception(Content::FORMAT_VIEW),
          "perspicacite" => $obj->getPerspicacite(Content::FORMAT_VIEW),
          "survie" => $obj->getSurvie(Content::FORMAT_VIEW),
          "persuasion" => $obj->getPersuasion(Content::FORMAT_VIEW),
          "representation" => $obj->getRepresentation(Content::FORMAT_VIEW),
          "supercherie" => $obj->getSupercherie(Content::FORMAT_VIEW),
          "kamas" => $obj->getKamas(Content::FORMAT_BADGE),
          "drop_" => $obj->getDrop_(Content::FORMAT_BADGE),
          "other_item" => $obj->getOther_item(),
          "other_consumable" => $obj->getOther_consumable(),
          "other_spell" => $obj->getOther_spell(),
          "logo" => $obj->getFile('logo',new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
          'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='npc' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
          'edit' => $edit,
          'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=".$obj->getUniqid()."'><i class='fa-solid fa-file-pdf'></i></a>",
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
    if(!$currentUser->getRight('npc', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $manager = new NpcManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            $bookmark_icon = Style::ICON_REGULAR;
            if($currentUser->in_bookmark($obj)){
                $bookmark_icon = Style::ICON_SOLID;
            }

            $edit = "";
            if($currentUser->getRight('npc', User::RIGHT_WRITE)){
              $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Npc.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
            }

            $return["value"] = array(
              'id' => $obj->getId(Content::FORMAT_BADGE),
              "uniqid" => $obj->getUniqid(),
              "timestamp_add" => $obj->getTimestamp_add(),
              "timestamp_updated" => $obj->getTimestamp_updated(),
              "name" => $obj->getName(),
              "classe" => $obj->getClasse(Content::FORMAT_OBJECT)->getName(),
              "story" => $obj->getStory(),
              "historical" => $obj->getHistorical(),
              "alignment" => $obj->getAlignment(),
              "level" => $obj->getLevel(),
              "master_bonus" => $obj->getMaster_bonus(Content::FORMAT_ICON),
              "trait" => $obj->getTrait(Content::FORMAT_BADGE),
              "other_info" => $obj->getOther_info(),
              "age" => $obj->getAge(Content::FORMAT_VIEW),
              "size" => $obj->getSize(Content::FORMAT_VIEW),
              "weight" => $obj->getWeight(Content::FORMAT_VIEW),
              "life" => $obj->getLife(Content::FORMAT_VIEW),
              "pa" => $obj->getPa(Content::FORMAT_VIEW),
              "pm" => $obj->getPm(Content::FORMAT_VIEW),
              "po" => $obj->getPo(Content::FORMAT_VIEW),
              "ini" => $obj->getIni(Content::FORMAT_VIEW),
              "invocation" => $obj->getInvocation(Content::FORMAT_VIEW),
              "touch" => $obj->getTouch(Content::FORMAT_VIEW),
              "ca" => $obj->getCa(Content::FORMAT_VIEW),
              "dodge_pa" => $obj->getDodge_pa(Content::FORMAT_VIEW),
              "dodge_pm" => $obj->getDodge_pm(Content::FORMAT_VIEW),
              "fuite" => $obj->getFuite(Content::FORMAT_VIEW),
              "tacle" => $obj->getTacle(Content::FORMAT_VIEW),
              "vitality" => $obj->getVitality(Content::FORMAT_VIEW),
              "sagesse" => $obj->getSagesse(Content::FORMAT_VIEW),
              "strong" => $obj->getStrong(Content::FORMAT_VIEW),
              "intel" => $obj->getIntel(Content::FORMAT_VIEW),
              "agi" => $obj->getAgi(Content::FORMAT_VIEW),
              "chance" => $obj->getChance(Content::FORMAT_VIEW),
              "res_neutre" => $obj->getRes_neutre(Content::FORMAT_VIEW),
              "res_terre" => $obj->getRes_terre(Content::FORMAT_VIEW),
              "res_feu" => $obj->getRes_feu(Content::FORMAT_VIEW),
              "res_air" => $obj->getRes_air(Content::FORMAT_VIEW),
              "res_eau" => $obj->getRes_eau(Content::FORMAT_VIEW),
              "acrobatie" => $obj->getAcrobatie(Content::FORMAT_VIEW),
              "discretion" => $obj->getDiscretion(Content::FORMAT_VIEW),
              "escamotage" => $obj->getEscamotage(Content::FORMAT_VIEW),
              "athletisme" => $obj->getAthletisme(Content::FORMAT_VIEW),
              "intimidation" => $obj->getIntimidation(Content::FORMAT_VIEW),
              "arcane" => $obj->getArcane(Content::FORMAT_VIEW),
              "histoire" => $obj->getHistoire(Content::FORMAT_VIEW),
              "investigation" => $obj->getInvestigation(Content::FORMAT_VIEW),
              "nature" => $obj->getNature(Content::FORMAT_VIEW),
              "religion" => $obj->getReligion(Content::FORMAT_VIEW),
              "dressage" => $obj->getDressage(Content::FORMAT_VIEW),
              "medecine" => $obj->getMedecine(Content::FORMAT_VIEW),
              "perception" => $obj->getPerception(Content::FORMAT_VIEW),
              "perspicacite" => $obj->getPerspicacite(Content::FORMAT_VIEW),
              "survie" => $obj->getSurvie(Content::FORMAT_VIEW),
              "persuasion" => $obj->getPersuasion(Content::FORMAT_VIEW),
              "representation" => $obj->getRepresentation(Content::FORMAT_VIEW),
              "supercherie" => $obj->getSupercherie(Content::FORMAT_VIEW),
              "kamas" => $obj->getKamas(Content::FORMAT_BADGE),
              "drop_" => $obj->getDrop_(Content::FORMAT_BADGE),
              "other_item" => $obj->getOther_item(),
              "other_consumable" => $obj->getOther_consumable(),
              "other_spell" => $obj->getOther_spell(),
              "logo" => $obj->getFile('logo',new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='npc' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              'edit' => $edit,
              'pdf' => "<a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=".$obj->getUniqid()."'><i class='fa-solid fa-file-pdf'></i></a>",
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
    if(!$currentUser->getRight('npc', User::RIGHT_READ)){
      return "Vous n'avez pas les droits pour générer un pdf";}else{

      if(isset($_REQUEST['uniqid'])){
        $managerS = new NpcManager();
        // Récupération de l'objet
          if($managerS->existsUniqid($_REQUEST['uniqid'])){
            $obj = $managerS->getFromUniqid($_REQUEST['uniqid']);
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
            require "view/pdf/npc.php";
            $html .= $content . "</body></html>";
            $dompdf->loadHtml($html, 'UTF-8');

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();
            
            $dompdf->stream($name.".pdf", array("Attachment" => false));
            return true;
          }
      }
    
    }

  }

  public function getJean(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('npc', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['level'], $_REQUEST['classe'], $_REQUEST['powerful'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
        
          if(isset($_REQUEST['intel'])){$intel = $_REQUEST['intel'];} else {$intel = false;}
          if(isset($_REQUEST['strong'])){$strong = $_REQUEST['strong'];} else {$strong = false;}
          if(isset($_REQUEST['chance'])){$chance = $_REQUEST['chance'];} else {$chance = false;}
          if(isset($_REQUEST['agi'])){$agi = $_REQUEST['agi'];} else {$agi = false;}
          if(isset($_REQUEST['vitality'])){$vitality = $_REQUEST['vitality'];} else {$vitality = false;}
          if(isset($_REQUEST['sagesse'])){$sagesse = $_REQUEST['sagesse'];} else {$sagesse = false;}

          if(isset($_REQUEST['name'])){$name = $_REQUEST['name'];} else {$name = "";}

          $obj = $this->generate(
            $_REQUEST['level'],
            $_REQUEST['classe'],
            $_REQUEST['powerful'],
            $name,
            [
              "intel" => $intel,
              "strong" => $strong,
              "chance" => $chance,
              "agi" => $agi,
              "vitality" => $vitality,
              "sagesse" => $sagesse
            ]
          );

          // instantiate and use the dompdf class
          $dompdf = new Dompdf\Dompdf();
          $dompdf->getOptions()->setChroot($_SERVER["DOCUMENT_ROOT"]);
          $html = "";
          require "view/pdf/header.php";
          $html .= $content;
          require "view/pdf/npc.php";
          $html .= $content . "</body></html>";
          $dompdf->loadHtml($html, 'UTF-8');

          // (Optional) Setup the paper size and orientation
          $dompdf->setPaper('A4', 'portrait');

          // Render the HTML as PDF
          $dompdf->render();
          
          $dompdf->stream($obj->getName().".pdf", array("Attachment" => false));
      }

    }

    echo json_encode($return);
    flush();
  }

  protected function generate($level, $classe, $powerful = 4, $name = "", $speficific_main = []){
    $is_intel = false;
    if(isset($speficific_main['intel'])){
      if($this->returnBool($speficific_main['intel'])){
        $is_intel = true;
      }
    }
    $is_strong = false;
    if(isset($speficific_main['strong'])){
      if($this->returnBool($speficific_main['strong'])){
        $is_strong = true;
      }
    }
    $is_chance = false;
    if(isset($speficific_main['chance'])){
      if($this->returnBool($speficific_main['chance'])){
        $is_chance = true;
      }
    }
    $is_agi = false;
    if(isset($speficific_main['agi'])){
      if($this->returnBool($speficific_main['agi'])){
        $is_agi = true;
      }
    }
    $is_vitality = false;
    if(isset($speficific_main['vitality'])){
      if($this->returnBool($speficific_main['vitality'])){
        $is_vitality = true;
      }
    }
    $is_sagesse = false;
    if(isset($speficific_main['sagesse'])){
      if($this->returnBool($speficific_main['sagesse'])){
        $is_sagesse = true;
      }
    }

    if(!is_numeric($powerful)){
      $powerful = 4;
    }
    if(!is_numeric($level)){
      $level = rand(1,20);
    }
    $coef = 1;
    switch ($powerful) {
      case 1:
        $coef = 0.7;
      break;
      case 2:
        $coef = 0.8;
      break;
      case 3:
        $coef = 0.9;
      break;
      case 5:
        $coef = 1.1;
      break;
      case 6:
        $coef = 1.2;
      break;
      case 7:
        $coef = 1.3;
      break;
      default:
        $coef = 1;
      break;
    }

    $intel = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['base'];
    if($is_intel){
      $intel = $this::calcExp(ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['expression_item'], ['level' => $level]);
      $intel = round($coef * $intel);
      if($intel < ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min']){$intel = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min'];}
      if($intel > ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item']){$intel = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item'];}
    }
    $chance = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['base'];;
    if($is_chance){
        $chance = $this::calcExp(ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['expression_item'], ['level' => $level]);
        $chance = round($coef * $chance);
        if($chance < ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min']){$chance = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min'];}
        if($chance > ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item']){$chance = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item'];}
    }
    $strong = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['base'];;
    if($is_strong){
      if($this->returnBool($speficific_main['strong'])){
        $strong = $this::calcExp(ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['expression_item'], ['level' => $level]);
        $strong = round($coef * $strong);
        if($strong < ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min']){$strong = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min'];}
        if($strong > ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item']){$strong = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item'];}
      }
    }
    $agi = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['base'];;
    if($is_agi){
      $agi = $this::calcExp(ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['expression_item'], ['level' => $level]);
      $agi = round($coef * $agi);
      if($agi < ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min']){$agi = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min'];}
      if($agi > ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item']){$agi = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item'];}
    }
    $vitality = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['base'];;
    if($is_vitality){
      $vitality = $this::calcExp(ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['expression_item'], ['level' => $level]);
      $vitality = round($coef * $vitality);
      if($vitality < ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min']){$vitality = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min'];}
      if($vitality > ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item']){$vitality = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item'];}
    }
    $sagesse = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['base'];;
    if($is_sagesse){
        $sagesse = $this::calcExp(ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['expression_item'], ['level' => $level]);
        $sagesse = round($coef * $sagesse);
        if($sagesse < ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min']){$sagesse = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['min'];}
        if($sagesse > ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item']){$sagesse = ControllerModule::BALANCE_SPEFICIFIC_MAIN['classe']['max_item'];}
      }

    $pa = $this::calcExp(ControllerModule::BALANCE_PA['classe']['expression_item'], ['level' => $level]);
    $pa = round( (1+($coef-1)*0.8) * $pa);
    if($pa < ControllerModule::BALANCE_PA['classe']['min']){$pa = ControllerModule::BALANCE_PA['classe']['min'];}
    if($pa > ControllerModule::BALANCE_PA['classe']['max_item']){$pa = ControllerModule::BALANCE_PA['classe']['max_item'];}

    $pm = $this::calcExp(ControllerModule::BALANCE_PM['classe']['expression_item'], ['level' => $level]);
    $pm = round( (1+($coef-1)*0.8) * $pm);
    if($pm < ControllerModule::BALANCE_PM['classe']['min']){$pm = ControllerModule::BALANCE_PM['classe']['min'];}
    if($pm > ControllerModule::BALANCE_PM['classe']['max_item']){$pm = ControllerModule::BALANCE_PM['classe']['max_item'];}

    $po = $this::calcExp(ControllerModule::BALANCE_PO['classe']['expression_item'], ['level' => $level]);
    $po = round( (1+($coef-1)*0.8) * $po);
    if($po < ControllerModule::BALANCE_PO['classe']['min']){$po = ControllerModule::BALANCE_PO['classe']['min'];}
    if($po > ControllerModule::BALANCE_PO['classe']['max_item']){$po = ControllerModule::BALANCE_PO['classe']['max_item'];}

    $invoc = $this::calcExp(ControllerModule::BALANCE_INVOCATION['classe']['expression_item'], ['level' => $level]);
    $invoc = round( (1+($coef-1)*0.8) * $invoc);
    if($invoc < ControllerModule::BALANCE_INVOCATION['classe']['min']){$invoc = ControllerModule::BALANCE_INVOCATION['classe']['min'];}
    if($invoc > ControllerModule::BALANCE_INVOCATION['classe']['max_item']){$invoc = ControllerModule::BALANCE_INVOCATION['classe']['max_item'];}

    $ini = $this::calcExp(ControllerModule::BALANCE_INI['classe']['expression_base'], ['level' => $level]);
    if(isset($speficific_main['intel'])){
      if($this->returnBool($speficific_main['intel'])){
        $ini += $intel;
        if($ini > ControllerModule::BALANCE_INI['classe']['max_item']){$ini = ControllerModule::BALANCE_INI['classe']['max_item'];}
      }
    }
    $ini = round($coef * $ini);
    if($ini < ControllerModule::BALANCE_INI['classe']['min']){$ini = ControllerModule::BALANCE_INI['classe']['min'];}

    $touch = $this::calcExp(ControllerModule::BALANCE_TOUCH['classe']['expression_item'], ['level' => $level]);

    $res = $this::calcExp(ControllerModule::BALANCE_RES['classe']['expression_item'], ['level' => $level]);
    $res = round( (1+($coef-1)*0.8) * $res);
    if($res < ControllerModule::BALANCE_RES['classe']['min']){$res = ControllerModule::BALANCE_RES['classe']['min'];}
    $res_neutre = ControllerModule::BALANCE_RES['classe']['base'];
    if($powerful >= 5){
        $res_neutre = $res;
    }
    $res_terre = ControllerModule::BALANCE_RES['classe']['base'];
    if($is_strong){$res_terre = $res;}
    $res_feu = ControllerModule::BALANCE_RES['classe']['base'];
    if($is_intel){$res_feu = $res;}
    $res_air = ControllerModule::BALANCE_RES['classe']['base'];
    if($is_agi){$res_air = $res;}
    $res_eau = ControllerModule::BALANCE_RES['classe']['base'];
    if($is_chance){$res_eau = $res;}

    $tacle = $this::calcExp(ControllerModule::BALANCE_TACLE['classe']['expression_base'], ['level' => $level]);
    $fuite = $tacle;
    $tacle = round( (1+($coef-1)*0.8) * $tacle);
    if($tacle < ControllerModule::BALANCE_TACLE['classe']['min']){$tacle = ControllerModule::BALANCE_TACLE['classe']['min'];}
    if($tacle > ControllerModule::BALANCE_TACLE['classe']['max_item']){$tacle = ControllerModule::BALANCE_TACLE['classe']['max_item'];}

    $fuite = round( (1+($coef-1)*0.8) * $fuite);
    if($fuite < ControllerModule::BALANCE_TACLE['classe']['min']){$fuite = ControllerModule::BALANCE_TACLE['classe']['min'];}
    if($fuite > ControllerModule::BALANCE_TACLE['classe']['max_item']){$fuite = ControllerModule::BALANCE_TACLE['classe']['max_item'];}

    $ca = $this::calcExp(ControllerModule::BALANCE_CA['classe']['expression_base'], ['level' => $level]);
    $ca = round( (1+($coef-1)*0.8) * $ca);
    if($ca < ControllerModule::BALANCE_CA['classe']['min']){$ca = ControllerModule::BALANCE_CA['classe']['min'];}
    if($ca > ControllerModule::BALANCE_CA['classe']['max_item']){$ca = ControllerModule::BALANCE_CA['classe']['max_item'];}

    $dodge = $this::calcExp(ControllerModule::BALANCE_DODGE['classe']['expression_base'], ['level' => $level]);
    $dodge = round( (1+($coef-1)*0.8) * $dodge);
    if($dodge < ControllerModule::BALANCE_DODGE['classe']['min']){$dodge = ControllerModule::BALANCE_DODGE['classe']['min'];}
    if($dodge > ControllerModule::BALANCE_DODGE['classe']['max_item']){$dodge = ControllerModule::BALANCE_DODGE['classe']['max_item'];}

    $skill = $this::calcExp(ControllerModule::BALANCE_SKILL['classe']['expression_base'], ['level' => $level]);

    $acrobatie = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $discretion = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $escamotage = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $athletisme = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $intimidation = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $arcane = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $histoire = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $investigation = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $nature = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $religion = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $dressage = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $medecine = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $perception= round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $perspicacite = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $survie = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $persuasion = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $representation = round(rand(-1, 6 * $coef * $coef * $coef) / 5);
    $supercherie = round(rand(-1, 6 * $coef * $coef * $coef) / 5);

    if($is_agi){
      $acrobatie += round($skill);
      $discretion += $acrobatie;
      $escamotage += $acrobatie;
    }
    if($is_strong){
      $athletisme += round($skill);
      $intimidation += $athletisme;
    }
    if($is_intel){
      $arcane += round($skill);
      $histoire += $arcane;
      $investigation += $arcane;
      $nature += $arcane;
      $religion += $arcane;
    }
    if($is_sagesse){
      $dressage += round($skill);
      $medecine += $dressage;
      $perception += $dressage;
      $perspicacite += $dressage;
      $survie += $dressage;
    }
    if($is_chance){
      $persuasion += round($skill);
      $representation += $persuasion;
      $supercherie += $persuasion;
    }

    switch ($_REQUEST['classe']) {
      case Classe::FECA: // Intel et Eau
        $name_auto = "JeanProtection";
        $dice = 10;
        $perspicacite += 3;
        $religion += 2;
        $arcane += 3;
      break;
      case Classe::OSAMODAS: // Intel et Agi
        $name_auto = "JeanInvoc";
        $dice = 10;
        $dressage += 4;
        $perspicacite +=2;
        $nature +=2;
      break;
      case Classe::ENUTROF: // Eau et Intel
        $name_auto = "JeanPelle";
        $dice = 10;
        $investigation += 2;
        $perception += 2;
        $persuasion += 3;
      break;
      case Classe::SRAM: // Force et Agi
        $name_auto = "JeanPiège";
        $dice = 8;
        $discretion += 3;
        $escamotage += 3;
        $perception += 2;
      break;
      case Classe::XELOR: // Force et Intel
        $name_auto = "JeanHorloge";
        $dice = 8;
        $arcane += 3;
        $histoire += 2;
        $investigation += 3;
      break;
      case Classe::ECAFLIP: // Force et Chance
        $name_auto = "Jean4feuilles";
        $dice = 10;
        $acrobatie += 4;
        $discretion += 2;
        $representation += 2;
      break;
      case Classe::ENIRIPSA: // Intel et Agi
        $name_auto = "JeanSoigne";
        $dice = 8;
        $arcane += 2;
        $medecine += 4;
        $perspicacite += 2;
      break;
      case Classe::IOP: // Force et Agi
        $name_auto = "JeanCastagne";
        $dice = 10;
        $athletisme += 2;
        $intimidation += 2;
        $religion += 2;
        $representation += 2;
      break;
      case Classe::CRA: // Chance et Intel
        $name_auto = "JeanFleche";
        $dice = 8;
        $histoire += 3;
        $perception += 3;
        $persuasion += 2;
      break;
      case Classe::SADIDA: // Force et Chance
        $name_auto = "JeanPousse";
        $dice = 10;
        $nature += 4;
        $perception += 2;
        $survie += 2;
      break;
      case Classe::SACRIER: // Force et Chance
        $name_auto = "JeanCaisse";
        $dice = 12;
        $athletisme += 2;
        $intimidation += 2;
        $survie += 4;
      break;
      case Classe::PANDAWA: // Force et intel
        $name_auto = "JeanCabane";
        $dice = 10;
        $athletisme += 4;
        $survie += 2;
        $supercherie += 2;
      break;
    }

    $life =  $this::calcExp(ControllerModule::BALANCE_LIFE['classe']['expression_item'], ['level' => $level, "dice" => $dice]);
    $life += $vitality * $level;
    $random = rand(-0.1 * $life, 0.1 * $life);
    $life = round( (1+($coef-1)*1.7) * $life + $random);
    if($life < ControllerModule::BALANCE_LIFE['classe']['min']){$life = ControllerModule::BALANCE_LIFE['classe']['min'];}

    if(empty($name)){$name = $name_auto;}

    $obj = new Npc(array(
      'name'=>$name,
      'classe'=> $classe,
      'trait'=>"PNJ",
      'level'=>round($level),
      'age'=>"25",
      'size'=>'1m70',
      'weight'=>'70 kg',
      'life'=>round($life),
      'pa'=> round($pa),
      'pm'=> round($pm),
      'po'=> round($po),
      'ini'=> round($ini),
      'touch'=> round($touch),
      'invocation'=>round($level / 3.33),
      'ca'=> round($ca) - 10,
      'dodge_pa'=>round($dodge) - 10,
      'dodge_pm'=>round($dodge) - 10,
      'fuite'=>round($fuite),
      'tacle'=>round($tacle),
      'vitality'=>round($vitality),
      'sagesse'=>round($sagesse),
      'strong'=>round($strong),
      'intel'=>round($intel),
      'agi'=>round($agi),
      'chance'=>round($chance),
      'res_neutre'=>round($res_neutre),
      'res_terre'=>round($res_terre),
      'res_feu'=>round($res_feu),
      'res_air'=>round($res_air),
      'res_eau'=>round($res_eau),
      'acrobatie'=>$acrobatie,
      'discretion'=>$discretion,
      'escamotage'=>$escamotage,
      'athletisme'=>$athletisme,
      'intimidation'=>$intimidation,
      'arcane'=>$arcane,
      'histoire'=>$histoire,
      'investigation'=>$investigation,
      'nature'=>$nature,
      'religion'=>$religion,
      'dressage'=>$dressage,
      'medecine'=>$medecine,
      'perception'=>$perception,
      'perspicacite'=>$perspicacite,
      'survie'=>$survie,
      'persuasion'=>$persuasion,
      'representation'=>$representation,
      'supercherie'=>$supercherie
    ));
    $obj->setUniqid(uniqid());
    $obj->setTimestamp_add();
    $obj->setTimestamp_updated();
    return $obj;
  }
  
  public function add(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('npc', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['name'], $_REQUEST['classe'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          $name = $_REQUEST['name'];
          if(isset($_REQUEST['level'])){
            $level = $_REQUEST['level'];
          } else {
            $level = 1;
          }
          $total_point = round((2 + $level)); // (60 + 200) / 20 => 13 mod par les niveaux et 13 mod par les équipements
          switch ($_REQUEST['classe']) {
            case Classe::FECA: // Intel et Eau
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $intel = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $vitality = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $chance = $rest;
              $sagesse = $total_point;
              $strong = 0;
              $agi = 0;
  
              $life = round(10 + 6 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 8));
              $discretion = 0 + rand(0, round($level / 10));
              $escamotage = 0;
              $athletisme = 0 + rand(0, round($level / 7));
              $intimidation = 0;
              $arcane = 3 + rand(0, round($level / 8));
              $histoire = 0 + rand(0, round($level / 6));
              $investigation = + rand(0, round($level / 6));
              $nature = 0;
              $religion = 2 + rand(0, round($level / 8));
              $dressage = 0;
              $medecine = 0 + rand(0, round($level / 10));
              $perception= 0 + rand(0, round($level / 7));
              $perspicacite = 3 + rand(0, round($level / 6));
              $survie = 0 + rand(0, round($level / 10));
              $persuasion = 0 + rand(0, round($level / 7));
              $representation = 0 + rand(0, round($level / 7));
              $supercherie = 0;
            break;
            case Classe::OSAMODAS: // Intel et Agi
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $sagesse = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $vitality = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $intel = $rest;
              $agi = $total_point;
              $strong = 0;
              $chance = 0;
  
              $life = round(10 + 6 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 8));
              $discretion = 0 + rand(0, round($level / 7));
              $escamotage = 0 + rand(0, round($level / 7));
              $athletisme = 0 + rand(0, round($level / 10));
              $intimidation = 0 + rand(0, round($level / 40));
              $arcane = 0 + rand(0, round($level / 10));
              $histoire = 0 + rand(0, round($level / 8));
              $investigation = 0 + rand(0, round($level / 10));
              $nature = 2 + rand(0, round($level / 6));
              $religion = 0 + rand(0, round($level / 12));
              $dressage = 4 + rand(0, round($level / 5));
              $medecine = 0 + rand(0, round($level / 8));
              $perception= 0 + rand(0, round($level / 8));
              $perspicacite = 2 + rand(0, round($level / 12));
              $survie = 0 + rand(0, round($level / 10));
              $persuasion = 0 + rand(0, round($level / 40));
              $representation = 0 + rand(0, round($level / 40));
              $supercherie = 0 + rand(0, round($level / 40));
            break;
            case Classe::ENUTROF: // Eau et Intel
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $chance = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $sagesse = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $intel = $rest;
              $vitality = $total_point;
              $strong = 0;
              $agi = 0;
  
              $life = round(10 + 6 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 10));
              $discretion = 0 + rand(0, round($level / 9));
              $escamotage = 0 + rand(0, round($level / 8));
              $athletisme = 0 + rand(0, round($level / 40));
              $intimidation = 0 + rand(0, round($level / 40));
              $arcane = 0 + rand(0, round($level / 10));
              $histoire = 0 + rand(0, round($level / 10));
              $investigation = 2 + rand(0, round($level / 8));
              $nature = 0 + rand(0, round($level / 10));
              $religion = 0 + rand(0, round($level / 40));
              $dressage = 0 + rand(0, round($level / 8));
              $medecine = 0 + rand(0, round($level / 40));
              $perception= 2 + rand(0, round($level / 8));
              $perspicacite = 0 + rand(0, round($level / 8));
              $survie = 0 + rand(0, round($level / 40));
              $persuasion = 3 + rand(0, round($level / 6));
              $representation = 0 + rand(0, round($level / 9));
              $supercherie = 0 + rand(0, round($level / 6));
            break;
            case Classe::SRAM: // Force et Agi
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $agi = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $sagesse = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $strong = $rest;
              $vitality = $total_point;
              $intel = 0;
              $chance = 0;
  
              $life = round(10 + 5 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 6));
              $discretion = 3 + rand(0, round($level / 7));
              $escamotage = 3 + rand(0, round($level / 7));
              $athletisme = 0 + rand(0, round($level / 7));
              $intimidation = 0 + rand(0, round($level / 8));
              $arcane = 0 + rand(0, round($level / 10));
              $histoire = 0 + rand(0, round($level / 10));
              $investigation = 0 + rand(0, round($level / 9));
              $nature = 0 + rand(0, round($level / 40));
              $religion = 0 + rand(0, round($level / 40));
              $dressage = 0 + rand(0, round($level / 40));
              $medecine = 0 + rand(0, round($level / 40));
              $perception= 2 + rand(0, round($level / 8));
              $perspicacite = 0 + rand(0, round($level / 9));
              $survie = 0 + rand(0, round($level / 10));
              $persuasion = 0 + rand(0, round($level / 8));
              $representation = 0 + rand(0, round($level / 40));
              $supercherie = 0 + rand(0, round($level / 6));
            break;
            case Classe::XELOR: // Force et Intel
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $intel = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $sagesse = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $strong = $rest;
              $vitality = $total_point;
              $chance = 0;
              $agi = 0;
  
              $life = round(10 + 5 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 40));
              $discretion = 0 + rand(0, round($level / 10));
              $escamotage = 0 + rand(0, round($level / 40));
              $athletisme = 0 + rand(0, round($level / 8));
              $intimidation = 0 + rand(0, round($level / 9));
              $arcane = 3 + rand(0, round($level / 6));
              $histoire = 2 + rand(0, round($level / 7));
              $investigation = 3 + rand(0, round($level / 8));
              $nature = 0 + rand(0, round($level / 10));
              $religion = 0 + rand(0, round($level / 40));
              $dressage = 0 + rand(0, round($level / 40));
              $medecine = 0 + rand(0, round($level / 10));
              $perception= 0 + rand(0, round($level / 8));
              $perspicacite = 0 + rand(0, round($level / 9));
              $survie = 0 + rand(0, round($level / 10));
              $persuasion = 0 + rand(0, round($level / 10));
              $representation = 0 + rand(0, round($level / 10));
              $supercherie = 0 + rand(0, round($level / 40));
            break;
            case Classe::ECAFLIP: // Force et Chance
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $chance = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $sagesse = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $vitality = $rest;
              $strong = $total_point;
              $intel = 0;
              $agi = 0;
  
              $life = round(12 + 6 * $level + $vitality * $level);
  
              $acrobatie = 4 + rand(0, round($level / 6));
              $discretion = 2 + rand(0, round($level / 7));
              $escamotage = 0 + rand(0, round($level / 10));
              $athletisme = 0 + rand(0, round($level / 8));
              $intimidation = 0 + rand(0, round($level / 9));
              $arcane = 0 + rand(0, round($level / 40));
              $histoire = 0 + rand(0, round($level / 10));
              $investigation = 0 + rand(0, round($level / 40));
              $nature = 0 + rand(0, round($level / 10));
              $religion = 0 + rand(0, round($level / 40));
              $dressage = 0 + rand(0, round($level / 9));
              $medecine = 0 + rand(0, round($level / 40));
              $perception= 0 + rand(0, round($level / 10));
              $perspicacite = 0 + rand(0, round($level / 9));
              $survie = 0 + rand(0, round($level / 10));
              $persuasion = 0 + rand(0, round($level / 40));
              $representation = 2 + rand(0, round($level / 10));
              $supercherie = 0 + rand(0, round($level / 9));
            break;
            case Classe::ENIRIPSA: // Intel et Agi

              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $vitality = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $agi = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $sagesse = $rest;
              $intel = $total_point;
              $chance = 0;
              $strong = 0;
  
              $life = round(8 + 5 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 8));
              $discretion = 0 + rand(0, round($level / 8));
              $escamotage = 0 + rand(0, round($level / 40));
              $athletisme = 0 + rand(0, round($level / 40));
              $intimidation = 0 + rand(0, round($level / 40));
              $arcane = 2 + rand(0, round($level / 8));
              $histoire = 0 + rand(0, round($level / 9));
              $investigation = 0 + rand(0, round($level / 9));
              $nature = 0 + rand(0, round($level / 10));
              $religion = 0 + rand(0, round($level / 40));
              $dressage = 0 + rand(0, round($level / 7));
              $medecine = 4 + rand(0, round($level / 6));
              $perception= 0 + rand(0, round($level / 8));
              $perspicacite = 2 + rand(0, round($level / 8));
              $survie = 0 + rand(0, round($level / 10));
              $persuasion = 0 + rand(0, round($level / 10));
              $representation = 0 + rand(0, round($level / 40));
              $supercherie = 0 + rand(0, round($level / 40));
            break;
            case Classe::IOP: // Force et Agi
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $strong = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $agi = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $vitality = $rest;
              $sagesse = $total_point;
              $chance = 0;
              $intel = 0;
  
              $life = round(12 + 6 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 10));
              $discretion = 0 + rand(0, round($level / 40));
              $escamotage = 0 + rand(0, round($level / 40));
              $athletisme = 2 + rand(0, round($level / 7));
              $intimidation = 2 + rand(0, round($level / 7));
              $arcane = 0 + rand(0, round($level / 40));
              $histoire = 0 + rand(0, round($level / 8));
              $investigation = 0 + rand(0, round($level / 10));
              $nature = 0 + rand(0, round($level / 10));
              $religion = 2 + rand(0, round($level / 6));
              $dressage = 0 + rand(0, round($level / 40));
              $medecine = 0 + rand(0, round($level / 10));
              $perception= 0 + rand(0, round($level / 8));
              $perspicacite = 0 + rand(0, round($level / 8));
              $survie = 0 + rand(0, round($level / 10));
              $persuasion = 0 + rand(0, round($level / 40));
              $representation = 2 + rand(0, round($level / 8));
              $supercherie = 0 + rand(0, round($level / 40));
            break;
            case Classe::CRA: // Chance et Intel
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $chance = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $intel = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $sagesse = $rest;
              $vitality = $total_point;
              $strong = 0;
              $agi = 0;
  
              $life = round(10 + 5 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 8));
              $discretion = 0 + rand(0, round($level / 9));
              $escamotage = 0 + rand(0, round($level / 10));
              $athletisme = 0 + rand(0, round($level / 10));
              $intimidation = 0 + rand(0, round($level / 40));
              $arcane = 0 + rand(0, round($level / 9));
              $histoire = 3 + rand(0, round($level / 7));
              $investigation = 0 + rand(0, round($level / 9));
              $nature = 0 + rand(0, round($level / 10));
              $religion = 0 + rand(0, round($level / 40));
              $dressage = 0 + rand(0, round($level / 40));
              $medecine = 0 + rand(0, round($level / 40));
              $perception= 3 + rand(0, round($level / 8));
              $perspicacite = 0 + rand(0, round($level / 9));
              $survie = 0 + rand(0, round($level / 10));
              $persuasion = 2 + rand(0, round($level / 8));
              $representation = 0 + rand(0, round($level / 40));
              $supercherie = 0 + rand(0, round($level / 10));
            break;
            case Classe::SADIDA: // Force et Chance
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $strong = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $sagesse = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $chance = $rest;
              $vitality = $total_point;
              $agi = 0;
              $intel = 0;
  
              $life = round(10 + 6 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 40));
              $discretion = 0 + rand(0, round($level / 40));
              $escamotage = 0 + rand(0, round($level / 40));
              $athletisme = 0 + rand(0, round($level / 7));
              $intimidation = 0 + rand(0, round($level / 7));
              $arcane = 0 + rand(0, round($level / 40));
              $histoire = 0 + rand(0, round($level / 40));
              $investigation = 0 + rand(0, round($level / 10));
              $nature = 4 + rand(0, round($level / 7));
              $religion = 0 + rand(0, round($level / 10));
              $dressage = 0 + rand(0, round($level / 8));
              $medecine = 0 + rand(0, round($level / 10));
              $perception= 2 + rand(0, round($level / 8));
              $perspicacite = 0 + rand(0, round($level / 8));
              $survie = 2 + rand(0, round($level / 7));
              $persuasion = 0 + rand(0, round($level / 10));
              $representation = 0 + rand(0, round($level / 8));
              $supercherie = 0 + rand(0, round($level / 10));
            break;
            case Classe::SACRIER: // Force et Chance
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $vitality = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $strong = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $chance = $rest;
              $sagesse = $total_point;
              $agi = 0;
              $intel = 0;
  
              $life = round(12 + 7 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 10));
              $discretion = 0 + rand(0, round($level / 40));
              $escamotage = 0 + rand(0, round($level / 40));
              $athletisme = 2 + rand(0, round($level / 7));
              $intimidation = 2 + rand(0, round($level / 7));
              $arcane = 0 + rand(0, round($level / 10));
              $histoire = 0 + rand(0, round($level / 40));
              $investigation = 0 + rand(0, round($level / 10));
              $nature = 0 + rand(0, round($level / 10));
              $religion = 0 + rand(0, round($level / 40));
              $dressage = 0 + rand(0, round($level / 40));
              $medecine = 0 + rand(0, round($level / 10));
              $perception = 0 + rand(0, round($level / 8));
              $perspicacite = 0 + rand(0, round($level / 8));
              $survie = 4 + rand(0, round($level / 6));
              $persuasion = 0 + rand(0, round($level / 9));
              $representation = 0 + rand(0, round($level / 7));
              $supercherie = 0 + rand(0, round($level / 10));
            break;
            case Classe::PANDAWA: // Force et intel
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $intel = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $strong = $rest;
              $rest = rand(0, $total_point); if($total_point - $rest <= 0 ){ $total_point = 0;}else{$total_point -= $rest;}
              $vitality = $rest;
              $sagesse = $total_point;
              $agi = 0;
              $chance = 0;
  
              $life = round(12 + 6 * $level + $vitality * $level);
  
              $acrobatie = 0 + rand(0, round($level / 10));
              $discretion = 0 + rand(0, round($level / 10));
              $escamotage = 0 + rand(0, round($level / 10));
              $athletisme = 4 + rand(0, round($level / 7));
              $intimidation = 0 + rand(0, round($level / 7));
              $arcane = 0 + rand(0, round($level / 8));
              $histoire = 0 + rand(0, round($level / 10));
              $investigation = 0 + rand(0, round($level / 9));
              $nature = 0 + rand(0, round($level / 7));
              $religion = 0 + rand(0, round($level / 10));
              $dressage = 0 + rand(0, round($level / 40));
              $medecine = 0 + rand(0, round($level / 40));
              $perception = 0 + rand(0, round($level / 9));
              $perspicacite = 0 + rand(0, round($level / 8));
              $survie = 2 + rand(0, round($level / 7));
              $persuasion = 0 + rand(0, round($level / 40));
              $representation = 0 + rand(0, round($level / 9));
              $supercherie = 2 + rand(0, round($level / 6));
            break;
            
          }
        
            $manager = new NpcManager();
            $obj = new Npc(array(
              'name'=>$name,
              'classe'=> $_REQUEST['classe'],
              'level'=>$level,
              'age'=>"25",
              'size'=>'1m70',
              'weight'=>'70 kg',
              'life'=>$life,
              'pa'=> round( 6 + $level / 3.33),
              'pm'=>round(3 + $level / 7.3),
              'po'=> round($level / 3.33),
              'ini'=>round($level / 3.33),
              'invocation'=>round($level / 3.33),
              'touch'=>round($level / 3),
              'ca'=> round($level / 3.33),
              'dodge_pa'=>round($level / 3.33),
              'dodge_pm'=>round($level / 3.33),
              'fuite'=>round($level / 3.33),
              'tacle'=>round($level / 3.33),
              'vitality'=>$vitality,
              'sagesse'=>$sagesse,
              'strong'=>$strong,
              'intel'=>$intel,
              'agi'=>$agi,
              'chance'=>$chance,
              'res_neutre'=>round($level / 4.4),
              'res_terre'=>round($level / 4.4),
              'res_feu'=>round($level / 4.4),
              'res_air'=>round($level / 4.4),
              'res_eau'=>round($level / 4.4),
              'acrobatie'=>$acrobatie,
              'discretion'=>$discretion,
              'escamotage'=>$escamotage,
              'athletisme'=>$athletisme,
              'intimidation'=>$intimidation,
              'arcane'=>$arcane,
              'histoire'=>$histoire,
              'investigation'=>$investigation,
              'nature'=>$nature,
              'religion'=>$religion,
              'dressage'=>$dressage,
              'medecine'=>$medecine,
              'perception'=>$perception,
              'perspicacite'=>$perspicacite,
              'survie'=>$survie,
              'persuasion'=>$persuasion,
              'representation'=>$representation,
              'supercherie'=>$supercherie
            ));
            $obj->setUniqid(uniqid());
            $obj->setTimestamp_add();
            $obj->setTimestamp_updated();
            
            if($manager->add($obj)){
              $return['state'] = true;
              $return['script'] = "Npc.open('".$obj->getUniqid()."', Controller.DISPLAY_EDITABLE)";
            }else {
              $return['error'] = 'Impossible d\'ajouter l\'objet';
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

    if(!$currentUser->getRight('npc', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour modifier cet objet";} else {
      
      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          // Récupération des objets
            $manager = new NpcManager();

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
    if(!$currentUser->getRight('npc', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {

        $array = [];
        $manager = new NpcManager;
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
                    $click_action = "onclick=\"Npc.open('".$object->getUniqid()."')\"";
                  break;
                }

                $name = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getName());
                ob_start();   ?>
                  <a <?=$click_action?> class="d-flex justify-content-between align-items-baseline flex-nowrap">
                    <div class="d-flex justify-content-start align-item-baseline">
                      <div class="img-back-20 me-2" style="background-image:url(<?=$object->getClasse(Content::FORMAT_OBJECT)->getFile('logo', new Style(['format' => Content::FORMAT_BRUT]))?>)"></div>
                      <?=$name?>
                    </div>
                    <p><small class='size-0-6 badge back-indigo-l-1 mx-2'>PNJ</small></p>
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
 