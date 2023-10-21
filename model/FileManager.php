<?php

class FileManager extends Manager{

    // Ajout d'un fichier
    //  $fileManager = new FileManager(array(
    //     "dirname" => $dirname,
    //     "file" => $_FILES["file"],
    //     "formatallowed" => "all",
    //     "name" => $_FILES["file"]["name"],
    //   ));

    private $_file = array();
    private $_dirname = "";
    private $_formatallowed = array();
    private $_name = "";

    private $_action_exist_name = FileManager::UPLOAD_ACTION_EXIST_NAME_DEFAULT;

    const UPLOAD_ACTION_EXIST_NAME_REPLACE = 0;
    const UPLOAD_ACTION_EXIST_NAME_RENAME = 1;
    const UPLOAD_ACTION_EXIST_NAME_ERROR = 2;
    const UPLOAD_ACTION_EXIST_NAME_DEFAULT = FileManager::UPLOAD_ACTION_EXIST_NAME_REPLACE;

    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }
    public function hydrate(array $donnees){
        foreach($donnees as $champ => $valeur){
          $method = "set".ucfirst($champ);

          if(method_exists($this,$method)){
              $this->$method($valeur);
          }
        }
    }
    public function setFile(array $file){
        $this->_file = $file;
        return true;
    }
    public function setDirname(string $dirname){
        $this->_dirname = $dirname;
        return true;
    }
    public function setFormatallowed(array $formatallowed){
        if(is_array($formatallowed)){
            $extensions = self::getListeExtention(self::FORMAT_ALL);
            foreach ($formatallowed as $format) {
                if(!in_array($format, $extensions)){
                    throw new Exception("Une des extentions n'est pas valides.");
                }
            }
            $this->_formatallowed = $formatallowed;
            return true;
        }
        throw new Exception("Les extentions ne sont pas valides.");
    }
    public function setName(string $name){
        $name = str_replace("." . pathinfo($name, PATHINFO_EXTENSION), "", $name);
        $this->_name = $name;
    }
    public function getFile(){
        return $this->_file;
    }
    public function getDirname(){
        return $this->_dirname;
    }
    public function getFormatallowed(){
        return $this->_formatallowed;
    }
    public function getName(){
        return $this->_name;
    }
    public function getActionExistName(){
        return $this->_action_exist_name;
    }
    public function setActionExistName(int $action){
        if($action == FileManager::UPLOAD_ACTION_EXIST_NAME_REPLACE || $action == FileManager::UPLOAD_ACTION_EXIST_NAME_RENAME || $action == FileManager::UPLOAD_ACTION_EXIST_NAME_ERROR){
            $this->_action_exist_name = $action;
            return true;
        } else {
            throw new Exception("L'action n'est pas valide.");
        }
    }
    public function upload(){ 
        $return = array(
            "state" => false,
            'error' => "erreur inconnue",
            'path' => null
        );

        if ($this->getFile()['error'] > 0){
            $return['error'] = "Une erreur s'est produite lors du téléchargement du fichier.";
        } else {
            
            $extension_upload = strtolower(substr(strrchr($this->getFile()['name'],'.'), 1));
            if(in_array($extension_upload, $this->getFormatallowed())) // Vérifie que le format fait parties des formats acceptés par le site
            {
                // On formate le chemin et on créé des sous dossiers si ils existent pas 
                $dirname = FileManager::formatPath($this->getDirname(), false, true);
                FileManager::setDir($dirname);

                // Copie du fichier télécharger
                    $fileObj = New File($this->getFile()['name']);
                    if(!empty($this->getName())){
                        $path =  $dirname . $this->getName() . "." . $extension_upload;
                        $path_without_extention =  $dirname . $this->getName();
                    } else {
                        $path =  $dirname . FileManager::formatPath($this->getFile()['name'], false, false);
                        $path_without_extention =  $dirname . FileManager::formatPath($fileObj->getName(with_extention: false), false, false);
                    }

                    foreach ($this->getListeExtention(self::FORMAT_ALL) as $ext) {
                        $test_path = $path_without_extention . "." . $ext;
                        if(file_exists($test_path)){
                            switch ($this->_action_exist_name) {
                                case FileManager::UPLOAD_ACTION_EXIST_NAME_REPLACE:
                                    FileManager::remove($test_path);
                                break;
                                case FileManager::UPLOAD_ACTION_EXIST_NAME_RENAME:
                                    if($test_path == $path){
                                        $i = 1;
                                        $path = $path_without_extention . "_" . $i . "." . $extension_upload;
                                        while (file_exists($test_path)) {
                                            $path = $path_without_extention . "_" . $i . "." . $extension_upload;
                                            $i++;
                                        }
                                    }
                                break;

                                case FileManager::UPLOAD_ACTION_EXIST_NAME_ERROR:
                                    $return['error'] = "Un fichier portant ce nom existe déjà.";
                                    return $return;
                                break;
                            }
                        }
                    }



                    if(FileManager::isImage($fileObj) && class_exists('Imagick')){
                        $success = FileManager::add($this->getFile()['tmp_name'], $path);
                    } else {
                        $success = move_uploaded_file($this->getFile()['tmp_name'], $path);
                    }
    
                    if($success){
                        if(file_exists($path)){
                            $return['state'] = true;
                            $return['path'] = $path;
                        }else {
                            $return['error'] =  "Le fichier est introuvable : " . $path;
                        }
                    } else {
                        $return['error'] =  "Une erreur s'est produite lors du téléchargement du fichier.";
                    }

            } else {
                $return['error'] = "L'extention du fichier n'est pas valide.";
            }
        }
        return $return;
    }

    //Format File
        const FORMAT_ALL = "all";
        const FORMAT_IMG = "img";
        const FILE_EXTENTION_IMG = array(
            "png",
            "jpeg",
            "jpg",
            "gif",
            "jpg",
            'svg', 
            'bmp',
            'tif',
            'tiff',
            'raw',
            'psd',
            'ia',
            'jp2',
            'j2k',
            'jpf',
            'jpx',
            'jpm',
            'mj2',
            'ico',
            'jfif'
        );
        const FORMAT_VIDEO = "video";
        const FILE_EXTENTION_VIDEO = array(
            "3gp",
            "3g2",
            "avi",
            "mpg",
            'mp4', 
            'mp4a',
            'mp4b',
            'mp4r',
            'mp4v',
            'mp4p',
            'asf',
            'wmv',
            'wma',
            'flv',
            'nut',
            'rm',
            'mov',
            'mkv',
            'ogg',
            'ogv',
            'oga',
            'ogx',
            'spx',
            'opus',
            'ogm',
            'vob',
            'ifo',
            'webm',
            'weba'
        );
        const FORMAT_AUDIO = "audio";
        const FILE_EXTENTION_AUDIO = array(
            "flac",
            "ape",
            "alac",
            "mp3",
            'wma', 
            'ogg',
            'm4a',
            'mpc',
            'ac3',
            'dts',
            'wav',
            'aiff'
        );
        const FORMAT_PDF = "pdf";
        const FILE_EXTENTION_PDF = array(
            "pdf"
        );
        const FORMAT_TABLEUR = "tableur";
        const FILE_EXTENTION_TABLEUR = array(
            "xlsx",
            "xls",
            "xlsm",
            "xlsb",
            'csv', 
            'mht',
            'xltx',
            'xltm',
            'xlt',
            'dif',
            'slk',
            'xlam',
            'xla',
            'xps'
        );
        const FORMAT_DOCUMENT = "document";
        const FILE_EXTENTION_DOCUMENT = array(
            "docx",
            "doc",
            "docm",
            "dotx",
            'dotm', 
            'dot',
            'mht',
            'rtf',
            'odt',
            'txt',
            'html',
            'xml'
        );
        const FORMAT_SLIDER = "slider";
        const FILE_EXTENTION_SLIDER = array(
            "pptx",
            "ppt",
            "pptm",
            "potx",
            'potm', 
            'pot',
            'thmx',
            'ppsx',
            'ppsm',
            'pps'
        );
        const FORMAT_ARCHIVE = "archive";
        const FILE_EXTENTION_ARCHIVE = array(
            "rar",
            "zip",
            "7z",
            "arj",
            'bz2', 
            'cab',
            'gz',
            'iso',
            'jar',
            'lz',
            'lzh',
            'tar',
            'uue',
            'xz',
            'z',
            'zipx',
            '001'
        );
        const FORMAT_OTHER = "autre";
        static function getListeExtention(string ... $types){
            $return = array();
            foreach ($types as $value) {
                switch ($value) {
                    case FileManager::FORMAT_ALL:
                        $return = array_merge($return, FileManager::FILE_EXTENTION_IMG);
                        $return = array_merge($return, FileManager::FILE_EXTENTION_VIDEO);
                        $return = array_merge($return, FileManager::FILE_EXTENTION_AUDIO);
                        $return = array_merge($return, FileManager::FILE_EXTENTION_PDF);
                        $return = array_merge($return, FileManager::FILE_EXTENTION_TABLEUR);
                        $return = array_merge($return, FileManager::FILE_EXTENTION_DOCUMENT);
                        $return = array_merge($return, FileManager::FILE_EXTENTION_SLIDER);
                        $return = array_merge($return, FileManager::FILE_EXTENTION_ARCHIVE);
                        return $return;
                    break;
                    case FileManager::FORMAT_IMG:
                        $return = array_merge($return, FileManager::FILE_EXTENTION_IMG);
                    break;
                    case FileManager::FORMAT_VIDEO:
                        $return = array_merge($return, FileManager::FILE_EXTENTION_VIDEO);
                    break;
                    case FileManager::FORMAT_AUDIO:
                        $return = array_merge($return, FileManager::FILE_EXTENTION_AUDIO);
                    break;
                    case FileManager::FORMAT_PDF:
                        $return = array_merge($return,FileManager::FILE_EXTENTION_PDF);
                    break;
                    case FileManager::FORMAT_TABLEUR:
                        $return = array_merge($return, FileManager::FILE_EXTENTION_TABLEUR);
                    break;
                    case FileManager::FORMAT_DOCUMENT:
                        $return = array_merge($return, FileManager::FILE_EXTENTION_DOCUMENT);
                    break;
                    case FileManager::FORMAT_SLIDER:
                        $return = array_merge($return, FileManager::FILE_EXTENTION_SLIDER);
                    break;
                    case FileManager::FORMAT_ARCHIVE:
                        $return = array_merge($return, FileManager::FILE_EXTENTION_ARCHIVE);
                    break;
                }
            }
            return array_unique($return);
        }
        static function isImage(File $file){
            if(in_array(strtolower(substr(strrchr($file->getPath(),'.'), 1)), FileManager::FILE_EXTENTION_IMG)){
                return true;
            } else {
                return false;
            }
        }
        static function isPdf(File $file){
            if(in_array(strtolower(substr(strrchr($file->getPath(),'.'), 1)), FileManager::FILE_EXTENTION_PDF)){ //Regarde si l'extention du fichier est un pdf, 
                return true;
            } else {
                return false;
            }
        }
        static function isDocument(File $file){
            if(in_array(strtolower(substr(strrchr($file->getPath(),'.'), 1)), FileManager::FILE_EXTENTION_DOCUMENT)){ 
                return true;
            } else {
                return false;
            }
        }
        static function isTableur(File $file){
            if(in_array(strtolower(substr(strrchr($file->getPath(),'.'), 1)), FileManager::FILE_EXTENTION_TABLEUR)){ 
                return true;
            } else {
                return false;
            }
        }
        static function isSlider(File $file){
            if(in_array(strtolower(substr(strrchr($file->getPath(),'.'), 1)), FileManager::FILE_EXTENTION_SLIDER)){ 
                return true;
            } else {
                return false;
            }
        }
        static function isArchive(File $file){
            if(in_array(strtolower(substr(strrchr($file->getPath(),'.'), 1)), FileManager::FILE_EXTENTION_ARCHIVE)){ 
                return true;
            } else {
                return false;
            }
        }
        static function isAudio(File $file){
            if(in_array(strtolower(substr(strrchr($file->getPath(),'.'), 1)), FileManager::FILE_EXTENTION_AUDIO)){ 
                return true;
            } else {
                return false;
            }
        }
        static function isVideo(File $file){
            if(in_array(strtolower(substr(strrchr($file->getPath(),'.'), 1)), FileManager::FILE_EXTENTION_VIDEO)){ 
                return true;
            } else {
                return false;
            }
        }
        
        static function add(string $path_read, string $dir_dest, string $rename = "", bool $compress = true, bool $set_format_png = true, bool $resize = true, bool | string $thumbnail = "auto"){ // Ecrit après avoir convertit une image en png et la redimention (max 800 * 800) puis la compresse si besoins (- de 2Mo)
            $dir_dest = FileManager::formatPath($dir_dest, false, true);
            if(is_file($path_read)){
                $file_read = new File($path_read);
                $name = $file_read->getName(with_extention: false);
                if($rename != ""){$name = $rename;}
                $file_write = new File($dir_dest . "/" . $name . $file_read->getExtention());
    
                $success = FileManager::write(
                    path_read: $file_read->getPath(),
                    path_write: $file_write->getPath(),
                    compress: $compress,
                    set_format_png: $set_format_png,
                    resize: $resize
                );

                if(file_exists($file_write->getPath()) && $success){

                    if($thumbnail == "auto" || $thumbnail == true){
                        $new_file = new File($file_write->getPath());

                        if($new_file->getSize() > 1000000 || $thumbnail == true){
                            $file_thumbnail = new File($dir_dest . $name . "_thumb". $file_read->getExtention());
                            FileManager::write(
                                path_read: $new_file->getPath(),
                                path_write: $file_thumbnail->getPath(),
                                compress: $compress,
                                set_format_png: $set_format_png,
                                resize: $resize,
                                is_thumbnail: true
                            );
                        }
                    }

                    return true;
                } else {
                    return false;
                }

            } else {
                return false;
            }
        }
        static function write(string $path_read, string $path_write, bool $compress = true, bool $set_format_png = true, bool $resize = true, bool $is_thumbnail = false){
                $im = new Imagick($path_read);
                
                if($set_format_png){
                    if($im->setImageFormat("png") == false){
                        return false;
                    }
                }
                if($resize){
                    $ratio = $im->getImageWidth() / $im->getImageHeight();
                    if($ratio > 1){
                        $im->resizeImage(800,800/$ratio,Imagick::FILTER_LANCZOS,1, true);
                    } else {
                        $im->resizeImage(800*$ratio,800,Imagick::FILTER_LANCZOS,1, true);
                    }
                }  
                if($compress){
                    for ($i=10; $i < 90; $i += 10) { 
                        if($im->getImageLength() > 1000000){
                            if($im->setCompressionQuality(100 - $i) == false){
                                return false;
                            }
                        } else {
                            break;
                        }
                    }
                }
                if($is_thumbnail){
                    $im->thumbnailImage(150,150, true);
                }
                return $im->writeImage($path_write);
        }

    // Autres
        static function setDir(string $dirname){
            $dirname = FileManager::formatPath($dirname, false, true);
            $dirnameArray = explode('/',$dirname);
            if(!is_array($dirnameArray)){
                return false;
            }
            $dirnameArray = array_filter($dirnameArray);
            $path = "";

            foreach ($dirnameArray as $dir) {
                $path .= $dir . "/";
                if(!file_exists($path)){
                    if(!mkdir($path)){
                        return false;
                    }
                }
            }
            return true;

        }
        static function rename(string $path, string $newname){ // Nouveau nom sans l'extention, précisé l'extention dans un 2ème paramètre si elle change
            $path_parts = pathinfo($path);
            if(file_exists($path)){
                $newname = FileManager::formatPath($newname, false, false);

                if(!file_exists($path_parts['dirname'] . '/' . $newname)){
                    if(rename($path, $path_parts['dirname'] . '/' . $newname)){
                        $path = FileManager::formatPath($path, false, false);
                        $newpath = FileManager::formatPath($path_parts['dirname'] . '/' . $newname, false, false);
                        return true;
                    }else{
                        throw new Exception("Impossible de renommer le fichier.");
                    }
                } else {
                    throw new Exception("Un fichier portant ce nom existe déjà.");
                }
            } else {
                throw new Exception("Le fichier cible n'existe pas.");
            }
        }
        static function move(string $path, string $newdir, bool $remove = true){
            if(file_exists($path)){
                $newdir = FileManager::formatPath($newdir, false, true);
                if(!FileManager::setDir($newdir)){return false;} // On créer le dossier si il ne l'est pas.

                if(is_dir($path)){
                    $path = FileManager::formatPath($path, false, true);
                    $newpath = FileManager::formatPath($newdir, false, true);
                    foreach (scandir($path) as $file) {
                        if($file != ".." && $file != "."){
                            if(is_dir($path . $file)){
                                if(!FileManager::move($path . $file . '/', $newpath . $file . "/")){return false;}
                            } else {
                                if(!copy($path . $file, $newpath . $file)){return false;}
                                if($remove){FileManager::remove($path . $file);}
                            }
                        }
                    }
                    if($remove){FileManager::remove($path);}

                } elseif(is_file($path)){
                    $newpath = FileManager::formatPath($newdir . basename($path), false, false);
                    $path = FileManager::formatPath($path, false, false);
                    if(!copy($path, $newpath)){return false;}
                    if($remove){FileManager::remove($path);}
                }
                return true;
            } else {
                return false;
            }
        }
        static function remove(string $path, bool $removeRoot = true){ // supprimer un fichier et un dossier avec son contenu. RemoveRoot permet de supprimer ou non le dossier racine
            if(file_exists($path)){

                if(is_dir($path)){
                    
                    $dir = FileManager::formatPath($path, false, true);
                    foreach (scandir($dir) as $file) {
                        if($file != '.' && $file != '..'){
                            if(is_dir($dir . $file)){
                                if(!FileManager::remove($dir . $file . '/')){return false;}
                            } else {
                                if(!unlink($dir . $file)){return false;}
                            }
                        }
                    }
                    if($removeRoot){
                        if(!rmdir($dir)){return false;}
                    }
                    return true;

                } elseif(is_file($path)) {

                    if(!unlink($path)){
                        return false;
                    }
                    return true;
                    
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        static function isDirEmpty(string $dir){
            $find=0;
            if ($dh = opendir($dir)){
                while (($file = readdir($dh)) !== false && $find==0){
                    if ($file!="." && $file!=".." ){
                        $find=1;
                    }
                }
                closedir($dh);
            }
            if($find==0) {
                return true;
            } else {
                return false;
            }
        }
        static function isDir(File | string $file){
            $path = "";
            if(is_object($file)){
                if($file instanceof File){
                    $path = $file->getPath();
                } else {
                    return false;
                }
            } else {
                $path = $file;
            }
            if(is_dir(trim($path))){
                return true;
            } else {
                if(substr(trim($path), -1) == '/'){
          
                    return true;
                }
            }
            return false;
        }
        
        const ARRAY_REPLACE_SPECIAL_CARACTERE = [
            "<" => "_",
            ">" => "_",
            ":" => "_",
            '"' => "_",
            "|" => "_",
            "?" => "_",
            "*" => "_"
        ];
        static function formatPath(string $path, bool $start = false, bool $end = true, array $replace = []){ 
            
            if(!empty($replace)){
                if(is_array($replace)){
                    foreach ($replace as $search => $change) {
                        $path = str_replace($search,$change,$path);
                    }
                }
            }

            if($start){
                if(substr($path, 0, 1) != '/'){
                    $path = "/" . $path;
                }
            } else {
                if(substr($path, 0, 1) == '/'){
                    $path = substr($path, 1);
                }
            }
            if($end){
                if(substr($path, -1) != '/'){
                    $path .= '/';
                }   
            } else {
                if(substr($path, -1) == '/'){
                    $path = substr($path, 0, -1);
                }
            }

            if(is_file($path) && substr($path, -1) == '/'){ // Si c'est un fichier alors pas de / à la fin
                $path = substr($path, 0, -1);
            }
            return $path;
        }

        static function inMedias(File $file){
            $path = explode('/', $file->getPath());
            if(is_array($path)){
                if($path[0] == 'medias'){
                    return true;
                }
            }
            return false;
        }

        static function clearTemp(){
            if(FileManager::remove("medias/temp", false)){ // On supprimer tout le contenu du répertoire temp sans supprimer le répertoire temp
                return true;
            } else {
                return false;
            }
        }

        static function findExtention(string $path, string $format = FileManager::FORMAT_ALL){
            if(file_exists($path)){
                $path_parts = pathinfo($path);
                if(isset($path_parts['extension'])){
                    return $path_parts['extension'];
                }
            }
            $extensions = FileManager::getListeExtention($format);
            foreach ($extensions as $extension) {
                if(file_exists($path .'.'. $extension)){
                    return $extension;
                }
            }
            return "";
        }

        static function solveNameFromPaternAndObject(Object $obj, string $patern){
            preg_match_all("/\[(\w+)\]/", $patern, $fcs_name);
            foreach ($fcs_name[1] as $fc_name) {
                $method = "get".ucfirst($fc_name);
                if(method_exists($obj, $method)){
                    $patern = str_replace("[".$fc_name."]", $obj->$method(), $patern);
                }
            }
            return $patern;
        }

        // thumbnail
            static function addThumbnail(File $file){
                if(!$file->isThumbnail()){
                    if(!$file->existThumbnail()){
                        $thumb = $file->getThumbnail();
                        return FileManager::write(
                            path_read:$file->getPath(),
                            path_write:$thumb->getPath(),
                            compress:true,
                            set_format_png:false,
                            resize:true,
                            is_thumbnail:true
                        );
                    } else {
                        return false;
                    }
                }
                return false;
            }
            static function removeThumbnail(File $file){
                if(!$file->isThumbnail()){
                    if($file->existThumbnail()){
                        $file = $file->getThumbnail();
                    } else {
                        return false;
                    }
                }
                return FileManager::remove(path:$file->getPath(), removeRoot:false);
            }
}