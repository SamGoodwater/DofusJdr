<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($is_link)){ $is_link = false; }else{ if(!is_bool($is_link)){ $is_link = false; } }
    $onclick = "";
    if($is_link){
        $onclick = "ondblclick=\"User.open('".$obj->getUniqid()."');\"";
    }    
?>

<p class="text_resume_tooltops-show" data-target="#user<?=$obj->getUniqid()?>"  <?=$onclick?>>
    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_ICON, 'class' => "pe-1"]))?><?=$obj->getName()?>
</p>
<div id="user<?=$obj->getUniqid()?>" class="size-0-8"  style="display:none;">

</div>