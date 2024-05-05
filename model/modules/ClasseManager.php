<?php
class ClasseManager extends Manager
{

// GET
    public function getAll(bool $usable = false, int $offset = -1, int $limit = -1){
        $limitClause = ($limit != -1 && $offset != -1) ? 'LIMIT :offset, :limit' : '';
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $orderByClause = 'ORDER BY usable DESC';
        $requete = 'SELECT * FROM classe ' . $whereClause . ' ' . $orderByClause . ' ' . $limitClause;

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
        $post = $this->_bdd->prepare('SELECT * FROM classe WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Classe($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM classe WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Classe($this->securite($req));
    }

    public function countAll(bool $usable = false){
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $requete = 'SELECT id FROM classe ' . $whereClause;
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
            $req = $this->_bdd->prepare('SELECT id FROM classe WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM classe WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
            $req = $this->_bdd->prepare('SELECT id FROM classe WHERE name = ?');
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
        FROM classe        
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
    public function add(Classe $object){

        $req = $this->_bdd->prepare('INSERT INTO classe(
                    official_id,
                    dofusdb_id,
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    description_fast,
                    description,
                    life,
                    life_dice,
                    specificity,
                    weapons_of_choice,
                    trait,
                    usable,
                    dofus_version
                   )
            VALUES (
                    :official_id,
                    :dofusdb_id,
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :description_fast,
                    :description,
                    :life,
                    :life_dice,
                    :specificity,
                    :weapons_of_choice,
                    :trait,
                    :usable,
                    :dofus_version
                )');

        return $req->execute(array(
            'official_id' => $object->getOfficial_id(),
            'dofusdb_id' => $object->getDofusdb_id(),
            'uniqid' => $object->getUniqid(),
            'timestamp_add' => $object->getTimestamp_add(),
            'timestamp_updated' => $object->getTimestamp_updated(),
            'name' => $object->getName(),
            'description_fast' => $object->getDescription_fast(),
            'description' => $object->getDescription(),
            'life' => $object->getLife(),
            'life_dice' => $object->getLife_dice(),
            'specificity' => $object->getSpecificity(),
            'weapons_of_choice' => $object->getWeapons_of_choice(),
            'trait' => $object->getTrait(),
            "usable" => $object->getUsable(),
            "dofus_version" => $object->getDofus_version()
        ));
    }
    public function update(Classe $object){
        $req = $this->_bdd->prepare('UPDATE classe SET
                    official_id=:official_id,
                    dofusdb_id=:dofusdb_id,
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    description_fast=:description_fast,
                    description=:description,
                    life=:life,
                    life_dice=:life_dice,
                    specificity=:specificity,
                    weapons_of_choice=:weapons_of_choice,
                    trait=:trait,
                    usable=:usable,
                    dofus_version=:dofus_version
            WHERE id = :id');

        return $req->execute(array(
            'id' => $object->getId(),
            'official_id' => $object->getOfficial_id(),
            'dofusdb_id' => $object->getDofusdb_id(),
            'uniqid' => $object->getUniqid(),
            'timestamp_add' => $object->getTimestamp_add(),
            'timestamp_updated' => $object->getTimestamp_updated(),
            'name' => $object->getName(),
            'description_fast' => $object->getDescription_fast(),
            'description' => $object->getDescription(),
            'life' => $object->getLife(),
            'life_dice' => $object->getLife_dice(),
            'specificity' => $object->getSpecificity(),
            'weapons_of_choice' => $object->getWeapons_of_choice(),
            'trait' => $object->getTrait(),
            "usable" => $object->getUsable(),
            "dofus_version" => $object->getDofus_version()
        ));
    }
    public function delete(Classe $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);
        
        $this->removeAllLinkSpellFromClasse($object);
        $this->removeAllLinkCapabilityFromClasse($object);

        FileManager::remove($object->getFile('img'));
        FileManager::remove($object->getFile('logo'));
        $req = $this->_bdd->prepare('DELETE FROM classe WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// Link Spell
    // Renvoi un tableau de tableau de spell
    public function getLinkSpell(Classe $classe){
        $class = new ReflectionClass('Spell');
        $properties = $class->getProperties(ReflectionProperty::IS_PRIVATE);
        // Properties name inclu dans la classe mère
        $attributeNames = array(
            "id","uniqid","timestamp_add","timestamp_updated","usable"
        );
        foreach ($properties as $property) {
            $attributeNames[] = lcfirst(substr($property->getName(), 1));
        }
        $aliasColumns = array();
        foreach ($attributeNames as $attributeName) {
            $aliasColumns[] = "spell.$attributeName AS spell_$attributeName";
        }

        $aliasColumnsString = implode(", ", $aliasColumns);
        $req = $this->_bdd->prepare("SELECT link_classe_spell.id_group, $aliasColumnsString
                                    FROM link_classe_spell
                                    LEFT JOIN spell ON link_classe_spell.id_spell = spell.id
                                    WHERE id_classe = ?
                                    ORDER BY link_classe_spell.id_group ASC, spell.level ASC");
        $req->execute(array($classe->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);

        $groups = array();
        foreach ($ret as $row) {
            $groupID = $row["id_group"];
            if (!isset($groups[$groupID])) {
                $groups[$groupID] = array();
            }
            $spell = array();
            foreach ($attributeNames as $attributeName) {
                $spell[$attributeName] = $row["spell_$attributeName"];
            }
            $groups[$groupID][] = !empty($spell["id"]) && $spell["id"] != 0 ? new Spell($spell) : null;
        }
        if(empty($groups)){return "";}
        return $groups;
    }
    public function getGroupLinkSpellFromClassAndSpell(Classe $classe, Spell $spell){
        $req = $this->_bdd->prepare('SELECT id_group FROM link_classe_spell WHERE id_classe = :id_classe AND id_spell = :id_spell');
        $req->execute(array("id_classe" => $classe->getId(), "id_spell" => $spell->getId()));
        $result = $req->fetch(PDO::FETCH_ASSOC);
        if($result) {
            return $result['id_group'];
        } else {
            return null; // Le lien entre la classe et le sort n'existe pas
        }
    }    
    public function existsLinkSpell(Classe $classe, Spell $spell){
        $req = $this->_bdd->prepare('SELECT id FROM link_classe_spell WHERE id_classe = :id_classe AND id_spell = :id_spell');
        $req->execute(array("id_classe" => $classe->getId(), "id_spell" => $spell->getId()));
        return $req->rowCount();
    }  
    public function addLinkSpell(Classe $classe, Spell $spell, $groupID){
        if(!Content::exist($spell)){$spell = new Spell(["id" => 0]);}
    
        if($this->existsLinkSpell($classe, $spell) && $spell->getId() != 0){return false;}
    
        $req = $this->_bdd->prepare('INSERT INTO link_classe_spell(
                    id_classe,
                    id_spell,
                    id_group
                )
            VALUES (
                    :id_classe,
                    :id_spell,
                    :id_group
                )');
    
        return $req->execute(array(
            "id_classe" => $classe->getId(),
            "id_spell"=> $spell->getId(),
            "id_group"=> $groupID
        ));
    }
    public function updateLinkSpell(Classe $classe, Spell $spell, Spell $spellNew, $groupID){
        if(!Content::exist($spell)){$spell->setId(0);}
        if($spell->getId() == $spellNew->getId()){return false;}
        if($this->existsLinkSpell($classe, $spellNew)){return false;}
    
        $req = $this->_bdd->prepare('SELECT id_group FROM link_classe_spell WHERE id_classe = :idC AND id_spell = :idS');
        $req->execute(["idC" => $classe->getId(), ":idS" => $spell->getId()]);
        $link = $req->fetch(PDO::FETCH_ASSOC);
        if(empty($link)){return false;}
    
        $req = $this->_bdd->prepare('UPDATE link_classe_spell SET id_spell = :id_spellNew, id_group = :id_group WHERE id_classe = :id_classe AND id_spell = :id_spell');
        return $req->execute(array(
            "id_classe" => $classe->getId(),
            "id_spell" => $spell->getId(),
            "id_spellNew" => $spellNew->getId(),
            "id_group" => $groupID
        ));
    }
    public function removeLinkSpell(Classe $classe, Spell $spell, $groupID){
        if($this->existsLinkSpell($classe, $spell) == 0){return false;} // Le lien n'existe pas
        $req = $this->_bdd->prepare('DELETE FROM link_classe_spell WHERE id_classe = :id_classe AND id_spell = :id_spell AND id_group = :id_group');
        return $req->execute(array("id_classe" =>  $classe->getId(), "id_spell" =>  $spell->getId(), "id_group" =>  $groupID));
    }
    public function removeAllLinkSpellFromClasse(Classe $classe){
        $req = $this->_bdd->prepare('DELETE FROM link_classe_spell WHERE id_classe = :id');
        return $req->execute(array("id" =>  $classe->getId()));
    }
    public function removeAllLinkSpellFromSpell(Spell $spell){
        $req = $this->_bdd->prepare('DELETE FROM link_classe_spell WHERE id_spell = :id');
        return $req->execute(array("id" =>  $spell->getId()));
    }
    public function removeAllLinkSpellFromGroup($groupID){
        $req = $this->_bdd->prepare('DELETE FROM link_classe_spell WHERE id_group = :id');
        return $req->execute(array("id" =>  $groupID));
    }
    public function cleanLinkSpell(){
        $req = $this->_bdd->prepare('DELETE FROM link_classe_spell WHERE id_spell = 0');
        return $req->execute();
    }

 // Link Capability
    public function getLinkCapability(Classe $classe){
        $req = $this->_bdd->prepare('   SELECT *, link_classe_capability.id AS link_id 
                                        FROM link_classe_capability
                                        INNER JOIN capability ON link_classe_capability.id_capability = capability.id
                                        WHERE id_classe = ?
                                        ORDER BY capability.level ASC');
        $req->execute(array($classe->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        foreach ($ret as $link) {
            $return[] = new Capability($this->securite($link));
        }
        return $return;
    }
    public function existsLinkCapability(Classe $classe, Capability $capability){
        $req = $this->_bdd->prepare('SELECT id FROM link_classe_capability WHERE id_classe = ? AND id_capability = ?');
        $req->execute(array($classe->getId(), $capability->getId()));
        return $req->rowCount();
    }
    public function addLinkCapability(Classe $classe, Capability $capability){
        if($this->existsLinkCapability($classe, $capability)){return false;}
        $req = $this->_bdd->prepare('INSERT INTO link_classe_capability(
                    id_classe,
                    id_capability
                )
            VALUES (
                    :id_classe,
                    :id_capability
                )');

        return $req->execute(array(
            "id_classe" => $classe->getId(),
            "id_capability"=> $capability->getId()
        ));

        // Renvoi le dernier ingredient ajouté
        $post = $this->_bdd->prepare('SELECT id FROM link_classe_capability ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function removeLinkCapability(Classe $classe, Capability $capability){
        $req = $this->_bdd->prepare('DELETE FROM link_classe_capability WHERE id_classe = :id_classe AND id_capability = :id_capability');
        return $req->execute(array("id_classe" =>  $classe->getId(), "id_capability" =>  $capability->getId()));
    }
    public function removeAllLinkCapabilityFromClasse(Classe $classe){
        $req = $this->_bdd->prepare('DELETE FROM link_classe_capability WHERE id_classe = :id');
        return $req->execute(array("id" =>  $classe->getId()));
    }
    public function removeAllLinkCapabilityFromCapability(Capability $capability){
        $req = $this->_bdd->prepare('DELETE FROM link_classe_capability WHERE id_capability = :id');
        return $req->execute(array("id" =>  $capability->getId()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Classe($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
