<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($is_link)){ $is_link = false; }else{ if(!is_bool($is_link)){ $is_link = false; } }
    $onclick = "";
    if($is_link){
        $onclick = "ondblclick=\"Classe.open('".$obj->getUniqid()."');\"";
    }    
?>

<p onmouseover="showTooltips(this, '#classe<?=$obj->getUniqid()?>');" <?=$onclick?>>
    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'class' => "pe-1"]))?><?=$obj->getName()?>
</p>
<div id="classe<?=$obj->getUniqid()?>" class="size-0-8"  style="display:none;">
    <div class="d-flex flew-row flex-nowrap justify-content-start">
        <div>
            <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-30"]))?>
        </div>
        <div class="mx-2 d-flex flex-column justify-content-between">
            <div class="d-flex flex-nowrap justify-content-between">
                <p class="bold ms-1"><?=$obj->getName()?></p>
                <div class="me-1"><?=$obj->getWeapons_of_choice(Content::FORMAT_ICON)?></div>
            </div>
            <div class="size-0-7 text-grey-d-2"><?=$obj->getDescription_fast()?></div>
        </div>
    </div>
    <div class="justify-content-center d-flex short-badge-150 flex-wrap"><?=$obj->getTrait(Content::FORMAT_BADGE)?></div>
    <p class="card-text"><?=$obj->getSpell(Content::DISPLAY_LIST)?></p>
    <p class="card-text"><small class="text-muted"><?=$obj->getDescription_fast()?></small></p>
    <p class="card-text"><?=$obj->getDescription()?></p>
    <p class="text-main-d-2 size-1 text-bold mt-2 mb-1">Spécificités de la classe</p>
    <p class="card-text"><?=$obj->getSpecificity()?></p>
    <p class="text-main-d-2 size-1 text-bold mt-2 mb-1">Gestion des points de vie</p>
    <p class="card-text"><?=$obj->getLife()?></p>
</div>