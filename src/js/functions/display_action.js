function initResponsiveCKEditorTable(){
  
}
  
function initCardHover(){
  const timeout = 200;
  const cards = document.querySelectorAll('.resume');
  cards.forEach(card => {
    const pin = card.querySelector('.pincard');
    if(pin != null) {
      pin.onclick = function() {
        tooglePinCard(card);
      };
    }
    // Initiation des cartes résumés DUO
      if(card.classList.value.includes('duo')){
        if(!card.classList.value.includes('overlay') && !card.classList.value.includes('reduced')){
          const initialWidth = parseInt(card.style.width);
          card.dataset.initialwidth = initialWidth;
          card.classList.add('reduced');
          card.style.width = initialWidth / 2 + "px";
          card.closest('.resume-parent-container').style.width = initialWidth / 2 + "px";
        }
      }
    card.onmouseover = function() {
      toogleResumeCard(card, true);
    };
    card.onmouseleave = function() {
      setTimeout(() => {
        if(pin.dataset.pin != "on"){
          toogleResumeCard(card, false);
        }
      }, timeout);
    }
  });
}

function toogleResumeCard(card, extend = true){
  if(card != null){
    if(extend){
      if(card.classList.value.includes('duo')){
        const initialWidth = card.dataset.initialwidth;
        card.classList.add('overlay');
        card.classList.remove('reduced');
        card.style.width = initialWidth + "px";
      } 
      card.classList.add('active');
    } else {
      if(card.classList.value.includes('duo')){
        const initialWidth = card.dataset.initialwidth;
        card.classList.remove('overlay');
        card.classList.add('reduced');
        card.style.width = initialWidth / 2 + "px";
      }
      card.classList.remove('active');
    }
  }
}

function tooglePinCard(card){
  const pin = card.querySelector('.pincard');
  if(pin != null){
    if(pin.dataset.pin != "on"){
      pin.dataset.pin = "on";
      toogleResumeCard(card, true);
    } else {
      pin.dataset.pin = "off";
      toogleResumeCard(card, false);
    }
  }
}

function initSearchDropdown(){
  const searchContainers = document.querySelectorAll('.dropdown__search__container');
  searchContainers.forEach(searchContainer => {
    const input = searchContainer.querySelector('.dropdown__search__container__input');
    const dropdown_menu = searchContainer.closest('.dropdown-menu');
    const items = dropdown_menu.querySelectorAll('.dropdown-item');

    input.onkeyup = function(){
      if(input.value.length < 2){
        items.forEach(item => {
          item.style.display = 'block';
        });
        return;
      }
      const value = input.value.toLowerCase();
      items.forEach(item => {
        if(item.textContent.toLowerCase().includes(value)){
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    };

  });
}

function initCollapse() {
  const selects = document.querySelectorAll('.select-collapse');
  selects.forEach(select => {
    const container = select.parentElement;

    const hideAll = () => {
      const options = container.querySelectorAll('[data-target]');
      options.forEach(option => {
        let selector = option.dataset.target;
        let target_ = container.querySelector(selector);
        target_.style.display = 'none';
      });
    };
    hideAll();

    select.onchange = function () {
      hideAll();
      const value = select.querySelector('option:checked').dataset.target;
      const target = container.querySelector(value);
      if (target) {
        target.style.display = 'flex';
      }
    };
  });
}

function initTooltipsResume() {
  const tooltipElements = document.querySelectorAll('.text_resume_tooltops-show');

  tooltipElements.forEach(element => {
    const targetSelector = element.dataset.target;
    const targetElement = document.querySelector(targetSelector);

    if (!targetElement) {
      console.warn(`Tooltip target not found: ${targetSelector}`);
      return;
    }

    // Create a clone of the target element
    const clonedTooltip = targetElement.cloneNode(true);
    clonedTooltip.style.display = 'none';
    document.body.appendChild(clonedTooltip);

    element.addEventListener('mouseenter', (event) => {
      clonedTooltip.style.display = 'block';
      clonedTooltip.style.zindex = 99999;

      const updateTooltipPosition = (event) => {
        const screenWidth = window.innerWidth;
        const screenHeight = window.innerHeight;
        const cursorX = event.clientX;
        const cursorY = event.clientY;
        const offset = 3;
        const tooltipHeight = clonedTooltip.offsetHeight;

        // Determine horizontal position
        if (cursorX > screenWidth / 2) {
          // Cursor in the right half of the screen
          clonedTooltip.style.left = `${cursorX - clonedTooltip.offsetWidth - offset}px`;
        } else {
          // Cursor in the left half of the screen
          clonedTooltip.style.left = `${cursorX + offset}px`;
        }

        // Determine vertical position
        if (cursorY + tooltipHeight + offset > screenHeight) {
          // Not enough space below
          if (cursorY - tooltipHeight - offset < 0) {
            // Not enough space above either, place in the middle
            clonedTooltip.style.top = `${screenHeight / 2 - tooltipHeight / 2}px`;
          } else {
            // Place above the cursor
            clonedTooltip.style.top = `${cursorY - tooltipHeight - offset}px`;
          }
        } else {
          // Enough space below, place below the cursor
          clonedTooltip.style.top = `${cursorY + offset}px`;
        }
      };

      updateTooltipPosition(event); // Initial position
      document.addEventListener('mousemove', updateTooltipPosition);

      element.addEventListener('mouseleave', () => {
        clonedTooltip.style.display = 'none';
        document.removeEventListener('mousemove', updateTooltipPosition);
      }, { once: true });
    });
  });
}

// Initialize tooltips
initTooltipsResume();
