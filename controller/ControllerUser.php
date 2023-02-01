<?php
class ControllerUser extends Controller{

  public function countAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'success',
      'value' => "",
      'error' => 'erreur inconnue',
    ];
    if(!$currentUser->getRight('user', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{
      $manager = new UserManager();
      $return["value"] = $manager->countAll();
    }
    echo json_encode($return);
    flush();
  }
  public function getAll(){
    $currentUser = ControllerConnect::getCurrentUser();
    $json = array();  
    if(!$currentUser->getRight('user', User::RIGHT_READ)){
      $json = "Vous n'avez pas les droits pour lire cet objet";}else{

      $managerS = new UserManager();
      $objects = $managerS->getAll();

      foreach ($objects AS $object) {
        $json[] = array(
          'id' => $object->getId(Content::FORMAT_BADGE),
          "uniqid" => $object->getUniqid(),
          "timestamp_add" => $object->getTimestamp_add(),
          "last_connexion" => $object->getLast_connexion(),
          "pseudo" => $object->getPseudo(),
          "email" => $object->getEmail(),
          "rights" => $object->getRights(Content::FORMAT_BADGE),
          'edit' => "<a class='text-main-d-2 text-main-l-3-hover' onclick=\"User.open('{$object->getUniqid()}')\"><i class='far fa-edit'></i></a>",
          'detailView' => $object->getVisual(Content::FORMAT_CARD)
        );
      }

    }

    echo json_encode($json);
    flush();
  }
  public function getArrayFromUniqid(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'echec',
      'value' => [],
      'error' => 'erreur inconnue'
    ];
    if(!$currentUser->getRight('user', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $manager = new UserManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $object = $manager->getFromUniqid($_REQUEST['uniqid']);

            $return["value"] = array(
              'id' => $object->getId(Content::FORMAT_BADGE),
              "uniqid" => $object->getUniqid(),
              "timestamp_add" => $object->getTimestamp_add(),
              "last_connexion" => $object->getLast_connexion(),
              "pseudo" => $object->getPseudo(),
              "email" => $object->getEmail(),
              "rights" => $object->getRights(Content::FORMAT_BADGE),
              'edit' => "<a class='text-main-d-2 text-main-l-3-hover' onclick=\"User.open('{$object->getUniqid()}')\"><i class='far fa-edit'></i></a>",
              'detailView' => $object->getVisual(Content::FORMAT_CARD)
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
    if(!$currentUser->getRight('user', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $managerS = new UserManager();

        // Récupération de l'objet
          if($managerS->existsUniqid($_REQUEST['uniqid'])){

            $obj = $managerS->getFromUniqid($_REQUEST['uniqid']);
            $return['value'] = array(
              'visual' => $obj->getVisual(Content::FORMAT_MODIFY),
              "title" => $obj->getPseudo(Content::FORMAT_MODIFY)
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
  
  public function add(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'return' => 'echec',
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    if(!$currentUser->getRight('user', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['email'], $_REQUEST['password'], $_REQUEST['password_repeat'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        if($_REQUEST['password'] != $_REQUEST['password_repeat']){
          $return['error'] = 'Les deux mots de passe doivent être identiques.';
        } else {

            $manager = new UserManager();

            if($manager->existsEmail($_REQUEST['email'])){
              $return['error'] = "L'email est déjà utilisé pour un aure compte.";
            } else {
              $object = new User(array(
                'email' => $_REQUEST['email']
              ));
              $object->setPassword($_REQUEST["password"]);
              $object->setUniqid(uniqid());
              $object->setToken(uniqid().time());
              $object->setTimestamp_add();
              $object->setLast_connexion();

              if($manager->add($object)){
                $return['return'] = "success";
                $return['script'] = "User.open('".$object->getUniqid()."')";
              }else {
                $return['error'] = 'Impossible d\'ajouter l\'objet';
              }
            }
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

      if(!isset($_REQUEST['uniqid'], $_REQUEST['type'], $_REQUEST['value'])){
        $return['error'] = 'Impossible de récupérer les données';

      } else {

            $manager = new UserManager(); // A modifier

            if($manager->existsUniqid($_REQUEST['uniqid'])){

              $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

              if(!$currentUser->getRight('user', User::RIGHT_WRITE) && $obj->getUniqid() != $currentUser->getFromUniqid()){
                $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

                $method = "set".ucfirst($_REQUEST['type']);

                if(method_exists($obj,$method)){
                    $result = $obj->$method($_REQUEST['value']);
                    if($result == "success"){
                      $obj->setLast_connexion(time());
                      $manager->update($obj);
                      if(ControllerConnect::isConnect()){
                        $user = ControllerConnect::getCurrentUser();
                        if($user->getUniqid() == $obj->getUniqid()){
                          ControllerConnect::setCurrentUser($obj);
                        }
                      }
                      $return['return'] = "success";
                    } else {
                      $return['error'] = $result;
                    }

                } else {
                  $return['error'] = "Aucun type correspondant dans l'objet";
                }

              }

            } else {
              $return['error'] = 'Impossible de récupérer l\'objet.';
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
    if(!$currentUser->getRight('user', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

          // Récupération des objets
            $manager = new UserManager();

          // Récupération de l'objet
            if($manager->existsUniqid($_REQUEST['uniqid'])){

              $obj = $manager->getFromUniqid($_REQUEST['uniqid']);
              $manager->remove($obj);
              $return['return'] = "success";

            } else {
              $return['error'] = 'Cet objet n\'existe pas.';
            }
      }

    }

    echo json_encode($return);
    flush();
  }

  public function getBookmark(){
    $return = [
        "placement" => "start",
        "visual" => ""
    ];

    $user = ControllerConnect::getCurrentUser();
    $bookmarks = $user->getBookmark();

    if(!empty($bookmarks)){

        ob_start(); ?>
          <?=$user->getBookmark(Content::FORMAT_CARD);?>
        <?php $return["visual"] = ob_get_clean();

    } else {
        ob_start(); ?>
          <p>Aucun favoris dans le grimoire.</p>
          <p>Parcourez le site pour en rajouter</p>
        <?php $return["visual"] = ob_get_clean();
    }
              
    echo json_encode($return);
    flush();
  }

  public function addBookmark(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => "",
      "cookie" => []
    ];

      if(!isset($_REQUEST['uniqid'], $_REQUEST['classe'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
          $managerName = ucfirst(strtolower($_REQUEST["classe"])) . "Manager";
          if(class_exists($managerName)){
              $managerC = new $managerName();
              if(method_exists($managerC, 'existsUniqid')){
                  if($managerC->existsUniqid($_REQUEST['uniqid'])){
                      $obj = $managerC->getFromUniqid($_REQUEST['uniqid']);
                  }
              }
          }

          if(!isset($obj)){            
            $return['error'] = "La référence n'existe pas.";
          } else {
            if(!is_object($obj)){            
              $return['error'] = "La référence n'existe pas.";
            } else {
              $result = $currentUser->setBookmark($obj, "add");
              if($result){
                $return['cookie'] = $this->getCookieBookmark($currentUser);
                $return['state'] = true;
                ControllerConnect::setCurrentUser($currentUser);
              } else {
                $return['error'] = $result;
              }
            }
          }
      }

    echo json_encode($return);
    flush();
  }
  public function removeBookmark(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => "",
      "cookie" => []
    ];

      if(!isset($_REQUEST['uniqid'], $_REQUEST['classe'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
          $manager = new UserManager();
          $managerName = ucfirst(strtolower($_REQUEST["classe"])) . "Manager";
          if(class_exists($managerName)){
              $managerC = new $managerName();
              if(method_exists($managerC, 'existsUniqid')){
                  if($managerC->existsUniqid($_REQUEST['uniqid'])){
                      $obj = $managerC->getFromUniqid($_REQUEST['uniqid']);
                  }
              }
          }

          if(!isset($obj)){            
            $return['error'] = "La référence n'existe pas.";
          } else {
            if(!$manager->existsBookmark($currentUser, $obj)){
              $return['error'] = "Le favoris n'existe pas.";
            } else {
              $result = $currentUser->setBookmark($obj, "remove");
              if($result){
                $return['cookie'] = $this->getCookieBookmark($currentUser);
                $return['state'] = true;
                ControllerConnect::setCurrentUser($currentUser);
              } else {
                $return['error'] = $result;
              }
            }
          }
      }

    echo json_encode($return);
    flush();
  }

  public function setBookmark(){
    $currentUser = ControllerConnect::getCurrentUser();
    $manager = new UserManager();
    if(isset($_COOKIE["bookmark"])){
      $bookmarks_cookie = unserialize($_COOKIE["bookmark"]);
    } else {
      $bookmarks_cookie = [];
    }
    $bookmarks_db = $manager->getBookmarkFromUser($currentUser);
    if(!is_array($bookmarks_db)){$bookmarks_db = [];}
    $bookmarks_user = $currentUser->getBookmark();
    if(!is_array($bookmarks_user)){$bookmarks_user = [];}

    $bookmarks = array_merge($bookmarks_db, $bookmarks_user, $bookmarks_cookie);
    $new_bookmark = [];
    foreach ($bookmarks as $key => $bookmark) {
      if(is_object($bookmark)){
        $new_bookmark[get_class($bookmark) ."-".$bookmark->getUniqid()] = $bookmark;

      } elseif(is_array($bookmark)) {
        if(isset($bookmark["classe"])){
          $classe = $bookmark["classe"];
          if(isset($bookmark["uniqid"])){
            $uniqid = $bookmark["uniqid"];
            $managerName = ucfirst(strtolower($classe)) . "Manager";
            if(class_exists($managerName)){
              $managerC = new $managerName();
              if(method_exists($managerC, 'existsUniqid')){
                if($managerC->existsUniqid($uniqid)){
                    $obj = $managerC->getFromUniqid($uniqid);
                    $new_bookmark[$classe ."-".$uniqid] = $obj;
                }
              }
            }
          }
        }

      }elseif(preg_match('/^([a-z]{2,})-(\K\w{13}\b)$/i', $key, $matches)){
        $classe = $matches[1];
        $uniqid = $matches[2];
        $managerName = ucfirst(strtolower($classe)) . "Manager";
        if(class_exists($managerName)){
          $managerC = new $managerName();
          if(method_exists($managerC, 'existsUniqid')){
            if($managerC->existsUniqid($uniqid)){
                $obj = $managerC->getFromUniqid($uniqid);
                $new_bookmark[$classe ."-".$uniqid] = $obj;
            }
          }
        }

      }
    }
    $currentUser->setBookmarkArray($new_bookmark);
    ControllerConnect::setCurrentUser($currentUser);
  }

  public function getCookieBookmark(User $user){
    $serial = ""; $date = "";
    $bookmark_minified = $user->getBookmark(Content::FORMAT_TEXT);
    if(!empty($bookmark_minified) && $user->getCookie(User::COOKIE_GRIMOIRE)){
      $serial = urlencode(serialize($bookmark_minified));
      if(mb_strlen($serial) > 4096){
        $serial = "";
      }
    }
    $time = time() + (60 * 60 * 24 * 7 * 4 * 6); // 6 mois
    $date = gmdate("D, d M Y H:i:s",$time);
    return [
      "date" => $date,
      "serial" => $serial
    ];
  }

  public function search($term, $action = ControllerSearch::SEARCH_DONE_REDIRECT, $parameter = "", $limit = null, $only_usable = false){
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight('user', User::RIGHT_READ)){
      $array = [
        'error' => true,
        'visual' =>"Vous n'avez pas les droits pour faire cette recherche.",
        'label' => "Erreur"
      ]; } else {

        $array = [];
        $manager = new UserManager;
        $objects = $manager->search($term, $limit,$only_usable);

        if(!empty($objects)){
            $array = array();
            foreach ($objects as $object) {
                $click_action = "";
                switch ($action) {
                  default:
                    $click_action = "onclick=\"User.open('".$object->getUniqid()."')\"";
                  break;
                }

                $pseudo = str_ireplace($term,"<span class='back-secondary-l-4'>".$term."</span>", $object->getPseudo());
                ob_start();   ?>
                  <a <?=$click_action?> class="d-flex justify-content-between align-items-baseline flex-nowrap">
                    <?=$pseudo?>
                    <p><small class='size-0-6 badge back-brown-l-1 mx-2'>Utilisateur·trice</small></p>
                  </a>
                <?php $visual = ob_get_clean();

                $array[] = [
                  'error' => false,
                  'visual' =>$visual,
                  'label' => $object->getPseudo()
                ];
            }
        }
    }    
    return $array;
  }
}
 