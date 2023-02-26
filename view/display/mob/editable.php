<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  View::STYLE_ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div class="card mb-3">
    <div class="row g-0">
        <div class="col-auto">
            <?=$obj->getPath_img(Content::FORMAT_IMAGE, "img-back-200")?>
            <div class="text-center mt-2">
                <?=$obj->getPowerful(Content::FORMAT_BADGE)?>
                <?=$obj->getHostility(Content::DISPLAY_EDITABLE)?>
            </div>
        </div>
        <div class="col">
                <div class="d-flex justify-content-between">
                    <a class="text-grey-d-3" data-bs-toggle="collapse" href="#collapse<?=$obj->getUniqid()?>" role="button" aria-expanded="false" aria-controls="collapseExample">Comment calculer les caractéristiques ?</a>
                    <?=$obj->getUsable(Content::DISPLAY_EDITABLE)?>
                </div>
                <div class="collapse mb-2 size-0-9 text-grey-d-1" id="collapse<?=$obj->getUniqid()?>">
                    <p>
                        Certaines caractéristiques découlent des autres caractéristiques notamment du niveau de la créature.<br>
                        Pour les calculer, les formules sont indiqués sous la forme suivant [n * + / ou - caractéristique].
                    </p>
                    <ul class="size-0-9 text-grey-d-1">
                        <li>n est nombre entier</li>
                        <li>L'opérateur est * ou / pour les mutiplications ou divisions et + ou - pour les additions ou soustractions</li>
                        <li>La caractéristique fait référence à la valeur d'une autre caractéristique.</li>
                    </ul>
                    <p>
                        Lorsque le résultat n'est pas un nombre entier, il faut troncaturer le résultat, c'est à dire arrondir à l'inférieur.
                        Par exemple, si pour les PM de la créature la formule est [niveau / 3] et le niveau est 11, alors le résultat sera 11/3 = 3,66, soit 3 PM.
                    </p>
                </div>

                <div class="row">
                    <div class="col-auto">
                        <?=$obj->getLevel(Content::DISPLAY_EDITABLE)?>
                        <?=$obj->getIni(Content::DISPLAY_EDITABLE)?>
                        <?=$obj->getLife(Content::DISPLAY_EDITABLE)?>
                        <?=$obj->getPa(Content::DISPLAY_EDITABLE)?>
                        <?=$obj->getPm(Content::DISPLAY_EDITABLE)?>
                        <?=$obj->getPo(Content::DISPLAY_EDITABLE)?>
                        <?=$obj->getTouch(Content::DISPLAY_EDITABLE)?>
                    </div>  
                    <div class="col-auto">
                        <?=$obj->getVitality(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getSagesse(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getStrong(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getIntel(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getAgi(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getChance(Content::DISPLAY_EDITABLE);?>
                    </div>   
                    <div class="col-auto">
                        <?=$obj->getCa(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getFuite(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getTacle(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getDodge_pa(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getDodge_pm(Content::DISPLAY_EDITABLE);?>
                    </div> 
                    <div class="col-auto">
                        <?=$obj->getRes_neutre(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getRes_terre(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getRes_feu(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getRes_air(Content::DISPLAY_EDITABLE);?>
                        <?=$obj->getRes_eau(Content::DISPLAY_EDITABLE);?>
                    </div>            
                </div>
                <div class="nav-item-divider back-main-d-1"></div>
                <p class='size-0-7 mb-1'>Mob <?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$obj->getTimestamp_updated(Content::DATE_FR);?></p>
            </div>
        </div>
    </div>
    <div class="card-text my-2"><?=$obj->getTrait(Content::DISPLAY_EDITABLE);?></div>
    <div class="card-text my-2"><?=$obj->getDescription(Content::DISPLAY_EDITABLE);?></div>
    <div class="card-text my-2"><?=$obj->getZone(Content::DISPLAY_EDITABLE);?></div>
    <div class="card-text my-2"><?=$obj->getSpell(Content::DISPLAY_EDITABLE)?></div>
    <div class="text-right font-size-0-8 m-1"><a class='text-red-d-2 text-red-l-3-hover' onclick="Mob.remove('<?=$obj->getUniqid()?>')"><i class="fas fa-trash"></i> Supprimer</a></p>
</div>