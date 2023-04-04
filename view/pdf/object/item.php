<td>
    <table border="0" cellspacing="0">
        <tr>
            <?php $img = $_SERVER["DOCUMENT_ROOT"]."/".$obj['obj']->getFile('logo',new Style(['format' => Content::FORMAT_BRUT])); ?>
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
            <?php $img =  $_SERVER["DOCUMENT_ROOT"]."/medias/icons/kamas.png"; ?>
            <td><span class="text-kamas"><?=$obj['price']?></span> <img src="<?=$img?>" height="17"></td>
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