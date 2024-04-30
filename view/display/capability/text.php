<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($is_link)){ $is_link = false; }else{ if(!is_bool($is_link)){ $is_link = false; } }
    $onclick = "";
    if($is_link){
        $onclick = "ondblclick=\"Capability.open('".$obj->getUniqid()."');\"";
    }    
?>

<p class="text_resume_tooltops-show" data-target="#capability<?=$obj->getUniqid()?>"  <?=$onclick?>>
    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'class' => "pe-1"]))?><?=$obj->getName()?>
</p>
<div id="capability<?=$obj->getUniqid()?>"  style="display:none;">
    <div class="p-2 m-1 size-0-8 back-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>-l-5">
        <div class="d-flex flew-row flex-nowrap">
            <div class="card-body m-1 p-0">
                <div class="d-flex flex-row justify-content-between ">
                    <p class="bold"><?=$obj->getName()?></p>
                    <div class="d-flex flex-row align-content-center">
                        <div style="height:18px;"><?=$obj->getPo_editable(Content::FORMAT_ICON)?></div>
                    </div>
                </div>
                <p class="d-flex flex-row justify-content-around align-items-center">
                    <?=$obj->getPa(Content::FORMAT_ICON)?> 
                    <?=$obj->getPo(Content::FORMAT_ICON)?> 
                    <div><?=$obj->getTime_before_use_again(Content::FORMAT_ICON)?></div>
                    <div><?=$obj->getCasting_time(Content::FORMAT_ICON)?></div>
                    <div><?=$obj->getDuration(Content::FORMAT_ICON)?></div>
                </p>
            </div>
        </div>
        <div class="d-flex flex-row justify-content-around align-items-baseline flex-wrap">
            <div class="me-1 mb-1"><?=$obj->getIs_magic(Content::FORMAT_BADGE)?></div>
            <div class="me-1 mb-1"><?=$obj->getRitual_available(Content::FORMAT_BADGE)?></div>
            <div class="me-1 mb-1"><?=$obj->getSpecialization(Content::FORMAT_BADGE)?></div>
            <div class="me-1 mb-1"><?=$obj->getPowerful(Content::FORMAT_BADGE)?></div>
            <div class="me-1 mb-1"><?=$obj->getElement(Content::FORMAT_BADGE)?></div>
        </div>
        <?=$obj->getEffect()?>
        <div class="m-2 nav-item-divider back-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>"></div>
        <?=$obj->getDescription()?>
    </div>
</div>