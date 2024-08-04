/* Ce code devrait être restructurer pour en plusieurs classes :
  - DataFetcher pour récupérer les données
  - DataDisplay pour afficher les données
  - DataFilter pour filtrer les données
*/

class View {

    static VIEW_PANEL = 0;
    static VIEW_DETAILED_CARD = 1;
    static VIEW_MINIMAL_CARD = 2;
    static VIEW_TABLE = 3;
    static VIEW_TEXT = 4;
    static VIEW_LIST = 5;

    constructor(classReference, properties) {
        if(!classReference){
            View.showError('Erreur lors de la récupération', 'Le nom de la classe est manquant', "red-d-3", 0);
            return false;
        }
        this.classReference = classReference;
        this.className = classReference.MODEL_NAME;

        this.limit = 20;
        this.offset = 0;

        // Paramètres
        this.currentPage = 1;
        this.totalPages = 0;
        this.itemsPerPage = this.limit;
        this.usable = false;
        this.view = this.VIEW_MINIMAL_CARD;

        // Element du DOM
        this.tableElement = document.querySelector('.view-container__bottom__list__table-view');
        this.detailedCardElement = document.querySelector('.view-container__bottom__list__detailled-card-view');
        this.minimalCardElement = document.querySelector('.view-container__bottom__list__minimal-card-view');

        this.viewChoiceSelectElement = document.querySelector('.view-container__top__settings__view-choice');
        this.progressBarElement = document.querySelector('.view-container__bottom__list__progress__bar');
        this.progressLabelElement = document.querySelector('.view-container__bottom__list__progress__label');

        this.templates = [];
        this.totalObjects = 0;
        this.objects = [];
        this.currentObjects = [];
        this.properties = [];
        this.propertiesFilteredTable = [];
        this.propertiesFilteredMinimalCard = [];
        this.propertiesFilteredDetailedCard = [];
        this.propertiesToName = [];
        this.propertiesSearch = [];
        
        this.idRowSelected = [];

        if(properties) {
            this.properties = Object.keys(properties);
            this.properties.forEach(key => {
                this.propertiesToName[key] = properties[key].name;
                if(properties[key].filter.table == false) {
                    this.propertiesFilteredTable.push(key);
                }
                if(properties[key].filter.minimal_card == false) {
                  this.propertiesFilteredMinimalCard.push(key);
                }
                if(properties[key].filter.detailed_card == false) {
                  this.propertiesFilteredDetailedCard.push(key);
                }
                if(properties[key].search) {
                  this.propertiesSearch.push(key);
                }
            });
        }

        this.init();
    }
  
