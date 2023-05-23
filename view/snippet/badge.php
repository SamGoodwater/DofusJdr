<?php 
    // Obligatoire
    if(!isset($content)) {throw new Exception("content is not set");}else{if(!is_string($content) && !is_numeric($content)) {throw new Exception("content is not set");}}
    
    // Conseillé
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($style)) { $style = Style::STYLE_BACK;}else{if(!in_array($style, [Style::STYLE_BACK, Style::STYLE_OUTLINE, Style::STYLE_NONE])) {$style = Style::STYLE_BACK;}}

    // Optionnel - valeur par défault ok
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "";}else{if(!is_string($id)) {$id = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($content)) {$tooltip = "";}}
    if(!isset($tooltip_with_content)) { $tooltip_with_content = true;}else{if(!is_bool($tooltip_with_content)) {$tooltip_with_content = true;}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}
    if(isset($href)){$href = "href=\"" . $href. "\"";}else{$href = "";}
    if(isset($onclick)){$onclick = "onclick=\"". $onclick. ";\"";}else{$onclick = "";}
    if(isset($ondblclick)){$ondblclick = "ondblclick=\"". $ondblclick. ";\"";}else{$ondblclick = "";}

    switch ($style) {
        case Style::STYLE_OUTLINE:
            $style = "badge-outline border-".$color . " text-" . $color;
        break;

        case Style::STYLE_BACK:
            $style = "badge back-".$color;
        break;
        
        default:
            $style = "";
        break;
    }
    if($tooltip_with_content && $tooltip != ""){$tooltip = $tooltip . " : " . $content;}
?>

<span style="<?=$css?>" <?=$data?> id="<?=$id?>" <?=$href?> <?=$onclick?> <?=$ondblclick?> data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>" class="<?=$style?> <?=$class?>"><?=$content?></span>