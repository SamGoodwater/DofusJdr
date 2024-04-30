<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div class="card mb-3">
    <p class='size-0-7 m-1'>PNJ <?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$obj->getTimestamp_updated(Content::DATE_FR);?></p>
    <div class="row m-2">
        <div class="col-auto">
            <div class="selector-image-main"><?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_EDITABLE]))?></div>
        </div>
        <div class="col-auto">
            <h6 class="text-center">Classe</h6>
            <?=$obj->getClasse(Content::FORMAT_OBJECT)->getVisual(new Style(["display" => Content::DISPLAY_EDITABLE]))?>
            <p class="mt-4 text-center"><a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='btn btn-sm btn-animate btn-border-red' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=<?=$obj->getUniqid()?>'><i class='fa-solid fa-file-pdf'></i> Générer un pdf</a></p>
        </div>E
        <div class="col ms-4">
            <div class="d-flex justify-content-between align-items-baseline">
                <p class="text-center"><?=$obj->getLevel(Content::DISPLAY_EDITABLE)?></p>
            </div>
            <div class="d-flex flex-row">
                <?=$obj->getAge(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getSize(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getWeight(Content::DISPLAY_EDITABLE)?>
            </div>
            <?=$obj->getAlignment(Content::DISPLAY_EDITABLE)?>
            <?=$obj->getHistorical(Content::DISPLAY_EDITABLE)?>
            <?=$obj->getTrait(Content::DISPLAY_EDITABLE);?>
        </div>
    </div>
    <div class="card-body">
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Caractèristiques</h4>
        <div class="d-flex flex-row justify-content-between">
            <?=$obj->getLife(Content::DISPLAY_EDITABLE)?>
            <?=$obj->getPA(Content::DISPLAY_EDITABLE)?>
            <?=$obj->getPM(Content::DISPLAY_EDITABLE)?>
            <?=$obj->getPO(Content::DISPLAY_EDITABLE)?>
            <?=$obj->getIni(Content::DISPLAY_EDITABLE)?>
            <?=$obj->getInvocation(Content::DISPLAY_EDITABLE)?>
            <?=$obj->getTouch(Content::DISPLAY_EDITABLE)?>
        </div>
        <div class="row justify-content-around">
            <div class="col-auto">
                <?=$obj->getVitality(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getSagesse(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getStrong(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getIntel(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getAgi(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getChance(Content::DISPLAY_EDITABLE)?>
            </div>
            <div class="col-auto">
                <?=$obj->getCa(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getDodge_pa(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getDodge_pm(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getFuite(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getTacle(Content::DISPLAY_EDITABLE)?>
            </div>
            <div class="col-auto">
                <?=$obj->getDo_fixe_neutre(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getDo_fixe_terre(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getDo_fixe_feu(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getDo_fixe_air(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getDo_fixe_eau(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getDo_fixe_multiple(Content::DISPLAY_EDITABLE)?>
            </div>
            <div class="col-auto">
                <?=$obj->getRes_neutre(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getRes_terre(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getRes_feu(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getRes_air(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getRes_eau(Content::DISPLAY_EDITABLE)?>
            </div>
        </div>

        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Compétences</h4>
        <div class="row">
            <div class="col-auto my-2">
                <p class="text-agi">Dépendant de l'agilité</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getAgi(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getAcrobatie_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getAcrobatie_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getDiscretion_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getDiscretion_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getEscamotage_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getEscamotage_mastery(Content::DISPLAY_EDITABLE)?>
                <p class="text-force mt-2">Dépendant de la Force</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getStrong(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getAthletisme_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getAthletisme_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getIntimidation_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getIntimidation_mastery(Content::DISPLAY_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-intel">Dépendant de l'Intelligence</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getIntel(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getArcane_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getArcane_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getHistoire_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getHistoire_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getInvestigation_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getInvestigation_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getNature_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getNature_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getReligion_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getReligion_mastery(Content::DISPLAY_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-sagesse">Dépendant de la Sagesse</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getSagesse(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getDressage_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getDressage_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getMedecine_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getMedecine_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getPerception_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getPerception_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getPerspicacite_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getPerspicacite_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getSurvie_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getSurvie_mastery(Content::DISPLAY_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-chance">Dépendant de la Chance</p>
                <p class="text-grey-d-1 size-0-9 mb-2"><?=$obj->getChance(Content::FORMAT_BADGE)?> + Bonus d'équipement</p>
                <?=$obj->getPersuasion_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getPersuasion_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getRepresentation_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getRepresentation_mastery(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getSupercherie_bonus(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getSupercherie_mastery(Content::DISPLAY_EDITABLE)?>
            </div>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="text-main-d-1 text-center">Informations</h4>
        <p class="card-text my-2"><?=$obj->getDescription(Content::DISPLAY_EDITABLE);?></p>
        <p class="card-text my-2"><?=$obj->getStory(Content::DISPLAY_EDITABLE);?></p>
        <p class="card-text my-2"><?=$obj->getOther_info(Content::DISPLAY_EDITABLE);?></p>
        <?=$obj->getLocation(Content::DISPLAY_EDITABLE);?>
        <div class="d-flex flex-row justify-content-between my-2">
            <div class="w-100 me-3"><?=$obj->getDrop_(Content::DISPLAY_EDITABLE)?></div>
            <div><?=$obj->getKamas(Content::DISPLAY_EDITABLE)?></div>
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
        <div class="dy-2 px-1"><?=$obj->getOther_consumable(Content::DISPLAY_EDITABLE);?></div>
    </div>
    <p class="text-right font-size-0-8 m-1"><a class='btn btn-sm btn-animate btn-border-red' onclick="Npc.remove('<?=$obj->getUniqid()?>')"><i class="fa-solid fa-trash"></i> Supprimer</a></p>
</div>