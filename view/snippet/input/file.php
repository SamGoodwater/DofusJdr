<?php 
    // Obligatoire
    if(!isset($url)) {throw new Exception("url is not set");}else{if(!is_string($url)) {throw new Exception("url is not set");}}
    if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(!is_string($uniqid)) {throw new Exception("uniqid is not set");}}
    if(!isset($name_file)) {throw new Exception("name_file is not set");}else{if(!is_string($name_file) && !is_numeric($name_file)) {throw new Exception("name_file is not set");}}
    
    // Conseillé
    if(!isset($label)) { $label = "";}else{if(!is_string($label) && !is_string($label)) {$label = "";}}
    if(!isset($view_img_path)) { $view_img_path = "";}else{if(!is_string($view_img_path) && !is_numeric($view_img_path)) {$view_img_path = "";}}
    if(!isset($is_dropbable)) { $is_dropbable = true;}else{if(!is_bool($is_dropbable)) {$is_dropbable = true;}}
    if(!isset($extention_available)) { $extention_available = FileManager::getListeExtention(FileManager::FORMAT_IMG, FileManager::FORMAT_AUDIO, FileManager::FORMAT_VIDEO, FileManager::FORMAT_PDF, FileManager::FORMAT_DOCUMENT, FileManager::FORMAT_TABLEUR, FileManager::FORMAT_SLIDER);}else{if(!is_array($extention_available)) {$extention_available = FileManager::getListeExtention(FileManager::FORMAT_IMG, FileManager::FORMAT_AUDIO, FileManager::FORMAT_VIDEO, FileManager::FORMAT_PDF, FileManager::FORMAT_DOCUMENT, FileManager::FORMAT_TABLEUR, FileManager::FORMAT_SLIDER);}}
    if(!isset($parameters)) {$parameters = array() ;}else{if(!is_array($parameters)) { $parameters = array();}} // Ecrit sous la forme du [nom => valeur]

    // Optionnel - valeur par défault ok
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "textarea_".$uniqid;}else{if(!is_string($id)) {$id = "textarea_".$uniqid;}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment) && !is_numeric($content)) {$comment = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($content)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}
?>

<div> 
    <div id="fileupload<?=$uniqid?>" class="fileupload">
        <form 
            data-url="<?=$url?>" 
            data-viewimgpath="<?=$view_img_path?>"
            data-dropable="<?=$is_dropbable?>" 
            data-dropzone="#collapse"
            accept=".<?=implode(",.", $extention_available)?>"
            data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>"
            <?=$data?>
            style="<?=$css?>"
            id="<?=$id?>"
            class="<?=$class?>"
            capture="environnement"> 
            <p><?=$label?></p>
            <input class="file-input form-control form-control-<?=$color?>-focus form-control" name="file" type="file" hidden>
            <input name="uniqid" type="hidden" value="<?=$uniqid?>" hidden>
            <input name="name_file" type="hidden" value="<?=$name_file?>" hidden>
            <?php foreach($parameters as $key => $value){ ?>
                <input type="hidden" name="<?=$key?>" value="<?=$value?>">
            <?php } ?>
        </form>
        <section class="progress-area"></section>
        <section class="uploaded-area"></section>
    </div>
    <script>
        File.loadFileUpload('#fileupload<?=$uniqid?>');
    </script>  

    <?php if(!empty($comment)){ ?>
        <span class="size-0-8 text-grey"><?=$comment?></span>
    <?php } ?>
</div>