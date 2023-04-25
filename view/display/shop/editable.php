<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }

    $npc = $obj->getId_seller(Content::FORMAT_OBJECT);
?>

<div class="card mb-3">
    <p class='size-0-7 m-1'>Hôtel de vente N°<?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$obj->getTimestamp_updated(Content::DATE_FR);?></p>
    <div class="d-flex flex-row justify-content-around flex-wrap m-3">
        <div class="col-auto">
            <h4 class="m-2 mx-4"><?=$obj->getName(Content::DISPLAY_EDITABLE)?></h4>
            <p><?=$obj->getLocation(Content::DISPLAY_EDITABLE)?></p>
            <p><?=$obj->getPrice(Content::DISPLAY_EDITABLE)?></p>
        </div>
        <div class="col-auto">
            <h6 class="text-center">Marchand·e</h6>
            <div class="row justify-content-center">
                <?php if(!empty($npc)){ ?>
                    <?=$npc->getVisual(Content::DISPLAY_RESUME)?>
                <?php }  else {?>
                    <p class="text-center">Aucun</p>
                <?php } ?>
            </div>
        </div>
    </div>
    <?=$obj->getDescription(Content::DISPLAY_EDITABLE)?>
    <div class="card-body text-center">
        <h3>Equipements</h3>
        <?=$obj->getItem(Content::DISPLAY_EDITABLE)?>
        <h3>Consommables</h3>
        <?=$obj->getConsumable(Content::DISPLAY_EDITABLE)?>
    </div>
    <p class="text-right font-size-0-8 m-1"><a class='btn btn-sm btn-border-red' onclick="Shop.remove('<?=$obj->getUniqid()?>')"><i class="fas fa-trash"></i> Supprimer</a></p>
</div>