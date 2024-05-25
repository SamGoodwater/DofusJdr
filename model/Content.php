<?php

use PhpParser\Node\Expr\Cast\Object_;

abstract class Content
{
    use CheckingFct, SecurityFct, CalcFct, StringFct, NumberFct;
    
    //Format GETTERS

    const DISPLAY_CARD = 100;
    const DISPLAY_RESUME = 101;
    const DISPLAY_EDITABLE = 102;
    const DISPLAY_FULL = 103;
    const DISPLAY_LIST = 104;
    const DISPLAY_MENU = 105;
    const DISPLAY_CARROUSEL = 106;
    const DISPLAY = [
        self::DISPLAY_EDITABLE => "Modifier",
        self::DISPLAY_CARD => "Carte",
        self::DISPLAY_RESUME => "Résumé",
        self::DISPLAY_LIST => "Liste",
        self::DISPLAY_FULL => "Complet",
        self::DISPLAY_MENU => "Menu",
        self::DISPLAY_CARROUSEL => "Carrousel"
    ];

    const FORMAT_BRUT = 0;
    const FORMAT_VIEW = 1;
    const FORMAT_EDITABLE = 102;
    const FORMAT_ICON = 3;
    const FORMAT_BADGE = 4;
    const FORMAT_OBJECT = 5;
    const FORMAT_ARRAY = 6;
    const FORMAT_LIST = 7;
    const FORMAT_TEXT = 8;
    const FORMAT_PATH = 9;
    const FORMAT_LINK = 10;

    //Date
    const DATE_DB = "Y-m-d";
    const DATE_TIME_DB = "Y-m-d H:i:s";
    const DATE_FR = "d-m-Y";
    const TIME_FR = "H:i:s";
    const DATE_TIME_FR = "H:i:s d-m-Y";

    const FORMAT_COLOR_HEX = 0;
    const FORMAT_COLOR_VERBALE = 1;

    // Autocomplete
    const AUTOCOMPLETE_SEPARATOR = "*|*";

    const FILES = [];
    // EXCEMPLE
    // const FILES = [
    //     "img" => [
    //         "type" => FileManager::FORMAT_IMG,
    //         "default" => "medias/modules/classes/default.png",
    //         "default_editable" => "medias/modules/classes/default_[type].png",
    //         "dir" => "medias/classes/",
    //         "preferential_format" => "png",
    //         'naming' => "[uniqid]",
    //         'is_dir' => false // ENSEMBLE DE FICHIER : TRUE ||| FICHIER UNIQUE : FALSE
    //     ],
    //     ...
    // ]

    const PATH_FILE_GENERAL_DEFAULT = "medias/no_file_found_logo.svg";

    protected $_id ='';
    protected $_uniqid ='';
    protected $_timestamp_add=0;
    protected $_timestamp_updated=0;
    protected $_usable = false;
    protected $_files = [];
    

