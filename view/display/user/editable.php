<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div class="card mb-3">
    <p class='size-0-7 mb-1'><?=$obj->getIs_admin(Content::FORMAT_BADGE, false);?> Utilisateur <?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Dernière connexion le <?=$obj->getLast_connexion(Content::DATE_FR);?> à <?=$obj->getLast_connexion(Content::TIME_FR);?></p>
    <div class="m-1"><?=$obj->getPseudo(Content::FORMAT_EDITABLE)?></div>
    <div class="m-1 mb-3"><?=$obj->getEmail(Content::FORMAT_EDITABLE)?></div>
    <?php if($user->getRight('user', User::RIGHT_WRITE)){ ?>
        <h3>Modifier les droits</h3>
        <?=$obj->getRights(Content::FORMAT_EDITABLE)?>
    <?php } ?>
    <h3>Modifier le mot de passe</h3>
    <?=$obj->getPassword(Content::FORMAT_EDITABLE)?>
</div>