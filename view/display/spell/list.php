<?php
// Obligatoire
    if(!isset($spells)) {throw new Exception("spells is not set");}else{if(!is_array($spells)) {throw new Exception("spells is not set");}}

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
        <?php foreach ($spells as $spell) { ?>
            <div style="position:relative;width:<?=$size?>px;">
                <?php if($is_removable){ ?>
                    <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                        <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Détacher le sort" class="p-4 btn-text-red" onclick="if (confirm('Etes vous sûr d\'étacher le sort ?')){<?=ucfirst($class_name)?>.update('<?=$uniqid?>',{action:'remove', uniqid:'<?=$spell->getUniqid()?>'},'spell', IS_VALUE);}"><i class="fas fa-times"></i></a>
                    </div>
                <?php } ?>
                <?= $spell->getVisual(Content::DISPLAY_RESUME);?>
            </div>
        <?php } ?>
    </div>
</div>