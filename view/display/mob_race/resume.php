<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div id="<?=$style->getId()?>" class="resume <?=$style->getClass()?>" style="position:relative;width: <?=$style->getSize()?>px;">
    <div ondblclick="Mob_race.open('<?=$obj->getUniqid()?>');" class="card-hover-linked card p-2 m-1" >
        <div class="d-flex flew-row flex-nowrap">
            <div class="m-1">
                <div class="d-flex flex-row justify-content-between ">
                    <div class="col-auto">
                        <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-30"]))?>
                    </div>
                    <div class="col-auto">
                        <p class="bold"><?=$obj->getName()?></p>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column justify-content-between ms-auto resume-rapid-menu">
                <a onclick='User.changeBookmark(this);' data-classe='condition' data-uniqid='<?=$obj->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
            </div>
        </div>
        <div class="card-hover-showed">
            <div class="d-flex flex-row justify-content-around align-items-baseline flex-wrap">
                <div class="me-1 mb-1"><?=$obj->getSuper_race(Content::FORMAT_ICON)?></div>
            </div>
        </div>
    </div>
</div>