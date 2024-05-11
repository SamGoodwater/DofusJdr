<?php
// Obligatoire
    if(!isset($spells)) {throw new Exception("spells is not set");}else{if(!is_array($spells)) {throw new Exception("spells is not set");}}
    if(!isset($in_competition)){ $in_competition = false; }else{ if(!is_bool($in_competition)){ $in_competition = false; } }

// Conseillé
    if(!isset($size)) { $size = "300";}else{if(!is_string($size) && !is_numeric($size)) {$size = "300";}}
    if(!isset($is_removable)) { $is_removable = false;}else{if(!is_bool($is_removable)) {$is_removable = false;}}
    if(!isset($is_editable)) { $is_editable = false;}else{if(!is_bool($is_editable)) {$is_editable = false;}}
    if($is_removable){
        if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
        if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
    }
?>

<div>
    <div class="d-flex flex-row justify-content-around flex-wrap">
        <?php $i=0; foreach ($spells as $id_group => $spell) { $i++;
            $ran = rand(0,1000000) . "_" . $i;
            if(!$in_competition){
                
                if(Content::exist($spell)){?>

                    <div class="resume-parent-container" style='width:<?=$size?>px;'>
                        <?php if($is_removable){ ?>
                            <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                                <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Détacher le sort" class="p-4 btn-text-red" onclick="if (confirm('Etes vous sûr de vouloir détacher le sort ?')){<?=ucfirst($class_name)?>.update('<?=$uniqid?>',{action:'remove', uniqid:'<?=$spell->getUniqid()?>', id_group:'<?=$id_group?>'},'spell', IS_VALUE);}"><i class="fa-solid fa-times"></i></a>
                            </div>
                        <?php } ?>
                        <?= $spell->getVisual(new Style(["display" => Content::DISPLAY_RESUME, "size" => $size]));?>
                    </div>

                <?php  }
            } else{ ?>
            <div>
                <?php if(is_array($spell)){
                    count($spell) > 1 ? $duo = "duo" : $duo = ""; ?>
                    <div id="resume_in_competition<?=$ran?>" class="resume_in_competition d-flex flex-column">

                        <?php $spl = $spell[0];
                        if(Content::exist($spl)){ ?>
                            <p class="text-center"><span class="size-0-8 light">Groupe de Sort(s) n°<?=$i?></span> - <?=$spl->getLevel(Content::FORMAT_BADGE)?></p>
                        <?php } ?>

                        <div class="d-flex justify-content-around align-items-center">

                            <?php foreach ($spell as $spl) { ?>

                                <div class="resume-parent-container" data-uniqid="<?=$spl->getUniqid();?>">
                                    <?php if($is_editable){ ?>
                                        <div class="text-center" style="position:absolute;top:0px;right:-10px;z-index:2;">
                                            <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modifier le sort" class="p-2 ps-4 btn-text-main" onclick="Classe.initModalUpdateSpell('<?=$id_group?>', '<?=$spl->getUniqid()?>');"><i class="fa-solid fa-edit"></i></a>
                                        </div>
                                    <?php } ?>
                                    <?php if($is_removable){ ?>
                                        <div class="text-center" style="position:absolute;bottom:0px;right:-7px;z-index:2;">
                                            <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Détacher le sort" class="p-2 ps-4 btn-text-red" onclick="if (confirm('Etes vous sûr de vouloir détacher le sort ?')){<?=ucfirst($class_name)?>.update('<?=$uniqid?>',{action:'remove', uniqid:'<?=$spl->getUniqid()?>', id_group:'<?=$id_group?>'},'spell', IS_VALUE, '', true);}"><i class="fa-solid fa-times"></i></a>
                                        </div>
                                    <?php } ?>
                                    <?= $spl->getVisual(new Style(["display" => Content::DISPLAY_RESUME, "size" => $size, "class" => $duo]));?>
                                </div>

                            <?php } ?>

                        </div>
                    </div>

                    <?php if($is_editable) { ?>
                        <div class="d-flex flex-colum align-items-center justify-content-center">
                            <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Ajouter un sort à ce groupe de sorts" class="ms-2 btn btn-sm btn-animate btn-border-grey" onclick="Classe.initModalUpdateSpell('<?=$id_group?>');"><i class="fa-regular fa-plus-square"></i> Ajouter</a>
                        </div>
                    <?php }

                } ?>
            </div>

        <?php }

    } ?>

    </div>
</div>