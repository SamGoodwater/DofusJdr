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
        let data_offset = document.getElementById("data_offset");
        let data_limit = document.getElementById("data_limit");
        let data_max = document.getElementById("data_max");
        let data_total = document.getElementById("data_total");
        let data_write = document.getElementById("data_write");
        let data_submit = document.getElementById("data_submit");
        let data_gettotal = document.getElementById("data_gettotal");
        let result = document.getElementById("result");
        let info = document.getElementById("data_info");
        let data_showitems = document.getElementById("data_showItems");
        let data_showconsumables = document.getElementById("data_showConsumables");
        let data_showresources = document.getElementById("data_showRessources");

        let offset = null;
        let limit = null;
        let max = null;
        let total = null;
        let current_object = null;
        let is_writing = 0;
        let is_showitems = 0;
        let is_showconsumables = 0;
        let is_showresources = 0;
        let type = "";

        let super_category = {};
        let category = {};

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
            if(offset > max && max > 0 && total > 0){
                MsgAlert("Erreur", "L'offset ne peut pas être supérieur à la limite", "danger", 0);
                return false;
            } else {
                request(token);
            }
        });

        let request = (token) => {
            let URL = 'index.php?c=tools&a=updatedbFromDofusDB';
            let is_writing_text = "";
            if(is_writing){
                is_writing_text = "en écriture";
            } else {
                is_writing_text = "affichage seulement";
            }
            info.innerHTML += "Récupération de la page " + current_object + " sur " + max + " (type : "+type+", "+ is_writing_text +")<br>";
            
            $.ajax({
                url: URL,
                type: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "Authorization": "Bearer " + token
                },
                data: {
                    template: "items",
                    offset: current_object,
                    limit: limit,
                    write: is_writing,
                    showitems: is_showitems,
                    showconsumables: is_showconsumables,
                    showresources: is_showresources
                },
                dataType: "json",
                success: function(data, status) {
                    if (data.state) {
                        // if(data.super_category_list != undefined){
                        //     if(data.super_category_list != ""){
                        //         Object.entries(data.super_category_list).forEach(element => {
                        //             const [key, value] = element;
                        //             if (super_category[key] == undefined) {
                        //                 super_category[key] = value;
                        //                 let text = document.createElement('p');
                        //                 text.innerHTML = key + ' => "' + value + '",';
                        //                 super_category_list.appendChild(text);
                        //             }
                        //         });
                        //     }
                        // }
    
                        // if(data.category_list != undefined){
                        //     if(data.category_list != ""){
                        //         Object.entries(data.category_list).forEach(element => {
                        //             const [key, value] = element;
                        //             if (category[key] == undefined) {
                        //                 category[key] = value;
                        //                 let text = document.createElement('p');
                        //                 text.innerHTML = key + ' => "' + value + '",';
                        //                 category_list.appendChild(text);
                        //             }
                        //         });
                        //     }
                        // }

                        result.innerHTML += data.value;
                        current_object = Number(current_object) + Number(limit);
                        if (Number(current_object) <= Number(max)) {
                            request(data.token);
                        } else {
                            info.innerHTML += "<span class='text-green bold'>Récupération terminée</span><br>";
                            MsgAlert("Aspiration terminée", "Les données ont été aspirées", "success", 0);
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
        let data_total = document.getElementById("data_total");
        let data_max = document.getElementById("data_max");
        let data_offset = document.getElementById("data_offset");
        let data_submit = document.getElementById("data_submit");

        let super_category_list = document.getElementById("super_category_list");
        let category_list = document.getElementById("category_list");

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