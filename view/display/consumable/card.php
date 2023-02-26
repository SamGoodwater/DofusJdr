<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div class="card mb-3">
    <div class="row g-0">
        <div class="col-auto">
            <a style="position:relative;top:5px;left:5px;" href="<?=$obj->getPath_img()?>" download="<?=$obj->getName().'.'.substr(strrchr($obj->getPath_img(),'.'),1);?>"><i class="fas fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
            <?=$obj->getPath_img(Content::FORMAT_FANCY, "img-back-150")?>
        </div>
        <div class="col">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <p class="d-flex flex-row justify-content-start flex-wrap">
                            <span class="m-1"><?=$obj->getType(Content::FORMAT_BADGE)?></span>
                            <span class="m-1"><?=$obj->getLevel(Content::FORMAT_BADGE)?></span>
                            <span class="m-1"><?=$obj->getPrice(Content::FORMAT_BADGE)?></span>
                            <span class="m-1"><?=$obj->getRarity(Content::FORMAT_BADGE)?></span>
                        </p>
                    </div>
                    <div class="col-auto">
                        <?=$obj->getUsable(Content::FORMAT_BADGE)?>
                        <?php if($user->getRight('consumable', User::RIGHT_WRITE)){ ?>
                            <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Consumable.open('<?=$obj->getUniqid()?>', Controller.DISPLAY_EDITABLE);"><i class='far fa-edit'></i></a>
                        <?php } ?>
                    </div>                     
                </div>
                <div class="nav-item-divider back-main-d-1"></div>
                <h5 class="card-title"><?=$obj->getName()?></h5>
                <p class="card-text"><?=$obj->getEffect()?></p>
            </div>
        </div>
    </div>
    <div>
        <p class="card-text"><small class="text-muted"><?=$obj->getDescription()?></small></p>
        <?php if(!empty($obj->getRecepe())){ ?>
            <div class="nav-item-divider back-main-d-1"></div>
            <p class="card-text"><small class="text-muted"><?=$obj->getRecepe()?></small></p>
        <?php } ?>
    </div>
</div>