<?php ob_start(); ?>      
        
        <table valign="top" align="left">
          
            <?php $lign=0; foreach ($spells as $spell) {
                if($lign%3 == 0){ ?>
                    <tr>
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
        
<?php $content = ob_get_clean();