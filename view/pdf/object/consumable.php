<td>
    <table border="0" cellspacing="0">
        <tr>
            <?php 
                $file_img = New File($obj['obj']->getFile('logo', new Style(['format' => Content::FORMAT_BRUT])));
                $img = "data:image/".$file_img->getExtention().";base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $file_img->getPath()));
            ?>
            <td align="left"><img src="<?=$img?>" height="50"></td>
            <td><h3><?=$obj['obj']->getName()?></h3></td>
        </tr>
        <tr>
            <td><span class="starter"><?=$obj['obj']->getType(Content::FORMAT_BADGE)?></span></td>
            <td><span class="starter">Niveau : </span> <?=$obj['obj']->getLevel()?></td>
            <td><span class="starter">quantité : </span><?=$obj['quantity']?></td>
        </tr>
        <tr>
            <td><span class="starter"><?=$obj['obj']->getRarity(Content::FORMAT_BADGE)?></span></td>
            <td><span class="text-kamas"><?=$obj['price']?></span> <img src="<?=$icons['kamas']?>" height="17"></td>
        </tr>
        <tr>
            <td colspan="2"><?=$obj['obj']->getEffect()?></td>
        </tr>
        <tr>
            <td colspan="2"><span class="starter"><?=$obj['obj']->getDescription()?></span></td>
        </tr>
        <tr>
            <td colspan="2"><span style="color:#b71c1c;">Commentaire : <?=$obj['comment']?></span></td>
        </tr>
    </table>
</td>