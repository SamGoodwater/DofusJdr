<?php
// Obligatoire
    if(!isset($ressources)) {throw new Exception("ressources is not set");}else{if(!is_array($ressources)) {throw new Exception("ressources is not set");}}

// Conseillé
    if(!isset($size)) { $size = "300";}else{if(!is_string($size) && !is_numeric($size)) {$size = "300";}}
    if(!isset($is_removable)) { $is_removable = false;}else{if(!is_bool($is_removable)) {$is_removable = false;}}
    if($is_removable){
        if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
        if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
    }
?>

<div>
    <div class="d-flex flex-row justify-content-around flex-wrap">
        <?php foreach ($ressources as $ressource) { ?>
            <div style="position:relative;width:<?=$size?>px;">
                <?php if($is_removable){ ?>
                    <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                        <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Détacher la ressource" class="p-4 btn-text-red" onclick="if (confirm('Etes vous sûr de vouloir d\'étacher la ressource ?')){<?=ucfirst($class_name)?>.update('<?=$uniqid?>',{action:'remove', uniqid:'<?=$ressource->getUniqid()?>'},'ressource', IS_VALUE);}"><i class="fa-solid fa-times"></i></a>
                    </div>
                <?php } ?>
                <?= $ressource->getVisual(new Style(["display" => Content::DISPLAY_RESUME]));?>
            </div>
        <?php } ?>
    </div>
</div>