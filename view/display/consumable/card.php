<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div class="card mb-3">
    <div class="row g-0">
        <div class="col-auto selector-image-main">
            <a style="position:relative;top:5px;left:5px;" href="<?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_BRUT]))?>" download="<?=$obj->getName().'.'.substr(strrchr($obj->getFile('logo',new Style(['format' => Content::FORMAT_BRUT])),'.'),1);?>"><i class="fa-solid fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
            <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-150"]))?>
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
                            <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Consumable.open('<?=$obj->getUniqid()?>', Controller.DISPLAY_EDITABLE);"><i class='fa-regular fa-edit'></i></a>
                        <?php } ?>
                    </div>                     
                </div>
                <div class="nav-item-divider back-main-d-1"></div>
                <h2 class="card-title"><?=$obj->getName()?></h2>
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