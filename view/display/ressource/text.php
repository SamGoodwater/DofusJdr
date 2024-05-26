<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($is_link)){ $is_link = false; }else{ if(!is_bool($is_link)){ $is_link = false; } }
    $onclick = "";
    if($is_link){
        $onclick = "ondblclick=\"Ressource.open('".$obj->getUniqid()."');\"";
    }    
?>

<p class="text_resume_tooltops-show" data-target="#ressource<?=$obj->getUniqid()?>"  <?=$onclick?>>
    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'class' => "pe-1"]))?><?=$obj->getName()?>
</p>
<div id="ressource<?=$obj->getUniqid()?>" class="box_resume_tooltips"  style="display:none;">
    <div class="d-flex flew-row flex-nowrap justify-content-start">
        <div>
            <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-30"]))?>
        </div>
        <div class="m-1 p-0">
            <p class="bold ms-1"><?=$obj->getName()?></p>
            <div class="d-flex flex-wrap justify-content-around align-items-baseline">
                <div class="mt-1 text-level short-badge-150"><?=$obj->getLevel(Content::FORMAT_ICON)?></div>
                <div class="mt-1 short-badge-100"><?=$obj->getType(Content::FORMAT_BADGE)?></div>
                <div class="mt-1 mx-2"><?=$obj->getPrice(Content::FORMAT_ICON)?></div>
                <div class="mt-1 mx-2"><?=$obj->getWeight(Content::FORMAT_ICON)?></div>
                <div class="mt-1 mx-2"><?=$obj->getRarity(Content::FORMAT_BADGE)?></div>
            </div>
        </div>
    </div>
    <div>
        <?=$obj->getUsable(Content::FORMAT_BADGE)?>
    </div>
</div>