<?php 

cssDynamicWriteColors(Style::COLOR_ALLOWED);
cssDynamicWriteColors(Module::COLOR_CUSTOM);
function cssDynamicWriteColors(array $colors){
    $types = array(
        "text",
        "back",
        "border"
    );

    ?><style>
    <?php 
    foreach ($colors as $name => $color) {

        if(is_array($color)){

            foreach ($types as $type) {
                switch ($type) {
                    case 'text':
                        if(count($color) == 2){
                            echo ".".$type."-".$name."{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 49%, var(--".$color[1].") 51%, var(--".$color[1].") 100%);}";
                            echo ".".$type."-".$name."-hover:hover{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 49%, var(--".$color[1].") 51%, var(--".$color[1].") 100%);}";
                        } elseif(count($color) == 3){
                            echo ".".$type."-".$name."{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 32%, var(--".$color[1].") 34%, var(--".$color[1].") 65%, var(--".$color[2].") 67%, var(--".$color[2].") 100%);}";
                            echo ".".$type."-".$name."-hover:hover{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 32%, var(--".$color[1].") 34%, var(--".$color[1].") 65%, var(--".$color[2].") 67%, var(--".$color[2].") 100%);}";
                        } elseif(count($color) == 4){
                            echo ".".$type."-".$name."{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 24%, var(--".$color[1].") 26%, var(--".$color[1].") 49%, var(--".$color[2].") 51%, var(--".$color[2].") 74%, var(--".$color[3].") 76%, var(--".$color[3].") 100%);}";
                            echo ".".$type."-".$name."-hover:hover{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 24%, var(--".$color[1].") 26%, var(--".$color[1].") 49%, var(--".$color[2].") 51%, var(--".$color[2].") 74%, var(--".$color[3].") 76%, var(--".$color[3].") 100%);}";
                        }
                        foreach (["a", "d", "l"] as $y) {
                            $n = 5; if($y == "a"){$n = 4;}
                            for ($i=1; $i <= $n; $i++) {
                                if(count($color) == 2){
                                    echo ".".$type."-".$name."-".$y."-".$i."{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 49%, var(--".$color[1]."-".$y."-".$i.") 51%, var(--".$color[1]."-".$y."-".$i.") 100%);}";
                                    echo ".".$type."-".$name."-".$y."-".$i."-hover:hover{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 49%, var(--".$color[1]."-".$y."-".$i.") 51%, var(--".$color[1]."-".$y."-".$i.") 100%);}";
                                } elseif(count($color) == 3){
                                   echo ".".$type."-".$name."-".$y."-".$i."{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 32%, var(--".$color[1]."-".$y."-".$i.") 34%, var(--".$color[1]."-".$y."-".$i.") 65%, var(--".$color[2]."-".$y."-".$i.") 67%, var(--".$color[2]."-".$y."-".$i.") 100%);}";
                                   echo ".".$type."-".$name."-".$y."-".$i."-hover:hover{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 32%, var(--".$color[1]."-".$y."-".$i.") 34%, var(--".$color[1]."-".$y."-".$i.") 65%, var(--".$color[2]."-".$y."-".$i.") 67%, var(--".$color[2]."-".$y."-".$i.") 100%);}";
                                } elseif(count($color) == 4){
                                    echo ".".$type."-".$name."-".$y."-".$i."{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 24%, var(--".$color[1]."-".$y."-".$i.") 26%, var(--".$color[1]."-".$y."-".$i.") 49%, var(--".$color[2]."-".$y."-".$i.") 51%, var(--".$color[2]."-".$y."-".$i.") 74%, var(--".$color[3]."-".$y."-".$i.") 76%, var(--".$color[3]."-".$y."-".$i.") 100%);}";
                                    echo ".".$type."-".$name."-".$y."-".$i."-hover:hover{background-clip: text!important; -webkit-background-clip: text!important; -moz-background-clip: text!important; -moz-text-fill-color: transparent!important; -webkit-text-fill-color: transparent!important; background : linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 24%, var(--".$color[1]."-".$y."-".$i.") 26%, var(--".$color[1]."-".$y."-".$i.") 49%, var(--".$color[2]."-".$y."-".$i.") 51%, var(--".$color[2]."-".$y."-".$i.") 74%, var(--".$color[3]."-".$y."-".$i.") 76%, var(--".$color[3]."-".$y."-".$i.") 100%);}";
                                }
                            }
                        }
                    break;

                    case 'border':
                        if(count($color) == 2){
                            echo ".".$type."-".$name."{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 49%, var(--".$color[1].") 51%, var(--".$color[1].") 100%);}";
                            echo ".".$type."-".$name."-hover:hover{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 49%, var(--".$color[1].") 51%, var(--".$color[1].") 100%);}";
                        } elseif(count($color) == 3){
                            echo ".".$type."-".$name."{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 32%, var(--".$color[1].") 34%, var(--".$color[1].") 65%, var(--".$color[2].") 67%, var(--".$color[2].") 100%);}";
                            echo ".".$type."-".$name."-hover:hover{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 32%, var(--".$color[1].") 34%, var(--".$color[1].") 65%, var(--".$color[2].") 67%, var(--".$color[2].") 100%);}";
                        } elseif(count($color) == 4){
                            echo ".".$type."-".$name."{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 24%, var(--".$color[1].") 26%, var(--".$color[1].") 49%, var(--".$color[2].") 51%, var(--".$color[2].") 74%, var(--".$color[3].") 76%, var(--".$color[3].") 100%);}";
                            echo ".".$type."-".$name."-hover:hover{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 24%, var(--".$color[1].") 26%, var(--".$color[1].") 49%, var(--".$color[2].") 51%, var(--".$color[2].") 74%, var(--".$color[3].") 76%, var(--".$color[3].") 100%);}";
                        }
                        foreach (["a", "d", "l"] as $y) {
                            $n = 5; if($y == "a"){$n = 4;}
                            for ($i=1; $i <= $n; $i++) {
                                if(count($color) == 2){
                                    echo ".".$type."-".$name."-".$y."-".$i."{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 49%, var(--".$color[1]."-".$y."-".$i.") 51%, var(--".$color[1]."-".$y."-".$i.") 100%);}";
                                    echo ".".$type."-".$name."-".$y."-".$i."-hover:hover{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 49%, var(--".$color[1]."-".$y."-".$i.") 51%, var(--".$color[1]."-".$y."-".$i.") 100%);}";
        
                                } elseif(count($color) == 3){
                                    echo ".".$type."-".$name."-".$y."-".$i."{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 32%, var(--".$color[1]."-".$y."-".$i.") 34%, var(--".$color[1]."-".$y."-".$i.") 65%, var(--".$color[2]."-".$y."-".$i.") 67%, var(--".$color[2]."-".$y."-".$i.") 100%);}";
                                    echo ".".$type."-".$name."-".$y."-".$i."-hover:hover{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 32%, var(--".$color[1]."-".$y."-".$i.") 34%, var(--".$color[1]."-".$y."-".$i.") 65%, var(--".$color[2]."-".$y."-".$i.") 67%, var(--".$color[2]."-".$y."-".$i.") 100%);}";
                                } elseif(count($color) == 4){
                                    echo ".".$type."-".$name."-".$y."-".$i."{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 24%, var(--".$color[1]."-".$y."-".$i.") 26%, var(--".$color[1]."-".$y."-".$i.") 49%, var(--".$color[2]."-".$y."-".$i.") 51%, var(--".$color[2]."-".$y."-".$i.") 74%, var(--".$color[3]."-".$y."-".$i.") 76%, var(--".$color[3]."-".$y."-".$i.") 100%);}";
                                    echo ".".$type."-".$name."-".$y."-".$i."-hover:hover{border-image-slice: 1!important; border-image: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 24%, var(--".$color[1]."-".$y."-".$i.") 26%, var(--".$color[1]."-".$y."-".$i.") 49%, var(--".$color[2]."-".$y."-".$i.") 51%, var(--".$color[2]."-".$y."-".$i.") 74%, var(--".$color[3]."-".$y."-".$i.") 76%, var(--".$color[3]."-".$y."-".$i.") 100%);}";
                                }
                            }
                        }
                    break;

                    case 'back':
                        if(count($color) == 2){
                            echo  ".".$type."-".$name."{background: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 49%, var(--".$color[1].") 51%, var(--".$color[1].") 100%);}";
                            echo  ".".$type."-".$name."-hover:hover{background: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 49%, var(--".$color[1].") 51%, var(--".$color[1].") 100%);}";
                        } elseif(count($color) == 3){
                            echo  ".".$type."-".$name."{background: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 32%, var(--".$color[1].") 34%, var(--".$color[1].") 65%, var(--".$color[2].") 67%, var(--".$color[2].") 100%);}";
                            echo  ".".$type."-".$name."-hover:hover{background: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 32%, var(--".$color[1].") 34%, var(--".$color[1].") 65%, var(--".$color[2].") 67%, var(--".$color[2].") 100%);}";
                        } elseif(count($color) == 4){
                            echo  ".".$type."-".$name."{background: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 24%, var(--".$color[1].") 26%, var(--".$color[1].") 49%, var(--".$color[2].") 51%, var(--".$color[2].") 74%, var(--".$color[3].") 76%, var(--".$color[3].") 100%);}";
                            echo  ".".$type."-".$name."-hover:hover{background: linear-gradient(45deg, var(--".$color[0].") 0%, var(--".$color[0].") 24%, var(--".$color[1].") 26%, var(--".$color[1].") 49%, var(--".$color[2].") 51%, var(--".$color[2].") 74%, var(--".$color[3].") 76%, var(--".$color[3].") 100%);}";
                        }
                        foreach (["a", "d", "l"] as $y) {
                            $n = 5; if($y == "a"){$n = 4;}
                            for ($i=1; $i <= $n; $i++) {
                                if(count($color) == 2){
                                    echo  ".".$type."-".$name."-".$y."-".$i."{background: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 49%, var(--".$color[1]."-".$y."-".$i.") 51%, var(--".$color[1]."-".$y."-".$i.") 100%);}";
                                    echo  ".".$type."-".$name."-".$y."-".$i."-hover:hover{background: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 49%, var(--".$color[1]."-".$y."-".$i.") 51%, var(--".$color[1]."-".$y."-".$i.") 100%);}";
                                } elseif(count($color) == 3){
                                    echo  ".".$type."-".$name."-".$y."-".$i."{background: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 32%, var(--".$color[1]."-".$y."-".$i.") 34%, var(--".$color[1]."-".$y."-".$i.") 65%, var(--".$color[2]."-".$y."-".$i.") 67%, var(--".$color[2]."-".$y."-".$i.") 100%);}";
                                    echo  ".".$type."-".$name."-".$y."-".$i."-hover:hover{background: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 32%, var(--".$color[1]."-".$y."-".$i.") 34%, var(--".$color[1]."-".$y."-".$i.") 65%, var(--".$color[2]."-".$y."-".$i.") 67%, var(--".$color[2]."-".$y."-".$i.") 100%);}";
                                } elseif(count($color) == 4){
                                    echo  ".".$type."-".$name."-".$y."-".$i."{background: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 24%, var(--".$color[1]."-".$y."-".$i.") 26%, var(--".$color[1]."-".$y."-".$i.") 49%, var(--".$color[2]."-".$y."-".$i.") 51%, var(--".$color[2]."-".$y."-".$i.") 74%, var(--".$color[3]."-".$y."-".$i.") 76%, var(--".$color[3]."-".$y."-".$i.") 100%);}";
                                    echo  ".".$type."-".$name."-".$y."-".$i."-hover:hover{background: linear-gradient(45deg, var(--".$color[0]."-".$y."-".$i.") 0%, var(--".$color[0]."-".$y."-".$i.") 24%, var(--".$color[1]."-".$y."-".$i.") 26%, var(--".$color[1]."-".$y."-".$i.") 49%, var(--".$color[2]."-".$y."-".$i.") 51%, var(--".$color[2]."-".$y."-".$i.") 74%, var(--".$color[3]."-".$y."-".$i.") 76%, var(--".$color[3]."-".$y."-".$i.") 100%);}";
                                }
                            }
                        }
                    break;                                       
                }
            }

        } else {

            if(empty($name) || is_int($name)){ $name = $color;}
            foreach ($types as $type) {
    
                $css = "";
                switch ($type) {
                    case 'text':
                        $css = "color";
                    break;
                    case 'border':
                        $css = "border-color";
                    break;
                    case 'back':
                        $css = "background-color";
                    break;                                       
                }
    
                echo ".".$type."-".$name."{".$css.": var(--".$color.")!important;}";
                echo ".".$type."-".$name."-hover:hover{".$css.": var(--".$color.")!important;}";
    
                foreach (["a", "d", "l"] as $y) {
                    $n = 5; if($y == "a"){$n = 4;}
                    for ($i=1; $i <= $n; $i++) { 
                        echo ".".$type."-".$name."-".$y."-".$i."{".$css.": var(--".$color."-".$y."-".$i.")!important;}";
                        echo ".".$type."-".$name."-".$y."-".$i."-hover:hover{".$css.": var(--".$color."-".$y."-".$i.")!important;}";
                    } 
                }
            }
            echo ".form-control-".$name."-focus:focus{border-color: var(--".$color.")!important;box-shadow: 0 0 0 .2rem var(--".$color.")!important;}";
        }
    } ?>
    </style><?php
}

