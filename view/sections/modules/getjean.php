<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Jean
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
        "title" => "Jean",
        "description" => "Création d'un PNJ lambda",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false,
        "onlyForAdmin" => false,
        "shownListAddInPage" => true,
        "refStockDataOption" => "" // référence des données de l'option dans la page
    );

if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    $manager = new ClasseManager();
    ob_start(); ?>
        <p>Les caractéristiques sont générées en fonction du niveau de manière semi-aléatoire.
            C'est à dire qu'elles sont différentes en fonction du niveau, de la puissance et de la classe du ou de la PNJ.</p>
        <div id="createjean">
            <div class="form-floating mb-2">
                <select onchange="checkSpecific_main(this);" class="form-select" id="classe" aria-label="Classe du ou de la PNJ">
                    <?php 
                        foreach ($manager->getAll() as $classe) {
                            echo "<option value='".$classe->getUniqid()."'>".$classe->getName()."</option>";      
                        }
                    ?>
                </select>
                <label for="classe">Classe du ou de la PNJ</label>
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
            <div class="form-floating">
                <input type="text" class="form-control form-control-main-focus" id="name" placeholder="nom">
                <label for="name">Nom du ou de la PNJ</label>
                <p><small>Facultatif</small></p>
            </div>
            <div class="my-2">
                <label for="powerful" class="form-label badge back-deep-purple-d-3">Puissance <span id="powerful_value">4</span></label>
                <input onchange="$('#powerful_value').text($(this).val());" type="range" class="form-range" min="1" max="9" step="1" value="4" id="powerful">
                <p><small>Sur une échelle de 9 valeurs, avec 1 étant une créature extrémement faible et 9 une créature extrément forte.</small></p>
            </div>
            <div class="my-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="intel">
                    <label class="form-check-label" for="intel">Intelligence <img class='icon-15' src='medias/icons/modules/intel.png'></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="strong">
                    <label class="form-check-label" for="strong">Force <img class='icon-15' src='medias/icons/modules/force.png'></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="chance">
                    <label class="form-check-label" for="chance">Chance <img class='icon-15' src='medias/icons/modules/chance.png'></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="agi">
                    <label class="form-check-label" for="agi">Agilité <img class='icon-15' src='medias/icons/modules/agi.png'></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="sagesse">
                    <label class="form-check-label" for="sagesse">Sagesse <img class='icon-15' src='medias/icons/modules/sagesse.png'></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="vitality">
                    <label class="form-check-label" for="vitality">Vitalité <img class='icon-15' src='medias/icons/modules/vitality.png'></label>
                </div>
            </div>
            <button class="btn btn-sm btn-animate btn-back-secondary" onclick="createJean()">Générer le PDF</button>
            <script>
                function checkSpecific_main(select){
                    var intel = $('#createjean #intel');
                    var strong = $('#createjean #strong');
                    var chance = $('#createjean #chance');
                    var agi = $('#createjean #agi');
                    var sagesse = $('#createjean #sagesse');
                    var vitality = $('#createjean #vitality');
                    intel.attr('checked', false);
                    strong.attr('checked', false);
                    agi.attr('checked', false);
                    chance.attr('checked', false);
                    vitality.attr('checked', false);
                    sagesse.attr('checked', false);
                    switch ($(select).val()) {
                        case '<?=Classe::FECA?>':
                            intel.attr('checked', true);
                            chance.attr('checked', true);
                            vitality.attr('checked', true);
                        break;
                        case '<?=Classe::OSAMODAS?>':
                            intel.attr('checked', true);
                            chance.attr('checked', true);
                            sagesse.attr('checked', true);
                        break;
                        case '<?=Classe::ENUTROF?>':
                            intel.attr('checked', true);
                            agi.attr('checked', true);
                            sagesse.attr('checked', true);
                        break;
                        case '<?=Classe::SRAM?>':
                            strong.attr('checked', true);
                            agi.attr('checked', true);
                            sagesse.attr('checked', true);
                        break;
                        case '<?=Classe::XELOR?>':
                            strong.attr('checked', true);
                            intel.attr('checked', true);
                            sagesse.attr('checked', true);
                        break;
                        case '<?=Classe::ECAFLIP?>':
                            strong.attr('checked', true);
                            chance.attr('checked', true);
                            vitality.attr('checked', true);
                        break;
                        case '<?=Classe::ENIRIPSA?>':
                            intel.attr('checked', true);
                            agi.attr('checked', true);
                            vitality.attr('checked', true);
                        break;
                        case '<?=Classe::IOP?>':
                            strong.attr('checked', true);
                            agi.attr('checked', true);
                            vitality.attr('checked', true);
                        break;
                        case '<?=Classe::CRA?>':
                            intel.attr('checked', true);
                            chance.attr('checked', true);
                            vitality.attr('checked', true);
                        break;
                        case '<?=Classe::SADIDA?>':
                            strong.attr('checked', true);
                            chance.attr('checked', true);
                            sagesse.attr('checked', true);
                        break;
                        case '<?=Classe::SACRIER?>':
                            strong.attr('checked', true);
                            chance.attr('checked', true);
                            vitality.attr('checked', true);
                        break;
                        case '<?=Classe::PANDAWA?>':
                            strong.attr('checked', true);
                            intel.attr('checked', true);
                            vitality.attr('checked', true);
                        break;
                    }
                }
                function createJean(){
                    var name = $('#createjean #name').val();
                    var classe = $('#createjean #classe').val();
                    var level = $('#createjean #level').val();
                    var powerful = $('#createjean #powerful').val();
                    var intel = "";
                    if($('#createjean #intel').is(':checked')){
                    intel = "&intel=1";
                    }
                    var strong = "";
                    if($('#createjean #strong').is(':checked')){
                    strong = "&strong=1";
                    }
                    var chance = "";
                    if($('#createjean #chance').is(':checked')){
                    chance = "&chance=1";
                    }
                    var agi = "";
                    if($('#createjean #agi').is(':checked')){
                    agi = "&agi=1";
                    }
                    var sagesse = "";
                    if($('#createjean #sagesse').is(':checked')){
                    sagesse = "&sagesse=1";
                    }
                    var vitality = "";
                    if($('#createjean #vitality').is(':checked')){
                    vitality = "&vitality=1";
                    }
                    window.open('index.php?c=npc&a=getJean&name='+name+'&classe='+classe+'&level='+level+'&powerful='+powerful+intel+strong+chance+agi+sagesse+vitality, '_blank')
                }
            </script>
        </div>
    <?php $template["content"] = ob_get_clean();

}