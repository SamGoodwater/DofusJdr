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
          "name" => "<b>". $obj->getName()."</b>",
          "classe" => $obj->getClasse(Content::FORMAT_OBJECT)->getName(),
          "description" => $obj->getDescription(),
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
          "do_fixe_neutre" => $obj->getDo_fixe_neutre(Content::FORMAT_VIEW),
          "do_fixe_terre" => $obj->getDo_fixe_terre(Content::FORMAT_VIEW),
          "do_fixe_feu" => $obj->getDo_fixe_feu(Content::FORMAT_VIEW),
          "do_fixe_air" => $obj->getDo_fixe_air(Content::FORMAT_VIEW),
          "do_fixe_eau" => $obj->getDo_fixe_eau(Content::FORMAT_VIEW),
          "do_fixe_multiple" => $obj->getDo_fixe_multiple(Content::FORMAT_VIEW),
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
          'resume' => "<div class='size-0-8 col'><div>{$obj->getPa(Content::FORMAT_ICON)}</div><div>{$obj->getPm(Content::FORMAT_ICON)}</div><div>{$obj->getPo(Content::FORMAT_ICON)}</div><div>{$obj->getIni(Content::FORMAT_ICON)}</div><div>{$obj->getTouch(Content::FORMAT_ICON)}</div></div>",
          'resumeattack' => "<div class='size-0-8 col'><div>{$obj->getVitality(Content::FORMAT_ICON)}</div><div>{$obj->getSagesse(Content::FORMAT_ICON)}</div><div>{$obj->getStrong(Content::FORMAT_ICON)}</div><div>{$obj->getIntel(Content::FORMAT_ICON)}</div><div>{$obj->getAgi(Content::FORMAT_ICON)}</div><div>{$obj->getChance(Content::FORMAT_ICON)}</div></div>",
          'resumedefend' => "<div class='size-0-8 col'><div>{$obj->getCa(Content::FORMAT_ICON)}</div><div>{$obj->getFuite(Content::FORMAT_ICON)}</div><div>{$obj->getTacle(Content::FORMAT_ICON)}</div><div>{$obj->getDodge_pa(Content::FORMAT_ICON)}</div><div>{$obj->getDodge_pm(Content::FORMAT_ICON)}</div></div>",
          'resumedom' => "<div class='size-0-8 col'><div>{$obj->getDo_fixe_neutre(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_terre(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_feu(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_air(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_eau(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_multiple(Content::FORMAT_ICON)}</div></div>",
          'resumeres' => "<div class='size-0-8 col'><div>{$obj->getRes_neutre(Content::FORMAT_ICON)}</div><div>{$obj->getRes_terre(Content::FORMAT_ICON)}</div><div>{$obj->getRes_feu(Content::FORMAT_ICON)}</div><div>{$obj->getRes_air(Content::FORMAT_ICON)}</div><div>{$obj->getRes_eau(Content::FORMAT_ICON)}</div></div>",
          "logo" => $obj->getFile('logo',new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
          'bookmark' => "<a onclick='User.toogleBookmark(this);' data-classe='npc' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
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
              "name" => "<b>". $obj->getName()."</b>",
              "classe" => $obj->getClasse(Content::FORMAT_OBJECT)->getName(),
              "description" => $obj->getDescription(),
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
              "do_fixe_neutre" => $obj->getDo_fixe_neutre(Content::FORMAT_VIEW),
              "do_fixe_terre" => $obj->getDo_fixe_terre(Content::FORMAT_VIEW),
              "do_fixe_feu" => $obj->getDo_fixe_feu(Content::FORMAT_VIEW),
              "do_fixe_air" => $obj->getDo_fixe_air(Content::FORMAT_VIEW),
              "do_fixe_eau" => $obj->getDo_fixe_eau(Content::FORMAT_VIEW),
              "do_fixe_multiple" => $obj->getDo_fixe_multiple(Content::FORMAT_VIEW),
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
              'resume' => "<div class='size-0-8 col'><div>{$obj->getPa(Content::FORMAT_ICON)}</div><div>{$obj->getPm(Content::FORMAT_ICON)}</div><div>{$obj->getPo(Content::FORMAT_ICON)}</div><div>{$obj->getIni(Content::FORMAT_ICON)}</div><div>{$obj->getTouch(Content::FORMAT_ICON)}</div></div>",
              'resumeattack' => "<div class='size-0-8 col'><div>{$obj->getVitality(Content::FORMAT_ICON)}</div><div>{$obj->getSagesse(Content::FORMAT_ICON)}</div><div>{$obj->getStrong(Content::FORMAT_ICON)}</div><div>{$obj->getIntel(Content::FORMAT_ICON)}</div><div>{$obj->getAgi(Content::FORMAT_ICON)}</div><div>{$obj->getChance(Content::FORMAT_ICON)}</div></div>",
              'resumedefend' => "<div class='size-0-8 col'><div>{$obj->getCa(Content::FORMAT_ICON)}</div><div>{$obj->getFuite(Content::FORMAT_ICON)}</div><div>{$obj->getTacle(Content::FORMAT_ICON)}</div><div>{$obj->getDodge_pa(Content::FORMAT_ICON)}</div><div>{$obj->getDodge_pm(Content::FORMAT_ICON)}</div></div>",
              'resumedom' => "<div class='size-0-8 col'><div>{$obj->getDo_fixe_neutre(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_terre(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_feu(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_air(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_eau(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_multiple(Content::FORMAT_ICON)}</div></div>",
              'resumeres' => "<div class='size-0-8 col'><div>{$obj->getRes_neutre(Content::FORMAT_ICON)}</div><div>{$obj->getRes_terre(Content::FORMAT_ICON)}</div><div>{$obj->getRes_feu(Content::FORMAT_ICON)}</div><div>{$obj->getRes_air(Content::FORMAT_ICON)}</div><div>{$obj->getRes_eau(Content::FORMAT_ICON)}</div></div>",
              "logo" => $obj->getFile('logo',new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_XL])),
              'bookmark' => "<a onclick='User.toogleBookmark(this);' data-classe='npc' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
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

  protected function generate(Npc $npc = null, $level, $classe, $powerful = 4, $name = "", $name_ai = [], $speficific_main = []){
    $master_bonus = (1 + $level / 4); 
    
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
        $coef = 0.75;
      break;
      case 2:
        $coef = 0.85;
      break;
      case 3:
        $coef = 0.9;
      break;
      case 5:
        $coef = 1.1;
      break;
      case 6:
        $coef = 1.15;
      break;
      case 7:
        $coef = 1.25;
      break;
      default:
        $coef = 1;
      break;
    }

    $points_to_distribution = 6;
    if($powerful > 7){$points_to_distribution = 7;}
    if($powerful < 3){$points_to_distribution = 5;}
    switch ($level) {
      case 2:
        $points_to_distribution += 1;
      break;
      case 4:
        $points_to_distribution += 2;
      break;
      case 6:
        $points_to_distribution += 3;
      break;
      case 10:
        $points_to_distribution += 4;
      break;
      case 12:
        $points_to_distribution += 5;
      break;
      case 14:
        $points_to_distribution += 6;
      break;
      case 16:
        $points_to_distribution += 7;
      break;
      case 20:
        $points_to_distribution += 8;
      break;
    }

    $caracteristicsFillingOrder = [];
    if($is_intel){array_push($caracteristicsFillingOrder, "intel");}
    if($is_strong){array_push($caracteristicsFillingOrder, "strong");}
    if($is_chance){array_push($caracteristicsFillingOrder, "chance");}
    if($is_agi){array_push($caracteristicsFillingOrder, "agi");}
    if(!empty($caracteristicsFillingOrder)){
      shuffle($caracteristicsFillingOrder);
    }
    $caracteristicsFillingOrderVitaAndSagesse = [];
    if($is_vitality){array_push($caracteristicsFillingOrderVitaAndSagesse, "vitality");}
    if($is_sagesse){array_push($caracteristicsFillingOrderVitaAndSagesse, "sagesse");}
    if(!empty($caracteristicsFillingOrderVitaAndSagesse)){
      shuffle($caracteristicsFillingOrderVitaAndSagesse);
      array_push($caracteristicsFillingOrder, $caracteristicsFillingOrderVitaAndSagesse);
    }

    switch ($_REQUEST['classe']) {
      case Classe::FECA: // Intel, force et chance
        $caracteristicsToFill = [];
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        if(!in_array("strong", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "strong");}
        if(!in_array("chance", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "chance");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
      case Classe::OSAMODAS: // Intel, agi et force
        $caracteristicsToFill = [];
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        if(!in_array("agi", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "agi");}
        if(!in_array("strong", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "strong");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
      case Classe::ENUTROF: // Eau et Feu, terre
        $caracteristicsToFill = [];
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        if(!in_array("strong", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "strong");}
        if(!in_array("chance", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "chance");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
      case Classe::SRAM: // Terre, eau, air
        $caracteristicsToFill = [];
        if(!in_array("strong", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "strong");}
        if(!in_array("chance", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "chance");}
        if(!in_array("agi", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "agi");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
      case Classe::XELOR: // Force et eau, feu
        $caracteristicsToFill = [];
        if(!in_array("strong", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "strong");}
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        if(!in_array("chance", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "chance");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);

      break;
      case Classe::ECAFLIP: // Feu, air et Chance
        $caracteristicsToFill = [];
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        if(!in_array("agi", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "agi");}
        if(!in_array("chance", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "chance");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
      case Classe::ENIRIPSA: // Intel et Agi, eau
        $caracteristicsToFill = [];
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        if(!in_array("agi", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "agi");}
        if(!in_array("chance", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "chance");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
      case Classe::IOP: // Force et Agi, feu
        $caracteristicsToFill = [];
        if(!in_array("strong", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "strong");}
        if(!in_array("agi", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "agi");}
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
      case Classe::CRA: // Chance et Intel, air
        $caracteristicsToFill = [];
        if(!in_array("chance", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "chance");}
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        if(!in_array("agi", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "agi");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
      case Classe::SADIDA: // Force et Chance, feu
        $caracteristicsToFill = [];
        if(!in_array("strong", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "strong");}
        if(!in_array("chance", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "chance");}
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
      case Classe::SACRIER: // Force et air, feu
        $caracteristicsToFill = [];
        if(!in_array("strong", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "strong");}
        if(!in_array("agi", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "agi");}
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
      case Classe::PANDAWA: // Force et intel, air
        $caracteristicsToFill = [];
        if(!in_array("strong", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "strong");}
        if(!in_array("intel", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "intel");}
        if(!in_array("agi", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "agi");}
        shuffle($caracteristicsToFill);
        array_push($caracteristicsFillingOrder, $caracteristicsToFill[0]);
      break;
    }

    $caracteristicsToFill = [];
    if(!in_array("vitality", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "vitality");}
    if(!in_array("sagesse", $caracteristicsFillingOrder)){array_push($caracteristicsToFill, "sagesse");}
    shuffle($caracteristicsToFill);
    array_push($caracteristicsFillingOrder, $caracteristicsToFill);

    $mainCaracteristics = Creature::distribCaractericticsPoints(pointsTotals:$points_to_distribution, level:$level, caracteristicsFillingOrder:$caracteristicsFillingOrder);
    $intel = $mainCaracteristics["intel"];
    $strong = $mainCaracteristics["strong"];
    $chance = $mainCaracteristics["chance"];
    $agi = $mainCaracteristics["agi"];
    $vitality = $mainCaracteristics["vitality"];
    $sagesse = $mainCaracteristics["sagesse"];
    
    $pa = $this::calcExp(Creature::CARACTERISTICS['pa']['classe']['expression_base'], ['level' => $level]);
    if($pa < Creature::CARACTERISTICS['pa']['classe']['min']){$pa = Creature::CARACTERISTICS['pa']['classe']['min'];}
    if($pa > Creature::CARACTERISTICS['pa']['classe']['max_item']){$pa = Creature::CARACTERISTICS['pa']['classe']['max_item'];}

    $pm = $this::calcExp(Creature::CARACTERISTICS['pm']['classe']['expression_base'], ['level' => $level]);
    if($pm < Creature::CARACTERISTICS['pm']['classe']['min']){$pm = Creature::CARACTERISTICS['pm']['classe']['min'];}
    if($pm > Creature::CARACTERISTICS['pm']['classe']['max_item']){$pm = Creature::CARACTERISTICS['pm']['classe']['max_item'];}

    $po = $this::calcExp(Creature::CARACTERISTICS['po']['classe']['expression_base'], ['level' => $level]);
    if($po < Creature::CARACTERISTICS['po']['classe']['min']){$po = Creature::CARACTERISTICS['po']['classe']['min'];}
    if($po > Creature::CARACTERISTICS['po']['classe']['max_item']){$po = Creature::CARACTERISTICS['po']['classe']['max_item'];}

    $invoc = $this::calcExp(Creature::CARACTERISTICS['invocation']['classe']['expression_base'], ['level' => $level]);
    if($invoc < Creature::CARACTERISTICS['invocation']['classe']['min']){$invoc = Creature::CARACTERISTICS['invocation']['classe']['min'];}
    if($invoc > Creature::CARACTERISTICS['invocation']['classe']['max_item']){$invoc = Creature::CARACTERISTICS['invocation']['classe']['max_item'];}

    $ini = $this::calcExp(Creature::CARACTERISTICS['ini']['classe']['expression_base'], ['level' => $level]);
    $ini += $intel;
    if($ini > Creature::CARACTERISTICS['ini']['classe']['max_item']){$ini = Creature::CARACTERISTICS['ini']['classe']['max_item'];}
    if($ini < Creature::CARACTERISTICS['ini']['classe']['min']){$ini = Creature::CARACTERISTICS['ini']['classe']['min'];}

    $touch = $this::calcExp(Creature::CARACTERISTICS['touch']['classe']['expression_base'], ['level' => $level]);

    $tacle = $this::calcExp(Creature::CARACTERISTICS['tacle']['classe']['expression_base'], ['level' => $level]);
    $fuite = $tacle;
    $tacle += $chance;
    if($tacle < Creature::CARACTERISTICS['tacle']['classe']['min']){$tacle = Creature::CARACTERISTICS['tacle']['classe']['min'];}
    if($tacle > Creature::CARACTERISTICS['tacle']['classe']['max_item']){$tacle = Creature::CARACTERISTICS['tacle']['classe']['max_item'];}

    $fuite += $agi;
    if($fuite < Creature::CARACTERISTICS['fuite']['classe']['min']){$fuite = Creature::CARACTERISTICS['fuite']['classe']['min'];}
    if($fuite > Creature::CARACTERISTICS['fuite']['classe']['max_item']){$fuite = Creature::CARACTERISTICS['fuite']['classe']['max_item'];}

    $ca = $this::calcExp(Creature::CARACTERISTICS['ca']['classe']['expression_base'], ['level' => $level]);
    if($ca < Creature::CARACTERISTICS['ca']['classe']['min']){$ca = Creature::CARACTERISTICS['ca']['classe']['min'];}
    if($ca > Creature::CARACTERISTICS['ca']['classe']['max_item']){$ca = Creature::CARACTERISTICS['ca']['classe']['max_item'];}

    $dodge_pa = $this::calcExp(Creature::CARACTERISTICS['dodge_pa']['classe']['expression_base'], ['level' => $level]);
    $dodge_pa += $sagesse;
    if($dodge_pa < Creature::CARACTERISTICS['dodge_pa']['classe']['min']){$dodge_pa = Creature::CARACTERISTICS['dodge_pa']['classe']['min'];}
    if($dodge_pa > Creature::CARACTERISTICS['dodge_pa']['classe']['max_item']){$dodge_pa = Creature::CARACTERISTICS['dodge_pa']['classe']['max_item'];}

    $dodge_pm = $this::calcExp(Creature::CARACTERISTICS['dodge_pm']['classe']['expression_base'], ['level' => $level]);
    $dodge_pm += $sagesse;
    if($dodge_pm < Creature::CARACTERISTICS['dodge_pm']['classe']['min']){$dodge_pm = Creature::CARACTERISTICS['dodge_pm']['classe']['min'];}
    if($dodge_pm > Creature::CARACTERISTICS['dodge_pm']['classe']['max_item']){$dodge_pm = Creature::CARACTERISTICS['dodge_pm']['classe']['max_item'];}

    $skill_agi = $this::calcExp(Creature::CARACTERISTICS['skill_agi_of_choice']['classe']['expression_base'], ['level' => $level]);
    $skill_agi += $agi;
    $skill_force = $this::calcExp(Creature::CARACTERISTICS['skill_force_of_choice']['classe']['expression_base'], ['level' => $level]);
    $skill_force += $strong;
    $skill_intel = $this::calcExp(Creature::CARACTERISTICS['skill_intel_of_choice']['classe']['expression_base'], ['level' => $level]);
    $skill_intel += $intel;
    $skill_sagesse = $this::calcExp(Creature::CARACTERISTICS['skill_sagesse_of_choice']['classe']['expression_base'], ['level' => $level]);
    $skill_sagesse += $sagesse;
    $skill_chance = $this::calcExp(Creature::CARACTERISTICS['skill_chance_of_choice']['classe']['expression_base'], ['level' => $level]);
    $skill_chance += $chance;

    $acrobatie = round($agi);
    $discretion = round($agi);
    $escamotage = round($agi);
    $athletisme = round($strong);
    $intimidation = round($strong);
    $arcane = round($intel);
    $histoire = round($intel);
    $investigation = round($intel);
    $nature = round($intel);
    $religion = round($intel);
    $dressage = round($sagesse);
    $medecine = round($sagesse);
    $perception= round($sagesse);
    $perspicacite = round($sagesse);
    $survie = round($sagesse);
    $persuasion = round($chance);
    $representation = round($chance);
    $supercherie = round($chance);

    switch ($_REQUEST['classe']) {
      case Classe::FECA:
        $name_auto = "JeanProtection";
        $dice = 10;
        $perspicacite += $master_bonus;
        $religion += $master_bonus;
        $arcane += $master_bonus;
      break;
      case Classe::OSAMODAS:
        $name_auto = "JeanInvoc";
        $dice = 10;
        $dressage += $master_bonus;
        $perspicacite += $master_bonus;
        $nature +=$master_bonus;
      break;
      case Classe::ENUTROF:
        $name_auto = "JeanPelle";
        $dice = 10;
        $investigation += $master_bonus;
        $perception += $master_bonus;
        $persuasion += $master_bonus;
      break;
      case Classe::SRAM:
        $name_auto = "JeanPiège";
        $dice = 8;
        $discretion += $master_bonus;
        $escamotage += $master_bonus;
        $perception += $master_bonus;
      break;
      case Classe::XELOR:
        $name_auto = "JeanHorloge";
        $dice = 8;
        $arcane += $master_bonus;
        $histoire += $master_bonus;
        $investigation += $master_bonus;
      break;
      case Classe::ECAFLIP:
        $name_auto = "Jean4feuilles";
        $dice = 10;
        $acrobatie += $master_bonus;
        $discretion += $master_bonus;
        $representation += $master_bonus;
      break;
      case Classe::ENIRIPSA:
        $name_auto = "JeanSoigne";
        $dice = $master_bonus;
        $arcane += $master_bonus;
        $medecine += $master_bonus;
        $perspicacite += $master_bonus;
      break;
      case Classe::IOP:
        $name_auto = "JeanCastagne";
        $dice = 10;
        $athletisme += $master_bonus;
        $intimidation += $master_bonus;
        $religion += $master_bonus;
      break;
      case Classe::CRA:
        $name_auto = "JeanFleche";
        $dice = 8;
        $histoire += $master_bonus;
        $perception += $master_bonus;
        $persuasion += $master_bonus;
      break;
      case Classe::SADIDA:
        $name_auto = "JeanPousse";
        $dice = 10;
        $nature += $master_bonus;
        $perception += $master_bonus;
        $survie += $master_bonus;
      break;
      case Classe::SACRIER:
        $name_auto = "JeanCaisse";
        $dice = 12;
        $athletisme += $master_bonus;
        $intimidation += $master_bonus;
        $survie += $master_bonus;
      break;
      case Classe::PANDAWA:
        $name_auto = "JeanCabane";
        $dice = 10;
        $athletisme += $master_bonus;
        $survie += $master_bonus;
        $supercherie += $master_bonus;
      break;
    }

    if(!empty($name_ai)){
      if(isset($name_ai['use_ai'])){
        if($name_ai == true){
          $manager = new ClasseManager;
          if($manager->existsUniqid($_REQUEST['classe'])){
            $classe_name = $manager->getFromUniqid($_REQUEST['classe'])->getName();
            
            if(isset($name_ai['genre'])){
              $genre_ai = $name_ai['genre'];
            }
            if(isset($name_ai['genre'])){
              $inspiration_culturel = $name_ai['inspiration_culturel'];
            }
    
            $ai = new ArtificialIntelligence();
            $state = $ai->dispatch(
                name_file : $_REQUEST['template'],
                data : [
                    'classe' => $classe_name,
                    'genre' => $genre_ai,
                    'inspiration_culturel' => $inspiration_culturel
                ]
            );
            if($state['state'] === true){
                $name_auto = $state['value'];
            }
          }
        }
      }
    }

    $life =  $this::calcExp(Creature::CARACTERISTICS['life']['classe']['expression_base'], ['level' => $level, "dice" => $dice]);
    $life += $vitality * $level;
    $random = rand(-0.1 * $life, 0.1 * $life);
    $life = round( (1+($coef-1)*1.7) * $life + $random);
    if($life < Creature::CARACTERISTICS['life']['classe']['min']){$life = Creature::CARACTERISTICS['life']['classe']['min'];}

    if(empty($name)){$name = $name_auto;}

    if(!empty($npc)){
        $npc->getName($name);
        $npc->getClasse($classe);
        $npc->getTrait("PNJ");
        $npc->getLevel(round($level));
        $npc->getAge("25");
        $npc->getSize('1m70');
        $npc->getWeight('70 kg');
        $npc->getLife(round($life));
        $npc->getPa(round($pa));
        $npc->getPm(round($pm));
        $npc->getPo(round($po));
        $npc->getIni(round($ini));
        $npc->getTouch(round($touch));
        $npc->getInvocation(round($level / 3.33));
        $npc->getCa(round($ca) - 10);
        $npc->getDodge_pa(round($dodge_pa) - 10);
        $npc->getDodge_pm(round($dodge_pm) - 10);
        $npc->getFuite(round($fuite));
        $npc->getTacle(round($tacle));
        $npc->getVitality(round($vitality));
        $npc->getSagesse(round($sagesse));
        $npc->getStrong(round($strong));
        $npc->getIntel(round($intel));
        $npc->getAgi(round($agi));
        $npc->getChance(round($chance));
        $npc->getAcrobatie($acrobatie);
        $npc->getDiscretion($discretion);
        $npc->getEscamotage($escamotage);
        $npc->getAthletisme($athletisme);
        $npc->getIntimidation($intimidation);
        $npc->getArcane($arcane);
        $npc->getHistoire($histoire);
        $npc->getInvestigation($investigation);
        $npc->getNature($nature);
        $npc->getReligion($religion);
        $npc->getDressage($dressage);
        $npc->getMedecine($medecine);
        $npc->getPerception($perception);
        $npc->getPerspicacite($perspicacite);
        $npc->getSurvie($survie);
        $npc->getPersuasion($persuasion);
        $npc->getRepresentation($representation);
        $npc->getSupercherie($supercherie);
    } else {
      $npc = new Npc(array(
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
        'dodge_pa'=>round($dodge_pa) - 10,
        'dodge_pm'=>round($dodge_pm) - 10,
        'fuite'=>round($fuite),
        'tacle'=>round($tacle),
        'vitality'=>round($vitality),
        'sagesse'=>round($sagesse),
        'strong'=>round($strong),
        'intel'=>round($intel),
        'agi'=>round($agi),
        'chance'=>round($chance),
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
      $npc->setUniqid(uniqid());
      $npc->setTimestamp_add();
      $npc->setTimestamp_updated();
    }
    return $npc;
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
          $level = 1;
          if(isset($_REQUEST['level'])){
            $level = $_REQUEST['level'];
          }
          $powerful = 4;
          if(isset($_REQUEST['powerful'])){
            $powerful = $_REQUEST['powerful'];
          }
          $use_ai = false;
          if(isset($_REQUEST['use_ai'])){
            $use_ai = $this->returnBool($_REQUEST['use_ai']);
          }
          $genre = "non binaire";
          if(isset($_REQUEST['genre'])){
            $genre = $_REQUEST['genre'];
          }
          $inspiration_culturel = "";
          if(isset($_REQUEST['inspiration_culturel'])){
            $inspiration_culturel = $_REQUEST['inspiration_culturel'];
          }

          $obj = $this->generate(
            npc: null,
            level: $level,
            classe: $_REQUEST['classe'],
            powerful: $powerful,
            name: $name,
            name_ai:[
              "use_ai" => $use_ai,
              "genre" => $genre,
              "inspiration_culturel" => $inspiration_culturel
            ],
          );

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
                    $click_action = "onclick=\"User.toogleBookmark(this);\" data-classe=\"".strtolower(get_class($object))."\" data-uniqid=\"".$object->getUniqid()."\"";
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
 