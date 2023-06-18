<?php
class User extends Content
{

    public function __construct(array $donnees){
        parent::__construct($donnees);

        if(isset($donnees['rights'])){
            if($donnees['rights'] == ""){
                $this->setRights();
            }
        }
        if($this->_last_connexion == 0){
            $this->setLast_connexion();
        }
    }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ CONSTANTES ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        const RIGHT_NO = 0;
        const RIGHT_READ = 1;
        const RIGHT_WRITE = 2;

        const RIGHT_TYPE = [
            "aucun" => self::RIGHT_NO,
            "lecture" => self::RIGHT_READ,
            "écriture" => self::RIGHT_WRITE
        ];

        const COOKIE_REQUISITE = 1;
        const COOKIE_CONNEXION = 2;
        const COOKIE_BOOKMARK = 3;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_token='';
        private $_email='';
        private $_pseudo='Invité';
        private $_hash='';
        private $_last_connexion= 0 ;
        private $_rights = null;
        private $_is_admin = false;

        protected $_usable = true; // surcharge de la variable de Content

        private $_cookie = [
            self::COOKIE_REQUISITE => true,
            self::COOKIE_CONNEXION => false,
            self::COOKIE_BOOKMARK => false
        ];

        private $_bookmark = [];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getToken(){
            return $this->_token;
        }
        public function getLast_connexion($format=NULL){
            if(!empty($format)){
              return date($format, $this->_last_connexion);
            } else {
                return $this->_last_connexion;
            }
        }
        public function getEmail(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "User",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "email",
                            "label" => "Email",
                            "placeholder" => "Email de l'utilisatrice·teur",
                            "value" => $this->_email,
                            "parttern" => "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$", 
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_email;
            }
        }
        public function getPseudo(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "User",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "pseudo",
                            "label" => "Pseudo",
                            "placeholder" => "Pseudo de l'utilisatrice·teur",
                            "value" => $this->_pseudo,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_pseudo;
            }
        }
        public function getName(int $format = Content::FORMAT_BRUT){
            return $this->getPseudo($format);
        }
        public function gethash(int $format = Content::FORMAT_BRUT){
            return $this->_hash;
        }
        public function getIs_admin(int $format = Content::FORMAT_BRUT, $showNoneAdmin = true){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/checkbox",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "id" => "is_admin_" . $this->getUniqid(),
                            "input_name" => "is_admin",
                            "label" => $this->getIs_admin(Content::FORMAT_BADGE),
                            "checked" => $this->_is_admin,
                            "style" => Style::CHECK_SWITCH
                        ], 
                        write: false);
                    
                case Content::FORMAT_BADGE:
                    if($this->_is_admin){ 
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "Admin",
                                "color" => "secondary-d-1",
                                "style" => Style::STYLE_BACK,
                                "tooltip" => "L'utilisateur·trice est adminitrateur·trice"
                            ], 
                            write: false);
    
                    } elseif($showNoneAdmin) {
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => "Utilisateur·trice",
                                "color" => "grey-d-1",
                                "style" => Style::STYLE_BACK,
                                "tooltip" => "L'utilisateur·trice n'est pas adminitrateur·trice"
                            ], 
                            write: false);
                    } else {
                        return "";
                    }
    
                case Content::FORMAT_ICON:
                    if($this->_is_admin){
    
                        return $view->dispatch(
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_SOLID,
                                "icon" => "user-cog",
                                "color" => "secondary-d-3",
                                "tooltip" => "L'utilisateur·trice est adminitrateur·trice"
                            ], 
                            write: false); 
    
                    } elseif($showNoneAdmin) { 
    
                        return $view->dispatch(
                            template_name : "icon",
                            data : [
                                "style" => Style::ICON_SOLID,
                                "icon" => "user-lock",
                                "color" => "grey-d-3",
                                "tooltip" => "L'utilisateur·trice n'est pas adminitrateur·trice"
                            ], 
                            write: false);
                    } else {
                        return "";
                    }
                    
                default:
                    return $this->_is_admin;
            }
        }
        
        public function getPassword(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                    <div>

                    <?php $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "is_onchange" => false,
                            "label" => "Mot de passec / passphrase actuel",
                            "color" => "main",
                            "value" => "",
                            "placeholder" => "•••••••••",
                            "type" => "password",
                            "id" => "currentpassword",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: true); ?>

                    <?php $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "is_onchange" => false,
                            "label" => "Nouveau Mot de passec /nouvelle  passphrase",
                            "color" => "main",
                            "value" => "",
                            "placeholder" => "•••••••••",
                            "type" => "password",
                            "id" => "newpassword",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: true); ?>

                    <?php $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "is_onchange" => false,
                            "label" => "Répéter le nouveau Mot de passec / la nouvelle passphrase",
                            "color" => "main",
                            "value" => "",
                            "placeholder" => "•••••••••",
                            "type" => "password",
                            "id" => "repeatnewpassword",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: true); ?>

                        <div class="text-center mt-3"><a class="btn btn-sm btn-border-main" onclick="User.updatePassword('<?=$this->getUniqid()?>');">Modifier</a></div>
                    </div>
                    <?php return ob_get_clean();
                
                default:
                    return "";
            }
        }

        // Retourne si l'utilisateur à un certain droit sur un objet
        public function getRight(string $objet, int $right = User::RIGHT_WRITE){
            if($this->getIs_admin()){
                return true;
            }
            $rights = $this->getRights(Content::FORMAT_ARRAY);
            if(!array_key_exists($objet,$rights)){
                return false;
            }
            if($rights[$objet] == $right || $rights[$objet] == User::RIGHT_WRITE){
                return true;
            } else {
                return false;
            }
        }
        // Retourne les droits d'un utilisateur sur tout ou un objet
        public function getRights(int $format = Content::FORMAT_BRUT, string $type = "all"){
            $view = new View();
            $badge_right_no = $view->dispatch(
                template_name : "badge",
                data : [
                    "content" => "<i class='fas fa-exclamation-triangle'></i> Aucun",
                    "color" => "grey-d-3",
                    "tooltip" => "Aucun droit",
                    "style" => Style::STYLE_OUTLINE,
                ], 
                write: false);
            $badge_right_read = $view->dispatch(
                template_name : "badge",
                data : [
                    "content" => "<i class='fas fa-book-open'></i> Lecture",
                    "color" => "blue-d-3",
                    "tooltip" => "Droit de lecture",
                    "style" => Style::STYLE_OUTLINE,
                ], 
                write: false);
            $badge_right_write = $view->dispatch(
                template_name : "badge",
                data : [
                    "content" => "<i class='fas fa-edit'></i> Écriture",
                    "color" => "green-d-3",
                    "tooltip" => "Droit d'écriture",
                    "style" => Style::STYLE_OUTLINE,
                ], 
                write: false);

            if($this->isSerialized($this->_rights)){
                $rights = unserialize($this->_rights);
                if(!is_array($rights)){
                    $rights = [];
                }
            } else {
                $rights = [];
            }
            if($type != "all" && isset(Module::USER_RIGHT[$type])){
                $rights = [$type => $rights[$type]];
            }

            switch ($format) {
                case Content::FORMAT_EDITABLE:
                      ob_start(); ?>
                        <div class="m-3">
                           <?php foreach ($rights as $right_name => $value) {
                                if(isset(Module::USER_RIGHT[$right_name]) && in_array($value, User::RIGHT_TYPE)){?>
                                    <h5 class="m-0 mt-2">Droit concernant <?=ucfirst($right_name)?></h5>
                                    <?php foreach (User::RIGHT_TYPE as $type) {
                                        $checked = false; if($value == $type){ $checked = true;}
                                        $badge = $badge_right_no;
                                        switch ($type) {
                                            case self::RIGHT_READ:
                                                $badge = $badge_right_read; break;
                                            case self::RIGHT_WRITE:
                                                $badge = $badge_right_write; break;
                                            default: 
                                                $badge = $badge_right_no; break;
                                        }
                                        $view->dispatch(
                                            template_name : "input/checkbox",
                                            data : [
                                                "onchange" => "User.update('".$this->getUniqid()."', ['".$right_name."',".$type."], 'rights', ".Controller::IS_VALUE.");",
                                                "name" => "right_".$right_name,
                                                "color" => "secondary-d-2",
                                                "id" => "right_".$right_name."_" . $this->getUniqid(),
                                                "label" => $badge,
                                                "checked" => $checked,
                                                "style" => Style::CHECK_RADIO,
                                                'is_inline' => true
                                            ], 
                                            write: true);
                                        }
                                }
                            } ?>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="m-3">
                            <?php foreach ($rights as $right_name => $value) {
                                if(isset(Module::USER_RIGHT[$right_name]) && in_array($value, User::RIGHT_TYPE)){ ?>
                                    <h5 class="m-0 mt-2">Droit concernant <?=ucfirst($right_name)?></h5>
                                    <?php switch ($value) {
                                        case self::RIGHT_NO:
                                            $color = "grey";
                                            $title = "Aucun droit concernant les ".$right_name;
                                            $text = "Aucun <i class='fas fa-exclamation-triangle'></i>";
                                        break;
                                        case self::RIGHT_READ:
                                            $color = "blue";
                                            $title = "Lecture seule concernant les ".$right_name;
                                            $text = "Lecture <i class='fas fa-book-open'></i>";
                                        break;
                                        case self::RIGHT_WRITE:
                                            $color = "green";
                                            $title = "Lecture et écriture concernant les ".$right_name;
                                            $text = "Ecriture & Lecture <i class='fas fa-edit'></i>";
                                        break;
                                        default:
                                            return "Erreur";
                                    }
                                    $view->dispatch(
                                        template_name : "badge",
                                        data : [
                                            "content" => $text,
                                            "color" => $color."-d-3",
                                            "tooltip" => $title,
                                            "style" => Style::STYLE_OUTLINE,
                                        ], 
                                        write: true);
                                }
                            } ?>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_ICON:
                    ob_start(); ?>
                        <div class="m-3">
                            <?php foreach ($rights as $right_name => $value) {
                                if(isset(Module::USER_RIGHT[$right_name]) && in_array($value, User::RIGHT_TYPE)){
                                    switch ($value) {
                                        case self::RIGHT_NO:
                                            $color = "grey";
                                            $title = "Aucun droit concernant les ".$right_name;
                                            $icon = "exclamation-triangle";
                                        break;
                                        case self::RIGHT_READ:
                                            $color = "blue";
                                            $title = "Lecture seule concernant les ".$right_name;
                                            $icon = "book-open";
                                        break;
                                        case self::RIGHT_WRITE:
                                            $color = "green";
                                            $title = "Lecture et écriture concernant les ".$right_name;
                                            $icon = "edit";
                                        break;
                                        default:
                                            return "Erreur";
                                    }
                                    $view->dispatch(
                                        template_name : "icon",
                                        data : [
                                            "style" => Style::ICON_SOLID,
                                            "content" => ucfirst($right_name),
                                            "content_placement" => Style::POSITION_LEFT,
                                            "icon" => $icon,
                                            "color" => $color."-d-3",
                                            "tooltip" => $title
                                        ], 
                                        write: true);
                                }
                            } ?>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_ARRAY:
                    return $rights;
                
                default:
                    return $this->_rights;
            }
        }
        
        public function isConnect(){
            if($this->getEmail() != ""){
                return true;
            }
            return false;
        }

        public function getCookie($type){
            if(isset($this->_cookie[$type])){
                return $this->_cookie[$type];
            } else {
                return false;
            }
        }
        public function getBookmark($format = Content::FORMAT_BRUT){
            $view = new View();
            $bookmarks = $this->_bookmark;

            switch ($format) {
                case Content::FORMAT_ARRAY:
                    return $bookmarks;
                break;

                case Content::DISPLAY_CARD:
                    ob_start(); 
                    if(!empty($bookmarks)){?>
                        <div class="d-flex flex-row flex-wrap align-content-stretch justify-content-start ">
                            <?php foreach ($bookmarks as $bookmark) { ?>
                                <div class="mx-3 my-1">
                                    <?= $bookmark->getVisual(Content::DISPLAY_RESUME); ?>
                                </div>                       
                            <?php } ?>
                        </div>
                    <?php }
                    return ob_get_clean();
                break;

                case Content::FORMAT_TEXT:
                    $bookmark_minified = [];
                    foreach ($bookmarks as $bookmark) {
                        if(is_object($bookmark)){
                            $bookmark_minified[get_class($bookmark) ."-".$bookmark->getUniqid()] = [
                                "classe" => get_class($bookmark),
                                "uniqid" => $bookmark->getUniqid()
                            ];
                        }
                    }
                    return $bookmark_minified;
                
                default:
                    return $bookmarks;
                break;
            }
        }
        public function in_bookmark(Object $object){
            $bookmarks = $this->getBookmark(Content::FORMAT_ARRAY);
            foreach ($bookmarks as $bookmark) {
                if($bookmark->getUniqid() == $object->getUniqid() && get_class($bookmark) == get_class($object)){
                    return true;
                }
            }
            return false;
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setToken($data){
            $this->_token = $data;
            return true;
        }    
        public function setLast_connexion($data = ''){
            if(empty($data)){
                $this->_last_connexion = time();
            } else {
                $date = new DateTime();
                if($this->isTimestamp($data)){
                    $date->setTimestamp(intval($data));
                }else {
                    try {
                        $date = new DateTime($data);
                    } catch (\Exception $e) {
                        return 'La derrière connexion est incorrecte (format ?)';
                    }
                }
                $this->_last_connexion = $date->format('U');
                return true;
            }
        }
        public function setEmail($data){
            if($this->isEmail($data)){
            $this->_email = $data;
            return true;
            } else {
                throw new Exception("L'email n'est pas valide");
            }
        }
        public function setPseudo($data){
            $this->_pseudo = $data;
            return true;
        }
        public function setHash($data){
            $this->_hash = $data;
            return true;
        }
        public function setIs_admin($data){
            $this->_is_admin = $this->returnBool($data);
            return true;
        }

        public function setRights(array | string | null $data = null, string | null $right = null, int $value = User::RIGHT_NO){
            $new_right = array();
            if(is_array($data)){
                // Si ça vient de l'update du controller
                if(isset(Module::USER_RIGHT[reset($data)]) && in_array($data[1], User::RIGHT_TYPE)){
                    $new_right[$data[0]] = $data[1];
                } else {
                    // Si data est un tableau de droits composé du nom du droit en key et de la valeur en value
                    foreach ($data as $right_name => $data_value) {
                        if(isset(Module::USER_RIGHT[$right_name]) && in_array($data_value, User::RIGHT_TYPE)){
                            $new_right[$right_name] = $data_value;
                        }
                    }
                }
            } elseif(!empty($data) && @unserialize($data)) {
                // Si ça vient de la base de donnée
                $data_check = unserialize($data);
                foreach ($data_check as $right_name => $data_value) {
                    if(isset(Module::USER_RIGHT[$right_name]) && in_array($data_value, User::RIGHT_TYPE)){
                        $new_right[$right_name] = $data_value;
                    }
                }
            } elseif(empty($data) && empty($right) && empty($value)){
                $new_right = Module::USER_RIGHT;
            }

            // Si on veut modifier un droit en particulier
            if(isset(Module::USER_RIGHT[$right]) && in_array($value, User::RIGHT_TYPE)) {
                $new_right[$right] = $value;
            }
            if($this->isSerialized($this->getRights())){
                $old_right = unserialize($this->getRights());
            } else {
                $old_right = array();
            }
            if(is_array($old_right)){
                foreach ($old_right as $name => $value) {
                    if(!isset($new_right[$name])){
                        $new_right[$name] = $value;
                    }
                }
            }
            foreach (Module::USER_RIGHT as $name => $value) {
                if(!isset($new_right[$name])){
                    $new_right[$name] = $value;
                }
            }
            ksort($new_right); // Tri par ordre alphabétique les clés du tableau
            $this->_rights = serialize($new_right);
            return true;
        }

        public function setPassword($data){
            $this->_hash = password_hash($data, PASSWORD_BCRYPT);
            return true;
        }

        public function setCookie($type, string | bool $data){
            if(isset($this->_cookie[$type])){
                if($data == "false"){
                    $data = false;
                }else{
                    $data = true;
                }
                $this->_cookie[$type] = $data;
                return true;
            } else {
                return false;
            }
        }
        public function setBookmark(Object $obj, $action = "add"){ 
            $managerU = new UserManager;
            $name = get_class($obj)."-".$obj->getUniqid();
            switch ($action) {
                case 'add':
                    $this->_bookmark += [$name => $obj];
                    if(!empty($this->getEmail())){
                        if(!$managerU->existsBookmark($this, $obj)){
                            if($managerU->addBookmark($this, $obj)){
                                return true;
                            }else{
                                throw new Exception("Erreur lors de l'ajout du favoris");
                            }
                        }
                    }
                    return true;
                case "remove":
                    unset($this->_bookmark[$name]);
                    if(!empty($this->getEmail())){
                        if($managerU->existsBookmark($this, $obj)){
                            if($managerU->removeBookmark($this, $obj)){
                                return true;
                            }else{
                                throw new Exception("Erreur lors de la suppression du favoris");
                            }   
                        }
                    }
                    return true;
                default:
                    throw new Exception("L'action n'est pas valide");
            }
        }
        public function setBookmarkArray(Array $array){
            $this->_bookmark = $array;
        }
}