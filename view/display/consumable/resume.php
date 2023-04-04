<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div style="width: <?=$size?>px;">
    <div style="position:relative;">
        <div ondblclick="Consumable.open('<?=$obj->getUniqid()?>');" class="card-hover-linked card border-secondary-d-2 border p-2 m-1" style="width: <?=$size?>px;" >
            <div class="row">
                <div class="col-auto">
                    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-50"]))?>
                </div>
                <div class="col">
                    <p class="bold ms-1"><?=$obj->getName()?></p>
                    <p class="row">
                        <span class="col-auto me-1 mt-1 short-badge-150"><?=$obj->getType(Content::FORMAT_BADGE)?></span>
                        <span class="col-auto me-1 mt-1 short-badge-150"><?=$obj->getLevel(Content::FORMAT_BADGE)?></span>
                        <span class="col-auto me-1 mt-1 short-badge-150"><?=$obj->getPrice(Content::FORMAT_BADGE)?></span>
                        <span class="col-auto me-1 mt-1 short-badge-150"><?=$obj->getRarity(Content::FORMAT_BADGE)?></span>
                    </p>
                </div>
                <div class="col-auto d-flex flex-column justify-content-between ms-auto">
                    <a onclick='User.changeBookmark(this);' data-classe='consumable' data-uniqid='<?=$obj->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                </div>
            </div>
            <div class="card-hover-showed">
                <p class="card-text"><?=$obj->getEffect()?></p>
                <div class="nav-item-divider back-main-d-1"></div>
                <p class="card-text"><small class="size-0-9 text-secondary-d-3"><?=$obj->getDescription()?></small></p>
                <p class="card-text"><small class="text-muted size-0-7 text-grey-d-2"><?=$obj->getRecepe()?></small></p>
            </div>
        </div>
    </div>
</div>