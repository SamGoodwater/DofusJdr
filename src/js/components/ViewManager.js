class ViewManager{
	static initDisplay(){
		this.initDropdowns();
		this.initCollapse();
		this.initDrawer();
		initCardHover();
		initResponsiveCKEditorTable();
		initTooltipsResume();
		initSearchDropdown();
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
	
	// Le bouton d'initialisation du dropdown prend comme attribu le data-type="dropdown" et le data-target="selecteur du menu déroulant" et data-expanded="false" correspond à l'état du dropdown
	static initDropdowns(){ 
		let target = null;
		document.querySelectorAll('[data-type="dropdown"]').forEach(function (btn) {
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
			
			document.addEventListener('click', function (e) {
				if (!e.target.matches('[data-type="dropdown"]')) {
					document.querySelectorAll('[data-type="dropdown"] + div ul').forEach(function (menu) {
						menu.classList.remove('show');
						menu.setAttribute('data-expanded', 'false');
					});
					document.querySelectorAll('[data-type="dropdown"]').forEach(function (btn) {
						btn.classList.remove('show'); // Fermer la flèche quand on clique à l'extérieur
						btn.setAttribute('data-expanded', 'false');
					});
				}
			});
		});
	}

	static initDrawer(){
		let target = null;
		document.querySelectorAll('[data-type="drawer"]').forEach(function (btn) {
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
			
			document.addEventListener('click', function (e) {
				if (!e.target.matches('[data-type="drawer"]')) {
					document.querySelectorAll('[data-type="dropdown"]').forEach(function (btn) {
						btn.setAttribute('data-expanded', 'false');
						target = document.querySelector(btn.getAttribute('data-target'));
						target.classList.remove('show');
						target.setAttribute('data-expanded', 'false');
					});
				}
			});
		});
	}
}