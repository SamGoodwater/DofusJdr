<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($is_link)){ $is_link = false; }else{ if(!is_bool($is_link)){ $is_link = false; } }
    $onclick = "";
    if($is_link){
        $onclick = "ondblclick=\"Mob.open('".$obj->getUniqid()."');\"";
    }    
?>

<p onmouseover="showTooltips(this, '#mob<?=$obj->getUniqid()?>');" <?=$onclick?>>
    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'class' => "pe-1"]))?><?=$obj->getName()?>
</p>
<div id="mob<?=$obj->getUniqid()?>" class="size-0-8"  style="display:none;">
    <div class="d-flex flew-row flex-nowrap justify-content-start">
        <div>
            <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-30"]))?>
        </div>
        <div class="m-1 p-0">
            <p class="bold ms-1"><?=$obj->getName()?></p>
            <div class="d-flex flex-wrap justify-content-around align-items-baseline">
                <p class="mt-1 text-level short-badge-150"><?=$obj->getLevel(Content::FORMAT_BADGE)?></p> 
                <div class="mt-1 mx-2"> <?=$obj->getPowerful(Content::FORMAT_ICON)?></div>
                <p class="mt-1 short-badge-100"><?=$obj->getHostility(Content::FORMAT_BADGE)?></p>
            </div>
        </div>
    </div>
    <div class="justify-content-center flex-wrap d-flex short-badge-150"><?=$obj->getTrait(Content::FORMAT_BADGE)?></div>
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
</div>