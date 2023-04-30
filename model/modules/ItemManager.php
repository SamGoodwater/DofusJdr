<?php
class ItemManager extends Manager
{

    public function getAll(array $type = [], array $level = [], bool $usable = false, int $offset = -1, int $limit = -1){
        $limitClause = ($limit != -1 && $offset != -1) ? 'LIMIT :offset, :limit' : '';
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        if(!empty($level)){
            $whereClause .= ($whereClause == '') ? 'WHERE level IN (' : ' AND level IN (';
            foreach ($level as $value) {
                if($value > 0 && $value <= 20){
                    $whereClause .= $value .",";
                }
            }
            $whereClause = substr($whereClause, 0, -1).")";
        }
        if(!empty($type)){
            $whereClause .= ($whereClause == '') ? 'WHERE type IN (' : ' AND type IN (';
            foreach ($type as $value) {
                if(in_array($value, Item::TYPES)){
                    $whereClause .= $value .",";
                }
            }
            $whereClause = substr($whereClause, 0, -1).")";
        }
        $orderByClause = 'ORDER BY usable DESC, level ASC';
        $requete = 'SELECT * FROM item ' . $whereClause . ' ' . $orderByClause . ' ' . $limitClause;

        $req = $this->_bdd->prepare($requete);
        if($limit != -1 && $offset != -1){
            $req->bindValue(':offset', $offset, PDO::PARAM_INT);
            $req->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
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
    
    public function countAll(bool $usable = false, array $level = array(), array $type = array()){
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        if(!empty($level)){
            $whereClause .= ($whereClause == '') ? 'WHERE level IN (' : ' AND level IN (';
            foreach ($level as $value) {
                if($value > 0 && $value <= 20){
                    $whereClause .= $value .",";
                }
            }
            $whereClause = substr($whereClause, 0, -1).")";
        }
        if(!empty($type)){
            $whereClause .= ($whereClause == '') ? 'WHERE type IN (' : ' AND type IN (';
            foreach ($type as $value) {
                if(in_array($value, Item::TYPES)){
                    $whereClause .= $value .",";
                }
            }
            $whereClause = substr($whereClause, 0, -1).")";
        }
        $requete = 'SELECT id FROM item ' . $whereClause;
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
