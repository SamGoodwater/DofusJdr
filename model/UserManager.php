<?php
class UserManager extends Manager
{

// GET
    public function getAll(){
        $requete = "SELECT * FROM user INNER JOIN user_right ON user.id = user_right.id_user";
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
        $post = $this->_bdd->prepare('SELECT * FROM user INNER JOIN user_right ON user.id = user_right.id_user WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new User($req);
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM user INNER JOIN user_right ON user.id = user_right.id_user WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new User($req);
    }
    public function getFromEmail($email){
        $post = $this->_bdd->prepare('SELECT * FROM user INNER JOIN user_right ON user.id = user_right.id_user WHERE email = ?');
        $post->execute(array($email));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new User($req);
    }

    public function countAll(){
        $requete = "SELECT id FROM user";    
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
            $req = $this->_bdd->prepare('SELECT id FROM user WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM user WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            return $req->rowCount();
    }
    public function existsEmail($email){
        $req = $this->_bdd->prepare('SELECT id FROM user WHERE email = ?');
        $req->execute(array($this->securite($email)));
        return $req->rowCount();
    }
    public function existsPseudo($pseudo){
        $req = $this->_bdd->prepare('SELECT id FROM user WHERE pseudo = ?');
        $req->execute(array($this->securite($pseudo)));
        return $req->rowCount();
    }

    public function getMatch($emailOrPseudo, $password){
        $req = $this->_bdd->prepare('SELECT * FROM user INNER JOIN user_right ON user.id = user_right.id_user WHERE pseudo = :emailOrPseudo OR email = :emailOrPseudo');
        $req->execute(array("emailOrPseudo" => $emailOrPseudo));
        $ret = $req->fetch(PDO::FETCH_ASSOC);
        if(empty($ret)){
            return "L'adresse mail n'existe pas";
        }
        $user = new User($ret);
        if(password_verify($password, $user->gethash())){
            return $user;
        } else {
            return "Le mot de passe est incorrect";
        }
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
        FROM user        
        WHERE ( pseudo like :term) '.$usable . $limit.'');
        
        $req->execute(array("term" => $term));
        $result =  $req->fetchAll();
        
        if(!empty($result)){
            return $this->bdd2objects($result);
        } else {
            return array();
        }
    }

// WRITE
    public function add(User $object){

        $req = $this->_bdd->prepare('INSERT INTO user(
                    uniqid,
                    timestamp_add,
                    last_connexion,
                    token,
                    email,
                    pseudo,
                    hash
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :last_connexion,
                    :token,
                    :email,
                    :pseudo,
                    :hash
                )');
   
        $req->execute(array(
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "last_connexion" => $object->getLast_connexion(),
            "token" => $object->getToken(),
            "email" => $object->getEmail(),
            "pseudo" => $object->getPseudo(),
            "hash" => $object->getHash()
        ));

        // $req = $this->_bdd->prepare("SELECT id FROM user ORDER BY id DESC LIMIT 1");
        $req = $this->_bdd->prepare("SELECT Max(id) FROM user");
        $req->execute();
        $id = $req->fetch(PDO::FETCH_NUM)[0];
        $req = $this->_bdd->prepare('INSERT INTO user_right(
                    id_user
                )
            VALUES (
                    :id_user
                )');
                        
        return $req->execute(array(
            "id_user" => $id
        ));
    }
    public function update(User $object){

        $req = $this->_bdd->prepare('UPDATE user SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    last_connexion=:last_connexion,
                    token=:token,
                    email=:email,
                    pseudo=:pseudo,
                    hash=:hash
            WHERE id=:id');

        $req->execute(array(
            "id" => $object->getId(),
            "uniqid" => $object->getUniqid(),
            "timestamp_add" => $object->getTimestamp_add(),
            "last_connexion" => $object->getLast_connexion(),
            "token" => $object->getToken(),
            "email" => $object->getEmail(),
            "pseudo" => $object->getPseudo(),
            "hash" => $object->getHash()
        ));

        $req = $this->_bdd->prepare('UPDATE user_right SET
            right_classe=:right_classe,
            right_consumable=:right_consumable,
            right_item=:right_item,
            right_mob=:right_mob,
            right_npc=:right_npc,
            right_page=:right_page,
            right_section=:right_section,
            right_shop=:right_shop,
            right_spell=:right_spell,
            right_user=:right_user
        WHERE id_user=:id_user');

        return $req->execute(array(
            "id_user" => $object->getId(),
            "right_classe" => $object->getRight_classe(),
            "right_consumable" => $object->getRight_consumable(),
            "right_item" => $object->getRight_item(),
            "right_mob" => $object->getRight_mob(),
            "right_npc" => $object->getRight_npc(),
            "right_page" => $object->getRight_page(),
            "right_section" => $object->getRight_section(),
            "right_shop" => $object->getRight_shop(),
            "right_spell" => $object->getRight_spell(),
            "right_user" => $object->getRight_user()
        ));	

    }
    public function remove(User $object){
        $req = $this->_bdd->prepare('DELETE FROM user WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $object->getUniqid()));
    }

// ------------ BOOKMARK -------------
    // Getter
        public function getBookmarkFromUser(User $user){
            $post = $this->_bdd->prepare('SELECT * FROM bookmark WHERE uniqid_user = ?');
            $post->execute(array($user->getUniqid()));
            $req = $post->fetchAll(PDO::FETCH_ASSOC);
            if(empty($req)){return "";}
            return $this->convertBookmarktoObj($req);
        }
        public function getBookmarkFromUniqidObj($uniqid){
            $post = $this->_bdd->prepare('SELECT * FROM bookmark WHERE uniqid_obj = ?');
            $post->execute(array($uniqid));
            $req = $post->fetchAll(PDO::FETCH_ASSOC);
            if(empty($req)){return "";}
            return $this->convertBookmarktoObj($req);
        }
    // Exist
        public function existsBookmark(User $user, object $obj){
            $req = $this->_bdd->prepare('SELECT id FROM bookmark WHERE
                uniqid_user = :uniqid_user AND
                uniqid_obj = :uniqid_obj AND
                classe = :classe
            ');
            $req->execute(array(
                "uniqid_user" => $user->getUniqid(),
                "uniqid_obj" => $obj->getUniqid(),
                "classe" => get_class($obj)
            ));
            return $req->rowCount();
        }
    // Modif
        public function addBookmark(User $user, object $obj){
            $req = $this->_bdd->prepare('INSERT INTO bookmark(
                        uniqid_user,
                        uniqid_obj,
                        classe
                       )
                VALUES (
                        :uniqid_user,
                        :uniqid_obj,
                        :classe
                    )');
       
            return $req->execute(array(
                "uniqid_user" => $user->getUniqid(),
                "uniqid_obj" => $obj->getUniqid(),
                "classe" => get_class($obj)
            ));
        }
        public function removeBookmark(User $user, object $obj){
            $req = $this->_bdd->prepare('DELETE FROM bookmark WHERE 
                uniqid_user = :uniqid_user AND
                uniqid_obj = :uniqid_obj AND
                classe = :classe
            ');
            return $req->execute(array(
                "uniqid_user" => $user->getUniqid(),
                "uniqid_obj" => $obj->getUniqid(),
                "classe" => get_class($obj)
            ));
        }
        public function removeBookmarkFromUser(User $user){
            $req = $this->_bdd->prepare('DELETE FROM bookmark WHERE uniqid_user = :uniqid_user');
            return $req->execute(array(
                "uniqid_user" => $user->getUniqid()
            ));
        }
        public function removeBookmarkFromObj(object $obj){
            $req = $this->_bdd->prepare('DELETE FROM bookmark WHERE 
                uniqid_obj = :uniqid_obj AND
                classe = :classe
            ');
            return $req->execute(array(
                "uniqid_obj" => $obj->getUniqid(),
                "classe" => get_class($obj)
            ));
        }
    // Other
        private function convertBookmarktoObj($req){    
            $return = [];    
            foreach ($req as $data) {
                $managerName = ucfirst(strtolower($data["classe"])) . "Manager";
                if(class_exists($managerName)){
                    $manager = new $managerName();
                    if(method_exists($manager, 'existsUniqid')){
                        if($manager->existsUniqid($data['uniqid_obj'])){
                            $return[] = $manager->getFromUniqid($data['uniqid_obj']);
                        }
                    }
                }
            }
            return $return;
        }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new User($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
