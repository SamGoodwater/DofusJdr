<?php
class Mob_raceManager extends Manager
{

// GET
    public function getAll(bool $usable = false, int $offset = -1, int $limit = -1){
        $limitClause = ($limit != -1 && $offset != -1) ? 'LIMIT :offset, :limit' : '';
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $orderByClause = 'ORDER BY usable DESC';
        $requete = 'SELECT * FROM mob_race ' . $whereClause . ' ' . $orderByClause . ' ' . $limitClause;

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
        $post = $this->_bdd->prepare('SELECT * FROM mob_race WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Mob_race($this->securite($req));
    }
    public function getFromName($name){
        $post = $this->_bdd->prepare('SELECT * FROM mob_race WHERE name = ?');
        $post->execute(array($name));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Mob_race($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM mob_race WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Mob_race($this->securite($req));
    }

    public function countAll(bool $usable = false){
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $requete = 'SELECT id FROM mob_race ' . $whereClause;
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
            $req = $this->_bdd->prepare('SELECT id FROM mob_race WHERE id = ?');
            $req->execute(array($id));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM mob_race WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
            $req = $this->_bdd->prepare('SELECT id FROM mob_race WHERE name = ?');
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
        FROM mob_race        
        WHERE ( name like :term ) '.$usable . $limit.'');
        
        $req->execute(array("term" => $term));
        $result =  $req->fetchAll();
        
        if(!empty($result)){
            return $this->bdd2objects($result);
        } else {
            return array();
        }
    }

// WRITE
    public function add(Mob_race $object){
        $req = $this->_bdd->prepare('INSERT INTO mob_race(
                    dofusdb_id,
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    super_race,
                    usable
                   )
            VALUES (
                    :dofusdb_id,
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :super_race,
                    :usable
                )');

        return $req->execute(array(
            'dofusdb_id' => $object->getDofusdb_id(),
            'uniqid' => $object->getUniqid(),
            'timestamp_add' => $object->getTimestamp_add(),
            'timestamp_updated' => $object->getTimestamp_updated(),
            'name' => $object->getName(),
            'super_race' => $object->getSuper_race(),
            "usable" => $object->getUsable()
        ));
    }
    public function update(Mob_race $object){
        $req = $this->_bdd->prepare('UPDATE mob_race SET
                    dofusdb_id=:dofusdb_id,
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    super_race=:super_race,
                    usable=:usable
            WHERE id = :id');

        return $req->execute(array(
            'id' => $object->getId(),
            'dofusdb_id' => $object->getDofusdb_id(),
            'uniqid' => $object->getUniqid(),
            'timestamp_add' => $object->getTimestamp_add(),
            'timestamp_updated' => $object->getTimestamp_updated(),
            'name' => $object->getName(),
            'super_race' => $object->getSuper_race(),
            "usable" => $object->getUsable()
        ));

    }
    public function delete(Mob_race $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);

        $this->removeLinkRaceInMob($object);
        
        FileManager::remove($object->getFile('logo'));
        $req = $this->_bdd->prepare('DELETE FROM mob_race WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// REMOVE LINK RACE IN MOB
    public function removeLinkRaceInMob(Mob_race $object){
        $req = $this->_bdd->prepare('UPDATE mob SET
                race = NULL
        WHERE race=:race');
        return $req->execute(array("race" => $object->getId()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Mob_race($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }
}
