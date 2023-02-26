<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }

    $npc = $obj->getId_seller(Content::FORMAT_OBJECT);
?>

<div style="position:relative;" style="width: <?=$size?>px;">
    <div ondblclick="Shop.open('<?=$obj->getUniqid()?>');" class="card-hover-linked card p-2 m-1 border-secondary-d-2 border" style="width: <?=$size?>px;">
        <div class="d-flex flew-row flex-nowrap">
            <div>
                <p class="bold"><?=$obj->getName()?></p>
                <div class="d-flex justify-content-between">
                    <?=$obj->getLocation(Content::FORMAT_ICON)?>
                    <?=$obj->getPrice(Content::FORMAT_BADGE)?>
                </div>
                <p> Marchand·e : 
                    <?php if(!empty($npc)){ echo $npc->getName(); }  else { echo "Aucun"; } ?>
                </p>
            </div>
            <div class="d-flex flex-column justify-content-between ms-auto">
                <a onclick='User.changeBookmark(this);' data-classe='shop' data-uniqid='<?=$obj->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                <a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=shop&a=getPdf&uniqid=<?=$obj->getUniqid()?>'><i class='fas fa-file-pdf'></i></a>
            </div>
        </div>
        <div class="card-hover-showed">
            <?=$obj->getDescription()?> 
        </div>
    </div>
</div>