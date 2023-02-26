<?php 
    // Choix du design
    if(!isset($style)) { $style = View::STYLE_INPUT_BASIC;}else{if(!in_array($style, [View::STYLE_INPUT_BASIC, View::STYLE_INPUT_ICON, View::STYLE_INPUT_FLOATING])) {$style = View::STYLE_INPUT_BASIC;}}
    if(!isset($type)) { $type = "text";}else{if(!in_array($type, ["text", "email", "password", "number", "date", "time", "datetime-local", "month", "week", "url", "search", "tel", "color"])) {$type = "text";}}

    // Obligatoire
    if(isset($is_onchange)) {if(!is_bool($is_onchange)) {$is_onchange = true;}}else{$is_onchange = true;}
    if($is_onchange){
        if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
        if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
        if(!isset($input_name)) {throw new Exception("input_name is not set");}else{if(!is_string($input_name)) {throw new Exception("input_name is not set");}}
    }

    // Conseillé
    if(!isset($label)) {$label = "";}else{if(!is_string($label)) {$label = "";}}
    if(!isset($placeholder)) { $placeholder = "";}else{if(!is_string($placeholder)) {$placeholder = "";}}
    if(!isset($value)) { $value = "";}else{if(!is_string($value)) {$value = "";}}

    // Optionnel - valeur par défault ok
    if(!isset($color)) { $color = "main";}else{if(!View::isValidColor($color)) {$color = "main";}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "";}else{if(!is_string($id)) {$id = "";}}
    if(!isset($name)) { $name = "";}else{if(!is_string($name)) {$name = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment)) {$comment = "";}}
    if(!isset($size)) { $size = View::SIZE_BASIC;}else{if(!in_array($size, [View::SIZE_SM, View::SIZE_MD, View::SIZE_BASIC, View::SIZE_LG, View::SIZE_XL])) {$size = View::SIZE_BASIC;}}
    if(!isset($maxlenght)) { $maxlenght = 300;}else{if(!is_numeric($maxlenght)) {$maxlenght = 300;}}
    if(!isset($required)) { $required = false;}else{if(!is_bool($required)) {$required = false;}}
    if(!isset($disabled)) { $disabled = false;}else{if(!is_bool($disabled)) {$disabled = false;}}
    if(!isset($readonly)) { $readonly = false;}else{if(!is_bool($readonly)) {$readonly = false;}}
    if(!isset($autocomplete)) { $autocomplete = false;}else{if(!is_bool($autocomplete)) {$autocomplete = false;}}
    if(!isset($pattern)) { $pattern = "";}else{if(!is_string($pattern)) {$pattern = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = "bottom";}else{if(!is_string($tooltip_placement)) {$tooltip_placement = "bottom";}}

    if($style == View::STYLE_INPUT_ICON){
        if(!isset($icon)) {throw new Exception("icon is not set");}else{if(!is_string($icon)) {throw new Exception("icon is not set");}}
        if(!isset($style_icon)) { $style_icon = View::STYLE_ICON_SOLID;}else{if(!in_array($style_icon, [View::STYLE_ICON_REGULAR, VIEW::STYLE_ICON_SOLID, VIEW::STYLE_ICON_MEDIA])) {$style_icon = View::STYLE_ICON_SOLID;}}
        if(!isset($color_icon)) { $color_icon = $color;}else{if(!View::isValidColor($color_icon)) {$color_icon = $color;}}
    }

    $size = "form-control-" . $size;

switch ($style) {
    case View::STYLE_INPUT_ICON:
        $view = new View();
        $icon_visual = $view->dispatch(
            template_name : "icon",
            data : [
                "style" => $style_icon,
                "icon" => $icon,
                "color" => $color_icon
            ], 
            write: false);?>
    
        <div class="size-0-8 text-<?=$color?>">
            <label><?=$label?></label>
            <div class="input-group">
                <div class="input-group-text back-<?=$color?> text-white"><?=$icon_visual?></div>
                <input 
                    id="<?=$id?>"
                    name="<?=$name?>"
                    <?=$data?>
                    style="<?=$css?>"
                    <?php if($is_onchange): ?>
                        onchange="<?=ucfirst($class_name)?>.update('<?=$uniqid;?>', this, '<?=$input_name?>');" 
                    <?php endif; ?>
                    placeholder="<?=$placeholder?>" 
                    maxlength="<?=$maxlenght?>" 
                    type="<?=$type?>" 
                    pattern="<?=$pattern?>"
                    data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>"
                    <?=$required ? "required" : ""?>
                    <?=$disabled ? "disabled" : ""?>
                    <?=$readonly ? "readonly" : ""?>
                    <?=$autocomplete ? "autocomplete" : ""?>
                    class="form-control form-control-<?=View::getColorWithoutShade($color)?>-focus <?=$class?> <?=$size?>" 
                    value="<?=$value?>">
            </div>
        </div> 

    <?php break;

    case View::STYLE_INPUT_FLOATING: ?>
        
        <div>
            <div class="form-floating">
                <input 
                    id="<?=$id?>"
                    <?php if($is_onchange): ?>
                        onchange="<?=ucfirst($class_name)?>.update('<?=$uniqid;?>', this, '<?=$input_name?>');" 
                    <?php endif; ?>
                    placeholder="<?=$placeholder?>" 
                    maxlength="<?=$maxlenght?>" 
                    type="<?=$type?>" 
                    name="<?=$name?>"
                    <?=$data?>
                    style="<?=$css?>"
                    pattern="<?=$pattern?>"
                    data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>"
                    <?=$required ? "required" : ""?>
                    <?=$disabled ? "disabled" : ""?>
                    <?=$readonly ? "readonly" : ""?>
                    <?=$autocomplete ? "autocomplete" : ""?>
                    class="form-control form-control-<?=View::getColorWithoutShade($color)?>-focus <?=$class?> <?=$size?>" 
                    value="<?=$value?>">
                <label for="<?=$id?>" class="size-0-8 text-<?=$color?>"><?=$label?></label>
            </div>
            <?php if(!empty($comment)){ ?>
                <span class="size-0-8 text-grey"><?=$comment?></span>
            <?php } ?>
        </div>

    <?php break;
    
    default: ?>
       
        <div>
            <div>
                <label for="<?=$id?>" class="size-0-8 text-<?=$color?>"><?=$label?></label>
                <input 
                    id="<?=$id?>"
                    <?php if($is_onchange): ?>
                        onchange="<?=ucfirst($class_name)?>.update('<?=$uniqid;?>', this, '<?=$input_name?>');" 
                    <?php endif; ?>
                    placeholder="<?=$placeholder?>" 
                    maxlength="<?=$maxlenght?>" 
                    type="<?=$type?>" 
                    name="<?=$name?>" 
                    <?=$data?>
                    style="<?=$css?>"
                    pattern="<?=$pattern?>"
                    data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>"
                    <?=$required ? "required" : ""?>
                    <?=$disabled ? "disabled" : ""?>
                    <?=$readonly ? "readonly" : ""?>
                    <?=$autocomplete ? "autocomplete" : ""?>
                    class="form-control form-control-<?=View::getColorWithoutShade($color)?>-focus <?=$class?> <?=$size?>" 
                    value="<?=$value?>"> 
            </div>
            <?php if(!empty($comment)){ ?>
                <span class="size-0-8 text-grey"><?=$comment?></span>
            <?php } ?>
        </div>

   <?php break;
}