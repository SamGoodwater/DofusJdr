<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div class="card mb-3">
    <div class="row g-0">
        <div class="col-auto selector-image-main">
            <?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_EDITABLE, "class" => "img-back-150"]))?>
            <div class="text-center m-2">
                <?=$obj->getPowerful(Content::FORMAT_EDITABLE)?>
                <?=$obj->getUsable(Content::FORMAT_EDITABLE)?>
            </div>
        </div>
        <div class="col">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <?=$obj->getLevel(Content::FORMAT_EDITABLE)?>
                        <div class="d-flex justify-content-start align-content-start flex-wrap">
                            <div class="m-1"><?=$obj->getElement(Content::FORMAT_EDITABLE)?></div>
                        </div>
                        <div class="m-1"><?=$obj->getIs_magic(Content::FORMAT_EDITABLE)?></div>
                        <div class="m-1"><?=$obj->getRitual_available(Content::FORMAT_EDITABLE)?></div>
                    </div>  
                    <div class="col">
                        <?=$obj->getTime_before_use_again(Content::FORMAT_EDITABLE)?>
                        <?=$obj->getCasting_time(Content::FORMAT_EDITABLE)?>
                        <?=$obj->getDuration(Content::FORMAT_EDITABLE)?>
                        <?=$obj->getPa(Content::FORMAT_EDITABLE)?>
                        <?=$obj->getPo(Content::FORMAT_EDITABLE)?>
                        <?=$obj->getPo_editable(Content::FORMAT_EDITABLE)?>
                    </div>           
                </div>
                <div class="row m-1"><?=$obj->getSpecialization(Content::FORMAT_EDITABLE)?></div>
            </div>
        </div>
        <div class="nav-item-divider back-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>"></div>
        <p class='size-0-7 mb-1'>Aptitude <?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$obj->getTimestamp_updated(Content::DATE_FR);?></p>
        <p class="card-text mb-2"><?=$obj->getEffect(Content::FORMAT_EDITABLE)?></p>
        <p class="card-text  my-2"><?=$obj->getDescription(Content::FORMAT_EDITABLE)?></p>
        <div class="nav-item-divider back-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>"></div>
    </div>
    <p class="text-right font-size-0-8 m-1"><a class='btn btn-sm btn-animate btn-border-red' onclick="Capability.remove('<?=$obj->getUniqid()?>')"><i class="fa-solid fa-trash"></i> Supprimer</a></p>
</div>