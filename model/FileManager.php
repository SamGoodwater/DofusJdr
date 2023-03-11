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

    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }
    public function hydrate(array $donnees){
        foreach($donnees as $champ => $valeur){
          $method = "set".ucfirst($champ);

          if(method_exists($this,$method))
          {
              $this->$method($valeur);
          }
        }
    }
    public function setFile(array $file){
        $this->_file = $file;
    }
    public function setDirname(string $dirname){
        $this->_dirname = $dirname;
    }
    public function setFormatallowed(array $formatallowed){
        if(is_array($formatallowed)){
            $extensions = self::getListeExtention(self::FORMAT_ALL);
            foreach ($formatallowed as $format) {
                if(!in_array($format, $extensions)){
                    return "Une des extentions n'est pas valides.";
                }
            }
            $this->_formatallowed = $formatallowed;
            return "true";
        }
        return "Les extentions ne sont pas valides.";
    }
    public function setName(string $name){
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
                    if(!empty($name)){
                        $path =  $dirname . $name . "." . $extension_upload;
                    } else {
                        $path =  $dirname . FileManager::formatPath($this->getFile()['name'], false, false);
                    }

                    if(FileManager::isImage($fileObj) && class_exists('Imagick')){
                        $success = FileManager::WriteImageAfterCompressAnd2PNG($this->getFile()['tmp_name'], $path);
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
            'ico'
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
        
        static function WriteImageAfterCompressAnd2PNG($path_read, $path_write){ // Ecrit après avoir convertit une image en png et la redimention (max 800 * 800) puis la compresse si besoins (- de 2Mo)
            if(is_file($path_read))
            {
                $nom = explode('.', $path_write)[1];
                if(strtolower($nom) != 'png'){
                    $path_write = $nom . '.png';
                }
    
                $im = new Imagick($path_read);
                $success = $im->thumbnailImage(800,800, true);
                if($success == false)
                {
                    return false;
                }
                if($im->getImageLength() > 5000000)
                {
                    $success = $im->setCompressionQuality(20);
                    if($success == false)
                    {
                        return false;
                    }
                }
                    $success = $im->setImageFormat("png");
                    if($success == false)
                    {
                        return false;
                    }
                $success = $im->writeImage($path_write);
                if($success)
                {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        static function createThumbnail(File $file, $dir_dest){
            FileManager::formatPath($dir_dest, false, true);
            if(FileManager::isImage($file) && class_exists("Imagick")){
                $im = new Imagick($file->getPath());
                if(!$im->thumbnailImage(400,400, true)){
                    return false;
                }
                    $n = 0;
                    while ($im->getImageLength() <= 100000) {
                        if($n > 5){break;}
                        if(!$im->setCompressionQuality(20)){
                            return false;
                        }
                        $n++;
                    }
                return $im->writeImage($dir_dest . $file->getName(Content::FORMAT_BRUT, false). "_thumb." . $file->getExtention());
            } else {
                return false;
            }
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
                        return "Impossible de renommer le fichier.";
                    }
                } else {
                    return "Un fichier portant ce nom existe déjà.";
                }
            } else {
                return "Le fichier cible n'existe pas.";
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
}