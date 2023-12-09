<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($is_link)){ $is_link = false; }else{ if(!is_bool($is_link)){ $is_link = false; } }
    $onclick = "";
    if($is_link){
        $onclick = "ondblclick=\"Npc.open('".$obj->getUniqid()."');\"";
    }    
?>

<p data-event-trigger="mouseover" data-event-type="tooltips" data-event-target="#npc<?=$obj->getUniqid()?>" onmouseover="showTooltips(this);" <?=$onclick?>>
    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'class' => "pe-1"]))?><?=$obj->getName()?>
</p>
<div id="npc<?=$obj->getUniqid()?>" class="size-0-8"  style="display:none;">
    <div class="d-flex flew-row flex-nowrap justify-content-start">
        <div>
            <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-30"]))?>
        </div>
        <div class="m-1">
            <p class="bold ms-1"><?=$obj->getName()?></p>
            <div class="d-flex align-items-baseline">
                <h6>Classe : <?=$obj->getClasse(Content::FORMAT_OBJECT)->getName()?></h6>
                <div class="ms-3 text-center short-badge-150"><?=$obj->getLevel(Content::FORMAT_BADGE)?></div>
            </div>
        </div>
    </div>
    <div class="justify-content-center d-flex short-badge-150 flex-wrap"><?=$obj->getTrait(Content::FORMAT_BADGE)?></div>
        <p class="size-0-8 short-badge-150">
            <?=$obj->getAge(Content::FORMAT_BADGE)?>
            <?=$obj->getSize(Content::FORMAT_BADGE)?>
            <?=$obj->getWeight(Content::FORMAT_BADGE)?>
        </p>
        <p class="size-0-8"><span class="size-0-8 text-grey-d-2">Alignement : </span><?=$obj->getAlignment()?></p>
        <p class="size-0-8"><span class="size-0-8 text-grey-d-2">Historique : </span><?=$obj->getHistorical()?></p>
        <div class="d-flex justify-content-around flex-wrap">
            <div class="col-auto">
                <div><?=$obj->getPa(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getPm(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getPo(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getIni(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getLife(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getTouch(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getMaster_bonus(Content::FORMAT_ICON)?></div>
            </div>
            <div class="col-auto">
                <div><?=$obj->getVitality(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getSagesse(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getStrong(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getIntel(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getAgi(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getChance(Content::FORMAT_ICON);?></div>
            </div>
            <div class="col-auto">
                <div><?=$obj->getCa(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getFuite(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getTacle(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getDodge_pa(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getDodge_pm(Content::FORMAT_ICON);?></div>
            </div> 
            <div class="col-auto gap-1">
                <div><?=$obj->getDo_fixe_neutre(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getDo_fixe_terre(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getDo_fixe_feu(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getDo_fixe_air(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getDo_fixe_eau(Content::FORMAT_ICON)?></div>
                <div><?=$obj->getDo_fixe_multiple(Content::FORMAT_ICON)?></div>
            </div>
            <div class="col-auto">
                <div><?=$obj->getRes_neutre(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getRes_terre(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getRes_feu(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getRes_air(Content::FORMAT_ICON);?></div>
                <div><?=$obj->getRes_eau(Content::FORMAT_ICON);?></div>
            </div> 
        </div>
        <div class="row">
            <div class="col-auto my-1">
                <p class="m-1"><?=$obj->getAcrobatie(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getDiscretion(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getEscamotage(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getAthletisme(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getIntimidation(Content::FORMAT_VIEW)?></p>
            </div>
            <div class="col-auto my-1">
                <p class="m-1"><?=$obj->getArcane(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getHistoire(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getInvestigation(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getNature(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getReligion(Content::FORMAT_VIEW)?></p>
            </div>
            <div class="col-auto my-1">
                <p class="m-1"><?=$obj->getDressage(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getMedecine(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getPerception(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getPerspicacite(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getSurvie(Content::FORMAT_VIEW)?></p>
            </div>
            <div class="col-auto my-1">
                <p class="m-1"><?=$obj->getPersuasion(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getRepresentation(Content::FORMAT_VIEW)?></p>
                <p class="m-1"><?=$obj->getSupercherie(Content::FORMAT_VIEW)?></p>
            </div>
        </div>
        <p class="card-text text-grey-d-2"><?=$obj->getStory();?></p>
        <div class="nav-item-divider m-1 back-main-d-1"></div>
        <p class="card-text text-grey"><?=$obj->getOther_info();?></p>
        <div class="d-flex flex-row justify-content-between">
            <?=$obj->getDrop_()?>
            <?=$obj->getKamas()?>
        </div>
</div>