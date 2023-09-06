<?php
class SocialManager extends Manager
{

// GET
    public function getAll(bool $usable = false, int $offset = -1, int $limit = -1){
        $limitClause = ($limit != -1 && $offset != -1) ? 'LIMIT :offset, :limit' : '';
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $orderByClause = 'ORDER BY usable DESC';
        $requete = 'SELECT * FROM social ' . $whereClause . ' ' . $orderByClause . ' ' . $limitClause;

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
        $post = $this->_bdd->prepare('SELECT * FROM social WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Social($req);
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM social WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Social($req);
    }

    public function countAll(bool $usable = false){
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $requete = 'SELECT id FROM social ' . $whereClause;
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
            $req = $this->_bdd->prepare('SELECT id FROM social WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM social WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
        $req = $this->_bdd->prepare('SELECT id FROM social WHERE name = ?');
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
        FROM social        
        WHERE ( description like :term 
            OR text like :term
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
    public function add(Social $object){

        $req = $this->_bdd->prepare('INSERT INTO social(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    text,
                    link,
                    description,
                    visible,
                    usable
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :text,
                    :link,
                    :description,
                    :visible,
                    :usable
                )');
                         
        return $req->execute(array(
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "timestamp_updated" => $object->getTimestamp_updated(),
            "name" => $object->getName(),
            "text" => $object->getText(),
            "link" => $object->getLink(),
            "description" => $object->getDescription(),
            "visible" => $object->getVisible(),
            "usable" => $object->getUsable()
        ));
    }
    public function update(Social $object){
        $req = $this->_bdd->prepare('UPDATE social SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    text=:text,
                    link=:link,
                    description=:description,
                    visible=:visible,
                    usable=:usable
            WHERE id=:id');

        return $req->execute(array(
            "id" => $object->getId(),
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "timestamp_updated" => $object->getTimestamp_updated(),
            "name" => $object->getName(),
            "text" => $object->getText(),
            "link" => $object->getLink(),
            "description" => $object->getDescription(),
            "visible" => $object->getVisible(),
            "usable" => $object->getUsable()
        ));

    }
    public function delete(Social $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);
        $req = $this->_bdd->prepare('DELETE FROM social WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Social($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
