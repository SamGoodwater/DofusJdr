class Spell extends Controller{

    static MODEL_NAME = "spell";
    
    static add(){
        let URL = 'index.php?c=spell&a=add';
        let name = $('#modal #addSpell #name').val();
        
        $.post(URL,
            {
                name:name,
            },
            function(data, status)
            {
                if(data['script'] != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout du sort", 'Le sort ' + name + ' a bien été ajouté.', "green" , 3000);
                    $('#modal #addSspell #name').val("");
                    $('#modal').modal("hide");
                    $('#table').bootstrapTable('refresh');
                } else {
                    $('#display_error').text(data['error']);
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data.error, "danger" , 4000);
                }
            },
            "json"
        ); 
    }

    static ELEMENT_NEUTRE = 0;
    static ELEMENT_VITALITY = 1;
    static ELEMENT_SAGESSE = 2;
    static ELEMENT_TERRE = 3;
    static ELEMENT_FEU = 4;
    static ELEMENT_AIR = 5;
    static ELEMENT_EAU = 6;

    // -- EFFECTS --
        // Variety
        static VARIETY_ATTACK = 0;
        static VARIETY_SAVE = 1;
        // Cible
        static CIBLE_ALL = 0;
        static CIBLE_ALLY = 1;
        static CIBLE_ENEMY = 2;
        static CIBLE_SELF = 3;

        static OCCURENCE_TYPE_SEPARATOR = "||";

        static EFFECT_TYPE = {
            'variety': {
                'id': 0,
                'name': 'Variété'
            },
            'touch': {
                'id': 1,
                'name': 'Touche',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':0,
                    'comment':1
                }
            },
            'dd': {
                'id': 2,
                'name': 'Degré de difficulté',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':0,
                    'comment':1
                }
            },
            'damage': {
                'id': 3,
                'name': 'Dommage',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':0,
                    'comment':1
                }
            },
            'health': {
                'id': 4,
                'name': 'Soin',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':0,
                    'comment':1
                }
            },
            'lifesteal': {
                'id': 5,
                'name': 'Vol de vie',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':0,
                    'comment':1
                }
            },
            'effect': {
                'id': 6,
                'name': 'Effet',
                'edit' : {
                    'value':0,
                    'critical':0,
                    'duration':1,
                    'cible':1,
                    'comment':1
                }
            },
            'pa': {
                'id': 7,
                'name': 'PA',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':0,
                    'comment':1
                }
            },
            'state': {
                'id': 8,
                'name': 'Etat',
                'edit' : {
                    'value':0,
                    'critical':0,
                    'duration':1,
                    'cible':1,
                    'comment':1
                }
            },
            'malus_pa': {
                'id': 9,
                'name': 'Malus PA',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':0,
                    'comment':1
                }
            },
            'malus_pm': {
                'id': 10,
                'name': 'Malus PM',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':0,
                    'comment':1
                }
            },
            'malus_po': {
                'id': 11,
                'name': 'Malus PO',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':0,
                    'comment':1
                }
            },
            'malus_ca': {
                'id': 12,
                'name': 'Malus CA',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'malus_touch': {
                'id': 13,
                'name': 'Malus à la Touche',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'malus_dodge_pa': {
                'id': 14,
                'name': "Malus d'Esquive PA",
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'malus_dodge_pm': {
                'id': 15,
                'name': "Malus d'Esquive PM",
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'vulnerability': {
                'id': 16,
                'name': 'Vulnérabilité',
                'edit' : {
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'malus_damage': {
                'id': 17,
                'name': 'Malus de Dommage',
                'edit':{
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'bonus_pa': {
                'id': 18,
                'name': 'Bonus de PA'
            },
            'bonus_pm': {
                'id': 19,
                'name': 'Bonus de PM',
                'edit':{
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'bonus_po': {
                'id': 20,
                'name': 'Bonus de PO',
                'edit':{
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'bonus_ca': {
                'id': 21,
                'name': 'Bonus de CA',
                'edit':{
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'bonus_touch': {
                'id': 22,
                'name': 'Bonus de Touche',
                'edit':{
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'bonus_dodge_pa': {
                'id': 23,
                'name': "Bonus d'Esquive PA",
                'edit':{
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'bonus_dodge_pm': {
                'id': 24,
                'name': "Bonus d'Esquive PM",
                'edit':{
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'resistance': {
                'id': 25,
                'name': 'Résistance',
                'edit':{
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1
                }
            },
            'bonus_damage': {
                'id': 26,
                'name': 'Bonus de Dommage',
                'edit':{
                    'value':1,
                    'critical':1,
                    'duration':0,
                    'cible':1,
                    'comment':1,
                }
            },
            'area': {
                'id': 27,
                'name': "Zone d'effet",
                'edit':{
                    'value':0,
                    'critical':0,
                    'duration':0,
                    'cible':0,
                    'comment':1,
                }
            },
            'po': {
                'id': 28,
                'name': 'Portée',
                'edit':{
                    'value':0,
                    'critical':0,
                    'duration':0,
                    'cible':0,
                    'comment':1,
                }
            },
            'po_editable': {
                'id': 29,
                'name': 'PO modifiable',
                'edit':{
                    'value':0,
                    'critical':0,
                    'duration':0,
                    'cible':0,
                    'comment':1,
                }
            },
            'sight_line': {
                'id': 30,
                'name': 'Ligne de vue',
                'edit':{
                    'value':0,
                    'critical':0,
                    'duration':0,
                    'cible':0,
                    'comment':1,
                }
            },
            'cast_per_turn': {
                'id': 31,
                'name': 'Lancer par tour',
                'edit':{
                    'value':0,
                    'critical':0,
                    'duration':0,
                    'cible':0,
                    'comment':1,
                }
            },
            'cast_per_target': {
                'id': 32,
                'name': 'Lancer par cible',
                'edit':{
                    'value':0,
                    'critical':0,
                    'duration':0,
                    'cible':0,
                    'comment':1,
                }
            },
            'number_between_two_cast': {
                'id': 33,
                'name': 'Nombre de tour entre deux lancers',
                'edit':{
                    'value':0,
                    'critical':0,
                    'duration':0,
                    'cible':0,
                    'comment':1,
                }
            }
        };

    static initEditionModeSpellEffect(){
        const editModeSelectType = document.querySelector(".display__adding__prop__container__select-type");
        const editModeSelectLevel = document.querySelector(".display__adding__prop__container__select-level");
        const editModeBtnAddProp = document.querySelector(".display__adding__prop__container__btn-add");
        const insertBtn = document.querySelector(".display__adding__prop__insert");
        const showPropGUI = document.querySelector(".display__adding__gui");
        const showPropGUIAddingContent = showPropGUI.querySelector(".display__adding__gui__content");
        const showPropGUIType = document.querySelector(".display__adding__gui__type");
        const showPropGUILevel = document.querySelector(".display__adding__gui__level");
        const showPropText = {
            "container" : document.querySelector(".showPropText"),
            'textarea' : document.querySelector(".showPropText textarea"),
            'button' : document.querySelector(".showPropText button")
        };

        const displayProps = document.querySelector(".display__props");
        const message = displayProps.querySelector(".display__props__message");
        const props_container = displayProps.querySelector(".effects_array_tabs");
        const props_ul = props_container.querySelector("ul");
        const props_tabs = props_container.querySelector(".effects_arrays_container_tab");

        let prop = {};

        showPropGUI.style.display = "none";

        const templateValue = {
            '1': {
                'container' : document.querySelector("#template-edit-mode-spell-effect .template-value-container[data-model='1']"),
                'selectType' : document.querySelector("#template-edit-mode-spell-effect .template-value-container[data-model='1'] .template-value-select-type"),
                'typeValue' : {
                    'container' : document.querySelector("#template-edit-mode-spell-effect .template-value-container[data-model='1'] .template-value-free"),
                    'input' : document.querySelector("#template-edit-mode-spell-effect .template-value-container[data-model='1'] .template-value-free input"),
                },
                "typeConditionnal" : {
                    'container' :document.querySelector("#template-edit-mode-spell-effect .template-value-container[data-model='1'] .template-value-conditionnal"),
                    'selectCaract' : document.querySelector("#template-edit-mode-spell-effect .template-value-container[data-model='1'] .template-value-conditionnal .template-value-select-caract"),
                    'selectOperator' : document.querySelector("#template-edit-mode-spell-effect .template-value-container[data-model='1'] .template-value-conditionnal .template-value-select-operator"),
                    'inputLimite' : document.querySelector("#template-edit-mode-spell-effect .template-value-container[data-model='1'] .template-value-conditionnal .template-value-input-limit"),
                    'inputValue' : document.querySelector("#template-edit-mode-spell-effect .template-value-container[data-model='1'] .template-value-conditionnal .template-value-input-value")
                },
            }
        };

        const templateCritical = {
            '1': {
                'container' : document.querySelector("#template-edit-mode-spell-effect .template-critical-container[data-model='1']"),
                'typeValue' : {
                    'container' : document.querySelector("#template-edit-mode-spell-effect .template-critical-container[data-model='1'] .template-critical-free"),
                    'input' : document.querySelector("#template-edit-mode-spell-effect .template-critical-container[data-model='1'] .template-critical-free input"),
                },
                "typeConditionnal" : {
                    'container' :document.querySelector("#template-edit-mode-spell-effect .template-critical-container[data-model='1'] .template-critical-conditionnal"),
                    'selectCaract' : document.querySelector("#template-edit-mode-spell-effect .template-critical-container[data-model='1'] .template-critical-conditionnal .template-critical-select-caract"),
                    'selectOperator' : document.querySelector("#template-edit-mode-spell-effect .template-critical-container[data-model='1'] .template-critical-conditionnal .template-critical-select-operator"),
                    'inputLimite' : document.querySelector("#template-edit-mode-spell-effect .template-critical-container[data-model='1'] .template-critical-conditionnal .template-critical-input-limit"),
                    'inputValue' : document.querySelector("#template-edit-mode-spell-effect .template-critical-container[data-model='1'] .template-critical-conditionnal .template-critical-input-value")
                },
            }
        };

        const templateDuration = {
            '1': {
                'container' : document.querySelector("#template-edit-mode-spell-effect .template-duration-container[data-model='1']"),
                'input' : document.querySelector("#template-edit-mode-spell-effect .template-duration-container[data-model='1'] input"),
            }
        };

        const templateCible = {
            '1': {
                'container' : document.querySelector("#template-edit-mode-spell-effect .template-cible-container[data-model='1']"),
                'select' : document.querySelector("#template-edit-mode-spell-effect .template-cible-container[data-model='1'] select"),
            }
        };

        const templateComment = {
            '1': {
                'container' : document.querySelector("#template-edit-mode-spell-effect .template-comment-container[data-model='1']"),
                'input' : document.querySelector("#template-edit-mode-spell-effect .template-comment-container[data-model='1'] input"),
            }
        };

        editModeBtnAddProp.onclick = function(){
            showAddingProp();
        };

        const getTypeById = function(id) {
            for (let key in Spell.EFFECT_TYPE) {
                if (Spell.EFFECT_TYPE[key] instanceof Object && Spell.EFFECT_TYPE[key].id !== undefined) {
                    if (Spell.EFFECT_TYPE[key].id == id) {
                        return key;
                    }
                }
            }
            return null; // Retourne null si aucun type n'est trouvé pour cet id
        }

        const showAddingProp = function(){
            showPropGUI.style.display = "block";
            showPropGUIAddingContent.innerHTML = "";
            showPropGUIType.innerHTML = "";
            showPropGUILevel.innerHTML = "";

            let prop = editModeSelectType.value;
            let level = editModeSelectLevel.value;
            let propData = Spell.EFFECT_TYPE[prop];

            showPropGUIType.innerHTML = propData.name;
            showPropGUILevel.innerHTML = level;

            let model_value, model_critical, model_duration, model_cible, model_comment = null;
            let container_value, container_critical, container_duration, container_cible, container_comment = null;
            if(propData.edit){
                if(propData.edit.value){
                    model_value = propData.edit.value;
                }
                if(propData.edit.critical){
                    model_critical = propData.edit.critical;
                }
                if(propData.edit.duration){
                    model_duration = propData.edit.duration;
                }
                if(propData.edit.cible){
                    model_cible = propData.edit.cible;
                }
                if(propData.edit.comment){
                    model_comment = propData.edit.comment;
                }
            }

            if(templateValue[model_value] != undefined){
                container_value = templateValue[model_value].container;
                showPropGUIAddingContent.appendChild(container_value.cloneNode(true));
            }
            if(templateCritical[model_critical] != undefined){
                container_critical = templateCritical[model_critical].container;
                showPropGUIAddingContent.appendChild(container_critical.cloneNode(true));
            }
            if(templateDuration[model_duration] != undefined){
                container_duration = templateDuration[model_duration].container;;
                showPropGUIAddingContent.appendChild(container_duration.cloneNode(true));
            }
            if(templateCible[model_cible] != undefined){
                container_cible = templateCible[model_cible].container;
                showPropGUIAddingContent.appendChild(container_cible.cloneNode(true));
            }
            if(templateComment[model_comment] != undefined){
                container_comment = templateComment[model_comment].container;
                showPropGUIAddingContent.appendChild(container_comment.cloneNode(true));
            }
            initCollapse();

            const allInputs = showPropGUIAddingContent.querySelectorAll("input");
            const allSelects = showPropGUIAddingContent.querySelectorAll("select");
            
            allInputs.forEach(input => {
                input.addEventListener("input", function(){
                    checkPropIsOk();
                });
            });
            allSelects.forEach(select => { 
                select.addEventListener("change", function(){
                    checkPropIsOk();
                });
            });
        };

        const checkPropIsOk = function(){
            const valueContainer = showPropGUIAddingContent.querySelector(".template-value-container");
            const criticalContainer = showPropGUIAddingContent.querySelector(".template-critical-container");
            const durationContainer = showPropGUIAddingContent.querySelector(".template-duration-container");
            const cibleContainer = showPropGUIAddingContent.querySelector(".template-cible-container");
            const commentContainer = showPropGUIAddingContent.querySelector(".template-comment-container");

            if(valueContainer != null){
                const valueFree = valueContainer.querySelector(".template-value-free .template-value-input");
                const valueConditionnal = {
                    'caract': valueContainer.querySelector(".template-value-conditionnal .template-value-select-caract"),
                    'operator': valueContainer.querySelector(".template-value-conditionnal .template-value-select-operator"),
                    'limit': valueContainer.querySelector(".template-value-conditionnal .template-value-input-limit"),
                    'value': valueContainer.querySelector(".template-value-conditionnal .template-value-input-value")
                };
                if(valueFree.value == "" && (valueConditionnal.caract.value == "" || valueConditionnal.limit.value == "" || valueConditionnal.value.value == "")){
                    insertBtn.setAttribute("disabled", "disabled");
                    return false;
                }
            }


            insertBtn.removeAttribute("disabled");
        };

        const writeProp = function(value, level, variety, type){
            console.log("---WRITE PROP---");
            console.log("value : ");
            console.log(value);
            console.log("level : " + level);
            console.log("type : " + type);

             // Création de la propriété
            const div = document.createElement("div");
            div.id = "effects_array_tabs-" + level;
            div.classList.add("effects_array_prop");

            let grid_col = "";
            let grid_row = "";

            const view_title = document.createElement("div");
            view_title.classList.add("spell_prop_title");
            view_title.innerHTML = `<span class="badge back-`+type+`-d-2">`+ucFirst(type)+`</span>`;
            div.appendChild(view_title);

            const view_value = document.createElement("div");
            view_value.classList.add("spell_prop_value");
            view_value.innerHTML = value.value;
            div.appendChild(view_value);

            if(value.duration != undefined){
                const view_duration = document.createElement("div");
                view_duration.classList.add("spell_prop_duration");

                grid_col += "-duration";
                if(value.duration.length < 20 || isInt(value.duration)){
                    view_duration.innerHTML = "<p>Les effets sont réparties sur " + value.duration + " tours</p>";
                } else {
                    view_duration.innerHTML = "<p>Les effets sont réparties sur plusieurs tours comme décrit ci-après</p>" + value.duration;
                }

                div.appendChild(view_duration);
            }

            if(value.cible != undefined){
                const view_cible = document.createElement("div");
                view_cible.classList.add("spell_prop_cible");

                grid_col += "-cible";
                switch (value.cible) {
                    case Spell.CIBLE_ALLY:
                        view_cible.innerHTML = "<span class='badge back-teal-d-2' title='Le sort affecte toutes les créatures alliées présentes sur la zone d'effet.'>Allié·e·s</span>";
                    break;
                    case Spell.CIBLE_ENEMY:
                        view_cible.innerHTML = "<span class='badge back-orange-d-2' title='Le sort affecte toutes les créatures ennemies présentes sur la zone d'effet.'>Ennemies</span>";
                    break;
                    case Spell.CIBLE_SELF:
                        view_cible.innerHTML = "<span class='badge back-blue-d-2' title='Le sort affecte uniquement vous-même.'>Soi-même</span>";
                    break;
                    default:
                        view_cible.innerHTML = "<span class='badge back-purple-d-2' title='Le sort affecte toutes les créatures présentes sur la zone d'effet.'>Allié·e·s et ennemies</span>";
                    break;
                }

                div.appendChild(view_cible);
            }

            if(value.critical != undefined){
                const view_critical = document.createElement("div");
                view_critical.classList.add("spell_prop_critical");
                
                grid_row += "-critical";
                view_critical.innerHTML = value.critical;
                const view_critical_title = document.createElement("p");
                view_critical_title.classList.add("spell_prop_critical_title");
                view_critical_title.textContent = "Critique :";
                div.appendChild(view_critical_title);

                div.appendChild(view_critical);
            }

            if(value.comment != undefined || variety == Spell.VARIETY_SAVE){
                const view_comment = document.createElement("div");
                view_comment.classList.add("spell_prop_comment");
            
                grid_row += "-comment";

                if(value.comment != ""){
                    view_comment.innerHTML = "<p class='comment'>" + value.comment + "</p>";
                }
                if(variety == Spell.VARIETY_SAVE){
                    view_comment.innerHTML += "<p><small>En cas de réussite au jet de sauvegarde de la cible, les dommages subits sont divisés par deux (arrondi à l'inférieur) et les autres effets ne s'appliquent pas.</small></p>";
                }

                const view_comment_title = document.createElement("p");
                view_comment_title.classList.add("spell_prop_comment_title");
                view_comment_title.textContent = "Commentaire :";
                div.appendChild(view_comment_title);

                div.appendChild(view_comment);
            }

            div.classList.add("spell_prop_col" + grid_col);
            div.classList.add("spell_prop_row" + grid_row);

            props_tabs.appendChild(div);
            return div;
        }

        const showProps = function(){
            const test_tableau_type = {
                3 : [ // level
                    Spell.EFFECT_TYPE.variety.id = Spell.VARIETY_ATTACK,
                    Spell.EFFECT_TYPE.touch.id = {
                        value : "1d20 + {force} + {touch}", // Auto possible
                        comment : "eadeazlkenjazlkde",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.dd.id = {
                        value : "{ca}", // ca par défault
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.damage.id = {
                        value : "1d6 + {force}",
                        element : Spell.ELEMENT_TERRE,
                        cible : Spell.CIBLE_ALL, // allies, ennemies, all
                        duration : 0, // Si dégât sur plusieurs tours comme un poison, si 0 ou rien alors dégât instantané
                        comment : "Salut",
                        critical : "6 + 1d6 + {force}",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.health.id = {
                        value : "1d6 + {intel}",
                        element : Spell.ELEMENT_FEU,
                        cible : Spell.CIBLE_ALL, // allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.lifesteal.id = {
                        value : "1d6 + {intel}",
                        element : Spell.ELEMENT_FEU,
                        cible : Spell.CIBLE_ENEMY, // allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.effect.id = {
                        value : "Description de l'effet, avantage bonus ou malus ou autre",
                        cible : Spell.CIBLE_ENEMY, // allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },  
                    Spell.EFFECT_TYPE.state.id = {
                        value : "Etat à appliquer",
                        cible : Spell.CIBLE_SELF, // soit même, allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.malus_pa.id = {
                        value : "1",
                        cible : Spell.CIBLE_ENEMY, // allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.malus_pm.id = {
                        value : "1",
                        cible : Spell.CIBLE_ENEMY, // allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.malus_po.id = {
                        value : "1",
                        cible : Spell.CIBLE_ENEMY, // allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.malus_ca.id = {
                        value : "1",
                        cible : Spell.CIBLE_ENEMY, // allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.malus_touch.id = {
                        value : "1",
                        cible : Spell.CIBLE_ENEMY, // allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.malus_dodge_pa.id = {
                        value : "1",
                        cible : Spell.CIBLE_ENEMY, // allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                    Spell.EFFECT_TYPE.malus_dodge_pm.id = {
                        value : "1",
                        cible : Spell.CIBLE_ENEMY, // allies, ennemies, all
                        duration : 0,
                        comment : "",
                        critical : "",
                        occurence : 0
                    },
                ]
            };
            
            prop = Object.entries(test_tableau_type);

            props_ul.innerHTML = "";
            props_tabs.innerHTML = "";

            if(prop != null){
                prop.forEach((element) => {
                    const prop_level = element[1];
                    const level = element[0];

                    // Création du btn level
                        const li = document.createElement("li");
                        li.classList.add("btn", "btn-sm", "btn-back-main", "btn-animate");
                        const a = document.createElement("a");
                        a.textContent = "Niveau " + level;
                        a.href = "#effects_array_tabs-" + level;
                        li.appendChild(a);
                        props_ul.appendChild(li);

                        let variety = Spell.VARIETY_ATTACK;
                        let view = null;

                        for(const [key, value] of Object.entries(prop_level)){
                            let type = getTypeById(key);
                            if(type != null){     
                                if(key == Spell.EFFECT_TYPE.variety.id){
                                    variety = value;
                                } else {
                                    view = writeProp(value, level, variety, type);
                                    props_tabs.appendChild(view);
                                }
                            }
                        }

                });
            } else {
                message.textContent = "Aucun effet ni propriétés associées à ce sort.";
            }
        };
        showProps();

    }
}