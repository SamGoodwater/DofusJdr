<?php
class ControllerMob extends ControllerModule{
  public function count(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue'
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    
    if(!$currentUser->getRight('mob', User::RIGHT_READ)){
      $return["error"] = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new MobManager();
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
    if(!$currentUser->getRight('mob', User::RIGHT_READ)){
      $json = "Vous n'avez pas les droits pour lire cet objet";}else{

      $managerS = new MobManager();
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

      $objects = $managerS->getAll(
        usable:$usable,
        offset:$offset,
        limit:$limit
      );

      foreach ($objects as $obj) {

        $bookmark_icon = Style::ICON_REGULAR;
        if($currentUser->in_bookmark($obj)){
            $bookmark_icon = Style::ICON_SOLID;
        }

        $edit = "";
        if($currentUser->getRight('mob', User::RIGHT_WRITE)){
          $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Mob.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
        }

        $json[] = array(
          'id' => $obj->getId(Content::FORMAT_BADGE),
          'uniqid' => $obj->getUniqid(),
          'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
          'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
          'name' => "<b>". $obj->getName()."</b>",
          "description" => $obj->getDescription(),
          'level' => $obj->getLevel(),
          'vitality'=> $obj->getVitality(Content::FORMAT_ICON),
          'pa'=> $obj->getPa(Content::FORMAT_ICON),
          'pm'=> $obj->getPm(Content::FORMAT_ICON),
          'po'=> $obj->getPo(Content::FORMAT_ICON),
          'ini'=> $obj->getIni(Content::FORMAT_ICON),
          'touch'=> $obj->getTouch(Content::FORMAT_ICON),
          'life'=> $obj->getLife(Content::FORMAT_ICON),
          'sagesse'=> $obj->getSagesse(Content::FORMAT_ICON),
          'strong'=> $obj->getStrong(Content::FORMAT_ICON),
          'intel'=> $obj->getIntel(Content::FORMAT_ICON),
          'agi'=> $obj->getAgi(Content::FORMAT_ICON),
          'chance'=> $obj->getChance(Content::FORMAT_ICON),
          "do_fixe_neutre" => $obj->getDo_fixe_neutre(Content::FORMAT_VIEW),
          "do_fixe_terre" => $obj->getDo_fixe_terre(Content::FORMAT_VIEW),
          "do_fixe_feu" => $obj->getDo_fixe_feu(Content::FORMAT_VIEW),
          "do_fixe_air" => $obj->getDo_fixe_air(Content::FORMAT_VIEW),
          "do_fixe_eau" => $obj->getDo_fixe_eau(Content::FORMAT_VIEW),
          "do_fixe_multiple" => $obj->getDo_fixe_multiple(Content::FORMAT_VIEW),
          'ca'=> $obj->getCa(Content::FORMAT_ICON),
          'fuite'=> $obj->getFuite(Content::FORMAT_ICON),
          'tacle'=> $obj->getTacle(Content::FORMAT_ICON),
          'dodge_pa'=> $obj->getDodge_pa(Content::FORMAT_ICON),
          'dodge_pm'=> $obj->getDodge_pm(Content::FORMAT_ICON),
          'res_neutre'=> $obj->getres_neutre(Content::FORMAT_ICON),
          'res_terre'=> $obj->getRes_terre(Content::FORMAT_ICON),
          'res_feu'=> $obj->getRes_feu(Content::FORMAT_ICON),
          'res_air'=> $obj->getRes_air(Content::FORMAT_ICON),
          'res_eau'=> $obj->getRes_eau(Content::FORMAT_ICON),
          'hostility'=> $obj->getHostility(Content::FORMAT_BADGE),
          'size'=> $obj->getSize(Content::FORMAT_BADGE),
          'location'=> $obj->getLocation(),
          'spell'=> $obj->getSpell(),
          'powerful'=> $obj->getPowerful(Content::FORMAT_ICON),
          'trait' => $obj->getTrait(Content::FORMAT_BADGE),
          'path_img' => $obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_LG])),
          'usable' => $obj->getUsable(Content::FORMAT_ICON),
          'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='mob' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
          'edit' => $edit,
          'resume' => "<div class='size-0-8 col'><div>{$obj->getPa(Content::FORMAT_ICON)}</div><div>{$obj->getPm(Content::FORMAT_ICON)}</div><div>{$obj->getPo(Content::FORMAT_ICON)}</div><div>{$obj->getIni(Content::FORMAT_ICON)}</div><div>{$obj->getTouch(Content::FORMAT_ICON)}</div></div>",
          'resumeattack' => "<div class='size-0-8 col'><div>{$obj->getVitality(Content::FORMAT_ICON)}</div><div>{$obj->getSagesse(Content::FORMAT_ICON)}</div><div>{$obj->getStrong(Content::FORMAT_ICON)}</div><div>{$obj->getIntel(Content::FORMAT_ICON)}</div><div>{$obj->getAgi(Content::FORMAT_ICON)}</div><div>{$obj->getChance(Content::FORMAT_ICON)}</div></div>",
          'resumedefend' => "<div class='size-0-8 col'><div>{$obj->getCa(Content::FORMAT_ICON)}</div><div>{$obj->getFuite(Content::FORMAT_ICON)}</div><div>{$obj->getTacle(Content::FORMAT_ICON)}</div><div>{$obj->getDodge_pa(Content::FORMAT_ICON)}</div><div>{$obj->getDodge_pm(Content::FORMAT_ICON)}</div></div>",
          'resumedom' => "<div class='size-0-8 col'><div>{$obj->getDo_fixe_neutre(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_terre(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_feu(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_air(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_eau(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_multiple(Content::FORMAT_ICON)}</div></div>",
          'resumeres' => "<div class='size-0-8 col'><div>{$obj->getRes_neutre(Content::FORMAT_ICON)}</div><div>{$obj->getRes_terre(Content::FORMAT_ICON)}</div><div>{$obj->getRes_feu(Content::FORMAT_ICON)}</div><div>{$obj->getRes_air(Content::FORMAT_ICON)}</div><div>{$obj->getRes_eau(Content::FORMAT_ICON)}</div></div>",
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
    if(!$currentUser->getRight('mob', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $manager = new MobManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            $bookmark_icon = Style::ICON_REGULAR;
            if($currentUser->in_bookmark($obj)){
                $bookmark_icon = Style::ICON_SOLID;
            }

            $edit = "";
            if($currentUser->getRight('mob', User::RIGHT_WRITE)){
              $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"Mob.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='fa-regular fa-edit'></i></a>";
            }

            $return["value"] = array(
              'id' => $obj->getId(Content::FORMAT_BADGE),
              'uniqid' => $obj->getUniqid(),
              'timestamp_add' => $obj->getTimestamp_add(Content::DATE_FR),
              'timestamp_updated' => $obj->getTimestamp_updated(Content::DATE_FR),
              'name' => "<b>". $obj->getName()."</b>",
              "description" => $obj->getDescription(),
              'level' => $obj->getLevel(),
              'vitality'=> $obj->getVitality(Content::FORMAT_ICON),
              'pa'=> $obj->getPa(Content::FORMAT_ICON),
              'pm'=> $obj->getPm(Content::FORMAT_ICON),
              'po'=> $obj->getPo(Content::FORMAT_ICON),
              'ini'=> $obj->getIni(Content::FORMAT_ICON),
              'touch'=> $obj->getTouch(Content::FORMAT_ICON),
              'life'=> $obj->getLife(Content::FORMAT_ICON),
              'sagesse'=> $obj->getSagesse(Content::FORMAT_ICON),
              'strong'=> $obj->getStrong(Content::FORMAT_ICON),
              'intel'=> $obj->getIntel(Content::FORMAT_ICON),
              'agi'=> $obj->getAgi(Content::FORMAT_ICON),
              'chance'=> $obj->getChance(Content::FORMAT_ICON),
              "do_fixe_neutre" => $obj->getDo_fixe_neutre(Content::FORMAT_VIEW),
              "do_fixe_terre" => $obj->getDo_fixe_terre(Content::FORMAT_VIEW),
              "do_fixe_feu" => $obj->getDo_fixe_feu(Content::FORMAT_VIEW),
              "do_fixe_air" => $obj->getDo_fixe_air(Content::FORMAT_VIEW),
              "do_fixe_eau" => $obj->getDo_fixe_eau(Content::FORMAT_VIEW),
              "do_fixe_multiple" => $obj->getDo_fixe_multiple(Content::FORMAT_VIEW),
              'ca'=> $obj->getCa(Content::FORMAT_ICON),
              'fuite'=> $obj->getFuite(Content::FORMAT_ICON),
              'tacle'=> $obj->getTacle(Content::FORMAT_ICON),
              'dodge_pa'=> $obj->getDodge_pa(Content::FORMAT_ICON),
              'dodge_pm'=> $obj->getDodge_pm(Content::FORMAT_ICON),
              'res_neutre'=> $obj->getres_neutre(Content::FORMAT_ICON),
              'res_terre'=> $obj->getRes_terre(Content::FORMAT_ICON),
              'res_feu'=> $obj->getRes_feu(Content::FORMAT_ICON),
              'res_air'=> $obj->getRes_air(Content::FORMAT_ICON),
              'res_eau'=> $obj->getRes_eau(Content::FORMAT_ICON),
              'hostility'=> $obj->getHostility(Content::FORMAT_BADGE),
              'size'=> $obj->getSize(Content::FORMAT_BADGE),
              'location'=> $obj->getLocation(),
              'spell'=> $obj->getSpell(),
              'powerful'=> $obj->getPowerful(Content::FORMAT_ICON),
              'trait' => $obj->getTrait(Content::FORMAT_BADGE),
              'path_img' => $obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'size' => Style::SIZE_LG])),
              'usable' => $obj->getUsable(Content::FORMAT_ICON),
              'bookmark' => "<a onclick='User.changeBookmark(this);' data-classe='mob' data-uniqid='".$obj->getUniqid()."'><i class='".$bookmark_icon." fa-bookmark text-main-d-2 text-main-hover'></i></a>",
              'edit' => $edit,
              'resume' => "<div class='size-0-8 col'><div>{$obj->getPa(Content::FORMAT_ICON)}</div><div>{$obj->getPm(Content::FORMAT_ICON)}</div><div>{$obj->getPo(Content::FORMAT_ICON)}</div><div>{$obj->getIni(Content::FORMAT_ICON)}</div><div>{$obj->getTouch(Content::FORMAT_ICON)}</div></div>",
              'resumeattack' => "<div class='size-0-8 col'><div>{$obj->getVitality(Content::FORMAT_ICON)}</div><div>{$obj->getSagesse(Content::FORMAT_ICON)}</div><div>{$obj->getStrong(Content::FORMAT_ICON)}</div><div>{$obj->getIntel(Content::FORMAT_ICON)}</div><div>{$obj->getAgi(Content::FORMAT_ICON)}</div><div>{$obj->getChance(Content::FORMAT_ICON)}</div></div>",
              'resumedefend' => "<div class='size-0-8 col'><div>{$obj->getCa(Content::FORMAT_ICON)}</div><div>{$obj->getFuite(Content::FORMAT_ICON)}</div><div>{$obj->getTacle(Content::FORMAT_ICON)}</div><div>{$obj->getDodge_pa(Content::FORMAT_ICON)}</div><div>{$obj->getDodge_pm(Content::FORMAT_ICON)}</div></div>",
              'resumedom' => "<div class='size-0-8 col'><div>{$obj->getDo_fixe_neutre(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_terre(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_feu(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_air(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_eau(Content::FORMAT_ICON)}</div><div>{$obj->getDo_fixe_multiple(Content::FORMAT_ICON)}</div></div>",
              'resumeres' => "<div class='size-0-8 col'><div>{$obj->getRes_neutre(Content::FORMAT_ICON)}</div><div>{$obj->getRes_terre(Content::FORMAT_ICON)}</div><div>{$obj->getRes_feu(Content::FORMAT_ICON)}</div><div>{$obj->getRes_air(Content::FORMAT_ICON)}</div><div>{$obj->getRes_eau(Content::FORMAT_ICON)}</div></div>",
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
  public function add(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => "",
      'uniqid' => ""
    ];
    if(!$currentUser->getRight('mob', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['name'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
        $manager = new MobManager();

        if($manager->existsName($_REQUEST['name']) == false && !empty(trim($_REQUEST['name']))){
          if(isset($_REQUEST['intel'])){$intel = $this->returnBool($_REQUEST['intel']);} else {$intel = false;}
          if(isset($_REQUEST['strong'])){$strong = $this->returnBool($_REQUEST['strong']);} else {$strong = false;}
          if(isset($_REQUEST['chance'])){$chance = $this->returnBool($_REQUEST['chance']);} else {$chance = false;}
          if(isset($_REQUEST['agi'])){$agi = $this->returnBool($_REQUEST['agi']);} else {$agi = false;}
          if(isset($_REQUEST['vitality'])){$vitality = $this->returnBool($_REQUEST['vitality']);} else {$vitality = false;}
          if(isset($_REQUEST['sagesse'])){$sagesse = $this->returnBool($_REQUEST['sagesse']);} else {$sagesse = false;}
          $object = $this->generate(
            trim($_REQUEST['level']),
            $_REQUEST['powerful'],
            $_REQUEST['name'],
            [
              "intel" => $intel,
              "strong" => $strong,
              "chance" => $chance,
              "agi" => $agi,
              "vitality" => $vitality,
              "sagesse" => $sagesse
            ]
          );
            
            if($manager->add($object)){
              $return['state'] = true;
              $return['unqiid'] = $object->getUniqid();
              $return['script'] = "Mob.open('".$object->getUniqid()."', Controller.DISPLAY_EDITABLE)";
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
  public function getJeanMob(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('mob', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['level'], $_REQUEST['name'], $_REQUEST['powerful'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
        
          if(isset($_REQUEST['intel'])){$intel = $_REQUEST['intel'];} else {$intel = false;}
          if(isset($_REQUEST['strong'])){$strong = $_REQUEST['strong'];} else {$strong = false;}
          if(isset($_REQUEST['chance'])){$chance = $_REQUEST['chance'];} else {$chance = false;}
          if(isset($_REQUEST['agi'])){$agi = $_REQUEST['agi'];} else {$agi = false;}
          if(isset($_REQUEST['vitality'])){$vitality = $_REQUEST['vitality'];} else {$vitality = false;}
          if(isset($_REQUEST['sagesse'])){$sagesse = $_REQUEST['sagesse'];} else {$sagesse = false;}

          $obj = $this->generate(
            $_REQUEST['level'],
            $_REQUEST['powerful'],
            $_REQUEST['name'],
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
          require "view/pdf/mob.php";
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
  protected function generate($level, $powerful = 4, $name = "Mob", $speficific_main = []){
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

    $intel = Creature::CARACTERISTICS['intel']['balance']['mob']['base'];
    if($is_intel){
        $intel = $this::calcExp(Creature::CARACTERISTICS['intel']['balance']['mob']['expression'], ['level' => $level]);
        $intel = round($coef * $intel);
        if($intel < Creature::CARACTERISTICS['intel']['balance']['mob']['min']){$intel = Creature::CARACTERISTICS['intel']['balance']['mob']['min'];}
    }
    $chance = Creature::CARACTERISTICS['chance']['balance']['mob']['base'];
    if($is_chance){
      $chance = $this::calcExp(Creature::CARACTERISTICS['chance']['balance']['mob']['expression'], ['level' => $level]);
      $chance = round($coef * $chance);
      if($chance < Creature::CARACTERISTICS['chance']['balance']['mob']['min']){$chance = Creature::CARACTERISTICS['chance']['balance']['mob']['min'];}
    }
    $strong = Creature::CARACTERISTICS['strong']['balance']['mob']['base'];
    if($is_strong){
        $strong = $this::calcExp(Creature::CARACTERISTICS['strong']['balance']['mob']['expression'], ['level' => $level]);
        $strong = round($coef * $strong);
        if($strong < Creature::CARACTERISTICS['strong']['balance']['mob']['min']){$strong = Creature::CARACTERISTICS['strong']['balance']['mob']['min'];}
    }
    $agi = Creature::CARACTERISTICS['agi']['balance']['mob']['base'];
    if($is_agi){
        $agi = $this::calcExp(Creature::CARACTERISTICS['agi']['balance']['mob']['expression'], ['level' => $level]);
        $agi = round($coef * $agi);
        if($agi < Creature::CARACTERISTICS['agi']['balance']['mob']['min']){$agi = Creature::CARACTERISTICS['agi']['balance']['mob']['min'];}
    }
    $vitality = Creature::CARACTERISTICS['vitality']['balance']['mob']['base'];
    if($is_vitality){
        $vitality = $this::calcExp(Creature::CARACTERISTICS['vitality']['balance']['mob']['expression'], ['level' => $level]);
        $vitality = round($coef * $vitality);
        if($vitality < Creature::CARACTERISTICS['vitality']['balance']['mob']['min']){$vitality = Creature::CARACTERISTICS['vitality']['balance']['mob']['min'];}
    }
    $sagesse = Creature::CARACTERISTICS['sagesse']['balance']['mob']['base'];
    if($is_sagesse){
        $sagesse = $this::calcExp(Creature::CARACTERISTICS['sagesse']['balance']['mob']['expression'], ['level' => $level]);
        $sagesse = round($coef * $sagesse);
        if($sagesse < Creature::CARACTERISTICS['sagesse']['balance']['mob']['min']){$sagesse = Creature::CARACTERISTICS['sagesse']['balance']['mob']['min'];}
    }

    $life =  $this::calcExp(Creature::CARACTERISTICS['life']['balance']['mob']['expression'], ['level' => $level]);
    $random = (int) rand(round(-0.1 * $life), round(0.1 * $life));
    $life = (int) round( (1+($coef-1)*1.7) * $life + $random);
    $life += $vitality * $level;
    if($life < Creature::CARACTERISTICS['life']['balance']['mob']['min']){$life = Creature::CARACTERISTICS['life']['balance']['mob']['min'];}

    $pa = $this::calcExp(Creature::CARACTERISTICS['pa']['balance']['mob']['expression'], ['level' => $level]);
    $pa = round( (1+($coef-1)*0.8) * $pa);
    if($pa < Creature::CARACTERISTICS['pa']['balance']['mob']['min']){$pa = Creature::CARACTERISTICS['pa']['balance']['mob']['min'];}

    $pm = $this::calcExp(Creature::CARACTERISTICS['pm']['balance']['mob']['expression'], ['level' => $level]);
    $pm = round( (1+($coef-1)*0.8) * $pm);
    if($pm < Creature::CARACTERISTICS['pm']['balance']['mob']['min']){$pm = Creature::CARACTERISTICS['pm']['balance']['mob']['min'];}

    $po = $this::calcExp(Creature::CARACTERISTICS['po']['balance']['mob']['expression'], ['level' => $level]);
    $po = round( (1+($coef-1)*0.8) * $po);
    if($po < Creature::CARACTERISTICS['po']['balance']['mob']['min']){$po = Creature::CARACTERISTICS['po']['balance']['mob']['min'];}

    $ini = $this::calcExp(Creature::CARACTERISTICS['ini']['balance']['mob']['expression'], ['level' => $level]);
    $ini = $ini / 2;
    if($is_intel){
      if($this->returnBool($speficific_main['intel'])){
        $ini += $intel;
        if($ini > Creature::CARACTERISTICS['ini']['balance']['mob']['max']){$ini = Creature::CARACTERISTICS['ini']['balance']['mob']['max'];}
      }
    }
    $ini = round($coef * $ini);
    if($ini < Creature::CARACTERISTICS['ini']['balance']['mob']['min']){$ini = Creature::CARACTERISTICS['ini']['balance']['mob']['min'];}

    $touch = $this::calcExp(Creature::CARACTERISTICS['touch']['balance']['mob']['expression'], ['level' => $level]);

    $res_neutre = $this::calcExp(Creature::CARACTERISTICS['res_neutre']['balance']['mob']['expression'], ['level' => $level]);
    $res_neutre = round( (1+($coef-1)*0.8) * $res_neutre);
    if($res_neutre < Creature::CARACTERISTICS['res_neutre']['balance']['mob']['min']){$res_neutre = Creature::CARACTERISTICS['res_neutre']['balance']['mob']['min'];}
    if($res_neutre > Creature::CARACTERISTICS['res_neutre']['balance']['mob']['max']){$res_neutre = Creature::CARACTERISTICS['res_neutre']['balance']['mob']['max'];}

    $res_terre = $this::calcExp(Creature::CARACTERISTICS['res_terre']['balance']['mob']['expression'], ['level' => $level]);
    $res_terre = round( (1+($coef-1)*0.8) * $res_terre);
    if($res_terre < Creature::CARACTERISTICS['res_terre']['balance']['mob']['min']){$res_terre = Creature::CARACTERISTICS['res_terre']['balance']['mob']['min'];}
    if($res_terre > Creature::CARACTERISTICS['res_terre']['balance']['mob']['max']){$res_terre = Creature::CARACTERISTICS['res_terre']['balance']['mob']['max'];}

    $res_feu = $this::calcExp(Creature::CARACTERISTICS['res_feu']['balance']['mob']['expression'], ['level' => $level]);
    $res_feu = round( (1+($coef-1)*0.8) * $res_feu);
    if($res_feu < Creature::CARACTERISTICS['res_feu']['balance']['mob']['min']){$res_feu = Creature::CARACTERISTICS['res_feu']['balance']['mob']['min'];}
    if($res_feu > Creature::CARACTERISTICS['res_feu']['balance']['mob']['max']){$res_feu = Creature::CARACTERISTICS['res_feu']['balance']['mob']['max'];}

    $res_air = $this::calcExp(Creature::CARACTERISTICS['res_air']['balance']['mob']['expression'], ['level' => $level]);
    $res_air = round( (1+($coef-1)*0.8) * $res_air);
    if($res_air < Creature::CARACTERISTICS['res_air']['balance']['mob']['min']){$res_air = Creature::CARACTERISTICS['res_air']['balance']['mob']['min'];}
    if($res_air > Creature::CARACTERISTICS['res_air']['balance']['mob']['max']){$res_air = Creature::CARACTERISTICS['res_air']['balance']['mob']['max'];}

    $res_eau = $this::calcExp(Creature::CARACTERISTICS['res_eau']['balance']['mob']['expression'], ['level' => $level]);
    $res_eau = round( (1+($coef-1)*0.8) * $res_eau);
    if($res_eau < Creature::CARACTERISTICS['res_eau']['balance']['mob']['min']){$res_eau = Creature::CARACTERISTICS['res_eau']['balance']['mob']['min'];}
    if($res_eau > Creature::CARACTERISTICS['res_eau']['balance']['mob']['max']){$res_eau = Creature::CARACTERISTICS['res_eau']['balance']['mob']['max'];}


    $tacle = $this::calcExp(Creature::CARACTERISTICS['tacle']['balance']['mob']['expression'], ['level' => $level]);
    $tacle = $tacle / 2;
    $fuite = $tacle;
    if($is_chance){
        $tacle += $chance;
        if($tacle > Creature::CARACTERISTICS['tacle']['balance']['mob']['max']){$tacle = Creature::CARACTERISTICS['tacle']['balance']['mob']['max'];}
    }
    $tacle = round( (1+($coef-1)*0.8) * $tacle);
    if($tacle < Creature::CARACTERISTICS['tacle']['balance']['mob']['min']){$tacle = Creature::CARACTERISTICS['tacle']['balance']['mob']['min'];}

    if($is_agi){
        $fuite += $agi;
        if($fuite > Creature::CARACTERISTICS['fuite']['balance']['mob']['max']){$fuite = Creature::CARACTERISTICS['fuite']['balance']['mob']['max'];}
    }
    $fuite = round( (1+($coef-1)*0.8) * $fuite);
    if($fuite < Creature::CARACTERISTICS['fuite']['balance']['mob']['min']){$fuite = Creature::CARACTERISTICS['fuite']['balance']['mob']['min'];}

    $ca = $this::calcExp(Creature::CARACTERISTICS['ca']['balance']['mob']['expression'], ['level' => $level]);
    $ca -= 0.4*$level*$ca/24;
    if($is_vitality){
        $ca += $vitality;
        if($ca > Creature::CARACTERISTICS['ca']['balance']['mob']['max']){$ca = Creature::CARACTERISTICS['ca']['balance']['mob']['max'];}
    }
    $ca = round( (1+($coef-1)*0.8) * $ca);
    if($ca < Creature::CARACTERISTICS['ca']['balance']['mob']['min']){$ca = Creature::CARACTERISTICS['ca']['balance']['mob']['min'];}

    $dodge_pa = $this::calcExp(Creature::CARACTERISTICS['dodge_pa']['balance']['mob']['expression'], ['level' => $level]);
    $dodge_pa -= 0.4*$level*$dodge_pa/24;
    if($is_sagesse){
      $dodge_pa += $sagesse;
      if($dodge_pa > Creature::CARACTERISTICS['dodge_pa']['balance']['mob']['max']){$dodge_pa = Creature::CARACTERISTICS['tacle']['balance']['mob']['max'];}
    }
    $dodge_pa = round( (1+($coef-1)*0.8) * $dodge_pa);
    if($dodge_pa < Creature::CARACTERISTICS['dodge_pa']['balance']['mob']['min']){$dodge_pa = Creature::CARACTERISTICS['dodge_pa']['balance']['mob']['min'];}

    $dodge_pm = $this::calcExp(Creature::CARACTERISTICS['dodge_pm']['balance']['mob']['expression'], ['level' => $level]);
    $dodge_pm -= 0.4*$level*$dodge_pm/24;
    if($is_sagesse){
      $dodge_pm += $sagesse;
      if($dodge_pm > Creature::CARACTERISTICS['dodge_pm']['balance']['mob']['max']){$dodge_pm = Creature::CARACTERISTICS['tacle']['balance']['mob']['max'];}
    }
    $dodge_pm = round( (1+($coef-1)*0.8) * $dodge_pm);
    if($dodge_pm < Creature::CARACTERISTICS['dodge_pm']['balance']['mob']['min']){$dodge_pm = Creature::CARACTERISTICS['dodge_pm']['balance']['mob']['min'];}

    $obj = new Mob(array(
      'name'=>$name,
      'level'=>round($level),
      'life'=>round($life),
      'pa'=> round($pa),
      'pm'=> round($pm),
      'po'=> round($po),
      'ini'=> round($ini),
      'touch'=> round($touch),
      'ca'=> round($ca),
      'dodge_pa'=>round($dodge_pa),
      'dodge_pm'=>round($dodge_pm),
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
      'res_eau'=>round($res_eau)
    ));
    $obj->setHostility(Mob::HOSTILITY['neutre']);
    $obj->setUniqid(uniqid());
    $obj->setTimestamp_add();
    $obj->setTimestamp_updated();
    return $obj;
  }
  public function getSpellList(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];

    if(!$currentUser->getRight('mob', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour modifier cet objet";} else {
      
      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          // Récupération des objets
            $manager = new MobManager();

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
    if(!$currentUser->getRight('mob', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {

        $array = [];
        $manager = new MobManager;
        $objects = $manager->search($term, $limit,$only_usable);

        if(!empty($objects)){
            $array = array();
            foreach ($objects as $object) {
                $click_action = "";
                switch ($action) {
                  case ControllerModule::SEARCH_DONE_ADD_TO_BOOKMARK:
                    $click_action = "onclick=\"User.changeBookmark(this);\" data-classe=\"".strtolower(get_class($object))."\" data-uniqid=\"".$object->getUniqid()."\"";
                  break;
                  case ControllerModule::SEARCH_DONE_ADD_MOB_TO_SPELL:
                    $click_action = "onclick=\"Spell.update('".$parameter."',{action:'add', uniqid:'".$object->getUniqid()."'},'mob', IS_VALUE);\"";
                  break;
                  default:
                    $click_action = "onclick=\"Mob.open('".$object->getUniqid()."')\"";
                  break;
                }

                $name = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getName());
                ob_start();   ?>
                  <a <?=$click_action?> class="d-flex justify-content-between align-items-baseline flex-nowrap">
                    <div class="d-flex justify-content-start align-item-baseline">
                      <div class="img-back-20 me-2" style="background-image:url(<?=$object->getFile('logo', new Style(['format' => Content::FORMAT_BRUT]))?>)"></div>
                      <?=$name?>
                    </div>
                    <p><small class='size-0-6 badge back-red-l-1 mx-2'>Créature</small></p>
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