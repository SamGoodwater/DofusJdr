<?php 
    // Choix du design
    if(!isset($is_floating)) { $is_floating = true;}else{if(!is_bool($is_floating)) {$is_floating = true;}}

    // Obligatoire
    if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
    if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
    if(!isset($input_name)) {throw new Exception("input_name is not set");}else{if(!is_string($input_name)) {throw new Exception("input_name is not set");}}
    
    // Conseillé
    if(!isset($label)) {$label = "";}else{if(!is_string($label) && !is_numeric($content)) {$label = "";}}
    if(!isset($placeholder)) { $placeholder = "";}else{if(!is_string($placeholder) && !is_numeric($content)) {$placeholder = "";}}
    if(!isset($value)) { $value = "";}else{if(!is_string($value) && !is_numeric($content)) {$value = "";}}

    // Optionnel - valeur par défault ok
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "textarea_".$uniqid;}else{if(!is_string($id)) {$id = "textarea_".$uniqid;}}
    if(!isset($name)) { $name = "";}else{if(!is_string($name) && !is_numeric($content)) {$name = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment) && !is_numeric($content)) {$comment = "";}}
    if(!isset($maxlenght)) { $maxlenght = 1500;}else{if(!is_numeric($maxlenght)) {$maxlenght = 1500;}}
    if(!isset($required)) { $required = false;}else{if(!is_bool($required)) {$required = false;}}
    if(!isset($disabled)) { $disabled = false;}else{if(!is_bool($disabled)) {$disabled = false;}}
    if(!isset($readonly)) { $readonly = false;}else{if(!is_bool($readonly)) {$readonly = false;}}
    if(!isset($autocomplete)) { $autocomplete = false;}else{if(!is_bool($autocomplete)) {$autocomplete = false;}}
    if(!isset($pattern)) { $pattern = "";}else{if(!is_string($pattern)) {$pattern = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($content)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}
?>

<div>
    <?php if($is_floating){ ?>

        <div class="form-floating">
            <textarea 
                onchange="<?=ucfirst($class_name)?>.update('<?=$uniqid;?>', this, '<?=$input_name?>');" 
                class="form-control form-control-<?=Style::getColorWithoutShade($color)?>-focus <?=$class?>" 
                name="<?=$name?>"
                placeholder="<?=$placeholder?>" 
                maxlength="<?=$maxlenght?>"
                pattern="<?=$pattern?>"
                data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>"
                <?=$required ? "required" : ""?>
                <?=$disabled ? "disabled" : ""?>
                <?=$readonly ? "readonly" : ""?>
                <?=$autocomplete ? "autocomplete" : ""?>
                <?=$data?>
                style="<?=$css?>"
                id="<?=$id?>">
                <?=$value?>
            </textarea>
            <label for="<?=$id?>" class="size-0-8 text-<?=$color?>-d-2"><?=$label?></label>
        </div> 
        
    <?php } else { ?>
        
            <div class="form-group">
                <label for="<?=$id?>" class="size-0-8 text-<?=$color?>-d-2"><?=$label?></label>
                <textarea 
                    onchange="<?=ucfirst($class_name)?>.update('<?=$uniqid;?>', this, '<?=$input_name?>');" 
                    class="form-control form-control-<?=Style::getColorWithoutShade($color)?>-focus <?=$class?>" 
                    placeholder="<?=$placeholder?>" 
                    maxlength="<?=$maxlenght?>"
                    pattern="<?=$pattern?>"
                    name="<?=$name?>"
                    <?=$required ? "required" : ""?>
                    <?=$disabled ? "disabled" : ""?>
                    <?=$readonly ? "readonly" : ""?>
                    <?=$autocomplete ? "autocomplete" : ""?>
                    id="<?=$id?>">
                    <?=$value?>
                </textarea>
            </div>
        
    <?php } ?>

    <?php if(!empty($comment)){ ?>
        <span class="size-0-8 text-grey"><?=$comment?></span>
    <?php } ?>
</div>