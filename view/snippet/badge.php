<?php 
    // Obligatoire
    if(!isset($content)) {throw new Exception("content is not set");}else{if(!is_string($content)) {throw new Exception("content is not set");}}
    
    // Conseillé
    if(!isset($color)) { $color = "main";}else{if(!View::isValidColor($color)) {$color = "main";}}
    if(!isset($style)) { $style = View::STYLE_BACK;}else{if(!in_array($style, [View::STYLE_BACK, VIEW::STYLE_OUTLINE, View::STYLE_NONE])) {$style = View::STYLE_BACK;}}

    // Optionnel - valeur par défault ok
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "";}else{if(!is_string($id)) {$id = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = "bottom";}else{if(!is_string($tooltip_placement)) {$tooltip_placement = "bottom";}}
    if(isset($href)){$href = "href=\"" . $href. "\"";}else{$href = "";}
    if(isset($onclick)){$onclick = "onclick=\"". $onclick. ";\"";}else{$onclick = "";}

    switch ($style) {
        case View::STYLE_OUTLINE:
            $style = "badge-outline border-".$color . " text-" . $color;
        break;

        case View::STYLE_BACK:
            $style = "badge back-".$color;
        break;
        
        default:
            $style = "";
        break;
    }
?>

<span style="<?=$css?>" <?=$data?> id="<?=$id?>" <?=$href?> <?=$onclick?> data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>" class="<?=$style?> <?=$class?>"><?=$content?></span>