     //_____________________________________________
    // ------ EXTENTION DU CONSTRUCTEUR ------------
      async init() {
          this.hiddenAllViews();
          this.clearDataDisplay();
          this.initEvents();

          switch (Number(this.view)) {
            case Number(View.VIEW_MINIMAL_CARD):
                this.initMinimalCard();
            break;
            case Number(View.VIEW_DETAILED_CARD):
                this.initDetailedCard();
            break;
            case Number(View.VIEW_TABLE):
                this.initTable();
            break;
          }

          this.startFetch();
      }
      initEvents() {
          // ---------- Pagination
              document.querySelector('.view-container__bottom__list__pagination__controls__select').addEventListener('change', (e) => {
                  this.currentPage = 1;
                  this.updateDisplay();
              });
              document.querySelector('.view-container__bottom__list__pagination__number-box__previous').addEventListener('click', () => {
                  this.goToPage(this.currentPage - 1);
              });
              document.querySelector('.view-container__bottom__list__pagination__number-box__next').addEventListener('click', () => {
                  this.goToPage(this.currentPage + 1);
              });

          // ---------- Vue
            this.viewChoiceSelectElement.addEventListener('change', (e) => {
              // a faire
            });

            document.querySelector('.view-container__top__settings__btn-refresh').addEventListener('click', (e) => {
              this.refresh(true)
            });

              // Permet de changer la width max en fonction de si le menu est compacté ou non via l'écoute de l'évenement de class
              this.initAdaptDisplaySize();

            // --------- Settings
            
              // FILTER
              document.querySelector('.view-container__top__settings__filter-box__btn')
                .addEventListener('click', (e) => {
                  event.stopPropagation(); 
                  document.querySelector('.view-container__top__settings__filter-box__menu')
                    .classList.toggle('show');
              });
              document.querySelector(".view-container__top__settings__filter-box__menu__header__close")
                .addEventListener('click', (e) => {
                  document.querySelector('.view-container__top__settings__filter-box__menu')
                    .classList.remove('show');
              });
              let itemFilterMenu = document.querySelectorAll('.view-container__top__settings__filter-box__menu__container__item__checkbox');
              itemFilterMenu.forEach(item => {
                item.addEventListener('change', (e) => {
                  let property = e.target.getAttribute('data-property');
                  if (e.target.checked) {
                    switch (Number(this.view)) {
                      case Number(View.VIEW_MINIMAL_CARD):
                        this.propertiesFilteredMinimalCard = this.propertiesFilteredMinimalCard.filter(prop => prop != property);
                      break;
                      case Number(View.VIEW_DETAILED_CARD):
                        this.propertiesFilteredDetailedCard = this.propertiesFilteredDetailedCard.filter(prop => prop != property);
                      break;
                      case Number(View.VIEW_TABLE):
                        this.propertiesFilteredTable = this.propertiesFilteredTable.filter(prop => prop != property);
                      break;
                    }
                  } else {
                    switch (Number(this.view)) {
                      case Number(View.VIEW_MINIMAL_CARD):
                        this.propertiesFilteredMinimalCard.push(property);
                      break;
                      case Number(View.VIEW_DETAILED_CARD):
                        this.propertiesFilteredDetailedCard.push(property);
                      break;
                      case Number(View.VIEW_TABLE):
                        this.propertiesFilteredTable.push(property);
                      break;
                    }
                  }
                  this.updateFilterMenu();
                  this.updateDisplay();
                })
              });

              // SEARCH
              document.querySelector('.view-container__top__settings__search-container__input')
                .addEventListener('input', (e) => {
                  if(e.target.value.length > 2){
                    this.search(e.target.value, this.propertiesSearch);
                  } else {
                    this.currentObjects = this.objects;
                    this.updateDisplay();
                  }
              });
              document.querySelector('.view-container__top__settings__search-container__search-box__btn')
                .addEventListener('click', (e) => {
                  event.stopPropagation(); 
                  document.querySelector('.view-container__top__settings__search-container__search-box__menu')
                    .classList.toggle('show');
              });
              document.querySelector(".view-container__top__settings__search-container__search-box__menu__header__close")
                .addEventListener('click', (e) => {
                  document.querySelector('.view-container__top__settings__search-container__search-box__menu')
                    .classList.remove('show');
              });
              let itemSearchMenu = document.querySelectorAll('.view-container__top__settings__search-container__search-box__menu__container__item__checkbox');
              itemSearchMenu.forEach(item => {
                item.addEventListener('change', (e) => {
                  let property = e.target.getAttribute('data-property');
                  if (e.target.getAttribute('data-checked') == 'true') {
                    this.propertiesSearch = this.propertiesSearch.filter(prop => prop != property);
                    e.target.setAttribute('data-checked', 'false');
                  } else {
                    this.propertiesSearch.push(property);
                    e.target.setAttribute('data-checked', 'true');
                  }
                })
              });

              // SORT
              let sortOptions = document.querySelectorAll('.view-container__top__settings__sort-box__menu__option');
              sortOptions.forEach(option => {
                option.addEventListener('click', (e) => {

                  sortOptions.forEach(opt => {
                    if (opt !== e.target) {
                      opt.setAttribute('data-order', 0);
                    }
                  });

                  let order = e.target.getAttribute('data-order');
                  if(order == 1) {
                    order = -1;
                  } else {
                    order = 1;
                  }
                  let property = e.target.getAttribute('data-property');
                  this.sortItems([
                    [property, order]
                  ]);
                })
              });

              // USABLE
              document.querySelector('.view-container__top__settings__usable-box__checkbox')
                .addEventListener('change', (e) => {
                  this.refresh(true);
              });

            // Reset popovers au click en dehors
                // Liste des popovers à fermer au click en dehors. Ne pas oublier de mettre event.stopPropagation(); à l'évènement d'ouverture pour empêcher la propagation de l'évènement de fermeture
                const popoversToClose = [
                  document.querySelector('.view-container__top__settings__search-container__search-box__menu'),
                  document.querySelector('.view-container__top__settings__filter-box__menu')
                ];
                document.addEventListener('click', function(event) {
                  popoversToClose.forEach(popover => {
                    const isClickInside = popover.contains(event.target);
                    if (!isClickInside) {
                      popover.classList.remove('show'); // Retirer la classe 'show' pour masquer le popover
                    }
                  });
                });
      }

