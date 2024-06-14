class View {

    static VIEW_PANEL = 0;
    static VIEW_DETAILED_CARD = 1;
    static VIEW_MINIMAL_CARD = 2;
    static VIEW_TABLE = 3;
    static VIEW_TEXT = 4;
    static VIEW_LIST = 5;

    static VIEWS = [
        this.VIEW_PANEL = "Panneau",
        this.VIEW_DETAILED_CARD = "Carte détaillée",
        this.VIEW_MINIMAL_CARD = "Carte simplifiée",
        this.VIEW_TABLE = "Tableau",
        this.VIEW_TEXT = "Texte",
        this.VIEW_LIST = "Liste"
    ];

    constructor(fetchUrl, sortProperties, searchProperties, defaultDisplayProperties) {
      this.fetchUrl = fetchUrl;
      this.sortProperties = sortProperties;
      this.searchProperties = searchProperties;
      this.defaultDisplayProperties = defaultDisplayProperties;
      this.objects = [];
      this.templates = {};
      this.displayType = 'table';
      this.currentPage = 1;
      this.itemsPerPage = 10;
  
      this.init();
    }
  
    // Initialisation
    async init() {
      await this.fetchTemplates();
      await this.fetchObjects();
      this.render();
    }
  
    // Récupération des données
    async fetchTemplates() {
      const templateTypes = ['detailedCard', 'minimalCard', 'panel'];
      for (let type of templateTypes) {
        const response = await fetch(`/getTemplate?type=${type}`);
        this.templates[type] = await response.text();
      }
    }
  
    async fetchObjects() {
      const response = await fetch(this.fetchUrl);
      this.objects = await response.json();
    }
  
    render() {
      // Clear existing content
      document.getElementById('content').innerHTML = '';
  
      // Choose rendering method based on display type
      switch (this.displayType) {
        case 'table':
          this.renderTable();
          break;
        case 'detailedCard':
          this.renderCards('detailedCard');
          break;
        case 'minimalCard':
          this.renderCards('minimalCard');
          break;
      }
    }
  
    renderTable() {
      const table = document.createElement('table');
      const header = document.createElement('thead');
      const body = document.createElement('tbody');
  
      // Create header
      const headerRow = document.createElement('tr');
      this.defaultDisplayProperties.forEach(prop => {
        const th = document.createElement('th');
        th.innerText = prop;
        th.addEventListener('click', () => this.sortByProperty(prop));
        headerRow.appendChild(th);
      });
      header.appendChild(headerRow);
  
      // Create rows
      this.getPagedObjects().forEach(obj => {
        const row = document.createElement('tr');
        this.defaultDisplayProperties.forEach(prop => {
          const td = document.createElement('td');
          td.innerHTML = obj[prop] || '';
          row.appendChild(td);
        });
        body.appendChild(row);
      });
  
      table.appendChild(header);
      table.appendChild(body);
      document.getElementById('content').appendChild(table);
    }
  
    renderCards(templateType) {
      const container = document.createElement('div');
      this.getPagedObjects().forEach(obj => {
        const card = document.createElement('div');
        card.innerHTML = this.templates[templateType];
  
        for (const key in obj) {
          this.updateProperty(card, key, obj[key]);
        }
  
        // Store uniqid for future updates
        card.setAttribute('data-uniqid', obj.uniqid);
        container.appendChild(card);
      });
      document.getElementById('content').appendChild(container);
    }
  
    getPagedObjects() {
      const startIndex = (this.currentPage - 1) * this.itemsPerPage;
      const endIndex = this.currentPage * this.itemsPerPage;
      return this.objects.slice(startIndex, endIndex);
    }
  
    // Event Tri, Recherche, Pagination, Rafraîchissement
    sortByProperty(prop) {
      this.objects.sort((a, b) => {
        if (a[prop] < b[prop]) return -1;
        if (a[prop] > b[prop]) return 1;
        return 0;
      });
      this.render();
    }
  
    searchObjects(query) {
      const lowerCaseQuery = query.toLowerCase();
      this.objects = this.objects.filter(obj => {
        return this.searchProperties.some(prop => {
          return obj[prop].toString().toLowerCase().includes(lowerCaseQuery);
        });
      });
      this.render();
    }
  
    changeDisplayType(type) {
      this.displayType = type;
      this.render();
    }
  
    setItemsPerPage(count) {
      this.itemsPerPage = count;
      this.render();
    }
  
    nextPage() {
      this.currentPage++;
      this.render();
    }
  
    previousPage() {
      this.currentPage--;
      this.render();
    }
  
    refreshObject(uniqid) {
      fetch(`/getObject?uniqid=${uniqid}`)
        .then(response => response.json())
        .then(updatedObject => {
          const element = document.querySelector(`[data-uniqid="${uniqid}"]`);
          for (const key in updatedObject) {
            this.updateProperty(element, key, updatedObject[key]);
          }
        });
    }

    updateProperty(element, property, value) {
      const elements = element.querySelectorAll(`[data-render_prop="${property}"]`);
      elements.forEach(el => {
        const position = el.getAttribute('data-render_position') || 'innerhtml';
        const template = el.getAttribute('data-render_template') || '{value}';
        const replace = el.getAttribute('data-render_replace') === 'true';
  
        const integratedValue = template.replace('{value}', value);
  
        switch (position) {
          case 'innerhtml':
            if (replace) {
              el.innerHTML = integratedValue;
            } else {
              el.innerHTML += integratedValue;
            }
            break;
          case 'class':
            if (replace) {
              el.className = integratedValue;
            } else {
              el.className += ' ' + integratedValue;
            }
            break;
          case 'id':
            if (replace) {
              el.id = integratedValue;
            }
            break;
          case 'title':
            if (replace) {
              el.title = integratedValue;
            } else {
              el.title += ' ' + integratedValue;
            }
            break;
          // Add more cases as needed
        }
      });
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