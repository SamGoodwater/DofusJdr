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
            <?=$obj->getPath_img(Content::FORMAT_FANCY, "img-back-200")?>
        </div>
        <div class="col">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <?=$obj->getTrait(Content::FORMAT_BADGE)?>
                        <p class="text-main-l-2 size-0-8">Arme priviligiée :</p>
                        <?=$obj->getWeapons_of_choice(Content::FORMAT_BADGE)?>
                    </div>
                    <div class="col-auto ms-auto">
                        <?=$obj->getUsable(Content::FORMAT_BADGE)?>
                        <?php if($user->getRight('classe', User::RIGHT_WRITE)){ ?>
                            <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Classe.open('<?=$obj->getUniqid()?>', Controller.DISPLAY_EDITABLE);"><i class='far fa-edit'></i></a>
                        <?php } ?>
                    </div>                      
                </div>
                <div class="nav-item-divider back-main-d-1"></div>
                <h5 class="card-title"><?=$obj->getName()?></h5>
                <p class="card-text"><small class="text-muted"><?=$obj->getDescription_fast()?></small></p>
                <p class="card-text"><?=$obj->getDescription()?></p>
            </div>
        </div>
    </div>
    <div class="row g-0 mx-4">
        <p class="text-main-d-2 size-1-2 text-bold mt-4 mb-2">Spécificités de la classe</p>
        <p class="card-text"><?=$obj->getSpecificity()?></p>
        <p class="text-main-d-2 size-1-2 text-bold mt-4 mb-2">Gestion des points de vie</p>
        <p class="card-text"><?=$obj->getLife()?></p>
        <div class="nav-item-divider back-main-d-1"></div>
        <p class="card-text"><?=$obj->getSpell(Content::DISPLAY_RESUME)?></p>
    </div>
</div>