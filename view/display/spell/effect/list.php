<?php
    $display_view = new View();
    if(!isset($propsSortByLevelAndType)) {throw new Exception("propsSortByLevelAndType is not set");}else{if(!is_array($propsSortByLevelAndType)) {throw new Exception("propsSortByLevelAndType is not set");}}
    if(!isset($spell)) {throw new Exception("spell is not set");}else{if(get_class($spell) != "Spell") {throw new Exception("spell is not set");}}
?>    

<div id="effects_array_tabs<?=$spell->getUniqid()?>">
    <ul>
        <?php foreach ($propsSortByLevelAndType as $lvl => $val) { ?>
            <li><a href="#effects_array_tabs-<?=$lvl?>">Niveau <?=$lvl?></a></li>
        <?php } ?>
    </ul>
    <div class="effects_arrays_container_tab">
        <?php foreach ($propsSortByLevelAndType as $lvl => $props) { ?>
            <div id="effects_array_tabs-<?=$lvl?>" class="effects_arrays_tab">
                <?php foreach ($props as $type => $prop_html) {
                    ?> <div class="effects_array_prop">
                        <?=$prop_html?>
                    </div><?php
                } ?>
            </div>
        <?php } ?>
    </div>
</div>
<script>
    $( function() {
        $("#effects_array_tabs<?=$spell->getUniqid()?>").tabs({
            collapsible: true
        });
    } );
</script>