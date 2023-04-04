<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div class="card mb-3">
    <div class="d-flex flex-row justify-content-between align-items-start m-2">
        <div><?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-120"]));?></div>
        <div>
            <h6 class="text-center">Classe</h6>
            <?=$obj->getClasse(Content::FORMAT_OBJECT)->getVisual(Content::DISPLAY_RESUME)?>
        </div>
        <div>
            <div class="d-flex justify-content-between align-items-baseline">
                <h4><?=$obj->getName()?></h4>
                <p class="text-center"><?=$obj->getLevel(Content::FORMAT_BADGE)?></p>
            </div>
            <p>
                <?=$obj->getAge(Content::FORMAT_BADGE)?>
                <?=$obj->getSize(Content::FORMAT_BADGE)?>
                <?=$obj->getWeight(Content::FORMAT_BADGE)?>
            </p>
            <p><span class="size-0-8 text-grey-d-2">Alignement : </span><?=$obj->getAlignment()?></p>
            <p><span class="size-0-8 text-grey-d-2">Historique : </span><?=$obj->getHistorical()?></p>
        </div>
        <div class="m-2">
            <?php if($user->getRight('npc', User::RIGHT_WRITE)){ ?>
                <p><a class='text-main-d-2 text-main-l-3-hover' onclick="Npc.open('<?=$obj->getUniqid()?>', Controller.DISPLAY_EDITABLE)"><i class='far fa-edit'></i> Modifier</a></p>
            <?php } ?>
            <p><a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=<?=$obj->getUniqid()?>'><i class='fas fa-file-pdf'></i> Générer un pdf</a></p>
        </div>
    </div>
    <div class="card-body">
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Caractèristiques</h4>
        <div class="m-1 d-flex flex-row justify-content-between align-items-center">
            <p class="m-1"><?=$obj->getLife(Content::FORMAT_VIEW)?></p>
            <p class="m-1"><?=$obj->getPA(Content::FORMAT_VIEW)?></p>
            <p class="m-1"><?=$obj->getPM(Content::FORMAT_VIEW)?></p>
            <p class="m-1"><?=$obj->getPO(Content::FORMAT_VIEW)?></p>
        </div>
        <div class="m-1 mb-2 d-flex flex-row justify-content-between align-items-center">
            <p class="m-1"><?=$obj->getIni(Content::FORMAT_VIEW)?></p>
            <p class="m-1"><?=$obj->getInvocation(Content::FORMAT_VIEW)?></p>
            <p class="m-1"><?=$obj->getTouch(Content::FORMAT_VIEW)?></p>
        </div>
        <div class="row justify-content-around">
            <div class="col-auto">
                <p class="m-1"><?=$obj->getVitality(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getSagesse(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getStrong(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getIntel(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getAgi(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getChance(Content::FORMAT_VIEW)?></p>
            </div>
            <div class="col-auto">
                <p class="m-1"><?=$obj->getCa(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getDodge_pa(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getDodge_pm(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getFuite(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getTacle(Content::FORMAT_VIEW)?></p>
            </div>
            <div class="col-auto">
                <p class="m-1"><?=$obj->getRes_neutre(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getRes_terre(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getRes_feu(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getRes_air(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getRes_eau(Content::FORMAT_VIEW)?></p>
            </div>
        </div>

        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Compétences</h4>
        <div class="row">
            <div class="col-auto my-2">
                <p class="text-agi mb-2">Dépendant de l'agilité</p>
                <p class="m-1"><?=$obj->getAcrobatie(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getDiscretion(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getEscamotage(Content::FORMAT_VIEW)?></p>
                <p class="text-force my-2">Dépendant de la Force</p>
                <p class="m-1"><?=$obj->getAthletisme(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getIntimidation(Content::FORMAT_VIEW)?></p>
            </div>
            <div class="col-auto mb-2 mt-3">
                <p class="text-intel mb-2">Dépendant de l'Intelligence</p>
                <p class="m-1"><?=$obj->getArcane(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getHistoire(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getInvestigation(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getNature(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getReligion(Content::FORMAT_VIEW)?></p>
            </div>
            <div class="col-auto my-2">
                <p class="text-sagesse mb-2">Dépendant de la Sagesse</p>
                <p class="m-1"><?=$obj->getDressage(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getMedecine(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getPerception(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getPerspicacite(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getSurvie(Content::FORMAT_VIEW)?></p>
            </div>
            <div class="col-auto my-2">
                <p class="text-chance mb-2">Dépendant de la Chance</p>
                <p class="m-1"><?=$obj->getPersuasion(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getRepresentation(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getSupercherie(Content::FORMAT_VIEW)?></p>
            </div>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Informations</h4>
        <p class="card-text"><?=$obj->getStory();?></p>
        <p class="card-text"><?=$obj->getOther_info();?></p>
        <div class="d-flex flex-row justify-content-between">
            <?=$obj->getDrop_()?>
            <?=$obj->getKamas()?>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Sorts</h4>
        <div class="dy-2 px-1"><?=$obj->getSpell(Content::DISPLAY_RESUME);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_spell();?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Equipement</h4>
        <div class="dy-2 px-1"><?=$obj->getItem();?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_item();?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Consommables</h4>
        <div class="dy-2 px-1"><?=$obj->getConsumable();?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_consomable();?></div>
    </div>
</div>