class DisplayUI_tabs {

    // Element : HTMLElement faisant référence à l'élément HTML contenant la div first avec les boutons de navigation. Chaque boutons de navigation possédant un attribut data-targent correspondant à l'identifiant de l'élément HTML à afficher.
    constructor(btn_container) {
        let first_div = btn_container.querySelector("div:first-child");
        let btns = first_div.querySelectorAll("button");
        let tabs = [];
        let is_active = false;
        
        for (let i = 0; i < btns.length; i++) {
            if(btns[i].hasAttribute("data-target")){
                let tab = btn_container.querySelector(btns[i].getAttribute("data-target"));
                tabs.push(tab);

                if(is_active){
                    tab.classList.remove("visible");
                    tab.classList.remove("active");
                }

                if(btns[i].classList.contains("active") && !is_active){
                    is_active = true;
                    tab.classList.add("visible");
                }

                btns[i].addEventListener("click", function(){ 
                    for (let i = 0; i < tabs.length; i++) {
                        tabs[i].classList.remove("visible");
                        btns[i].classList.remove("active");
                    };
                    let tab = btn_container.querySelector(this.getAttribute("data-target"));
                    tab.classList.add("visible");
                    this.classList.add("active");

                });
            }
        };

        if(!is_active){
            tabs[0].classList.add("visible");
            btns[0].classList.add("active");
        }
    }

}
