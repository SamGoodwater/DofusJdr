<?php 
    // Choix du design
    if(!isset($style)){ $style = Style::CHECK_SWITCH;}else{if(!in_array($style, [Style::CHECK_CHECKBOX, Style::CHECK_SWITCH, Style::CHECK_RADIO])) {$style = Style::CHECK_SWITCH;}};
    if(!isset($is_inline)) { $is_inline = true;}else{if(!is_bool($is_inline)) {$is_inline = true;}}

    // Obligatoire
    if(isset($onchange)){
        if(is_string($onchange)){
            $onchange = $onchange;
        } else {
            throw new Exception("onchange is not set");
        }
    } else {
        if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
        if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
        if(!isset($input_name)) {throw new Exception("input_name is not set");}else{if(!is_string($input_name)) {throw new Exception("input_name is not set");}}
        $onchange = ucfirst($class_name).".update('".$uniqid."', this, '".$input_name."', ".Controller::IS_CHECKBOX.");";
    }
    
    // Conseillé
    if(!isset($checked)) { $checked = false;}else{if(!is_bool($checked)) {$checked = false;}}
    if(!isset($label)) {$label = "";}else{if(!is_string($label) && !is_numeric($content)) {$label = "";}}
    if(!isset($value)) {$value = "";}else{if(!is_string($value) && !is_numeric($content)) {$value = "";}}
    if(!isset($name)) { $name = "";}else{if(!is_string($name) && !is_numeric($content)) {$name = "";}}

    // Optionnel - valeur par défault ok
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "textarea_".$uniqid;}else{if(!is_string($id)) {$id = "textarea_".$uniqid;}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment) && !is_numeric($content)) {$comment = "";}}
    if(!isset($disabled)) { $disabled = false;}else{if(!is_bool($disabled)) {$disabled = false;}}
    if(!isset($readonly)) { $readonly = false;}else{if(!is_bool($readonly)) {$readonly = false;}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($content)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}

    if($is_inline){$inline = "form-check-inline";}else{$inline = "";} ?>

<div class="<?=$inline?>"> 

    <?php switch ($style) {
        case Style::CHECK_SWITCH:?>
            
            <div style="width:initial;" class="form-check form-switch my-1">
                <input 
                    onchange="<?=$onchange?>"
                    type="checkbox" 
                    class="form-check-input back-<?=$color?>-d-1 border-<?=$color?>-d-1 <?=$class?>" 
                    <?=$disabled ? "disabled" : ""?>
                    <?=$readonly ? "readonly" : ""?>
                    <?=$checked ? "checked" : ""?>
                    name="<?=$name?>"
                    value="<?=$value?>"
                    data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>"
                    <?=$data?>
                    style="<?=$css?>"
                    id="<?=$id?>">
                <label 
                    class="custom-control-label" 
                    for="<?=$id?>">
                        <?=$label?>
                </label>
            </div>

        <?php break;

        case Style::CHECK_RADIO: ?>

            <div class="form-check my-1">
                <input 
                    onchange="<?=$onchange?>"
                    type="radio" 
                    name="<?=$name?>"
                    value="<?=$value?>"
                    class="form-check-input back-<?=$color?>-d-1 border-<?=$color?>-d-1 <?=$class?>" 
                    <?=$disabled ? "disabled" : ""?>
                    <?=$readonly ? "readonly" : ""?>
                    <?=$checked ? "checked" : ""?>
                    <?=$data?>
                    style="<?=$css?>"
                    id="<?=$id?>">
                <label 
                    class="custom-control-label" 
                    for="<?=$id?>">
                        <?=$label?>
                </label>
            </div>

        <?php break;
        
        default: ?>
            
            <div class="form-check my-1">
                <input 
                    onchange="<?=$onchange?>"  
                    type="checkbox" 
                    name="<?=$name?>"
                    value="<?=$value?>"
                    class="form-check-input back-<?=$color?>-d-1 border-<?=$color?>-d-1 <?=$class?>" 
                    <?=$disabled ? "disabled" : ""?>
                    <?=$readonly ? "readonly" : ""?>
                    <?=$checked ? "checked" : ""?>
                    <?=$data?>
                    style="<?=$css?>"
                    id="<?=$id?>">
                <label 
                    class="custom-control-label" 
                    for="<?=$id?>">
                        <?=$label?>
                </label>
            </div>

        <?php break;
    } ?>

    <?php if(!empty($comment)){ ?>
        <span class="size-0-8 text-grey"><?=$comment?></span>
    <?php } ?>

</div>