<?php ob_start(); ?>

        <table width="100%" valign="middle" cellspacing="2">
            <tr width="100%" style="text-align:center;"> <!-- LIGNE -->
                <?php 
                    $file_img = New File($obj->getFile('logo', new Style(['format' => Content::FORMAT_BRUT])));
                    $img = "data:image/".$file_img->getExtention().";base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $file_img->getPath()));
                ?>
                <td width="15%" rowspan="2" align="left"><img src="<?=$img?>" height="100"></td>
                <td><h2><?=$obj->getName()?></h2></td>
                <td border="1" class="border-shield back-shield text-shield" width="10%"><h3><span class="starter">Niveau : </span><?=$obj->getLevel()?></h3></td>
                <?php 
                    $file_img = New File($GLOBALS['project']['logo']);
                    $img = "data:image/".$file_img->getExtention().";base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $file_img->getPath()));
                ?>
                <td width="15%" align="right"><img src="<?=$img?>" height="50"></td>
            </tr>
            <tr> <!-- LIGNE -->
                <td colspan="3">
                    <table>
                        <td><span class= "starter">Historique : </span><?=$obj->getHistorical()?></td>
                        <td><span class="starter">Alignement : </span><?=$obj->getAlignment()?></td>
                        <td><span class="starter">Age : </span><?=$obj->getAge()?> | <span class="starter">Taille : </span><?=$obj->getSize()?> | <span class="starter">Poids : </span><?=$obj->getWeight()?></td>
                    </table>
                </td>
            </tr>
        </table>

        <table width="100%" valign="middle" cellspacing="2">
            <tr> <!-- LIGNE -->
                <td><?=$obj->getClasse(Content::FORMAT_OBJECT)->getName()?></td>
                <td colspan="2"><span class="starter">Histoire : </span><?=$obj->getStory()?></td>
            </tr>  
            <tr> <!-- LIGNE -->
                <td colspan="3"><span class="starter">Traits : </span><?php
                    $traits = array_filter(array_merge( explode(",", $obj->getTrait()), explode(",", $obj->getClasse(Content::FORMAT_OBJECT)->getTrait()) ));
                    foreach ($traits as $trait) { ?>
                        <span><?=$trait?> | </span>
                    <?php } ?> 
                </td>
            </tr>
        </table>

        <table width="100%" valign="middle" align="center" cellspacing="2" border="1">
            <tr> <!-- LIGNE -->
                <td class="center back-life border-life">
                    <span class="starter">Points de vie / points de vie max</span><br>
                    <span class="enavant text-vitality right"> / <?=$obj->getLife()?></span>
                </td>
                <td class="center back-pa border-pa">
                    <span class="starter">PA</span><br>
                    <img src="<?=$icons['pa']?>" height="17">
                    <span class="enavant text-pa"><?=$obj->getPa()?></span>
                </td>
                <td class="center back-pm border-pm">
                    <span class="starter">PM</span><br>
                    <img src="<?=$icons['pm']?>" height="17">
                    <span class="enavant text-pm"><?=$obj->getPm()?></span>
                </td>
                <td class="center back-po border-po">
                    <span class="starter">PO</span><br>
                    <img src="<?=$icons['po']?>" height="17">
                    <span class="enavant text-pm"><?=$obj->getPo()?></span>
                </td>
                <td class="center back-ini border-ini">
                    <span class="starter">Ini</span><br>
                    <img src="<?=$icons['ini']?>" height="17">
                    <span class="enavant text-ini"><?=$obj->getIni() + $obj->getIntel()?></span><br>
                    <span class="starter"><?=$obj->getIni()?> (bonus) + <?=$obj->getIntel()?> (mod. Intel)</span>
                </td>
                <td class="center back-invocation border-invocation">
                    <span class="starter">Invocation</span><br>
                    <img src="<?=$icons['invocation']?>" height="17">
                    <span class="enavant text-invocation"><?=1+$obj->getInvocation()?></span><br>
                    <span class="starter">1 + <?=$obj->getInvocation()?> (bonus)</span>
                </td>
                <td class="center back-touch border-touch">
                    <span class="starter">Bonus du touche</span><br>
                    <span class="enavant text-touch"><?=1+$obj->getTouch()?></span>
                </td>
            </tr>
            <tr>
                <td valign="top" class="center back-shield border-shield">
                    <span class="starter">Pts de bouclier</span><br>
                </td>
                <td valign="top" class="center back-pa border-pa">
                    <span class="starter">Pts de vie temporaire</span><br>
                </td>
                <td class="center back-ca border-ca">
                    <span class="starter">CA</span><br>
                    <img src="<?=$icons['ca']?>" height="17">
                    <span class="enavant text-ca"><?=10+$obj->getCa()+$obj->getVitality()?></span><br>
                    <span class="starter">10 + <?=$obj->getCa()?> (bonus) + <?=$obj->getVitality()?> (mod. Vitalité)</span>
                </td>
                <td class="center back-pa border-pa">
                    <span class="starter">Esquive PA</span><br>
                    <img src="<?=$icons['dodge_pa']?>" height="17">
                    <span class="enavant text-pa"><?=10+$obj->getDodge_pa()+$obj->getSagesse()?></span><br>
                    <span class="starter">10 + <?=$obj->getDodge_pa()?> (bonus) + <?=$obj->getSagesse()?> (mod. Sagesse)</span>
                </td>
                <td class="center back-pm border-pm">
                    <span class="starter">Esquive PM</span><br>
                    <img src="<?=$icons['dodge_pm']?>" height="17">
                    <span class="enavant text-pm"><?=10+$obj->getDodge_pm()+$obj->getSagesse()?></span><br>
                    <span class="starter">10 + <?=$obj->getDodge_pm()?> (bonus) + <?=$obj->getSagesse()?> (mod. Sagesse)</span>
                </td>
                <td class="center back-fuite border-fuite">
                    <span class="starter">Fuite</span><br>
                    <img src="<?=$icons['fuite']?>" height="17">
                    <span class="enavant text-fuite"><?=$obj->getFuite()+$obj->getAgi()?></span><br>
                    <span class="starter"><?=$obj->getFuite()?> (bonus) + <?=$obj->getAgi()?> (mod. Agi)</span>
                </td>
                <td class="center back-tacle border-tacle">
                    <span class="starter">Tacle</span><br>
                    <img src="<?=$icons['tacle']?>" height="17">
                    <span class="enavant text-tacle"><?=$obj->getTacle()+$obj->getChance()?></span><br>
                    <span class="starter"><?=$obj->getTacle()?> (bonus) + <?=$obj->getChance()?> (mod. Chance)</span>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td>
                    <table valign="middle" align="center" cellspacing="2" border="1">
                        <tr> <!-- LIGNE -->
                            <td width="150px" class="center back-vitality border-vitality">
                                <span class="starter">Mod. Vitalité</span><br>
                                <img src="<?=$icons['vitality']?>" height="17">
                                <span class="enavant text-vitality"><?=$obj->getVitality()?></span>
                            </td>
                        </tr>
                        <tr> <!-- LIGNE -->
                            <td width="150px" class="center back-sagesse border-sagesse">
                                <span class="starter">Mod. Sagesse</span><br>
                                <img src="<?=$icons['sagesse']?>" height="17">
                                <span class="enavant text-sagesse"><?=$obj->getSagesse()?></span>
                            </td>
                        </tr>
                        <tr> <!-- LIGNE -->
                            <td width="150px" class="center back-force border-force">
                                <span class="starter">Mod. Force</span><br>
                                <img src="<?=$icons['force']?>" height="17">
                                <span class="enavant text-force"><?=$obj->getStrong()?></span>
                            </td>
                        </tr>
                        <tr> <!-- LIGNE -->
                            <td width="150px" class="center back-intel border-intel">
                                <span class="starter">Mod. Intel</span><br>
                                <img src="<?=$icons['intel']?>" height="17">
                                <span class="enavant text-intel"><?=$obj->getIntel()?></span>
                            </td>
                        </tr>
                        <tr> <!-- LIGNE -->
                            <td width="150px" class="center back-agi border-agi">
                                <span class="starter">Mod. Agi</span><br>
                                <img src="<?=$icons['agi']?>" height="17">
                                <span class="enavant text-agi"><?=$obj->getAgi()?></span>
                            </td>
                        </tr>
                        <tr> <!-- LIGNE -->
                            <td width="150px" class="center back-chance border-chance">
                                <span class="starter">Mod. Chance</span><br>
                                <img src="<?=$icons['chance']?>" height="17">
                                <span class="enavant text-chance"><?=$obj->getChance()?></span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table valign="middle" align="center" cellspacing="2" border="1"> <!-- Compétence -->
                        <tr>
                            <td class="center back-agi border-agi">
                                <span class="starter">Acrobatie</span><br>
                                <span class="enavant text-agi"><?=$obj->getAcrobatie() + $obj->getAgi()?></span><br>
                                <span class="starter"><?=$obj->getAcrobatie()?> (bonus) + <?=$obj->getAgi()?> (mod. Agi)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-agi border-agi">
                                <span class="starter">Discrétion</span><br>
                                <span class="enavant text-agi"><?=$obj->getDiscretion() + $obj->getAgi()?></span><br>
                                <span class="starter"><?=$obj->getDiscretion()?> (bonus) + <?=$obj->getAgi()?> (mod. Agi)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-agi border-agi">
                                <span class="starter">Escamotage</span><br>
                                <span class="enavant text-agi"><?=$obj->getEscamotage() + $obj->getAgi()?></span><br>
                                <span class="starter"><?=$obj->getEscamotage()?> (bonus) + <?=$obj->getAgi()?> (mod. Agi)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-force border-force">
                                <span class="starter">Athlétisme</span><br>
                                <span class="enavant text-force"><?=$obj->getAthletisme() + $obj->getStrong()?></span><br>
                                <span class="starter"><?=$obj->getAthletisme()?> (bonus) + <?=$obj->getStrong()?> (mod. Force)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-force border-force">
                                <span class="starter">Intimidation</span><br>
                                <span class="enavant text-force"><?=$obj->getIntimidation() + $obj->getStrong()?></span><br>
                                <span class="starter"><?=$obj->getIntimidation()?> (bonus) + <?=$obj->getStrong()?> (mod. Force)</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table valign="middle" align="center" cellspacing="2" border="1"> <!-- Compétence -->
                        <tr>
                            <td class="center back-intel border-intel">
                                <span class="starter">Arcane</span><br>
                                <span class="enavant text-intel"><?=$obj->getArcane() + $obj->getIntel()?></span><br>
                                <span class="starter"><?=$obj->getArcane()?> (bonus) + <?=$obj->getIntel()?> (mod. Intel)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-intel border-intel">
                                <span class="starter">Histoire</span><br>
                                <span class="enavant text-intel"><?=$obj->getHistoire() + $obj->getIntel()?></span><br>
                                <span class="starter"><?=$obj->getHistoire()?> (bonus) + <?=$obj->getIntel()?> (mod. Intel)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-intel border-intel">
                                <span class="starter">Religion</span><br>
                                <span class="enavant text-intel"><?=$obj->getReligion() + $obj->getIntel()?></span><br>
                                <span class="starter"><?=$obj->getReligion()?> (bonus) + <?=$obj->getIntel()?> (mod. Intel)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-intel border-intel">
                                <span class="starter">Investigation</span><br>
                                <span class="enavant text-intel"><?=$obj->getInvestigation() + $obj->getIntel()?></span><br>
                                <span class="starter"><?=$obj->getInvestigation()?> (bonus) + <?=$obj->getIntel()?> (mod. Intel)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-intel border-intel">
                                <span class="starter">Nature</span><br>
                                <span class="enavant text-intel"><?=$obj->getNature() + $obj->getIntel()?></span><br>
                                <span class="starter"><?=$obj->getNature()?> (bonus) + <?=$obj->getIntel()?> (mod. Intel)</span>
                            </td>
                        </tr>

                    </table>
                </td>
                <td>
                    <table valign="middle" align="center" cellspacing="2" border="1"> <!-- Compétence -->
                        <tr>
                            <td class="center back-sagesse border-sagesse">
                                <span class="starter">Dressage</span><br>
                                <span class="enavant text-sagesse"><?=$obj->getDressage() + $obj->getSagesse()?></span><br>
                                <span class="starter"><?=$obj->getDressage()?> (bonus) + <?=$obj->getSagesse()?> (mod. Sagesse)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-sagesse border-sagesse">
                                <span class="starter">Médecine</span><br>
                                <span class="enavant text-sagesse"><?=$obj->getMedecine() + $obj->getSagesse()?></span><br>
                                <span class="starter"><?=$obj->getMedecine()?> (bonus) + <?=$obj->getSagesse()?> (mod. Sagesse)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-sagesse border-sagesse">
                                <span class="starter">Perception</span><br>
                                <span class="enavant text-sagesse"><?=$obj->getPerception() + $obj->getSagesse()?></span><br>
                                <span class="starter"><?=$obj->getPerception()?> (bonus) + <?=$obj->getSagesse()?> (mod. Sagesse)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-sagesse border-sagesse">
                                <span class="starter">Perspicacité</span><br>
                                <span class="enavant text-sagesse"><?=$obj->getPerspicacite() + $obj->getSagesse()?></span><br>
                                <span class="starter"><?=$obj->getPerspicacite()?> (bonus) + <?=$obj->getSagesse()?> (mod. Sagesse)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-sagesse border-sagesse">
                                <span class="starter">Survie</span><br>
                                <span class="enavant text-sagesse"><?=$obj->getSurvie() + $obj->getSagesse()?></span><br>
                                <span class="starter"><?=$obj->getSurvie()?> (bonus) + <?=$obj->getSagesse()?> (mod. Sagesse)</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table valign="middle" align="center" cellspacing="2" border="1"> <!-- Compétence -->
                        <tr>
                            <td class="center back-chance border-chance">
                                <span class="starter">Persuasion</span><br>
                                <span class="enavant text-chance"><?=$obj->getPersuasion() + $obj->getChance()?></span><br>
                                <span class="starter"><?=$obj->getPersuasion()?> (bonus) + <?=$obj->getChance()?> (mod. Chance)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-chance border-chance">
                                <span class="starter">Représentation</span><br>
                                <span class="enavant text-chance"><?=$obj->getRepresentation() + $obj->getChance()?></span><br>
                                <span class="starter"><?=$obj->getRepresentation()?> (bonus) + <?=$obj->getChance()?> (mod. Chance)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="center back-chance border-chance">
                                <span class="starter">Supercherie</span><br>
                                <span class="enavant text-chance"><?=$obj->getSupercherie() + $obj->getChance()?></span><br>
                                <span class="starter"><?=$obj->getSupercherie()?> (bonus) + <?=$obj->getChance()?> (mod. Chance)</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table valign="middle" align="center" cellspacing="2" border="1">
                        <tr> <!-- LIGNE -->
                            <td width="70px" class="center back-neutre border-neutre">
                                <span class="starter">Res. Neutre</span><br>
                                <img src="<?=$icons['res_neutre']?>" height="17">
                                <span class="enavant text-neutre"><?=$obj->getRes_neutre()?></span>
                            </td>
                        </tr>
                        <tr> <!-- LIGNE -->
                            <td width="70px" class="center back-terre border-terre">
                                <span class="starter">Res. Terre</span><br>
                                <img src="<?=$icons['res_terre']?>" height="17">
                                <span class="enavant text-terre"><?=$obj->getRes_terre()?></span>
                            </td>
                        </tr>
                        <tr> <!-- LIGNE -->
                            <td width="70px" class="center back-feu border-feu">
                                <span class="starter">Res. Feu</span><br>
                                <img src="<?=$icons['res_feu']?>" height="17">
                                <span class="enavant text-feu"><?=$obj->getRes_feu()?></span>
                            </td>
                        </tr>
                        <tr> <!-- LIGNE -->
                            <td width="70px" class="center back-air border-air">
                                <span class="starter">Res. Air</span><br>
                                <img src="<?=$icons['res_air']?>" height="17">
                                <span class="enavant text-air"><?=$obj->getRes_air()?></span>
                            </td>
                        </tr>
                        <tr> <!-- LIGNE -->
                            <td width="70px" class="center back-eau border-eau">
                                <span class="starter">Res. Eau</span><br>
                                <img src="<?=$icons['res_eau']?>" height="17">
                                <span class="enavant text-eau"><?=$obj->getRes_eau()?></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table width="100%" valign="middle" cellspacing="2">
            <tr> <!-- LIGNE -->
                <td><span class="starter">Informations : </span><?=$obj->getOther_info()?></td>
                <td class="right"><span class="starter">Objets récupérables : </span><?=$obj->getDrop_()?></td>
            </tr>
            <tr> <!-- LIGNE -->
                <td><span class="starter">Equipements : </span><?=$obj->getOther_equipment()?></td>
                <td width="100px" class="right back-kamas border-kamas">
                    <span class="starter center">Kamas</span><br>
                    <span class="enavant text-kamas"><?=$obj->getKamas()?></span>
                    <img src="<?=$icons['kamas']?>" height="17">
                </td>
            </tr>
            <tr> <!-- LIGNE -->
                <td><span class="starter">Consommables : </span><?=$obj->getOther_consomable()?></td>
            </tr>
            <tr> <!-- LIGNE -->
                <td><span class="starter">Autres sorts : </span><?=$obj->getOther_spell()?></td>
            </tr>
        </table>

        <table id="item" width="100%" valign="middle" border="1">
            <caption>Objets et consommables</caption>
            <thead>
                <tr>
                    <th width="6%">Nb</th>
                    <th>Objet</th>
                    <th width="12%">Valeur</th>
                </tr>
            </thead>
            <tr class="colored">
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr class="colored">
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr class="colored">
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr class="colored">
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        </table>


<?php $content = ob_get_clean();