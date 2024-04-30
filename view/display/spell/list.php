<?php
// Obligatoire
    if(!isset($spells)) {throw new Exception("spells is not set");}else{if(!is_array($spells)) {throw new Exception("spells is not set");}}
    if(!isset($in_competition)){ $in_competition = false; }else{ if(!is_bool($in_competition)){ $in_competition = false; } }
    if(!isset($add_new_spell)){ $add_new_spell = false; }else{ if(!is_bool($add_new_spell)){ $add_new_spell = false; } }

// Conseillé
    if(!isset($size)) { $size = "300";}else{if(!is_string($size) && !is_numeric($size)) {$size = "300";}}
    if(!isset($is_removable)) { $is_removable = false;}else{if(!is_bool($is_removable)) {$is_removable = false;}}
    if(!isset($is_editable)) { $is_editable = false;}else{if(!is_bool($is_editable)) {$is_editable = false;}}
    if($is_removable){
        if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
        if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
    }
?>

<div>E
    <div class="d-flex flex-row justify-content-around flex-wrap">
        <?php $i=0; foreach ($spells as $spell) { $i++;
            $ran = rand(0,1000000) . "_" . $i;
            if(!$in_competition){
                
                if(Content::exist($spell)){?>

                    <div class="resume-parent-container" style='width:<?=$size?>px;'>
                        <?php if($is_removable){ ?>
                            <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                                <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Détacher le sort" class="p-4 btn-text-red" onclick="if (confirm('Etes vous sûr de vouloir détacher le sort ?')){<?=ucfirst($class_name)?>.update('<?=$uniqid?>',{action:'remove', uniqid:'<?=$spell->getUniqid()?>'},'spell', IS_VALUE);}"><i class="fa-solid fa-times"></i></a>
                            </div>
                        <?php } ?>
                        <?= $spell->getVisual(new Style(["display" => Content::DISPLAY_RESUME, "size" => $size]));?>
                    </div>

                <?php  }
            } else{ ?>
            <div>
                <?php if(Content::exist($spell['spell1'])){?>
                    <p class="text-center"><span class="size-0-8 light">Sort n°<?=$i?></span> - <?=$spell['spell1']->getLevel(Content::FORMAT_BADGE)?></p>
                <?php } elseif(Content::exist($spell['spell2'])){?>
                    <p class="text-center"><span class="size-0-8 light">Sort n°<?=$i?></span> - <?=$spell['spell2']->getLevel(Content::FORMAT_BADGE)?></p>
                <?php } ?>
                
                <?php Content::exist($spell["spell1"]) && Content::exist($spell["spell2"]) ? $duo = "duo" : $duo = "" ?>

                <div id="resume_in_competition<?=$ran?>" class="resume_in_competition">
                    <?php if(Content::exist($spell['spell1'])){?>
                        <div class="resume-parent-container spell1" data-uniqid="<?=$spell['spell1']->getUniqid();?>">
                            <?php if($is_editable){ ?>
                                <div class="text-center" style="position:absolute;top:0px;right:-10px;z-index:2;">
                                    <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modifier le sort" class="p-2 ps-4 btn-text-main" onclick="Classe.initModalUpdateSpell(this, '.spell2');"><i class="fa-solid fa-edit"></i></a>
                                </div>
                            <?php } ?>
                            <?php if($is_removable){ ?>
                                <div class="text-center" style="position:absolute;bottom:0px;right:-7px;z-index:2;">
                                    <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Détacher le sort" class="p-2 ps-4 btn-text-red" onclick="if (confirm('Etes vous sûr de vouloir détacher le sort ?')){<?=ucfirst($class_name)?>.update('<?=$uniqid?>',{action:'remove', uniqid:'<?=$spell['spell1']->getUniqid()?>'},'spell', IS_VALUE, '', true);}"><i class="fa-solid fa-times"></i></a>
                                </div>
                            <?php } ?>
                            <?= $spell['spell1']->getVisual(new Style(["display" => Content::DISPLAY_RESUME, "size" => $size, "class" => $duo]));?>
                        </div>
                    <?php } elseif($is_editable) { ?>
                        <div class="flex-row justify-content-center align-self-center">
                            <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Ajouter un sort à cette emplacement" class="ms-2 btn btn-sm btn-animate btn-border-grey" onclick="Classe.initModalUpdateSpell(this, '.spell2');"><i class="fa-regular fa-plus-square"></i> Ajouter</a>
                        </div>
                    <?php }

                    if(Content::exist($spell['spell1']) && Content::exist($spell['spell2'])){
                        echo "<p class='text-center size-0-6 bold'>ou</p>";
                    }
               
                    if(Content::exist($spell['spell2'])){ ?>
                        <div class="resume-parent-container spell2" data-uniqid="<?=$spell['spell2']->getUniqid();?>">
                            <?php if($is_editable){ ?>
                                <div class="text-center" style="position:absolute;top:0px;right:-10px;z-index:2;">
                                    <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Modifier le sort" class="p-2 ps-4 btn-text-main" onclick="Classe.initModalUpdateSpell(this, '.spell1');"><i class="fa-solid fa-edit"></i></a>
                                </div>
                            <?php } ?>
                            <?php if($is_removable){ ?>
                                <div class="text-center" style="position:absolute;bottom:0px;right:-7px;z-index:2;">
                                    <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Détacher le sort" class="p-2 ps-4 btn-text-red" onclick="if (confirm('Etes vous sûr de vouloir détacher le sort ?')){<?=ucfirst($class_name)?>.update('<?=$uniqid?>',{action:'remove', uniqid:'<?=$spell['spell2']->getUniqid()?>'},'spell', IS_VALUE, '', true);}"><i class="fa-solid fa-times"></i></a>
                                </div>
                            <?php } ?>
                            <?= $spell['spell2']->getVisual(new Style(["display" => Content::DISPLAY_RESUME, "size" => $size, "class" => $duo]));?>
                        </div>
                    <?php } elseif($is_editable) { ?>
                        <div class="flex-row justify-content-center align-self-center">
                            <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Ajouter un sort à cette emplacement" class="ms-2 btn btn-sm btn-animate btn-border-grey" onclick="Classe.initModalUpdateSpell(this, '.spell1');"><i class="fa-regular fa-plus-square"></i> Ajouter</a>
                        </div>
                    <?php } ?>
                </div>

            </div>


        <?php }
    }

        if($add_new_spell){ ?>


        <?php } ?>
    </div>
</div>