    function __construct(array $donnees = []){
        $this->hydrate($donnees);
        if($this->_timestamp_add == 0){
            $this->setTimestamp_add();
        }
        if($this->_timestamp_updated == 0){
            $this->setTimestamp_updated();
        }
        
        $className = strtolower(get_class($this));
        if(!empty($className::FILES)){
            foreach($className::FILES as $name => $data){
                if(isset($data['naming']) && isset($data['dir']) && isset($data['type']) && isset($data['default']) && !empty($data['naming']) && !empty($data['dir']) && !empty($data['type']) && !empty($data['default'])){
                    
                    $path = FileManager::formatPath($data['dir']);
                    $path .= FileManager::solveNameFromPaternAndObject($this,$data['naming']);
                    $is_dir = false; if(isset($data['is_dir'])){ $is_dir = $data['is_dir']; }

                    if($is_dir){
                        // ENSEMBLE DE FICHIER ------------------------------------------------
                            $path = FileManager::formatPath($path);
                            if(file_exists($path)){
                                foreach (scandir($path) as $file) {
                                    if($file != "." && $file != "..") {
                                        $path_file = $path . $file;
                                        if(file_exists($path_file)){
                                            $this->_files[$name][] = new File($path_file);
                                        }
                                    }
                                }
                                if(empty($this->_files[$name])){
                                    if(isset($data["default_editable"])){
                                            $path = FileManager::solveNameFromPaternAndObject($this,$data['default_editable']);
                                        if(file_exists($path)){
                                            $this->_files[$name][] = new File($path);
                                        }elseif(file_exists($data["default"])) {
                                            $this->_files[$name][] = new File($data["default"]);
                                        } else {
                                            $this->_files[$name][] = new File(Content::PATH_FILE_GENERAL_DEFAULT);
                                        }
                                    }elseif(file_exists($data["default"])) {
                                        $this->_files[$name][] = new File($data["default"]);
                                    } else {
                                        $this->_files[$name][] = new File(Content::PATH_FILE_GENERAL_DEFAULT);
                                    }
                                }	
                            } else {
                                $this->_files[$name][] = new File(Content::PATH_FILE_GENERAL_DEFAULT);
                            }

                    } else {
                        // FICHIER UNIQUE ------------------------------------------------
                            if(isset($data['preferential_format']) && !empty($data['preferential_format'])){
                                if(file_exists($path . "." . $data['preferential_format'])){
                                    $path .= "." . $data['preferential_format'];
                                } else {
                                    $path .= "." . FileManager::findExtention($path, $data["type"]);
                                }
                            } else {
                                $path .= "." . FileManager::findExtention($path, $data["type"]);
                            }
                            if(file_exists($path)){
                                $this->_files[$name] = new File($path);
                            }elseif(isset($data["default_editable"])){
                                $path = FileManager::solveNameFromPaternAndObject($this,$data['default_editable']);
                                if(file_exists($path)){
                                    $this->_files[$name] = new File($path);
                                }elseif(file_exists($data["default"])) {
                                    $this->_files[$name] = new File($data["default"]);
                                } else {
                                    $this->_files[$name] = new File(Content::PATH_FILE_GENERAL_DEFAULT);
                                }
                            }elseif(file_exists($data["default"])) {
                                $this->_files[$name] = new File($data["default"]);
                            } else {
                                $this->_files[$name] = new File(Content::PATH_FILE_GENERAL_DEFAULT);
                            }
                    }

                }
            }
        }
    }
    protected function hydrate(array $donnees){
        foreach($donnees as $champ => $valeur){
          $method = "set".ucfirst($champ);

          if(method_exists($this,$method)){
              $this->$method($this->securite($valeur));
          }
        }
    }

    // GETTERS
    public function getId(int $format = Content::FORMAT_BRUT){
        switch ($format) {
            case Content::FORMAT_BADGE:
                return "<span class='badge bg-secondary' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Identifiant N°{$this->_id}'>N°{$this->_id}</span>";
            
            default:
                return $this->_id;
        }
    }

