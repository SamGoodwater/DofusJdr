<?php 
    $levels = Content::getMinMaxFromFormule($obj->getLevel());
?>
<table valign="middle" cellspacing=3>
    <tr>
        <td>
            <table width="100%" valign="middle" border=0>
                <tr width="100%" style="text-align:center;" border=0> <!-- LIGNE -->
                    <?php $img = $_SERVER["DOCUMENT_ROOT"]."/".$obj->getPath_img(); ?>
                    <td width="15%" rowspan="2"><img src="<?=$img?>" height="100"></td>
                    <td><h2><?=$obj->getName()?></h2></td>
                    <td class="box-level"><h3><span class="starter">Niveau : </span><b class="enavant2"><?=$obj->getLevel()?></b></h3></td>
                </tr>
                <?php if(!empty($obj->getTrait()) || !empty($obj->getHostility()) || !empty($obj->getZone())){ ?>
                    <tr border=0>
                        <?php if(!empty($obj->getTrait())){ ?>
                            <td colspan="3"><span class="starter">Traits : </span><?php
                                foreach (explode(',',$obj->getTrait()) as $trait) { ?>
                                    <span><?=$trait?> | </span>
                                <?php } ?> 
                            </td>
                        <?php } ?>
                        <?php if(!empty($obj->getHostility()) || !empty($obj->getZone())){ ?>
                            <td>
                                <span class="badge frequency"><?=$obj->getHostility(Content::FORMAT_TEXT)?></span>
                                <?=$obj->getZone()?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <?php if(!empty($obj->getDescription())){ ?>
                    <tr border=0>
                        <td colspan="2"><?=$obj->getDescription()?></td>
                    </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
    <tr border=0>
        <td>
            <table border=0>
                <tr border=0>
                    <td>
                        <table>
                            <tr>
                                <td valign=top class="center box-life p1">
                                    <span class="starter">Points de vie / points de vie max</span><br>
                                    <span class="enavant2"><?=$obj->getLife()?></span><br>
                                    <p class="p3"></p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getLife())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getLife(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="center box-shield p1">
                                    <span class="starter mb5">Pts de bouclier</span><br>
                                    <p class="p4"></p>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="center box-vitality p1">
                                    <span class="starter mb5">Pts de vie temporaire</span><br>
                                    <p class="p4"></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td class="center box-pa">
                                    <span class="starter">PA</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['pa']?>" height="17">
                                        <span class="enavant2 p1"><?=$obj->getPa()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getPa())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getPa(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="center box-pm">
                                    <span class="starter">PM</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['pm']?>" height="17">
                                        <span class="enavant2 p1"><?=$obj->getPm()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getPm())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getPm(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="center box-po">
                                    <span class="starter">PO</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['po']?>" height="17">
                                        <span class="enavant2 p1"><?=$obj->getPo()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getPo())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getPo(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="center box-ini">
                                    <span class="starter">Ini</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['ini']?>" height="17">
                                        <span class="enavant2"><?=$obj->getIni()?></span><br>
                                    </p>
                                    <span class="starter"><?=$obj->getIntel()?> (mod. Intel)</span>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getIntel())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getIntel(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="center box-touch">
                                    <span class="starter">Bonus du touche</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['touch']?>" height="17">
                                        <span class="enavant"><?=$obj->getTouch()?></span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="center box-ca">
                                    <span class="starter">CA</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['ca']?>" height="17">
                                        <span class="enavant2"><?=$obj->getCa()?></span><br>
                                    </p>
                                    <span class="starter">+ 10 + <?=$obj->getVitality()?> (mod. Vitalité)</span>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getCa())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getCa(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table valign="middle" align="center">
                            <tr>
                                <td class="center box-dodge_pa">
                                    <span class="starter">Esquive PA</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['dodge_pa']?>" height="17">
                                        <span class="enavant2"><?=$obj->getDodge_pa()?></span><br>
                                    </p>
                                    <span class="starter">+ 10 + <?=$obj->getSagesse()?> (mod. Sagesse)</span>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getSagesse())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getDodge_pa(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="center box-dodge_pm">
                                    <span class="starter">Esquive PM</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['dodge_pm']?>" height="17">
                                        <span class="enavant2"><?=$obj->getDodge_pm()?></span><br>
                                    </p>
                                    <span class="starter">+ 10 + <?=$obj->getSagesse()?> (mod. Sagesse)</span>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getPa())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getDodge_pm(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="center box-fuite">
                                    <span class="starter">Fuite</span><br>
                                     <p class="p1">
                                        <img src="<?=$icons['fuite']?>" height="17">
                                        <span class="enavant2"><?=$obj->getFuite()?></span><br>
                                     </p>
                                    <span class="starter">+ <?=$obj->getAgi()?> (mod. Agi)</span>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getFuite())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getFuite(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="center box-tacle">
                                    <span class="starter">Tacle</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['tacle']?>" height="17">
                                        <span class="enavant2"><?=$obj->getTacle()?></span><br>
                                    </p>
                                    <span class="starter">+ <?=$obj->getChance()?> (mod. Chance)</span>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getTacle())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getTacle(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table valign="middle" align="center"">
                            <tr> <!-- LIGNE -->
                                <td class="center box-vitality">
                                    <span class="starter">Mod. Vitalité</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['vitality']?>" height="17">
                                        <span class="enavant2"><?=$obj->getVitality()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getVitality())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getVitality(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr> <!-- LIGNE -->
                                <td class="center box-sagesse">
                                    <span class="starter">Mod. Sagesse</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['sagesse']?>" height="17">
                                        <span class="enavant2"><?=$obj->getSagesse()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getSagesse())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getSagesse(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr> <!-- LIGNE -->
                                <td class="center box-strong">
                                    <span class="starter">Mod. Force</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['force']?>" height="17">
                                        <span class="enavant2"><?=$obj->getStrong()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getStrong())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getStrong(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr> <!-- LIGNE -->
                                <td class="center box-intel">
                                    <span class="starter">Mod. Intel</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['intel']?>" height="17">
                                        <span class="enavant2"><?=$obj->getIntel()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getIntel())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getIntel(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr> <!-- LIGNE -->
                                <td class="center box-agi">
                                    <span class="starter">Mod. Agi</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['agi']?>" height="17">
                                        <span class="enavant2"><?=$obj->getAgi()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getAgi())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getAgi(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr> <!-- LIGNE -->
                                <td class="center box-chance">
                                    <span class="starter">Mod. Chance</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['chance']?>" height="17">
                                        <span class="enavant2"><?=$obj->getChance()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getChance())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getChance(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table valign="middle" align="center">
                            <tr> <!-- LIGNE -->
                                <td class="center box-neutre">
                                    <span class="starter">Res. Neutre</span><br>
                                    <p class="pt1">
                                        <img src="<?=$icons['res_neutre']?>" height="17">
                                        <span class="enavant2"><?=$obj->getRes_neutre()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getRes_neutre())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getRes_neutre(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr> <!-- LIGNE -->
                                <td class="center box-terre">
                                    <span class="starter">Res. Terre</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['res_terre']?>" height="17">
                                        <span class="enavant2"><?=$obj->getRes_terre()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getRes_terre())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getRes_terre(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr> <!-- LIGNE -->
                                <td class="center box-feu">
                                    <span class="starter">Res. Feu</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['res_feu']?>" height="17">
                                        <span class="enavant2"><?=$obj->getRes_feu()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getRes_feu())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getRes_feu(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr> <!-- LIGNE -->
                                <td class="center box-air">
                                    <span class="starter">Res. Air</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['res_air']?>" height="17">
                                        <span class="enavant2"><?=$obj->getRes_air()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getRes_air())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getRes_air(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                            <tr> <!-- LIGNE -->
                                <td class="center box-eau">
                                    <span class="starter">Res. Eau</span><br>
                                    <p class="p1">
                                        <img src="<?=$icons['res_eau']?>" height="17">
                                        <span class="enavant2"><?=$obj->getRes_eau()?></span>
                                    </p>
                                    <p> 
                                        <table>
                                        <?php if(!preg_match("/^\d{1,}$/", $obj->getRes_eau())){
                                        $lign=0; for($i=$levels['min']; $i <= $levels['max']; $i++) {
                                            if($lign%6 == 0){
                                                    ?><tr>
                                                <?php } ?>

                                                    <td class="center"><span class="box-calcule"><i><i class="size-0-7">nvx</i><?=$i?></i><br><b><?=Content::getValueFromFormule($obj->getRes_eau(), $i)?></b></span></td>

                                                <?php if($lign%6 == 5){ ?>
                                                    </tr>
                                                <?php }
                                                $lign++;
                                            } } ?>      
                                        </table>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr border=0>
        <table valign="top" align="left" border=0>
            
            <?php $lign=0; foreach ($obj->getSpell(Content::FORMAT_ARRAY) as $spell) { ;
                if($lign%3 == 0){
                    ?><tr>
                <?php } ?>

                    <?php
                        $obj = $spell['obj'];
                        include "view/pdf/object/spell.php";
                    ?>
                    
                <?php if($lign%3 == 2){ ?>
                    </tr>
                <?php }
                $lign++;
            } ?>      
        </table>
    </tr>
</table>