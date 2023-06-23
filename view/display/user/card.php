<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div class="card mb-3 p-3">
    <p class='size-0-7 mb-1'><?=$obj->getIs_admin(Content::FORMAT_BADGE, false);?> Utilisateur·trice <?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Dernière connexion le <?=$obj->getLast_connexion(Content::DATE_FR);?> à <?=$obj->getLast_connexion(Content::TIME_FR);?></p>
    <div class="row">
        <div class="col">
            <h4><?=$obj->getPseudo()?></h4>
            <p class="text-grey-d-2 size-0-8"><?=$obj->getEmail()?></p>
        </div>
        <div class="col-auto">
            <?php if($user->getRight('user', User::RIGHT_WRITE)){ ?>
                <a class='text-main-d-2 text-main-l-3-hover' title='Modifier' onclick="User.open('<?=$obj->getUniqid()?>', Controller.DISPLAY_EDITABLE);"><i class='fa-regular fa-edit'></i></a>
            <?php } ?>
        </div>
    </div>
    <?php if($user->getRight("user", User::RIGHT_WRITE)){ ?>
        <div class="nav-item-divider back-main"></div>
        <h6>Droits</h6>
        <?=$obj->getRights(Content::FORMAT_BADGE)?>
    <?php } ?>
</div>