    public function getUniqid(){
        return $this->_uniqid;
    }
    public function getTimestamp_add($format=NULL){
        if(!empty($format)){
          return date($format, $this->_timestamp_add);
        } else {
            return $this->_timestamp_add;
        }
    }
    public function getTimestamp_updated($format=NULL){
        if(!empty($format)){
          return date($format, $this->_timestamp_updated);
        } else {
            return $this->_timestamp_updated;
        }
    }
    public function getUsable(int $format = Content::FORMAT_BRUT){
        $view = new View();
        switch ($format) {
            case Content::FORMAT_EDITABLE:
                return $view->dispatch(
                    template_name : "input/checkbox",
                    data : [
                        "class_name" => ucfirst(get_class($this)),
                        "uniqid" => $this->getUniqid(),
                        "id" => "usable_" . $this->getUniqid(),
                        "input_name" => "usable",
                        "label" => $this->getUsable(Content::FORMAT_BADGE),
                        "checked" => $this->returnBool($this->_usable),
                        "style" => Style::CHECK_SWITCH
                    ], 
                    write: false);
                
            case Content::FORMAT_BADGE:
                if($this->_usable){ 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Adapté au jdr",
                            "color" => "green-d-3",
                            "style" => Style::STYLE_BACK,
                            "tooltip" => "L'objet a été adapté au jdr"
                        ], 
                        write: false);

                } else {
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Non adapté au jdr",
                            "color" => "red-d-3",
                            "style" => Style::STYLE_BACK,
                            "tooltip" => "L'objet n'a pas encore été adapté au jdr - N'hésitez pas à le modifier"
                        ], 
                        write: false);
                }

            case Content::FORMAT_ICON:
                if($this->_usable){

                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_SOLID,
                            "icon" => "check",
                            "color" => "green-d-3",
                            "tooltip" => "L'objet a été adapté au jdr"
                        ], 
                        write: false); 

                } else { 

                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_SOLID,
                            "icon" => "times",
                            "color" => "red-d-3",
                            "tooltip" => "L'objet n'a pas encore été adapté au jdr - N'hésitez pas à le modifier"
                        ], 
                        write: false);
                }
                
            default:
                return $this->_usable;
        }
    }
    public function getFile(string $name_file, Style $style = new Style([]), $getfirst = false, $getThumbnail = false){
        $className = strtolower(get_class($this));
        if(!isset($className::FILES[$name_file])){
            throw new Exception("Ce nom de fichier n'est pas associé à cet objet.");
        }

        $view = new View();
        $multi_files = false;
        if(isset($this->_files[$name_file])){
            $files = null;
            if(is_array($this->_files[$name_file])){         
                if($getfirst){
                    if(!is_object($this->_files[$name_file][0])){
                        throw new Exception("Le type de l'objet n'est pas correcte.");
                    }
                    if(file_exists($this->_files[$name_file][0]->getPath())){
                        $file = $this->_files[$name_file][0];
                        if($getThumbnail){
                            $file = $file->getThumbnail();
                        }
                    } else {
                        $file = new File(Content::PATH_FILE_GENERAL_DEFAULT);
                        if(isset($className::FILES[$name_file]["default"])){
                            if(file_exists($className::FILES[$name_file]["default"])) {
                                $file = new File($className::FILES[$name_file]["default"]);
                            }
                        }
                    }

                    if($getThumbnail){
                        $file = $file->getThumbnail();
                    }
                    if(get_class($file) != "File"){
                        throw new Exception("Le type de l'objet n'est pas correcte.");
                    }

                    $is_removable = false;
                    if($style->getIs_removable() && $file->getPath() != $className::FILES[$name_file]["default"] && $file->getPath() != Content::PATH_FILE_GENERAL_DEFAULT){
                        $is_removable = true;   
                    }
                    $files[] = [
                        "file" => $file,
                        "is_removable" => $is_removable
                    ];
                }else{
                    $multi_files = true;
                    foreach ($this->_files[$name_file] as $file) {
                        if(!is_object($file)){
                            throw new Exception("Le type de l'objet n'est pas correcte.");
                        }
                        if(file_exists($file->getPath())){
                            $file = $file;
                            if($getThumbnail){
                                $file = $file->getThumbnail();
                            }
                        } else {
                            $file = new File(Content::PATH_FILE_GENERAL_DEFAULT);
                            if(isset($className::FILES[$name_file]["default"])){
                                if(file_exists($className::FILES[$name_file]["default"])) {
                                    $file = new File($className::FILES[$name_file]["default"]);
                                }
                            }
                        }

                        if($getThumbnail){
                            $file = $file->getThumbnail();
                        }
                        if(get_class($file) != "File"){
                            throw new Exception("Ce nom de fichier est associé à un ensemble de fichiers, vous devez utiliser la méthode getFiles().");
                        }

                        $is_removable = false;
                        if($style->getIs_removable() && $file->getPath() != $className::FILES[$name_file]["default"] && $file->getPath() != Content::PATH_FILE_GENERAL_DEFAULT){
                            $is_removable = true;   
                        }
                        $files = [
                            "file" => $file,
                            "is_removable" => $is_removable
                        ];
                    }
                }
            } else {
                if(!is_object($this->_files[$name_file])){
                    throw new Exception("Le type de l'objet n'est pas correcte.");
                }
                if(file_exists($this->_files[$name_file]->getPath())){
                    $file = $this->_files[$name_file];
                    if($getThumbnail){
                        $file = $file->getThumbnail();
                    }
                } else {
                    $file = new File(Content::PATH_FILE_GENERAL_DEFAULT);
                    if(isset($className::FILES[$name_file]["default"])){
                        if(file_exists($className::FILES[$name_file]["default"])) {
                            $file = new File($className::FILES[$name_file]["default"]);
                        }
                    }
                }
                if(get_class($file) != "File"){
                    throw new Exception("Le type de l'objet n'est pas correcte.");
                }

                $is_removable = false;
                if($style->getIs_removable() && $file->getPath() != $className::FILES[$name_file]["default"] && $file->getPath() != Content::PATH_FILE_GENERAL_DEFAULT){
                    $is_removable = true;   
                }
                $files = [
                    "file" => $file,
                    "is_removable" => $is_removable
                ];
            }
        } else {
            throw new Exception("Ce nom de fichier n'est pas associé à cet objet.");
        }

        switch ($style->getDisplay()) {
            case Content::FORMAT_BRUT:
                $return = [];
                if($multi_files){
                    foreach ($files as $file) {
                        $return[] = $file['file']->getPath();
                    }
                    return $return;
                } else {
                    return $files['file']->getPath();
                }

            case Content::FORMAT_OBJECT:
                return $files;

            case Content::DISPLAY_CARROUSEL:
                return $view->dispatch(
                    template_name : "carrousel",
                    data : [
                        "files" => $files,
                        "is_removable" => $style->getIs_removable()
                    ], 
                    write: false);

            case  Content::DISPLAY_EDITABLE:
                if($multi_files){
                    $style->setDisplay(Content::DISPLAY_CARROUSEL);
                } else {
                    $style->setDisplay(Content::FORMAT_VIEW);
                }
       
                $style->setIs_removable(true);

                ob_start();
                    echo $this->getFile($name_file, $style);

                    $view->dispatch(
                        template_name : "input/file",
                        data : [
                            "url" => "index.php?c=".strtolower($className)."&a=upload",
                            "uniqid" => $this->getUniqid(),
                            "label" => "Modifier le fichier",
                            "view_img_path" => "",
                            "extention_available" => FileManager::getListeExtention(FileManager::FORMAT_IMG),
                            "name_file" => $name_file
                        ], 
                        write: true);
                return ob_get_clean();

            case Content::FORMAT_VIEW:
                $style->setIs_download(true);

        }

        $return = "";
        if($multi_files){
            foreach ($files as $file) {
                if($file['is_removable']){
                    $style->setIs_removable(true);
                } else {
                    $style->setIs_removable(false);
                }
                $return .= $file['file']->getVisual($style);
            }
            return $return;
        } else {
            if($files['is_removable']){
                $style->setIs_removable(true);
            } else {
                $style->setIs_removable(false);
            }
            return $files['file']->getVisual($style);
        }
    }

    // SETTERS
    public function setId($data){
        if($data >= 0){
            $this->_id = $data;
            return true;
        } else {
            throw new Exception("Id est incorrect");
        }
    }
    public function setUniqid($data){
        $this->_uniqid = $data;
        return true;
    }    
    public function setTimestamp_add($data = ''){
        if(empty($data)){
            $this->_timestamp_add = time();
        } else {
            $date = new DateTime();
            if($this->isTimestamp($data)){
                $date->setTimestamp(intval($data));
            }else {
                try {
                    $date = new DateTime($data);
                } catch (\Exception $e) {
                    return 'La date de création est incorrecte (format ?)';
                }
            }
            $this->_timestamp_add = $date->format('U');
            return true;
        }
    }
    public function setTimestamp_updated($data = ''){
        if(empty($data)){
            $this->_timestamp_updated = time();
        } else {
            $date = new DateTime();
            if($this->isTimestamp($data)){
                $date->setTimestamp(intval($data));
            }else {
                try {
                    $date = new DateTime($data);
                } catch (\Exception $e) {
                    return 'La date de création est incorrecte (format ?)';
                }
            }
            $this->_timestamp_updated = $date->format('U');
            return true;
        }
    }
    public function setUsable($data){
        $this->_usable = $this->returnBool($data);
        return true;
    }

    public function getVisual(Style $style = new Style(["display" => Content::DISPLAY_CARD, "size" => "300"])) {
        $user = ControllerConnect::getCurrentUser();
        $bookmark_icon = Style::ICON_REGULAR;
        if($user->in_bookmark($this)){
            $bookmark_icon = Style::ICON_SOLID;
        }

        //OPTIONS
        if($style->getSize() < 100){$style->setSize(300);}

        $view = new View(View::TEMPLATE_DISPLAY);
        $className = strtolower(get_class($this));
        switch ($style->getDisplay()) {
            case Content::DISPLAY_EDITABLE:
                $template_name = $className."/editable";
            break;
            case  Content::DISPLAY_CARD:      
                $template_name =  $className."/card";
            break;
            case Content::DISPLAY_RESUME: 
                $template_name = $className."/resume"; 
            break;

            default:
                throw new Exception("Erreur : format de display non reconnu");
        }

        return $view->dispatch(
            template_name : $template_name,
            data : [
                "obj" => $this,
                "user" => $user,
                "bookmark_icon" => $bookmark_icon,
                "style" => $style
            ], 
            write: false);

    }

    static function exist($obj){
        if(is_object($obj)){
            if(!empty($obj)){
                if($obj->getId() > 0 && $obj->getId() != null){
                    return true;
                }
            }
        }
        return false;
    }
}