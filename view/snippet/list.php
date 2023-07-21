<?php 
    $rand_uniqid = uniqid();
    // Obligatoire
    if(!isset($items)) {throw new Exception("items is not set");}else{if(!is_array($items)) {throw new Exception("items is not set");}}
    
    // Conseillé
    if(!isset($title)) {$title = "";}else{if(!is_string($title) && !is_numeric($title)) {$title = "";}}
    if(!isset($id)) { $id = "list_".$rand_uniqid;}else{if(!is_string($id) && !is_numeric($id)) {$id = "list_".$rand_uniqid;}}

    // Optionnel - valeur par défault ok
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($name)) { $name = "";}else{if(!is_string($name)) {$name = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment)) {$comment = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip)&& !is_numeric($tooltip)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}
?>

<div>
    <?php if(!empty($title)){ ?>
        <h5><?=$title?></h5>
    <?php } ?>
    <ul class="list-group list-group-flush m-0 <?=$class?>"  id="<?=$id?>" style="<?=$css?>" <?=$data?> data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>">
        <?php
            foreach ($items as $key => $item) { 
                    $href = $onclick = $display = $item_class = $item_css = $item_id = "";
                    if(isset($item["href"])){$href = "href=\"" . $item["href"]. "\"";}
                    if(isset($item["onclick"])){$onclick = "";}
                    if(isset($item["display"])){$display = $item["display"];}
                    if(isset($item["class"])){$item_class = $item["class"];}
                    if(isset($item["css"])){$item_css = $item["css"];}
                    if(isset($item["id"])){$item_id = $item["id"];}?>
                    <li class="list-group-item p-1 <?=$item_class?>" id="<?=$item_id?>" <?=$item_css?> <?=$href?> <?=$onclick?>><?=$display?></li>
            <?php } ?>
    </ul>
    <?php if(!empty($comment)){ ?>
        <span class="size-0-8 text-grey"><?=$comment?></span>
    <?php } ?>
</div>
