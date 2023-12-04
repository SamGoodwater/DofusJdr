<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($style)){ $style = new Style; }else{ if(!get_class($style) == "Style"){ $style = new Style; } }
?>

<div class="card mb-3">
    <div class="row g-0">
        <div class="col-auto selector-image-main">
            <?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_EDITABLE, "class" => "img-back-200"]))?>
            <div class="text-center mt-2">
                <?=$obj->getPowerful(Content::FORMAT_BADGE)?>
                <?=$obj->getHostility(Content::DISPLAY_EDITABLE)?>
                <?=$obj->getSize(Content::DISPLAY_EDITABLE)?>
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

            </div>
        </div>
    </div>
    <div class="card-text my-2"><?=$obj->getTrait(Content::DISPLAY_EDITABLE);?></div>
    <div class="card-text my-2"><?=$obj->getDescription(Content::DISPLAY_EDITABLE);?></div>
    <div class="card-text my-2"><?=$obj->getZone(Content::DISPLAY_EDITABLE);?></div>
    <div class="card-text my-2"><?=$obj->getSpell(Content::DISPLAY_EDITABLE)?></div>
    <div class="card-text my-2"><?=$obj->getCapability(Content::DISPLAY_EDITABLE)?></div>
    <div class="text-right font-size-0-8 m-1"><a class='btn btn-sm btn-animate btn-border-red' onclick="Mob.remove('<?=$obj->getUniqid()?>')"><i class="fa-solid fa-trash"></i> Supprimer</a></p>
</div>



