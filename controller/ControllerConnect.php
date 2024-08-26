<?php
class ControllerConnect extends Controller{

    /* ♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥
            S T A T I C
    ♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥ */
        static function connect(){
            $controller = new ControllerConnect;
            if(ControllerConnect::isConnect()){
                $controller->connectUser(ControllerConnect::getCurrentUser());
            } else {
                if($controller->verifCookie()){
                    $controller->connectWithCookie();
                }
            }
            self::setInUserCookie_preference();
            $userController = new ControllerUser;
            $userController->verifAndUpdateRights(ControllerConnect::getCurrentUser());
            $userController->setBookmark();
        }
        static function isConnect(){
            if(isset($_SESSION['user'])){
                return true;
            }else {
                return false;
            }
        }
        static function getCurrentUser(){
            $controller = new ControllerConnect;
            if(ControllerConnect::isConnect()){
                if($controller->isSerialized($_SESSION['user'])){
                    return unserialize($_SESSION['user']);
                }
            }
            return new User([
                'pseudo' => 'guest',
                'rights' => Module::USER_RIGHT
            ]);
        }
        static function setCurrentUser(User $user){
            $_SESSION['user'] = serialize($user);
        }
        static function setInUserCookie_preference(){
            $user = self::getCurrentUser();
            if(isset($_COOKIE['cookie_preference'])){
                $cookies = explode("|", $_COOKIE['cookie_preference']);
                foreach ($cookies as $cookie) {
                    if(preg_match('/^requisite:(1|0|true|false)$/mi', $cookie, $matches)){
                        $user->setCookie(User::COOKIE_REQUISITE, $matches[1]);
                    } elseif(preg_match('/^connexion:(1|0|true|false)$/mi', $cookie, $matches)){
                        $user->setCookie(User::COOKIE_CONNEXION, $matches[1]);
                    }elseif(preg_match('/^bookmark:(1|0|true|false)$/mi', $cookie, $matches)){
                        $user->setCookie(User::COOKIE_BOOKMARK, $matches[1]);
                    }
                }
            }
        }
        private function createCookie(User $user){
            $token = md5($user->getToken()) . $user->getUniqid(); 
            $time = time() + (60 * 60 * 24 * 7); // 1 semaine
            if(setcookie('connexion', $token, $time, '/')){
                return [
                    "token" => $token,
                    "date" => gmdate("D, d M Y H:i:s",$time)
                ];
            } else {
                return [];
            }
        }
        private function deleteCookie(){
            $time = time() - 2 * 24 * 60 * 60; // -2 jour 
            if(setcookie('connexion', '', $time, '/')){
                return [
                    "token" => "",
                    "date" => gmdate("D, d M Y H:i:s",$time)
                ];
            } else {
                return [];
            }
        }
        private function verifCookie(){
            if(isset($_COOKIE['connexion'])){
                $uniqid = substr($_COOKIE['connexion'], 32);
                $manager = new UserManager;
                if($manager->existsUniqid($uniqid)){
                    $user = $manager->getFromUniqid($uniqid);
                    $token = md5($user->getToken()) . $user->getUniqid();
                    if($token == $_COOKIE['connexion']){
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        private function connectWithCookie(){
            $controller = new ControllerConnect;
            if($controller->verifCookie()){
                $uniqid = substr($_COOKIE['connexion'], 32);
                $manager = new UserManager;
                $user = $manager->getFromUniqid($uniqid);
                $controller->createCookie($user);
                $controller->connectUser($user);
            }
        }

    /* ♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥
                V I E W S
    ♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥♥ */
        public function getVisual($is_flush = true){
            $return = [
                "size" => "lg",
                "title" => "",
                "header" => "",
                "header_mobile" => "",
                "modal" => "",
                "script" => ""
            ];

            $user = ControllerConnect::getCurrentUser();

            if(ControllerConnect::isConnect() && !empty($user->getEmail())){

                $return["header"] = View::shortcutDispatch(
                    template_type: View::TEMPLATE_DISPLAY,
                    template_name : "user/header_btn",
                    data : [
                        "is_connect" => true,
                        "user" => $user
                    ],
                    write: false
                );
                $return["modal"] = $user->getVisual(new Style(["display" => Content::DISPLAY_EDITABLE]));
                $return["size"] = "lg";
                $return["title"] = "Compte";

            } else {

                // Nouvelle version
                $return["header"] = View::shortcutDispatch(
                    template_type: View::TEMPLATE_DISPLAY,
                    template_name : "user/header_btn",
                    data : [
                        "is_connect" => false,
                        "user" => $user
                    ],
                    write: false
                );
                // Nouvelle version
                $return["modal"] = View::shortcutDispatch(
                    template_type: View::TEMPLATE_DISPLAY,
                    template_name : "user/connect_modal",
                    data : [
                        "user" => $user
                    ],
                    write: false
                );
                $return["size"] = "xl";
                $return["title"] = "Connexion / Inscription";
            }
            if(isset($_REQUEST['is_flush'])){$is_flush = true;}
            if($is_flush){
                echo json_encode($return);
                flush();
            } else {
                return $return;
            }
        }
        
        public function connexion(){
            $return = [
                'state ' => false,
                'value' => "",
                'script' => "",
                'error' => 'erreur inconnue',
                'cookie' => ""
            ];

            $old_user = self::getCurrentUser();
            $cookie_requisite = $old_user->getCookie(User::COOKIE_REQUISITE);
            $cookie_connexion = $old_user->getCookie(User::COOKIE_CONNEXION);
            $cookie_bookmark = $old_user->getCookie(User::COOKIE_BOOKMARK);

            $ControllerConnect = new ControllerConnect();
            if(ControllerConnect::isConnect() && !empty($old_user->getEmail())){
                $return["error"] = "Vous êtes déjà connecté";
                $return["script"] = "Connect.getHeader(false);$('#modal').modal('hide');";
            } else {
                if(isset($_REQUEST['email'],$_REQUEST['password'])){
                    $UserManager = new UserManager();
                    $user = $UserManager->getMatch($_REQUEST['email'],$_REQUEST['password']);
                    if(is_object($user)){
                        $user->setCookie(User::COOKIE_REQUISITE, $cookie_requisite);
                        $user->setCookie(User::COOKIE_CONNEXION, $cookie_connexion);
                        $user->setCookie(User::COOKIE_BOOKMARK, $cookie_bookmark);
                        ControllerConnect::setCurrentUser($user);
                        $ControllerConnect->connectUser($user);
                        if(isset($_REQUEST['remember'])){
                            if($_REQUEST['remember'] == true && $user->getCookie(User::COOKIE_CONNEXION)){
                                $return['cookie'] = $ControllerConnect->createCookie($user);
                            }
                        }
                        $return["state"] = true;
                        $return["value"] = $ControllerConnect->getVisual(false);

                    } else {
                        $return["error"] = $user;
                    }

                } else {
                    $return["error"] = "L'adresse mail ou le mot de passe n'est pas correcte.";
                }
            }
            echo json_encode($return);
            flush();
        }
        public function disconnect(){  
            $return = [
                'state' => true,
                'value' => "",
                'error' => '',
                'script' => "",
                "cookie" => ""
            ];

            $ControllerConnect = new ControllerConnect();

            if(isset($_SESSION['user'])){
                unset($_SESSION['user']);
                $return["cookie"] = $ControllerConnect->deleteCookie();
            }
            session_destroy();

            $return["value"] = $ControllerConnect->getVisual(false); 

            echo json_encode($return);
            flush();
        }

        public function setCookiePreference(){
            $return = [
                'state ' => false,
                'error' => 'erreur inconnue',
                'cookie' => []
              ];

            if(isset($_REQUEST['connexion'], $_REQUEST['bookmark'])){
                $user = ControllerConnect::getCurrentUser();
                $user->setcookie(User::COOKIE_REQUISITE, true);
                $user->setcookie(User::COOKIE_CONNEXION, $_REQUEST['connexion']);
                $user->setcookie(User::COOKIE_BOOKMARK, $_REQUEST['bookmark']);
                ControllerConnect::setCurrentUser($user);
                $return["cookie"]["preference"] = "requisite:true|connexion:".$_REQUEST['connexion']."|bookmark:".$_REQUEST['bookmark'];
                $time = time() + (60 * 60 * 24 * 7 * 4 * 6); // 6 mois
                $return["cookie"]["date"] = gmdate("D, d M Y H:i:s",$time);
                $return['state'] = true;
            } else {
                $return["error"] = "Tout les cookies ne sont pas envoyés.";
            }
            echo json_encode($return);
            flush();
        }
        
        private function connectUser(User $user){
                $manager = new UserManager();
                $user->setLast_connexion(time());
                $manager->update($user);
                ControllerConnect::setCurrentUser($user);
                return true;
        }

        public function passwordForgotten(){
                $return = [
                    'state' => true,
                    'value' => "",
                    'error' => '',
                    'script' => ""
                  ];
              
                  if(!isset($_REQUEST['mail']))
                  {
                    $return['error'] = 'Impossible de récupérer les données';
                  } else {
        
                        // Récupération des objets
                        $manager = new UserManager();
                        // Récupération de l'objet
                        if($manager->existsEmail($_REQUEST['mail'])){
                            $user = $manager->getFromEmail($_REQUEST['mail']);
                            $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "é", "è", "à", "&", "^", "$", "*", "ù", "!", "?", ":", ";", ",", ".", "/", "|", "%", "µ", "£", "¤", "§", "°", "=", "+", "-", "(", ")", "[", "]", "{", "}", "<", ">", "~", "#", "²");
                            $password = "";
                            for($i=0;$i<9;$i++){
                                $password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
                            }
                            $user->setPassword($password);
                            $manager->update($user);
                            $mail = new Mail();
                            $mail->setTo($user->getEmail());
                            $mail->setSubject("Mot de passe oublié- ". $GLOBALS["project"]["name"]);
                            $mail->setTemplate("/view/mails/password_forgotten.php", 
                              [
                                "mail" => $user->getEmail(),
                                "password" => $password
                              ]
                            );
                            $result = $mail->send();
                            if($result !== true){
                                $return['error'] = $result;
                            } else {
                                $return['state'] = true;
                            }
                        }
                  }
              
                  echo json_encode($return);
                  flush();
        }
              
}