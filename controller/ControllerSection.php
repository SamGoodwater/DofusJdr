<?php
class ControllerSection extends Controller{

  // -------- GETTERS ET SETTERS ----------
  public function add(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('section', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(isset($_REQUEST['title'], $_REQUEST['url_name'], $_REQUEST['type'])){
        $manager = new SectionManager();
        $managerP = new PageManager;
        
        if($managerP->existsUrl_name($_REQUEST['url_name'])){
          $page = $managerP->getFromUrl_name($_REQUEST['url_name']);

          $option = ""; if(isset($_REQUEST['option'])){$option = $_REQUEST['option'];}

          $obj = new Section([
            "title" => trim($_REQUEST['title']),
            "type" => $_REQUEST['type'],
            'uniqid' => uniqid(),
            'uniqid_page' => $page->getUniqid(),
            "content" => $option
          ]);
          $order = $manager->getLastOrder_numFromUniqid_page($page->getUniqid()) + 1;
          $obj->setOrder_num($order);
          $obj->setTimestamp_add();
          $obj->setTimestamp_updated();
          $return['state'] = true;
          $manager->add($obj);
        }else {
          $return['error'] = "Impossible de récupérer la référence de la page.";
        }  
          
      } else {
        $return['error'] = "Les données ne sont pas complètes.";
      }

    }
    
    echo json_encode($return);
    flush();
  }
  public function update(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('section', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      $manager = new SectionManager(); // A modifier

      if($manager->existsUniqid($_REQUEST['uniqid'])){

        $obj = $manager->getFromUniqid($_REQUEST['uniqid']);
          $method = "set".ucfirst($_REQUEST['type']);

          if(method_exists($obj,$method)){
              $result = $obj->$method($_REQUEST['value']);
              if($result){
                $obj->setTimestamp_updated(time());
                $manager->update($obj);
                $return['state'] = true;
              } else {
                $return['error'] = $result;
              }

          } else {
            $return['error'] = "Aucun type correspondant dans l'objet";
          }
      }

    }

    echo json_encode($return);
    flush();
  }
  public function upload(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight('section', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

        if(isset($_REQUEST['uniqid'], $_FILES['file'])){
          $managerS = new SectionManager();
          $managerP = new PageManager;

          $tile = ""; if(isset($_REQUEST["title"])){$tile = trim($_REQUEST["title"]);}

            if($managerP->existsUniqid($_REQUEST['uniqid'])){
              $page = $managerP->getFromUniqid($_REQUEST['uniqid']);

              if($managerP->existsUniqid($_REQUEST['uniqid'])){
                $page = $managerP->getFromUniqid($_REQUEST['uniqid']);

                $obj = new Section([
                  "title" => $tile,
                  "type" => "view_file.php",
                  'uniqid' => uniqid(),
                  'uniqid_page' => $page->getUniqid(),
                  "content" => null
                ]);
                $order = $managerS->getLastOrder_numFromUniqid_page($page->getUniqid()) + 1;
                $obj->setOrder_num($order);
                $obj->setTimestamp_add();
                $obj->setTimestamp_updated();
                $return['state'] = true;

                  $dirname = FileManager::formatPath(Page::PATH_FILE, true, true).$obj->getUniqid();

                  $fileManager = new FileManager(array(
                    "dirname" => $dirname,
                    "file" => $_FILES["file"],
                    "name" => $_FILES["file"]["name"],
                  ));
                  $fileManager->setFormatallowed(FileManager::getListeExtention(FileManager::FORMAT_IMG, FileManager::FORMAT_AUDIO, FileManager::FORMAT_VIDEO, FileManager::FORMAT_PDF, FileManager::FORMAT_DOCUMENT, FileManager::FORMAT_TABLEUR, FileManager::FORMAT_SLIDER));
                  
                  $result = $fileManager->upload();

                  if($result["state"]){

                    $obj->setContent($result["path"]);
                    if($managerS->add($obj)){
                      $return['state'] = true;
                      $return['script'] = "Page.show('".$page->getUrl_name()."');";
                      $return['value'] = $result["path"];

                    } else {
                      $return['error'] = "Impossible de créer la section";
                    }
                  } else {
                    $return['error'] = $result['error'];
                  }

              }else {
                $return['error'] = "Impossible de récupérer la référence de la page.";
              }  
            } else {
              $return['error'] = "La page n'a pas été trouvé";
            }

        } else {
          $return['error'] = "Pas de fichier envoyé";
        }
    }
    
    echo json_encode($return);
    flush();
  }
  public function remove(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('section', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          // Récupération des objets
            $manager = new SectionManager();

          // Récupération de l'objet
            if($manager->existsUniqid($_REQUEST['uniqid'])){

              $item = $manager->getFromUniqid($_REQUEST['uniqid']);
              $page = $item->getUniqid_page(Content::FORMAT_OBJECT);
              $manager->delete($item);
              $return['state'] = true;
              $return['script'] = "Page.show('".$page->getUrl_name()."');";

            } else {
              $return['error'] = 'Cette section n\'existe pas.';
            }
      }

    }

