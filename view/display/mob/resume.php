<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div id="<?=$style->getId()?>" class="resume <?=$style->getClass()?>" style="width: <?=$style->getSize()?>px;">
    <div style="position:relative;">
        <div ondblclick="Mob.open('<?=$obj->getUniqid()?>');" class="card-hover-linked card border-secondary-d-2 border p-2 m-1" >
            <div class="d-flex flew-row flex-nowrap justify-content-start">
                <div>
                    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-50"]))?>
                    <div class="d-flex justify-content-around gap-1 mt-1">
                        <?=$obj->getPowerful(Content::FORMAT_ICON)?>
                        <?=$obj->getSize(Content::FORMAT_ICON)?>
                    </div>
                </div>
                <div class="m-1 p-0">
                    <p class="bold ms-1"><?=$obj->getName()?></p>
                    <div class="d-flex flex-wrap justify-content-around align-items-baseline">
                        <p class="mt-1 text-level short-badge-150"><?=$obj->getLevel(Content::FORMAT_BADGE)?></p> 
                        <p class="mt-1 short-badge-100"><?=$obj->getHostility(Content::FORMAT_BADGE)?></p>
                    </div>
                </div>
                <div class="d-flex flex-column justify-content-between ms-auto resume-rapid-menu">
                    <a onclick='User.changeBookmark(this);' data-classe='mob' data-uniqid='<?=$obj->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                    <p class="align-self-end"><a class="btn-text-secondary" title="Afficher les sorts" onclick="Mob.getSpellList('<?=$obj->getUniqid()?>');"><i class="fa-solid fa-magic"></i></a></p>
                </div>
            </div>
            <div class="justify-content-center flex-wrap d-flex short-badge-150"><?=$obj->getTrait(Content::FORMAT_BADGE)?></div>
            <div class="card-hover-showed">
                <div class="d-flex justify-content-around flex-wrap">
                    <div class="col-auto">
                        <div class="truncate-100"><?=$obj->getPa(Content::FORMAT_ICON)?></div>
                        <div class="truncate-100"><?=$obj->getPm(Content::FORMAT_ICON)?></div>
                        <div class="truncate-100"><?=$obj->getPo(Content::FORMAT_ICON)?></div>
                        <div class="truncate-100"><?=$obj->getIni(Content::FORMAT_ICON)?></div>
                        <div class="truncate-100"><?=$obj->getLife(Content::FORMAT_ICON)?></div>
                        <div class="truncate-100"><?=$obj->getTouch(Content::FORMAT_ICON)?></div>
                    </div>
                    <div class="col-auto">
                        <div class="truncate-100"><?=$obj->getVitality(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getSagesse(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getStrong(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getIntel(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getAgi(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getChance(Content::FORMAT_ICON);?></div>
                    </div>
                    <div class="col-auto">
                        <div class="truncate-100"><?=$obj->getCa(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getFuite(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getTacle(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getDodge_pa(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getDodge_pm(Content::FORMAT_ICON);?></div>
                    </div> 
                    <div class="col-auto">
                        <div class="truncate-100"><?=$obj->getRes_neutre(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getRes_terre(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getRes_feu(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getRes_air(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getRes_eau(Content::FORMAT_ICON);?></div>
                    </div> 
                    <div class="col-auto">
                        <div class="truncate-100"><?=$obj->getDo_fixe_neutre(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getDo_fixe_terre(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getDo_fixe_feu(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getDo_fixe_air(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getDo_fixe_eau(Content::FORMAT_ICON);?></div>
                        <div class="truncate-100"><?=$obj->getDo_fixe_multiple(Content::FORMAT_ICON);?></div>
                    </div> 
                </div>
                <button class="p-0 px-2 pt-2 btn btn-text-grey d-flex align-items-baseline w-100 justify-content-between font-size-0-7" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_skill<?=$obj->getUniqid()?>" aria-expanded="false" aria-controls="collapse_skill<?=$obj->getUniqid()?>">
                    <span>Compétences</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div id="collapse_skill<?=$obj->getUniqid()?>" class="row px-1">
                    <div class="col-auto my-1">
                        <p class="m-1"><?=$obj->getAcrobatie(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getDiscretion(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getEscamotage(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getAthletisme(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getIntimidation(Content::FORMAT_ICON)?></p>
                    </div>
                    <div class="col-auto my-1">
                        <p class="m-1"><?=$obj->getArcane(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getHistoire(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getInvestigation(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getNature(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getReligion(Content::FORMAT_ICON)?></p>
                    </div>
                    <div class="col-auto my-1">
                        <p class="m-1"><?=$obj->getDressage(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getMedecine(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getPerception(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getPerspicacite(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getSurvie(Content::FORMAT_ICON)?></p>
                    </div>
                    <div class="col-auto my-1">
                        <p class="m-1"><?=$obj->getPersuasion(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getRepresentation(Content::FORMAT_ICON)?></p>
                        <p class="m-1"><?=$obj->getSupercherie(Content::FORMAT_ICON)?></p>
                    </div>
                </div>
                <?php $spells = $obj->getSpell(Content::DISPLAY_LIST); 
                    if(!empty($spells)){ ?>
                        <p class="text-main-d-2 text-bold mt-3 text-center">Sorts</p>
                        <div><?=$spells?></div>
                <?php } ?>
                <?php $aptitudes = $obj->getCapability(Content::DISPLAY_LIST); 
                if(!empty($aptitudes)){ ?>
                    <p class="text-main-d-2 text-bold mt-3 text-center">Aptitudes</p>
                    <div><?=$aptitudes?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>