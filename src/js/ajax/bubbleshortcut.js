class Bubbleshortcut {
    static BUBBLES = [];

    static init(){
        if($(window).width() < 768){
            Bubbleshortcut.dropdownToogle();
        }
    }

    static update(bubbleId){
        let modal = $("#modal");
        let content = modal.find(".modal-body").html();
        let title = modal.find(".modal-title").html();
        let is_modal = modal.hasClass("show");
        let size = Page.SIZE_MD;
        if(modal.find(".modal-dialog").hasClass("modal-xl")){
            size = Page.SIZE_XL;
        } else if(modal.find(".modal-dialog").hasClass("modal-lg")){
            size = Page.SIZE_LG;
        } else if(modal.find(".modal-dialog").hasClass("modal-sm")){
            size = Page.SIZE_SM;
        } else if(modal.find(".modal-dialog").hasClass("modal-fullscreen")){
            size = Page.SIZE_FL;
        }

        let bubble = {
            "title": title,
            "content": content,
            "is_modal": is_modal,
            "size": size,
            "bubbleId": bubbleId
        };

        if(Bubbleshortcut.existFromBubbleId(bubbleId)){
            // update
            let index = Bubbleshortcut.getFromBubbleId(bubbleId, true);
            Bubbleshortcut.BUBBLES[index] = bubble;

            $(".modal__bubbleshortcut_toggle").removeClass("listed");
            $(".modal__bubbleshortcut_toggle").attr("onclick", "Bubbleshortcut.update('"+bubbleId+"')");
            
        } else {
            // add
            let name = "";
            if($("#modal .modal-title input").length > 0){
                name = $("#modal .modal-title input").val();
            } else {
                name = $("#modal .modal-title").text();
            }
            let path_img = "medias/no_file_found_logo.png";
            if($("#modal .selector-image-main [data-fancybox='gallery']") != null){
                path_img = $("#modal .selector-image-main [data-fancybox='gallery']").attr("href");
            }

            Bubbleshortcut.BUBBLES.push(bubble);
            let bubbleButton = "<a class='bubbleshorcut__button "+bubbleId+"' onclick=\"Bubbleshortcut.show('"+bubbleId+"');\" title='Ouvrir la fiche du / de la "+name+"'><div style=\"background-image:url('"+path_img+"')\"></div></a>";
            $(".bubbleshorcut_item").append(bubbleButton);

            $(".modal__bubbleshortcut_toggle").addClass("listed");
            $(".modal__bubbleshortcut_toggle").attr("onclick", "Bubbleshortcut.remove('"+bubbleId+"')");
        }
    }
    static remove(bubbleId){
        if(Bubbleshortcut.existFromBubbleId(bubbleId)){
            let index = Bubbleshortcut.getFromBubbleId(bubbleId, true);
            Bubbleshortcut.BUBBLES.splice(index, 1);
            $(".bubbleshorcut__button."+bubbleId).remove();
            $(".modal__bubbleshortcut_toggle").removeClass("listed");
            $(".modal__bubbleshortcut_toggle").attr("onclick", "Bubbleshortcut.update('"+bubbleId+"')");
        }
    }
    static show(bubbleId){
        if(Bubbleshortcut.existFromBubbleId(bubbleId)){
            let bubble = Bubbleshortcut.getFromBubbleId(bubbleId);
            let bubbleRef = $(".bubbleshorcut__button ."+bubbleId);

            let title = "";
            if(bubble.title != ""){
                title = bubble.title;
            }
            let is_modal = true;
            if(bubble.is_modal != ""){
                is_modal = bubble.is_modal;
            }
            let size = Page.SIZE_MD;
            if(bubble.size != ""){
                size = bubble.size;
            }
            let content = "";
            if(bubble.content != "" || bubble.content != null || bubble.content != undefined){
                content = bubble.content;
                Page.build(is_modal, title, content, size, true, bubbleId);
                bubbleRef.addClass("active");
            } else {
                MsgAlert("Erreur de chargement", "Il n'y a pas de contenu chargé pour cette bulle de raccourcis.", "danger" , 4000);
            }
            
        } else {
            MsgAlert("Erreur de chargement", "Aucune bulle de raccourcis n'est définie pour cette référence", "danger" , 4000);
        }
    }
    static hideAllBubbleActive(){
        $(".bubbleshorcut__button.active").removeClass("active");
    }
    static existFromBubbleId(bubbleId){
        if(Bubbleshortcut.BUBBLES.length <= 0){ return false; }

        let exist = false;
        Bubbleshortcut.BUBBLES.forEach(function(element, index, array){
            if(element.bubbleId == bubbleId){
                exist = true;
            }
        });
        return exist;
    }
    static getFromBubbleId(bubbleId, getIndex = false){
        let exist = "";
        if(Bubbleshortcut.existFromBubbleId(bubbleId)){
            Bubbleshortcut.BUBBLES.forEach(function(element, index, array){
                if(element.bubbleId == bubbleId){
                    if(getIndex){
                        exist = index;
                    } else {
                        exist = element;
                    }
                }
            });
        } else {
            exist = -1;
        }
        return exist;
    }

    static dropdownToogle(){
        let button = $(".bubbleshorcut__button_dropdown");
        let dropdown = $(".bubbleshorcut_item");
        if(dropdown.hasClass("show")){
            dropdown.removeClass("show");
            button.removeClass("active");
            button.attr("title", "Afficher les bulles de raccourcis");
        }else {
            dropdown.addClass("show");
            button.addClass("active");
            button.attr("title", "Cacher les bulles de raccourcis");
        }
    }


}