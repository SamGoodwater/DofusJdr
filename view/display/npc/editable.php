<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div class="card mb-3">
    <p class='size-0-7 m-1'>PNJ <?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$obj->getTimestamp_updated(Content::DATE_FR);?></p>
    <div class="row m-2">
        <div class="col-auto">
            <h6 class="text-center">Classe</h6>
            <?=$obj->getClasse(Content::FORMAT_OBJECT)->getVisual(Content::DISPLAY_RESUME)?>
            <p class="mt-4 text-center"><a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=<?=$obj->getUniqid()?>'><i class='fas fa-file-pdf'></i> Générer un pdf</a></p>
        </div>
        <div class="col ms-4">
            <div class="d-flex justify-content-between align-items-baseline">
                <p class="text-center"><?=$obj->getLevel(Content::FORMAT_EDITABLE)?></p>
            </div>
            <div class="d-flex flex-row">
                <?=$obj->getAge(Content::FORMAT_EDITABLE)?>
                <?=$obj->getSize(Content::FORMAT_EDITABLE)?>
                <?=$obj->getWeight(Content::FORMAT_EDITABLE)?>
            </div>
            <?=$obj->getAlignment(Content::FORMAT_EDITABLE)?>
            <?=$obj->getHistorical(Content::FORMAT_EDITABLE)?>
        </div>
    </div>
    <div class="card-body">
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Caractèristiques</h4>
        <div class="d-flex flex-row justify-content-between">
            <?=$obj->getLife(Content::FORMAT_EDITABLE)?>
            <?=$obj->getPA(Content::FORMAT_EDITABLE)?>
            <?=$obj->getPM(Content::FORMAT_EDITABLE)?>
            <?=$obj->getPO(Content::FORMAT_EDITABLE)?>
            <?=$obj->getIni(Content::FORMAT_EDITABLE)?>
            <?=$obj->getInvocation(Content::FORMAT_EDITABLE)?>
            <?=$obj->getTouch(Content::FORMAT_EDITABLE)?>
        </div>
        <div class="row justify-content-around">
            <div class="col-auto">
                <?=$obj->getVitality(Content::FORMAT_EDITABLE)?>
                <?=$obj->getSagesse(Content::FORMAT_EDITABLE)?>
                <?=$obj->getStrong(Content::FORMAT_EDITABLE)?>
                <?=$obj->getIntel(Content::FORMAT_EDITABLE)?>
                <?=$obj->getAgi(Content::FORMAT_EDITABLE)?>
                <?=$obj->getChance(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto">
                <?=$obj->getCa(Content::FORMAT_EDITABLE)?>
                <?=$obj->getDodge_pa(Content::FORMAT_EDITABLE)?>
                <?=$obj->getDodge_pm(Content::FORMAT_EDITABLE)?>
                <?=$obj->getFuite(Content::FORMAT_EDITABLE)?>
                <?=$obj->getTacle(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto">
                <?=$obj->getRes_neutre(Content::FORMAT_EDITABLE)?>
                <?=$obj->getRes_terre(Content::FORMAT_EDITABLE)?>
                <?=$obj->getRes_feu(Content::FORMAT_EDITABLE)?>
                <?=$obj->getRes_air(Content::FORMAT_EDITABLE)?>
                <?=$obj->getRes_eau(Content::FORMAT_EDITABLE)?>
            </div>
        </div>

        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Compétences</h4>
        <div class="row">
            <div class="col-auto my-2">
                <p class="text-agi">Dépendant de l'agilité</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getAgi(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getAcrobatie(Content::FORMAT_EDITABLE)?>
                <?=$obj->getDiscretion(Content::FORMAT_EDITABLE)?>
                <?=$obj->getEscamotage(Content::FORMAT_EDITABLE)?>
                <p class="text-force mt-2">Dépendant de la Force</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getStrong(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getAthletisme(Content::FORMAT_EDITABLE)?>
                <?=$obj->getIntimidation(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-intel">Dépendant de l'Intelligence</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getIntel(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getArcane(Content::FORMAT_EDITABLE)?>
                <?=$obj->getHistoire(Content::FORMAT_EDITABLE)?>
                <?=$obj->getInvestigation(Content::FORMAT_EDITABLE)?>
                <?=$obj->getNature(Content::FORMAT_EDITABLE)?>
                <?=$obj->getReligion(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-sagesse">Dépendant de la Sagesse</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getSagesse(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getDressage(Content::FORMAT_EDITABLE)?>
                <?=$obj->getMedecine(Content::FORMAT_EDITABLE)?>
                <?=$obj->getPerception(Content::FORMAT_EDITABLE)?>
                <?=$obj->getPerspicacite(Content::FORMAT_EDITABLE)?>
                <?=$obj->getSurvie(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-chance">Dépendant de la Chance</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getChance(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getPersuasion(Content::FORMAT_EDITABLE)?>
                <?=$obj->getRepresentation(Content::FORMAT_EDITABLE)?>
                <?=$obj->getSupercherie(Content::FORMAT_EDITABLE)?>
            </div>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Informations</h4>
        <p class="card-text my-2"><?=$obj->getStory(Content::FORMAT_EDITABLE);?></p>
        <p class="card-text my-2"><?=$obj->getOther_info(Content::FORMAT_EDITABLE);?></p>
        <div class="d-flex flex-row justify-content-between my-2">
            <?=$obj->getDrop_(Content::FORMAT_EDITABLE)?>
            <?=$obj->getKamas(Content::FORMAT_EDITABLE)?>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Sorts</h4>
        <p class="card-text my-2"><?=$obj->getClasse(Content::FORMAT_OBJECT)->getSpell(Content::FORMAT_BADGE);?></p>
        <p class="card-text my-2"><?=$obj->getOther_spell(Content::FORMAT_EDITABLE);?></p>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Equipements</h4>
        <p class="card-text my-2"><?=$obj->getOther_equipment(Content::FORMAT_EDITABLE);?></p>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Consommables</h4>
        <p class="card-text my-2"><?=$obj->getOther_consomable(Content::FORMAT_EDITABLE);?></p>
    </div>
    <p class="text-right font-size-0-8 m-1"><a class='text-red-d-2 text-red-l-3-hover' onclick="Npc.remove('<?=$obj->getUniqid()?>')"><i class="fas fa-trash"></i> Supprimer</a></p>
</div>