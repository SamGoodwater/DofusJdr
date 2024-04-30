<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div class="card p-2 m-2 border-2 border-main">
    <div class="row justify-content-between align-items-center mb-1">
        <div class="col-auto selector-image-main">      
            <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-40"]))?>
        </div>
        <div class="col-auto">
            <?=$obj->getSuper_race(Content::FORMAT_BADGE)?>
        </div>
        <div class="col-auto row justify-content-between">
            <div class="col-auto">
                <?=$obj->getUsable(Content::FORMAT_BADGE)?>
                <?php if($user->getRight('mob_race', User::RIGHT_WRITE)){ ?>
                    <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Mob_race.open('<?=$obj->getUniqid()?>', Controller.DISPLAY_EDITABLE);"><i class='fa-regular fa-edit'></i></a>
                <?php } ?>
            </div>                      
        </div>
    </div>
    <div>
        <div class="nav-item-divider back-main-d-2"></div>
        <h2 class="card-title"><?=$obj->getName()?></h2>
    </div>
</div>