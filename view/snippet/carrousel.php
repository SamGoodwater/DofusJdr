<?php 
    $rand_uniqid = uniqid();

    // Obligatoire
    if(!isset($files)) {throw new Exception("files is not set");}else{if(!is_array($files)) {throw new Exception("files is not set");}}

    // Conseillé
    if(!isset($id)) { $id = "carrousel_".$rand_uniqid;}else{if(!is_string($id) && !is_numeric($id)) {$id = "carrousel_".$rand_uniqid;}}
    if(!isset($is_removable)) { $is_removable = false;}else{if(!is_bool($is_removable)) {$is_removable = false;}}

    // Optionnel - valeur par défault ok
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment)) {$comment = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip)&& !is_numeric($tooltip)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}

    $number_of_files = count($files);
?>
<div>
    <div id="<?=$id?>" class="carousel slide <?=$class?>" style="<?=$css?>" <?=$data?> data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>">
        <div class="carousel-indicators">
            <?php for ($i=0; $i < $number_of_files; $i++) { ?>
                <button type="button" data-bs-target="#<?=$id?>" data-bs-slide-to="<?=$i?>" <?php if($i==0){echo "class=\"active\" aria-current=\"true\"";}?> aria-label="Slide <?=$i+1?>"></button>
            <?php } ?>
        </div>
        <div class="carousel-inner">
            <?php foreach($files as $file) { ?>
                <div class="carousel-item">
                    <?=$file['file']->getVisual(new Style(["display" => Content::FORMAT_VIEW]));?>
                    <?php if($file['is_removable'] && $is_removable){ ?>
                        <div class="text-center"><a onclick="File.remove('<?=$file['file']->getPath();?>');" class="btn-sm btn-text-red">Supprimer l'image</a></div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#<?=$id?>" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#<?=$id?>" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <?php if(!empty($comment)){ ?>
        <span class="size-0-8 text-grey"><?=$comment?></span>
    <?php } ?>
</div>

