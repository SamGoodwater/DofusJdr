<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div style="width: <?=$size?>px;">
    <div style="position:relative;">
        <div ondblclick="Classe.open('<?=$obj->getUniqid()?>');" class="card-hover-linked card border-secondary-d-2 border p-2 m-1" style="width: <?=$size?>px;" >
            <div class="d-flex flew-row flex-nowrap justify-content-start">
                <div>
                    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-50"]))?>
                </div>
                <div class="mx-2 d-flex flex-column justify-content-between">
                    <div class="d-flex flex-nowrap justify-content-between">
                        <p class="bold ms-1"><?=$obj->getName()?></p>
                        <div class="me-1"><?=$obj->getWeapons_of_choice(Content::FORMAT_ICON)?></div>
                    </div>
                    <div class="size-0-7 text-grey-d-2"><?=$obj->getDescription_fast()?></div>
                </div>
                <div class="d-flex flex-column justify-content-between ms-auto">
                    <a onclick='User.changeBookmark(this);' data-classe='classe' data-uniqid='<?=$obj->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                    <p class="align-self-end"><a class="btn-text-secondary" title="Afficher les sorts" onclick="Classe.getSpellList('<?=$obj->getUniqid()?>');"><i class="fas fa-magic"></i></a></p>
                    <a data-bs-toggle='tooltip' data-bs-placement='top' title='Générer un pdf' class='text-red-d-2 text-red-l-3-hover' target='_blank' href='index.php?c=classe&a=getPdf&uniqid=<?=$obj->getUniqid()?>'><i class='fas fa-file-pdf'></i></a>
                </div>
            </div>
            <div class="justify-content-center d-flex short-badge-150 flex-wrap"><?=$obj->getTrait(Content::FORMAT_BADGE)?></div>
            <div class="card-hover-showed">
                <p class="card-text"><small class="text-muted"><?=$obj->getDescription_fast()?></small></p>
                <p class="card-text"><?=$obj->getDescription()?></p>
                <p class="text-main-d-2 size-1-2 text-bold mt-4 mb-2">Spécificités de la classe</p>
                <p class="card-text"><?=$obj->getSpecificity()?></p>
                <p class="text-main-d-2 size-1-2 text-bold mt-4 mb-2">Gestion des points de vie</p>
                <p class="card-text"><?=$obj->getLife()?></p>
            </div>
        </div>
    </div>
</div>