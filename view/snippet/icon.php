<?php 
    // Obligatoire
    if(!isset($icon)) {throw new Exception("icon is not set");}else{if(!is_string($icon)) {throw new Exception("icon is not set");}}
    
    // Conseillé
    if(!isset($is_btn)) { $is_btn = false;}else{if(!is_bool($is_btn)) {$is_btn = false;}}
    if(!isset($btn_type)) { $btn_type = Style::STYLE_TEXT;}else{if(!in_array($btn_type, [Style::STYLE_BACK, Style::STYLE_BORDER, STYLE::STYLE_UNDERLINE, Style::STYLE_TEXT, Style::STYLE_NONE])) {$btn_type = Style::STYLE_TEXT;}}
    if($is_btn || $btn_type == Style::STYLE_NONE){
        $btn_type = "btn-".$btn_type."-";
    } else {
        $btn_type = "text-";
    }
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($content)) { $content = "";}else{if(!is_string($content) && !is_numeric($content)) {$content = "";}}
    if(!isset($style)) { $style = Style::ICON_SOLID;}else{if(!in_array($style, [Style::ICON_REGULAR, Style::ICON_SOLID, Style::ICON_MEDIA])) {$style = Style::ICON_SOLID;}}
    if(!isset($dirfile)) { $dirfile = "icons/modules/";}else{if(!is_string($dirfile) && empty($dirfile)) {$dirfile = "icons/modules/";}}
    $dirfile = str_replace("../", "/", $dirfile);$dirfile = str_replace("./", "/", $dirfile);
    if(!isset($size)) { $size = "";}else{if(!in_array($size, [Style::SIZE_SM, Style::SIZE_LG, '', Style::SIZE_XL]) && !is_string($size) && !is_numeric($size)) {$size = "";}}
    if($style != Style::ICON_MEDIA){
        switch ($size) {
            case Style::SIZE_XL:
                $size = "size-2";
            break;
            case Style::SIZE_LG:
                $size = "size-1-5";
            break;
            case Style::SIZE_SM:
                $size = "size-0-8";
            break;
        }
    } elseif($style == Style::ICON_MEDIA) {
        if($size == Style::SIZE_XL){
            $size = "icon-50";
        } elseif($size == Style::SIZE_LG) {
            $size = "icon-30";
        } elseif($size == Style::SIZE_SM) {
            $size = "icon-15";
        } elseif(is_numeric($size)) {
            $size = "icon-".$size;
        } else {
            $size = "";
        }
    }
    
    // Optionnel - valeur par défault ok
    if(!isset($truncate)) { $truncate = "100";}else{if(!is_numeric($truncate)) {$truncate = "100";}}
    if(!isset($content_placement)) { $content_placement = Style::POSITION_LEFT;}else{if(!in_array($content_placement, [Style::POSITION_BOTTOM, Style::POSITION_LEFT, Style::POSITION_RIGHT, Style::POSITION_TOP])) {$content_placement = Style::POSITION_LEFT;}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "";}else{if(!is_string($id)) {$id = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($content)) {$tooltip = "";}}
    if(!isset($tooltip_with_content)) { $tooltip_with_content = true;}else{if(!is_bool($tooltip_with_content)) {$tooltip_with_content = true;}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}
    if(isset($href)){$href = "href=\"" . $href. "\"";}else{$href = "";}
    if(isset($onclick)){$onclick = "onclick=\"". $onclick. ";\"";}else{$onclick = "";}

    $content_before = ""; $content_after = "";$top_bottom_class = "";

    switch ($content_placement) {
        case Style::POSITION_BOTTOM:
            $top_bottom_class = "d-flex flex-column align-items-center";
            $content_after = "<span class='size-0-7 position-relative light' style='top:-1px;'>".$content."</span>";
        break;
        case Style::POSITION_TOP:
            $top_bottom_class = "d-flex flex-column align-items-center";
            $content_before = "<span class='size-0-7 position-relative light' style='bottom:-1px;'>".$content."</span>";
        break;
        case Style::POSITION_LEFT:
            $content_before = "<span class='me-1'>".$content."</span>";
        break;
        case Style::POSITION_RIGHT:
            $content_after = "<span class='ms-1'>".$content."</span>";
        break;
    }
    if($tooltip_with_content && $tooltip != ""){$tooltip = $tooltip . " : " . $content;}

    if($style == Style::ICON_MEDIA){ ?>

        <span class="<?=$top_bottom_class?> truncate-<?=$truncate?>">
            <i style="<?=$css?>" <?=$data?> id="<?=$id?>" <?=$href?> <?=$onclick?> data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>" class="<?=$btn_type.$color?> <?=$class?>"><?=$content_before?><img class='icon <?=$size?>' src='medias/<?=$dirfile.$icon?>'><?=$content_after?></i>
        </span>
        
    <?php } else { ?>

        <span class="<?=$top_bottom_class?> truncate-<?=$truncate?>"><?=$content_before?><i style="<?=$css?>" <?=$data?> id="<?=$id?>" <?=$href?> <?=$onclick?> data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>" class="<?=$style?> fa-<?=$icon?> <?=$btn_type.$color?> <?=$class?> <?=$size?>"></i><?=$content_after?></span>
        
    <?php }