<td valign="top" width="33%" class="spacing box-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>">
    <table align="left">
        <tr>
            <?php $img = $_SERVER["DOCUMENT_ROOT"]."/".$obj->getFile('logo',new Style(['format' => Content::FORMAT_BRUT])); ?>
            <td width="20%"><img src="<?=$img?>" height="50"></td>
            <td width="80%" class="relative baseline left" align="left">
                <div>
                    <h3 class="left"><?=$obj->getName()?></h3>
                    <p>
                        <?php if($obj->getIs_magic()){?>
                            <span class='badge magic'>Magique</span>
                        <?php } else { ?>
                            <span class='badge physic'>Physique</span>
                        <?php } ?>
                        <span class="badge powerfull">Puissance <?=$obj->getPowerful(Content::FORMAT_TEXT);?></span>
                    </p>
                </div>
                <div class="absolute" style="top:10px;right:35px;">
                    <?php $img = $_SERVER["DOCUMENT_ROOT"]."/medias/icons/pa.png"; ?>
                    <div class="relative">
                        <div><img height="30" src="<?=$img?>"></div>
                        <span class="absolute pa" style="top:11px;right:11px;z-index:100;"><?=$obj->getPa()?></span>
                    </div>
                </div>
                <?php $img = $_SERVER["DOCUMENT_ROOT"].$obj->getPo_editable(Content::FORMAT_PATH)?>
                <span class="absolute" style="top:17px;right:3px;"><img height="17" src="<?=$img?>"></span>  
            </td>
        </tr>
        <tr class="baseline center">
            <td valign="middle"><span class="badge level">Niv. <?=$obj->getLevel()?></span></td>
            <td valign="middle" class="baseline center">
                <span class='po'><?=$obj->getPo()?></span>
                <?php $img = $_SERVER["DOCUMENT_ROOT"].$obj->getSight_line(Content::FORMAT_PATH)?>
                <img height="13" src="<?=$img?>">
                <span class="badge frequency"><?=$obj->getFrequency()?></span>
            </td>
        </tr>
        <tr>
            <td class="badge" style="color:white;background-color:<?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM[$obj->getElement(Content::FORMAT_COLOR_VERBALE)]."-d-2"]?>;"><?=ucfirst($obj->getElement(Content::FORMAT_TEXT))?></td>
            <td><?php foreach ($obj->getType(Content::FORMAT_TEXT) as $key => $type) { ?>
                    <span class="badge" style="background-color:<?=Style::COLOR_TO_HEX[Style::getColorFromLetter($key) . '-d-2']?>;color:white;"><?=$type?></span>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom:5px; border-bottom:1px solid grey;"><?=$obj->getEffect()?></td>
        </tr>
        <tr>
            <td colspan="2" class="size-0-9"><?=$obj->getDescription()?></td>
        </tr>
    </table>
</td>   