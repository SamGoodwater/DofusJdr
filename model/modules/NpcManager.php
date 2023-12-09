<?php
class NpcManager extends Manager
{

// GET
    public function getAll(bool $usable = false, int $offset = -1, int $limit = -1){
        $limitClause = ($limit != -1 && $offset != -1) ? 'LIMIT :offset, :limit' : '';
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $orderByClause = 'ORDER BY usable DESC, level ASC';
        $requete = 'SELECT * FROM npc ' . $whereClause . ' ' . $orderByClause . ' ' . $limitClause;

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

    public function countAll(bool $usable = false){
        $whereClause = ($usable) ? 'WHERE usable = 1' : '';
        $requete = 'SELECT id FROM npc ' . $whereClause;
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
                    description,
                    location,
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
                    do_fixe_neutre,
                    do_fixe_terre,
                    do_fixe_feu,
                    do_fixe_air,
                    do_fixe_eau,
                    do_fixe_multiple,
                    res_neutre,
                    res_terre,
                    res_feu,
                    res_air,
                    res_eau,
                    acrobatie_bonus,
                    discretion_bonus,
                    escamotage_bonus,
                    athletisme_bonus,
                    intimidation_bonus,
                    arcane_bonus,
                    histoire_bonus,
                    investigation_bonus,
                    nature_bonus,
                    religion_bonus,
                    dressage_bonus,
                    medecine_bonus,
                    perception_bonus,
                    perspicacite_bonus,
                    survie_bonus,
                    persuasion_bonus,
                    representation_bonus,
                    supercherie_bonus,
                    acrobatie_mastery,
                    discretion_mastery,
                    escamotage_mastery,
                    athletisme_mastery,
                    intimidation_mastery,
                    arcane_mastery,
                    histoire_mastery,
                    investigation_mastery,
                    nature_mastery,
                    religion_mastery,
                    dressage_mastery,
                    medecine_mastery,
                    perception_mastery,
                    perspicacite_mastery,
                    survie_mastery,
                    persuasion_mastery,
                    representation_mastery,
                    supercherie_mastery,
                    kamas,
                    drop_,
                    other_item,
                    other_consumable,
                    other_spell
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :classe,
                    :description,
                    :location,
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
                    :do_fixe_neutre,
                    :do_fixe_terre,
                    :do_fixe_feu,
                    :do_fixe_air,
                    :do_fixe_eau,
                    :do_fixe_multiple,
                    :res_neutre,
                    :res_terre,
                    :res_feu,
                    :res_air,
                    :res_eau,
                    :acrobatie_bonus,
                    :discretion_bonus,
                    :escamotage_bonus,
                    :athletisme_bonus,
                    :intimidation_bonus,
                    :arcane_bonus,
                    :histoire_bonus,
                    :investigation_bonus,
                    :nature_bonus,
                    :religion_bonus,
                    :dressage_bonus,
                    :medecine_bonus,
                    :perception_bonus,
                    :perspicacite_bonus,
                    :survie_bonus,
                    :persuasion_bonus,
                    :representation_bonus,
                    :supercherie_bonus,
                    :acrobatie_mastery,
                    :discretion_mastery,
                    :escamotage_mastery,
                    :athletisme_mastery,
                    :intimidation_mastery,
                    :arcane_mastery,
                    :histoire_mastery,
                    :investigation_mastery,
                    :nature_mastery,
                    :religion_mastery,
                    :dressage_mastery,
                    :medecine_mastery,
                    :perception_mastery,
                    :perspicacite_mastery,
                    :survie_mastery,
                    :persuasion_mastery,
                    :representation_mastery,
                    :supercherie_mastery,
                    :kamas,
                    :drop_,
                    :other_item,
                    :other_consumable,
                    :other_spell
                )');
        
        return $req->execute(array(
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "timestamp_updated" => $object->getTimestamp_updated(),
            "name" => $object->getName(),
            "classe" => $object->getClasse(),
            'description' => $object->getDescription(),
            'location' => $object->getLocation(),
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
            "do_fixe_neutre" => $object->getDo_fixe_neutre(),
            "do_fixe_terre" => $object->getDo_fixe_terre(),
            "do_fixe_feu" => $object->getDo_fixe_feu(),
            "do_fixe_air" => $object->getDo_fixe_air(),
            "do_fixe_eau" => $object->getDo_fixe_eau(),
            "do_fixe_multiple" => $object->getDo_fixe_multiple(),
            "res_neutre" => $object->getRes_neutre(),
            "res_terre" => $object->getRes_terre(),
            "res_feu" => $object->getRes_feu(),
            "res_air" => $object->getRes_air(),
            "res_eau" => $object->getRes_eau(),
            "acrobatie_bonus" => $object->getAcrobatie_bonus(),
            "discretion_bonus" => $object->getDiscretion_bonus(),
            "escamotage_bonus" => $object->getEscamotage_bonus(),
            "athletisme_bonus" => $object->getAthletisme_bonus(),
            "intimidation_bonus" => $object->getIntimidation_bonus(),
            "arcane_bonus" => $object->getArcane_bonus(),
            "histoire_bonus" => $object->getHistoire_bonus(),
            "investigation_bonus" => $object->getInvestigation_bonus(),
            "nature_bonus" => $object->getNature_bonus(),
            "religion_bonus" => $object->getReligion_bonus(),
            "dressage_bonus" => $object->getDressage_bonus(),
            "medecine_bonus" => $object->getMedecine_bonus(),
            "perception_bonus" => $object->getPerception_bonus(),
            "perspicacite_bonus" => $object->getPerspicacite_bonus(),
            "survie_bonus" => $object->getSurvie_bonus(),
            "persuasion_bonus" => $object->getPersuasion_bonus(),
            "representation_bonus" => $object->getRepresentation_bonus(),
            "supercherie_bonus" => $object->getSupercherie_bonus(),
            "acrobatie_mastery" => $object->getAcrobatie_mastery(),
            "discretion_mastery" => $object->getDiscretion_mastery(),
            "escamotage_mastery" => $object->getEscamotage_mastery(),
            "athletisme_mastery" => $object->getAthletisme_mastery(),
            "intimidation_mastery" => $object->getIntimidation_mastery(),
            "arcane_mastery" => $object->getArcane_mastery(),
            "histoire_mastery" => $object->getHistoire_mastery(),
            "investigation_mastery" => $object->getInvestigation_mastery(),
            "nature_mastery" => $object->getNature_mastery(),
            "religion_mastery" => $object->getReligion_mastery(),
            "dressage_mastery" => $object->getDressage_mastery(),
            "medecine_mastery" => $object->getMedecine_mastery(),
            "perception_mastery" => $object->getPerception_mastery(),
            "perspicacite_mastery" => $object->getPerspicacite_mastery(),
            "survie_mastery" => $object->getSurvie_mastery(),
            "persuasion_mastery" => $object->getPersuasion_mastery(),
            "representation_mastery" => $object->getRepresentation_mastery(),
            "supercherie_mastery" => $object->getSupercherie_mastery(),
            "kamas" => $object->getKamas(),
            "drop_" => $object->getDrop_(),
            "other_item" => $object->getOther_item(),
            "other_consumable" => $object->getOther_consumable(),
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
                    description=:description,
                    location=:location,
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
                    do_fixe_neutre=:do_fixe_neutre,
                    do_fixe_terre=:do_fixe_terre,
                    do_fixe_feu=:do_fixe_feu,
                    do_fixe_air=:do_fixe_air,
                    do_fixe_eau=:do_fixe_eau,
                    do_fixe_multiple=:do_fixe_multiple,
                    res_neutre=:res_neutre,
                    res_terre=:res_terre,
                    res_feu=:res_feu,
                    res_air=:res_air,
                    res_eau=:res_eau,
                    acrobatie_bonus=:acrobatie_bonus,
                    discretion_bonus=:discretion_bonus,
                    escamotage_bonus=:escamotage_bonus,
                    athletisme_bonus=:athletisme_bonus,
                    intimidation_bonus=:intimidation_bonus,
                    arcane_bonus=:arcane_bonus,
                    histoire_bonus=:histoire_bonus,
                    investigation_bonus=:investigation_bonus,
                    nature_bonus=:nature_bonus,
                    religion_bonus=:religion_bonus,
                    dressage_bonus=:dressage_bonus,
                    medecine_bonus=:medecine_bonus,
                    perception_bonus=:perception_bonus,
                    perspicacite_bonus=:perspicacite_bonus,
                    survie_bonus=:survie_bonus,
                    persuasion_bonus=:persuasion_bonus,
                    representation_bonus=:representation_bonus,
                    supercherie_bonus=:supercherie_bonus,
                    acrobatie_mastery=:acrobatie_mastery,
                    discretion_mastery=:discretion_mastery,
                    escamotage_mastery=:escamotage_mastery,
                    athletisme_mastery=:athletisme_mastery,
                    intimidation_mastery=:intimidation_mastery,
                    arcane_mastery=:arcane_mastery,
                    histoire_mastery=:histoire_mastery,
                    investigation_mastery=:investigation_mastery,
                    nature_mastery=:nature_mastery,
                    religion_mastery=:religion_mastery,
                    dressage_mastery=:dressage_mastery,
                    medecine_mastery=:medecine_mastery,
                    perception_mastery=:perception_mastery,
                    perspicacite_mastery=:perspicacite_mastery,
                    survie_mastery=:survie_mastery,
                    persuasion_mastery=:persuasion_mastery,
                    representation_mastery=:representation_mastery,
                    supercherie_mastery=:supercherie_mastery,
                    kamas=:kamas,
                    drop_=:drop_,
                    other_item=:other_item,
                    other_consumable=:other_consumable,
                    other_spell=:other_spell
            WHERE id=:id');

        return $req->execute(array(
            "id" => $object->getId(),
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "timestamp_updated" => $object->getTimestamp_updated(),
            "name" => $object->getName(),
            "classe" => $object->getClasse(),
            'description' => $object->getDescription(),
            'location' => $object->getLocation(),
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
            "do_fixe_neutre" => $object->getDo_fixe_neutre(),
            "do_fixe_terre" => $object->getDo_fixe_terre(),
            "do_fixe_feu" => $object->getDo_fixe_feu(),
            "do_fixe_air" => $object->getDo_fixe_air(),
            "do_fixe_eau" => $object->getDo_fixe_eau(),
            "do_fixe_multiple" => $object->getDo_fixe_multiple(),
            "res_neutre" => $object->getRes_neutre(),
            "res_terre" => $object->getRes_terre(),
            "res_feu" => $object->getRes_feu(),
            "res_air" => $object->getRes_air(),
            "res_eau" => $object->getRes_eau(),
            "acrobatie_bonus" => $object->getAcrobatie_bonus(),
            "discretion_bonus" => $object->getDiscretion_bonus(),
            "escamotage_bonus" => $object->getEscamotage_bonus(),
            "athletisme_bonus" => $object->getAthletisme_bonus(),
            "intimidation_bonus" => $object->getIntimidation_bonus(),
            "arcane_bonus" => $object->getArcane_bonus(),
            "histoire_bonus" => $object->getHistoire_bonus(),
            "investigation_bonus" => $object->getInvestigation_bonus(),
            "nature_bonus" => $object->getNature_bonus(),
            "religion_bonus" => $object->getReligion_bonus(),
            "dressage_bonus" => $object->getDressage_bonus(),
            "medecine_bonus" => $object->getMedecine_bonus(),
            "perception_bonus" => $object->getPerception_bonus(),
            "perspicacite_bonus" => $object->getPerspicacite_bonus(),
            "survie_bonus" => $object->getSurvie_bonus(),
            "persuasion_bonus" => $object->getPersuasion_bonus(),
            "representation_bonus" => $object->getRepresentation_bonus(),
            "supercherie_bonus" => $object->getSupercherie_bonus(),
            "acrobatie_mastery" => $object->getAcrobatie_mastery(),
            "discretion_mastery" => $object->getDiscretion_mastery(),
            "escamotage_mastery" => $object->getEscamotage_mastery(),
            "athletisme_mastery" => $object->getAthletisme_mastery(),
            "intimidation_mastery" => $object->getIntimidation_mastery(),
            "arcane_mastery" => $object->getArcane_mastery(),
            "histoire_mastery" => $object->getHistoire_mastery(),
            "investigation_mastery" => $object->getInvestigation_mastery(),
            "nature_mastery" => $object->getNature_mastery(),
            "religion_mastery" => $object->getReligion_mastery(),
            "dressage_mastery" => $object->getDressage_mastery(),
            "medecine_mastery" => $object->getMedecine_mastery(),
            "perception_mastery" => $object->getPerception_mastery(),
            "perspicacite_mastery" => $object->getPerspicacite_mastery(),
            "survie_mastery" => $object->getSurvie_mastery(),
            "persuasion_mastery" => $object->getPersuasion_mastery(),
            "representation_mastery" => $object->getRepresentation_mastery(),
            "supercherie_mastery" => $object->getSupercherie_mastery(),
            "kamas" => $object->getKamas(),
            "drop_" => $object->getDrop_(),
            "other_item" => $object->getOther_item(),
            "other_consumable" => $object->getOther_consumable(),
            "other_spell" => $object->getOther_spell()
        ));

    }
    public function delete(Npc $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);

        $this->removeAllLinkConsumableFromNpc($object);    
        $this->removeAllLinkItemFromNpc($object);    
        $this->removeAllLinkSpellFromNpc($object);    
        $this->removeAllLinkCapabilityFromNpc($object);    

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
        $req = $this->_bdd->prepare('SELECT * FROM link_npc_consumable INNER JOIN npc ON link_npc_consumable.id_consumable = consumable.id WHERE link_npc_consumable.id_npc = ? AND link_npc_consumable.id_consumable = ?');
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

        // Renvoi le dernier ingredient ajouté
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

        // Renvoi le dernier ingredient ajouté
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

        // Renvoi le dernier ingredient ajouté
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

 // Link Capability
    public function getLinkCapability(Npc $npc){
        $req = $this->_bdd->prepare('   SELECT *, link_npc_capability.id AS link_id 
                                        FROM link_npc_capability
                                        INNER JOIN capability ON link_npc_capability.id_capability = capability.id
                                        WHERE id_npc = ?
                                        ORDER BY capability.level ASC');
        $req->execute(array($npc->getId()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(empty($ret)){return "";}
        $return = array();
        foreach ($ret as $link) {
            $return[] = new Capability($this->securite($link));
        }
        return $return;
    }
    public function existsLinkCapability(Npc $npc, Capability $capability){
        $req = $this->_bdd->prepare('SELECT id FROM link_npc_capability WHERE id_npc = ? AND id_capability = ?');
        $req->execute(array($npc->getId(), $capability->getId()));
        return $req->rowCount();
    }
    public function addLinkCapability(Npc $npc, Capability $capability){
        if($this->existsLinkCapability($npc, $capability)){return false;}
        $req = $this->_bdd->prepare('INSERT INTO link_npc_capability(
                    id_npc,
                    id_capability
                )
            VALUES (
                    :id_npc,
                    :id_capability
                )');

        return $req->execute(array(
            "id_npc" => $npc->getId(),
            "id_capability"=> $capability->getId()
        ));

        // Renvoi le dernier ingredient ajouté
        $post = $this->_bdd->prepare('SELECT id FROM link_npc_capability ORDER BY id DESC LIMIT 1');
        return $post->execute();
    }
    public function removeLinkCapability(Npc $npc, Capability $capability){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_capability WHERE id_npc = :id_npc AND id_capability = :id_capability');
        return $req->execute(array("id_npc" =>  $npc->getId(), "id_capability" =>  $capability->getId()));
    }
    public function removeAllLinkCapabilityFromNpc(Npc $npc){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_capability WHERE id_npc = :id');
        return $req->execute(array("id" =>  $npc->getId()));
    }
    public function removeAllLinkCapabilityFromCapability(Capability $capability){
        $req = $this->_bdd->prepare('DELETE FROM link_npc_capability WHERE id_capability = :id');
        return $req->execute(array("id" =>  $capability->getId()));
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
