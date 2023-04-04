<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div class="card mb-3" id="mob<?=$obj->getUniqid()?>">
    <div class="row g-0">
        <div class="col-auto">
            <a style="position:relative;top:5px;left:5px;" href="<?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_BRUT]))?>" download="<?=$obj->getName().'.'.substr(strrchr($obj->getFile('logo',new Style(['format' => Content::FORMAT_BRUT])),'.'),1);?>"><i class="fas fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
            <?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-200"]))?>
        </div>
        <div class="col">
            <div class="card-body">
                <div class="row justify-content-between">
                    <div class="col-auto">
                        <div><?=$obj->getLevel(Content::FORMAT_LIST)?></div>
                        <div><?=$obj->getLife(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getTouch(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getHostility(Content::FORMAT_BADGE)?></div>
                        <div> <?=$obj->getPowerful(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getTrait(Content::FORMAT_BADGE);?></div>
                    </div>
                    <div class="col-auto">
                        <div><?=$obj->getPa(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getPm(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getPo(Content::FORMAT_BADGE)?></div>
                        <div><?=$obj->getIni(Content::FORMAT_BADGE)?></div>
                    </div>
                    <div class="col-auto">
                        <div><?=$obj->getVitality(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getSagesse(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getStrong(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getIntel(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getAgi(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getChance(Content::FORMAT_BADGE);?></div>
                    </div>
                    <div class="col-auto">
                        <div><?=$obj->getCa(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getFuite(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getTacle(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getDodge_pa(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getDodge_pm(Content::FORMAT_BADGE);?></div>
                    </div> 
                    <div class="col-auto">
                        <div><?=$obj->getRes_neutre(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getRes_terre(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getRes_feu(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getRes_air(Content::FORMAT_BADGE);?></div>
                        <div><?=$obj->getRes_eau(Content::FORMAT_BADGE);?></div>
                    </div>                
                </div>
                <div class="nav-item-divider back-main-d-1"></div>
                <div class="d-flex justify-content-between">
                    <h3><?=$obj->getName()?></h3>
                    <div>
                        <?=$obj->getUsable(Content::FORMAT_BADGE)?>
                        <?php if($user->getRight('mob', User::RIGHT_WRITE)){ ?>
                            <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Mob.open('<?=$obj->getUniqid()?>', Controller.DISPLAY_EDITABLE);"><i class='far fa-edit'></i></a>
                        <?php } ?>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <div class="mx-3">
        <p class="card-text my-2"><?=$obj->getDescription()?></p>
        <p class="card-text my-2"><small class="text-muted"><i class="fas fa-map-marker-alt text-main-d-2 me-2"></i> Zone de vie: <?=$obj->getZone()?></small></p>
        <p class="card-text my-2"><?=$obj->getSpell(Content::DISPLAY_RESUME)?></p>
    </div>
</div>