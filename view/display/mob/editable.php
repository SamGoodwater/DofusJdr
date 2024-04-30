<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div class="card mb-3" id="mob<?=$obj->getUniqid()?>">
    <div class="d-flex flex-row justify-content-between align-items-start m-2">
        <div class="selector-image-main"><?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_EDITABLE, "class" => "img-back-200"]))?></div>
        <div>
            <div class="d-flex justify-content-between align-items-baseline">
                <div class="col-auto gap-2">
                    <h4><?=$obj->getName(Content::FORMAT_EDITABLE)?></h4>
                    <p class="text-center"><?=$obj->getLevel(Content::FORMAT_EDITABLE)?></p>
                </div>
                <div class="col-auto gap-2">
                    <?=$obj->getPowerful(Content::FORMAT_BADGE);?>
                    <?=$obj->getSize(Content::FORMAT_EDITABLE);?>
                </div>
                <div class="col-auto gap-2">
                    <?=$obj->getHostility(Content::FORMAT_EDITABLE);?>
                    <?=$obj->getRace(Content::FORMAT_EDITABLE);?>
                </div>
            </div>
        </div>
        <?=$obj->getTrait(Content::FORMAT_EDITABLE);?>
    </div>
    <div class="card-body">
        <div class="nav-item-divider back-main-l-3 m-0"></div>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getLife(Content::FORMAT_EDITABLE)?>
            <?=$obj->getPa(Content::FORMAT_EDITABLE)?>
            <?=$obj->getPm(Content::FORMAT_EDITABLE)?>
            <?=$obj->getPo(Content::FORMAT_EDITABLE)?>
            <?=$obj->getIni(Content::FORMAT_EDITABLE)?>
            <?=$obj->getMaster_bonus(Content::FORMAT_EDITABLE)?>
        </div>
        <h4 class="text-main-d-1 text-center">Caractéristiques</h4>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getVitality(Content::FORMAT_EDITABLE)?>
            <?=$obj->getSagesse(Content::FORMAT_EDITABLE)?>
            <?=$obj->getStrong(Content::FORMAT_EDITABLE)?>
            <?=$obj->getIntel(Content::FORMAT_EDITABLE)?>
            <?=$obj->getAgi(Content::FORMAT_EDITABLE)?>
            <?=$obj->getChance(Content::FORMAT_EDITABLE)?>
        </div>
        <h4 class="text-main-d-1 text-center">Dommages</h4>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getTouch(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_neutre(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_terre(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_feu(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_air(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_eau(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_multiple(Content::FORMAT_EDITABLE)?>
        </div>
        <h4 class="text-main-d-1 text-center">Protection</h4>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getCa(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDodge_pa(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDodge_pm(Content::FORMAT_EDITABLE)?>
            <?=$obj->getFuite(Content::FORMAT_EDITABLE)?>
            <?=$obj->getTacle(Content::FORMAT_EDITABLE)?>
        </div>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getRes_neutre(Content::FORMAT_EDITABLE)?>
            <?=$obj->getRes_terre(Content::FORMAT_EDITABLE)?>
            <?=$obj->getRes_feu(Content::FORMAT_EDITABLE)?>
            <?=$obj->getRes_air(Content::FORMAT_EDITABLE)?>
            <?=$obj->getRes_eau(Content::FORMAT_EDITABLE)?>
        </div>
        <div class="nav-item-divider back-main-l-3 m-0"></div>
        <h4 class="text-main-d-1 text-center">Compétences</h4>
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div class="col-auto my-2">
                <p class="text-agi mb-2">Dépendant de l'agilité
                <?=$obj->getAcrobatie(Content::FORMAT_EDITABLE)?>
                <?=$obj->getDiscretion(Content::FORMAT_EDITABLE)?>
                <?=$obj->getEscamotage(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
            <p class="text-force mb-2">Dépendant de la Force
                <?=$obj->getAthletisme(Content::FORMAT_EDITABLE)?>
                <?=$obj->getIntimidation(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-intel mb-2">Dépendant de l'Intelligence
                <?=$obj->getArcane(Content::FORMAT_EDITABLE)?>
                <?=$obj->getHistoire(Content::FORMAT_EDITABLE)?>
                <?=$obj->getInvestigation(Content::FORMAT_EDITABLE)?>
                <?=$obj->getNature(Content::FORMAT_EDITABLE)?>
                <?=$obj->getReligion(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-sagesse mb-2">Dépendant de la Sagesse
                <?=$obj->getDressage(Content::FORMAT_EDITABLE)?>
                <?=$obj->getMedecine(Content::FORMAT_EDITABLE)?>
                <?=$obj->getPerception(Content::FORMAT_EDITABLE)?>
                <?=$obj->getPerspicacite(Content::FORMAT_EDITABLE)?>
                <?=$obj->getSurvie(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-chance mb-2">Dépendant de la Chance
                <?=$obj->getPersuasion(Content::FORMAT_EDITABLE)?>
                <?=$obj->getRepresentation(Content::FORMAT_EDITABLE)?>
                <?=$obj->getSupercherie(Content::FORMAT_EDITABLE)?>
            </div>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <p class='size-0-7 mb-1'>Mob <?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$obj->getTimestamp_updated(Content::DATE_FR);?></p>
        <h4 class="text-main-d-1 text-center">Informations</h4>
        <p><?=$obj->getDescription(Content::FORMAT_EDITABLE);?></p>
        <p><?=$obj->getOther_info(Content::FORMAT_EDITABLE);?></p>
        <div class="d-flex flex-row justify-content-between">
            <?=$obj->getDrop_(Content::FORMAT_EDITABLE)?>
            <?=$obj->getKamas(Content::FORMAT_EDITABLE)?>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Sorts</h4>
        <div class="dy-2 px-1"><?=$obj->getSpell(Content::FORMAT_EDITABLE);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_spell(Content::FORMAT_EDITABLE);?></div>
        <h4 class="pt-2 text-center">Aptitudes</h4>
        <div class="dy-2 px-1"><?=$obj->getCapability(Content::FORMAT_EDITABLE);?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Équipement</h4>
        <div class="dy-2 px-1"><?=$obj->getItem(Content::FORMAT_EDITABLE);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_item(Content::FORMAT_EDITABLE);?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Consommables</h4>
        <div class="dy-2 px-1"><?=$obj->getConsumable(Content::FORMAT_EDITABLE);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_consumable(Content::FORMAT_EDITABLE);?></div>
    </div>
</div>