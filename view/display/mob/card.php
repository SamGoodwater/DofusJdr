<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div class="card mb-3" id="mob<?=$obj->getUniqid()?>">
    <div class="d-flex flex-row justify-content-between align-items-center m-2">
        <div class="selector-image-main"><?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-120"]));?></div>
        <div class="d-flex justify-content-between align-items-baseline gap-2">
            <?=$obj->getLevel(Content::FORMAT_LIST)?>
            <?=$obj->getPowerful(Content::FORMAT_BADGE);?>
            <?=$obj->getSize(Content::FORMAT_BADGE);?>
            <?=$obj->getHostility(Content::FORMAT_BADGE);?>
            <?=$obj->getTrait(Content::FORMAT_BADGE);?>
        </div>
        <div class="m-2 m-2 align-self-start">
            <?php if($user->getRight('mob', User::RIGHT_WRITE)){ ?>
                <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="Mob.open('<?=$obj->getUniqid()?>', Controller.DISPLAY_EDITABLE);"><i class='fa-regular fa-edit'></i></a>
            <?php } ?>
            <p><a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=<?=$obj->getUniqid()?>'><i class='fa-solid fa-file-pdf'></i> Générer un pdf</a></p>
        </div>
    </div>
    <div class="card-body">
        <div class="nav-item-divider back-main-l-3 m-0"></div>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getLife(Content::FORMAT_VIEW)?>
            <?=$obj->getPa(Content::FORMAT_VIEW)?>
            <?=$obj->getPm(Content::FORMAT_VIEW)?>
            <?=$obj->getPo(Content::FORMAT_VIEW)?>
            <?=$obj->getIni(Content::FORMAT_VIEW)?>
            <?=$obj->getMaster_bonus(Content::FORMAT_VIEW)?>
        </div>
        <h4 class="text-main-d-1 text-center">Caractéristiques</h4>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getVitality(Content::FORMAT_VIEW)?>
            <?=$obj->getSagesse(Content::FORMAT_VIEW)?>
            <?=$obj->getStrong(Content::FORMAT_VIEW)?>
            <?=$obj->getIntel(Content::FORMAT_VIEW)?>
            <?=$obj->getAgi(Content::FORMAT_VIEW)?>
            <?=$obj->getChance(Content::FORMAT_VIEW)?>
        </div>
        <h4 class="text-main-d-1 text-center">Dommages</h4>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getTouch(Content::FORMAT_VIEW)?>
            <?=$obj->getDo_fixe_neutre(Content::FORMAT_VIEW)?>
            <?=$obj->getDo_fixe_terre(Content::FORMAT_VIEW)?>
            <?=$obj->getDo_fixe_feu(Content::FORMAT_VIEW)?>
            <?=$obj->getDo_fixe_air(Content::FORMAT_VIEW)?>
            <?=$obj->getDo_fixe_eau(Content::FORMAT_VIEW)?>
            <?=$obj->getDo_fixe_multiple(Content::FORMAT_VIEW)?>
        </div>
        <h4 class="text-main-d-1 text-center">Protection</h4>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getCa(Content::FORMAT_VIEW)?>
            <?=$obj->getDodge_pa(Content::FORMAT_VIEW)?>
            <?=$obj->getDodge_pm(Content::FORMAT_VIEW)?>
            <?=$obj->getFuite(Content::FORMAT_VIEW)?>
            <?=$obj->getTacle(Content::FORMAT_VIEW)?>
        </div>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getRes_neutre(Content::FORMAT_VIEW)?>
            <?=$obj->getRes_terre(Content::FORMAT_VIEW)?>
            <?=$obj->getRes_feu(Content::FORMAT_VIEW)?>
            <?=$obj->getRes_air(Content::FORMAT_VIEW)?>
            <?=$obj->getRes_eau(Content::FORMAT_VIEW)?>
        </div>
        <div class="nav-item-divider back-main-l-3 m-0"></div>
        <h4 class="text-main-d-1 text-center">Compétences</h4>
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div class="col-auto my-2">
                <p class="text-agi mb-2">Dépendant de l'agilité
                <?=$obj->getAcrobatie(Content::FORMAT_VIEW)?>
                <?=$obj->getDiscretion(Content::FORMAT_VIEW)?>
                <?=$obj->getEscamotage(Content::FORMAT_VIEW)?>
            </div>
            <div class="col-auto my-2">
            <p class="text-force mb-2">Dépendant de la Force
                <?=$obj->getAthletisme(Content::FORMAT_VIEW)?>
                <?=$obj->getIntimidation(Content::FORMAT_VIEW)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-intel mb-2">Dépendant de l'Intelligence
                <?=$obj->getArcane(Content::FORMAT_VIEW)?>
                <?=$obj->getHistoire(Content::FORMAT_VIEW)?>
                <?=$obj->getInvestigation(Content::FORMAT_VIEW)?>
                <?=$obj->getNature(Content::FORMAT_VIEW)?>
                <?=$obj->getReligion(Content::FORMAT_VIEW)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-sagesse mb-2">Dépendant de la Sagesse
                <?=$obj->getDressage(Content::FORMAT_VIEW)?>
                <?=$obj->getMedecine(Content::FORMAT_VIEW)?>
                <?=$obj->getPerception(Content::FORMAT_VIEW)?>
                <?=$obj->getPerspicacite(Content::FORMAT_VIEW)?>
                <?=$obj->getSurvie(Content::FORMAT_VIEW)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-chance mb-2">Dépendant de la Chance
                <?=$obj->getPersuasion(Content::FORMAT_VIEW)?>
                <?=$obj->getRepresentation(Content::FORMAT_VIEW)?>
                <?=$obj->getSupercherie(Content::FORMAT_VIEW)?>
            </div>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Informations</h4>
        <p><?=$obj->getDescription();?></p>
        <p class="card-text my-2"><small class="text-muted"><i class="fa-solid fa-map-marker-alt text-main-d-2 me-2"></i> Zone de vie: <?=$obj->getLocation()?></small></p>
        <p><?=$obj->getOther_info();?></p>
        <div class="d-flex flex-row justify-content-between">
            <?=$obj->getDrop_()?>
            <?=$obj->getKamas()?>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Sorts</h4>
        <div class="dy-2 px-1"><?=$obj->getSpell(Content::DISPLAY_RESUME);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_spell();?></div>
        <h4 class="pt-2 text-center">Aptitudes</h4>
        <div class="dy-2 px-1"><?=$obj->getCapability(Content::DISPLAY_RESUME);?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Équipement</h4>
        <div class="dy-2 px-1"><?=$obj->getItem();?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_item();?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Consommables</h4>
        <div class="dy-2 px-1"><?=$obj->getConsumable();?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_consumable();?></div>
    </div>
</div>