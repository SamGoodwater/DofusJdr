<?php 
    // Choix du design
    if(!isset($style)){ $style = View::STYLE_CHECK_SWITCH;}else{if(!in_array($style, [View::STYLE_CHECK_CHECKBOX, View::STYLE_CHECK_SWITCH, View::STYLE_CHECK_RADIO])) {$style = View::STYLE_CHECK_SWITCH;}};
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
        $onchange = ucfirst($class_name)."update('".$uniqid."', this, '".$input_name."', ".Controller::IS_CHECKBOX.");";
    }
    
    // Conseillé
    if(!isset($checked)) { $checked = false;}else{if(!is_bool($checked)) {$checked = false;}}
    if(!isset($label)) {$label = "";}else{if(!is_string($label)) {$label = "";}}
    if(!isset($value)) {$value = "";}else{if(!is_string($value)) {$value = "";}}
    if(!isset($name)) { $name = "";}else{if(!is_string($name)) {$name = "";}}

    // Optionnel - valeur par défault ok
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($color)) { $color = "main";}else{if(!View::isValidColor($color)) {$color = "main";}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "textarea_".$uniqid;}else{if(!is_string($id)) {$id = "textarea_".$uniqid;}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment)) {$comment = "";}}
    if(!isset($disabled)) { $disabled = false;}else{if(!is_bool($disabled)) {$disabled = false;}}
    if(!isset($readonly)) { $readonly = false;}else{if(!is_bool($readonly)) {$readonly = false;}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = "bottom";}else{if(!is_string($tooltip_placement)) {$tooltip_placement = "bottom";}}

    if($is_inline){$inline = "form-check-inline";}else{$inline = "";} ?>

<div class="<?=$inline?>"> 

    <?php switch ($style) {
        case View::STYLE_CHECK_SWITCH:?>
            
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

        case View::STYLE_CHECK_RADIO: ?>

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