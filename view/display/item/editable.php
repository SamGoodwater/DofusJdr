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
        <div class="col-auto selector-image-main"><?=$obj->getFile('logo',  new Style(['format' => Content::FORMAT_EDITABLE, "class" => "img-back-150"]))?></div>
        <div class="col">
            <div class="card-body">
                <div class="row">
                    <div class="col-auto">
                        <div class="d-flex justify-content-start flex-wrap">
                            <div class="me-4"><?=$obj->getLevel(Content::FORMAT_EDITABLE)?></div>
                            <div><?=$obj->getPrice(Content::FORMAT_EDITABLE)?></div>
                        </div>   
                    </div>
                    <div class="col-auto d-flex flex-column justify-content-center">
                        <?=$obj->getType(Content::FORMAT_EDITABLE)?>
                        <?=$obj->getRarity(Content::FORMAT_EDITABLE)?>
                    </div>  
                    <div class="col-auto ms-auto">
                        <?=$obj->getUsable(Content::FORMAT_EDITABLE)?>
                    </div>   
                </div>
                <div class="nav-item-divider back-main-d-1"></div>
                <p class='size-0-7 mb-2'>Équipement <?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$obj->getTimestamp_updated(Content::DATE_FR);?></p>
            </div>
        </div>
    </div>
    <div class="card-text m-3"><?=$obj->getBonus(Content::FORMAT_EDITABLE);?></div>
    <div class="card-text m-3"><?=$obj->getEffect(Content::FORMAT_EDITABLE);?></div>
    <div class="card-text m-3"><?=$obj->getDescription(Content::FORMAT_EDITABLE);?></div>
    <div class="card-text m-3"><?=$obj->getRecepe(Content::FORMAT_EDITABLE);?></div>
    <p class="text-right font-size-0-8 m-1"><a class='btn btn-sm btn-animate btn-border-red' onclick="Item.remove('<?=$obj->getUniqid()?>')"><i class="fa-solid fa-trash"></i> Supprimer</a></p>
</div>