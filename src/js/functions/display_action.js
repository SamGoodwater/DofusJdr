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
  if(pin.dataset.pin != "on"){
    pin.dataset.pin = "on";
    toogleResumeCard(card, true);
  } else {
    pin.dataset.pin = "off";
    toogleResumeCard(card, false);
  }
}

let is_tooltipsCustom_triggerable = true;
function initTooltipResume(){
  const offset = 10;

  const cleanTooltips = () => {
    const clones = document.querySelectorAll(".container-tooltips");
    clones.forEach(clone => {
      clone.remove();
    });
  };

    const triggers = document.querySelectorAll('.text_resume_tooltops-show');
    triggers.forEach(trigger => {

        trigger.onmouseleave = function (event) {
          if (event.target.classList.contains('container-tooltips') !== true) {
            cleanTooltips();
          }
        };

        trigger.onmouseover = function () {
          if(is_tooltipsCustom_triggerable){
            is_tooltipsCustom_triggerable = false;
            setTimeout(function(){is_tooltipsCustom_triggerable = true;}, 250);

            const targetElement = document.querySelector(trigger.dataset.target);
            if (targetElement) {
              let x = 0; let y = 0;
              const triggerRect = trigger.getBoundingClientRect();
              if(triggerRect.right + targetElement.offsetWidth > window.innerWidth){
                x = triggerRect.x - targetElement.offsetWidth - offset;
              } else {
                x = triggerRect.width + offset;
              }
              y = triggerRect.y + offset;

              let clone = targetElement.cloneNode(true);
              clone.style.display = 'flex';
              clone.style.position = "fixed";
              clone.classList.add('container-tooltips');
              clone.style.top = x + "px";
              clone.style.left = y + "px";
              clone.onmouseleave = function () {
                cleanTooltips();
              };
              document.body.appendChild(clone);

              targetElement.style.display = 'none';
            }
          }
        };
    });
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
