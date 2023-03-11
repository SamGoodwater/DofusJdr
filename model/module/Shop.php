<?php
class Shop extends Content
{

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ CONSTANTES ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public const PRICE = [
            "Très peu cher" => 1,
            "Peu cher" => 2,
            "Normal" => 0,
            "Cher" => 3,
            "Très cher" => 4
        ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='Hôtel de vente';
        private $_description='';
        private $_location='';
        private $_price=Shop::PRICE["Normal"];
        private $_id_seller = "";

        protected $_usable = true; // surcharge de la variable de Content

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Shop",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom de l'hôtel de vente",
                            "placeholder" => "Nom de l'hôtel de vente",
                            "value" => $this->_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $view = new View(View::TEMPLATE_SNIPPET);
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => "Shop",
                            "id" => "description".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "description",
                            "label" => "Description",
                            "value" => $this->_description
                        ], 
                        write: false);
                
                default:
                    return html_entity_decode($this->_description);
            }
        }
        public function getLocation(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => "Shop",
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "location",
                            "label" => "Emplacement",
                            "placeholder" => "Ville, région, rue, quartier, etc",
                            "value" => $this->_location,
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "map-marker-alt",
                            "style_icon" => Style::ICON_SOLID,
                            "comment" => "Ville, région, quartier, rue, etc"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_SOLID,
                            "icon" => "map-marker-alt",
                            "tooltip" => "Emplacement de l'hôtel de vente",
                            "content" => $this->_location,
                            "content_placement" => "after",
                        ], 
                        write: false); 
                
                default:
                    return $this->_location;
            }
        }
        public function getPrice(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (Shop::PRICE as $name => $value) {
                        $items[] = [
                            "display" => ucfirst($name),
                            "onclick" => "Shop.update('{$this->getUniqid()}', {$value}, 'price', ".Controller::IS_VALUE.")",
                            "class" => "badge-outline border-".Style::getColorFromLetter($value)."-d-2"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getPrice(Content::FORMAT_BADGE),
                            "tooltip" => "Prix moyen",
                            "items" => $items,
                            "id" => "price_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_price, Shop::PRICE)){
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => array_search($this->_price, Shop::PRICE),
                                "color" => Style::getColorFromLetter($this->_price)."-d-2",
                                "tooltip" => "Bonus de portée",
                                "style" => Style::STYLE_OUTLINE
                            ], 
                            write: false);
                    } else  {
                        return "";
                    }

                default:
                    return $this->_price;
            }
        }
        public function getId_seller(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $manager = new NpcManager();
            if($manager->existsId($this->_id_seller)){
                $npc = $manager->getFromId($this->_id_seller);
            }

            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach ($manager->getAll() as $value) {
                        $items[] = [
                            "display" => ucfirst($value->getName()),
                            "onclick" => "Shop.update('{$this->getUniqid()}', {$value->getId()}, 'id_seller', ".Controller::IS_VALUE.")",
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $npc->getName(),
                            "tooltip" => "Marchand·e (PNJ)",
                            "items" => $items,
                            "id" => "id_seller_{$this->getUniqid()}",
                        ], 
                        write: false);

                case Content::DISPLAY_RESUME:
                    if(isset($npc)){
                        return $npc->getVisual(Content::DISPLAY_RESUME);
                    } else {
                        return "";
                    }

                case Content::FORMAT_OBJECT:
                    if(isset($npc)){
                        return $npc;
                    } else {
                        return "";
                    }
                
                case Content::FORMAT_IMAGE:
                    if(isset($npc)){
                        return $npc->getClasse(Content::FORMAT_OBJECT)->getPath_img_logo(Content::FORMAT_IMAGE, "img-back-30");
                    } else {
                        return "";
                    }
                    

                default:
                    return $this->_id_seller;
            }
        }
        
        public function getConsumable(int $format = Content::FORMAT_BRUT, bool $is_removable = false){
            $view = new View();
            $manager = new ShopManager;
            $links = $manager->getLinkConsumable($this);

            switch ($format) { 
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div><?=$this->getConsumable(Content::FORMAT_BRUT, true)?></div>
                        <h6 class="mt-1">Ajouter des consommables</h6>
                        <?php 
                            $view->dispatch(
                                template_name : "input/search",
                                data : [
                                    "id" => "addConsumable" . $this->getUniqid(),
                                    "title" => "Ajouter un consomable",
                                    "label" => "Rechercher un consomable",
                                    "placeholder" => "Rechercher un consomable",
                                    "search_in" => ControllerSearch::SEARCH_IN_CONSUMABLE,
                                    "parameter" => $this->getUniqid(),
                                    "action" => ControllerSearch::SEARCH_DONE_ADD_CONSUMABLE_TO_SHOP,
                                ], 
                                write: true);
                        ?>  
                    <?php return ob_get_clean();

                case Content::FORMAT_ARRAY:
                    return $links;
                
                default:
                    $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                    return $view->dispatch(
                        template_name : "shop/list_link",
                        data : [
                            "links" => $links,
                            "uniqid" => $this->getUniqid(),
                            "user" => ControllerConnect::getCurrentUser(),
                            "class_name" => "shop",
                            "input_name" => "consumable",
                            "is_editable" => $is_removable
                        ], 
                        write: false
                    );
            }   
        }
        public function getItem(int $format = Content::FORMAT_BRUT, bool $is_removable = false){
            $view = new View();
            $manager = new ShopManager;
            $links = $manager->getLinkItem($this);

            switch ($format) { 
                case Content::FORMAT_EDITABLE:
                    ob_start(); ?>
                        <div><?=$this->getItem(Content::FORMAT_BRUT, true)?></div>
                        <h6 class="mt-1">Ajouter des équipements</h6>
                        <?php 
                            $view->dispatch(
                                template_name : "input/search",
                                data : [
                                    "id" => "addItem" . $this->getUniqid(),
                                    "title" => "Ajouter un équipement",
                                    "label" => "Rechercher un équipement",
                                    "placeholder" => "Rechercher un équipement",
                                    "search_in" => ControllerSearch::SEARCH_IN_CONSUMABLE,
                                    "parameter" => $this->getUniqid(),
                                    "action" => ControllerSearch::SEARCH_DONE_ADD_ITEM_TO_SHOP,
                                ], 
                                write: true);
                        ?>  
                    <?php return ob_get_clean();

                case Content::FORMAT_ARRAY:
                    return $links;
                
                default:
                    $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                    return $view->dispatch(
                        template_name : "shop/list_link",
                        data : [
                            "links" => $links,
                            "uniqid" => $this->getUniqid(),
                            "user" => ControllerConnect::getCurrentUser(),
                            "class_name" => "shop",
                            "input_name" => "item",
                            "is_editable" => $is_removable
                        ], 
                        write: false
                    );
            }   
        }
        
    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName($data){
            $this->_name = $data;
            return true;
        }
        public function setDescription($data){
            $this->_description = $data;
            return true;
        }
        public function setLocation($data){
            $this->_location = $data;
            return true;
        }
        public function setPrice($data){
            if(in_array($data, Shop::PRICE)){
                $this->_price= $data;
                return true;
            } else {
                return "Le prix moyen n'est pas valide";
            }
        }
        public function setId_seller($data){
            $this->_id_seller = $data;
            return true;
        }

        /* Data = array(
                        uniqid => id du consomable,
                        quantity => quantité du consomable,
                        price => prix du consomable,
                        action => remove / add / update
                    )
            Js : Shop.update(UniqidS,{action:'add|remove|update', uniqid:'uniqIdC', quantity:'Quantity',price:'price',comment:'Comment'},'consumable', IS_VALUE);
        */
        public function setConsumable(array $data){ 
            $managerS = new ShopManager;
            $managerC = new ConsumableManager;
            if(!isset($data['uniqid'])){return "L'uniqid du consommable n'est pas défini";}
            if($managerC->existsUniqid($data['uniqid'])){
                $consumable = $managerC->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            if(isset($data['quantity'])){$quantity=$data['quantity'];} else {$quantity="";}
                            if(isset($data['price'])){$price=$data['price'];} else {$price="";}
                            if(isset($data['comment'])){$comment=$data['comment'];} else {$comment="";}
                            return $managerS->addLinkConsumable($this, $consumable, $quantity, $price, $comment);
               
                        case "remove":
                            return $managerS->removeLinkConsumable($this, $consumable);

                        case "update":
                            if($managerS->existsLinkConsumable($this, $consumable)){
                                $link = $managerS->getLinkConsumable($this, $consumable);
                                if(isset($data['quantity'])){$quantity=$data['quantity'];} else {$quantity=$link["quantity"];}
                                if(isset($data['price'])){$price=$data['price'];} else {$price=$link["price"];}
                                if(isset($data['comment'])){$comment=$data['comment'];} else {$comment=$link["comment"];}
                                return $managerS->updateLinkConsumable($this, $consumable, $quantity, $price, $comment);

                            } else {
                                if(isset($data['quantity'])){$quantity=$data['quantity'];} else {$quantity="";}
                                if(isset($data['price'])){$price=$data['price'];} else {$price="";}
                                if(isset($data['comment'])){$comment=$data['comment'];} else {$comment="";}
                                return $managerS->addLinkConsumable($this, $consumable, $quantity, $price, $comment);
                            }
                        
                        default:
                            return "L'action n'est pas valide";
                    }

                } else {
                    return "Une action est requise.";
                }

            }
        }

        /* Data = array(
                uniqid => id de l'équipement,
                quantity => quantité de l'équipement,
                price => prix du consomable,
                action => remove / add / update
            )
            Js : Shop.update(UniqidS,{action:'add|remove|update', uniqid:'uniqIdC', quantity:'Quantity',price:'price',comment:'Comment'},'item', IS_VALUE);
        */
        public function setItem(array $data){ 
            $managerS = new ShopManager;
            $managerI = new ItemManager;
            if(!isset($data['uniqid'])){return "L'uniqid du l'équipement n'est pas défini";}
            if($managerI->existsUniqid($data['uniqid'])){
                $item = $managerI->getFromUniqid($data['uniqid']); 

                if(isset($data['action'])){
                    switch ($data['action']) {
                        case 'add':
                            if(array_key_exists("quantity", $data)){$quantity=$data['quantity'];} else {$quantity="";}
                            if(array_key_exists("price", $data)){$price=$data['price'];} else {$price="";}
                            if(array_key_exists("comment", $data)){$comment=$data['comment'];} else {$comment="";}
                            return $managerS->addLinkItem($this, $item, $quantity, $price, $comment);
               
                        case "remove":
                            return $managerS->removeLinkItem($this, $item);

                        case "update":
                            if($managerS->existsLinkItem($this, $item)){
                                $link = $managerS->getLinkItemFromItem($this, $item);
                                if(array_key_exists("quantity", $data)){$quantity=$data['quantity'];} else {$quantity=$link["quantity"];}
                                if(array_key_exists("price", $data)){$price=$data['price'];} else {$price=$link["price"];}
                                if(array_key_exists("comment", $data)){$comment=$data['comment'];} else {$comment=$link["comment"];}
                                return $managerS->updateLinkItem($this, $item, $quantity, $price, $comment);

                            } else {
                                if(array_key_exists("quantity", $data)){$quantity=$data['quantity'];} else {$quantity="";}
                                if(array_key_exists("price", $data)){$price=$data['price'];} else {$price="";}
                                if(array_key_exists("comment", $data)){$comment=$data['comment'];} else {$comment="";}
                                return $managerS->addLinkItem($this, $item, $quantity, $price, $comment);
                            }
                        
                        default:
                            return "L'action n'est pas valide";
                    }

                } else {
                    return "Une action est requise.";
                }

            }
        }

}