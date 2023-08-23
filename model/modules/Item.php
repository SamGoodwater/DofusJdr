<?php
class Item extends Content
{
    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/items/default.svg",
            "default_editable" => "medias/modules/items/default_[type].svg",
            "dir" => "medias/modules/items/",
            "preferential_format" => "svg",
            "naming" => "[uniqid]"
        ]
    ];

    const TYPES = [
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
        private $_bonus="";
        private $_type=Item::TYPE_ARC;
        private $_recepe="";
        private $_price=0;
        private $_rarity=5;

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
                            "color" => Style::getColorFromLetter($this->_level, true) . "-d-3",
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
                            "class" => "text-".Style::getColorFromLetter($this->_level, true) . "-d-3"
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

        public function getBonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus = [];
           
            if($this->isSerialized($this->_bonus)){
                $bonus_brut = unserialize($this->_bonus);
                if($bonus_brut != -1){ // -1 correspond à un équipement sans bonus
                    if(is_array($bonus_brut)){
                        if(!empty($bonus_brut)){
                            foreach ($bonus_brut as $caract) {
                                if(isset($caract['type'])){
                                    $type = $caract['type'];
                                    if(isset(Creature::CARACTERISTICS[$type])){
                                        $bonus[$type] = [
                                            "type" => $type,
                                            "name" => Creature::CARACTERISTICS[$type]["name"],
                                            "value" => $caract['value'],
                                            "color" => Creature::CARACTERISTICS[$type]["color"],
                                            "icon" => Creature::CARACTERISTICS[$type]["icon"],
                                            "price" => Creature::CARACTERISTICS[$type]["price"],
                                            "path_icon" => "medias/icons/modules/" . Creature::CARACTERISTICS[$type]["icon"]
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_EDITABLE:
                   
                    $items_bonus_all = [];
                    foreach (Creature::CARACTERISTICS as $type => $caract) {
                        $items_bonus_all[] = [
                            "display" => "<span data-type='" .$type."' data-name='".ucfirst($caract['name'])."' data-color='".$caract['color']."' data-icon='medias/icons/modules/".$caract['icon']."' class='badge-outline w-100 m-0 px-2 text-left text-".$caract['color']."-d-4 border-".$caract['color']."-d-4'><img class='icon-lg pe-2' src='medias/icons/modules/".$caract['icon']."' alt=\"Icône du bonus ".$caract['name']."\">" .ucfirst($caract['name'])."</span>",
                            "onclick" => "",
                            "class" => "w-100 m-0 p-0"
                        ];
                    }

                    $items_bonus = [];
                    foreach ($bonus as $type => $caract) {


                        ob_start(); ?>

                            <div class='input-group input-group-sm mb-3 border-<?=$caract['color']?>-d-4'>
                                <span class='input-group-text back-<?=$caract['color']?>-l-4 text-<?=$caract['color']?>-d-4'>
                                    <img src='<?=$caract['path_icon']?>' alt="Icône du bonus <?=$caract['name']?>" class='icon-xl'><?=ucfirst($caract['name'])?>
                                </span>
                                <input type='text' data-type="<?=$caract['type']?>" class='item_bonus form-control form-control-<?=$caract['color']?>-focus text-<?=$caract['color']?>-d-4' value='<?=$caract['value']?>'>
                                <a onclick='$(this).parent().parent().parent().empty();' class='btn btn-text-red align-self-center' title='Supprimer cette caractéristique'>
                                    <i class='size-1-2 fa-solid fa-trash'></i>
                                </a>
                            </div>
                        <?php 

                        $items_bonus[] = [
                            "display" => ob_get_clean()
                        ];
                    }

                    ob_start(); ?>
                        <div class="m-2">

                            <?php $view->dispatch(
                                template_name : "list",
                                data : [
                                    "title" => "Bonus de l'équipement",
                                    "tooltip" => "",
                                    "items" => $items_bonus,
                                    "id" => "bonus_list_{$this->getUniqid()}"
                                ], 
                                write: true); ?>

                        <?php if(empty($bonus)){ ?>
                            <ul id="bonus_list_<?=$this->getUniqid()?>" class="list-group list-group-flush">
                                <li class="list-group-item none-bonus-text">Aucun bonus de caractéristique</li>
                            </ul>
                        <?php } ?>

                            <div class="d-flex justify-content-start align-items-end">
                                <div class="mx-2">
                                    <?php $view->dispatch(
                                        template_name : "dropdown",
                                        data : [
                                            "label" => "Ajouter un bonus",
                                            "tooltip" => "Bonus à l'équipement",
                                            "items" => $items_bonus_all,
                                            "class" => "mx-2 btn-border-main btn btn-sm",
                                            "id" => "bonus_dropdown_{$this->getUniqid()}",
                                        ], 
                                        write: true); ?>
                                </div>

                                <div class="mx-2">
                                    <?php $view->dispatch(
                                    template_name : "input/text",
                                    data : [
                                        "is_onchange" => false,
                                        "id" => "bonus_value",
                                        "label" => "Valeur du bonus",
                                        "size" => STYLE::SIZE_SM,
                                        "class" => "",
                                        "placeholder" => "Choisissez une valeur",
                                        "value" => "",
                                        "style" => Style::INPUT_BASIC
                                    ], 
                                    write: true); ?>
                                </div>

                                <button class="btn btn-sm btn-border-main" onclick="addDisplayBonus()"><i class="fa-solid fa-plus"></i></button>
                                <button class="btn btn-sm btn-border-secondary mx-2" onclick="saveBonus()"><i class="fa-solid fa-floppy-disk"></i> Enregistrer les bonus</button>
                            </div>
                            <p><small id="error_msg_bonus" class="text-red-d-4 size-0-8"></small></p>

                        </div>
                        <script>
                            function addDisplayBonus() {
                                $("#error_msg_bonus").html("");

                                let list = $("#bonus_list_<?=$this->getUniqid()?>");
                                let bonus_value = $("#bonus_value").val();
                                let select = $("#bonus_dropdown_<?=$this->getUniqid()?> span");
                                let type = select.attr("data-type");
                                let color = select.attr("data-color");
                                let icon = select.attr("data-icon");
                                let name = select.attr("data-name");

                                if(bonus_value == "" || bonus_value == null || bonus_value == undefined){
                                    $("#error_msg_bonus").html("Veuillez saisir une valeur pour le bonus");
                                    return;
                                }
                                if(type == "" || type == null || type == undefined){
                                    $("#error_msg_bonus").html("Veuillez choisir un type de bonus");
                                    return;
                                }

                                let item = document.createElement("li");
                                item.classList.add("list-group-item");
                                item.classList.add("p-1");
                                item.innerHTML = "<div class='input-group input-group-sm mb-3 border-"+color+"-d-4'><span class='input-group-text back-"+color+"-l-4 text-"+color+"-d-4'><img src='"+icon+"' alt=\"Icône du bonus "+name+"\" class='icon-xl'> "+name+"</span><input type='text' data-type='"+type+"' class='item_bonus form-control form-control-"+color+"-focus text-"+color+"-d-4' value='"+bonus_value+"'><a onclick='$(this).parent().parent().parent().empty();' class='btn btn-text-red align-self-center' title='Supprimer cette caractéristique'><i class='size-1-2 fa-solid fa-trash'></i></a></div>";
                                list.append(item);

                                if($('.none-bonus-text') != null){
                                    $('.none-bonus-text').remove();
                                }
                            }

                            function saveBonus() {
                                let list = $("#bonus_list_<?=$this->getUniqid()?>");
                                let bonus = [];
                                let error = false;

                                if(list.children().length > 0){
                                    list.children().each(function(){
                                        if($(this).find("input").length == 0) return;
                                        if($(this).hasClass("none-bonus-text")) return;

                                        let type = $(this).find("input").attr("data-type");
                                        let value = $(this).find("input").val();
                                        if(value == "" || value == null || value == undefined){
                                            $("#error_msg_bonus").html("Veuillez saisir une valeur pour le bonus");
                                            $(this).find("input").css("border-color", "red");
                                            error = true;
                                            return;
                                        }
                                        if(type == "" || type == null || type == undefined){
                                            $("#error_msg_bonus").html("Veuillez choisir un type de bonus");
                                            $(this).find("input").css("border-color", "red");
                                            error = true;
                                            return;
                                        }
                                        bonus.push({
                                            "type" : type,
                                            "value" : value
                                        });
                                    });
                                } else {
                                    bonus = "-1";
                                }
                                
                                Item.update('<?=$this->getUniqid()?>', bonus, 'bonus', IS_VALUE);
                            }

                        </script>

                    <?php return ob_get_clean();


                case Content::FORMAT_BADGE:
                    if(empty($bonus)){
                        $bonus = [
                            "aucun" => [
                                "type" => "none",
                                "name" => "Aucun bonus",
                                "value" => "",
                                "color" => "gray",
                                "icon" => "",
                                "path_icon" => ""
                            ]
                        ];
                    }

                    ob_start(); ?>
                        <div class="d-flex justify-content-start align-content-center flex-wrap">
                            <?php foreach ($bonus as $type => $caract) { ?>
                                <span class='me-2 mb-2 badge badge-outline text-<?=$caract['color']?>-d-4 border-<?=$caract['color']?>-d-4'>
                                    <img class='icon-lg pe-1' src='<?=$caract['path_icon']?>' alt="Icône du bonus <?=$caract['name']?>">
                                    <span class='size-0-9'><?=ucfirst($caract['name'])?></span> : <?=$caract['value']?></span>
                            <?php } ?>
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_ARRAY:
                    return $bonus;

                default:
                    return $this->_bonus;
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
                    foreach (Item::TYPES as $name => $type) {
                        $items[] = [
                            "display" => "<span class='badge back-".Style::getColorFromLetter($type)."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => "Item.update('".$this->getUniqid()."', '".$type."', 'type',".Controller::IS_VALUE.")"
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
                    if(in_array($this->_type, Item::TYPES)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_type, Item::TYPES)),
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
                            "label" => "Prix estimé - Défini",
                            "placeholder" => $this->getEstimatedPrice(Content::FORMAT_TEXT),
                            "tooltip" => "Prix estimé - Défini",
                            "value" => $this->_price,
                            "color" => "kamas-d-3",
                            "style" => Style::INPUT_ICON,
                            'icon' => "kamas.png",
                            "style_icon" => Style::ICON_MEDIA,
                            "comment" => "Prix estimé automatique : " . $this->getEstimatedPrice() . " kamas"
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    $text = "{$this->_price} kamas (défini) <br> <small>".$this->getEstimatedPrice()." kamas (calculé)</small>";
                    $tooltip = "Prix estimé de l'équipement {$this->_price} kamas (défini) - Prix calculé automatiquement : ".$this->getEstimatedPrice()." kamas";
                    if($this->_price == $this->getEstimatedPrice()){
                        $text = "{$this->_price} kamas";
                        $tooltip = "Prix estimé de l'équipement - défini";
                    }
                    if($this->_price == 0 || empty($this->_price)){
                        $text = $this->getEstimatedPrice() . " kamas";
                        $tooltip = "Prix estimé de l'équipement - calculé automatiquement";
                    }
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $text,
                            "color" => "kamas-d-4",
                            "tooltip" => $tooltip,
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    $tooltip = "Prix estimé de l'équipement {$this->_price} kamas (défini) - Prix calculé automatiquement : ".$this->getEstimatedPrice()." kamas";
                    if($this->_price == $this->getEstimatedPrice()){
                        $tooltip = "Prix estimé de l'équipement - défini";
                    }
                    $text = $this->_price;
                    if($this->_price == 0 || empty($this->_price)){
                        $text = $this->getEstimatedPrice();
                    }
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "kamas.png",
                            "color" => "kamas-d-3",
                            "tooltip" => $tooltip,
                            "content" => $text,
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
                            "display" => "<span class='badge back-".Style::getColorFromLetter($rarity, true)."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => "Item.update('{$this->getUniqid()}', {$rarity}, 'rarity', ".Controller::IS_VALUE.")"
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

        public function getEstimatedPrice(int $format = Content::FORMAT_BRUT){
            $estimated_price = 0;
            if(is_array($this->getBonus(Content::FORMAT_ARRAY))){
                foreach($this->getBonus(Content::FORMAT_ARRAY) as $bonus){
                    if(is_array($bonus)){
                        $multiplier = $this->extractNumber($bonus['value']);
                        if($multiplier == 0 || !is_numeric($multiplier)) $multiplier = 1;

                        if(isset($bonus['price']) && is_numeric($bonus['price'])){
                            $estimated_price += $multiplier * (int) $bonus['price'];
                        }
                    }
                }
            }

            $view = new View();
            switch ($format) {
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$estimated_price} kamas",
                            "color" => "kamas-d-4",
                            "tooltip" => "Prix estimé de l'équipement - calculé automatiquement",
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
                            "tooltip" => "Prix estimé de l'équipement - calculé automatiquement",
                            "content" => $estimated_price,
                            "content_placement" => Style::POSITION_LEFT
                        ], 
                        write: false); 

                case Content::FORMAT_TEXT:
                    return "Le prix calculé automatiquement est de {$estimated_price} kamas";
                
                default:
                    return $estimated_price;
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
        public function setBonus($data){
            if(empty($data)){
                $this->_bonus = null;
                return true;
            }
            if($this->isSerialized($data)){
                $this->_bonus = $data;
            } else {
                $this->_bonus = serialize($data);
            }
            return true;
        }
        public function setType($data){
            if(in_array($data, Item::TYPES)){
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
}
