<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div class="card p-2 m-2 border-2 <?=$obj->getElement(Content::FORMAT_COLOR_VERBALE, "border")?>">
    <div class="row g-0">
        <div class="col-md-2">
            <a style="position:relative;top:5px;left:5px;" href="<?=$obj->getPath_img()?>" download="<?=$obj->getName().'.'.substr(strrchr($obj->getPath_img(),'.'),1);?>"><i class="fas fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
            <?=$obj->getPath_img(Content::FORMAT_FANCY, "img-back-120")?>
        </div>
        <div class="col-md-10">
            <div class="card-body">
                <div class="d-flex flex-wrap mb-2">
                    <div class="mx-2"><?=$obj->getLevel(Content::FORMAT_BADGE)?></div>
                    <div class="mx-2"><?=$obj->getIs_magic(Content::FORMAT_BADGE)?></div>
                    <div class="mx-2"><?=$obj->getCategory(Content::FORMAT_BADGE)?></div>
                    <div class="mx-2"><?=$obj->getPowerful(Content::FORMAT_BADGE)?></div>
                    <div class="mx-2"><?=$obj->getElement(Content::FORMAT_BADGE)?></div>
                    <div class="mx-2"><?=$obj->getType(Content::FORMAT_BADGE)?></div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-auto">
                        <div><?=$obj->getPa(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getPo_editable(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getPo(Content::FORMAT_BADGE)?></div>
                    </div>
                    <div class="col-auto">
                        <div><?=$obj->getCast_per_turn(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getSight_line(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getNumber_between_two_cast(Content::FORMAT_BADGE)?></div>
                    </div>
                    <div class="col-auto">
                        <?=$obj->getUsable(Content::FORMAT_BADGE)?>
                        <?php if($user->getRight('spell', User::RIGHT_WRITE)){ ?>
                            <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Spell.open('<?=$obj->getUniqid()?>', Controller.DISPLAY_EDITABLE);"><i class='far fa-edit'></i></a>
                        <?php } ?>
                    </div>                      
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="nav-item-divider <?=$obj->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
        <h5 class="card-title"><?=$obj->getName()?></h5>
        <p class="card-text"><?=$obj->getEffect()?></p>
        <p class="card-text"><small class="text-muted"><?=$obj->getDescription()?></small></p>
        <div class="nav-item-divider <?=$obj->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
        <div class="d-flex justify-content-center"><?=$obj->getId_invocation(Content::DISPLAY_RESUME)?></div>
    </div>
</div>