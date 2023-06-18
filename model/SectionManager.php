<?php
class SectionManager extends Manager
{
    public const PATH_SECTION = "view/sections/";

// GET
    public function getAll(){
        $req = $this->_bdd->prepare("SELECT * FROM section WHERE visible = ? ORDER BY id");
        $req->execute();
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($ret)){
            return $this->bdd2objects($ret);
        } else {
            return array();
        }
    }
    public function getAllFromPage(Page $page){
        $req = $this->_bdd->prepare("SELECT * FROM section WHERE uniqid_page = :uniqid_page ORDER BY order_num");
        $req->execute(array("uniqid_page" => $page->getUniqid()));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($ret)){
            return $this->bdd2objects($ret);
        } else {
            return array();
        }
    }
    public function getFromId($id){
        $post = $this->_bdd->prepare('SELECT * FROM section WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Section($this->securite($req));
    }
    public function getFromUniqid($id){
        $post = $this->_bdd->prepare('SELECT * FROM section WHERE uniqid = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(empty($req)){return "";}
        return new Section($this->securite($req));
    }

    public function getLastOrder_numFromUniqid_page($uniqid){
        $post = $this->_bdd->prepare('SELECT `order_num` FROM `section` WHERE `uniqid_page` = ? ORDER BY `order_num` DESC LIMIT 1');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        if(isset($req["order_num"])){
            return $req["order_num"];
        } else {
            return 0;
        }
    }

// EXISTS
    public function existsId($id){
            $req = $this->_bdd->prepare('SELECT id FROM section WHERE id = ?');
            $req->execute(array($this->securite($id)));
            return $req->rowCount();
    }
    public function existsUniqid($uniqid){
        $req = $this->_bdd->prepare('SELECT id FROM section WHERE uniqid = ?');
        $req->execute(array($this->securite($uniqid)));
        return $req->rowCount();
    }
    public function existsPage(Page $page){
            $req = $this->_bdd->prepare('SELECT id FROM section WHERE id_page = ?');
            $req->execute(array($page->getId()));
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
        FROM section      
        WHERE ( 
                content like :term OR 
                title like :term) '. $limit.'');
        
        $req->execute(array("term" => $term));
        $result =  $req->fetchAll();
        
        if(!empty($result)){
            return $this->bdd2objects($result);
        } else {
            return array();
        }
    }

// WRITE
    public function add(Section $section){
        $req = $this->_bdd->prepare('INSERT INTO section(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    type,
                    uniqid_page,
                    title,
                    content,
                    order_num
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :type,
                    :uniqid_page,
                    :title,
                    :content,
                    :order_num
                )');

        return $req->execute(array(
                'uniqid' => $section->getUniqid(),
                'timestamp_add' => $section->getTimestamp_add(),
                'timestamp_updated' => $section->getTimestamp_updated(),
                'type' => $section->getType(),
                'uniqid_page' => $section->getUniqid_page(),
                'title' => $section->getTitle(),
                'content' => $section->getContent(),
                'order_num' => $section->getOrder_num()
        ));
        
    }
    public function update(Section $section){
        $req = $this->_bdd->prepare('UPDATE section SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    type=:type,
                    uniqid_page=:uniqid_page,
                    title=:title,
                    content=:content,
                    order_num=:order_num
            WHERE id=:id');
        
        return $req->execute(array(
            'id' => $section->getId(),
            'uniqid' => $section->getUniqid(),
            'timestamp_add' => $section->getTimestamp_add(),
            'timestamp_updated' => $section->getTimestamp_updated(),
            'type' => $section->getType(),
            'uniqid_page' => $section->getUniqid_page(),
            'title' => $section->getTitle(),
            'content' => $section->getContent(),
            'order_num' => $section->getOrder_num()
        ));
    }
    public function delete(Section $section){
        if(file_exists("medias/page/".$section->getUniqid()."/")){
            FileManager::remove("medias/page/".$section->getUniqid()."/");
        }
        if(file_exists($section->getContent())){
            unlink($section->getContent());
        }
        $req = $this->_bdd->prepare('DELETE FROM section WHERE uniqid = :uniqid');
        return $req->execute(array("uniqid" => $section->getUniqid()));
    }
    public function deleteFromPage(Page $page){
        $req = $this->_bdd->prepare('DELETE FROM section WHERE uniqid_page = :uniqid');
        $req->execute(array("uniqid" => $page->getId()));
    }

// TEMPLATE SECTIONS FILES
    public function getAllTemplateFile(){    
        $templates = array(
            "Bases" => array()
        );
        foreach (scandir(SectionManager::PATH_SECTION) as $file) {
    
            if($file != ".." && $file != "."){
                if(is_dir(SectionManager::PATH_SECTION.$file)){
                    $templates[$file] = $this->getTemplateName(SectionManager::PATH_SECTION.$file);
                }else{
                    $templates['Bases'][] = $file;
                }
            } else {
            }
        }
        return $templates;
    }
    private function getTemplateName($path){
        $path = FileManager::formatPath($path);
        $templates = array();
        foreach (scandir($path) as $file) {
            if(substr(strrchr($file,'.'),1) == "php" && $file != ".." && $file != "."){
                if(file_exists($path.$file)){
                    $templates[] = $file;
                }
            }
        }
        return $templates;
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Section($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
