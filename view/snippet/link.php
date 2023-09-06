<?php 
    // Obligatoire
    if(!isset($content)) {throw new Exception("title is not set");}else{if(!is_string($content)) {throw new Exception("title is not set");}}
    if(!isset($onclick)) {$onclick = "";}else{if(!is_string($onclick)) {$onclick = "";}}
    if(!isset($href)) {$href = "";}else{if(!is_string($href)) {$href = "";}}
    if(!isset($alt)) { $alt = "main";}else{if(!is_string($alt)) {$alt = "main";}}

    // Conseillé
    if(!isset($comment)) {$comment = "";}else{if(!is_string($comment) && !is_numeric($comment)) {$comment="";}}
    if(!isset($target)) {$target = Style::LINK_DEFAULT;}else{if(!in_array($target, [Style::LINK_BLANK, Style::LINK_DEFAULT, Style::LINK_PARENT, Style::LINK_SELF])) {$target=Style::LINK_DEFAULT;}}

    // Optionnel - valeur par défault ok
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($id)) { $id = "";}else{if(!is_string($id)) {$id = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
    if(!isset($tooltip)) { $tooltip = "";}else{if(!is_string($tooltip) && !is_numeric($tooltip)) {$tooltip = "";}}
    if(!isset($tooltip_placement)) { $tooltip_placement = Style::DIRECTION_BOTTOM;}else{if(!in_array($tooltip_placement, [Style::DIRECTION_BOTTOM, Style::DIRECTION_TOP, Style::DIRECTION_RIGHT, Style::DIRECTION_LEFT])) {$tooltip_placement = Style::DIRECTION_BOTTOM;}}

    if(empty($href)  && empty($onclick)){
        throw new Exception("href and onclick can't be set at the same time");
    }
    if($href != ""){$href = 'href="'.$href.'"';}
    if($onclick != ""){$onclick = 'onclick="'.$onclick.'"';}
?>

<a alt="<?=$alt?>" <?=$href?> <?=$onclick?> data-bs-toggle="tooltip" data-bs-placement="<?=$tooltip_placement?>" title="<?=$tooltip?>" <?=$data?> id="<?=$id?>" class="<?=$class?>" style="<?=$css?>"><?=$content?></a>
<?php if(!empty($comment)):?>
    <p class="text-grey-d-2 size-0-8"><?=$comment?></p>
<?php endif;?>