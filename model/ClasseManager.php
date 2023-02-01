<?php
class ClasseManager extends Manager
{

// GET
    public function getAll(){
        $requete = "SELECT * FROM classe ORDER BY id"; 
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

    public function countAll(){
        $requete = "SELECT id FROM classe";    
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
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    description_fast,
                    description,
                    life,
                    specificity,
                    weapons_of_choice,
                    trait,
                    usable
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :description_fast,
                    :description,
                    :life,
                    :specificity,
                    :weapons_of_choice,
                    :trait,
                    :usable
                )');

        return $req->execute(array(
            'uniqid' => $object->getUniqid(),
            'timestamp_add' => $object->getTimestamp_add(),
            'timestamp_updated' => $object->getTimestamp_updated(),
            'name' => $object->getName(),
            'description_fast' => $object->getDescription_fast(),
            'description' => $object->getDescription(),
            'life' => $object->getLife(),
            'specificity' => $object->getSpecificity(),
            'weapons_of_choice' => $object->getWeapons_of_choice(),
            'trait' => $object->getTrait(),
            "usable" => $object->getUsable()
        ));
    }
    public function update(Classe $object){
        $req = $this->_bdd->prepare('UPDATE classe SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    description_fast=:description_fast,
                    description=:description,
                    life=:life,
                    specificity=:specificity,
                    weapons_of_choice=:weapons_of_choice,
                    trait=:trait,
                    usable=:usable
            WHERE id = :id');

        return $req->execute(array(
            'id' => $object->getId(),
            'uniqid' => $object->getUniqid(),
            'timestamp_add' => $object->getTimestamp_add(),
            'timestamp_updated' => $object->getTimestamp_updated(),
            'name' => $object->getName(),
            'description_fast' => $object->getDescription_fast(),
            'description' => $object->getDescription(),
            'life' => $object->getLife(),
            'specificity' => $object->getSpecificity(),
            'weapons_of_choice' => $object->getWeapons_of_choice(),
            'trait' => $object->getTrait(),
            "usable" => $object->getUsable()
        ));
    }
    public function delete(Classe $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);
        
        $req = $this->_bdd->prepare('DELETE FROM classe WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// Link Spell
    public function getLinkSpell(Classe $classe){
        $req = $this->_bdd->prepare('SELECT * FROM link_classe_spell 
                                                INNER JOIN classe ON link_classe_spell.id_classe = classe.id  
                                                INNER JOIN spell ON link_classe_spell.id_spell = spell.id  
                                                WHERE link_classe_spell.id_classe = ?
                                                ORDER BY spell.level');
        $req->execute(array($classe->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        $manager = new SpellManager;
        foreach ($ret as $link) {
            if($manager->existsId($link["id_spell"])){
                $return[] = $manager->getFromId($link["id_spell"]);
            }
        }
        return $return;
    }
    public function existsLinkSpell(Classe $classe, Spell $spell){
        $req = $this->_bdd->prepare('SELECT id FROM link_classe_spell WHERE id_classe = ? AND id_spell = ?');
        $req->execute(array($classe->getId(), $spell->getId()));
        return $req->rowCount();
    }
    public function addLinkSpell(Classe $classe, Spell $spell){
        if($this->existsLinkSpell($classe, $spell)){return false;}
        $req = $this->_bdd->prepare('INSERT INTO link_classe_spell(
                    id_classe,
                    id_spell
                )
            VALUES (
                    :id_classe,
                    :id_spell
                )');

        return $req->execute(array(
            "id_classe" => $classe->getId(),
            "id_spell"=> $spell->getId()
        ));

        // Renvoi le dernier ingredient ajouté
        $post = $this->_bdd->prepare('SELECT id FROM link_classe_spell ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function removeLinkSpell(Classe $classe, Spell $spell){
        $req = $this->_bdd->prepare('DELETE FROM link_classe_spell WHERE id_classe = :id_classe AND id_spell = :id_spell');
        return $req->execute(array("id_classe" =>  $classe->getId(), "id_spell" =>  $spell->getId()));
    }
    public function removeAllLinkSpellFromMob(Classe $classe){
        $req = $this->_bdd->prepare('DELETE FROM link_classe_spell WHERE id_classe = :id');
        return $req->execute(array("id" =>  $classe->getId()));
    }
    public function removeAllLinkSpellFromSpell(Spell $spell){
        $req = $this->_bdd->prepare('DELETE FROM link_classe_spell WHERE id_spell = :id');
        return $req->execute(array("id" =>  $spell->getId()));
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
