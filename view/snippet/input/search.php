<?php 
    $uniqid = uniqid();

    // Obligatoire
    if(!isset($action)) {throw new Exception("action is not set");}else{if(!is_numeric($action)) {throw new Exception("action is not set");}}
    if(!isset($parameter)) {throw new Exception("parameter is not set");}
    if(!isset($search_in)) {throw new Exception("search_in is not set");}else{if(!is_numeric($search_in)) {throw new Exception("search_in is not set");}}
    
    // Conseillé
    if(!isset($label)) {$label = "";}else{if(!is_string($label) && !is_numeric($content)) {$label = "";}}
    if(!isset($placeholder)) { $placeholder = "Rechercher";}else{if(!is_string($placeholder) && !is_numeric($content)) {$placeholder = "Rechercher";}}
    if(!isset($id)) { $id = "search_".$uniqid;}else{if(!is_string($id)) {$id = "search_".$uniqid;}}

    // Optionnel - valeur par défault ok
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($minlenght)) {$minlenght = 3;}else{if(!is_numeric($minlenght)) {$minlenght = 3;}}
    if(!isset($limit)) {$limit = 10;}else{if(!is_numeric($limit)) {$limit = 10;}}
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($title)) { $title = "";}else{if(!is_string($title) && !is_numeric($content)) {$title = "";}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment) && !is_numeric($content)) {$comment = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($content)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}
?>

<div>
    <?php if(!empty($title)){ ?>
        <h6><?=$title?></h6>
    <?php } ?>
    <div class="d-flex flex-row justify-content-evenly align-items-baseline mb-3 position-relative">
        <div class="form-floating w-100">
            <input  type="text"
                    data-url = "index.php?c=search&a=search"
                    data-search_in = <?=$search_in?>
                    data-minlenght = <?=$minlenght?>
                    data-parameter = "<?=$parameter?>"
                    data-action = <?=$action?>
                    data-limit = <?=$limit?>
                    data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>"
                    class="form-control form-control-<?=Style::getColorWithoutShade($color)?>-focus <?=$class?>" 
                    id="<?=$id?>" 
                    <?=$data?>
                    style="<?=$css?>"
                    placeholder="<?=$placeholder?>">
            <label for="<?=$id?>"><?=$label?></label>
        </div>
        <span id="search-sign"></span>
    </div>
    <script>autocomplete_load("#<?=$id?>");</script>
    <?php if(!empty($comment)){ ?>
        <span class="size-0-8 text-grey"><?=$comment?></span>
    <?php } ?>
</div>
