<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div style="position:relative;width: <?=$size?>px;">
    <div ondblclick="Spell.open('<?=$obj->getUniqid()?>');" class="card-hover-linked card p-2 m-1 <?=$obj->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>-l-5 <?=$obj->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>-l-4-hover border-solid border-2 <?=$obj->getElement(Content::FORMAT_COLOR_VERBALE, "border")?>-d-2" style="width: <?=$size?>px;" >
        <div class="d-flex flew-row flex-nowrap">
            <div>
                <?=$obj->getPath_img(Content::FORMAT_IMAGE, "img-back-50")?>
                <p class="mt-1"><?=$obj->getLevel(Content::FORMAT_ICON)?></p> 
            </div>

            <div class="card-body m-1 p-0">
                <div class="d-flex flew-row justify-content-between ">
                    <p class="bold"><?=$obj->getName()?></p>
                    <div class="d-flex flex-row align-content-center">
                        <div style="height:18px;"><?=$obj->getPo_editable(Content::FORMAT_ICON)?></div>
                        <div><?=$obj->getPa(Content::FORMAT_ICON)?></div>
                    </div>
                </div>
                <p class="d-flex flex-row justify-content-around align-items-center">
                    <?=$obj->getPo(Content::FORMAT_ICON)?> 
                    <?=$obj->getSight_line(Content::FORMAT_ICON)?> 
                    <?=$obj->getFrequency(Content::FORMAT_BADGE)?>
                </p>
            </div>
            <div class="d-flex flex-column justify-content-between ms-auto">
                <a onclick='User.changeBookmark(this);' data-classe='spell' data-uniqid='<?=$obj->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                <a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=spell&a=getPdf&uniqids=<?=$obj->getUniqid()?>'><i class='fas fa-file-pdf'></i></a>
            </div>
        </div>
        <div class="card-hover-showed">
            <div class="d-flex flex-row justify-content-around align-items-baseline flex-wrap">
                <?=$obj->getIs_magic(Content::FORMAT_BADGE)?>
                <?=$obj->getType(Content::FORMAT_BADGE)?>
                <?=$obj->getCategory(Content::FORMAT_BADGE)?>
                <?=$obj->getPowerful(Content::FORMAT_BADGE)?>
                <?=$obj->getElement(Content::FORMAT_BADGE)?>
            </div>
            <?=$obj->getEffect()?>
            <div class="nav-item-divider <?=$obj->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
            <?=$obj->getDescription()?>
            <?php if(!empty($obj->getId_invocation())){ ?>
                <div class="nav-item-divider <?=$obj->getElement(Content::FORMAT_COLOR_VERBALE, "back")?>"></div>
                <div style="margin:-4px;"><?=$obj->getId_invocation(Content::DISPLAY_RESUME, size:290)?></div>
            <?php } ?>
        </div>
    </div>
</div>