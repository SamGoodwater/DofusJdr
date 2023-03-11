<?php 

    // Obligatoire
    if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
    if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
    if(!isset($input_name)) {throw new Exception("input_name is not set");}else{if(!is_string($input_name)) {throw new Exception("input_name is not set");}}
    
    // Conseillé
    if(!isset($label)) {$label = "";}else{if(!is_string($label) && !is_numeric($content)) {$label = "";}}
    if(!isset($min)) {$min = 0;}else{if(!is_int($min)) {$min = 0;}}
    if(!isset($max)) {$max = 100;}else{if(!is_int($max)) {$max = 100;}}
    if(!isset($step)) {$step = 1;}else{if(!is_int($step)) {$step = 1;}}
    if(!isset($value)) {$value = 0;}else{if(!is_int($value)) {$value = 0;}}

    // Optionnel - valeur par défault ok
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "textarea_".$uniqid;}else{if(!is_string($id)) {$id = "textarea_".$uniqid;}}
    if(!isset($name)) { $name = "";}else{if(!is_string($name) && !is_numeric($content)) {$name = "";}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment) && !is_numeric($content)) {$comment = "";}}
    if(!isset($disabled)) { $disabled = false;}else{if(!is_bool($disabled)) {$disabled = false;}}
    if(!isset($readonly)) { $readonly = false;}else{if(!is_bool($readonly)) {$readonly = false;}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($content)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}
?>
<div>
    <div>
        <input 
            onchange="<?=ucfirst($class_name)?>.update('<?=$uniqid?>', this, '<?=$input_name?>');"  
            type="range" 
            class="form-range border-<?=$color?> <?=$class?>" 
            min="<?=$min?>"
            max="<?=$max?>"
            step="<?=$step?>"
            <?=$disabled ? "disabled" : ""?>
            <?=$readonly ? "readonly" : ""?>
            value="<?=$value?>"
            name="<?=$name?>"
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
    <?php if(!empty($comment)){ ?>
        <span class="size-0-8 text-grey"><?=$comment?></span>
    <?php } ?>
</div>