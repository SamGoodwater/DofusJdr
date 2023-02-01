<?php
class ConsumableManager extends Manager
{

// GET
    public function getAll($usable = 0){
        if($usable){
            $requete = "SELECT * FROM consumable WHERE usable = 1 ORDER BY usable DESC, level"; 
            $req = $this->_bdd->prepare($requete);
            $req->execute();
        } else {
            $requete = "SELECT * FROM consumable ORDER BY usable DESC, level"; 
            $req = $this->_bdd->prepare($requete);
            $req->execute();
        }

        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($ret)){
            return $this->bdd2objects($ret);
        } else {
            return array();
        }
    }
    public function getFromId($id){
        $post = $this->_bdd->prepare('SELECT * FROM consumable WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Consumable($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM consumable WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Consumable($this->securite($req));
    }

    public function countAll(){
        $requete = "SELECT id FROM consumable";    
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
            $req = $this->_bdd->prepare('SELECT id FROM consumable WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM consumable WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
            $req = $this->_bdd->prepare('SELECT id FROM consumable WHERE name = ?');
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
        $usable = "";
        if($this->returnBool($only_usable)){
            $usable = "AND usable = 1 ";
        }
        
        $term = "%" . $term . "%";
        $req = $this->_bdd->prepare('SELECT *
        FROM consumable        
        WHERE ( description like :term 
            OR name like :term ) '.$usable . $limit.'');
        
        $req->execute(array("term" => $term));
        $result =  $req->fetchAll();
        
        if(!empty($result)){
            return $this->bdd2objects($result);
        } else {
            return array();
        }
    }

// WRITE
    public function add(Consumable $object){
        $req = $this->_bdd->prepare('INSERT INTO consumable(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    type,
                    name,
                    description,
                    effect,
                    level,
                    recepe,
                    price,
                    rarity,
                    usable
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :type,
                    :name,
                    :description,
                    :effect,
                    :level,
                    :recepe,
                    :price,
                    :rarity,
                    :usable
                )');

        return $req->execute(array(
            'uniqid' => $object->getUniqid(),
            'timestamp_add' => $object->getTimestamp_add(),
            'timestamp_updated' => $object->getTimestamp_updated(),
            'type' => $object->getType(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'effect' => $object->getEffect(),
            'level' => $object->getLevel(),
            'recepe' => $object->getRecepe(),
            'price' => $object->getPrice(),
            "rarity" => $object->getRarity(),
            "usable" => $object->getUsable()
        ));
    }
    public function update(Consumable $object){
        $req = $this->_bdd->prepare('UPDATE consumable SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    type=:type,
                    name=:name,
                    description=:description,
                    effect=:effect,
                    level=:level,
                    recepe=:recepe,
                    price=:price,
                    rarity=:rarity,
                    usable=:usable
            WHERE id = :id');

        return $req->execute(array(
            'id' => $object->getId(),
            'uniqid' => $object->getUniqid(),
            'timestamp_add' => $object->getTimestamp_add(),
            'timestamp_updated' => $object->getTimestamp_updated(),
            'type' => $object->getType(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'effect' => $object->getEffect(),
            'level' => $object->getLevel(),
            'recepe' => $object->getRecepe(),
            'price' => $object->getPrice(),
            "rarity" => $object->getRarity(),
            "usable" => $object->getUsable()
            ));
    }
    public function delete(Consumable $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);
        
        $req = $this->_bdd->prepare('DELETE FROM consumable WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Consumable($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
