/*
  Cette classe a pour rôle de gérer les différents composants customisés de l'interface utilisateur.

*/

class DisplayUI {

    static update() {
        let tabs = document.querySelectorAll("[data-component='tabs']");
        for (let i = 0; i < tabs.length; i++) {
            new DisplayUI_tabs(tabs[i]);
        }
    }

}