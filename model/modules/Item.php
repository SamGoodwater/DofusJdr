<?php
class Item extends Content
{
    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/items/default.svg",
            "dir" => "medias/modules/items/",
            "preferential format" => "svg",
            "naming" => "[uniqid]"
        ]
    ];

    const TYPE_LIST = [
        "Arcs" => Item::TYPE_ARC,
        "Baguettes" => Item::TYPE_BAGUETTE,
        "Bâtons" => Item::TYPE_BATON,
        "Dagues" => Item::TYPE_DAGUE,
        "Epées" => Item::TYPE_EPEE,
        "Marteaux" => Item::TYPE_MARTEAU,
        "Pelles" => Item::TYPE_PELLE,
        "Haches" => Item::TYPE_HACHE,
        "Outils" => Item::TYPE_OUTIL,
        "Pioches" => Item::TYPE_PIOCHE,
        "Faux" => Item::TYPE_FAUX,
        "Arbalètes" => Item::TYPE_ARBALETE,
        "Armes magiques" => Item::TYPE_ARME_MAGIQUE,
        "Chapeaux" => Item::TYPE_CHAPEAU,
        "Capes" => Item::TYPE_CAPE,
        "Amulettes" => Item::TYPE_AMULETTE,
        "Anneaux" => Item::TYPE_ANNEAU,
        "Ceintures" => Item::TYPE_CEINTURE,
        "Bottes" => Item::TYPE_BOTTES,
        "Boucliers" => Item::TYPE_BOUCLIER,
        "Dofus" => Item::TYPE_DOFUS,       
        "Familiers" => Item::TYPE_FAMILIER,
        "Montures" => Item::TYPE_MONTURE
    ];

    const TYPE_ARC = 23;
    const TYPE_BAGUETTE = 1;
    const TYPE_BATON = 2;
    const TYPE_DAGUE = 3;
    const TYPE_EPEE = 4;
    const TYPE_MARTEAU = 5;
    const TYPE_PELLE = 6;
    const TYPE_HACHE = 7;
    const TYPE_OUTIL = 8;
    const TYPE_PIOCHE = 9;
    const TYPE_FAUX = 10;
    const TYPE_ARBALETE = 11;
    const TYPE_ARME_MAGIQUE = 12;

    const TYPE_CHAPEAU = 13;
    const TYPE_CAPE = 14;
    const TYPE_AMULETTE = 15;
    const TYPE_ANNEAU = 16;
    const TYPE_CEINTURE = 17;
    const TYPE_BOTTES = 18;
    const TYPE_BOUCLIER = 19;
    const TYPE_DOFUS = 20;

    const TYPE_FAMILIER = 21;
    const TYPE_MONTURE = 22;

    const RARITY_LIST = [
        "Unique" => 0,
        "Mythique" => 1,
        "Rare" => 2,
        "Inhabituel" => 3,
        "Commun" => 4,
        "Très répandu" => 5
    ];


    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_level="";
        private $_description="";
        private $_effect="";
        private $_type=Item::TYPE_ARC;
        private $_recepe="";
        private $_price=0;
        private $_rarity=5;

        private $_actif = false;
        private $_twohands = false;
        private $_pa=0;
        private $_po=0;

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Item",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom",
                            "placeholder" => "Nom de l'équipement",
                            "value" => $this->_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Item",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "level",
                            "label" => "Niveau",
                            "placeholder" => "Niveau de l'équipement",
                            "tooltip" => "Niveau à partir duquel il est possible de porter l'équipement",
                            "value" => $this->_level,
                            "color" => Style::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Niveau {$this->_level}",
                            "color" => Style::getColorFromLetter($this->_level) . "-d-3",
                            "tooltip" => "Niveau à partir duquel il est possible de porter l'équipement",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_level,
                            "color" => "",
                            "tooltip" => "Niveau à partir duquel il est possible de porter l'équipement",
                            "style" => Style::STYLE_NONE,
                            "class" => "text-".Style::getColorFromLetter($this->_level) . "-d-3"
                        ], 
                        write: false);
                
                default:
                    return $this->_level;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Item",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "description",
                            "label" => "Description",
                            "maxlenght" => "2000",
                            "placeholder" => "",
                            "value" => $this->_description
                        ], 
                        write: false);

                default:
                    return $this->_description;
            }
        }
        public function getEffect(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Item",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "effect",
                            "label" => "Effets",
                            "maxlenght" => "1000",
                            "tooltip" => "Effets de l'équipement",
                            "placeholder" => "",
                            "value" => $this->_effect
                        ], 
                        write: false);

                default:
                    return $this->_effect;
            }
        }
        public function getRecepe(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => "Item",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "recepe",
                            "label" => "Recette",
                            "maxlenght" => "1000",
                            "placeholder" => "",
                            "tooltip" => "Recette de fabrication de l'équipement",
                            "value" => $this->_recepe
                        ], 
                        write: false);

                default:
                    return $this->_recepe;
            }
        }
        public function getType(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Item::TYPE_LIST as $name => $type) {
                        $items[] = [
                            "display" => ucfirst($name),
                            "onclick" => "Item.update('".$this->getUniqid()."', '".$type."', 'type',".Controller::IS_VALUE.")",
                            "class" => "badge back-".Style::getColorFromLetter($type)."-d-2"
                        ];
                    }

                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getType(Content::FORMAT_BADGE),
                            "tooltip" => "Type de l'équipement",
                            "items" => $items,
                            "id" => "type_{$this->getUniqid()}",
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    if(in_array($this->_type, Item::TYPE_LIST)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_type, Item::TYPE_LIST)),
                                "color" => Style::getColorFromLetter($this->_type) . "-d-2",
                                "tooltip" => "Type de l'équipement",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_type;
            }
        }
        // ---------- Plus utile
        public function getActif(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $checked = ""; 
                    if($this->_actif){ $checked = "checked"; } else { $checked = ""; }
                    ob_start(); ?>
                        <div class="form-check form-switch">
                            <input onchange="Item.update('<?=$this->getUniqid();?>', this, 'actif', <?=Controller::IS_CHECKBOX?>);"  type="checkbox" class="form-check-input back-actif border-actif" <?=$checked?> id="customSwitchActif<?=$this->getId()?>">
                            <label class="custom-control-label" for="customSwitchActif<?=$this->getUniqid()?>"><?=$this->getActif(Content::FORMAT_BADGE);?></label>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if($this->_actif){ 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title='Item actif' class='badge back-actif-d-2'>Actif</span>";
                    } else { 
                        return "<span data-bs-toggle='tooltip' data-bs-placement='top' title='Item passif' class='badge back-actif-d-2'>Passif</span>";
                    }

                case Content::FORMAT_ICON:
                    if($this->_actif){ 
                        return "<i data-bs-toggle='tooltip' data-bs-placement='top' title='Item actif' class='fas fa-check-circle text-actif'></i>";
                    } else { 
                        return "<i data-bs-toggle='tooltip' data-bs-placement='top' title='Item passif' class='fas fa-ban text-actif'></i>";
                    }
                    
                default:
                    return $this->_actif;
            }
        }
        public function getTwohands(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $checked = ""; 
                    if($this->_twohands){ $checked = "checked"; } else { $checked = ""; }
                    ob_start(); ?>
                        <div class="form-check form-switch">
                            <input onchange="Item.update('<?=$this->getUniqid();?>', this, 'twohands', <?=Controller::IS_CHECKBOX?>);"  type="checkbox" class="form-check-input back-twohands border-twohands" <?=$checked?> id="customSwitchtwohands<?=$this->getId()?>">
                            <label class="custom-control-label" for="customSwitchtwohands<?=$this->getUniqid()?>"><?=$this->getTwohands(Content::FORMAT_BADGE);?></label>
                        </div>
                    <?php return ob_get_clean();
                    
                case Content::FORMAT_BADGE:
                    if($this->_twohands){ 
                        return "<span class='badge back-twohands-d-2' data-bs-toggle='tooltip' data-bs-placement='top' title='1 main'>1 main <img class='icon' src='medias/modules/icons/onehand.png'></span>";
                    } else { 
                        return "<span class='badge back-twohands-d-2' data-bs-toggle='tooltip' data-bs-placement='top' title='2 mains'>2 mains <img class='icon' src='medias/modules/icons/twohand.png'></span>";
                    }

                case Content::FORMAT_ICON:
                    if($this->_twohands){ 
                        return "<span class='text-twohands-d-2' data-bs-toggle='tooltip' data-bs-placement='top' title='1 main'>1 <img class='icon' src='medias/modules/icons/onehand.png'></span>";
                    } else { 
                        return "<span class='text-twohands-d-2' data-bs-toggle='tooltip' data-bs-placement='top' title='2 mains'>2 <img class='icon' src='medias/modules/icons/twohand.png'></span>";
                    }
                    
                default:
                    return $this->_twohands;
            }
        }
        public function getPa(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div class="mb-1 text-pa-d-2">
                            <label>Coût en point d'action de l'équipement</label>
                            <div class="input-group">
                                <div class="input-group-text back-pa-d-2 text-white"><img class='icon' src='medias/modules/icons/pa.png'></div>
                                <input 
                                    onchange="Item.update('<?=$this->getUniqid();?>', this, 'pa');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Coût en point d'action de l'équipement"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_pa?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-pa-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Coût en point d'action de l'équipement\">PA {$this->_pa}</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-pa-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Coût en point d'action de l'équipement\">{$this->_pa} <img class='icon' src='medias/modules/icons/pa.png'></span>";
                
                default:
                    return $this->_pa;
            }
        }
        public function getPo(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div class="mb-1 text-po-d-2">
                            <label>Portée de l'équipement</label>
                            <div class="input-group">
                                <div class="input-group-text back-po text-white"><img class="icon" src="medias/modules/icons/po.png"></div>
                                <input 
                                    onchange="Item.update('<?=$this->getUniqid();?>', this, 'po');" 
                                    data-bs-toggle='tooltip' data-bs-placement='bottom' title="Portée de l'équipement"
                                    type="text" 
                                    class="form-control form-control-main-focus" 
                                    value="<?=$this->_po?>">
                            </div>
                        </div>
                    <?php return ob_get_clean();
                
                case Content::FORMAT_BADGE:
                    return "<span class='badge back-po-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Portée de l'équipement\">PO {$this->_po}</span>";
                   
                case Content::FORMAT_ICON:
                    return "<span class='text-po-d-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title=\"Portée de l'équipement\">{$this->_po} <img class='icon' src='medias/modules/icons/po.png'></span>";
                
                default:
                    return $this->_po;
            }
        }
        // ---------- fin
        public function getPrice(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Item",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "price",
                            "label" => "Prix estimé",
                            "placeholder" => "Prix estimé",
                            "tooltip" => "Prix estimé",
                            "value" => $this->_price,
                            "color" => "kamas-d-3",
                            "style" => Style::INPUT_ICON,
                            'icon' => "kamas.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_price} kamas",
                            "color" => "kamas-d-4",
                            "tooltip" => "Prix estimé de l'équipement",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "kamas.png",
                            "color" => "kamas-d-3",
                            "tooltip" => "Prix estimé de l'équipement",
                            "content" => $this->_price,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false); 
                
                default:
                    return $this->_price;
            }
        }
        public function getRarity(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Item::RARITY_LIST as $name => $rarity) {
                        $items[] = [
                            "display" => ucfirst($name),
                            "onclick" => "Item.update('{$this->getUniqid()}', {$rarity}, 'rarity', ".Controller::IS_VALUE.")",
                            "class" => "badge back-".Style::getColorFromLetter($rarity, true)."-d-2"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getRarity(Content::FORMAT_BADGE),
                            "tooltip" => "Rareté de l'équipement",
                            "items" => $items,
                            "id" => "rarity_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_rarity, Item::RARITY_LIST)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_rarity, Item::RARITY_LIST)),
                                "color" => Style::getColorFromLetter($this->_rarity, true) . "-d-2",
                                "tooltip" => "Rareté de l'équipement",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_rarity;
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName($data){
            $this->_name = $data;
            return true;
        }
        public function setLevel($data){
            $this->_level = $data;
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
        public function setType($data){
            if(in_array($data, Item::TYPE_LIST)){
                $this->_type = $data;
                return true;
            } else {
                throw new Exception("Valeur incorrect");
            }
        }
        public function setRecepe($data){
            $this->_recepe = $data;
            return true;
        }
        public function setActif($data){
            // $this->_actif = $this->returnBool($data);
            $this->_actif = $data;
            return true;
        }
        public function setTwohands($data){
            $this->_twohands = $data;
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
                throw new Exception("Rareté invalide");
            }
        }
        public function setPo(int $data){
            if(is_numeric($data)){
                $this->_po = $data;
                return true;
            } else {
                throw new Exception("La valeur doit être un nombre");
            }
        }
        public function setPa(int $data){
            if(is_numeric($data)){
                $this->_pa = $data;
                return true;
            } else {
                throw new Exception("La valeur doit être un nombre");
            }
        }
}
