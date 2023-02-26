<?php 
    // Obligatoire
    if(!isset($icon)) {throw new Exception("icon is not set");}else{if(!is_string($icon)) {throw new Exception("icon is not set");}}
    
    // Conseillé
    if(!isset($color)) { $color = "main";}else{if(!View::isValidColor($color)) {$color = "main";}}
    if(!isset($content)) { $content = "";}else{if(!is_string($content)) {$content = "";}}
    if(!isset($style)) { $style = View::STYLE_ICON_SOLID;}else{if(!in_array($style, [View::STYLE_ICON_REGULAR, VIEW::STYLE_ICON_SOLID, VIEW::STYLE_ICON_MEDIA])) {$style = View::STYLE_ICON_SOLID;}}

    // Optionnel - valeur par défault ok
    if(!isset($content_placement)) { $content_placement = "before";}else{if(!in_array($content_placement, ["before", "after"])) {$content_placement = "before";}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "";}else{if(!is_string($id)) {$id = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = "bottom";}else{if(!is_string($tooltip_placement)) {$tooltip_placement = "bottom";}}
    if(isset($href)){$href = "href=\"" . $href. "\"";}else{$href = "";}
    if(isset($onclick)){$onclick = "onclick=\"". $onclick. ";\"";}else{$onclick = "";}

    $content_before = ""; $content_after = "";
    if($content_placement == "after"){
        $content_after = "<span class='ms-1'>".$content."</span>";;
    } else {
        $content_before = "<span class='me-1'>".$content."</span>";
    }

    if($style == View::STYLE_ICON_MEDIA){ ?>

        <span><i style="<?=$css?>" <?=$data?> id="<?=$id?>" <?=$href?> <?=$onclick?> data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>" class="text-<?=$color?> <?=$class?>"><?=$content_before?><img class='icon' src='medias/icons/<?=$icon?>'><?=$content_after?></i></span>
        
    <?php } else { ?>

        <span><?=$content_before?><i style="<?=$css?>" <?=$data?> id="<?=$id?>" <?=$href?> <?=$onclick?> data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>" class="<?=$style?> fa-<?=$icon?> text-<?=$color?> <?=$class?>"></i><?=$content_after?></span>
        
    <?php }