<?php
class Consumable extends Content
{
    public function __construct(array $donnees){
        $this->hydrate($donnees);
        if(file_exists("medias/consumables/".$this->getUniqid().".svg")){
            $this->setPath_image("medias/consumables/".$this->getUniqid().".svg");
        }
    }

    const TYPE_FOOD = 3;
    const TYPE_POTION = 1;
    const TYPE_TREAT = 2;
    const TYPE_PARCHMENT = 4;
    const TYPE_STONE= 5;

    const TYPE_LIST = [
        "nourriture" => Consumable::TYPE_FOOD,
        "potion" => Consumable::TYPE_POTION,
        "confiserie" => Consumable::TYPE_TREAT,
        "parchemin" => Consumable::TYPE_PARCHMENT,
        "pierre" => Consumable::TYPE_STONE
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_type=Consumable::TYPE_FOOD;
        private $_name='';
        private $_description='';
        private $_effect="";
        private $_level=1;
        private $_recepe="";
        private $_price=0;
        private $_rarity=5;
        private $_path_img="medias/consumables/default.svg";
        private $_usable=false;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getType(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="dropdown">
                            <a class="" type="button" id="dropdownDisplay<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getType(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                            <div class="dropdown-menu" aria-labelledby="dropdownDisplay<?=$this->getId()?>">
                                <?php foreach (Consumable::TYPE_LIST as $key => $type) { ?>
                                    <a class="dropdown-item" onclick="Consumable.update('<?=$this->getUniqid()?>', <?=$type?>, 'type', <?=Controller::IS_VALUE?>);$('#dropdownDisplay<?=$this->getId()?>').html($(this).html());"><span class='badge back-<?=View::getColorFromLetter($type)?>-d-2'><?=ucfirst($key)?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_BADGE:
                    if(in_array($this->_type, Consumable::TYPE_LIST)){
                        return "<span class='badge back-".View::getColorFromLetter($this->_type)."-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Type de consommable\">".array_search($this->_type, Consumable::TYPE_LIST)."</span>";
                    } else  {
                        return "";
                    }

                default:
                    return $this->_type;
            }
        }
        public function getName(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <input 
                                onchange="Consumable.update('<?=$this->getUniqid();?>', this, 'name');" 
                                placeholder="Nom" 
                                maxlength="300" 
                                type="text" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_name?>">
                            <label class="size-0-8">Nom</label>
                        </div>
                    <?php return ob_get_clean();
                
                default:
                    return $this->_name;
            }

        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <textarea 
                                placeholder=""
                                onchange="Consumable.update('<?=$this->getUniqid();?>', this, 'description');" 
                                class="form-control form-control-main-focus" 
                                maxlength="20000"><?=$this->_description?></textarea>
                            <label class="size-0-8">Description</label>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_description;
            }
        }
        public function getEffect(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <textarea 
                                placeholder=""
                                onchange="Consumable.update('<?=$this->getUniqid();?>', this, 'effect');" 
                                class="form-control form-control-main-focus" 
                                maxlength="20000"><?=$this->_effect?></textarea>
                            <label class="size-0-8">Effets</label>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_effect;
            }
        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-<?=View::getColorFromLetter($this->_level, true)?>-d-3">
                            <label>Niveau du consommable</label>
                            <input 
                                onchange="Consumable.update('<?=$this->getUniqid();?>', this, 'level');" 
                                data-bs-toggle='tooltip' data-bs-placement='bottom' title="Niveau à partir duquel il est possible d'apprendre d'utiliser le consommable"
                                min="0" max="200" 
                                type="number" 
                                class="form-control form-control-main-focus" 
                                value="<?=$this->_level?>">
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-".View::getColorFromLetter($this->_level, true)."-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Niveau à partir duquel il est possible d'utiliser le consommable\">Niveau {$this->_level}</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-".View::getColorFromLetter($this->_level, true)."-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Niveau à partir duquel il est possible d'utiliser le consommable\">{$this->_level}</span>";
                
                default:
                    return $this->_level;
            }
        }
        public function getRecepe(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="form-floating mb-1">
                            <textarea 
                                placeholder=""
                                onchange="Consumable.update('<?=$this->getUniqid();?>', this, 'recepe');" 
                                class="form-control form-control-main-focus" 
                                maxlength="20000"><?=$this->_recepe?></textarea>
                            <label class="size-0-8">Recette</label>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_recepe;
            }
        }
        public function getPrice(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="mb-1 text-po-d-4">
                            <label>Prix estimé</label>
                            <div class="input-group">
                                <div class="input-group-text back-kamas text-white">K</div>
                                <input 
                                    onchange="Consumable.update('<?=$this->getUniqid();?>', this, 'price');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Prix estimé"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_price?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-kamas-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Prix estimé du consommable\">{$this->_price} kamas</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-po-d-4' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Prix estimé du consommable\">{$this->_price} <img class='icon' src='medias/icons/kamas.png'></span>";
                
                default:
                    return $this->_price;
            }
        }
        public function getRarity(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="dropdown">
                            <a class="" type="button" id="rarity<?=$this->getId()?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$this->getRarity(Content::FORMAT_BADGE)?> <i class="fas fa-chevron-down font-size-0-8 text-grey"></i></a>
                            <div class="dropdown-menu" aria-labelledby="rarity<?=$this->getId()?>"> <?php
                                foreach (Item::RARITY_LIST as $name => $rarity) { ?>
                                    <a class="dropdown-item" onclick="Consumable.update('<?=$this->getUniqid()?>', <?=$rarity?>, 'rarity', <?=Controller::IS_VALUE?>);$('#rarity<?=$this->getId()?>').html($(this).html());"><span class='badge back-<?=View::getColorFromLetter($name)?>-d-2'><?=$name?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php return ob_get_clean();
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_rarity, Item::RARITY_LIST)){
                        return "<span class='badge back-".View::getColorFromLetter(array_search($this->_rarity, Item::RARITY_LIST))."-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Rareté de l'équipement\">".array_search($this->_rarity, Item::RARITY_LIST)."</span>";
                    } else  {
                        return "";
                    }

                default:
                    return $this->_rarity;
            }
        }
        public function getPath_img(int $format = Content::FORMAT_BRUT, $css = ""){
            if(file_exists("medias/consumables/".$this->getUniqid().".svg")){
                $this->setPath_image("medias/consumables/".$this->getUniqid().".svg");
            }

            if(!empty($this->_path_img) || file_exists($this->_path_img)){
                $path = $this->_path_img;
            } else {
                $path = "medias/consumables/default.svg";
            }

            switch ($format) {
                case  Content::FORMAT_BRUT:
                    return $path;
                break;
                case  Content::FORMAT_MODIFY:
                    ob_start(); ?>
                        <div class="text-center">
                            <div id='showFile_path_image' class='m-2 d-flex justify-content-center'><?=$this->getPath_img(Content::FORMAT_FANCY, 'img-back-180');?></div>
                            <a type="button" 
                                class="btn btn-border-secondary btn-sm me-2" 
                                onclick=''>
                                <i class="fas fa-cloud-upload-alt"></i> Modifier
                            </a>
                        </div>
                    <?php return ob_get_clean();
                break;
                case  Content::FORMAT_IMAGE:
                    if($css == "") { $css = "img-back-90 rounded-circle";}
                    ob_start(); ?>
                        <div class='<?=$css?>' style="background-image:url('<?=$path?>')"></div>
                    <?php return ob_get_clean();
                break;
                case Content::FORMAT_FANCY:
                    $svg_plugin = "";
                    if(strtolower(pathinfo($path)['extension']) == "svg"){
                        $svg_plugin = "data-type='iframe'";
                    }
                    if($css == "") { $css = "img-back-30 rounded-circle";}
                    ob_start(); ?>
                        <a data-fancybox="gallery" <?=$svg_plugin?> href='<?=$path?>' class="d-flex justify-content-center"><div class='<?=$css?>' style='background-image:url("<?=$path?>")'></div></a>
                    <?php return ob_get_clean();
                break;
                
                default:
                    return $path;
                break;
            }
        }
        public function getUsable(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_MODIFY:
                    $checked = ""; 
                    if($this->_usable){ $checked = "checked"; } else { $checked = ""; }
                    ob_start(); ?>
                        <div style="width:initial;" class="form-check form-switch my-1">
                            <input onchange="Consumable.update('<?=$this->getUniqid();?>', this, 'usable', <?=Controller::IS_CHECKBOX?>);"  type="checkbox" class="form-check-input back-main-d-1 border-main-d-1" <?=$checked?> id="customSwitchUsable<?=$this->getId()?>">
                            <label class="custom-control-label" for="customSwitchUsable<?=$this->getUniqid()?>"><?=$this->getUsable(Content::FORMAT_BADGE);?></label>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if($this->_usable){ 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title=\"L'objet a été adapté au jdr\" class='badge back-green-d-3'>Adapté au jdr</span>";
                    } else { 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title=\"L'objet n'a pas encore été adapté au jdr - N'hésitez pas à le modifier\" class='badge back-red-d-3'>Non adapté au jdr</span>";
                    }

                case Content::FORMAT_ICON:
                    if($this->_usable){ 
                        return "<i data-bs-toggle='tooltip' data-bs-placement='top' title=\"L'objet a été adapté au jdr\" class='fas fa-check text-green-d-3'></i>";
                    } else { 
                        return "<i data-bs-toggle='tooltip' data-bs-placement='top' title=\"L'objet n'a pas encore été adapté au jdr - N'hésitez pas à le modifier\" class='fas fa-times text-red-d-3'></i>";
                    }
                    
                default:
                    return $this->_usable;
            }
        }

        public function getVisual(int $display = Content::DISPLAY_CARD, int $size = 300){
            $user = ControllerConnect::getCurrentUser();
            $bookmark_icon = "far";
            if($user->in_bookmark($this)){
                $bookmark_icon = "fas";
            }

            //OPTIONS
            if($size < 100){$size = 300;}

            switch ($display) {
                case Content::DISPLAY_MODIFY:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-auto"><?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-150")?></div>
                                <div class="col">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-auto">
                                                <div class="d-flex justify-content-start flex-wrap">
                                                    <div class="me-4"><?=$this->getLevel(Content::FORMAT_MODIFY)?></div>
                                                    <div><?=$this->getPrice(Content::FORMAT_MODIFY)?></div>
                                                </div>   
                                            </div>
                                            <div class="col-auto d-flex flex-column justify-content-center">
                                                <?=$this->getType(Content::FORMAT_MODIFY)?>
                                                <?=$this->getRarity(Content::FORMAT_MODIFY)?>
                                            </div>  
                                            <div class="col-auto ms-auto">
                                                <?=$this->getUsable(Content::FORMAT_MODIFY)?>
                                            </div>   
                                        </div>
                                    </div>
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <p class='size-0-7 mb-2'>Consommable <?=$this->getId(Content::FORMAT_BADGE);?> | Créé le <?=$this->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$this->getTimestamp_updated(Content::DATE_FR);?></p>
                                </div>
                            </div>
                            <p class="card-text m-3"><?=$this->getEffect(Content::FORMAT_MODIFY);?></p>
                            <p class="card-text m-3"><?=$this->getDescription(Content::FORMAT_MODIFY);?></p>
                            <p class="card-text m-3"><?=$this->getRecepe(Content::FORMAT_MODIFY);?></p>
                            <p class="text-right font-size-0-8 m-1"><a class='text-red-d-2 text-red-l-3-hover' onclick="Consumable.remove('<?=$this->getUniqid()?>')"><i class="fas fa-trash"></i> Supprimer</a></p>
                        </div>
                    <?php return ob_get_clean();
                break;

                case  Content::DISPLAY_CARD:      
                    ob_start(); ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-auto">
                                    <a style="position:relative;top:5px;left:5px;" href="<?=$this->getPath_img()?>" download="<?=$this->getName().'.'.substr(strrchr($this->getPath_img(),'.'),1);?>"><i class="fas fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
                                    <?=$this->getPath_img(Content::FORMAT_FANCY, "img-back-150")?>
                                </div>
                                <div class="col">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <p class="d-flex flex-row justify-content-start flex-wrap">
                                                    <span class="m-1"><?=$this->getType(Content::FORMAT_BADGE)?></span>
                                                    <span class="m-1"><?=$this->getLevel(Content::FORMAT_BADGE)?></span>
                                                    <span class="m-1"><?=$this->getPrice(Content::FORMAT_BADGE)?></span>
                                                    <span class="m-1"><?=$this->getRarity(Content::FORMAT_BADGE)?></span>
                                                </p>
                                            </div>
                                            <div class="col-auto">
                                                <?=$this->getUsable(Content::FORMAT_BADGE)?>
                                                <?php if($user->getRight('consumable', User::RIGHT_WRITE)){ ?>
                                                    <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Consumable.open('<?=$this->getUniqid()?>', Controller.DISPLAY_MODIFY);"><i class='far fa-edit'></i></a>
                                                <?php } ?>
                                            </div>                     
                                        </div>
                                        <div class="nav-item-divider back-main-d-1"></div>
                                        <h5 class="card-title"><?=$this->getName()?></h5>
                                        <p class="card-text"><?=$this->getEffect()?></p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="card-text"><small class="text-muted"><?=$this->getDescription()?></small></p>
                                <?php if(!empty($this->getRecepe())){ ?>
                                    <div class="nav-item-divider back-main-d-1"></div>
                                    <p class="card-text"><small class="text-muted"><?=$this->getRecepe()?></small></p>
                                <?php } ?>
                            </div>
                        </div>
                    <?php return ob_get_clean();
                break;

                case Content::DISPLAY_RESUME:
                    ob_start(); ?>
                        <div style="width: <?=$size?>px;">
                            <div style="position:relative;">
                                <div ondblclick="Consumable.open('<?=$this->getUniqid()?>');" class="card-hover-linked card border-secondary-d-2 border p-2 m-1" style="width: <?=$size?>px;" >
                                    <div class="row">
                                        <div class="col-auto">
                                            <?=$this->getPath_img(Content::FORMAT_IMAGE, "img-back-50")?>
                                        </div>
                                        <div class="col">
                                            <p class="bold ms-1"><?=$this->getName()?></p>
                                            <p class="row">
                                                <span class="col-auto me-1 mt-1 short-badge-150"><?=$this->getType(Content::FORMAT_BADGE)?></span>
                                                <span class="col-auto me-1 mt-1 short-badge-150"><?=$this->getLevel(Content::FORMAT_BADGE)?></span>
                                                <span class="col-auto me-1 mt-1 short-badge-150"><?=$this->getPrice(Content::FORMAT_BADGE)?></span>
                                                <span class="col-auto me-1 mt-1 short-badge-150"><?=$this->getRarity(Content::FORMAT_BADGE)?></span>
                                            </p>
                                        </div>
                                        <div class="col-auto d-flex flex-column justify-content-between ms-auto">
                                            <a onclick='User.changeBookmark(this);' data-classe='consumable' data-uniqid='<?=$this->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                                        </div>
                                    </div>
                                    <div class="card-hover-showed">
                                        <p class="card-text"><?=$this->getEffect()?></p>
                                        <div class="nav-item-divider back-main-d-1"></div>
                                        <p class="card-text"><small class="size-0-9 text-secondary-d-3"><?=$this->getDescription()?></small></p>
                                        <p class="card-text"><small class="text-muted size-0-7 text-grey-d-2"><?=$this->getRecepe()?></small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php return ob_get_clean();
                break;

                default:
                    return "Erreur : format de display non reconnu";
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setType($data){
            if(in_array($data, Consumable::TYPE_LIST)){
                $this->_type = $data;
                return true;
            } else {
                return "Type est incorrect";
            }
        }
        public function setName($data){
            $this->_name = $data;
            return true;
        }
        public function setDescription($data){
            $this->_description = $data;
            return true;
        }
        public function setEffect($data){
            $this->_effect = $data;
            return true;
        }
        public function setLevel($data){
            if(is_numeric($data)){
                $this->_level = $data;
                return true;
            } else {
                return "La valeur doit être un nombre";
            }
        }
        public function setRecepe($data){
            $this->_recepe = $data;
            return true;
        }
        public function setPrice($data){
            $this->_price = $data;
            return true;
        }
        public function setRarity($data){
            if(in_array($data, Item::RARITY_LIST)){
                $this->_rarity = $data;
                return true;
            } else {
                $this->_rarity = Item::RARITY_LIST["Très répandu"];
                return "Erreur : donnée non valide;";
            }
        }
        public function setPath_image($data){
            if(is_file($data)){
                $file = New File($data);
                if(FileManager::isImage($file)){
                    $this->_path_img = $data;
                    return true;
                } else {
                    return "Le fichier doit être une image.";
                }
            } else {
                return "Le fichier n'est pas valide.";
            }
        }
        public function setUsable($data){
            $this->_usable = $this->returnBool($data);
            return true;
        }
}
