<?php

abstract class Content
{
    use CheckingFct, SecurityFct;
    
    //Format GETTERS

    const DISPLAY_CARD = 100;
    const DISPLAY_RESUME = 101;
    const DISPLAY_EDITABLE = 102;
    const DISPLAY_LIST = 104;
    const DISPLAY_FULL = 103;
    const DISPLAY = [
        self::DISPLAY_EDITABLE => "Modifier",
        self::DISPLAY_CARD => "Carte",
        self::DISPLAY_RESUME => "Résumé",
        self::DISPLAY_LIST => "Liste",
        self::DISPLAY_FULL => "Complet",
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
    //         "default" => "medias/classes/default.png",
    //         "dir" => "medias/classes/",
    //         "preferential_format" => "png",
    //         'naming' => "[uniqid]"
    //     ],
    //     ...
    // ]

    const PATH_FILE_GENERAL_DEFAULT = "medias/no_file_found_logo.png";

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
                    } elseif(file_exists($data["default"])) {
                        $this->_files[$name] = new File($data["default"]);
                    } else {
                        $this->_files[$name] = new File(Content::PATH_FILE_GENERAL_DEFAULT);
                    }
                }
            }
        }
    }

    protected function hydrate(array $donnees){
        foreach($donnees as $champ => $valeur){
          $method = "set".ucfirst($champ);

          if(method_exists($this,$method))
          {
              $this->$method($this->securite($valeur));
          }
        }
    }
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
    public function getFile(string $name_file, Style $style = new Style([])){
        $className = strtolower(get_class($this));
        $view = new View();
        if(isset($this->_files[$name_file])){
            $file = $this->_files[$name_file];
        } else {
            throw new Exception("Ce nom de fichier n'est pas associé à cet objet.");
        }

        if($file->getPath() == $className::FILES[$name_file]["default"] || $file->getPath() == Content::PATH_FILE_GENERAL_DEFAULT){
            $style->setIs_removable(false);
        } else {
            $style->setIs_removable(true);
        }
        switch ($style->getDisplay()) {
            case  Content::FORMAT_BRUT:
                return $file->getPath();

            case Content::FORMAT_OBJECT:
                return $file;

            case  Content::DISPLAY_EDITABLE:
                $style->setFormat(Content::FORMAT_VIEW);
                ob_start();
                    echo $file->getVisual($style);
                    if($style->getIs_removable()){
                        ?><div class="text-center"><a onclick="File.remove('<?=$file->getPath();?>');" class="btn-sm btn-text-red">Supprimer l'image</a></div><?php
                    }
                    $view->dispatch(
                        template_name : "input/file",
                        data : [
                            "url" => "index.php?c=".strtolower($className)."&a=upload",
                            "uniqid" => $this->getUniqid(),
                            "label" => "Modifier l'Image",
                            "view_img_path" => $file->getPath(),
                            "extention_available" => FileManager::getListeExtention(FileManager::FORMAT_IMG),
                            "name_file" => $name_file
                        ], 
                        write: true);
                return ob_get_clean();

            case Content::FORMAT_VIEW:
                $style->setIs_download(true);

        }
        return $file->getVisual($style);
    }
    public function setId($data){
        if($data > 0){
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

    public function getVisual(int $display = Content::DISPLAY_CARD, int $size = 300) {
        $user = ControllerConnect::getCurrentUser();
        $bookmark_icon = Style::ICON_REGULAR;
        if($user->in_bookmark($this)){
            $bookmark_icon = Style::ICON_SOLID;
        }

        //OPTIONS
        if($size < 100){$size = 300;}

        $view = new View(View::TEMPLATE_DISPLAY);
        $className = strtolower(get_class($this));
        switch ($display) {
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
                "size" => $size
            ], 
            write: false);

    }

    static function removeSpecialCaractere(string | int $string){ // Enlève tout les accents et les caractères spéciaux
        $caracteres = array('à' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
        'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
        'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
        'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
        'Œ' => 'oe', 'œ' => 'oe',
        '$' => 's');
        $string = strtr($string, $caracteres);
        $string = preg_replace('#[^A-Za-z0-9]+#', '-', $string);
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        setlocale(LC_ALL, 'fr_FR');
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        $string = preg_replace('#[^0-9a-z]+#i', '_', $string);
        while(strpos($string, '--') !== false)
        {
            $string = str_replace('--', '_', $string);
        }
        $string = trim($string, '_');

        return $string;
    }
    static function getMinMaxFromFormule(string | int $formule){
        $count_min = 0;
        $count_max = 0;
        $same = false;
        if(!preg_match_all("/\[(.*)\]/i", $formule, $matches, PREG_SET_ORDER)){
            $count_min = $formule;
            $count_max = $formule;
            $same = true;
        }else{
            $formule = str_replace(' ', '', $matches[0][1]);
                if(preg_match_all("/(\d{1,}d\d{1,})/i", $formule, $dices, PREG_SET_ORDER)){
                    foreach ($dices as $dice) {
                        $dice = explode('d', $dice[0]);
                        $count_min += $dice[0];
                        $count_max += $dice[0] * $dice[1];
                    }
                }
                if(preg_match_all("/(\d{1,}-\d{1,})/i", $formule, $values, PREG_SET_ORDER)){
                    foreach ($values as $value) {
                        $value = explode('-', $value[0]);
                        $count_min += $value[0];
                        $count_max += $value[1];
                    }
                }
                if(preg_match_all("/(\+\d{1,}|\d{1,}\+)/i", $formule, $additions, PREG_SET_ORDER)){
                    foreach ($additions as $addition) {
                        $addition = str_replace('+', '', $addition[0]);
                        $count_min += $addition;
                        $count_max += $addition;
                    }
                }
                if(preg_match_all("/(Niveau.*(\/|\*)(\d{1,}))/i", $formule, $level, PREG_SET_ORDER)){
                    $level = $level[0];
                    if(is_integer(intval($level[3]))){
                        if($level[2] == "/"){
                            $count_min += round(1 / intval($level[3]));
                            $count_max += round(20 / intval($level[3]));
                        } elseif($level[2] == "*"){
                            $count_min += round(1 * intval($level[3]));
                            $count_max += round(20 * intval($level[3]));
                        }
                    }
                } else {
                    if(preg_match_all("/(Niveau.*)/i", $formule, $level, PREG_SET_ORDER)){
                        $count_min += 1;
                        $count_max += 20;
                    }
                }
        }
        
        return [
            'min' => $count_min,
            'max' => $count_max,
            "same" => $same
        ];
    }
    static function getValueFromFormule(string | int $formule, string | int $var){
        $var = intval(str_replace(' ', '', $var));
        $val = 0;
        if(!preg_match_all("/\[(.*)\]/i", $formule, $matches, PREG_SET_ORDER)){
            $val = $formule;
        }else{
            $formule = str_replace(' ', '', $matches[0][1]);
                if(preg_match_all("/niveau|level/i", $formule, $level, PREG_SET_ORDER)){
                    $val += $var;
                }
                if(preg_match_all("/((\*|\/)(\d{1,})|(\d{1,})(\*|\/))/i", $formule, $multiplications, PREG_SET_ORDER)){
                    foreach ($multiplications as $multiplication) {
                        $multiplication = array_filter($multiplication);
                        if(isset($multiplication[4]) && !isset($multiplication[2])){
                            $multiplication[2] = $multiplication[4];
                        }
                        if(isset($multiplication[5]) && !isset($multiplication[3])){
                            $multiplication[3] = $multiplication[5];
                        }
                        if($multiplication[2] == "*"){
                            $val = $val * intval($multiplication[3]);
                        } elseif($multiplication[2] == "/"){
                            $val = $val / intval($multiplication[3]);
                        } elseif($multiplication[3] == "*"){
                            $val = $val * intval($multiplication[2]);
                        } elseif($multiplication[3] == "/"){
                            $val = $val / intval($multiplication[2]);
                        }
                    }
                }
                if(preg_match_all("/([^*\/](\+|\-)(\d{1,})|(\d{1,})(\+|\-))/i", $formule, $additions, PREG_SET_ORDER)){
                    foreach ($additions as $addition) {
                        $addition = array_filter($addition);
                        if(isset($addition[4]) && !isset($addition[2])){
                            $addition[2] = $addition[4];
                        }
                        if(isset($addition[5]) && !isset($addition[3])){
                            $addition[3] = $addition[5];
                        }
                        if($addition[2] == "+"){
                            $val += intval($addition[3]);
                        } elseif($addition[2] == "-"){
                            $val -= intval($addition[3]);
                        } elseif($addition[3] == "+"){
                            $val += intval($addition[2]);
                        } elseif($addition[3] == "-"){
                            $val -= intval($addition[2]);
                        }
                    }
                }
        }
        return round(intval($val), 0, PHP_ROUND_HALF_DOWN);
    }
}
