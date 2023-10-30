<?php
// Obligatoire
    if(!isset($links)) {throw new Exception("links is not set");}else{if(!is_array($links)) {throw new Exception("links is not set");}}
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($input_name)) {throw new Exception("input_name is not set");}else{if(!in_array($input_name, ['consumable', 'item'])) {throw new Exception("input_name is not set");}}
    
// Conseillé
    if(!isset($size)) { $size = "15";}else{if(!is_string($size) && !is_numeric($size)) {$size = "15";}}
    if(!isset($is_editable)) { $is_editable = false;}else{if(!is_bool($is_editable)) {$is_editable = false;}}
    if($is_editable){
        if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
        if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
    }
?>

<div class="d-flex flex-row justify-content-around flex-wrap"> <?php
    if(!empty($links)){
        foreach ($links as $link) { ?>
            <div class="m-2" style="width: <?=$size?>rem;position:relative;">
                <div class="card border border-grey back-white card-hover-linked" style="width: <?=$size?>rem;">
                    <div>
                        <?php if($is_editable){ ?>
                            <div class="text-center" style="position:absolute;top:5px;right:7px;z-index:9;height:30px;width:30px;">
                                <a data-bs-toggle='tooltip' data-bs-placement='bottom' title="Dissocier cet objet de cet hôtel de vente" class="btn-underline-red" style="position:absolute;top:10px;right:10px;z-index:10;" onclick="if (confirm('Etes vous sûr dissocier l\'objet de cet hôtel de vente ?')){<?=ucfirst($class_name)?>.update('<?=$uniqid?>',{action:'remove', uniqid:'<?=$link['obj']->getUniqid()?>'},'<?=$input_name?>', IS_VALUE);}"><i class="fa-solid fa-times"></i></a>
                            </div>
                        <?php } ?>
                        <a class="text-left" style="position:absolute;top:5px;left:5px;" href="<?=$link["obj"]->getFile('logo',new Style(['format' => Content::FORMAT_BRUT]))?>" download="<?=$link["obj"]->getName().'.'.substr(strrchr($link["obj"]->getFile('logo',new Style(['format' => Content::FORMAT_BRUT])),'.'),1);?>"><i class="fa-solid fa-download text-main-d-3 text-main-d-1-hover"></i></a>        
                        <?=$link["obj"]->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-70"]))?>
                        <div class="card-body position-relative" id="view<?=$link['obj']->getUniqid()?>">
                            <span class="ms-1 position-absolute" style="top:-14px;left:5px;"><?=$link["obj"]->getLevel(Content::FORMAT_BADGE)?></span> 
                            <?php if(!empty($link['quantity'])){ ?>
                                <span class="position-absolute translate-middle badge rounded-pill back-main-d-2 size-1" style="top:0px;right:5px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Quantité">
                                    <?=$link['quantity']?><span class="visually-hidden">unread messages</span>
                                </span>
                            <?php } ?>
                            <h2 class="card-title"><?=$link["obj"]->getName()?></h2>
                            <div class="row justify-content-between">
                                <p class="d-flex flex-row justify-content-between flex-wrap">
                                    <span class="me-1"><?=$link["obj"]->getType(Content::FORMAT_BADGE)?></span>
                                    <span class="ms-1"><?=$link["obj"]->getRarity(Content::FORMAT_BADGE)?></span>          
                                </p>
                                <p class="d-flex flex-row   justify-content-around mt-1 badge back-white border border-2 border-kamas-d-4">
                                    <?php if(!empty($link['price'])){ ?>
                                        <span class='text-kamas-d-4 size-1-2' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Prix de l'objet"><?=$link["price"]?> <img class='icon' src='medias/icons/modules/kamas.png'></span>     
                                    <?php } ?>
                                    <?php if(!empty($link['obj']->getPrice())){ ?>
                                        <span class="ms-1 size-0-8 text-grey-d-2 text-right">Prix recommandé :<br><?=$link["obj"]->getPrice()?> <img class='icon-15' src='medias/icons/modules/kamas.png'></span>
                                    <?php } ?>
                                </p>
                            </div>
                            <p class="card-text"><?=$link["obj"]->getEffect()?></p>
                        </div>
                       </div>
                    <div class="card-hover-showed">
                        <div class="nav-divider back-main-d-1"></div>
                        <p class="card-text size-0-8 text-grey-d-2"><?=$link["obj"]->getDescription()?></p>
                        <?php if(!empty($link['comment'])){ ?>
                            <div class="nav-consumable-divider back-main-d-1"></div>
                            <p class="card-text text-red-d-2"><?=$link["comment"]?></p>
                        <?php } ?>
                        <div class="nav-divider back-main-d-1"></div>
                        <?php if($is_editable && ($user->getRight($input_name, User::RIGHT_WRITE) || $user->Is_admin())){ ?>
                            <a class="text-center btn-underline-main" onclick="switchCard('<?=$link['obj']->getUniqid()?>');">Passer en mode édition</a>
                        <?php } ?>
                    </div>
                    <?php if($is_editable && ($user->getRight($input_name, User::RIGHT_WRITE) || $user->Is_admin())){ ?>
                        <div class="card-body" id="modify<?=$link['obj']->getUniqid()?>">
                            <h2 class="card-title"><?=$link["obj"]->getName()?></h2>
                            <div class="form-floating mb-3">
                                <input value="<?=$link['quantity']?>" id="quantityInput<?=$link['obj']->getUniqid()?>" onchange="Shop.update('<?=$uniqid?>',{action:'update', uniqid:'<?=$link['obj']->getUniqid()?>', quantity:$(this).val()},'<?=$input_name?>', IS_VALUE, true);" type="text" class="form-control form-control-main-focus" placeholder="Quantité">
                                <label for="quantityInput<?=$link['obj']->getUniqid()?>">Quantité</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input value="<?=$link['price']?>" id="priceInput<?=$link['obj']->getUniqid()?>" onchange="Shop.update('<?=$uniqid?>',{action:'update', uniqid:'<?=$link['obj']->getUniqid()?>', price:$(this).val()},'<?=$input_name?>', IS_VALUE, true);" type="text" class="form-control form-control-main-focus" placeholder="Prix">
                                <label for="priceInput<?=$link['obj']->getUniqid()?>">Prix</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input value="<?=$link['comment']?>" id="commentInput<?=$link['obj']->getUniqid()?>" onchange="Shop.update('<?=$uniqid?>',{action:'update', uniqid:'<?=$link['obj']->getUniqid()?>', comment:$(this).val()},'<?=$input_name?>', IS_VALUE, true);" type="text" class="form-control form-control-main-focus" placeholder="Commentaire">
                                <label for="commentInput<?=$link['obj']->getUniqid()?>">Commentaire</label>
                            </div>
                            <div>
                                <?php if($user->getRight($input_name, User::RIGHT_WRITE) || $user->Is_admin()){ ?>
                                    <a class="btn btn-sm btn-animate btn-text-secondary" onclick="<?=ucfirst($input_name)?>.open('<?=$link['obj']->getUniqid()?>', Controller.DISPLAY_EDITABLE);">Editer l'objet</a>
                                <?php } ?>
                            </div>
                            <div class="nav-divider back-main-d-1"></div>
                            <a class="text-center btn-underline-main" onclick="switchCard('<?=$link['obj']->getUniqid()?>');">Revenir à la vue descriptive</a>
                        </div>
                    <?php } ?>
                    <script>$("#modify<?=$link['obj']->getUniqid()?>").hide();</script>
                </div>
            </div>
        <?php } ?>
            <?php if($is_editable && ($user->getRight($input_name, User::RIGHT_WRITE) || $user->Is_admin())){ ?>
                <script>
                    function switchCard(uniqid){
                        if($('#view' + uniqid).is(":hidden")){
                            $('#view' + uniqid).show("slow");
                            $('#modify' + uniqid).hide("slow");
                        } else {
                            $('#view' + uniqid).hide("slow");
                            $('#modify' + uniqid).show("slow");
                        }
                    }
                </script>
            <?php } ?>
    <?php }
?> </div> <?php 