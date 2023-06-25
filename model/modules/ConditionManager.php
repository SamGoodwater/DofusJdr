<?php
class ConditionManager extends Manager
{

// GET
    public function getAll(bool $usable = false, int $offset = -1, int $limit = -1){
        $limitClause = ($limit != -1 && $offset != -1) ? 'LIMIT :offset, :limit' : '';
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $orderByClause = 'ORDER BY usable DESC';
        $requete = 'SELECT * FROM condition_ ' . $whereClause . ' ' . $orderByClause . ' ' . $limitClause;

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
        $post = $this->_bdd->prepare('SELECT * FROM condition_ WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Condition($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM condition_ WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Condition($this->securite($req));
    }
    public function getFromName($name){
        $post = $this->_bdd->prepare('SELECT * FROM condition_ WHERE name = ?');
        $post->execute(array($name));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Condition($this->securite($req));
    }

    public function countAll(bool $usable = false){
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $requete = 'SELECT id FROM condition_ ' . $whereClause;
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
            $req = $this->_bdd->prepare('SELECT id FROM condition_ WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM condition_ WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
            $req = $this->_bdd->prepare('SELECT id FROM condition_ WHERE name = ?');
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
        FROM condition_        
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
    public function add(Condition $condition){
        $req = $this->_bdd->prepare('INSERT INTO condition_(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    description,
                    is_unbewitchable,
                    usable
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :description,
                    :is_unbewitchable,
                    :usable
                )');

        return $req->execute(array(
            'uniqid' => $condition->getUniqid(),
            'timestamp_add' => $condition->getTimestamp_add(),
            'timestamp_updated' => $condition->getTimestamp_updated(),
            'name' => $condition->getName(),
            'description' => $condition->getDescription(),
            'is_unbewitchable' => $condition->getIs_unbewitchable(),
            "usable" => $condition->getUsable()
        ));
    }
    public function update(Condition $condition){
        $req = $this->_bdd->prepare('UPDATE condition_ SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    description=:description,
                    is_unbewitchable=:is_unbewitchable,
                    usable=:usable
            WHERE id = :id');

        return $req->execute(array(
            'id' => $condition->getId(),
            'uniqid' => $condition->getUniqid(),
            'timestamp_add' => $condition->getTimestamp_add(),
            'timestamp_updated' => $condition->getTimestamp_updated(),
            'name' => $condition->getName(),
            'description' => $condition->getDescription(),
            'is_unbewitchable' => $condition->getIs_unbewitchable(),
            "usable" => $condition->getUsable()
            ));
    }
    public function delete(Condition $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);
        $req = $this->_bdd->prepare('DELETE FROM condition_ WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Condition($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
