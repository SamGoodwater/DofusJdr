<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }

    $classe = $obj->getClasse(Content::FORMAT_OBJECT);
?>

<div style="width: <?=$size?>px;">
    <div style="position:relative;">
        <div ondblclick="Npc.open('<?=$obj->getUniqid()?>');" class="card-hover-linked card p-2 m-1 border-secondary-d-2 border" style="width: <?=$size?>px;" >
            <div class="d-flex flew-row flex-nowrap justify-content-start">
                <div>
                    <?=$classe->getPath_img(Content::FORMAT_IMAGE, "img-back-50")?>
                </div>
                <div class="m-1">
                    <p class="bold ms-1"><?=$obj->getName()?></p>
                    <div class="d-flex align-items-baseline">
                        <h6>Classe : <?=$obj->getClasse(Content::FORMAT_OBJECT)->getName()?></h6>
                        <div class="ms-3 text-center short-badge-150"><?=$obj->getLevel(Content::FORMAT_BADGE)?></div>
                    </div>
                </div>
                <div class="ms-auto align-self-end">
                    <a onclick='User.changeBookmark(this);' data-classe='npc' data-uniqid='<?=$obj->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                    <p><a class="btn-text-secondary" title="Afficher les sorts" onclick="Classe.getSpellList('<?=$classe->getUniqid()?>');"><i class="fas fa-magic"></i></a></p>
                    <p><a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=npc&a=getPdf&uniqid=<?=$obj->getUniqid()?>'><i class='fas fa-file-pdf'></i></a></p>
                </div>
            </div>
            <div class="justify-content-center d-flex short-badge-150 flex-wrap"><?=$obj->getTrait(Content::FORMAT_BADGE)?></div>
            <div class="card-hover-showed">
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
                <div class="nav-item-divider back-main-d-1"></div>
                    <p class="card-text text-grey"><?=$obj->getOther_info();?></p>
                    <div class="d-flex flex-row justify-content-between">
                        <?=$obj->getDrop_()?>
                        <?=$obj->getKamas()?>
                    </div>
                </div>
        </div>
    </div>
</div>