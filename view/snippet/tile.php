<?php 
    // Obligatoire
    if(!isset($title)) {throw new Exception("title is not set");}else{if(!is_string($title)) {throw new Exception("title is not set");}}
    if(!isset($text)) {$text = "";}else{if(!is_string($text) && !is_numeric($text)) {$text="";}}
    
    // Conseillé
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($color_title)) { $color_title = $color;}else{if(!Style::isValidColor($color_title)) {$color_title = $color;}}
    if(!isset($color_text)) { $color_text = $color;}else{if(!Style::isValidColor($color_text)) {$color_text = $color;}}
    if(!isset($with_border)) { $with_border = true;}else{if(!is_bool($with_border)) {$with_border = true;}}
    if(!isset($comment)) {$comment = "";}else{if(!is_string($comment) && !is_numeric($comment)) {$comment="";}}
    if(!isset($size)) {$size = 18;}else{if(!is_numeric($size) && !in_array($size, [Style::SIZE_SM, Style::SIZE_MD, Style::SIZE_LG, Style::SIZE_XL])) {$size=18;}}

    // Optionnel - valeur par défault ok
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "";}else{if(!is_string($id)) {$id = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($tooltip)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}

    if($with_border){
        $class .= " border-{$color} border-1 border-solid";
    }

    $coef = 1 - strlen($title) / 50;
    $size_title= 1.1 * $coef;
    $size_text = 1;

    switch ($size) {
        case Style::SIZE_SM:
            $size_title = "0.9" * $coef;
            $size_text = "0.8";
        break;
        case Style::SIZE_MD:
            $size_title = "1.1" * $coef;
            $size_text = "0.9";
        break;
        case Style::SIZE_LG:
            $size_title = "1.3" * $coef;
            $size_text = "1";
        break;
        case Style::SIZE_XL:
            $size_title = "1.6" * $coef;
            $size_text = "1.1";
        break;
    }

?>

<div data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>" <?=$data?> id="<?=$id?>" class="d-flex flex-column align-items-center text-center justify-content-center <?=$class?>" style="<?=$css?>">
    <h6 class="m-0 text-<?=$color_title?>" style="font-size:<?=$size_title?>rem"><?=$title?></h6>
    <p class="text-<?=$color_text?>" style="font-size:<?=$size_text?>rem"><?=$text?></p>
    <?php if(!empty($comment)){?>
        <p class="text-grey-d-2 size-0-8"><?=$comment?></p>
    <?php }?>
</div>