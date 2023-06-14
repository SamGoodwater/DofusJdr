<?php
class PageManager extends Manager{

// GET
    public function getAll(){
        $req = $this->_bdd->prepare("SELECT * FROM page ORDER BY order_num");
        $req->execute();
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($ret)){
            return $this->bdd2objects($ret);
        } else {
            return array();
        }
    }
    public function getAllParent(){
        $req = $this->_bdd->prepare("SELECT * FROM page WHERE category BETWEEN ? AND ? ORDER BY order_num");
        $req->execute(array(Page::CATEGORY_STARING, Page::CATEGORY_OTHER));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($ret)){
            return $this->bdd2objects($ret);
        } else {
            return array();
        }
    }
    public function getAllFromCategory($category){
        $req = $this->_bdd->prepare("SELECT * FROM page WHERE category = ? ORDER BY order_num");
        $req->execute(array($category));
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($ret)){
            return $this->bdd2objects($ret);
        } else {
            return array();
        }
    }
    public function getAllNonCategory(){
        $req = $this->_bdd->prepare("SELECT * FROM page WHERE category < 0 ORDER BY order_num");
        $req->execute();
        $ret = $req->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($ret)){
            return $this->bdd2objects($ret);
        } else {
            return array();
        }
    }
    public function getFromId($id){
        $post = $this->_bdd->prepare('SELECT * FROM page WHERE id = ?');
        $post->execute(array($id));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        
        return new Page($this->securite($req));
    }
    public function getFromUniqid($uniqid){
        $post = $this->_bdd->prepare('SELECT * FROM page WHERE uniqid = ?');
        $post->execute(array($uniqid));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        
        return new Page($this->securite($req));
    }
    public function getFromUrl_name($url_name){
        $post = $this->_bdd->prepare('SELECT * FROM page WHERE url_name = ?');
        $post->execute(array($url_name));
        $req = $post->fetch(PDO::FETCH_ASSOC);
        
        return new Page($this->securite($req));
    }

    public function getLastOrder_numFromCategory($category){
        $post = $this->_bdd->prepare('SELECT `order_num` FROM `page` WHERE `category` = ? ORDER BY `order_num` DESC LIMIT 1');
        $post->execute(array($category));
        $req = $post->fetch(PDO::FETCH_ASSOC); 
        return $req["order_num"];
    }

// EXISTS
    public function existsId($id){
            $req = $this->_bdd->prepare('SELECT id FROM page WHERE id = ?');
            $req->execute(array($this->securite($id)));
            $result=$req->rowCount();

            if($result){
                return true;
            }else {
                return false;
            }
    }
    public function existsUniqid($uniqid){
            $req = $this->_bdd->prepare('SELECT id FROM page WHERE uniqid = ?');
            $req->execute(array($this->securite($uniqid)));
            $result=$req->rowCount();

            if($result){
                return true;
            }else {
                return false;
            }
    }
    public function existsUrl_name($url_name){
            $req = $this->_bdd->prepare('SELECT id FROM page WHERE url_name = ?');
            $req->execute(array($this->securite($url_name)));
            $result=$req->rowCount();

            if($result){
                return true;
            }else {
                return false;
            }
    }

    // SEARCH
    public function search($term){
        $term = "%" . $term . "%";
        $req = $this->_bdd->prepare('SELECT *
        FROM page 
        WHERE ( name like :term)        
        LIMIT 20');
       
        $req->execute(array("term" => $term));
        $result = $req->fetchAll();
        
        if(!empty($result)){
            return $this->bdd2objects($result);
        } else {
            return array();
        }
    }

// WRITE
    public function add(Page $page){
        $req = $this->_bdd->prepare('INSERT INTO page(
                    uniqid,
                    timestamp_add,
                    timestamp_updated,
                    name,
                    url_name,
                    order_num,
                    category,
                    is_dropdown,
                    public,
                    is_editable
                   )
            VALUES (
                    :uniqid,
                    :timestamp_add,
                    :timestamp_updated,
                    :name,
                    :url_name,
                    :order_num,
                    :category,
                    :is_dropdown,
                    :public,
                    :is_editable
                )');

        return $req->execute(array(
            'uniqid' => $page->getUniqid(),
            'timestamp_add' => $page->getTimestamp_add(),
            'timestamp_updated' => $page->getTimestamp_updated(),
            'name' => $page->getName(),
            'url_name' => $page->getUrl_name(),
            'order_num' => $page->getOrder_num(),
            'category' => $page->getCategory(),
            "is_dropdown" => $page->getIs_dropdown(),
            "public" => $page->getPublic(),
            "is_editable" => $page->getIs_editable()
        ));

    }

    public function update(Page $page){
        $req = $this->_bdd->prepare('UPDATE page SET
                    uniqid=:uniqid,
                    timestamp_add=:timestamp_add,
                    timestamp_updated=:timestamp_updated,
                    name=:name,
                    url_name=:url_name,
                    order_num=:order_num,
                    category=:category,
                    is_dropdown=:is_dropdown,
                    public=:public,
                    is_editable=:is_editable
            WHERE id=:id');

        return $req->execute(array(
                'id' => $page->getId(),
                'uniqid' => $page->getUniqid(),
                'timestamp_add' => $page->getTimestamp_add(),
                'timestamp_updated' => $page->getTimestamp_updated(),
                'name' => $page->getName(),
                'url_name' => $page->getUrl_name(),
                'order_num' => $page->getOrder_num(),
                'category' => $page->getCategory(),
                "is_dropdown" => $page->getIs_dropdown(),
                "public" => $page->getPublic(),
                "is_editable" => $page->getIs_editable()
            ));
    }

    public function delete(Page $page){
        $ms = New SectionManager();
        $ms->deleteFromPage($page);

        $req = $this->_bdd->prepare('DELETE FROM page WHERE id = :id');
        return $req->execute(array("id" => $page->getId()));
    }

// OTHER
    private function bdd2objects(array $bdd_results){
        if(empty($bdd_results)){return false;}
        foreach($bdd_results as $entry => $value){
            $objet = new Page($this->securite($value));
            $tableau_objet[] = $objet;
        }
        return $tableau_objet;
    }

}
