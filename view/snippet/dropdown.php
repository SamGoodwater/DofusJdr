<?php 
    $rand_uniqid = uniqid();
    // Obligatoire
    if(!isset($items)) {throw new Exception("items is not set");}else{if(!is_array($items)) {throw new Exception("items is not set");}}
    
    // Conseillé
    if(!isset($label)) {$label = "";}else{if(!is_string($label) && !is_numeric($label)) {$label = "";}}
    if(!isset($id)) { $id = "dropdown_".$rand_uniqid;}else{if(!is_string($id) && !is_numeric($id)) {$id = "dropdown_".$rand_uniqid;}}

    // Optionnel - valeur par défault ok
    if(!isset($is_search)) { $is_search = false;}else{if(!is_bool($is_search)) {$is_search = false;}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($name)) { $name = "";}else{if(!is_string($name)) {$name = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment)) {$comment = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip)&& !is_numeric($tooltip)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}
?>
<div class="d-flex flex-column align-items-center justify-content-center">
    <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>">
        <a class="d-flex align-items-center <?=$class?>" type="button" id="<?=$id?>" style="<?=$css?>" <?=$data?> data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><div><?=$label?></div> <i class="fa-solid fa-chevron-down font-size-0-8 text-grey"></i></a>
        <div class="dropdown-menu" aria-labelledby="<?=$id?>">
            <?php if($is_search){ ?>
                <div class="dropdown__search__container">
                    <i class="fa-solid fa-search font-size-0-8 text-grey"></i>
                    <input class="dropdown__search__container__input" placeholder="Rechercher dans la sélection suivante" type="text">
                </div>
            <?php } ?>
            <?php foreach ($items as $key => $item) { 
                    $href = $onclick = $display = $item_class = $item_css = $item_id = "";
                    if(isset($item["href"])){$href = "href=\"" . $item["href"]. "\"";}
                    if(isset($item["onclick"])){$onclick = "onclick=\"". $item["onclick"]. ";$('#".$id." div').html($(this).html());\"";}
                    if(isset($item["display"])){$display = $item["display"];}
                    if(isset($item["class"])){$item_class = $item["class"];}
                    if(isset($item["css"])){$item_css = $item["css"];}
                    if(isset($item["id"])){$item_id = $item["id"];}?>
                <a class="dropdown-item <?=$item_class?>" id="<?=$item_id?>" <?=$item_css?> <?=$href?> <?=$onclick?>><?=$display?></a>
            <?php } ?>
        </div>
    </div>

    <?php if(!empty($comment)){ ?>
        <span class="size-0-8 text-grey"><?=$comment?></span>
    <?php } ?>
</div>
