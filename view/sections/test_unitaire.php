<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Tests unitaires frontend
    Version 1.0

    Une section est une partie d'une page.
    $titre : Aide à l'utilisateur pour choisir quel section utilisé
    $description : Aide à l'utilisateur pour choisir quel section utilisé
    $content : contenu affiché dans la page. La variable $this->getContent(), fait référence à l'enregistrement des options dans la base de donnée.
    $option : input permettant d'envoyer des données lors de la création de la section. L'input name doit être "option"
    $editable : booléen permettant de savoir si la section est éditable ou non.
    
    $template_vars est la valeur contenant les options de la section. Elle peut être utilisé dans la variable $content.
    Elle se décompose : 
    $template_vars['content'] : contenu de la section
    $template_vars['uniqid'] : identifiant de la section
*/
if(!isset($template_vars) || !is_array($template_vars)){$template_vars = array();}
if(!isset($template_vars['content'])){ $template_vars['content'] = "";}
if(!isset($template_vars['uniqid'])){ $template_vars['uniqid'] = "";}
if(!isset($template_vars['uniqid_page'])){ $template_vars['uniqid_page'] = "";}
if(!isset($template_vars['get'])){ $template_vars['get'] = Section::GET_SECTION_DESCRIPTION;}
    
    $template = array(
        "title" => "Tests unitaires backend",
        "description" => "Section exécutant les tests unitaires en backend",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => true,
        "shownListAddInPage" => true,
        "refStockDataOption" => "" // référence des données de l'option dans la page
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    ob_start(); ?>
        <div>
            <div style="display:none;">
                <div class="addpage">
                    <input id="name" value="test">
                    <select id="category"><option value="0"></option></select>
                    <input id="switchdropdownadd">
                    <input id="switchpublicadd">
                    <input checked id="switcheditableadd">
                </div>
            </div>

            <div>
                <h1>Tests unitaires backend</h1>
            </div>

<!--             
    Faire fichier JSON
    Parcourir fichier JSON et créer les requetes JS et les ajouter dans la pile
    requetes :
    - ajouter
    - update pour chacun des attributs
    - remove
    - get All et getFromUniqid
    Mettre un système de % en fonction d'où on en est dans la pile
    Afficher les résultats au fur et à mesure
    Si true alors ok, faire un décompte des succès pour chacun des objets et à chaque erreur écrire le message pour l'objet en question
 -->
                <?php try {
                    foreach (glob("model/*.json") as $filename) {
                        $settings = json_decode(file_get_contents($filename), true);
                        $class_name = ucfirst($settings['name']);
                        $obj = new $class_name();
                        $obj->setUniqid(uniqid());
                        $n_success = 0;
                        $n_error = 0;
                        $n_max = 0;

                        ?>
                            <h3 class="mb-2">Modèle : <?=$class_name?></h3>
                            <h5>Mise à jour des propriétés de la classe</h5>
                            <p><a class="btn btn-sm btn-animate btn-text-main" data-bs-toggle="collapse" href="#collapseTest<?=$obj->getUniqid()?>" role="button" aria-expanded="false" aria-controls="collapseTest<?=$obj->getUniqid()?>">Voir les détails</a></p>
                            <div class="collapse" id="collapseTest<?=$obj->getUniqid()?>">
                                <ul>
                                <?php

                                foreach ($settings['property'] as $name => $method) {
                                $data = null;
                                    if(isset($method['type']) && $name != "id" && $name != "uniqid" && $name != "timestamp_add"){
                                        if(in_array($method['type'],["int","float","numeric","decimal"])){
                                            $min = 0; if(isset($method['min'])){$min = $method['min'];}
                                            $max = 100; if(isset($method['max'])){$max = $method['max'];}
                                            $data = rand($min, $max);
                                            
                                        }elseif(in_array($method['type'],["text", "richtext", "varchar"])){
                                            $min = 10;
                                            $max = 20;
                                            switch ($method['type']) {
                                                case 'text':
                                                    $min = 40;
                                                    $max = 100;
                                                break;
                                                case 'richtext':
                                                    $min = 40;
                                                    $max = 100;
                                                    $data = "<p class='bold'>";
                                                break;
                                                case 'varchar':
                                                    $min = 10;
                                                    $max = 20;
                                                break;
                                            }
                                            if(isset($method['min'])){$min = $method['min'];}
                                            if(isset($method['max'])){$max = $method['max'];}
                                            $length = rand($min, $max);
                                            $chars = "abcdefghijklmnopqrstuvwxyz";
                                            for($x = 0; $x < $length; $x++ ) {
                                                $data .= $chars[rand( 0, strlen($chars) - 1 ) ];
                                                if($method['type'] != 'varchar'){
                                                    if(rand(0,10) > 8){
                                                        $data .= " ";
                                                    }
                                                }
                                            }
                                            if($method['type'] == 'richtext'){$data .= "</p>";}
                        
                                        }elseif($method['type'] == 'bool'){
                                            $data = rand(0,1);
                        
                                        }else{
                                            $data = "";
                                        }
                                        $method = "set".ucfirst($name);
                                        ?><li><?=$n_max?> - Propriété : <?=$name?> |<?php
                                        if($obj->$method($data)){
                                            $n_success++;
                                             ?> <span class="text-green-d-2">Succès</span><?php
                                        }else{
                                            $n_error++;
                                            ?> <span class="text-red-d-2">Echec</span><?php
                                        }
                                        ?> : <span class="text-grey-d-2 size-0-8"><?=$data?></span></li><?php
                                        $n_max++;
                                    }
                                }

                                ?>
                                </ul>
                            </div> <!-- Fin du collapse -->
                            <p><span class="text-green-d-2">Succès : <?=$n_success?></span> | <span class="text-red-d-2">Echecs : <?=$n_error?></span></p>
                        
                        <h5>Mise à jour par le controlleur</h5>
                            
                                        
                        <?php
                    } 
                } catch (\Throwable $th) {
                    dump($th);
                }?>


            </script>

        </div>
    <?php $template["content"] = ob_get_clean();

}