<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div id="<?=$style->getId()?>" class="resume <?=$style->getClass()?>" style="width: <?=$style->getSize()?>px;">
    <div ondblclick="Spell.open('<?=$obj->getUniqid()?>');" class="card-hover-linked card p-2 m-1 back-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>-l-5 back-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>-l-4-hover"  >
        <div class="d-flex flew-row flex-nowrap align-items-center">
            <div>
                <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-50"]))?>
                <p class="mt-1 level"><?=$obj->getLevel(Content::FORMAT_BADGE)?></p> 
            </div>

            <div class="card-body m-1 p-0">
                <div class="d-flex flex-row justify-content-between ">
                    <p class="resume-title"><?=$obj->getName()?></p>
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
            <div class="d-flex flex-column justify-content-between ms-auto resume-rapid-menu">
                <a onclick='User.changeBookmark(this);' data-classe='spell' data-uniqid='<?=$obj->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                <a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer lkjkjnj un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=spell&a=getPdf&uniqids=<?=$obj->getUniqid()?>'><i class='fa-solid fa-file-pdf'></i></a>
            </div>
        </div>
        <div class="card-hover-showed">
            <div class="d-flex flex-row justify-content-around align-items-baseline flex-wrap">
                <div class="me-1 mb-1"><?=$obj->getIs_magic(Content::FORMAT_BADGE)?></div>
                <div class="me-1 mb-1"><?=$obj->getType(Content::FORMAT_BADGE)?></div>
                <div class="me-1 mb-1"><?=$obj->getCategory(Content::FORMAT_BADGE)?></div>
                <div class="me-1 mb-1"><?=$obj->getPowerful(Content::FORMAT_BADGE)?></div>
                <div class="me-1 mb-1"><?=$obj->getElement(Content::FORMAT_BADGE)?></div>
            </div>
            <?=$obj->getEffect()?>
            <div class="nav-item-divider back-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>"></div>
            <?=$obj->getDescription()?>
            <?php if(!empty($obj->getInvocation())){ ?>
                <div class="nav-item-divider back-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>"></div>
                <div><?=$obj->getInvocation(Content::DISPLAY_LIST)?></div>
            <?php } ?>
        </div>
    </div>
</div>