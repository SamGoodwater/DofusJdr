<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div class="card p-2 m-2 border-2 border-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>">
    <div class="row g-0">
        <div class="col-md-2 selector-image-main">
            <a style="position:relative;top:5px;left:5px;" href="<?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_BRUT]))?>" download="<?=$obj->getName().'.'.substr(strrchr($obj->getFile('logo',new Style(['format' => Content::FORMAT_BRUT])),'.'),1);?>"><i class="fa-solid fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
            <?= $obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-120"]));?>
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
                        <div><?=$obj->getFrequency(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getSight_line(Content::FORMAT_BADGE)?></div>
                    </div>
                    <div class="col-auto">
                        <?=$obj->getArea(Content::FORMAT_BADGE)?>
                    </div>
                    <div class="col-auto">
                        <?=$obj->getUsable(Content::FORMAT_BADGE)?>
                        <?php if($user->getRight('spell', User::RIGHT_WRITE)){ ?>
                            <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Spell.open('<?=$obj->getUniqid()?>', Controller.DISPLAY_EDITABLE);"><i class='fa-regular fa-edit'></i></a>
                        <?php } ?>
                    </div>                      
                </div>
            </div>
        </div>E
    </div>
    <div>
        <div class="nav-item-divider back-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>"></div>
        <h2 class="card-title"><?=$obj->getName()?></h2>
        <p class="card-text"><?=$obj->getEffect()?></p>
        <!-- <p class="card-text"><?php //$obj->getEffect_array(Content::FORMAT_VIEW)?></p> -->
        <p class="card-text"><small class="text-muted"><?=$obj->getDescription()?></small></p>
        <div class="nav-item-divider back-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>"></div>
        <div class="d-flex justify-content-center"><?=$obj->getInvocation(Content::DISPLAY_RESUME)?></div>
    </div>
</div>