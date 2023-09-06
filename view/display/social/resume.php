<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<a  href="<?=$obj->getLink()?>" 
    target="_blank" 
    id="<?=$style->getId()?>" 
    class="<?=$style->getClass()?>" 
    title="<?=$obj->getName()?> : <?=$obj->getDescription()?>"
    style="width: <?=$style->getSize()?>px;">
    <img src="<?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_BRUT]))?>" alt="">
    <?=$obj->getText()?>
</a>