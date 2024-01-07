<?php
    $display_view = new View();
    if(!isset($title)) {throw new Exception("title is not set");}else{if(!is_string($title)) {throw new Exception("title is not set");}}
    if(!isset($value)) {throw new Exception("value is not set");}else{if(!is_string($value) && !is_numeric($value) && !empty($value)) {throw new Exception("value is not set");}}
    if(!isset($description)) { $description = "";}else{if(!is_string($description)) {$description = "";}}
    if(!isset($critical)) { $critical = "";}else{if(!is_string($critical)) {$critical = "";}}
    if(!isset($element)) { $element = "";}else{if(!Spell::ELEMENT[$element]) {$element = "";}}
    if(!isset($cible)) { $cible = null;}else{if(!in_array($cible, [Spell::CIBLE_ALL, Spell::CIBLE_ALLY, Spell::CIBLE_ENEMY, Spell::CIBLE_SELF])) {$cible = null;}}
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment)) {$comment = "";}}
    if(!isset($variety)) { $variety = Spell::VARIETY_ATTACK;}else{if(!in_array($variety, [Spell::VARIETY_ATTACK, Spell::VARIETY_SAVE])) {$variety = Spell::VARIETY_ATTACK;}}

    if(isset($element)){
        if(!empty($element) && isset(Spell::ELEMENT[$element])){
            $element = Spell::ELEMENT[$element];
            $title .= " (<i class='italic size-0-7'>{$element['name']}</i>)";
            if(empty($color)){
                $color = $element['color'];
            }
        }
    }
?>    

<div>
    <div class="spell_prop_title">
        <?php $display_view->dispatch(
            template_name : "badge",
            data : [
                "content" => $title  ,
                "color" => $color."-d-2",
                "tooltip" => $description,
                "style" => Style::STYLE_BACK
            ], 
            write: true); ?>
    </div>
    <div class="spell_prop_value">
        <?=$value?>
    </div>
    <?php if(!empty($cible)){ ?>
        <div class="spell_prop_cible">
            <?php switch ($cible) {
                case Spell::CIBLE_ALLY:
                    $title = "Allié·e·s";
                    $description = "Le sort affecte toutes les créatures alliées présentes sur la zone d'effet.";
                    $color = "teal";
                break;
                case Spell::CIBLE_ENEMY:
                    $title = "Ennemies";
                    $description = "Le sort affecte toutes les créatures ennemies présentes sur la zone d'effet.";
                    $color = "orange";
                break;
                case Spell::CIBLE_SELF:
                    $title = "Soi-même";
                    $description = "Le sort affecte uniquement vous-même.";
                    $color = "blue";
                break;
                default:
                    $title = "Allié·e·s et ennemies";
                    $description = "Le sort affecte toutes les créatures présentes sur la zone d'effet.";
                    $color = "purple";
                break;
            }
            $display_view->dispatch(
                template_name : "badge",
                data : [
                    "content" => $title,
                    "color" => $color."-d-2",
                    "tooltip" => $description,
                    "style" => Style::STYLE_OUTLINE
                ], 
                write: true); ?>
        </div>
    <?php } ?>
    <?php if(!empty($duration)){ ?>
        <div class="spell_prop_duration">
            <?php if(strlen($duration) < 20){ ?>
                <p>Les effets sont réparties sur <?=$duration?> tours</p>
            <?php } else { ?>
                <p>Les effets sont réparties sur plusieurs tours comme décrit ci-après</p><?=$duration?>    
            <?php } ?>
        </div>
    <?php } ?>
    <?php if(!empty($critical)){ ?>
        <div class="spell_prop_critical">
            <span>Critiques : </span><?=$critical?>
        </div>
    <?php } ?>
    <?php if(!empty($comment) || $variety == Spell::VARIETY_SAVE){ ?>
        <div class="spell_prop_comment">
            <?php if(isset($comment)){ 
                if(!empty($comment)){ ?>
                    <p class='comment'><?=trim($comment)?></p>
                <?php }
            }
            if($variety == Spell::VARIETY_SAVE){ ?>
                <p><small>En cas de réussite au jet de sauvegarde de la cible, les dommages subits sont divisés par deux (arrondi à l'inférieur) et les autres effets ne s'appliquent pas.</small></p>
            <?php } ?>
        </div>
    <?php } ?>
</div>