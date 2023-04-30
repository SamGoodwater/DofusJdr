<?php
class CapabilityManager extends Manager
{

// GET
    public function getAll(array $element = [], array $category = [], array $level = [], bool $usable = false, int $offset = -1, int $limit = -1){
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
        if(!empty($category)){
            $whereClause .= ($whereClause == '') ? 'WHERE category IN (' : ' AND category IN (';
            foreach ($category as $value) {
                if(in_array($value, Capability::CATEGORY)){
                    $whereClause .= $value .",";
                }
            }
            $whereClause = substr($whereClause, 0, -1).")";
        }
        if(!empty($element)){
            $whereClause .= ($whereClause == '') ? 'WHERE element IN (' : ' AND element IN (';
            foreach ($element as $value) {
                if(isset(Spell::ELEMENT[$value])){
                    $whereClause .= $value .",";
                }
            }
            $whereClause = substr($whereClause, 0, -1).")";
        }
        $orderByClause = 'ORDER BY usable DESC, level ASC';
        $requete = 'SELECT * FROM capability ' . $whereClause . ' ' . $orderByClause . ' ' . $limitClause;

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
        $post = $this->_bdd->prepare('SELECT * FROM capability WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Capability($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM capability WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Capability($this->securite($req));
    }
    public function getFromName($name){
        $post = $this->_bdd->prepare('SELECT * FROM capability WHERE name = ?');
        $post->execute(array($name));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Capability($this->securite($req));
    }

    public function countAll(bool $usable = false, array $level = array(), array $category = array(), array $element = array()){
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
        if(!empty($category)){
            $whereClause .= ($whereClause == '') ? 'WHERE category IN (' : ' AND category IN (';
            foreach ($category as $value) {
                if(in_array($value, Capability::CATEGORY)){
                    $whereClause .= $value .",";
                }
            }
            $whereClause = substr($whereClause, 0, -1).")";
        }
        if(!empty($element)){
            $whereClause .= ($whereClause == '') ? 'WHERE element IN (' : ' AND element IN (';
            foreach ($element as $value) {
                if(isset(Spell::ELEMENT[$value])){
                    $whereClause .= $value .",";
                }
            }
            $whereClause = substr($whereClause, 0, -1).")";
        }
        $requete = 'SELECT id FROM capability ' . $whereClause;
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
            $req = $this->_bdd->prepare('SELECT id FROM capability WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM capability WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
            $req = $this->_bdd->prepare('SELECT id FROM capability WHERE name = ?');
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
        FROM capability        
        WHERE ( description like :term 
            OR effect like :term 
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
    public function add(Capability $capability){
        $req = $this->_bdd->prepare('INSERT INTO capability(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    description,
                    effect,
                    level,
                    po,
                    po_editable,
                    time_before_use_again,
                    element,
                    category,
                    is_magic,
                    powerful,
                    usable
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :description,
                    :effect,
                    :level,
                    :po,
                    :po_editable,
                    :time_before_use_again,
                    :element,
                    :category,
                    :is_magic,
                    :powerful,
                    :usable
                )');

        return $req->execute(array(
            'uniqid' => $capability->getUniqid(),
            'timestamp_add' => $capability->getTimestamp_add(),
            'timestamp_updated' => $capability->getTimestamp_updated(),
            'name' => $capability->getName(),
            'description' => $capability->getDescription(),
            'effect' => $capability->getEffect(),
            'level' => $capability->getLevel(),
            'po' => $capability->getPo(),
            'po_editable' => $capability->getPo_editable(),
            'time_before_use_again' => $capability->getTime_before_use_again(),
            "element" => $capability->getElement(),
            "category" => $capability->getCategory(),
            "is_magic" => $capability->getIs_magic(),
            "powerful" => $capability->getPowerful(),
            "usable" => $capability->getUsable()
        ));
    }
    public function update(Capability $capability){
        $req = $this->_bdd->prepare('UPDATE capability SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    description=:description,
                    effect=:effect,
                    level=:level,
                    po=:po,
                    po_editable=:po_editable,
                    time_before_use_again=:time_before_use_again,
                    element=:element,
                    category=:category,
                    is_magic=:is_magic,
                    powerful=:powerful,
                    usable=:usable
            WHERE id = :id');

        return $req->execute(array(
            'id' => $capability->getId(),
            'uniqid' => $capability->getUniqid(),
            'timestamp_add' => $capability->getTimestamp_add(),
            'timestamp_updated' => $capability->getTimestamp_updated(),
            'name' => $capability->getName(),
            'description' => $capability->getDescription(),
            'effect' => $capability->getEffect(),
            'level' => $capability->getLevel(),
            'po' => $capability->getPo(),
            'po_editable' => $capability->getPo_editable(),
            'time_before_use_again' => $capability->getTime_before_use_again(),
            "element" => $capability->getElement(),
            "category" => $capability->getCategory(),
            "is_magic" => $capability->getIs_magic(),
            "powerful" => $capability->getPowerful(),
            "usable" => $capability->getUsable()
            ));
    }
    public function delete(Capability $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);
        $this->removeAllLinkTypeFromCapability($object);
        $req = $this->_bdd->prepare('DELETE FROM capability WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

    // Link Type
    public function getLinkType(Capability $capability){
        $req = $this->_bdd->prepare('SELECT type FROM link_capability_type INNER JOIN capability ON link_capability_type.id_capability = capability.id WHERE link_capability_type.id_capability = ? ORDER BY link_capability_type.type');
        $req->execute(array($capability->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        foreach ($ret as $link) {
            if(in_array($link['type'], Capability::TYPE)){
                $return[] = $link['type'];
            }
        }
        return $return;
    }
    public function existsLinkType(Capability $capability, $type){
        $req = $this->_bdd->prepare('SELECT id FROM link_capability_type WHERE id_capability = ? AND type = ?');
        $req->execute(array($capability->getId(), $type));
        return $req->rowCount();
    } 
    public function addLinkType(Capability $capability, $type){
        if($this->existsLinkType($capability, $type)){return false;}
        $req = $this->_bdd->prepare('INSERT INTO link_capability_type(
                    id_capability,
                    type
                )
            VALUES (
                    :id_capability,
                    :type
                )');

        return $req->execute(array(
            "id_capability" => $capability->getId(),
            "type"=> $type,
        ));

        // Renvoi le dernier ingredient ajouté
        $post = $this->_bdd->prepare('SELECT id FROM link_capability_type ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function removeLinkType(Capability $capability, $type){
        $req = $this->_bdd->prepare('DELETE FROM link_capability_type WHERE id_capability = :id_capability AND type = :type');
        return $req->execute(array("id_capability" =>  $capability->getId(), "type" =>  $type));
    }
    public function removeAllLinkTypeFromCapability(Capability $capability){
        $req = $this->_bdd->prepare('DELETE FROM link_capability_type WHERE id_capability = :id');
        return $req->execute(array("id" =>  $capability->getId()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Capability($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
