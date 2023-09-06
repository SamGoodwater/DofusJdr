<?php 
    // Obligatoire
    if(isset($is_onchange)) {if(!is_bool($is_onchange)) {$is_onchange = true;}}else{$is_onchange = true;}
    if($is_onchange){
        if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
        if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
        if(!isset($input_name)) {throw new Exception("input_name is not set");}else{if(!is_string($input_name)) {throw new Exception("input_name is not set");}}
    }

    // Conseillé
    if(!isset($label)) {$label = "";}else{if(!is_string($label) && !is_numeric($content)) {$label = "";}}
    if(!isset($value)) { $value = "";}else{if(!is_string($value) && !is_numeric($value)) {$value = "";}}

    // Optionnel - valeur par défault ok
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "";}else{if(!is_string($id)) {$id = "";}}
    if(!isset($name)) { $name = "";}else{if(!is_string($name) && !is_numeric($content)) {$name = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment) && !is_numeric($content)) {$comment = "";}}
    if(!isset($size)) { $size = Style::SIZE_BASIC;}else{if(!in_array($size, [Style::SIZE_SM, Style::SIZE_MD, Style::SIZE_BASIC, Style::SIZE_LG, Style::SIZE_XL])) {$size = Style::SIZE_BASIC;}}
    if(!isset($required)) { $required = false;}else{if(!is_bool($required)) {$required = false;}}
    if(!isset($disabled)) { $disabled = false;}else{if(!is_bool($disabled)) {$disabled = false;}}
    if(!isset($readonly)) { $readonly = false;}else{if(!is_bool($readonly)) {$readonly = false;}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($content)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}

    $size = "form-control-" . $size;

?>
        <div>
            <div>
                <label for="<?=$id?>" class="form-label size-0-8 text-<?=$color?>"><?=$label?></label>
                <input 
                    id="<?=$id?>"
                    <?php if($is_onchange): ?>
                        onchange="<?=ucfirst($class_name)?>.update('<?=$uniqid;?>', this, '<?=$input_name?>');" 
                    <?php endif; ?>
                    type="color" 
                    class="form-control form-control-<?=Style::getColorWithoutShade($color)?>-focus <?=$class?> <?=$size?>" 
                    value="<?=$value?>" 
                    title="Choose your color"
                    name="<?=$name?>" 
                    <?=$data?>
                    style="<?=$css?>"
                    data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>"
                    <?=$required ? "required" : ""?>
                    <?=$disabled ? "disabled" : ""?>
                    <?=$readonly ? "readonly" : ""?>>
            </div>
            <?php if(!empty($comment)){ ?>
                <span class="size-0-8 text-grey"><?=$comment?></span>
            <?php } ?>
        </div>
