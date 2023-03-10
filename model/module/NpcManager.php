<?php
class NpcManager extends Manager
{

// GET
    public function getAll(){
        $requete = "SELECT * FROM npc ORDER BY level"; 
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
        $post = $this->_bdd->prepare('SELECT * FROM npc WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Npc($this->securite($req));
    }
    public function getFromName($name){
        $post = $this->_bdd->prepare('SELECT * FROM npc WHERE name = ?');
        $post->execute(array($name));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Npc($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM npc WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Npc($req);
    }

    public function countAll(){
        $requete = "SELECT id FROM npc";    
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
            $req = $this->_bdd->prepare('SELECT id FROM npc WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM npc WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsName($name){
            $req = $this->_bdd->prepare('SELECT id FROM npc WHERE name = ?');
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
        FROM npc        
        WHERE ( other_info like :term 
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
    public function add(Npc $object){
        $req = $this->_bdd->prepare('INSERT INTO npc(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    classe,
                    story,
                    historical,
                    alignment,
                    level,
                    trait,
                    other_info,
                    age,
                    size,
                    weight,
                    life,
                    pa,
                    pm,
                    po,
                    ini,
                    invocation,
                    touch,
                    ca,
                    dodge_pa,
                    dodge_pm,
                    fuite,
                    tacle,
                    vitality,
                    sagesse,
                    strong,
                    intel,
                    agi,
                    chance,
                    res_neutre,
                    res_terre,
                    res_feu,
                    res_air,
                    res_eau,
                    acrobatie,
                    discretion,
                    escamotage,
                    athletisme,
                    intimidation,
                    arcane,
                    histoire,
                    investigation,
                    nature,
                    religion,
                    dressage,
                    medecine,
                    perception,
                    perspicacite,
                    survie,
                    persuasion,
                    representation,
                    supercherie,
                    kamas,
                    drop_,
                    other_item,
                    other_consomable,
                    other_spell
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :classe,
                    :story,
                    :historical,
                    :alignment,
                    :level,
                    :trait,
                    :other_info,
                    :age,
                    :size,
                    :weight,
                    :life,
                    :pa,
                    :pm,
                    :po,
                    :ini,
                    :invocation,
                    :touch,
                    :ca,
                    :dodge_pa,
                    :dodge_pm,
                    :fuite,
                    :tacle,
                    :vitality,
                    :sagesse,
                    :strong,
                    :intel,
                    :agi,
                    :chance,
                    :res_neutre,
                    :res_terre,
                    :res_feu,
                    :res_air,
                    :res_eau,
                    :acrobatie,
                    :discretion,
                    :escamotage,
                    :athletisme,
                    :intimidation,
                    :arcane,
                    :histoire,
                    :investigation,
                    :nature,
                    :religion,
                    :dressage,
                    :medecine,
                    :perception,
                    :perspicacite,
                    :survie,
                    :persuasion,
                    :representation,
                    :supercherie,
                    :kamas,
                    :drop_,
                    :other_item,
                    :other_consomable,
                    :other_spell
                )');
        
        return $req->execute(array(
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "timestamp_updated" => $object->getTimestamp_updated(),
            "name" => $object->getName(),
            "classe" => $object->getClasse(),
            "story" => $object->getStory(),
            "historical" => $object->getHistorical(),
            "alignment" => $object->getAlignment(),
            "level" => $object->getLevel(),
            "trait" => $object->getTrait(),
            "other_info" => $object->getOther_info(),
            "age" => $object->getAge(),
            "size" => $object->getSize(),
            "weight" => $object->getWeight(),
            "life" => $object->getLife(),
            "pa" => $object->getPa(),
            "pm" => $object->getPm(),
            "po" => $object->getPo(),
            "ini" => $object->getIni(),
            "invocation" => $object->getInvocation(),
            "touch" => $object->getTouch(),
            "ca" => $object->getCa(),
            "dodge_pa" => $object->getDodge_pa(),
            "dodge_pm" => $object->getDodge_pm(),
            "fuite" => $object->getFuite(),
            "tacle" => $object->getTacle(),
            "vitality" => $object->getVitality(),
            "sagesse" => $object->getSagesse(),
            "strong" => $object->getStrong(),
            "intel" => $object->getIntel(),
            "agi" => $object->getAgi(),
            "chance" => $object->getChance(),
            "res_neutre" => $object->getRes_neutre(),
            "res_terre" => $object->getRes_terre(),
            "res_feu" => $object->getRes_feu(),
            "res_air" => $object->getRes_air(),
            "res_eau" => $object->getRes_eau(),
            "acrobatie" => $object->getAcrobatie(),
            "discretion" => $object->getDiscretion(),
            "escamotage" => $object->getEscamotage(),
            "athletisme" => $object->getAthletisme(),
            "intimidation" => $object->getIntimidation(),
            "arcane" => $object->getArcane(),
            "histoire" => $object->getHistoire(),
            "investigation" => $object->getInvestigation(),
            "nature" => $object->getNature(),
            "religion" => $object->getReligion(),
            "dressage" => $object->getDressage(),
            "medecine" => $object->getMedecine(),
            "perception" => $object->getPerception(),
            "perspicacite" => $object->getPerspicacite(),
            "survie" => $object->getSurvie(),
            "persuasion" => $object->getPersuasion(),
            "representation" => $object->getRepresentation(),
            "supercherie" => $object->getSupercherie(),
            "kamas" => $object->getKamas(),
            "drop_" => $object->getDrop_(),
            "other_item" => $object->getOther_item(),
            "other_consomable" => $object->getOther_consomable(),
            "other_spell" => $object->getOther_spell()
        ));
    }
    public function update(Npc $object){
        $req = $this->_bdd->prepare('UPDATE npc SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    classe=:classe,
                    story=:story,
                    historical=:historical,
                    alignment=:alignment,
                    level=:level,
                    trait=:trait,
                    other_info=:other_info,
                    age=:age,
                    size=:size,
                    weight=:weight,
                    life=:life,
                    pa=:pa,
                    pm=:pm,
                    po=:po,
                    ini=:ini,
                    invocation=:invocation,
                    touch=:touch,
                    ca=:ca,
                    dodge_pa=:dodge_pa,
                    dodge_pm=:dodge_pm,
                    fuite=:fuite,
                    tacle=:tacle,
                    vitality=:vitality,
                    sagesse=:sagesse,
                    strong=:strong,
                    intel=:intel,
                    agi=:agi,
                    chance=:chance,
                    res_neutre=:res_neutre,
                    res_terre=:res_terre,
                    res_feu=:res_feu,
                    res_air=:res_air,
                    res_eau=:res_eau,
                    acrobatie=:acrobatie,
                    discretion=:discretion,
                    escamotage=:escamotage,
                    athletisme=:athletisme,
                    intimidation=:intimidation,
                    arcane=:arcane,
                    histoire=:histoire,
                    investigation=:investigation,
                    nature=:nature,
                    religion=:religion,
                    dressage=:dressage,
                    medecine=:medecine,
                    perception=:perception,
                    perspicacite=:perspicacite,
                    survie=:survie,
                    persuasion=:persuasion,
                    representation=:representation,
                    supercherie=:supercherie,
                    kamas=:kamas,
                    drop_=:drop_,
                    other_item=:other_item,
                    other_consomable=:other_consomable,
                    other_spell=:other_spell
            WHERE id=:id');

        return $req->execute(array(
            "id" => $object->getId(),
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "timestamp_updated" => $object->getTimestamp_updated(),
            "name" => $object->getName(),
            "classe" => $object->getClasse(),
            "story" => $object->getStory(),
            "historical" => $object->getHistorical(),
            "alignment" => $object->getAlignment(),
            "level" => $object->getLevel(),
            "trait" => $object->getTrait(),
            "other_info" => $object->getOther_info(),
            "age" => $object->getAge(),
            "size" => $object->getSize(),
            "weight" => $object->getWeight(),
            "life" => $object->getLife(),
            "pa" => $object->getPa(),
            "pm" => $object->getPm(),
            "po" => $object->getPo(),
            "ini" => $object->getIni(),
            "invocation" => $object->getInvocation(),
            "touch" => $object->getTouch(),
            "ca" => $object->getCa(),
            "dodge_pa" => $object->getDodge_pa(),
            "dodge_pm" => $object->getDodge_pm(),
            "fuite" => $object->getFuite(),
            "tacle" => $object->getTacle(),
            "vitality" => $object->getVitality(),
            "sagesse" => $object->getSagesse(),
            "strong" => $object->getStrong(),
            "intel" => $object->getIntel(),
            "agi" => $object->getAgi(),
            "chance" => $object->getChance(),
            "res_neutre" => $object->getRes_neutre(),
            "res_terre" => $object->getRes_terre(),
            "res_feu" => $object->getRes_feu(),
            "res_air" => $object->getRes_air(),
            "res_eau" => $object->getRes_eau(),
            "acrobatie" => $object->getAcrobatie(),
            "discretion" => $object->getDiscretion(),
            "escamotage" => $object->getEscamotage(),
            "athletisme" => $object->getAthletisme(),
            "intimidation" => $object->getIntimidation(),
            "arcane" => $object->getArcane(),
            "histoire" => $object->getHistoire(),
            "investigation" => $object->getInvestigation(),
            "nature" => $object->getNature(),
            "religion" => $object->getReligion(),
            "dressage" => $object->getDressage(),
            "medecine" => $object->getMedecine(),
            "perception" => $object->getPerception(),
            "perspicacite" => $object->getPerspicacite(),
            "survie" => $object->getSurvie(),
            "persuasion" => $object->getPersuasion(),
            "representation" => $object->getRepresentation(),
            "supercherie" => $object->getSupercherie(),
            "kamas" => $object->getKamas(),
            "drop_" => $object->getDrop_(),
            "other_item" => $object->getOther_item(),
            "other_consomable" => $object->getOther_consomable(),
            "other_spell" => $object->getOther_spell()
        ));

    }
    public function delete(Npc $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);

        $this->removeAllLinkConsumableFromNpc($object);    
        $this->removeAllLinkItemFromNpc($object);    
        $this->removeAllLinkSpellFromNpc($object);    
        $req = $this->_bdd->prepare('DELETE FROM npc WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// Link Consumable
    public function getLinkConsumable(Npc $npc){
        $req = $this->_bdd->prepare('SELECT * FROM link_npc_consumable INNER JOIN consumable ON link_npc_consumable.id_consumable = consumable.id WHERE link_npc_consumable.id_npc = ?');
        $req->execute(array($npc->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        foreach ($ret as $link) {
            $return[] = [
                "obj" => new Consumable($this->securite($link)),
                "quantity" => $link['quantity']
            ];
        }
        return $return;
    }
    public function getLinkConsumableFromConsumable(Npc $npc , Consumable $consumable){
        $req = $this->_bdd->prepare('SELECT * FROM link_npc_consumable INNER JOIN npc ON link_npc_consumable.id_consumable = consumable.id WHERE link_npc_consumable.id_npc = ? AND link_npc_consumable.id_item = ?');
        $req->execute(array($npc->getId(), $consumable->getId()));
        $link = $req->fetch(PDO::FETCH_ASSOC);
        if(empty($link)){return "";}
        $return = array();
        $return = [
            "obj" => new Consumable($this->securite($link)),
            "quantity" => $link['quantity']
        ];
        return $return;
    }
    public function existsLinkConsumable(Npc $npc, Consumable $consumable){
        $req = $this->_bdd->prepare('SELECT id FROM link_npc_consumable WHERE id_npc = ? AND id_consumable = ?');
        $req->execute(array($npc->getId(), $consumable->getId()));
        return $req->rowCount();
    }
    public function addLinkConsumable(Npc $npc, Consumable $consumable, string | int $quantity = null){
        $req = $this->_bdd->prepare('INSERT INTO link_npc_consumable(
                    id_npc,
                    id_consumable,
                    quantity
                )
            VALUES (
                    :id_npc,
                    :id_consumable,
                    :quantity
                )');

        return $req->execute(array(
            "id_npc" => $npc->getId(),
            "id_consumable"=> $consumable->getId(),
            "quantity" => $quantity
        ));

        // Renvoi le dernier ingredient ajout??
        $post = $this->_bdd->prepare('SELECT id FROM link_npc_consumable ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function updateLinkConsumable(Npc $npc, Consumable $consumable, string | int $quantity = null){
        $req = $this->_bdd->prepare('UPDATE link_npc_consumable SET
                quantity=:quantity
            WHERE   id_npc = :id_npc AND
                    id_consumable = :id_consumable');

        return $req->execute(array(
            "id_npc" => $npc->getId(),
            "id_consumable"=> $consumable->getId(),
            "quantity" => $quantity
        ));
    }
    public function removeLinkConsumable(Npc $npc, Consumable $consumable){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_consumable WHERE id_npc = :id_npc AND id_consumable = :id_consumable');
        return $req->execute(array("id_npc" =>  $npc->getId(), "id_consumable" =>  $consumable->getId()));
    }
    public function removeAllLinkConsumableFromNpc(Npc $npc){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_consumable WHERE id_npc = :id');
        $req->execute(array("id" =>  $npc->getId()));
    }
    public function removeAllLinkConsumableFromConsumable(Consumable $consumable){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_consumable WHERE id_consumable = :id');
        $req->execute(array("id" =>  $consumable->getId()));
    }

// Link Item
    public function getLinkItem(Npc $npc){
        $req = $this->_bdd->prepare('SELECT * FROM link_npc_item INNER JOIN item ON link_npc_item.id_item = item.id WHERE link_npc_item.id_npc = ?');
        $req->execute(array($npc->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        foreach ($ret as $link) {
            $return[] = [
                "obj" => new Item($this->securite($link)),
                "quantity" => $link['quantity']
            ];
        }
        return $return;
    }
    public function getLinkItemFromItem(Npc $npc , Item $item){
        $req = $this->_bdd->prepare('SELECT * FROM link_npc_item INNER JOIN item ON link_npc_item.id_item = item.id WHERE link_npc_item.id_npc = ? AND link_npc_item.id_item = ?');
        $req->execute(array($npc->getId(), $item->getId()));
        $link = $req->fetchAll(PDO::FETCH_ASSOC)[0];
        if(empty($link)){return array();}
        $return = array();
        $return = [
            "obj" => new Item($this->securite($link)),
            "quantity" => $link['quantity']
        ];
        return $return;
    }
    public function existsLinkItem(Npc $npc, Item $item){
        $req = $this->_bdd->prepare('SELECT id FROM link_npc_item WHERE id_npc = ? AND id_item = ?');
        $req->execute(array($npc->getId(), $item->getId()));
        return $req->rowCount();
    } 
    public function addLinkItem(Npc $npc, Item $item, string | int $quantity = null){
        $req = $this->_bdd->prepare('INSERT INTO link_npc_item(
                    id_npc,
                    id_item,
                    quantity
                )
            VALUES (
                    :id_npc,
                    :id_item,
                    :quantity
                )');

        return $req->execute(array(
            "id_npc" => $npc->getId(),
            "id_item"=> $item->getId(),
            "quantity" => $quantity
        ));

        // Renvoi le dernier ingredient ajout??
        $post = $this->_bdd->prepare('SELECT id FROM link_npc_item ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function updateLinkItem(Npc $npc, Item $item, string | int $quantity = null){
        $req = $this->_bdd->prepare('UPDATE link_npc_item SET
                quantity=:quantity
            WHERE   id_npc = :id_npc AND
                    id_item = :id_item');

        return $req->execute(array(
            "id_npc" => $npc->getId(),
            "id_item"=> $item->getId(),
            "quantity" => $quantity
        ));
    }
    public function removeLinkItem(Npc $npc, Item $item){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_item WHERE id_npc = :id_npc AND id_item = :id_item');
        return $req->execute(array("id_npc" =>  $npc->getId(), "id_item" =>  $item->getId()));
    }
    public function removeAllLinkItemFromNpc(Npc $npc){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_item WHERE id_npc = :id');
        return $req->execute(array("id" =>  $npc->getId()));
    }
    public function removeAllLinkItemFromItem(Item $item){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_item WHERE id_item = :id');
        return $req->execute(array("id" =>  $item->getId()));
    }

// Link Spell
    public function getLinkSpell(Npc $npc){
        $req = $this->_bdd->prepare('   SELECT *, link_npc_spell.id AS link_id 
                                        FROM link_npc_spell
                                        INNER JOIN spell ON link_npc_spell.id_spell = spell.id
                                        WHERE id_npc = ?
                                        ORDER BY spell.level ASC');
        $req->execute(array($npc->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        foreach ($ret as $link) {
            $return[] = new Spell($this->securite($link));
        }
        return $return;
    }
    public function existsLinkSpell(Npc $npc, Spell $spell){
        $req = $this->_bdd->prepare('SELECT id FROM link_npc_spell WHERE id_npc = ? AND id_spell = ?');
        $req->execute(array($npc->getId(), $spell->getId()));
        return $req->rowCount();
    }
    public function addLinkSpell(Npc $npc, Spell $spell){
        if($this->existsLinkSpell($npc, $spell)){return false;}
        $req = $this->_bdd->prepare('INSERT INTO link_npc_spell(
                    id_npc,
                    id_spell
                )
            VALUES (
                    :id_npc,
                    :id_spell
                )');

        return $req->execute(array(
            "id_npc" => $npc->getId(),
            "id_spell"=> $spell->getId()
        ));

        // Renvoi le dernier ingredient ajout??
        $post = $this->_bdd->prepare('SELECT id FROM link_npc_spell ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function removeLinkSpell(Npc $npc, Spell $spell){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_spell WHERE id_npc = :id_npc AND id_spell = :id_spell');
        return $req->execute(array("id_npc" =>  $npc->getId(), "id_spell" =>  $spell->getId()));
    }
    public function removeAllLinkSpellFromNpc(Npc $npc){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_spell WHERE id_npc = :id');
        return $req->execute(array("id" =>  $npc->getId()));
    }
    public function removeAllLinkSpellFromSpell(Spell $spell){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_spell WHERE id_spell = :id');
        return $req->execute(array("id" =>  $spell->getId()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Npc($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
