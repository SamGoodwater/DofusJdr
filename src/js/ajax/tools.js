class Tools {

    static req(btn, token)
    {
        let tool = btn.getAttribute('data-tool');
        btn.classList.add('disabled');
        let URL = 'index.php?c=tools&a='+tool;
        $.ajax({
            type: "POST",
            url: URL,
            dataType: "json",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "Authorization": "Bearer " + token
            },
            success: function(data) {
                btn.classList.remove('disabled');
                document.getElementById('tools-answer').innerHTML = data.value;
                if(data.state){
                    MsgAlert("Succès","", "green" , 0);
                } else {
                    MsgAlert("Erreur","", "red" , 0);
                }
            }
        }); 
    }

    static initVacum(token){
        // let data_select = document.getElementById("data_select");
        let data_template = document.getElementById("data_template");
        let data_offset = document.getElementById("data_offset");
        let data_limit = document.getElementById("data_limit");
        let data_max = document.getElementById("data_max");
        let data_total = document.getElementById("data_total");
        let data_write = document.getElementById("data_write");
        let data_show = document.getElementById("data_show");
        let data_submit = document.getElementById("data_submit");
        let data_cancel = document.getElementById("data_cancel");
        let data_gettotal = document.getElementById("data_gettotal");
        let result = document.getElementById("result");
        let info = document.getElementById("data_info");
        let data_showitems = document.getElementById("data_showItems");
        let data_showconsumables = document.getElementById("data_showConsumables");
        let data_showresources = document.getElementById("data_showRessources");
        let data_progress = document.getElementById("data_progress");
        let data_progress_text = data_progress.querySelector("div");

        let offset = null;
        let limit = null;
        let max = null;
        let total = null;
        let current_object = null;
        let is_writing = 0;
        let is_showing = 0;
        let is_showitems = 0;
        let is_showconsumables = 0;
        let is_showresources = 0;
        let template = null;
        if(data_template.value == "items" || data_template.value == "mobs" || data_template.value == "spells"){
            template = data_template.value;
        } else {
            MsgAlert("Erreur", "Le template n'est pas correct", "danger", 0);
            return false;
        }

        let super_category = {};
        let category = {};

        let continu = true;

        data_gettotal.addEventListener("click", function(){
            Tools.getTotalElementFromDofusDB(token);
        });

        data_submit.addEventListener("click", function(){
            result.innerHTML = "";
            info.innerHTML = "";
            offset = data_offset.value;
            limit = data_limit.value;
            max = data_max.value;
            current_object = offset;
            is_writing = 0;
            if(data_write.checked){
                is_writing = 1;
            }
            if(data_show.checked){
                is_showing = 1;
            }
            is_showitems = 0;
            if(data_showitems.checked){
                is_showitems = 1;
            }
            is_showconsumables = 0;
            if(data_showconsumables.checked){
                is_showconsumables = 1;
            }
            is_showresources = 0;
            if(data_showresources.checked){
                is_showresources = 1;
            }
            
            total = data_total.getAttribute("data-total");
            if(Number(offset) > Number(max)){
                MsgAlert("Erreur", "L'offset ne peut pas être supérieur à la valeur max", "danger", 0);
                return false;
            } else {
                if(Number(limit) > 50){
                    MsgAlert("Erreur", "La limite ne doit pas être supérieur à 50.", "danger", 0);
                    return false;
                } else {
                    request(token);
                }
            }

        });

        data_cancel.addEventListener("click", function(){
            info.innerHTML = "";
            result.innerHTML = "";
            data_progress_text.innerHTML = "0%";
            data_progress_text.setAttribute("style", "width:0%");
            data_progress.setAttribute("aria-valuenow", 0);
            continu = false;
            data_cancel.classList.add("disabled");
        });

        let request = (token) => {
            data_cancel.classList.remove("disabled");
            let URL = 'index.php?c=tools&a=updatedbFromDofusDB';
            let is_writing_text = "";
            if(is_writing){
                is_writing_text = "en écriture";
            } else {
                is_writing_text = "affichage seulement";
            }
            let ratio = (current_object / max) * 100;
            info.innerHTML = "Récupération de la page " + current_object + " sur " + max + " (template : "+template+", "+ is_writing_text +")<br>";
            data_progress_text.innerHTML = Math.round(ratio) + "%";
            data_progress_text.setAttribute("style", "width:"+Math.round(ratio)+"%");
            data_progress.setAttribute("aria-valuenow", Math.round(ratio));
            
            $.ajax({
                url: URL,
                type: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "Authorization": "Bearer " + token
                },
                data: {
                    template: template,
                    offset: current_object,
                    limit: limit,
                    write: is_writing,
                    show: is_showing,
                    showitems: is_showitems,
                    showconsumables: is_showconsumables,
                    showresources: is_showresources
                },
                dataType: "json",
                success: function(data, status) {
                    if(!continu){
                        return false;
                    }
                    if (data.state) {
                        result.innerHTML += data.value;
                        current_object = Number(current_object) + Number(limit);
                        if (Number(current_object) <= Number(max)) {
                            request(data.token);
                        } else {
                            info.innerHTML += "<span class='text-green bold'>Récupération terminée</span><br>";
                            MsgAlert("Aspiration terminée", "Les données ont été aspirées", "success", 0);
                            data_cancel.classList.add("disabled");
                            return true;
                        }
                    } else {
                        info.innerHTML += "<span class='text-red bold'>Erreur : "+data.value+"</span><br>";
                        MsgAlert("Impossible de récupérer l'élément", 'Erreur : ' + data.value, "danger", 0);
                    }
                }
            });
        }

    }

    static getTotalElementFromDofusDB(token){
        let data_template = document.getElementById("data_template");
        let data_total = document.getElementById("data_total");
        let data_max = document.getElementById("data_max");
        let data_offset = document.getElementById("data_offset");
        let data_submit = document.getElementById("data_submit");
        let template = null;

        if(data_template.value == "items" || data_template.value == "mobs" || data_template.value == "spells"){
            template = data_template.value;
        } else {
            MsgAlert("Erreur", "Le template n'est pas correct", "danger", 0);
            return false;
        }

        data_total.setAttribute("data-max", 0);
        data_total.innerHTML = `<div class="spinner-border text-secondary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>`;
        data_submit.classList.add("disabled");

        let URL = 'index.php?c=tools&a=getTotalElementFromDofusDB';
        $.ajax({
            url: URL,
            type: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "Authorization": "Bearer " + token
            },
            data: {
                template: template
            },
            dataType: "json",
            success: function(data, status) {
                if (data.state) {
                    data_offset.max = data.value;
                    data_max.value = data.value;
                    data_total.innerHTML = data.value;
                    data_total.setAttribute("data-total", data.value);
                    data_submit.classList.remove("disabled");
                } else {
                    data_total.innerHTML = "Erreur";
                }
            }
        });
    }
    
}