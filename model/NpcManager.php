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
                    other_equipment,
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
                    :other_equipment,
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
            "other_equipment" => $object->getOther_equipment(),
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
                    other_equipment=:other_equipment,
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
            "other_equipment" => $object->getOther_equipment(),
            "other_consomable" => $object->getOther_consomable(),
            "other_spell" => $object->getOther_spell()
        ));

    }
    public function delete(Npc $object){
        $managerUser = new UserManager();
        $managerUser->removeBookmarkFromObj($object);
        
        $req = $this->_bdd->prepare('DELETE FROM npc WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
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
