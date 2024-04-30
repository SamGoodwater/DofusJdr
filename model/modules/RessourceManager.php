<?php
class RessourceManager extends Manager
{

// GET
    public function getAll(bool $usable = false, int $offset = -1, int $limit = -1){
        $limitClause = ($limit != -1 && $offset != -1) ? 'LIMIT :offset, :limit' : '';
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $orderByClause = 'ORDER BY usable DESC';
        $requete = 'SELECT * FROM ressource ' . $whereClause . ' ' . $orderByClause . ' ' . $limitClause;

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
        $post = $this->_bdd->prepare('SELECT * FROM ressource WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Ressource($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM ressource WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Ressource($this->securite($req));
    }
    public function getFromName($name){
        $post = $this->_bdd->prepare('SELECT * FROM ressource WHERE name = ?');
        $post->execute(array($name));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Ressource($this->securite($req));
    }
    public function getFromDofusdb_id($id){
        $post = $this->_bdd->prepare('SELECT * FROM ressource WHERE dofusdb_id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Ressource($this->securite($req));
    }

    public function countAll(bool $usable = false){
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $requete = 'SELECT id FROM ressource ' . $whereClause;
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
            $req = $this->_bdd->prepare('SELECT id FROM ressource WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM ressource WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
            $req = $this->_bdd->prepare('SELECT id FROM ressource WHERE name = ?');
            $req->execute(array($this->securite($name)));
            return $req->rowCount();
    }
    public function existsDofusdb_id($id){
        $req = $this->_bdd->prepare('SELECT id FROM ressource WHERE dofusdb_id = ?');
        $req->execute(array($this->securite($id)));
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
        FROM ressource        
        WHERE ( description like :term 
            OR type like :term 
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
    public function add(Ressource $ressource){
        $req = $this->_bdd->prepare('INSERT INTO ressource(
                    dofusdb_id,
                    official_id,
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    description,
                    level,
                    type,
                    price,
                    weight,
                    rarity,
                    usable,
                    dofus_version
                   )
            VALUES (
                    :dofusdb_id,
                    :official_id,
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :description,
                    :level,
                    :type,
                    :price,
                    :weight,
                    :rarity,
                    :usable,
                    :dofus_version
                )');

        return $req->execute(array(
            'dofusdb_id' => $ressource->getDofusdb_id(),
            'official_id' => $ressource->getOfficial_id(),
            'uniqid' => $ressource->getUniqid(),
            'timestamp_add' => $ressource->getTimestamp_add(),
            'timestamp_updated' => $ressource->getTimestamp_updated(),
            'name' => $ressource->getName(),
            'description' => $ressource->getDescription(),
            'level' => $ressource->getLevel(),
            'type' => $ressource->getType(),
            'price' => $ressource->getPrice(),
            'weight' => $ressource->getWeight(),
            'rarity' => $ressource->getRarity(),
            "usable" => $ressource->getUsable(),
            "dofus_version" => $ressource->getDofus_version()
        ));
    }
    public function update(Ressource $ressource){
        $req = $this->_bdd->prepare('UPDATE ressource SET
                    dofusdb_id=:dofusdb_id,
                    official_id=:official_id,
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    description=:description,
                    level=:level,
                    type=:type,
                    price=:price,
                    weight=:weight,
                    rarity=:rarity,
                    usable=:usable,
                    dofus_version=:dofus_version
            WHERE id = :id');

        return $req->execute(array(
            'id' => $ressource->getId(),
            'dofusdb_id' => $ressource->getDofusdb_id(),
            'official_id' => $ressource->getOfficial_id(),
            'uniqid' => $ressource->getUniqid(),
            'timestamp_add' => $ressource->getTimestamp_add(),
            'timestamp_updated' => $ressource->getTimestamp_updated(),
            'name' => $ressource->getName(),
            'description' => $ressource->getDescription(),
            'level' => $ressource->getLevel(),
            'type' => $ressource->getType(),
            'price' => $ressource->getPrice(),
            'weight' => $ressource->getWeight(),
            'rarity' => $ressource->getRarity(),
            "usable" => $ressource->getUsable(),
            "dofus_version" => $ressource->getDofus_version()
            ));
    }
    public function delete(Ressource $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);

        // Supprimer les ingrédients dans les recettes d'item

        FileManager::remove($object->getFile('logo'));
        $req = $this->_bdd->prepare('DELETE FROM ressource WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Ressource($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
