<?php
class User extends Content
{
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ CONSTANTES ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        const RIGHT_NO = 0;
        const RIGHT_READ = 1;
        const RIGHT_WRITE = 2;

        const COOKIE_REQUISITE = 0;
        const COOKIE_CONNEXION = 1;
        const COOKIE_GRIMOIRE = 2;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_token='';
        private $_email='';
        private $_pseudo='Invité';
        private $_hash='';
        private $_last_connexion='';

        private $_right_classe = self::RIGHT_NO;
        private $_right_consumable = self::RIGHT_NO;
        private $_right_item = self::RIGHT_NO;
        private $_right_mob = self::RIGHT_NO;
        private $_right_npc = self::RIGHT_NO;
        private $_right_page = self::RIGHT_NO;
        private $_right_section = self::RIGHT_NO;
        private $_right_shop = self::RIGHT_NO;
        private $_right_spell = self::RIGHT_NO;
        private $_right_user = self::RIGHT_NO;

        private $_cookie = [
            self::COOKIE_REQUISITE => true,
            self::COOKIE_CONNEXION => false,
            self::COOKIE_GRIMOIRE => false
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
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="size-0-9">Email</p>
                            <input 
                                onchange="User.update('<?=$this->getUniqid();?>', this, 'email');" 
                                placeholder="Email" 
                                maxlength="100"
                                type="mail" 
                                class="form-control form-control-sm" 
                                value="<?=$this->_email?>">
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_email;
            }
        }
        public function getPseudo(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="m-1">
                            <p class="size-0-9">Pseudo</p>
                            <input 
                                onchange="User.update('<?=$this->getUniqid();?>', this, 'pseudo');" 
                                placeholder="Pseudo" 
                                maxlength="120"
                                type="text" 
                                class="form-control form-control-sm" 
                                value="<?=$this->_pseudo?>">
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_pseudo;
            }
        }
        public function gethash(int $format = Content::FORMAT_BRUT){
            return $this->_hash;
        }

        public function getRight_classe(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div style="width:350px">
                                <label for="right_classe" class="form-label pe-2">Droits sur les classes</label><?=$this->getRight_classe(Content::FORMAT_BADGE)?>
                                <input onchange="changeRangeText(this);User.update('<?=$this->getUniqid();?>', this, 'right_classe');" type="range" class="form-range" value="<?=$this->_right_classe?>" min="0" max="2" step="1" id="right_classe">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    switch ($this->_right_classe) {
                        case self::RIGHT_NO:
                            $color = "grey";
                            $title = "Aucun droit sur les classes";
                            $text = "Classes <i class='fas fa-exclamation-triangle'></i>";
                        break;
                        case self::RIGHT_READ:
                            $color = "blue";
                            $title = "Lecture seule sur les classes";
                            $text = "Classes <i class='fas fa-book-open'></i>";
                        break;
                        case self::RIGHT_WRITE:
                            $color = "green";
                            $title = "Lecture et écriture sur les classes";
                            $text = "Classes <i class='fas fa-edit'></i>";
                        break;
                        default:
                            return "Erreur";
                    }

                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="<?=$title?>" class='badge-outline text-<?=$color?>-d-3 border-<?=$color?>-d-3'><?=$text?></span>
                    <?php return ob_get_clean();
                    
                default:
                    return $this->_right_classe;
            }
        }
        public function getRight_consumable(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div style="width:350px">
                                <label for="right_consumable" class="form-label pe-2">Droits sur les consommables</label><?=$this->getRight_consumable(Content::FORMAT_BADGE)?>
                                <input onchange="changeRangeText(this);User.update('<?=$this->getUniqid();?>', this, 'right_consumable');" type="range" class="form-range" value="<?=$this->_right_consumable?>" min="0" max="2" step="1" id="right_consumable">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    switch ($this->_right_consumable) {
                        case self::RIGHT_NO:
                            $color = "grey";
                            $title = "Aucun droit sur les consommables";
                            $text = "Consommables <i class='fas fa-exclamation-triangle'></i>";
                        break;
                        case self::RIGHT_READ:
                            $color = "blue";
                            $title = "Lecture seule sur les consommables";
                            $text = "Consommables <i class='fas fa-book-open'></i>";
                        break;
                        case self::RIGHT_WRITE:
                            $color = "green";
                            $title = "Lecture et écriture sur les consommables";
                            $text = "Consommables <i class='fas fa-edit'></i>";
                        break;
                        default:
                            return "Erreur";
                    }
                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="<?=$title?>" class='badge-outline text-<?=$color?>-d-3 border-<?=$color?>-d-3'><?=$text?></span>
                    <?php return ob_get_clean();

                default:
                    return $this->_right_consumable;
            }
        }
        public function getRight_item(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div style="width:350px">
                                <label for="right_item" class="form-label pe-2">Droits sur les équipements</label><?=$this->getRight_item(Content::FORMAT_BADGE)?>
                                <input onchange="changeRangeText(this);User.update('<?=$this->getUniqid();?>', this, 'right_item');" type="range" class="form-range" value="<?=$this->_right_item?>" min="0" max="2" step="1" id="right_item">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    switch ($this->_right_item) {
                        case self::RIGHT_NO:
                            $color = "grey";
                            $title = "Aucun droit sur les équipements";
                            $text = "Equipements <i class='fas fa-exclamation-triangle'></i>";
                        break;
                        case self::RIGHT_READ:
                            $color = "blue";
                            $title = "Lecture seule sur les équipements";
                            $text = "Equipements <i class='fas fa-book-open'></i>";
                        break;
                        case self::RIGHT_WRITE:
                            $color = "green";
                            $title = "Lecture et écriture sur les équipements";
                            $text = "Equipements <i class='fas fa-edit'></i>";
                        break;
                        default:
                            return "Erreur";
                    }
                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="<?=$title?>" class='badge-outline text-<?=$color?>-d-3 border-<?=$color?>-d-3'><?=$text?></span>
                    <?php return ob_get_clean();
                    
                default:
                    return $this->_right_item;
            }
        }
        public function getRight_mob(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div style="width:350px">
                                <label for="right_mob" class="form-label pe-2">Droits sur les créatures</label><?=$this->getRight_mob(Content::FORMAT_BADGE)?>
                                <input onchange="changeRangeText(this);User.update('<?=$this->getUniqid();?>', this, 'right_mob');" type="range" class="form-range" value="<?=$this->_right_mob?>" min="0" max="2" step="1" id="right_mob">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    switch ($this->_right_mob) {
                        case self::RIGHT_NO:
                            $color = "grey";
                            $title = "Aucun droit sur les créatures";
                            $text = "Créatures <i class='fas fa-exclamation-triangle'></i>";
                        break;
                        case self::RIGHT_READ:
                            $color = "blue";
                            $title = "Lecture seule sur les créatures";
                            $text = "Créatures <i class='fas fa-book-open'></i>";
                        break;
                        case self::RIGHT_WRITE:
                            $color = "green";
                            $title = "Lecture et écriture sur les créatures";
                            $text = "Créatures <i class='fas fa-edit'></i>";
                        break;
                        default:
                            return "Erreur";
                    }
                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="<?=$title?>" class='badge-outline text-<?=$color?>-d-3 border-<?=$color?>-d-3'><?=$text?></span>
                    <?php return ob_get_clean();

                default:
                    return $this->_right_mob;
            }
        }
        public function getRight_npc(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div style="width:350px">
                                <label for="right_npc" class="form-label pe-2">Droits sur les PNJ</label><?=$this->getRight_npc(Content::FORMAT_BADGE)?>
                                <input onchange="changeRangeText(this);User.update('<?=$this->getUniqid();?>', this, 'right_npc');" type="range" class="form-range" value="<?=$this->_right_npc?>" min="0" max="2" step="1" id="right_npc">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    switch ($this->_right_npc) {
                        case self::RIGHT_NO:
                            $color = "grey";
                            $title = "Aucun droit sur les PNJ";
                            $text = "PNJ <i class='fas fa-exclamation-triangle'></i>";
                        break;
                        case self::RIGHT_READ:
                            $color = "blue";
                            $title = "Lecture seule sur les PNJ";
                            $text = "PNJ <i class='fas fa-book-open'></i>";
                        break;
                        case self::RIGHT_WRITE:
                            $color = "green";
                            $title = "Lecture et écriture sur les PNJ";
                            $text = "PNJ <i class='fas fa-edit'></i>";
                        break;
                        default:
                            return "Erreur";
                    }
                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="<?=$title?>" class='badge-outline text-<?=$color?>-d-3 border-<?=$color?>-d-3'><?=$text?></span>
                    <?php return ob_get_clean();

                default:
                    return $this->_right_npc;
            }
        }   
        public function getRight_page(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div style="width:350px">
                                <label for="right_page" class="form-label pe-2">Droits sur les pages</label><?=$this->getRight_page(Content::FORMAT_BADGE)?>
                                <input onchange="changeRangeText(this);User.update('<?=$this->getUniqid();?>', this, 'right_page');" type="range" class="form-range" value="<?=$this->_right_page?>" min="0" max="2" step="1" id="right_page">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    switch ($this->_right_page) {
                        case self::RIGHT_NO:
                            $color = "grey";
                            $title = "Aucun droit sur les pages";
                            $text = "Pages <i class='fas fa-exclamation-triangle'></i>";
                        break;
                        case self::RIGHT_READ:
                            $color = "blue";
                            $title = "Lecture seule sur les pages";
                            $text = "Pages <i class='fas fa-book-open'></i>";
                        break;
                        case self::RIGHT_WRITE:
                            $color = "green";
                            $title = "Lecture et écriture sur les pages";
                            $text = "Pages <i class='fas fa-edit'></i>";
                        break;
                        default:
                            return "Erreur";
                    }
                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="<?=$title?>" class='badge-outline text-<?=$color?>-d-3 border-<?=$color?>-d-3'><?=$text?></span>
                    <?php return ob_get_clean();
                    
                default:
                    return $this->_right_page;
            }
        }
        public function getRight_section(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div style="width:350px">
                                <label for="right_section" class="form-label pe-2">Droits sur les sections</label><?=$this->getRight_section(Content::FORMAT_BADGE)?>
                                <input onchange="changeRangeText(this);User.update('<?=$this->getUniqid();?>', this, 'right_section');" type="range" class="form-range" value="<?=$this->_right_section?>" min="0" max="2" step="1" id="right_section">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    switch ($this->_right_section) {
                        case self::RIGHT_NO:
                            $color = "grey";
                            $title = "Aucun droit sur les sections";
                            $text = "Section <i class='fas fa-exclamation-triangle'></i>";
                        break;
                        case self::RIGHT_READ:
                            $color = "blue";
                            $title = "Lecture seule sur les sections";
                            $text = "Section <i class='fas fa-book-open'></i>";
                        break;
                        case self::RIGHT_WRITE:
                            $color = "green";
                            $title = "Lecture et écriture sur les sections";
                            $text = "Section <i class='fas fa-edit'></i>";
                        break;
                        default:
                            return "Erreur";
                    }
                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="<?=$title?>" class='badge-outline text-<?=$color?>-d-3 border-<?=$color?>-d-3'><?=$text?></span>
                    <?php return ob_get_clean();
                    
                default:
                    return $this->_right_section;
            }
        }
        public function getRight_shop(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div style="width:350px">
                                <label for="right_shop" class="form-label pe-2">Droits sur les hôtels de vente</label><?=$this->getRight_shop(Content::FORMAT_BADGE)?>
                                <input onchange="changeRangeText(this);User.update('<?=$this->getUniqid();?>', this, 'right_shop');" type="range" class="form-range" value="<?=$this->_right_shop?>" min="0" max="2" step="1" id="right_shop">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    switch ($this->_right_shop) {
                        case self::RIGHT_NO:
                            $color = "grey";
                            $title = "Aucun droit sur les hôtels de vente";
                            $text = "HdV <i class='fas fa-exclamation-triangle'></i>";
                        break;
                        case self::RIGHT_READ:
                            $color = "blue";
                            $title = "Lecture seule sur les hôtels de vente";
                            $text = "HdV <i class='fas fa-book-open'></i>";
                        break;
                        case self::RIGHT_WRITE:
                            $color = "green";
                            $title = "Lecture et écriture sur les hôtels de vente";
                            $text = "HdV <i class='fas fa-edit'></i>";
                        break;
                        default:
                            return "Erreur";
                    }
                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="<?=$title?>" class='badge-outline text-<?=$color?>-d-3 border-<?=$color?>-d-3'><?=$text?></span>
                    <?php return ob_get_clean();

                default:
                    return $this->_right_shop;
            }
        }
        public function getRight_spell(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div style="width:350px">
                                <label for="right_spell" class="form-label pe-2">Droits sur les sorts</label><?=$this->getRight_spell(Content::FORMAT_BADGE)?>
                                <input onchange="changeRangeText(this);User.update('<?=$this->getUniqid();?>', this, 'right_spell');" type="range" class="form-range" value="<?=$this->_right_spell?>" min="0" max="2" step="1" id="right_spell">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    switch ($this->_right_spell) {
                        case self::RIGHT_NO:
                            $color = "grey";
                            $title = "Aucun droit sur les sorts";
                            $text = "Sorts <i class='fas fa-exclamation-triangle'></i>";
                        break;
                        case self::RIGHT_READ:
                            $color = "blue";
                            $title = "Lecture seule sur les sorts";
                            $text = "Sorts <i class='fas fa-book-open'></i>";
                        break;
                        case self::RIGHT_WRITE:
                            $color = "green";
                            $title = "Lecture et écriture sur les sorts";
                            $text = "Sorts <i class='fas fa-edit'></i>";
                        break;
                        default:
                            return "Erreur";
                    }
                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="<?=$title?>" class='badge-outline text-<?=$color?>-d-3 border-<?=$color?>-d-3'><?=$text?></span>
                    <?php return ob_get_clean();
                    
                default:
                    return $this->_right_spell;
            }
        }
        public function getRight_user(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div style="width:350px">
                                <label for="right_user" class="form-label pe-2">Droits sur les utilisateurs</label><?=$this->getRight_user(Content::FORMAT_BADGE)?>
                                <input onchange="changeRangeText(this);User.update('<?=$this->getUniqid();?>', this, 'right_user');" type="range" class="form-range" value="<?=$this->_right_user?>" min="0" max="2" step="1" id="right_user">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    switch ($this->_right_user) {
                        case self::RIGHT_NO:
                            $color = "grey";
                            $title = "Aucun droit sur les utilisateurs";
                            $text = "Utilisateurs·trices  <i class='fas fa-exclamation-triangle'></i>";
                        break;
                        case self::RIGHT_READ:
                            $color = "blue";
                            $title = "Lecture seule sur les utilisateurs";
                            $text = "Utilisateurs·trices <i class='fas fa-book-open'></i>";
                        break;
                        case self::RIGHT_WRITE:
                            $color = "green";
                            $title = "Lecture et écriture sur les utilisateurs";
                            $text = "Utilisateurs·trices <i class='fas fa-edit'></i>";
                        break;
                        default:
                            return "Erreur";
                    }
                    ob_start(); ?>
                        <span data-bs-toggle='tooltip' data-bs-placement='top' title="<?=$title?>" class='badge-outline text-<?=$color?>-d-3 border-<?=$color?>-d-3'><?=$text?></span>
                    <?php return ob_get_clean();

                default:
                    return $this->_right_user;
            }
        }

        public function getPassword(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div>
                            <div class="form-group">
                                <label>Mot de passe actuel</label>
                                <input 
                                    id='password<?=$this->getId()?>' 
                                    placeholder="•••••••••" 
                                    type="password" 
                                    class="form-control" 
                                    maxlength="500" 
                                    value="">
                            </div>
                            <div class="form-group">
                                <label>Nouveau mot de passe</label>
                                <input 
                                    id='newpassword<?=$this->getId()?>' 
                                    placeholder="•••••••••" 
                                    type="password" 
                                    class="form-control" 
                                    maxlength="500" 
                                    value="">
                            </div>
                            <div class="form-group">
                                <label>Répéter le nouveau mot de passe</label>
                                <input 
                                    id='repeatnewpassword<?=$this->getId()?>' 
                                    placeholder="•••••••••" 
                                    type="password" 
                                    class="form-control" 
                                    maxlength="500" 
                                    value="">
                            </div>
                            <div class="text-center"><a class="btn btn-sm btn-border-main" onclick="User.updatePassword(<?=$this->getId()?>);">Modifier</a></div>
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return "";
            }
        }
        public function getRight($objet, int $right = User::RIGHT_WRITE){
            $method = "getRight_".strtolower($objet);
            if(method_exists($this,$method)){
                $right_val = $this->$method();
                if($right_val == self::RIGHT_WRITE){
                    return true;
                }elseif($right_val == self::RIGHT_READ){
                    if($right_val == $right || $right == self::RIGHT_NO){
                        return true;
                    } else {
                        return false;
                    }
                }elseif($right_val == self::RIGHT_NO){
                    if($right == self::RIGHT_NO){
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }

            }else{
                return "Ce droit n'existe pas";
            }
        }

        public function getRights(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                      ob_start(); ?>
                        <div class="m-3">
                           <?php foreach (get_class_methods($this) as $value) {
                                if(strpos($value, "getRight_") !== false){
                                    echo $this->$value(Content::FORMAT_MODIFY);
                                }
                            } ?>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-between">
                            <?php foreach (get_class_methods($this) as $value) {
                                if(strpos($value, "getRight_") !== false){
                                    echo $this->$value(Content::FORMAT_BADGE);
                                }
                            } ?>
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return "";
            }
        }
        
        public function getVisual(int $format = Content::FORMAT_BRUT){

            switch ($format) {
                case Content::FORMAT_MODIFY:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <p class='size-0-7 mb-1'>Utilisateur <?=$this->getId(Content::FORMAT_BADGE);?> | Créé le <?=$this->getTimestamp_add(Content::DATE_FR);?> | Dernière connexion le <?=$this->getLast_connexion(Content::DATE_FR);?> à <?=$this->getLast_connexion(Content::TIME_FR);?></p>
                            <?=$this->getPseudo(Content::FORMAT_MODIFY)?>
                            <?=$this->getEmail(Content::FORMAT_MODIFY)?>
                            <?php if($this->getRight("user", User::RIGHT_WRITE)){ ?>
                                <h3>ToolsBox</h3>
                                <div class="flex-row justify-content-start align-item-baseline mb-2">
                                    <a onclick="Tools.req('savedb', '<?=$this->generateAndSaveToken()?>');" class="btn-sm btn btn-back-main">Sauver la base de donnée</a>
                                    <a onclick="Tools.req('verifAndCreatePageNeeded', '<?=$this->generateAndSaveToken()?>');" class="btn-sm btn btn-back-main">Vérifier et créer les pages obligatoires</a>
                                </div>
                               
                                <h3>Modifier les droits</h3>
                                <?=$this->getRights(Content::FORMAT_MODIFY)?>
                            <?php } ?>
                            <h3>Modifier le mot de passe</h3>
                            <?=$this->getPassword(Content::FORMAT_MODIFY)?>
                        </div>
                    <?php return ob_get_clean();

                case  Content::FORMAT_CARD:      
                    ob_start(); ?>
                        <div class="card mb-3 p-3">
                            <p class='size-0-7 mb-1'>Utilisateur <?=$this->getId(Content::FORMAT_BADGE);?> | Créé le <?=$this->getTimestamp_add(Content::DATE_FR);?> | Dernière connexion le <?=$this->getLast_connexion(Content::DATE_FR);?> à <?=$this->getLast_connexion(Content::TIME_FR);?></p>
                            <p><?=$this->getPseudo()?> | <?=$this->getEmail()?></p>
                            <h3>Droits</h3>
                            <?php if($this->getRight("user", User::RIGHT_WRITE)){ ?>
                                <?=$this->getRights(Content::FORMAT_BADGE)?>
                            <?php } ?>
                        </div>
                    <?php return ob_get_clean();
            }

        }
        public function isConnect(){
            if($this->getEmail() != ""){
                return true;
            }
            return false;
        }

        public function getCookie($type){
            if(isset($this->_cookie[$type]) && !empty($this->_cookie[$type])){
                return $this->returnBool($this->_cookie[$type]);
            } else {
                return false;
            }
        }
        public function getBookmark($format = Content::FORMAT_BRUT){
            $bookmarks = $this->_bookmark;

            switch ($format) {
                case Content::FORMAT_ARRAY:
                    return $bookmarks;
                break;

                case Content::FORMAT_CARD:
                    ob_start(); 
                    if(!empty($bookmarks)){?>
                        <div class="d-flex flex-row flex-wrap align-content-stretch justify-content-start ">
                            <?php foreach ($bookmarks as $bookmark) { ?>
                                <div class="m-3 position-relative">
                                    <a style="position:absolute;top:5px;right:5px;z-index:1;" onclick="User.changeBookmark(this);" data-classe='<?=strtolower(get_class($bookmark))?>' data-uniqid="<?=$bookmark->getUniqid()?>" title="Détacher du grimoire"><i class="fas fa-bookmark text-main-d-2 text-main-hover"></i></a>
                                    <?= $bookmark->getVisual(Content::FORMAT_RESUME); ?>
                                </div>
                                <div class="nav-item-divider back-secondary-d-2"></div>                             
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

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setToken($data){
            $this->_token = $data;
            return "success";
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
                return "success";
            }
        }
        public function setEmail($data){
            $this->_email = $data;
            return "success";
        }
        public function setPseudo($data){
            $this->_pseudo = $data;
            return "success";
        }
        public function setHash($data){
            $this->_hash = $data;
            return "success";
        }

        public function setRight_classe($data){
            if($data == self::RIGHT_NO || self::RIGHT_READ || self::RIGHT_WRITE){
                $this->_right_classe = $data;
                return "success";
            } else {
                $this->_right_classe = self::RIGHT_NO;
                return "right_classe est incorrect";
            }
        }
        public function setRight_consumable($data){
            if($data == self::RIGHT_NO || self::RIGHT_READ || self::RIGHT_WRITE){
                $this->_right_consumable = $data;
                return "success";
            } else {
                $this->_right_consumable = self::RIGHT_NO;
                return "right_consumable est incorrect";
            }
        }
        public function setRight_item($data){
            if($data == self::RIGHT_NO || self::RIGHT_READ || self::RIGHT_WRITE){
                $this->_right_item = $data;
                return "success";
            } else {
                $this->_right_item = self::RIGHT_NO;
                return "right_item est incorrect";
            }
        }
        public function setRight_mob($data){
            if($data == self::RIGHT_NO || self::RIGHT_READ || self::RIGHT_WRITE){
                $this->_right_mob = $data;
                return "success";
            } else {
                $this->_right_mob = self::RIGHT_NO;
                return "right_mob est incorrect";
            }
        }
        public function setRight_npc($data){
            if($data == self::RIGHT_NO || self::RIGHT_READ || self::RIGHT_WRITE){
                $this->_right_npc = $data;
                return "success";
            } else {
                $this->_right_npc = self::RIGHT_NO;
                return "right_npc est incorrect";
            }
        }
        public function setRight_page($data){
            if($data == self::RIGHT_NO || self::RIGHT_READ || self::RIGHT_WRITE){
                $this->_right_page = $data;
                return "success";
            } else {
                $this->_right_page = self::RIGHT_NO;
                return "right_page est incorrect";
            }
        }
        public function setRight_section($data){
            if($data == self::RIGHT_NO || self::RIGHT_READ || self::RIGHT_WRITE){
                $this->_right_section = $data;
                return "success";
            } else {
                $this->_right_section = self::RIGHT_NO;
                return "right_section est incorrect";
            }
        }
        public function setRight_shop($data){
            if($data == self::RIGHT_NO || self::RIGHT_READ || self::RIGHT_WRITE){
                $this->_right_shop = $data;
                return "success";
            } else {
                $this->_right_shop = self::RIGHT_NO;
                return "right_shop est incorrect";
            }
        }
        public function setRight_spell($data){
            if($data == self::RIGHT_NO || self::RIGHT_READ || self::RIGHT_WRITE){
                $this->_right_spell = $data;
                return "success";
            } else {
                $this->_right_spell = self::RIGHT_NO;
                return "right_spell est incorrect";
            }
        }
        public function setRight_user($data){
            if($data == self::RIGHT_NO || self::RIGHT_READ || self::RIGHT_WRITE){
                $this->_right_user = $data;
                return "success";
            } else {
                $this->_right_user = self::RIGHT_NO;
                return "right_user est incorrect";
            }
        }

        public function setPassword($data){
            $this->_hash = password_hash($data, PASSWORD_BCRYPT);
            return "success";
        }

        public function setCookie($type, bool $data){
            if(isset($this->_cookie[$type])){
                $this->_cookie[$type] = $this->returnBool($data);
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
                                return "Erreur lors de l'ajout du favoris";
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
                                return "Erreur lors de la suppression du favoris";
                            }   
                        }
                    }
                    return true;
                default:
                    return "L'action n'est pas valide";
            }
        }
        public function setBookmarkArray(Array $array){
            $this->_bookmark = $array;
        }
}