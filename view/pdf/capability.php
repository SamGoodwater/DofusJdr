<?php ob_start(); ?>      
        
        <table valign="top" align="left">
          
            <?php $lign=0; foreach ($capabilities as $capability) {
                if($lign%3 == 0){ ?>
                    <tr>
                <?php } ?>

                    <?php
                        $obj = $capability;
                        include "view/pdf/object/capability.php";
                    ?> 
                    
                <?php if($lign%3 == 2){ ?>
                    </tr>
                <?php }
                $lign++;
            } ?>      
        </table>
        
<?php $content = ob_get_clean();