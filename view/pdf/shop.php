<?php ob_start(); ?>

        <table width="100%" valign="middle" cellspacing="2">
            <tr width="100%" style="text-align:center;"> <!-- LIGNE -->
                <td width="60%"><h2><?=$shop->getName()?></h2></td>
                <td width="40%">
                    <table>
                        <tr>
                            <?php if(!empty($shop->getId_seller())){ 
                                $img = $_SERVER["DOCUMENT_ROOT"]."/".$shop->getId_seller(Content::FORMAT_IMAGE)->getClasse(Content::FORMAT_OBJECT)->getPath_img(); ?>
                                <td width="15%" rowspan="2" align="left"><img src="<?=$img?>" height="80"></td>
                                <td><?=$shop->getId_seller(Content::FORMAT_OBJECT)->getName()?></td>
                            <?php } else { ?>
                                <td>Il n'y a pas de marchand·e attitré·e</td>
                            <?php } ?>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr> <!-- LIGNE -->
                <td colspan="2"><?=$shop->getDescription()?></td>
            </tr>
        </table>

        <table><tr valign="middle" align="center" width="100%"><td><h1>Equipements</h1></td></tr></table>
        <table valign="middle" align="center" cellspacing="1" border="1">
            <?php $lign=0; foreach ($shop->getItem(Content::FORMAT_ARRAY) as $item) {
                if($lign%2 == 0){ ?>
                    <tr>
                <?php } ?>

                    <?php
                        $obj = $item;
                        include "view/pdf/object/item.php";
                    ?>

                <?php if($lign%2 == 1){ ?>
                    </tr>
                <?php }
                $lign++;
            } ?>                
        </table>

        <table><tr valign="middle" align="center" width="100%"><td><h1>Consommables</h1></td></tr></table>
        <table valign="middle" align="center" cellspacing="1" border="1">
            <?php $lign=0; foreach ($shop->getConsumable(Content::FORMAT_ARRAY) as $consumable) {
                if($lign%2 == 0){ ?>
                    <tr>
                <?php } ?>

                    <?php
                        $obj = $consumable;
                        include "view/pdf/object/consumable.php";
                    ?>

                <?php if($lign%2 == 1){ ?>
                    </tr>
                <?php }
                $lign++;
            } ?>                
        </table>

<?php $content = ob_get_clean();