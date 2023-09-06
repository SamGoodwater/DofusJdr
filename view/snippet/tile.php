<?php 
    // Obligatoire
    if(!isset($title)) {throw new Exception("title is not set");}else{if(!is_string($title)) {throw new Exception("title is not set");}}
    
    // Conseillé
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($comment)) {$comment = "";}else{if(!is_string($comment) && !is_numeric($comment)) {$comment="";}}
    if(!isset($size)) {$size = 18;}else{if(!is_numeric($size)) {$size=18;}}

    // Optionnel - valeur par défault ok
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "";}else{if(!is_string($id)) {$id = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($tooltip)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}
?>

<div data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>" <?=$data?> id="<?=$id?>" class="card border-<?=$color?> border-1 border-solid  <?=$class?>" style="width: <?=$size?>rem;<?=$css?>">
    <div class="m-2">
        <h6 class="m-0 text-<?=$color?>"><?=$title?></h6>
        <p class="text-grey-d-2 size-0-8"><?=$comment?></p>
    </div> 
</div>