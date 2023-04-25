<?php
class CapabilityManager extends Manager
{

// GET
    public function getAll($element = ["all"], $category = ["all"], $level = ["all"], $usable = 0){
        $usable_text = "(usable = 1 OR usable = 0)"; if($usable) { $usable_text = "usable = 1"; }
        $category_text = "";
        if(is_array($category) && !empty($category)){
            if($category[0] == "all"){
                $category_text = "";
            } else {
                foreach ($category as $value) {
                    if(in_array($value, Capability::CATEGORY)){
                        $category_text .= $value .",";
                    }
                }
            }
        }
        if($category_text != ""){
            $category_text = " AND category IN (".substr($category_text, 0, -1).")";
        }

        $element_text = "";
        if(is_array($element) && !empty($element)){
            if($element[0] == "all"){
                $element_text = "";
            } else {
                foreach ($element as $value) {
                    if(isset(Spell::ELEMENT[$value])){
                        $element_text .= $value .",";
                    }
                }
            }
        }
        if($element_text != ""){
            $element_text = " AND element IN (".substr($element_text, 0, -1).")";
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

        $requete = "SELECT * FROM capability WHERE ".$usable_text." ".$element_text." ".$category_text." ".$level_text." ORDER BY usable DESC, level"; 

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

    public function countAll(){
        $requete = "SELECT id FROM capability";    
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
