<?php 
cssDynamicWriteColor();

function cssDynamicWriteColor(){

    $types = array(
        "text",
        "back",
        "border"
    );

    ?><style>
        <?php 
        $colors = Style::COLOR_ALLOWED;
        $colors[] = "main";$colors[] = "secondary";$colors[] = "tertiary";
        foreach ($colors as $color) {
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

                // TEXT
                    echo ".".$type."-".$color."{".$css.": var(--".$color.")!important;}";
                    echo ".".$type."-".$color."-hover:hover{".$css.": var(--".$color.")!important;}";

                    echo ".".$type."-".$color."-d-1{".$css.": var(--".$color."-d-1)!important;}";
                    echo ".".$type."-".$color."-d-1-hover:hover{".$css.": var(--".$color."-d-1)!important;}";
                    echo ".".$type."-".$color."-d-2{".$css.": var(--".$color."-d-2)!important;}";
                    echo ".".$type."-".$color."-d-2-hover:hover{".$css.": var(--".$color."-d-2)!important;}";
                    echo ".".$type."-".$color."-d-3{".$css.": var(--".$color."-d-3)!important;}";
                    echo ".".$type."-".$color."-d-3-hover:hover{".$css.": var(--".$color."-d-3)!important;}";
                    echo ".".$type."-".$color."-d-4{".$css.": var(--".$color."-d-4)!important;}";
                    echo ".".$type."-".$color."-d-4-hover:hover{".$css.": var(--".$color."-d-4)!important;}";
                    echo ".".$type."-".$color."-d-5{".$css.": var(--".$color."-d-5)!important;}";
                    echo ".".$type."-".$color."-d-5-hover:hover{".$css.": var(--".$color."-d-5)!important;}";

                    echo ".".$type."-".$color."-l-1{".$css.": var(--".$color."-l-1)!important;}";
                    echo ".".$type."-".$color."-l-1-hover:hover{".$css.": var(--".$color."-l-1)!important;}";
                    echo ".".$type."-".$color."-l-2{".$css.": var(--".$color."-l-2)!important;}";
                    echo ".".$type."-".$color."-l-2-hover:hover{".$css.": var(--".$color."-l-2)!important;}";
                    echo ".".$type."-".$color."-l-3{".$css.": var(--".$color."-l-3)!important;}";
                    echo ".".$type."-".$color."-l-3-hover:hover{".$css.": var(--".$color."-l-3)!important;}";
                    echo ".".$type."-".$color."-l-4{".$css.": var(--".$color."-l-4)!important;}";
                    echo ".".$type."-".$color."-l-4-hover:hover{".$css.": var(--".$color."-l-4)!important;}";
                    echo ".".$type."-".$color."-l-5{".$css.": var(--".$color."-l-5)!important;}";
                    echo ".".$type."-".$color."-l-5-hover:hover{".$css.": var(--".$color."-l-5)!important;}";

                    echo ".".$type."-".$color."-a-1{".$css.": var(--".$color."-a-1)!important;}";
                    echo ".".$type."-".$color."-a-1-hover:hover{".$css.": var(--".$color."-a-1)!important;}";
                    echo ".".$type."-".$color."-a-2{".$css.": var(--".$color."-a-2)!important;}";
                    echo ".".$type."-".$color."-a-2-hover:hover{".$css.": var(--".$color."-a-2)!important;}";
                    echo ".".$type."-".$color."-a-3{".$css.": var(--".$color."-a-3)!important;}";
                    echo ".".$type."-".$color."-a-3-hover:hover{".$css.": var(--".$color."-a-3)!important;}";
                    echo ".".$type."-".$color."-a-4{".$css.": var(--".$color."-a-4)!important;}";
                    echo ".".$type."-".$color."-a-4-hover:hover{".$css.": var(--".$color."-a-4)!important;}";
            }
            echo ".form-control-".$color."-focus:focus{border-color: var(--".$color.")!important;box-shadow: 0 0 0 .2rem var(--".$color.")!important;}";
        } ?>
    </style><?php
}

