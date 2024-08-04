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
        static function includeCss(){
            $style_color_mode = "src/styles/include_manually/color_light_mode.css";

            $dir_import = [
                "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css", // Bootstrap
                "https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css", // Bootstrap Table
                "https://unpkg.com/jquery-resizable-columns@0.2.3/dist/jquery.resizableColumns.css", // Resizable Columns
                [
                    "link" => "https://use.fontawesome.com/releases/v6.4.0/css/all.css", // FontAwesome
                    "media" => "all"
                ],
                "https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css", // Fancybox
                "https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/panzoom.css", // Panzoom
                "https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css", // JSTree
                $style_color_mode,
                "src/styles/plugin",
                "src/styles/css"
            ];

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
                        if($file != '.' && $file != '..' && !empty($file) && !is_dir($path)){
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
            }

            require_once "src/styles/common/color_constructor.php";
        }
        static function includeJS(){  
            if($_SESSION['JqueryAppel'] == false ){?>
                    
                    <?php $dir_import = [
                        "https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js", // Jquery,
                        [
                            "link" => "https://code.jquery.com/ui/1.13.3/jquery-ui.min.js", // Jquery UI
                            "crossorigin" => "anonymous",
                            "integrity" => "sha256-sw0iNNXmOJbQhYFuC9OF2kOlD5KQKe1y5lfBn4C9Sjg="
                        ],
                        "src/js/plugin/upload/vendor/jquery.ui.widget.js", // Jquery WIDGET UI
                        [
                            "link" => "https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js", // POPPER
                            "crossorigin" => "anonymous",
                            "integrity" => "sha512-2rNj2KJ+D8s1ceNasTIex6z4HWyOnEYLVC3FigGOmyQCZc2eBXKgOxQmo3oKLHyfcj53uz4QMsRCWNbLd32Q1g=="
                        ],
                        [
                            "link" => "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js", // Bootstrap
                            "crossorigin" => "anonymous",
                            "integrity" => "sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
                        ],
                        [
                            "link" => "https://kit.fontawesome.com/a416056d6c.js",  // FontAwesome
                            "crossorigin" => "anonymous",
                            "integrity" => ""
                        ],
                        "https://cdn.jsdelivr.net/npm/amplitudejs@5.3.2/dist/amplitude.js", // Amplitude JS, outil d'aide à la création d'un lecteur
                        "https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js", // Fancybox
                        "src/js/", // Ini et function
                        "src/js/functions/",
                        "src/js/components/",
                        "src/js/modules/",
                        "src/js/plugin/upload/",
                        "src/js/ajax/",
                        "src/js/ajax/modules/",
                        "src/js/plugin/ckeditor5/", //https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js
                        "src/js/plugin/",
                        "src/js/plugin/table-bootstrap-export/libs/",
                        "src/js/plugin/table-bootstrap-export/tableExport.min.js",
                        "https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js", // Table Export
                        "https://unpkg.com/bootstrap-table@1.18.2/dist/bootstrap-table.min.js", // Bootstrap Table
                        "https://unpkg.com/bootstrap-table@1.18.2/dist/locale/bootstrap-table-fr-FR.min.js", // Bootstrap Table
                        "https://unpkg.com/bootstrap-table@1.19.1/dist/extensions/multiple-sort/bootstrap-table-multiple-sort.js", // Bootstrap Table
                        "https://cdn.jsdelivr.net/gh/wenzhixin/bootstrap-table-examples@master/utils/natural-sorting/dist/natural-sorting.js", // Bootstrap Table
                        "https://unpkg.com/bootstrap-table@1.19.1/dist/extensions/filter-control/bootstrap-table-filter-control.min.js", // Bootstrap Table
                        "https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/export/bootstrap-table-export.min.js", // Bootstrap Table
                        "https://unpkg.com/bootstrap-table@1.19.1/dist/extensions/toolbar/bootstrap-table-toolbar.min.js", // Bootstrap Table
                        "https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/resizable/bootstrap-table-resizable.min.js", // Bootstrap Table
                        "https://unpkg.com/jquery-resizable-columns@0.2.3/dist/jquery.resizableColumns.min.js" // Resizable Columns
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
                                if($file != '.' && $file != '..' && !empty($file) && !is_dir($path)){
                                    if(substr($path, -3) == ".js"){
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