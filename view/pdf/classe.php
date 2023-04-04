<?php ob_start(); ?>
        
        <table width="100%" valign="middle" align="left">
            <tr>
                <td width="15%" class="starter center">
                    <?php $img = $_SERVER["DOCUMENT_ROOT"]."/".$obj->getFile('logo',new Style(['format' => Content::FORMAT_BRUT])); ?>
                    <img src="<?=$img?>" height="100"><br>
                    Arme priviligiée<br>
                    <?php $img = $_SERVER["DOCUMENT_ROOT"]."/".$obj->getWeapons_of_choice(Content::FORMAT_PATH); ?>
                    <img src="<?=$img?>" height="20">    
                    <p><?=$obj->getTrait()?></p>      
                </td>
                <td width="50%">
                    <table>
                        <tr><td colspan="2"><h1 class="center"><?=$obj->getName()?></h1></td></tr>
                        <tr><td colspan="2" class="starter"><?=$obj->getDescription_fast()?></td></tr>
                        <tr><td colspan="2"><?=$obj->getDescription()?></td></tr>
                    </table>
                </td>
                <td width="35%"><?=$obj->getLife()?></td>
            </tr>
        </table>
        <table width="100%" align="left">
            <tr>
                <td style="padding-bottom:10px;"><?=$obj->getSpecificity()?></td>
            </tr>
        </table>

        <table valign="top" align="left">
          
            <?php $mobs = []; $lign=0; $managerMob = new MobManager; foreach ($obj->getSpell(Content::FORMAT_ARRAY) as $spell) { ;
                if($managerMob->existsId($spell->getId_invocation())){
                    $mobs[] = $spell->getId_invocation(Content::FORMAT_OBJECT);
                }
                if($lign%3 == 0){
                    ?><tr>
                <?php } ?>

                    <?php
                        $obj = $spell;
                        include "view/pdf/object/spell.php";
                    ?>
                    
                <?php if($lign%3 == 2){ ?>
                    </tr>
                <?php }
                $lign++;
            } ?>      
        </table>

        <!-- Mob -->
        <?php if(!empty($mobs)){
            foreach ($mobs as $mob) { ?>

                    <?php
                        $obj = $mob;
                        include "view/pdf/object/mob.php";
                    ?>

            <?php } 
        } ?>

<?php $content = ob_get_clean();