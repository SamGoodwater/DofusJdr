<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($is_link)){ $is_link = false; }else{ if(!is_bool($is_link)){ $is_link = false; } }
    $onclick = "";
    if($is_link){
        $onclick = "ondblclick=\"Item.open('".$obj->getUniqid()."');\"";
    }    
?>

<p class="text_resume_tooltops-show" data-target="#item<?=$obj->getUniqid()?>"  <?=$onclick?>>
    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'class' => "pe-1"]))?><?=$obj->getName()?>
</p>
<div id="item<?=$obj->getUniqid()?>" class="size-0-8"  style="display:none;">
    <div class="row">
        <div class="col-auto">
            <?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-30"]))?>
        </div>
        <div class="col">
            <p class="bold ms-1"><?=$obj->getName()?></p>
            <p class="row">
                <span class="col-auto me-1 mt-1 short-badge-150"><?=$obj->getType(Content::FORMAT_BADGE)?></span>
                <span class="col-auto me-1 mt-1 short-badge-150"><?=$obj->getLevel(Content::FORMAT_BADGE)?></span>
                <span class="col-auto me-1 mt-1 short-badge-150"><?=$obj->getPrice(Content::FORMAT_BADGE)?></span>
                <span class="col-auto me-1 mt-1 short-badge-150"><?=$obj->getRarity(Content::FORMAT_BADGE)?></span>
            </p>
        </div>
    </div>
    <p class="card-text"><?=$obj->getEffect()?></p>
    <div class="nav-item-divider m-1 back-main-d-1"></div>
    <p class="card-text"><small class="size-0-9 text-secondary-d-3"><?=$obj->getDescription()?></small></p>
    <p class="card-text"><small class="text-muted size-0-7 text-grey-d-2"><?=$obj->getRecepe()?></small></p>
</div>