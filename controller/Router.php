<?php 
class Router {
    use SecurityFct;

    private string $_url;
    private object $_controller;
    private string $_action;
    private array $_params;

    public function __construct($url) {
        try {
            $this->_url = filter_var($url, FILTER_SANITIZE_URL);

            $this->_controller = new View();
            $this->_action = "includeMainTemplate";
            $this->_params = array();
            $this->parseUrl();
            $this->dispatch();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function parseUrl() {
        $url = array_slice(explode("/", $this->_url), 1);
        
        if(isset($_GET['c'])){
            $this->_controller = $this->loadController($_GET['c']);
        }
        if(isset($_GET['a'])){
            $this->_action = $_GET['a'];
        }
        if(count($url) > 0) {
            foreach($url AS $key => $param) {
                $this->_params[$key] = $param;
            }
        }
    }

    private function loadController(string $controller) {
        $controllerName = "Controller".ucfirst($controller);
        if(class_exists($controllerName)) {
            $controller = new $controllerName();
            return $controller;
        } else {
            return new View();
        }
    }

    private function dispatch() {
        if(method_exists($this->_controller, $this->_action)) {
            $action = $this->_action;
            $this->_controller->$action(extract($this->_params));
        } else {
            $this->_controller->includeMainTemplate();
        }
    }

    public function getController() {
        return $this->_controller;
    }
    public function getAction(){
        return $this->_action;   
    }
    public function getParams(string $name = null){
        if(isset($this->_params[$name])){
            return $this->_params[$name];
        }
        return $this->_params;
    }

    // Statics functions
        static function includeCss(){ ?>

            <!-- C U S T O M -->
            <?php $dir_import = [
                [
                    "link" => "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css", // Bootstrap
                    "crossorigin" => "anonymous",
                    "integrity" => "sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM"
                ],
                "https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css",
                "https://unpkg.com/jquery-resizable-columns@0.2.3/dist/jquery.resizableColumns.css",
                [
                    "link" => "https://use.fontawesome.com/releases/v6.4.0/css/all.css",
                    "media" => "all"
                ],
                "https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css",
                "https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/panzoom.css",
                "https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css",
                "src/css/plugin",
                "src/css/styles"
            ];?>

            <?php 
            foreach ($dir_import as $dir) {
                if(is_array($dir)){
                    if(isset($dir['link'])){$link = $dir['link'];}else{$link = "";}
                    if(isset($dir['crossorigin'])){$crossorigin = $dir['crossorigin'];}else{$crossorigin = "";}
                    if(isset($dir['integrity'])){$integrity = $dir['integrity'];}else{ $integrity = "";}
                    if(isset($dir['media'])){$media = $dir['media'];}else{ $media = "";}
                    $dir = $link;
                }else{
                    $crossorigin = "";
                    $integrity = "";
                    $media = "";
                }

                if(is_dir($dir)){
                    $dir = FileManager::formatPath($dir, false, true);
                    $dir_url = $dir;
                    if(!substr($dir,4) == "http"){
                        $dir_url = $GLOBALS['project']['base_url'].$dir;
                    }
                    foreach(scandir($dir) as $file) {
                        $path =  $dir_url . $file;
                        if($file != '.' && $file != '..' && !empty($file) && !is_dir($dir.$file)){
                            if(substr($path, -4) == ".css"){
                                ?> <link href="<?=$path?>" crossorigin="<?=$crossorigin?>" media="<?=$media?>" integrity="<?=$integrity?>" rel="stylesheet" type="text/css"> <?php
                            }
                        }
                    }  
                } else {
                    if(!substr($dir,4) == "http"){
                        $dir = $GLOBALS['project']['base_url'].$dir;
                    }
                    if(substr($dir, -4) == ".css"){
                        ?> <link href="<?=$dir?>"  crossorigin="<?=$crossorigin?>" media="<?=$media?>" integrity="<?=$integrity?>" rel="stylesheet" type="text/css"> <?php
                    }
                }
            } ?>

            <?php  require_once "src/css/common/color.php";
        }
        static function includeJS(){  
            if($_SESSION['JqueryAppel'] == false ){?>
                    
                    <?php $dir_import = [
                        [
                            "link" => "https://code.jquery.com/jquery-3.7.0.min.js", // Jquery
                            "crossorigin" => "anonymous",
                            "integrity" => "sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="
                        ],
                        [
                            "link" => "https://code.jquery.com/ui/1.13.2/jquery-ui.min.js", // Jquery UI
                            "crossorigin" => "anonymous",
                            "integrity" => "sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0="
                        ],
                        "src/js/plugin/upload/vendor/jquery.ui.widget.js",
                        [
                            "link" => "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js", // Bootstrap
                            "crossorigin" => "anonymous",
                            "integrity" => "sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
                        ],
                        [
                            "link" => "https://kit.fontawesome.com/a416056d6c.js",  // FontAwesome
                            "crossorigin" => "anonymous",
                            "integrity" => ""
                        ],
                        "https://cdn.jsdelivr.net/npm/amplitudejs@5.3.2/dist/amplitude.js", // Amplitude JS, outil d'aide à la création d'un lecteur
                        "https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js", // Fancybox
                        "src/js/", // Ini et function
                        "src/js/functions",
                        "src/js/modules",
                        "src/js/plugin/upload/",
                        "src/js/ajax/",
                        "src/js/ajax/modules",
                        "src/js/plugin/ckeditor5/", //https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js
                        "src/js/plugin/",
                        "src/js/plugin/table-bootstrap-export/libs/",
                        "src/js/plugin/table-bootstrap-export/tableExport.min.js",
                        "https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js",
                        "https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.js",
                        "https://unpkg.com/bootstrap-table@1.18.2/dist/locale/bootstrap-table-fr-FR.min.js",
                        "https://unpkg.com/bootstrap-table@1.19.1/dist/extensions/multiple-sort/bootstrap-table-multiple-sort.js",
                        "https://cdn.jsdelivr.net/gh/wenzhixin/bootstrap-table-examples@master/utils/natural-sorting/dist/natural-sorting.js",
                        "https://unpkg.com/bootstrap-table@1.19.1/dist/extensions/filter-control/bootstrap-table-filter-control.min.js",
                        "https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/export/bootstrap-table-export.min.js",
                        "https://unpkg.com/bootstrap-table@1.19.1/dist/extensions/toolbar/bootstrap-table-toolbar.min.js",
                        "https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/resizable/bootstrap-table-resizable.min.js",
                        "https://unpkg.com/jquery-resizable-columns@0.2.3/dist/jquery.resizableColumns.min.js"
                    ];?>

                    <?php 
                    foreach ($dir_import as $dir) {
                        if(is_array($dir)){
                            if(isset($dir['link'])){$link = $dir['link'];}else{$link = "";}
                            if(isset($dir['crossorigin'])){$crossorigin = $dir['crossorigin'];}else{$crossorigin = "";}
                            if(isset($dir['integrity'])){$integrity = $dir['integrity'];}else{ $integrity = "";}
                            $dir = $link;
                        }else{
                            $crossorigin = "";
                            $integrity = "";
                        }

                        if(is_dir($dir)){
                            $dir = FileManager::formatPath($dir, false, true);
                            $dir_url = $dir;
                            if(!substr($dir,4) == "http"){
                                $dir_url = $GLOBALS['project']['base_url'].$dir;
                            }
                            foreach(scandir($dir) as $file) {
                                $path = $dir_url . $file;
                                if($file != '.' && $file != '..' && !empty($file) && !is_dir($dir.$file)){
                                    if(substr($path, -2) == "js"){
                                        ?> <script src="<?=$path?>" crossorigin="<?=$crossorigin?>" integrity="<?=$integrity?>" type="text/javascript"></script> <?php
                                    }
                                }
                            }  
                        } else {
                            if(!substr($dir,4) == "http"){
                                $dir = $GLOBALS['project']['base_url'].$dir;
                            }
                            if(substr($dir, -2) == "js"){
                                ?> <script src="<?=$dir?>"  crossorigin="<?=$crossorigin?>" integrity="<?=$integrity?>" type="text/javascript"></script> <?php
                            }
                        }
                    } ?>

                <?php  $_SESSION['JqueryAppel'] = true;

            }
        }

}