<div class="card mb-3" id="mob<?=$obj->getUniqid()?>">
    <div class="d-flex flex-row justify-content-between align-items-start m-2">
        <div class="selector-image-main"><?=$obj->getFile('logo',new Style(['format' => Content::FORMAT_EDITABLE, "class" => "img-back-200"]))?></div>
        <div>
            <div class="d-flex justify-content-between align-items-baseline">
                <h4><?=$obj->getName(Content::FORMAT_EDITABLE)?></h4>
                <p class="text-center"><?=$obj->getLevel(Content::FORMAT_EDITABLE)?></p>
                <p>
                    <?=$obj->getPowerful(Content::FORMAT_BADGE);?>
                    <?=$obj->getSize(Content::FORMAT_EDITABLE);?>
                    <?=$obj->getHostility(Content::FORMAT_EDITABLE);?>
                    <?=$obj->getTrait(Content::FORMAT_EDITABLE);?>
                </p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="nav-item-divider back-main-l-3 m-0"></div>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getLife(Content::FORMAT_EDITABLE)?>
            <?=$obj->getPa(Content::FORMAT_EDITABLE)?>
            <?=$obj->getPm(Content::FORMAT_EDITABLE)?>
            <?=$obj->getPo(Content::FORMAT_EDITABLE)?>
            <?=$obj->getIni(Content::FORMAT_EDITABLE)?>
            <?=$obj->getMaster_bonus(Content::FORMAT_EDITABLE)?>
        </div>
        <h4 class="text-main-d-1 text-center">Caractéristiques</h4>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getVitality(Content::FORMAT_EDITABLE)?>
            <?=$obj->getSagesse(Content::FORMAT_EDITABLE)?>
            <?=$obj->getStrong(Content::FORMAT_EDITABLE)?>
            <?=$obj->getIntel(Content::FORMAT_EDITABLE)?>
            <?=$obj->getAgi(Content::FORMAT_EDITABLE)?>
            <?=$obj->getChance(Content::FORMAT_EDITABLE)?>
        </div>
        <h4 class="text-main-d-1 text-center">Dommages</h4>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getTouch(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_neutre(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_terre(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_feu(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_air(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_eau(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDo_fixe_multiple(Content::FORMAT_EDITABLE)?>
        </div>
        <h4 class="text-main-d-1 text-center">Protection</h4>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getCa(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDodge_pa(Content::FORMAT_EDITABLE)?>
            <?=$obj->getDodge_pm(Content::FORMAT_EDITABLE)?>
            <?=$obj->getFuite(Content::FORMAT_EDITABLE)?>
            <?=$obj->getTacle(Content::FORMAT_EDITABLE)?>
        </div>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <?=$obj->getRes_neutre(Content::FORMAT_EDITABLE)?>
            <?=$obj->getRes_terre(Content::FORMAT_EDITABLE)?>
            <?=$obj->getRes_feu(Content::FORMAT_EDITABLE)?>
            <?=$obj->getRes_air(Content::FORMAT_EDITABLE)?>
            <?=$obj->getRes_eau(Content::FORMAT_EDITABLE)?>
        </div>
        <div class="nav-item-divider back-main-l-3 m-0"></div>
        <h4 class="text-main-d-1 text-center">Compétences</h4>
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div class="col-auto my-2">
                <p class="text-agi mb-2">Dépendant de l'agilité
                <?=$obj->getAcrobatie(Content::FORMAT_EDITABLE)?>
                <?=$obj->getDiscretion(Content::FORMAT_EDITABLE)?>
                <?=$obj->getEscamotage(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
            <p class="text-force mb-2">Dépendant de la Force
                <?=$obj->getAthletisme(Content::FORMAT_EDITABLE)?>
                <?=$obj->getIntimidation(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-intel mb-2">Dépendant de l'Intelligence
                <?=$obj->getArcane(Content::FORMAT_EDITABLE)?>
                <?=$obj->getHistoire(Content::FORMAT_EDITABLE)?>
                <?=$obj->getInvestigation(Content::FORMAT_EDITABLE)?>
                <?=$obj->getNature(Content::FORMAT_EDITABLE)?>
                <?=$obj->getReligion(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-sagesse mb-2">Dépendant de la Sagesse
                <?=$obj->getDressage(Content::FORMAT_EDITABLE)?>
                <?=$obj->getMedecine(Content::FORMAT_EDITABLE)?>
                <?=$obj->getPerception(Content::FORMAT_EDITABLE)?>
                <?=$obj->getPerspicacite(Content::FORMAT_EDITABLE)?>
                <?=$obj->getSurvie(Content::FORMAT_EDITABLE)?>
            </div>
            <div class="col-auto my-2">
                <p class="text-chance mb-2">Dépendant de la Chance
                <?=$obj->getPersuasion(Content::FORMAT_EDITABLE)?>
                <?=$obj->getRepresentation(Content::FORMAT_EDITABLE)?>
                <?=$obj->getSupercherie(Content::FORMAT_EDITABLE)?>
            </div>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <p class='size-0-7 mb-1'>Mob <?=$obj->getId(Content::FORMAT_BADGE);?> | Créé le <?=$obj->getTimestamp_add(Content::DATE_FR);?> | Modifié le <?=$obj->getTimestamp_updated(Content::DATE_FR);?></p>
        <h4 class="text-main-d-1 text-center">Informations</h4>
        <p><?=$obj->getDescription(Content::FORMAT_EDITABLE);?></p>
        <p class="card-text my-2"><small class="text-muted"><i class="fa-solid fa-map-marker-alt text-main-d-2 me-2"></i> Zone de vie: <?=$obj->getZone(Content::FORMAT_EDITABLE)?></small></p>
        <p><?=$obj->getOther_info(Content::FORMAT_EDITABLE);?></p>
        <div class="d-flex flex-row justify-content-between">
            <?=$obj->getDrop_(Content::FORMAT_EDITABLE)?>
            <?=$obj->getKamas(Content::FORMAT_EDITABLE)?>
        </div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Sorts</h4>
        <div class="dy-2 px-1"><?=$obj->getSpell(Content::FORMAT_EDITABLE);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_spell(Content::FORMAT_EDITABLE);?></div>
        <h4 class="pt-2 text-center">Aptitudes</h4>
        <div class="dy-2 px-1"><?=$obj->getCapability(Content::FORMAT_EDITABLE);?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Équipement</h4>
        <div class="dy-2 px-1"><?=$obj->getItem(Content::FORMAT_EDITABLE);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_item(Content::FORMAT_EDITABLE);?></div>
        <div class="nav-item-divider back-main-d-1"></div>
        <h4 class="pt-2 text-center">Consommables</h4>
        <div class="dy-2 px-1"><?=$obj->getConsumable(Content::FORMAT_EDITABLE);?></div>
        <div class="dy-2 px-1"><?=$obj->getOther_consumable(Content::FORMAT_EDITABLE);?></div>
    </div>
</div>