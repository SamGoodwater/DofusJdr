<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : JeanMob
    Version 1
    
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
        "title" => "JeanMob",
        "description" => "Création d'une créature lambda",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    $manager = new ClasseManager();
    ob_start(); ?>
        <p>Les caractéristiques sont générées en fonction du niveau,des caractéristiques principales et de la puissance de manière semi-aléatoire.
            C'est à dire qu'elles sont différentes en fonction du niveau, des caractéristiques et de la puissance de la créature.</p>
        <div id="createjean">
            <div class="form-floating">
                <input type="text" class="form-control form-control-main-focus" id="name" placeholder="nom">
                <label for="name">Nom de la créature</label>
            </div>
            <div class="form-floating mb-2">
                <select class="form-select" id="level" aria-label="Niveau du ou de la PNJ">
                    <?php 
                        for ($i=1; $i <= 20 ; $i++) {
                            echo "<option value='".$i."'>Niveau ".$i."</option>";      
                        }
                    ?>
                </select>
                <label for="level">Niveau du ou de la PNJ</label>
            </div>
            <div class="my-2">
                <label for="powerful" class="form-label badge back-deep-purple-d-3">Puissance <span id="powerful_value">4</span></label>
                <input onchange="$('#powerful_value').text($(this).val());" type="range" class="form-range" min="1" max="7" step="1" value="4" id="powerful">
                <p><small>Sur une échelle de 7 valeurs, avec 1 étant une créature extrémement faible et 7 une créature extrément forte.</small></p>
            </div>
            <div class="my-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="intel">
                    <label class="form-check-label" for="intel">Intelligence <img class='icon-sm' src='medias/icons/intel.png'></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="strong">
                    <label class="form-check-label" for="strong">Force <img class='icon-sm' src='medias/icons/force.png'></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="chance">
                    <label class="form-check-label" for="chance">Chance <img class='icon-sm' src='medias/icons/chance.png'></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="agi">
                    <label class="form-check-label" for="agi">Agilité <img class='icon-sm' src='medias/icons/agi.png'></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="sagesse">
                    <label class="form-check-label" for="sagesse">Sagesse <img class='icon-sm' src='medias/icons/sagesse.png'></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="vitality">
                    <label class="form-check-label" for="vitality">Vitalité <img class='icon-sm' src='medias/icons/vitality.png'></label>
                </div>
            </div>
            <button class="btn btn-sm btn-back-secondary" onclick="createJeanMob();">Générer le PDF</button>
        </div>
        <script>
            function createJeanMob(){
                var name = $('#createjeanmob #name').val();
                var level = $('#createjeanmob #level').val();
                var powerful = $('#createjeanmob #powerful').val();
                var intel = "";
                if($('#createjeanmob #intel').is(':checked')){
                intel = "&intel=1";
                }
                var strong = "";
                if($('#createjeanmob #strong').is(':checked')){
                strong = "&strong=1";
                }
                var chance = "";
                if($('#createjeanmob #chance').is(':checked')){
                chance = "&chance=1";
                }
                var agi = "";
                if($('#createjeanmob #agi').is(':checked')){
                agi = "&agi=1";
                }
                var sagesse = "";
                if($('#createjeanmob #sagesse').is(':checked')){
                sagesse = "&sagesse=1";
                }
                var vitality = "";
                if($('#createjeanmob #vitality').is(':checked')){
                vitality = "&vitality=1";
                }
                window.open('index.php?c=mob&a=getJeanmob&name='+name+'&level='+level+'&powerful='+powerful+intel+strong+chance+agi+sagesse+vitality, '_blank')
            }
        </script>
    <?php $template["content"] = ob_get_clean();

}