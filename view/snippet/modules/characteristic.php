<?php 
    // Obligatoire
    if(!isset($name)) {throw new Exception("content is not set");}else{if(!is_string($name) && !is_numeric($name)) {throw new Exception("content is not set");}}
    if(!isset($icon)) {throw new Exception("icon is not set");}else{if(!is_string($icon) && !is_numeric($icon)) {throw new Exception("icon is not set");}}
    if(!isset($value)) {throw new Exception("value is not set");}else{if(!is_string($value) && !is_numeric($value)) {throw new Exception("value is not set");}}
    
    // Conseillé
    if(!isset($detail)) { $detail = "";}else{if(!is_string($detail)) {$detail = "";}}
    if(!isset($detail_on_level)) { $detail_on_level = "";}else{if(!is_string($detail_on_level)) {$detail_on_level = "";}}
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($size)) { $size = Style::SIZE_MD;}else{if(!in_array($size, [Style::SIZE_SM, Style::SIZE_LG, Style::SIZE_MD, Style::SIZE_XL])) {$size = Style::SIZE_MD;}}

    if(!isset($style_icon)) { $style_icon = Style::ICON_MEDIA;}else{if(!in_array($style_icon, [Style::ICON_REGULAR, Style::ICON_SOLID, Style::ICON_MEDIA])) {$style_icon = Style::ICON_MEDIA;}}
    if(!isset($dirfile)) { $dirfile = "medias/icons/modules/";}else{if(!is_string($dirfile)) {$dirfile = "medias/icons/modules/";}}
    $dirfile = str_replace("../", "/", $dirfile);$dirfile = str_replace("./", "/", $dirfile);
    
    // Optionnel - valeur par défault ok
    if(!isset($truncate)) { $truncate = "150";}else{if(!is_numeric($truncate)) {$truncate = "120";}}
    if(!isset($comment)) {$comment = "";}else{if(!is_string($comment) && !is_numeric($comment)) {$comment="";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($content)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}

    if(empty($tooltip)){
        $tooltip = $name . " : " . $value;
    }

    $size_icon = "";
    $size_name= "";
    $size_detail = "";
    $size_hover = "";
    switch ($size) {
        case Style::SIZE_XL:
            $size_name = "size-1-3";
            $size_detail = "size-1";
        break;
        case Style::SIZE_LG:
            $size_name = "size-1-2";
            $size_detail = "size-0-9";
        break;
        case Style::SIZE_MD:
            $size_name = "size-1";
            $size_detail = "size-0-8";
        break;
        case Style::SIZE_SM:
            $size_name = "size-0-9";
            $size_detail = "size-0-7";
        break;
    }
    if($style_icon != Style::ICON_MEDIA){
        switch ($size) {
            case Style::SIZE_XL:
                $size_icon = "size-2";
            break;
            case Style::SIZE_LG:
                $size_icon = "size-1-5";
            break;
            case Style::SIZE_MD:
                $size_icon = "size-1";
            break;
            case Style::SIZE_SM:
                $size_icon = "size-0-8";
            break;
        }
    } elseif($style_icon == Style::ICON_MEDIA) {
        switch ($size) {
            case Style::SIZE_XL:
                $size_icon = "icon-40";
            break;
            case Style::SIZE_LG:
                $size_icon = "icon-30";
            break;
            case Style::SIZE_MD:
                $size_icon = "icon-20";
            break;
            case Style::SIZE_SM:
                $size_icon = "icon-15";
            break;
        }
    }

?>

<div>
    <div style="position:relative;">
        <div class="characteristic-card card-<?=$size?> border-<?=$color?>-d-4 back-white">
            <div class="d-flex flex-column align-items-center" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>">
                <div class="d-flex flex-nowrap justify-content-center gap-1 align-items-center">
                    <div>
                        <span class="text-<?=$color?>-d-4">
                            <?php if($style_icon == Style::ICON_MEDIA){ ?>
                                <img src="<?=$dirfile . $icon?>" alt="<?=$name?>" class="icon <?=$size_icon?>"/>
                            <?php } else { ?>
                                <i class="<?=$style_icon?> fa-<?=$icon?>"></i>
                            <?php } ?>
                        </span>
                    </div>
                    <p <?=$data?> data-event-display-caractistic-add-text="false" class="text-<?=$color?>-d-4 text-title truncate-<?=$truncate?>"><?=$value?></p>
                </div>
                <p style="position:relative;top:-0.5rem;" class="<?=$size_name?> text-<?=$color?>-d-4 text-center truncate-<?=$truncate?>"><?=ucfirst($name)?></p>
            </div>
            <div class="characteristic-card-showed back-white border-<?=$color?>-d-4 px-1 pb-1">
                <div class="nav-item-divider m-0 back-<?=$color?>-l-2 mx-4"></div>
                <p class="text-grey-d-1 size-0-6 italic">Provenance de la caractéristique</p>
                <p class="text-center <?=$size_detail?>"><?=$detail?></p>
                <?php if(!empty($detail_on_level)){ ?>
                    <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-1">
                        <?=$detail_on_level?>
                    </div>
                <?php } ?>
                <?php if(!empty($comment)){ ?>
                    <p><small class="text-grey-d-2"><?=$comment?> </small></p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>