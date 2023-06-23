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
                "modal" => ""
            ];

            $user = ControllerConnect::getCurrentUser();

            if(ControllerConnect::isConnect() && !empty($user->getEmail())){

                ob_start(); ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-animate btn-back-secondary" onclick="Connect.getHeader(true);"><?=$user->getPseudo()?></button>
                        <button type="button"  class="btn btn-sm btn-back-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item btn-animate back-secondary-l-4-hover" onclick="User.open('<?=$user->getUniqid()?>');">Paramètres</a></li>
                            <?php if($user->getRight("page", User::RIGHT_WRITE)){ ?>
                                <li><a class="dropdown-item btn-animate back-secondary-l-4-hover" onclick="Page.show('gestion_des_pages');">Gérer les pages</a></li>
                            <?php } ?>
                            <?php if($user->getIs_admin()){ ?>
                                <li><a class="dropdown-item btn-animate back-secondary-l-4-hover" onclick="Page.show('user_manager');">Gérer les utilisateurs·trices</a></li>
                            <?php } ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item btn-animate back-secondary-l-4-hover dropdown-item-toogletoolbar-button" onclick="toogleToolbar();">Masquer la barre d'outils</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item btn-animate back-secondary-l-4-hover" onclick="Connect.disconnect();">Deconnexion</a></li>
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
                                <li><a class="dropdown-item btn-animate back-secondary-l-4-hover" onclick="User.open('<?=$user->getUniqid()?>');">Paramètres</a></li>
                                <?php if($user->getRight("page", User::RIGHT_WRITE)){ ?>
                                    <li><a class="dropdown-item btn-animate back-secondary-l-4-hover" onclick="Page.show('gestion_des_pages');">Gérer les pages</a></li>
                                <?php } ?>
                                <?php if($user->getIs_admin()){ ?>
                                    <li><a class="dropdown-item btn-animate back-secondary-l-4-hover" onclick="Page.show('user_manager');">Gérer les utilisateurs·trices</a></li>
                                <?php } ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item btn-animate back-secondary-l-4-hover" onclick="Connect.disconnect();">Deconnexion</a></li>
                            </ul>
                        </div>
                    <?php $return["header_mobile"] = ob_get_clean();

                $return["modal"] = $user->getVisual(new Style(["display" => Content::DISPLAY_EDITABLE]));
                $return["size"] = "lg";
                $return["title"] = "Compte";

            } else {
                ob_start(); ?>
                    <div>
                        <button type="button" onclick="Connect.getHeader(true);" class="btn btn-sm btn-animate btn-back-secondary">Connexion</button>
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
                    <div class="text-center user__modal_connexion">
                        <div class="d-flex flex-row justify-content-around align-item-baseline pb-4 pt-2 px-4">
                            <p><a class="btn-text-main user__modal_btn-switch" onclick="switchConnectInscript('#modalConnexionUser', this);">Se connecter</a></p>
                            <p><a class="btn-text-main user__modal_btn-switch" onclick="switchConnectInscript('#modalAddUser', this);">S'inscrire</a></p>
                            <p><a class="btn-text-main user__modal_btn-switch" onclick="switchConnectInscript('#modalPasswordForgotten', this);">Mot de passe oublié</a></p>
                        </div>
                        <div id="modalConnexionUser" class="user__modal_tab">
                            <h3>Connexion</h3>
                            <div class="form-floating mb-3">
                                <input required type="text" class="form-control form-control-main-focus form-control form-control-main-focus-main-focus" id="email" placeholder="Email">
                                <label for="email">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input required type="password" class="form-control form-control-main-focus" id="password" placeholder="Mot de passe ou Passe-phrase">
                                <label for="password">Mot de passe ou Passe-phrase</label>
                            </div>
                            <?php $disabled=""; if(!$user->getCookie(User::COOKIE_CONNEXION)){$disabled="disabled title='Cette fonctionnalité resquière l\'utilisation d'un cookie de connexion.'";}?>
                            <div class="form-check text-left">
                                <input class="form-check-input form-control-main-focus back-main-l-2 border-main" type="checkbox" value="" <?=$disabled?> id="remember">
                                <label class="form-check-label" for="remember">Garder la connexion</label>
                            </div>
                            <p class="size-0-8 text-grey-d-1 text-left"><i class="fa-solid fa-question-circle"></i> Cette fonctionnalité resquière l'utilisation d'un cookie de connexion.</p>
                            <p id="display_error" class="bold text-red-d-3 size-0-8"></p>
                            <button type="button" onclick="Connect.connect();" class="btn btn-animate btn-border-secondary">Se connecter</button>
                        </div>
                        
                        <div id="modalAddUser" class="user__modal_tab">
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
                            <button type="button" onclick="User.add();" class="btn btn-animate btn-border-secondary">S'inscrire</button>
                        </div>

                        <div id="modalPasswordForgotten" class="user__modal_tab">
                            <h3 class="mb-3">Mot de passe oublié</h3>
                            <p>Saisissez l'adresse mail que vous avez utilisé pour créer votre compte.</p>
                            <div class="form-floating mb-3">
                                <input required type="text" class="form-control form-control-main-focus form-control form-control-main-focus-main-focus" id="email" placeholder="Email">
                                <label for="email">Email</label>
                            </div>
                            <p class="mb-3">Un nouveau mot de passe vous sera transmis sur cette adresse mail. Pensez à le modifier dès votre première connexion.</p>
                            <button type="button" onclick="Connect.passwordForgotten();" class="btn btn-border-secondary">Envoyer un nouveau mot de passe</button>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function(){
                            switchConnectInscript('#modalConnexionUser', ".user__modal_connexion .user__modal_btn-switch:first");
                        });
                        function switchConnectInscript(tab, btn){
                            $(".user__modal_tab").hide();
                            $(".user__modal_connexion .user__modal_btn-switch").css("border-bottom", "none");
                            $(tab).show();
                            $(btn).css("border-bottom", "solid 1px var(--main-d-2)");
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

