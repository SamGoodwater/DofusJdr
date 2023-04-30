<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div class="card mb-3">
    <p class='size-0-7 m-1'>PNJ <?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$obj->getTimestamp_updated(Content::DATE_FR);?></p>
    <div class="row m-2">
        <div class="col-auto">
            <div><?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_EDITABLE]))?></div>
        </div>
        <div class="col-auto">
            <h6 class="text-center">Classe</h6>
            <?=$obj->getClasse(Content::FORMAT_OBJECT)->getVisual(Content::DISPLAY_RESUME)?>
            <p class="mt-4 text-center"><a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='btn btn-sm btn-border-red' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=<?=$obj->getUniqid()?>'><i class='fas fa-file-pdf'></i> Générer un pdf</a></p>
        </div>
        <div class="col ms-4">
            <div class="d-flex justify-content-between align-items-baseline">
                <p class="text-center"><?=$obj->getLevel(Content::DISPLAY_RESUME)?></p>
            </div>
            <div class="d-flex flex-row">
                <?=$obj->getAge(Content::DISPLAY_RESUME)?>
                <?=$obj->getSize(Content::DISPLAY_RESUME)?>
                <?=$obj->getWeight(Content::DISPLAY_RESUME)?>
            </div>
            <?=$obj->getAlignment(Content::DISPLAY_RESUME)?>
            <?=$obj->getHistorical(Content::DISPLAY_RESUME)?>
        </div>
    </div>
    <div class="card-body">
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Caractèristiques</h4>
        <div class="d-flex flex-row justify-content-between">
            <?=$obj->getLife(Content::DISPLAY_RESUME)?>
            <?=$obj->getPA(Content::DISPLAY_RESUME)?>
            <?=$obj->getPM(Content::DISPLAY_RESUME)?>
            <?=$obj->getPO(Content::DISPLAY_RESUME)?>
            <?=$obj->getIni(Content::DISPLAY_RESUME)?>
            <?=$obj->getInvocation(Content::DISPLAY_RESUME)?>
            <?=$obj->getTouch(Content::DISPLAY_RESUME)?>
        </div>
        <div class="row justify-content-around">
            <div class="col-auto">
                <?=$obj->getVitality(Content::DISPLAY_RESUME)?>
                <?=$obj->getSagesse(Content::DISPLAY_RESUME)?>
                <?=$obj->getStrong(Content::DISPLAY_RESUME)?>
                <?=$obj->getIntel(Content::DISPLAY_RESUME)?>
                <?=$obj->getAgi(Content::DISPLAY_RESUME)?>
                <?=$obj->getChance(Content::DISPLAY_RESUME)?>
            </div>
            <div class="col-auto">
                <?=$obj->getCa(Content::DISPLAY_RESUME)?>
                <?=$obj->getDodge_pa(Content::DISPLAY_RESUME)?>
                <?=$obj->getDodge_pm(Content::DISPLAY_RESUME)?>
                <?=$obj->getFuite(Content::DISPLAY_RESUME)?>
                <?=$obj->getTacle(Content::DISPLAY_RESUME)?>
            </div>
            <div class="col-auto">
                <?=$obj->getRes_neutre(Content::DISPLAY_RESUME)?>
                <?=$obj->getRes_terre(Content::DISPLAY_RESUME)?>
                <?=$obj->getRes_feu(Content::DISPLAY_RESUME)?>
                <?=$obj->getRes_air(Content::DISPLAY_RESUME)?>
                <?=$obj->getRes_eau(Content::DISPLAY_RESUME)?>
            </div>
        </div>

        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Compétences</h4>
        <div class="row">
            <div class="col-auto my-2">
                <p class="text-agi">Dépendant de l'agilité</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getAgi(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getAcrobatie(Content::DISPLAY_RESUME)?>
                <?=$obj->getDiscretion(Content::DISPLAY_RESUME)?>
                <?=$obj->getEscamotage(Content::DISPLAY_RESUME)?>
                <p class="text-force mt-2">Dépendant de la Force</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getStrong(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getAthletisme(Content::DISPLAY_RESUME)?>
                <?=$obj->getIntimidation(Content::DISPLAY_RESUME)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-intel">Dépendant de l'Intelligence</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getIntel(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getArcane(Content::DISPLAY_RESUME)?>
                <?=$obj->getHistoire(Content::DISPLAY_RESUME)?>
                <?=$obj->getInvestigation(Content::DISPLAY_RESUME)?>
                <?=$obj->getNature(Content::DISPLAY_RESUME)?>
                <?=$obj->getReligion(Content::DISPLAY_RESUME)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-sagesse">Dépendant de la Sagesse</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getSagesse(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getDressage(Content::DISPLAY_RESUME)?>
                <?=$obj->getMedecine(Content::DISPLAY_RESUME)?>
                <?=$obj->getPerception(Content::DISPLAY_RESUME)?>
                <?=$obj->getPerspicacite(Content::DISPLAY_RESUME)?>
                <?=$obj->getSurvie(Content::DISPLAY_RESUME)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-chance">Dépendant de la Chance</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getChance(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getPersuasion(Content::DISPLAY_RESUME)?>
                <?=$obj->getRepresentation(Content::DISPLAY_RESUME)?>
                <?=$obj->getSupercherie(Content::DISPLAY_RESUME)?>
            </div>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Informations</h4>
        <p class="card-text my-2"><?=$obj->getStory(Content::DISPLAY_RESUME);?></p>
        <p class="card-text my-2"><?=$obj->getOther_info(Content::DISPLAY_RESUME);?></p>
        <div class="d-flex flex-row justify-content-between my-2">
            <div class="w-100 me-3"><?=$obj->getDrop_(Content::DISPLAY_RESUME)?></div>
            <div><?=$obj->getKamas(Content::DISPLAY_RESUME)?></div>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <div class="dy-2 px-1"><?=$obj->getSpell(Content::DISPLAY_EDITABLE);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_spell(Content::DISPLAY_EDITABLE);?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <div class="dy-2 px-1"><?=$obj->getCapability(Content::DISPLAY_EDITABLE);?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <div class="dy-2 px-1"><?=$obj->getItem(Content::DISPLAY_EDITABLE);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_item(Content::DISPLAY_EDITABLE);?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <div class="dy-2 px-1"><?=$obj->getConsumable(Content::DISPLAY_EDITABLE);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_consomable(Content::DISPLAY_EDITABLE);?></div>
    </div>
    <p class="text-right font-size-0-8 m-1"><a class='btn btn-sm btn-border-red' onclick="Npc.remove('<?=$obj->getUniqid()?>')"><i class="fas fa-trash"></i> Supprimer</a></p>
</div>