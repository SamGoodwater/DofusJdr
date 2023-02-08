<?php
class ShopManager extends Manager
{

// GET
    public function getAll(){
        $requete = "SELECT * FROM shop ORDER BY location"; 
        $req = $this->_bdd->prepare($requete);
        $req->execute();
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($ret)){
            return $this->bdd2objects($ret);
        } else {
            return array();
        }
    }
    public function getFromId($id){
        $post = $this->_bdd->prepare('SELECT * FROM shop WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Shop($req);
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM shop WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Shop($req);
    }

    public function countAll(){
        $requete = "SELECT id FROM shop";    
        $req = $this->_bdd->prepare($requete);
        $req->execute();
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($ret)){
            return count($ret);
        } else {
            return 0;
        }
    }

// EXISTS
    public function existsId($id){
            $req = $this->_bdd->prepare('SELECT id FROM shop WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM shop WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
        $req = $this->_bdd->prepare('SELECT id FROM shop WHERE name = ?');
        $req->execute(array($this->securite($name)));
        return $req->rowCount();
    }

// SEARCH
    public function search($term, $limit = null, $only_usable = false){
        if(!empty($limit) && is_integer((int) $limit)){
            $limit = "LIMIT " . $limit ;
        } else {
            $limit = "";
        }
        $usable = ""; // PAS UTILISE car pas usable dans la DB

        $term = "%" . $term . "%";
        $req = $this->_bdd->prepare('SELECT *
        FROM shop        
        WHERE ( description like :term 
            OR name like :term ) '. $limit.'');
        
        $req->execute(array("term" => $term));
        $result =  $req->fetchAll();
        
        if(!empty($result)){
            return $this->bdd2objects($result);
        } else {
            return array();
        }
    }

// WRITE
    public function add(Shop $object){

        $req = $this->_bdd->prepare('INSERT INTO shop(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    description,
                    location,
                    price,
                    id_seller
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :description,
                    :location,
                    :price,
                    :id_seller
                )');
                         
        return $req->execute(array(
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "timestamp_updated" => $object->getTimestamp_updated(),
            "name" => $object->getName(),
            "description" => $object->getDescription(),
            "location" => $object->getLocation(),
            "price" => $object->getPrice(),
            "id_seller" => $object->getId_seller()
        ));
    }
    public function update(Shop $object){
        $req = $this->_bdd->prepare('UPDATE shop SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    description=:description,
                    location=:location,
                    price=:price,
                    id_seller=:id_seller
            WHERE id=:id');

        return $req->execute(array(
            "id" => $object->getId(),
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "timestamp_updated" => $object->getTimestamp_updated(),
            "name" => $object->getName(),
            "description" => $object->getDescription(),
            "location" => $object->getLocation(),
            "price" => $object->getPrice(),
            "id_seller" => $object->getId_seller()
        ));

    }
    public function delete(Shop $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);
        $this->removeAllLinkConsumableFromShop($object);
        $this->removeAllLinkitemFromShop($object);
        $req = $this->_bdd->prepare('DELETE FROM shop WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// Link Consumable
    public function getLinkConsumable(Shop $shop){
        $req = $this->_bdd->prepare('SELECT *, link_shop_consumable.price AS price_link FROM link_shop_consumable INNER JOIN shop ON link_shop_consumable.id_shop = shop.id WHERE link_shop_consumable.id_shop = ? ORDER BY link_shop_consumable.price');
        $req->execute(array($shop->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        $manager = new ConsumableManager;
        foreach ($ret as $link) {
            if($manager->existsId($link["id_consumable"])){
                $return[] = [
                    "obj" => $manager->getFromId($link["id_consumable"]),
                    "price" => $link["price_link"],
                    "quantity" => $link['quantity'],    
                    "comment" => $link['comment']
                ];
            }
        }
        return $return;
    }
    public function getLinkConsumableFromItem(Shop $shop , Consumable $consumable){
        $req = $this->_bdd->prepare('SELECT *, link_shop_consumable.price AS price_link FROM link_shop_consumable INNER JOIN shop ON link_shop_consumable.id_shop = shop.id WHERE link_shop_consumable.id_shop = ? AND link_shop_consumable.id_item = ? ORDER BY link_shop_consumable.price');
        $req->execute(array($shop->getId(), $consumable->getId()));
        $link = $req->fetch(PDO::FETCH_ASSOC);
        if(empty($link)){return "";}
        $return = array();
        $manager = new ConsumableManager;
        if($manager->existsId($link["id_consumable"])){
            $return = [
                "obj" => $manager->getFromId($link["id_consumable"]),
                "price" => $link["price"],
                "quantity" => $link['quantity'],    
                "comment" => $link['comment']
            ];
        }
        return $return;
    }
    public function existsLinkConsumable(Shop $shop, Consumable $consumable){
        $req = $this->_bdd->prepare('SELECT id FROM link_shop_consumable WHERE id_shop = ? AND id_consumable = ?');
        $req->execute(array($shop->getId(), $consumable->getId()));
        return $req->rowCount();
    }
    public function addLinkConsumable(Shop $shop, Consumable $consumable, $quantity = "", $price = "", $comment = ""){
        $req = $this->_bdd->prepare('INSERT INTO link_shop_consumable(
                    id_shop,
                    id_consumable,
                    quantity,
                    price,
                    comment
                )
            VALUES (
                    :id_shop,
                    :id_consumable,
                    :quantity,
                    :price,
                    :comment
                )');

        return $req->execute(array(
            "id_shop" => $shop->getId(),
            "id_consumable"=> $consumable->getId(),
            "quantity" => $quantity,
            "price" => $price,
            "comment" => $comment
        ));

        // Renvoi le dernier ingredient ajouté
        $post = $this->_bdd->prepare('SELECT id FROM link_shop_consumable ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function updateLinkConsumable(Shop $shop, Consumable $consumable, $quantity = "", $price = "", $comment = ""){
        $req = $this->_bdd->prepare('UPDATE link_shop_consumable SET
                quantity=:quantity,
                price=:price,
                comment=:comment
            WHERE   id_shop = :id_shop AND
                    id_consumable = :id_consumable');

        return $req->execute(array(
            "id_shop" => $shop->getId(),
            "id_consumable"=> $consumable->getId(),
            "quantity" => $quantity,
            "price" => $price,
            "comment" => $comment
        ));
    }
    public function removeLinkConsumable(Shop $shop, Consumable $consumable){
        $req = $this->_bdd->prepare('DELETE FROM link_shop_consumable WHERE id_shop = :id_shop AND id_consumable = :id_consumable');
        return $req->execute(array("id_shop" =>  $shop->getId(), "id_consumable" =>  $consumable->getId()));
    }
    public function removeAllLinkConsumableFromShop(Shop $shop){
        $req = $this->_bdd->prepare('DELETE FROM link_shop_consumable WHERE id_shop = :id');
        $req->execute(array("id" =>  $shop->getId()));
    }
    public function removeAllLinkConsumableFromConsumable(Consumable $consumable){
        $req = $this->_bdd->prepare('DELETE FROM link_shop_consumable WHERE id_consumable = :id');
        $req->execute(array("id" =>  $consumable->getId()));
    }

// Link Item
    public function getLinkItem(Shop $shop){
        $req = $this->_bdd->prepare('SELECT *, link_shop_item.price AS price_link FROM link_shop_item INNER JOIN shop ON link_shop_item.id_shop = shop.id WHERE link_shop_item.id_shop = ? ORDER BY link_shop_item.price');
        $req->execute(array($shop->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        $manager = new ItemManager;
        foreach ($ret as $link) {
            if($manager->existsId($link["id_item"])){
                $return[] = [
                    "obj" => $manager->getFromId($link["id_item"]),
                    "price" => $link["price_link"],
                    "quantity" => $link['quantity'],    
                    "comment" => $link['comment']
                ];
            }
        }
        return $return;
    }
    public function getLinkItemFromItem(Shop $shop , Item $item){
        $req = $this->_bdd->prepare('SELECT *, link_shop_item.price AS price_link FROM link_shop_item INNER JOIN shop ON link_shop_item.id_shop = shop.id WHERE link_shop_item.id_shop = ? AND link_shop_item.id_item = ? ORDER BY link_shop_item.price');
        $req->execute(array($shop->getId(), $item->getId()));
        $link = $req->fetchAll(PDO::FETCH_ASSOC)[0];
        if(empty($link)){return array();}
        $return = array();
        $manager = new ItemManager;
        if($manager->existsId($link["id_item"])){
            $return = [
                "obj" => $manager->getFromId($link["id_item"]),
                "price" => $link["price_link"],
                "quantity" => $link['quantity'],    
                "comment" => $link['comment']
            ];
            return $return;
        }
        return array();
    }
    public function existsLinkItem(Shop $shop, Item $item){
        $req = $this->_bdd->prepare('SELECT id FROM link_shop_item WHERE id_shop = ? AND id_item = ?');
        $req->execute(array($shop->getId(), $item->getId()));
        return $req->rowCount();
    } 
    public function addLinkItem(Shop $shop, Item $item, $quantity = "", $price = "", $comment = ""){
        $req = $this->_bdd->prepare('INSERT INTO link_shop_item(
                    id_shop,
                    id_item,
                    quantity,
                    price,
                    comment
                )
            VALUES (
                    :id_shop,
                    :id_item,
                    :quantity,
                    :price,
                    :comment
                )');

        return $req->execute(array(
            "id_shop" => $shop->getId(),
            "id_item"=> $item->getId(),
            "quantity" => $quantity,
            "price" => $price,
            "comment" => $comment
        ));

        // Renvoi le dernier ingredient ajouté
        $post = $this->_bdd->prepare('SELECT id FROM link_shop_item ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function updateLinkItem(Shop $shop, Item $item, $quantity = "", $price = "", $comment = ""){
        $req = $this->_bdd->prepare('UPDATE link_shop_item SET
                quantity=:quantity,
                price=:price,
                comment=:comment
            WHERE   id_shop = :id_shop AND
                    id_item = :id_item');

        return $req->execute(array(
            "id_shop" => $shop->getId(),
            "id_item"=> $item->getId(),
            "quantity" => $quantity,
            "price" => $price,
            "comment" => $comment
        ));
    }
    public function removeLinkItem(Shop $shop, Item $item){
        $req = $this->_bdd->prepare('DELETE FROM link_shop_item WHERE id_shop = :id_shop AND id_item = :id_item');
        return $req->execute(array("id_shop" =>  $shop->getId(), "id_item" =>  $item->getId()));
    }
    public function removeAllLinkItemFromShop(Shop $shop){
        $req = $this->_bdd->prepare('DELETE FROM link_shop_item WHERE id_shop = :id');
        return $req->execute(array("id" =>  $shop->getId()));
    }
    public function removeAllLinkItemFromItem(Item $item){
        $req = $this->_bdd->prepare('DELETE FROM link_shop_item WHERE id_item = :id');
        return $req->execute(array("id" =>  $item->getId()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Shop($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
