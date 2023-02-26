<?php
class ControllerPage extends Controller{

  // -------- AFFICHAGE ----------

  public function show(){
    $return = [
      'state' => false,
      'html' => "",
      'title' => "",
      'modal_title' => "",
      'modal_html' => "",
      "size" => Controller::SIZE_LG,
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    $currentUser = ControllerConnect::getCurrentUser();

    if(isset($_REQUEST["url_name"])){
      $url_name = $_REQUEST["url_name"];
      $settings = "";
      if(isset($_REQUEST['settings'])){
        $settings = $_REQUEST['settings'];
      }
      $manager = new PageManager;
      if($manager->existsUrl_name($url_name)){

        $page = $manager->getFromUrl_name($url_name);
        if($page->getPublic() || $currentUser->getRight('page', User::RIGHT_READ)){
            $title = $page->getName();
            $content = $page->getVisual(Content::FORMAT_EDITABLE);
            $modal = $page->getModal();
            $return["modal_html"] = $modal['html'];
            $return["modal_title"] = $modal['title'];
        } else {
          $template_vars = [
            'get' => Section::GET_SECTION_CONTENT,
            'content' => "",
            'uniqid' => "",
            "uniqid_page" => ""
          ];
          include_once "view/sections/home.php";
          $content = $template["content"];
          $title = $template["title"];
          new AlertManager(new Alert("Impossible d'accéder à la page","Vous n'avez pas les droits.","",Alert::ALERT_DANGER,6000));
        }

      } else {
        $template_vars = [
          'get' => Section::GET_SECTION_CONTENT,
          'content' => "",
          'uniqid' => "",
          "uniqid_page" => ""
        ];
        include_once "view/sections/home.php";
        $content = $template["content"];
        $title = $template["title"];
      }
      $return["html"] = $content;
      $return["title"] = $title;
      $return["state"] = true;
    } else {
      $return["error"] = "Données manquantes.";
    }

    echo json_encode($return);
    flush();
  }

  // -------- GETTERS ET SETTERS ----------
  public function add(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => "",
      "link" => ""
    ];
    if($currentUser->getRight('page', User::RIGHT_WRITE)){

      if(isset($_REQUEST['name'], $_REQUEST['category'])){

        $is_dropdown = false;
        if(isset($_REQUEST["is_dropdown"])){
          $is_dropdown = $this->returnBool($_REQUEST["is_dropdown"]);
        }
        $public = false;
        if(isset($_REQUEST["public"])){
          $public = $this->returnBool($_REQUEST["public"]);
        }
        $is_editable = true;
        if(isset($_REQUEST["is_editable"])){
          $is_editable = $this->returnBool($_REQUEST["is_editable"]);
        }

        $manager = new PageManager();
        $order = $manager->getLastOrder_numFromCategory($_REQUEST["category"]) + 1;

        $obj = new Page([
          "name" => trim($_REQUEST['name']),
          "category" => $_REQUEST["category"],
          'uniqid' => uniqid(),
          "order_num" => $order,
          "is_dropdown" => $is_dropdown,
          'public' => $public,
          'is_editable' => $is_editable
        ]);
        $obj->setUrl_name();
        $obj->setTimestamp_add();
        $obj->setTimestamp_updated();
        $manager->add($obj);
          
        $return['link'] = $obj->getUrl_name();
        $return['state'] = true;
      } else {
        $return['error'] = "Les données ne sont pas complètes.";
      }

    } else {
      $return['error'] = "Impossible d'écrire l'objet";
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
    if($currentUser->getRight('page', User::RIGHT_WRITE)){

      $manager = new PageManager();

      if($manager->existsUniqid($_REQUEST['uniqid'])){

        $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

        if(!in_array($obj->getUniqid(), Page::UNIQID_NO_EDIT)){
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

        } else {
          $return['error'] = "Cette page ne peut pas être modifier.";
        }

      }
    } else {
      $return['error'] = "Impossible d'écrire l'objet";
    }

    echo json_encode($return);
    flush();
  }
  public function updateorder(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if($currentUser->getRight('page', User::RIGHT_WRITE)){

      if(isset($_REQUEST['arr'])){
       

        if(is_array($_REQUEST["arr"])){

          $manager = new PageManager();
          foreach ($_REQUEST['arr'] as $uniqid => $order_num) {
            if($manager->existsUniqid($uniqid)){
              $page = $manager->getFromUniqid($uniqid);
              if(!in_array($page->getUniqid(), Page::UNIQID_NO_EDIT)){
                if(is_numeric($order_num)){
                  $page->setOrder_num(intval($order_num));
                  $page->setTimestamp_updated(time());
                  $manager->update($page);
                }
              }
            }
          }
          $return['script'] = "location.reload();";
          $return['state'] = true;

        } else {
          $return['error'] = "Les données envoyés ne sont pas valides.";
        }

      } else {
        $return['error'] = "Les données envoyés ne sont pas valides.";
      }

    } else {
      $return['error'] = "Impossible d'écrire l'objet";
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
    if($currentUser->getRight('page', User::RIGHT_WRITE)){

      if(!isset($_REQUEST['uniqid']))
      {
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        // Récupération des objets
          $manager = new PageManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){

            $item = $manager->getFromUniqid($_REQUEST['uniqid']);
            $manager->delete($item);
            $return['state'] = true;

          } else {
            $return['error'] = 'Cette page n\'existe pas.';
          }
      }

    } else {
      $return['error'] = "Impossible d'écrire l'objet";
    }

    echo json_encode($return);
    flush();
  }
}
 