    echo json_encode($return);
    flush();
  }

  public function getVisual(){
    $return = [
        "size" => "xl",
        "title" => "",
        "header" => "",
        "modal" => "",
        "script" => ""
    ];

        $user = ControllerConnect::getCurrentUser();
        $manager = new PageManager;
        
        if($user->getRight('section', User::RIGHT_WRITE)){

          if(isset($_REQUEST['uniqid'])){

            if($manager->existsUniqid($_REQUEST['uniqid'])){
              $page = $manager->getFromUniqid($_REQUEST['uniqid']);

              $return["modal"] = $page->getModal();
              $return["size"] = "xl";
              $return["title"] = "Ajouter une section à la page : ".$page->getName();

            } else {
              $return["title"] = "Cette page n'existe pas.";
              $return['script'] = " MsgAlert(\"Cette page n'existe pas.\", '', 'red' , 6000);";
            }
          } else {
            $return["title"] = "Impossible de récupérer la référence de la page.";
            $return['script'] = " Impossible de récupérer la référence de la page.\", '', 'red' , 6000);";
          }
        } else {
          $return["title"] = "Vous n'avez pas les droits pour écrire une section.";
          $return['script'] = " MsgAlert(\"Vous n'avez pas les droits pour écrire une section.\", '', 'red' , 6000);";
        }
              
        echo json_encode($return);
        flush();
  }

  public function search($term, $action = ControllerModule::SEARCH_DONE_REDIRECT, $parameter = "", $limit = null, $only_usable = false){
    $currentUser = ControllerConnect::getCurrentUser();

    if(!$currentUser->getRight('section', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {

        $array = [];
        $manager = new SectionManager;

        switch ($action) {
          default:
            $sections = $manager->search($term, $limit,$only_usable);
          break;
        }
        if(!empty($sections)){
            $array = array();
            foreach ($sections AS $object) {
                $click_action = "";
                switch ($action) {
                  default:
                    $click_action = "onclick=\"Page.show('".$object->getUniqid_page(Content::FORMAT_OBJECT)->getUrl_name()."')\";";
                  break;
                }
                
                preg_match_all("/(.{0,50}".$term.".{0,50})/i", strip_tags($object->getContent()), $content, PREG_SET_ORDER);
                if(isset($content[0][0])){$content = "...".$content[0][0] . "...";}else{$content = "";}
                $content = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $content);
                $title = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getTitle());
                ob_start(); ?>
                  <a <?=$click_action?> class="item-search">
                    <div class="d-flex justify-content-between align-items-baseline flex-nowrap">
                      <p><?=$title?></p>
                      <p><small class='size-0-6 badge back-grey-l-1 mx-2'>Paragraphe</small></p>
                    </div>
                    <p class="size-0-8 text-grey"><?=$content?></p>
                  </a>
                <?php $visual = ob_get_clean();
                $array[] = [
                  'error' => false,
                  'visual' => $visual,
                  'label' => $object->getTitle()
                ];
            }
        }
    }
    return $array;
  }
}