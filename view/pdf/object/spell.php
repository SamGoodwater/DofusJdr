<td valign="top" width="33%" class="spacing box-<?=$obj->getElement(Content::FORMAT_COLOR_VERBALE)?>">
    <table align="left">
        <tr>
            <?php 
                $file_img = New File($obj->getFile('logo',new Style(['format' => Content::FORMAT_BRUT])));
                $img = "data:image/".$file_img->getExtention().";base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $file_img->getPath()));
            ?>
            <td width="20%"><img src="<?=$img?>" height="50"></td>
            <td width="80%" class="relative baseline left" align="left">
                <div>
                    <h3 class="left"><?=$obj->getName()?></h3>
                    <p>
                        <?php if($obj->getIs_magic()){?>
                            <span class='badge magic'>Wakfu</span>
                        <?php } else { ?>
                            <span class='badge physic'>Physique</span>
                        <?php } ?>
                        <span class="badge powerfull"><?=$obj->getPowerful(Content::FORMAT_TEXT);?></span>
                    </p>
                </div>
                <div class="absolute" style="top:10px;right:35px;">
                    <div class="relative">
                        <div><img height="30" src="<?=$icons["pa"]?>"></div>
                        <span class="absolute pa" style="top:11px;right:11px;z-index:100;"><?=$obj->getPa()?></span>
                    </div>
                </div>
                <?php 
                    $file_img = New File($obj->getPo_editable(Content::FORMAT_PATH));
                    $img = "data:image/".$file_img->getExtention().";base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $file_img->getPath()));
                ?>
                <span class="absolute" style="top:17px;right:3px;"><img height="17" src="<?=$img?>"></span>  
            </td>
        </tr>
        <tr class="baseline center">
            <td valign="middle"><span class="badge level">Niv. <?=$obj->getLevel()?></span></td>
            <td valign="middle" class="baseline center">
                <span class='po'><?=$obj->getPo()?></span>
                <?php 
                    $file_img = New File($obj->getSight_line(Content::FORMAT_PATH));
                    $img = "data:image/".$file_img->getExtention().";base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $file_img->getPath()));
                ?>
                <img height="13" src="<?=$img?>">
                <span class="badge frequency"><?=$obj->getFrequency()?></span>
            </td>
        </tr>
        <tr>
            <?php 
            if(is_array(Style::COLOR_CUSTOM[$obj->getElement(Content::FORMAT_COLOR_VERBALE)])){
                $totalRed = 0;$totalGreen = 0;$totalBlue = 0;
                $numbers_color = count(Style::COLOR_CUSTOM[$obj->getElement(Content::FORMAT_COLOR_VERBALE)]);
                foreach (Style::COLOR_CUSTOM[$obj->getElement(Content::FORMAT_COLOR_VERBALE)] as $color_verbal) {
                    $rgb = Style::hex2rgb(Style::COLOR_TO_HEX[$color_verbal . "-d-4"]);
                    $totalRed += hexdec($rgb['red']);
                    $totalGreen += hexdec($rgb['green']);
                    $totalBlue += hexdec($rgb['blue']);
                }
                $averageRed = round($totalRed / $numbers_color);
                $averageGreen = round($totalGreen / $numbers_color);
                $averageBlue = round($totalBlue / $numbers_color);
                $color = "#" . dechex($averageRed) . dechex($averageGreen) . dechex($averageBlue);
            } else {
                $color = Style::COLOR_TO_HEX[Style::COLOR_CUSTOM[$obj->getElement(Content::FORMAT_COLOR_VERBALE)]."-d-2"];
            } ?>
            <td class="badge" style="color:white;background:<?=$color?>;"><?=ucfirst($obj->getElement(Content::FORMAT_TEXT))?></td>
            <td><?php foreach ($obj->getType(Content::FORMAT_TEXT) as $key => $type) { ?>
                    <span class="badge" style="background:<?=Style::COLOR_TO_HEX[Style::getColorFromLetter($key) . '-d-2']?>;color:white;"><?=$type?></span>
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