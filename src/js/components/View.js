class View {

    static VIEW_PANEL = 0;
    static VIEW_DETAILED_CARD = 1;
    static VIEW_MINIMAL_CARD = 2;
    static VIEW_TABLE = 3;
    static VIEW_TEXT = 4;
    static VIEW_LIST = 5;

    constructor(fetchUrlCount, fetchUrlObj, fetchUrlTemplates, limit = 100, offset = 0) {
        this.fetchUrlObj = fetchUrlObj;
        this.fetchUrlCount = fetchUrlCount;
        this.fetchUrlTemplates = fetchUrlTemplates;
        this.limit = limit;
        this.offset = offset;
        this.usable = false;
        this.view = this.VIEW_MINIMAL_CARD;

        this.tableElement = document.querySelector('.view_container_bottom__list__table-view');
        this.detailedCardElement = document.querySelector('.view_container_bottom__list__detailled-card-view');
        this.minimalCardElement = document.querySelector('.view_container_bottom__list__minimal-card-view');

        this.templates = [];
        this.totalObjects = 0;
        this.objects = [];

        this.init();
    }
  
    // Initialisation
    async init() {
        this.setUsable();
        this.hiddenAllViews();
        this.setView();
        this.clearDataDisplay();

        // this.fetchTemplates();
        this.fetchObjects();
    }

    setView() {
        const view = document.querySelector('.view-container__top__settings__view-choice');
        const value = Number(view.options[view.selectedIndex].value);
        const views = [View.VIEW_MINIMAL_CARD, View.VIEW_DETAILED_CARD, View.VIEW_TABLE];
    
        this.view = views.includes(value) ? value : View.VIEW_MINIMAL_CARD;
    
        const viewElements = {
            [View.VIEW_MINIMAL_CARD]: this.minimalCardElement,
            [View.VIEW_DETAILED_CARD]: this.detailedCardElement,
            [View.VIEW_TABLE]: this.tableElement
        };
    
        Object.values(viewElements).forEach(element => element.classList.remove('show'));
        viewElements[this.view].classList.add('show');
    }
    
    setUsable() {
      if (document.querySelector('.view-container__top__settings__usable-box__checkbox').checked) {
        this.usable = true;
      } else {
        this.usable = false;
      }
    }
  
    // Récupération des données
    async fetchTemplates() {
        try {
            let requestSettings = {
                minimalCard : {
                    url : `${this.fetchUrlTemplates}?view=${View.VIEW_MINIMAL_CARD}`,
                    name : 'minimalCard'
                },
                detailedCard : {
                    url : `${this.fetchUrlTemplates}?view=${View.VIEW_DETAILED_CARD}`,
                    name : 'detailedCard'
                }
            };

            for (const [key, value] of Object.entries(requestSettings)) {
                const response = await fetch(value.url);
                const data = await response.json();
                if(data.state){
                    this.templates[value.name] = data.value;
                } else {
                    View.showError('Erreur lors de la récupération du template', data.error, "red-d-3", 7000);
                    return false;
                }
            }

            console.log(this.templates);
        } catch (error) {
            View.showError('Erreur lors de la récupération du template', error, "red-d-3", 7000);
        }
    }
  
    async fetchObjects() {
      try {
    
        // Récupérer le nombre total d'objets;
        const countResponse = await fetch(`${this.fetchUrlCount}${this.usable ? '&usable=1' : ''}`);
        let count = await countResponse.json(); // Assurez-vous que le serveur renvoie un JSON contenant le count
        if(count.state){
          this.totalObjects = count.value;
        } else {
          View.showError('Erreur lors de la récupération', count.error, "red-d-3", 7000);
          return false;
        }
  
        let data = [];
        let customOffset = this.offset;
        let customLimit = this.limit;
    
        // Fonction pour faire une requête et ajouter les objets au tableau data
        const request = async () => {
          let url = `${this.fetchUrlObj}&offset=${customOffset}&limit=${customLimit}${this.usable ? '&usable=1' : ''}`;
          const response = await fetch(url);
          const responseData = await response.json();
          this.choiceDisplay(responseData); // Afficher les données
          this.objects = this.objects.concat(responseData);
        }
    
        // Faire des requêtes tant qu'il reste des objets à récupérer
        while (this.totalObjects > customOffset) {
          if (this.totalObjects - customOffset < customLimit) {
            customLimit = this.totalObjects - customOffset;
          }
          await request();
          customOffset += customLimit;
        }
        return true;

      } catch (error) {
        View.showError('Erreur lors de la récupération', error, "red-d-3", 7000);
        return false;
      }
    }

    // Affichage des données
    choiceDisplay(data) {
      switch (Number(this.view)) {
        case Number(View.VIEW_MINIMAL_CARD):
          this.displayMinimalCard(data);
          break;
        case Number(View.VIEW_DETAILED_CARD):
          this.displayDetailedCard(data);
          break;
        case Number(View.VIEW_TABLE):
          this.displayTable(data);
          break;
        default:
          this.displayMinimalCard(data);
          break;
      }
    }

    displayMinimalCard(objects) {
      console.log(objects);
    }

    displayDetailedCard(objects) {
      console.log(objects);
    }

    displayTable(objects) {
      const tbody = this.tableElement.querySelector('tbody');
    
      // Obtenir toutes les colonnes pour identifier les data-field
      const columns = this.tableElement.querySelectorAll('thead th');
    
      objects.forEach(obj => {
        const tr = document.createElement('tr');
    
        columns.forEach(column => {
          const field = column.getAttribute('data-field');
          const td = document.createElement('td');
          // Si on veut ajouter une classe à la cellule
          // td.classList.add('text-center');
    
          if (field && obj.hasOwnProperty(field)) {
            td.innerHTML = obj[field] !== null && obj[field] !== undefined ? obj[field] : '';
          }
    
          tr.appendChild(td);
        });
    
        tbody.appendChild(tr);
      });
    }
    
    clearDataDisplay(){
        const tbody = this.tableElement.querySelector('.view_container_bottom__list__table-view tbody');
        tbody.innerHTML = '';
        this.detailedCardElement.innerHTML = '';
        this.minimalCardElement.innerHTML = '';
    }
    hiddenAllViews(){
        this.tableElement.classList.remove('show');
        this.detailedCardElement.classList.remove('show');
        this.minimalCardElement.classList.remove('show');
    }
    
    // Autres méthodes
    static showError(title = '', description = '', color = 'red-d-3', delay = 0) {
      if (title === '' && description === '') {
        title = "Impossible d'accéder à l'erreur";
        description = "Une erreur a été rencontrée lors de la manipulation des données mais il est impossible de savoir de quelle erreur il s'agit. Veuillez réessayer plus tard.";
      }
  
      const container = document.querySelector('.view-container__error-box');
      const titleElement = document.querySelector('.view-container__error-box__title');
      const descriptionElement = document.querySelector('.view-container__error-box__description');
  
      if (!container || !titleElement || !descriptionElement) {
        console.log("Erreur : éléments d'affichage de l'erreur non trouvés dans le DOM.");
        return;
      }
  
      if (color !== "") {
        container.classList.add("text-" + color);
      }
  
      if (title !== '') {
        titleElement.innerHTML = title;
      }
      if (description !== '') {
        descriptionElement.innerHTML = description;
      }
  
      if (delay > 0) {
        setTimeout(function () {
          container.classList.remove("text-" + color);
          titleElement.innerHTML = '';
          descriptionElement.innerHTML = '';
        }, delay);
      }
    }
    static clearError() { // Effacer l'erreur
      const container = document.querySelector('.view-container__error-box');
      const titleElement = document.querySelector('.view-container__error-box__title');
      const descriptionElement = document.querySelector('.view-container__error-box__description');

      container.classList.forEach(className => {
        if (className.startsWith('text-')) {
          container.classList.remove(className);
        }
      });
      titleElement.innerHTML = '';
      descriptionElement.innerHTML = '';
    }

    static toogleSortOrderBtn() {

      document.querySelectorAll('[data-type="sort-order"]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          const icon = this.querySelector('i');
          const isNumeric = false;
          if(this.getAttribute('data-numeric') == 'true') {
            isNumeric = true;
          }

          if(this.getAttribute('data-order') == -1) {
            this.setAttribute('data-order', 1);
            this.setAttribute('title', "Trier par ordre croissant");
            if(isNumeric) {
              icon.classList.remove('fa-arrow-up-9-1');
              icon.classList.add('fa-arrow-up-1-9');

            } else {
              icon.classList.remove('fa-arrow-up-z-a');
              icon.classList.add('fa-arrow-up-a-z');
            }
          } else {
            this.setAttribute('data-order', -1);
            this.setAttribute('title', "Trier par ordre décroissant");
            if(isNumeric) {
              icon.classList.remove('fa-arrow-up-1-9');
              icon.classList.add('fa-arrow-up-9-1');
            } else {
              icon.classList.remove('fa-arrow-up-a-z');
              icon.classList.add('fa-arrow-up-z-a');
            }
          }

        });
      });
  
    }

  }