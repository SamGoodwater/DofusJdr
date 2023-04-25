<?php
class SpellManager extends Manager
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
                    if(in_array($value, Spell::CATEGORY)){
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

        $requete = "SELECT * FROM spell WHERE ".$usable_text." ".$element_text." ".$category_text." ".$level_text." ORDER BY usable DESC, level"; 

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
        $post = $this->_bdd->prepare('SELECT * FROM spell WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Spell($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM spell WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Spell($this->securite($req));
    }
    public function getFromName($name){
        $post = $this->_bdd->prepare('SELECT * FROM spell WHERE name = ?');
        $post->execute(array($name));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Spell($this->securite($req));
    }

    public function countAll(){
        $requete = "SELECT id FROM spell";    
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
            $req = $this->_bdd->prepare('SELECT id FROM spell WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM spell WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
            $req = $this->_bdd->prepare('SELECT id FROM spell WHERE name = ?');
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
        FROM spell        
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
    public function add(Spell $spell){
        $req = $this->_bdd->prepare('INSERT INTO spell(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    description,
                    effect,
                    level,
                    po,
                    po_editable,
                    pa,
                    cast_per_turn,
                    sight_line,
                    number_between_two_cast,
                    element,
                    category,
                    id_invocation,
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
                    :pa,
                    :cast_per_turn,
                    :sight_line,
                    :number_between_two_cast,
                    :element,
                    :category,
                    :id_invocation,
                    :is_magic,
                    :powerful,
                    :usable
                )');

        return $req->execute(array(
            'uniqid' => $spell->getUniqid(),
            'timestamp_add' => $spell->getTimestamp_add(),
            'timestamp_updated' => $spell->getTimestamp_updated(),
            'name' => $spell->getName(),
            'description' => $spell->getDescription(),
            'effect' => $spell->getEffect(),
            'level' => $spell->getLevel(),
            'po' => $spell->getPo(),
            'po_editable' => $spell->getPo_editable(),
            'pa' => $spell->getPa(),
            'cast_per_turn' => $spell->getCast_per_turn(),
            'sight_line' => $spell->getSight_line(),
            'number_between_two_cast' => $spell->getNumber_between_two_cast(),
            "element" => $spell->getElement(),
            "category" => $spell->getCategory(),
            "id_invocation" => $spell->getId_invocation(),
            "is_magic" => $spell->getIs_magic(),
            "powerful" => $spell->getPowerful(),
            "usable" => $spell->getUsable()
        ));
    }
    public function update(Spell $spell){
        $req = $this->_bdd->prepare('UPDATE spell SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    description=:description,
                    effect=:effect,
                    level=:level,
                    po=:po,
                    po_editable=:po_editable,
                    pa=:pa,
                    cast_per_turn=:cast_per_turn,
                    sight_line=:sight_line,
                    number_between_two_cast=:number_between_two_cast,
                    element=:element,
                    category=:category,
                    id_invocation=:id_invocation,
                    is_magic=:is_magic,
                    powerful=:powerful,
                    usable=:usable
            WHERE id = :id');

        return $req->execute(array(
            'id' => $spell->getId(),
            'uniqid' => $spell->getUniqid(),
            'timestamp_add' => $spell->getTimestamp_add(),
            'timestamp_updated' => $spell->getTimestamp_updated(),
            'name' => $spell->getName(),
            'description' => $spell->getDescription(),
            'effect' => $spell->getEffect(),
            'level' => $spell->getLevel(),
            'po' => $spell->getPo(),
            'po_editable' => $spell->getPo_editable(),
            'pa' => $spell->getPa(),
            'cast_per_turn' => $spell->getCast_per_turn(),
            'sight_line' => $spell->getSight_line(),
            'number_between_two_cast' => $spell->getNumber_between_two_cast(),
            "element" => $spell->getElement(),
            "category" => $spell->getCategory(),
            "id_invocation" => $spell->getId_invocation(),
            "is_magic" => $spell->getIs_magic(),
            "powerful" => $spell->getPowerful(),
            "usable" => $spell->getUsable()
            ));
    }
    public function delete(Spell $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);
        $this->removeAllLinkTypeFromSpell($object);
        $req = $this->_bdd->prepare('DELETE FROM spell WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

    // Link Type
    public function getLinkType(Spell $spell){
        $req = $this->_bdd->prepare('SELECT type FROM link_spell_type INNER JOIN spell ON link_spell_type.id_spell = spell.id WHERE link_spell_type.id_spell = ? ORDER BY link_spell_type.type');
        $req->execute(array($spell->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        foreach ($ret as $link) {
            if(in_array($link['type'], Spell::TYPE)){
                $return[] = $link['type'];
            }
        }
        return $return;
    }
    public function existsLinkType(Spell $spell, $type){
        $req = $this->_bdd->prepare('SELECT id FROM link_spell_type WHERE id_spell = ? AND type = ?');
        $req->execute(array($spell->getId(), $type));
        return $req->rowCount();
    } 
    public function addLinkType(Spell $spell, $type){
        if($this->existsLinkType($spell, $type)){return false;}
        $req = $this->_bdd->prepare('INSERT INTO link_spell_type(
                    id_spell,
                    type
                )
            VALUES (
                    :id_spell,
                    :type
                )');

        return $req->execute(array(
            "id_spell" => $spell->getId(),
            "type"=> $type,
        ));

        // Renvoi le dernier ingredient ajouté
        $post = $this->_bdd->prepare('SELECT id FROM link_spell_type ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function removeLinkType(Spell $spell, $type){
        $req = $this->_bdd->prepare('DELETE FROM link_spell_type WHERE id_spell = :id_spell AND type = :type');
        return $req->execute(array("id_spell" =>  $spell->getId(), "type" =>  $type));
    }
    public function removeAllLinkTypeFromSpell(Spell $spell){
        $req = $this->_bdd->prepare('DELETE FROM link_spell_type WHERE id_spell = :id');
        return $req->execute(array("id" =>  $spell->getId()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Spell($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