      initAdaptDisplaySize(){
        // Sélectionnez l'élément à observer
          const targetNode = document.querySelector('.app');
          // Configurez les options de l'observateur
          const config = { attributes: true, attributeFilter: ['class'] };

          // Créez une instance de MutationObserver
          const observer = new MutationObserver((mutationsList, observer) => {
              for (let mutation of mutationsList) {
                  if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                      let container = document.querySelector('.view-container__bottom__list');
                      if (mutation.target.classList.contains('app-extend')) {
                          container.style.maxWidth = 'calc(95vw - $width-nav)';
                      } else {
                          container.style.maxWidth = '95vw';
                      }
                  }
              }
          });

          // Commencez à observer l'élément cible
          observer.observe(targetNode, config);
      }

    //_____________________________________________
    // ------- RECUPERATION DES DONNEES -----------

      setView() {
          let value = Number(this.viewChoiceSelectElement.options[this.viewChoiceSelectElement.selectedIndex].value);
          if(value == Number(View.VIEW_MINIMAL_CARD) || Number(value == View.VIEW_DETAILED_CARD) || value == Number(View.VIEW_TABLE)) {
              this.view = value;
          } else {
              this.view = View.VIEW_MINIMAL_CARD;
          }
          
          switch (Number(this.view)) {
              case Number(View.VIEW_MINIMAL_CARD):
              this.minimalCardElement.classList.add('show');
              break;
              case Number(View.VIEW_DETAILED_CARD):
              this.detailedCardElement.classList.add('show');
              break;
              case Number(View.VIEW_TABLE):
              this.tableElement.classList.add('show');
              break;
              default:
              this.minimalCardElement.classList.add('show');
              break;
          }
      }
      setUsable() {
        if (document.querySelector('.view-container__top__settings__usable-box__checkbox').checked) {
          this.usable = true;
        } else {
          this.usable = false;
        }
      }
      setLimit() {
          let limit = document.querySelector('.view-container__bottom__list__pagination__controls__select').value;
          if(limit == 'all') {
              this.itemsPerPage = this.totalObjects;
              this.limit = 200;
          } else {
              this.limit = parseInt(limit);
              this.itemsPerPage = this.limit;
          }
      }
      async setTotalItems() {
        let url = "index.php?c="+this.className.toLowerCase()+"&a=count";
        // Récupérer le nombre total d'objets;
        const countResponse = await fetch(`${url}${this.usable ? '&usable=1' : ''}`);
        let count = await countResponse.json(); // Assurez-vous que le serveur renvoie un JSON contenant le count
        if(count.state){
          this.totalObjects = count.value;
        } else {
          View.showError('Erreur lors de la récupération', count.error, "red-d-3", 7000);
          return false;
        }
      }
      setTotalPages() {
          this.totalPages = Math.ceil(this.currentObjects.length / this.itemsPerPage);
          if (this.currentPage < 1 || this.currentPage > this.totalPages ) {
              View.showError('Erreur lors de la récupération', 'Numéro de page invalide', "red-d-3", 7000);
              return []; // Numéro de page invalide
          }
      }
      async startFetch() {
          await this.setUsable();
          await this.setLimit();
          await this.setView();
          await this.setTotalItems();
          this.currentPage = 1;
          await this.fetchObjects();
          await this.setTotalPages();
      }
      async fetchTemplates() {
          try {
                let urlTemplate = "index.php?c=view&a=getTemplate&obj_type="+this.className.toLowerCase();
                let requestSettings = {
                  minimalCard : {
                      url : `${urlTemplate}&view=${View.VIEW_MINIMAL_CARD}`,
                      name : 'minimalCard'
                  },
                  detailedCard : {
                      url : `${urlTemplate}&view=${View.VIEW_DETAILED_CARD}`,
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

          } catch (error) {
              View.showError('Erreur lors de la récupération du template', error, "red-d-3", 7000);
          }
      }  
      async fetchObjects() {
        try {
          this.initProgressBar();

          let customOffset = this.offset;
          let customLimit = this.limit;
      
          // Fonction pour faire une requête et ajouter les objets au tableau data
          const request = async () => {
            let urlBase = "index.php?c="+this.className.toLowerCase()+"&a=getAll";
            let url = `${urlBase}&offset=${customOffset}&limit=${customLimit}${this.usable ? '&usable=1' : ''}`;
            const response = await fetch(url);
            const responseData = await response.json();
            this.objects = this.objects.concat(responseData);
          }

          let itemsPerPageReached = false;
      
          // Faire des requêtes tant qu'il reste des objets à récupérer
          while (this.totalObjects > customOffset) {
              this.updateProgressBar(customOffset, this.totalObjects);
              if (this.totalObjects - customOffset < customLimit) {
                  customLimit = this.totalObjects - customOffset;
              }
              if(customOffset > this.itemsPerPage && itemsPerPageReached == false) {
                  this.whenFetchOffsetReachedItemPerPage();
                  itemsPerPageReached = true;
              }
              await request();
              customOffset += customLimit;
          }
          this.updateProgressBar(this.totalObjects, this.totalObjects);
          setTimeout(() => {
              this.initProgressBar();
          }, 10000);

          this.whenFetchFinished();
          return true;

        } catch (error) {
          View.showError('Erreur lors de la récupération', error, "red-d-3", 7000);
          return false;
        }
      }
      async whenFetchOffsetReachedItemPerPage() {
        this.currentObjects = this.objects;
        switch (Number(this.view)) {
          case Number(View.VIEW_MINIMAL_CARD):
              await this.initMinimalCardWhenFetchLimitReachedItemsPerPage();
          break;
          case Number(View.VIEW_DETAILED_CARD):
              await this.initDetailedCardWhenFetchLimitReachedItemsPerPage();
          break;
          case Number(View.VIEW_TABLE):
              await this.initTableWhenFetchLimitReachedItemsPerPage();
          break;
        }

        let pagination = document.querySelector('.view-container__bottom__list__pagination__number-box__container-pages-btn');
        pagination.innerHTML = `<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>`;
      }
      async whenFetchFinished() {
        this.currentObjects = this.objects;
        switch (Number(this.view)) {
          case Number(View.VIEW_MINIMAL_CARD):
              this.initMinimalCardWhenFetchFinished();
          break;
          case Number(View.VIEW_DETAILED_CARD):
              this.initDetailedCardWhenFetchFinished();
          break;
          case Number(View.VIEW_TABLE):
              this.initTableWhenFetchFinished();
          break;
        }
      }

    //_____________________________________________
    // --------------- SORT & SEARCH --------------
      // Properties doit être un tableau de propriétés dans lequel on va faire la recherche
      search(term, properties) {
        if (this.searchTimeout) {
          clearTimeout(this.searchTimeout);
        }
      
        this.searchTimeout = setTimeout(() => {
          term = replaceSpecialChars(term.toLowerCase().trim().replace(/\s/g, ''));
          this.currentObjects = this.objects.filter(obj => {
            return properties.some(prop => {
              if (obj.base[prop]) {
                const value = replaceSpecialChars(String(obj.base[prop]).toLowerCase().trim());
                return value.includes(term);
              }
              return false;
            });
          });
          this.updateDisplay();
        }, 500); // 500ms de délai avant de lancer la recherche
      }
      
      
      sortItems(sortParams = [['level', 1]]) {
        // Filtrer et valider les propriétés et ordres
        const validParams = sortParams.filter(param => {
          const [property, order] = param;
          return this.properties.includes(property) && (order === 1 || order === -1);
        });
      
        // S'assurer qu'il y a au moins une propriété valide
        if (validParams.length === 0) {
          let fallbackProperty;
          if (this.properties.includes('level')) {
            fallbackProperty = 'level';
          } else if (this.properties.includes('name')) {
            fallbackProperty = 'name';
          } else {
            fallbackProperty = 'uniqid';
          }
          validParams.push([fallbackProperty, 1]);
        }
      
        // Fonction de comparaison pour le tri
        const compare = (a, b, property, order) => {
          const valueA = replaceSpecialChars(String(a.base[property]).toLowerCase().trim());
          const valueB = replaceSpecialChars(String(b.base[property]).toLowerCase().trim());
      
          // Comparaison pour les nombres
          if (!isNaN(valueA) && !isNaN(valueB)) {
            return (valueA - valueB) * order;
          } 
          // Comparaison pour les chaînes de caractères (en minuscules, normalisées)
          else {
            if (valueA < valueB) return -1 * order;
            if (valueA > valueB) return 1 * order;
            return 0;
          }
        };
      
        // Fonction de tri par comparaison multiple
        this.currentObjects.sort((a, b) => {
          for (const [property, order] of validParams) {
            const result = compare(a, b, property, order);
            if (result !== 0) return result;
          }
          return 0;
        });
      
        this.updateDisplay();
      }
      
    //_____________________________________________
    // --------------- MAJ INTERFACE ---------------
      // Progress Bar
      updateProgressBar(current, total) {
          let box = document.querySelector('.view-container__bottom__list__progress');
          box.classList.add('show');
          let percent = Math.round((current / total) * 100);
          this.progressBarElement.setAttribute('aria-valuenow', percent);
          this.progressLabelElement.style.width = percent + '%';
          this.progressLabelElement.innerHTML = percent + '%';
      }
      initProgressBar() {
          let box = document.querySelector('.view-container__bottom__list__progress');
          box.classList.remove('show');
          this.progressBarElement.setAttribute('aria-valuenow', 0);
          this.progressLabelElement.style.width = '0%';
          this.progressLabelElement.innerHTML = '0%';
      }
      
      // PAGINATION
      updateBtnPagination() {
        let paginationContainerElement = document.querySelector('.view-container__bottom__list__pagination__number-box__container-pages-btn');
        paginationContainerElement.innerHTML = ''; // Effacer les boutons de pagination existants

        for (let i = 1; i <= this.totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.classList.add('btn', 'btn-sm', 'btn-main-text');
            pageButton.textContent = i;
            if (i === this.currentPage) {
                pageButton.classList.add('active');
            }
            pageButton.addEventListener('click', () => this.goToPage(i));
            paginationContainerElement.appendChild(pageButton);
        }
      }
      goToPage(pageNumber) {
          if (pageNumber < 1 || pageNumber > this.totalPages) {
              View.showError('Erreur lors de la récupération', 'Numéro de page invalide', "red-d-3", 7000);
              return; // Numéro de page invalide
          }
          if(pageNumber == this.currentPage) return; // Si on est déjà sur la page demandée

          if(pageNumber == 1) {
            document.querySelector('.view-container__bottom__list__pagination__number-box__previous').setAttribute('disabled', 'true');
          } else {
            document.querySelector('.view-container__bottom__list__pagination__number-box__previous').removeAttribute('disabled');
          }
          if(pageNumber == this.totalPages) {
            document.querySelector('.view-container__bottom__list__pagination__number-box__next').setAttribute('disabled', 'true');
          } else {
            document.querySelector('.view-container__bottom__list__pagination__number-box__next').removeAttribute('disabled');
          }

          this.currentPage = pageNumber;
          this.updateDisplay();
      }

      // Affichage des données
      async refresh(download_data = false) {
          await this.clearDataDisplay();
          if(this.objects.length == 0 || download_data) {
              await this.startFetch();
          } else {
              this.currentObjects = this.objects;
              await this.setTotalPages();
              await this.setUsable();
              await this.setLimit();
              await this.setView();
              await this.setTotalItems();
              this.currentPage = 1;
              await this.updateDisplay();
          }
      }
      async updateDisplay() {
          this.clearDataDisplay();
          this.setLimit();
          this.setTotalPages();
          this.updateBtnPagination();
          let obj = this.currentObjects.slice((this.currentPage - 1) * this.itemsPerPage, this.currentPage * this.itemsPerPage);
          switch (Number(this.view)) {
              case Number(View.VIEW_MINIMAL_CARD):
                  this.displayMinimalCard(obj);
              break;
              case Number(View.VIEW_DETAILED_CARD):
                  this.displayDetailedCard(obj);
              break;
              case Number(View.VIEW_TABLE):
                  this.displayTable(obj);
              break;
              default:
                View.showError('Erreur lors de la récupération', 'Type de vue invalide', "red-d-3", 7000);
          }
      }

      // Modifier l'interface
      updateFilterMenu() {
          const filterMenuItem = document.querySelectorAll('.view-container__top__settings__filter-box__menu__container__item__checkbox');
          filterMenuItem.forEach(item => {
              let property = item.getAttribute('data-property');
              item.checked = true;
              switch (Number(this.view)) {
                case Number(View.VIEW_MINIMAL_CARD):
                    item.checked = !this.propertiesFilteredMinimalCard.includes(property);
                break;
                case Number(View.VIEW_DETAILED_CARD):
                    item.checked = !this.propertiesFilteredDetailedCard.includes(property);
                break;
                case Number(View.VIEW_TABLE):
                    item.checked = !this.propertiesFilteredTable.includes(property);
                break;
              }
          });
      }
    
      // Effacer les données
      clearDataDisplay(){
          const tbody = this.tableElement.querySelector('.view-container__bottom__list__table-view tbody');
          tbody.innerHTML = '';
          this.detailedCardElement.innerHTML = '';
          this.minimalCardElement.innerHTML = '';
      }
      hiddenAllViews(){
          this.tableElement.classList.remove('show');
          this.detailedCardElement.classList.remove('show');
          this.minimalCardElement.classList.remove('show');
      }

    //_____________________________________________
    // ---------------- MINIMAL CARD --------------
      async initMinimalCard() {

      }
      initMinimalCardWhenFetchLimitReachedItemsPerPage() {
      }
      initMinimalCardWhenFetchFinished() {
        
      }
      displayMinimalCard(objects) {
        console.log(objects);
      }

    //_____________________________________________
    // --------------- DETAILED CARD --------------
      async initDetailedCard() {

      }
      initDetailedCardWhenFetchLimitReachedItemsPerPage() {
      }
      initDetailedCardWhenFetchFinished() {
        
      }
      displayDetailedCard(objects) {
        console.log(objects);
      }

    //_____________________________________________
    // ------------------- TABLE -------------------
      async initTable() {

      }
      initTableWhenFetchLimitReachedItemsPerPage() {
        this.displayTable(this.currentObjects.slice((this.currentPage - 1) * this.itemsPerPage, this.currentPage * this.itemsPerPage));
      }
      async initTableWhenFetchFinished() {
        await this.filterTable();
        await this.updateFilterMenu();
        this.updateBtnPagination();
      }
      displayTable(objects) {
        const tbody = this.tableElement.querySelector('tbody');
        
        // Obtenir toutes les colonnes pour identifier les data-property
        const columns = this.tableElement.querySelectorAll('thead th');
        let idrow = 0;
        objects.forEach(obj => {
            const tr = document.createElement('tr');
            tr.setAttribute('data-idrow', idrow);
            tr.addEventListener('click', (e) => {
              if(tr.classList.contains('selected')) {
                this.idRowSelected = this.idRowSelected.filter(id => id != idrow);
                tr.classList.remove('selected');
              } else {
                this.idRowSelected.push(idrow);
                tr.classList.add('selected');
              };

              tr.addEventListener('dblclick', (e) => {
                if (this.classReference && typeof this.classReference.open === 'function') {
                  this.classReference.open(obj.base.uniqid);
                } else {
                  View.showError('Erreur d\'ouverture de l\'objet', 'Impossible d\'accéder à la classe', "red-d-3", 7000);
                }
              });

            });
            for (const [key, value] of Object.entries(obj.base)) {
              tr.setAttribute(`data-prop-${key}`, value);
            }
            
            columns.forEach(column => {
                const field = column.getAttribute('data-property');
                if(!this.propertiesFilteredTable.includes(field)) {
                  column.style.display = 'table-cell';
                  const format = column.getAttribute('data-format');
                  const idcol = column.getAttribute('data-idcol');
                  const td = document.createElement('td');
                  td.setAttribute('data-idcol',idcol);
                  td.setAttribute('data-idrow',idrow);
                  td.innerHTML = obj[format][field] !== null && obj[format][field] !== undefined ? obj[format][field] : '';
                  tr.appendChild(td);
                }
            });
        
            tbody.appendChild(tr);
            idrow++;
        });
        this.filterTable();
      }
      filterTable() {
        this.propertiesFilteredTable.forEach(property => {
            let th = this.tableElement.querySelector(`thead th[data-property=${property}]`);
            if(th) {
                let id = th.getAttribute('data-idcol');
                this.tableElement.querySelectorAll(`tbody td[data-idcol='${id}']`).forEach(td => {
                    td.style.display = 'none';
                });
                th.style.display = 'none';
            }
        });
      }
        
    //_____________________________________________
    // ----------- AUTRES METHODES ----------------
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