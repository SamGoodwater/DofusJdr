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
            if(ControllerConnect::isConnect()){
                return unserialize($_SESSION['user']);
            } else {
                return new User([
                    'pseudo' => 'guest',
                    'rights' => User::RIGHT
                ]);
            }
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
                        $user->setCookie(User::COOKIE_GRIMOIRE, $matches[1]);
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
                "modal" => ""
            ];

            $user = ControllerConnect::getCurrentUser();

            if(ControllerConnect::isConnect() && !empty($user->getEmail())){

                ob_start(); ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-back-secondary" onclick="Connect.getHeader(true);"><?=$user->getPseudo()?></button>
                        <button type="button"  class="btn btn-sm btn-back-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" onclick="User.open('<?=$user->getUniqid()?>');">Paramètres</a></li>
                            <?php if($user->getRight("page", User::RIGHT_WRITE)){ ?>
                                <li><a class="dropdown-item" onclick="Page.show('gestion_des_pages');">Gérer les pages</a></li>
                            <?php } ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item dropdown-item-toogletoolbar-button" onclick="toogleToolbar();">Masquer la barre d'outils</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" onclick="Connect.disconnect();">Deconnexion</a></li>
                        </ul>
                    </div>
                    <?php $return["header"] = ob_get_clean();
                        ob_start(); ?>
                        <div class="dropup-center dropup">
                            <button type="button"  class="dropdown-toggle border-none back-transparent" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php View::shortcutDispatch(
                                    template_type: View::TEMPLATE_SNIPPET,
                                    template_name : "icon",
                                    data : [
                                        "style" => Style::ICON_SOLID,
                                        "icon" => "user",
                                        "color" => "secondary",
                                        "is_btn" => true,
                                        "btn_type" => Style::STYLE_TEXT,
                                        "size" => "size-1-3",
                                        "tooltip" => "Accèder à mon compte",
                                        "onclick" => "",
                                        "content" => "Mon compte",
                                        "content_placement" => Style::POSITION_BOTTOM
                                    ], 
                                    write: true); ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li class="italic text-center"><?=$user->getPseudo()?></li>
                                <li class="item-divider-main"></li>
                                <li><a class="dropdown-item" onclick="User.open('<?=$user->getUniqid()?>');">Paramètres</a></li>
                                <?php if($user->getRight("page", User::RIGHT_WRITE)){ ?>
                                    <li><a class="dropdown-item" onclick="Page.show('gestion_des_pages');">Gérer les pages</a></li>
                                <?php } ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" onclick="Connect.disconnect();">Deconnexion</a></li>
                            </ul>
                        </div>
                    <?php $return["header_mobile"] = ob_get_clean();

                $return["modal"] = $user->getVisual(Content::FORMAT_EDITABLE);
                $return["size"] = "fl";
                $return["title"] = "Compte";

            } else {
                ob_start(); ?>
                    <div>
                        <button type="button" onclick="Connect.getHeader(true);" class="btn btn-sm btn-back-secondary">Connexion</button>
                    </div>
                <?php $return["header"] = ob_get_clean();
                ob_start(); ?>
                    <div>
                        <?php View::shortcutDispatch(
                            template_type: View::TEMPLATE_SNIPPET,
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_SOLID,
                                "icon" => "user",
                                "color" => "secondary",
                                "is_btn" => true,
                                "btn_type" => Style::STYLE_TEXT,
                                "size" => "size-1-3",
                                "tooltip" => "Se connecter ou créer un compte",
                                "onclick" => "Connect.getHeader(true);",
                                "content" => "Se connecter",
                                "content_placement" => Style::POSITION_BOTTOM
                            ], 
                            write: true); ?>
                    </div>
                <?php $return["header_mobile"] = ob_get_clean();
                ob_start(); ?>
                    <div class="text-center">
                        <div id="modalConnexionUser">
                            <h3>Connexion</h3>
                            <div class="form-floating mb-3">
                                <input required type="text" class="form-control form-control-main-focus form-control form-control-main-focus-main-focus" id="email" placeholder="Email">
                                <label for="email">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input required type="password" class="form-control form-control-main-focus" id="password" placeholder="Mot de passe ou Passe-phrase">
                                <label for="password">Mot de passe ou Passe-phrase</label>
                            </div>
                            <div class="form-check text-left">
                                <input class="form-check-input" type="checkbox" value="" id="remember">
                                <label class="form-check-label" for="remember">Garder la connexion</label>
                            </div>
                            <p class="size-0-8 text-grey-d-1 text-left"><i class="fas fa-question-circle"></i> Cette fonctionnalité resquière l'utilisation d'un cookie de connexion.</p>
                            <p id="display_error" class="bold text-red-d-3 size-0-8"></p>
                            <button type="button" onclick="Connect.connect();" class="btn btn-border-secondary">Se connecter</button>
                        </div>
                        
                        <div id="modalAddUser">
                            <h3>Inscription</h3>
                            <div class="form-floating mb-3">
                                <input required type="text" class="form-control form-control-main-focus" id="email" placeholder="Email">
                                <label for="email">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input required type="text" class="form-control form-control-main-focus" id="pseudo" placeholder="Pseudo">
                                <label for="email">Pseudo</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input required onchange="verifPassword();" type="password" class="form-control form-control-main-focus" id="password" placeholder="Mot de passe ou Passe-phrase">
                                <label for="password">Mot de passe ou Passe-phrase</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input required onchange="verifPassword();" type="password" class="form-control form-control-main-focus" id="password_repeat" placeholder="Réécrire le mot de passe ou la passe-phrase">
                                <label for="password_repeat">Réécrire le mot de passe ou la passe-phrase</label>
                            </div>
                            <p id="display_error" class="bold text-red-d-3 size-0-8"></p>
                            <button type="button" onclick="User.add();" class="btn btn-border-secondary">S'inscrire</button>
                        </div>

                        <a class="btn-text-main size-0-9 p-3 text-center" onclick="switchConnectInscript(this);">S'inscrire</a>
                    </div>
                    <script>
                        $("#modalAddUser").hide();
                        function switchConnectInscript(btn){
                            if($("#modalConnexionUser").css('display') == 'none'){
                                $("#modalConnexionUser").show();
                                $("#modalAddUser").hide();
                                $(btn).text("S'inscrire");
                            } else {
                                $("#modalConnexionUser").hide();
                                $("#modalAddUser").show();
                                $(btn).text("Se connecter");
                            }
                        }
                    </script>
                <?php $return["modal"] = ob_get_clean();
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
            $cookie_bookmark = $old_user->getCookie(User::COOKIE_GRIMOIRE);

            $ControllerConnect = new ControllerConnect();
            if(ControllerConnect::isConnect() && !empty($old_user->getEmail())){
                $return["error"] = "Vous êtes déjà connecté";
                $return["script"] = "Connect.getHeader(false);$('#modal').modal('hide');";
            } else {
                if(isset($_REQUEST['email'],$_REQUEST['password'])){
                    $UserManager = new UserManager();
                    $user = $UserManager->getMatch($_REQUEST['email'],$_REQUEST['password']);
                    if(is_object($user)){
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
                $user->setcookie(User::COOKIE_CONNEXION, $this->returnBool($_REQUEST['connexion']));
                $user->setcookie(User::COOKIE_GRIMOIRE, $this->returnBool($_REQUEST['bookmark']));
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
                new AlertManager(new Alert("Dernière connexion de " . $user->getPseudo(),"Dernière connexion le " . $user->getLast_connexion(Content::DATE_FR) . " à " .  $user->getLast_connexion(Content::TIME_FR),"",Alert::ALERT_INFO,6000));
                return true;
        }

}


// public function passwordForgotten(){
        //     $return = [
        //         'state' => true,
        //         'value' => "",
        //         'error' => '',
        //         'script' => ""
        //       ];
          
        //       if(!isset($_REQUEST['mail']))
        //       {
        //         $return['error'] = 'Impossible de récupérer les données';
        //       } else {
 
        //             // Récupération des objets
        //             $manager = new UserManager();
        //             // Récupération de l'objet
        //             if($manager->existsId($_REQUEST['mail'])){
        //                 $user = $managerM->getFromMail($_REQUEST['mail']);
        //                 $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
        //                 for($i=0;$i<9;$i++){
        //                     $password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
        //                 }
        //                 $user->setPassword($password);
        //                 $manager->update($user);
        //                                        // Envoi d'un mail :
        //                     // Création
        //                     $mail = New Mail();
        //                     // Paramètres
        //                         $mail->setSubject("Mot de passe oublié - " . $institution->getName());
        //                         $mail->setFrom($setting->getMail_username(), $institution->getName());
        //                         $mail->setReply($setting->getMail_username(), $institution->getName()); // Facultatif
        //                         $mail->setTo($_REQUEST['mail']); // --- peut-être répéter ---
        //                         $mail->setTemplate(Mail::TEMPLATE_PASSWORD_FORGOTTEN,["password" => $password]);
        //                       // Envoi
        //                         if(!$mail->send()){
        //                           $return['error'] = $result;
        //                         }

        //                 $return['state'] = true;
        //             }
        //       }
          
        //       echo json_encode($return);
        //       flush();
        // }
