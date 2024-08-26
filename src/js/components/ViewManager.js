class ViewManager{
	static initDisplay(){
		this.initDropdowns();
		this.initCollapse();
		initCardHover();
		initResponsiveCKEditorTable();
		initTooltipsResume();
		initSearchDropdown();
		$('[data-toggle="tooltip"]').tooltip();
	}
	
	// Le bouton d'initialisation du collapse prend comme attribu le data-type="colapse" et le data-target="selecteur de la box à afficher" et data-expanded="false" correspond à l'état du collapse
	static initCollapse(){
		let target = null;
		document.querySelectorAll('[data-type="collapse"]').forEach(function (btn) {
			if(btn.getAttribute('data-target') == null) return;
			target = document.querySelector(btn.getAttribute('data-target'));
			if(btn.getAttribute('data-expanded') == 'true') {
				target.classList.add('show');
				target.setAttribute('data-expanded', 'true');
			}
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				target.classList.toggle('show');
				if(target.classList.contains('show')) {
					target.setAttribute('data-expanded', 'true');
					this.setAttribute('data-expanded', 'true');
				} else {
					target.setAttribute('data-expanded', 'false');
					this.setAttribute('data-expanded', 'false');
				}
			});
		});
	}

	static initDropdowns() {
		document.querySelectorAll('[data-type="dropdown"]').forEach(function(dropdown) {
			// Vérifiez et appliquez l'état initial du dropdown
			if (dropdown.getAttribute('data-expanded') === 'true') {
				dropdown.classList.add('active');
			}

			// Sélectionnez le bouton dans le dropdown actuel
			const btn = dropdown.querySelector('button');
			if (btn) {
				if(btn.getAttribute('data-isListernerAdded') == 'true') return;
	
				// Ajoutez un écouteur d'événements pour le bouton
				btn.addEventListener('click', function(e) {
					e.preventDefault();
					dropdown.classList.toggle('active');
					if (dropdown.classList.contains('active')) {
						dropdown.setAttribute('data-expanded', 'true');
					} else {
						dropdown.setAttribute('data-expanded', 'false');
					}
				});
				btn.setAttribute('data-isListernerAdded', 'true');
			}
	
			// Ajoutez un écouteur d'événements pour fermer les dropdowns en cliquant à l'extérieur
			document.addEventListener('click', function(e) {
				if (!dropdown.contains(e.target)) {
					dropdown.classList.remove('active');
					dropdown.setAttribute('data-expanded', 'false');
				}
			});

		});
	}
	
}