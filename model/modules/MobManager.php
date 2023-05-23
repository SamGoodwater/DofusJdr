<?php
class MobManager extends Manager
{

// GET
    public function getAll(bool $usable = false, int $offset = -1, int $limit = -1){
        $limitClause = ($limit != -1 && $offset != -1) ? 'LIMIT :offset, :limit' : '';
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $orderByClause = 'ORDER BY usable DESC, level ASC';
        $requete = 'SELECT * FROM mob ' . $whereClause . ' ' . $orderByClause . ' ' . $limitClause;

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
        $post = $this->_bdd->prepare('SELECT * FROM mob WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Mob($this->securite($req));
    }
    public function getFromName($name){
        $post = $this->_bdd->prepare('SELECT * FROM mob WHERE name = ?');
        $post->execute(array($name));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Mob($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM mob WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Mob($this->securite($req));
    }

    public function countAll(bool $usable = false){
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $requete = 'SELECT id FROM mob ' . $whereClause;
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
            $req = $this->_bdd->prepare('SELECT id FROM mob WHERE id = ?');
            $req->execute(array($id));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM mob WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
            $req = $this->_bdd->prepare('SELECT id FROM mob WHERE name = ?');
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
        FROM mob        
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
    public function add(Mob $object){
        $req = $this->_bdd->prepare('INSERT INTO mob(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    description,
                    level,
                    vitality,
                    pa,
                    pm,
                    po,
                    ini,
                    touch,
                    life,
                    sagesse,
                    strong,
                    intel,
                    agi,
                    chance,
                    ca,
                    fuite,
                    tacle,
                    dodge_pa,
                    dodge_pm,
                    res_neutre,
                    res_terre,
                    res_feu,
                    res_air,
                    res_eau,
                    zone,
                    hostility,
                    trait,
                    size,
                    usable
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :description,
                    :level,
                    :vitality,
                    :pa,
                    :pm,
                    :po,
                    :ini,
                    :touch,
                    :life,
                    :sagesse,
                    :strong,
                    :intel,
                    :agi,
                    :chance,
                    :ca,
                    :fuite,
                    :tacle,
                    :dodge_pa,
                    :dodge_pm,
                    :res_neutre,
                    :res_terre,
                    :res_feu,
                    :res_air,
                    :res_eau,
                    :zone,
                    :hostility,
                    :trait,
                    :size,
                    :usable
                )');

        return $req->execute(array(
            'uniqid' => $object->getUniqid(),
            'timestamp_add' => $object->getTimestamp_add(),
            'timestamp_updated' => $object->getTimestamp_updated(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'level' => $object->getLevel(),
            'vitality' => $object->getVitality(),
            'pa' => $object->getPa(),
            'pm' => $object->getPm(),
            'po' => $object->getPo(),
            'ini' => $object->getIni(),
            'touch' => $object->getTouch(),
            'life' => $object->getLife(),
            'sagesse' => $object->getSagesse(),
            'strong' => $object->getStrong(),
            'intel' => $object->getIntel(),
            'agi' => $object->getAgi(),
            'chance' => $object->getChance(),
            "ca" => $object->getCa(),
            "fuite" => $object->getFuite(),
            "tacle" => $object->getTacle(),
            'dodge_pa' => $object->getDodge_pa(),
            'dodge_pm' => $object->getDodge_pm(),
            'res_neutre' => $object->getRes_neutre(),
            'res_terre' => $object->getRes_terre(),
            'res_feu' => $object->getRes_feu(),
            'res_air' => $object->getRes_air(),
            'res_eau' => $object->getRes_eau(),
            'zone' => $object->getZone(),
            'hostility' => $object->getHostility(),
            'trait' => $object->getTrait(),
            'size' => $object->getSize(),
            "usable" => $object->getUsable()
        ));
    }
    public function update(Mob $object){
        $req = $this->_bdd->prepare('UPDATE mob SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    description=:description,
                    level=:level,
                    vitality=:vitality,
                    pa=:pa,
                    pm=:pm,
                    po=:po,
                    ini=:ini,
                    touch=:touch,
                    life=:life,
                    sagesse=:sagesse,
                    strong=:strong,
                    intel=:intel,
                    agi=:agi,
                    chance=:chance,
                    ca=:ca,
                    fuite=:fuite,
                    tacle=:tacle,
                    dodge_pa=:dodge_pa,
                    dodge_pm=:dodge_pm,
                    res_neutre=:res_neutre,
                    res_terre=:res_terre,
                    res_feu=:res_feu,
                    res_air=:res_air,
                    res_eau=:res_eau,
                    zone=:zone,
                    hostility=:hostility,
                    trait=:trait,
                    size=:size,
                    usable=:usable
            WHERE id = :id');

        return $req->execute(array(
            'id' => $object->getId(),
            'uniqid' => $object->getUniqid(),
            'timestamp_add' => $object->getTimestamp_add(),
            'timestamp_updated' => $object->getTimestamp_updated(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'level' => $object->getLevel(),
            'vitality' => $object->getVitality(),
            'pa' => $object->getPa(),
            'pm' => $object->getPm(),
            "po" => $object->getPo(),
            'ini' => $object->getIni(),
            'touch' => $object->getTouch(),
            'life' => $object->getLife(),
            'sagesse' => $object->getSagesse(),
            'strong' => $object->getStrong(),
            'intel' => $object->getIntel(),
            'agi' => $object->getAgi(),
            'chance' => $object->getChance(),
            "ca" => $object->getCa(),
            "fuite" => $object->getFuite(),
            "tacle" => $object->getTacle(),
            'dodge_pa' => $object->getDodge_pa(),
            'dodge_pm' => $object->getDodge_pm(),
            'res_neutre' => $object->getRes_neutre(),
            'res_terre' => $object->getRes_terre(),
            'res_feu' => $object->getRes_feu(),
            'res_air' => $object->getRes_air(),
            'res_eau' => $object->getRes_eau(),
            'zone' => $object->getZone(),
            'hostility' => $object->getHostility(),
            'trait' => $object->getTrait(),
            'size' => $object->getSize(),
            "usable" => $object->getUsable()
            ));

    }
    public function delete(Mob $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);

        $this->removeAllLinkCapabilityFromMob($object);
        $this->removeAllLinkSpellFromMob($object);
        
        $req = $this->_bdd->prepare('DELETE FROM mob WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// Link Spell
    public function getLinkSpell(Mob $mob){
        $req = $this->_bdd->prepare('   SELECT *, link_mob_spell.id AS link_id 
                                        FROM link_mob_spell
                                        INNER JOIN spell ON link_mob_spell.id_spell = spell.id
                                        WHERE id_mob = ?
                                        ORDER BY spell.level ASC');
        $req->execute(array($mob->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        foreach ($ret as $link) {
            $return[] = new Spell($this->securite($link));
        }
        return $return;
    }
    public function existsLinkSpell(Mob $mob, Spell $spell){
        $req = $this->_bdd->prepare('SELECT id FROM link_mob_spell WHERE id_mob = ? AND id_spell = ?');
        $req->execute(array($mob->getId(), $spell->getId()));
        return $req->rowCount();
    }
    public function addLinkSpell(Mob $mob, Spell $spell){
        $req = $this->_bdd->prepare('INSERT INTO link_mob_spell(
                    id_mob,
                    id_spell
                )
            VALUES (
                    :id_mob,
                    :id_spell
                )');

        return $req->execute(array(
            "id_mob" => $mob->getId(),
            "id_spell"=> $spell->getId()
        ));

        // Renvoi le dernier ingredient ajouté
        $post = $this->_bdd->prepare('SELECT id FROM link_mob_spell ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function removeLinkSpell(Mob $mob, Spell $spell){
        $req = $this->_bdd->prepare('DELETE FROM link_mob_spell WHERE id_mob = :id_mob AND id_spell = :id_spell');
        return $req->execute(array("id_mob" =>  $mob->getId(), "id_spell" =>  $spell->getId()));
    }
    public function removeAllLinkSpellFromMob(Mob $mob){
        $req = $this->_bdd->prepare('DELETE FROM link_mob_spell WHERE id_mob = :id');
        return $req->execute(array("id" =>  $mob->getId()));
    }
    public function removeAllLinkSpellFromSpell(Spell $spell){
        $req = $this->_bdd->prepare('DELETE FROM link_mob_spell WHERE id_spell = :id');
        return $req->execute(array("id" =>  $spell->getId()));
    }

// Link Capability
    public function getLinkCapability(Mob $mob){
        $req = $this->_bdd->prepare('   SELECT *, link_mob_capability.id AS link_id 
                                        FROM link_mob_capability
                                        INNER JOIN capability ON link_mob_capability.id_capability = capability.id
                                        WHERE id_mob = ?
                                        ORDER BY capability.level ASC');
        $req->execute(array($mob->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        foreach ($ret as $link) {
            $return[] = new Capability($this->securite($link));
        }
        return $return;
    }
    public function existsLinkCapability(Mob $mob, Capability $capability){
        $req = $this->_bdd->prepare('SELECT id FROM link_mob_capability WHERE id_mob = ? AND id_capability = ?');
        $req->execute(array($mob->getId(), $capability->getId()));
        return $req->rowCount();
    }
    public function addLinkCapability(Mob $mob, Capability $capability){
        $req = $this->_bdd->prepare('INSERT INTO link_mob_capability(
                    id_mob,
                    id_capability
                )
            VALUES (
                    :id_mob,
                    :id_capability
                )');

        return $req->execute(array(
            "id_mob" => $mob->getId(),
            "id_capability"=> $capability->getId()
        ));

        // Renvoi le dernier ingredient ajouté
        $post = $this->_bdd->prepare('SELECT id FROM link_mob_capability ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function removeLinkCapability(Mob $mob, Capability $capability){
        $req = $this->_bdd->prepare('DELETE FROM link_mob_capability WHERE id_mob = :id_mob AND id_capability = :id_capability');
        return $req->execute(array("id_mob" =>  $mob->getId(), "id_capability" =>  $capability->getId()));
    }
    public function removeAllLinkCapabilityFromMob(Mob $mob){
        $req = $this->_bdd->prepare('DELETE FROM link_mob_capability WHERE id_mob = :id');
        return $req->execute(array("id" =>  $mob->getId()));
    }
    public function removeAllLinkCapabilityFromCapability(Capability $capability){
        $req = $this->_bdd->prepare('DELETE FROM link_mob_capability WHERE id_capability = :id');
        return $req->execute(array("id" =>  $capability->getId()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Mob($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }
}
