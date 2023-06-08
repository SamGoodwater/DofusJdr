<?php
class ControllerUser extends Controller{

  public function count(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue'
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    
    if(!$currentUser->getRight('user', User::RIGHT_READ)){
      $return["error"] = "Vous n'avez pas les droits pour lire cet objet";}else{

      $manager = new UserManager();
      $return['value'] = $manager->countAll();
      $return['state'] = true;
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
        offset:$offset,
        limit:$limit
      );

      foreach ($objs AS $obj) {
        $edit = "";
        if($currentUser->getRight('user', User::RIGHT_WRITE) && $obj->getIs_admin()){
          $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"User.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='far fa-edit'></i></a>";
        }

        $json[] = array(
          'id' => $obj->getId(Content::FORMAT_BADGE),
          "uniqid" => $obj->getUniqid(),
          "timestamp_add" => $obj->getTimestamp_add(),
          "last_connexion" => $obj->getLast_connexion(),
          "pseudo" => $obj->getPseudo(),
          "email" => $obj->getEmail(),
          "rights" => $obj->getRights(Content::FORMAT_BADGE),
          'edit' => $edit,
          'detailView' => $obj->getVisual(Content::DISPLAY_CARD)
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
    if(!$currentUser->getRight('user', User::RIGHT_READ)){
      $return['error'] = "Vous n'avez pas les droits pour lire cet objet";}else{

      if(!isset($_REQUEST['uniqid'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        $manager = new UserManager();

        // Récupération de l'objet
          if($manager->existsUniqid($_REQUEST['uniqid'])){
            $obj = $manager->getFromUniqid($_REQUEST['uniqid']);

            $edit = "";
            if($currentUser->getRight('user', User::RIGHT_WRITE) && $obj->getIs_admin()){
              $edit = "<a id='{$obj->getUniqid()}' class='text-main-d-2 text-main-l-3-hover' onclick=\"User.open('{$obj->getUniqid()}', Controller.DISPLAY_EDITABLE)\"><i class='far fa-edit'></i></a>";
            }

            $return["value"] = array(
              'id' => $obj->getId(Content::FORMAT_BADGE),
              "uniqid" => $obj->getUniqid(),
              "timestamp_add" => $obj->getTimestamp_add(),
              "last_connexion" => $obj->getLast_connexion(),
              "pseudo" => $obj->getPseudo(),
              "email" => $obj->getEmail(),
              "rights" => $obj->getRights(Content::FORMAT_BADGE),
              'edit' => $edit,
              'detailView' => $obj->getVisual(Content::DISPLAY_CARD)
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
      'script' => ""
    ];
    if(!$currentUser->getRight('user', User::RIGHT_WRITE) && $currentUser->getIs_admin()){
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

                $manager = new UserManager();
                $admins = $manager->getAllAdmins();
                $mail = new Mail();
                $mail->setSubject("Bienvenu sur ".$GLOBALS["project"]["name"]." !");
                $mail->setTo($object->getEmail());
                foreach ($admins as $admin) {
                  $mail->setCci($admin->getEmail());
                }
                $mail->setTemplate(Mail::TEMPLATE_NEW_USER, 
                  [
                    "mail" => $object->getEmail()
                  ]
                );
                $result = $mail->send();
                if($result !== true){
                  $return['error'] = $result;
                }

                $return['state'] = true;
                $return['script'] = "User.open('".$object->getUniqid()."', Controller.DISPLAY_EDITABLE)";
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

  public function updatePassword(){
    $currentUser = ControllerConnect::getCurrentUser();
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];

    if(!$currentUser->getRight('user', User::RIGHT_WRITE) && $currentUser->getIs_admin() && $currentUser->getUniqid() != $_REQUEST['uniqid']){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['uniqid'], $_REQUEST['new_password'], $_REQUEST['new_password_repeat'], $_REQUEST['current_password'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {

        if($_REQUEST['new_password'] != $_REQUEST['new_password_repeat']){
          $return['error'] = 'Les deux mots de passe doivent être identiques.';
        } else {

            $manager = new UserManager();

            if($manager->existsUniqid($_REQUEST['uniqid'])){
              $object = $manager->getFromUniqid($_REQUEST['uniqid']);

              if($manager->getMatch($object->getEmail(), $_REQUEST['current_password'])){

                $object->setPassword($_REQUEST["new_password"]);
                if($manager->update($object)){
                  
                  // Mail
                    $mail = new Mail();
                    $mail->setSubject("Modification de votre mot de passe - " . $GLOBALS["project"]["name"]);
                    $mail->setTo($object->getEmail());
                    $mail->setTemplate(Mail::TEMPLATE_GENERIC, 
                      [
                        "text" => "Votre mot de passe a bien été modifié.<br>Si vous n'êtes pas à l'origine de cette modification, veuillez contacter l'adminitrateur·trice du site. (".$GLOBALS['project']['mail']['contact'].")"
                      ]);
                    $result = $mail->send();
                    if($result !== true){
                      return $result;
                    }
                  
                  $return['state'] = true;
                  $return['script'] = "User.open('".$object->getUniqid()."', Controller.DISPLAY_EDITABLE)";
                  
                }else {
                  $return['error'] = 'Impossible d\'ajouter l\'objet';
                }
              } else {
                $return['error'] = "Le mot de passe actuel est incorrect.";
              }
            } else {
              $return['error'] = "L'objet n'existe pas.";
            }
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
          <?=$user->getBookmark(Content::DISPLAY_CARD);?>
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

  public function verifAndUpdateRights(User $user){
    $manager = new UserManager();
    $rights = $user->getRights(Content::FORMAT_ARRAY);
    $has_edited = false;
    foreach (User::RIGHT as $name => $value) {
      if(!isset($rights[$name])){
        $user->setRights($name, $value);
        $has_edited = true;
      }
    }
    if($has_edited){
      $manager->update($user);
      ControllerConnect::setCurrentUser($user);
    }
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

  // Events
    public function eventAfterUpdate(){
      $manager = new UserManager();
      $admins = $manager->getAllAdmins();
      $mail = new Mail();
      $mail->setSubject("Modification d'un compte - ". $GLOBALS["project"]["name"]);
      foreach ($admins as $admin) {
        $mail->setTo($admin->getEmail());
      }
      $mail->setTemplate(Mail::TEMPLATE_EDIT_USER, 
        [
          "new_user" => $this->obj,
          "old_user" => $this->obj_old
        ]
      );
      $result = $mail->send();
      if($result !== true){
         return $result;
      }
    }
}