<?php
// Obligatoire
    if(!isset($links)) {throw new Exception("links is not set");}else{if(!is_array($links)) {throw new Exception("links is not set");}}
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($input_name)) {throw new Exception("input_name is not set");}else{if(!in_array($input_name, ['consumable', 'item'])) {throw new Exception("input_name is not set");}}
    
// Conseillé
    if(!isset($size)) { $size = "300";}else{if(!is_string($size) && !is_numeric($size)) {$size = "300";}}
    if(!isset($is_editable)) { $is_editable = false;}else{if(!is_bool($is_editable)) {$is_editable = false;}}
    if($is_editable){
        if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
        if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
    }
    $view = new View();
?>

<div class="d-flex flex-row justify-content-around flex-wrap"> <?php
    if(!empty($links)){
        foreach ($links as $link) { ?>
            <div class="m-2" style="width: <?=$size?>px;position:relative;">
                <?php if($is_editable){ ?>
                    <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                        <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Dissocier cet objet de ce ou cette PNJ" class="btn-underline-red" style="position:absolute;top:10px;right:10px;z-index:10;" onclick="if (confirm('Etes vous sûr dissocier l\'objet de cet hôtel de vente ?')){<?=ucfirst($class_name)?>.update('<?=$uniqid?>',{action:'remove', uniqid:'<?=$link['obj']->getUniqid()?>'},'<?=$input_name?>', IS_VALUE);}"><i class="fa-solid fa-times"></i></a>
                    </div>
                <?php } ?>

                <?php if(!empty($link['quantity'])){ ?>
                    <span class="position-absolute translate-middle badge rounded-pill back-main-d-2 size-1" style="top:100px;left:40px;z-index:10;" data-bs-toggle="tooltip" data-bs-placement="top" title="Quantité">
                        <?=$link['quantity']?><span class="visually-hidden">unread messages</span>
                    </span>
                <?php }

                $bookmark_icon = Style::ICON_REGULAR;
                if($user->in_bookmark($link['obj'])){
                    $bookmark_icon = Style::ICON_SOLID;
                }
                $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                $view->dispatch(
                    template_name : $input_name."/resume",
                    data : [
                        "obj" => $link["obj"],
                        "bookmark_icon" => $bookmark_icon,
                        "user" => $user,
                        "size" => $size	
                    ], 
                    write: true);
                ?>

                <?php if($is_editable && ($user->getRight($input_name, User::RIGHT_WRITE) || $user->Is_admin())){ ?>
                    <a onclick="switchCard('#settingcard<?=$link['obj']->getUniqid()?>');" class="position-absolute" style="top:100px;right:5px;z-index:1001;" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier l'objet"><i class="fa-solid fa-cog"></i></a>

                    <div id="settingcard<?=$link["obj"]->getUniqid();?>" class="position-absolute back-white border border-main-d-2" style="z-index:1000;width: <?=$size?>px;top:0px;left:0;">
                        <h6><?=$link["obj"]->getName()?></h6>
                        <div class="form-floating mb-1">
                            <input value="<?=$link['quantity']?>" id="quantityInput<?=$link['obj']->getUniqid()?>" onchange="Npc.update('<?=$uniqid?>',{action:'update', uniqid:'<?=$link['obj']->getUniqid()?>', quantity:$(this).val()},'<?=strtolower($input_name)?>', IS_VALUE);" type="text" class="form-control form-control-main-focus" placeholder="Quantité">
                            <label for="quantityInput<?=$link['obj']->getUniqid()?>">Quantité</label>
                        </div>
                    </div>

                    <script>$("#settingcard<?=$link['obj']->getUniqid()?>").hide();</script>
                <?php } ?>
            </div>
            
        <?php } ?>

            <?php if($is_editable && ($user->getRight($input_name, User::RIGHT_WRITE) || $user->Is_admin())){ ?>
                <script>
                    function switchCard(selector){
                        if($(selector).is(":hidden")){
                            $(selector).show("slow");
                        } else {
                            $(selector).hide("slow");
                        }
                    }
                </script>
            <?php } ?>
    <?php }
?> </div> <?php 