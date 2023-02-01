<?php
class ItemManager extends Manager
{

    public function getAllFromType($type, $usable = 0){
        if($usable){
            $requete = "SELECT * FROM item WHERE usable = 1 AND type = ? ORDER BY usable DESC, level"; 
            $req = $this->_bdd->prepare($requete);
            $req->execute(array($type));
        } else {
            $requete = "SELECT * FROM item WHERE type = ? ORDER BY usable DESC, level"; 
            $req = $this->_bdd->prepare($requete);
            $req->execute(array($type));
        }

        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($ret)){
            return $this->bdd2objects($ret);
        } else {
            return array();
        }
    }
    public function getAll($type = ["all"], $level = ["all"], $usable = 0){
        $usable_text = "(usable = 1 OR usable = 0)"; if($usable) { $usable_text = "usable = 1"; }
        $type_text = "";
        if(is_array($type) && !empty($type) && $type[0] != "all"){
            foreach ($type as $value) {
                if(in_array($value, Item::TYPE_LIST)){
                    $type_text .= $value .",";
                }
            }
        }
        if($type_text != ""){
            $type_text = " AND type IN (".substr($type_text, 0, -1).")";
        }

        $level_text = "";
        if(is_array($level) && !empty($level) && $level[0] != "all"){
            foreach ($level as $value) {
                if($value > 0 && $value <= 20){
                    $level_text .= $value .",";
                }
            }
        }
        if($level_text != ""){
            $level_text = " AND level IN (".substr($level_text, 0, -1).")";
        }

        $requete = "SELECT * FROM item WHERE ".$usable_text." ".$level_text." ".$type_text." ORDER BY usable DESC, level"; 

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
        $post = $this->_bdd->prepare('SELECT * FROM item WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Item($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM item WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Item($this->securite($req));
    }
    
    public function countAll(){
        $requete = "SELECT id FROM item";    
        $req = $this->_bdd->prepare($requete);
        $req->execute();
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($ret)){
            return count($ret);
        } else {
            return 0;
        }
    }
    public function countFromType($type){
        $requete = "SELECT id FROM item WHERE type = ?";    
        $req = $this->_bdd->prepare($requete);
        $req->execute(array($type));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($ret)){
            return count($ret);
        } else {
            return 0;
        }
    }

// EXISTS
    public function existsId($id){
            $req = $this->_bdd->prepare('SELECT id FROM item WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM item WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
            $req = $this->_bdd->prepare('SELECT id FROM item WHERE name = ?');
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
        FROM item        
        WHERE ( description like :term 
            OR name like :term) '.$usable . $limit.'');
        
        $req->execute(array("term" => $term));
        $result =  $req->fetchAll();
        
        if(!empty($result)){
            return $this->bdd2objects($result);
        } else {
            return array();
        }
    }

// WRITE
    public function add(Item $item){
        $req = $this->_bdd->prepare('INSERT INTO item(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    level,
                    description,
                    effect,
                    type,
                    recepe,
                    actif,
                    twohands,
                    po,
                    pa,
                    price,
                    rarity,
                    usable
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :level,
                    :description,
                    :effect,
                    :type,
                    :recepe,
                    :actif,
                    :twohands,
                    :po,
                    :pa,
                    :price,
                    :rarity,
                    :usable
                )');

        return $req->execute(array(
            'uniqid' => $item->getUniqid(),
            'timestamp_add' => $item->getTimestamp_add(),
            'timestamp_updated' => $item->getTimestamp_updated(),
            'name' => $item->getName(),
            'level' => $item->getLevel(),
            'description' => $item->getDescription(),
            'effect' => $item->getEffect(),
            'type' => $item->getType(),
            'recepe' => $item->getRecepe(),
            'actif' => $item->getActif(),
            'twohands' => $item->getTwohands(),
            'po' => $item->getPo(),
            'pa' => $item->getPa(),
            'price' => $item->getPrice(),
            "rarity" => $item->getRarity(),
            "usable" => $item->getUsable()
        ));
    }
    public function update(Item $item){
        $req = $this->_bdd->prepare('UPDATE item SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    level=:level,
                    description=:description,
                    effect=:effect,
                    type=:type,
                    recepe=:recepe,
                    actif=:actif,
                    twohands=:twohands,
                    po=:po,
                    pa=:pa,
                    price=:price,
                    rarity=:rarity,
                    usable=:usable
            WHERE id = :id');

        return $req->execute(array(
            'id' => $item->getId(),
            'uniqid' => $item->getUniqid(),
            'timestamp_add' => $item->getTimestamp_add(),
            'timestamp_updated' => $item->getTimestamp_updated(),
            'name' => $item->getName(),
            'level' => $item->getLevel(),
            'description' => $item->getDescription(),
            'effect' => $item->getEffect(),
            'type' => $item->getType(),
            'recepe' => $item->getRecepe(),
            'actif' => $item->getActif(),
            'twohands' => $item->getTwohands(),
            'po' => $item->getPo(),
            'pa' => $item->getPa(),
            'price' => $item->getPrice(),
            "rarity" => $item->getRarity(),
            "usable" => $item->getUsable()
            ));
    }
    public function delete(Item $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);

        $req = $this->_bdd->prepare('DELETE FROM item WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Item($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
