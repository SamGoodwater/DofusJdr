<?php
// Obligatoire
    if(!isset($spells)) {throw new Exception("spells is not set");}else{if(!is_object($spells) && !is_array($spells)) {throw new Exception("spells is not set");}}

// Conseillé
    if(!isset($is_link)){ $is_link = false; }else{ if(!is_bool($is_link)){ $is_link = false; } }
    if(!isset($in_competition)){ $in_competition = false; }else{ if(!is_bool($in_competition)){ $in_competition = false; } }

    $onclick = "";
    $new_spells = []; 

    if(is_array($spells)){
       $i=0;

        foreach ($spells as $spell) {

            if(Content::exist($spell)){
                $i++;

                if($is_link){
                    $onclick = "ondblclick=\"Spell.open('".$spell->getUniqid()."');\"";
                }
                
                ob_start(); ?>
                    <p class="text_resume_tooltops-show" data-target="#spell<?=$spell->getUniqid()?>"  <?=$onclick?>>
                        <?=$spell->getFile('logo', new Style(['format' => Content::FORMAT_ICON, "class" => "pe-1"]))?><span <?php if($in_competition){echo "class='competition_name'"; }?>><?=$spell->getName()?></span>
                    </p>
                <?php $new_spells[$i]['text'] = ob_get_clean();
                
                ob_start(); ?>
                    <div id="spell<?=$spell->getUniqid()?>" class="box_resume_tooltips back-<?=$spell->getElement(Content::FORMAT_COLOR_VERBALE)?>-l-4 border-<?=$spell->getElement(Content::FORMAT_COLOR_VERBALE)?>-d-2" style="display:none;">
                        <div class="p-2 m-1">
                            <div class="d-flex flew-row flex-nowrap">
                                <div>
                                    <?=$spell->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-50"]))?>
                                    <p class="mt-1"><?=$spell->getLevel(Content::FORMAT_BADGE)?></p> 
                                </div>

                                <div class="card-body m-1 p-0">
                                    <div class="d-flex flex-row justify-content-between ">
                                        <p class="bold"><?=$spell->getName()?></p>
                                        <div class="d-flex flex-row align-content-center">
                                            <div style="height:18px;"><?=$spell->getPo_editable(Content::FORMAT_ICON)?></div>
                                            <div><?=$spell->getPa(Content::FORMAT_ICON)?></div>
                                        </div>
                                    </div>
                                    <p class="d-flex flex-row justify-content-around align-items-center">
                                        <?=$spell->getPo(Content::FORMAT_ICON)?> 
                                        <?=$spell->getSight_line(Content::FORMAT_ICON)?> 
                                        <?=$spell->getFrequency(Content::FORMAT_BADGE)?>
                                        <?=$spell->getArea(Content::FORMAT_BADGE)?>
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex flex-row justify-content-around align-items-baseline flex-wrap">
                                <div class="me-1 mb-1"><?=$spell->getIs_magic(Content::FORMAT_BADGE)?></div>
                                <div class="me-1 mb-1"><?=$spell->getType(Content::FORMAT_BADGE)?></div>
                                <div class="me-1 mb-1"><?=$spell->getCategory(Content::FORMAT_BADGE)?></div>
                                <div class="me-1 mb-1"><?=$spell->getPowerful(Content::FORMAT_BADGE)?></div>
                                <div class="me-1 mb-1"><?=$spell->getElement(Content::FORMAT_BADGE)?></div>
                            </div>
                            <?=$spell->getEffect()?>
                            <div class="m-2 nav-item-divider back-<?=$spell->getElement(Content::FORMAT_COLOR_VERBALE)?>"></div>
                            <?=$spell->getDescription()?>
                            <?php if(!empty($spell->getInvocation())){ ?>
                                <div class="m-2 nav-item-divider back-<?=$spell->getElement(Content::FORMAT_COLOR_VERBALE)?>"></div>
                                <div><?=$spell->getInvocation(Content::DISPLAY_LIST)?></div>
                            <?php } ?>
                        </div>
                    </div>
                <?php $new_spells[$i]['box'] = ob_get_clean();

            }

        }

    }

$in_competition ? print("<div class='display-text_in_competition'>") : print('');

if(is_array($new_spells) && !empty($new_spells)){
    echo "<ul class='list-unstyled'> ";
        foreach ($new_spells as $spell) {
            echo("<li>" . $spell['text'] . "</li>");
            echo($spell['box']);
        }
    echo "</ul>";
}

$in_competition ? print('</div>') : print('');
