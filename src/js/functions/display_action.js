function initCompetitionResume(target) {
    $(target).find('.resume').each(function() {
      var $resume = $(this);
      var $parentContainer = $resume.closest('.resume-parent-container');
      var initialWidth = $resume.width(); // Récupère la valeur de la largeur initiale
      var initialHeight = $resume.height(); // Récupère la valeur de la hauteur initiale
  
      // Ajoute la classe "reduced" à toutes les boîtes de résumé
      $resume.addClass('reduced').css('width', initialWidth / 2 + 'px');
      $parentContainer.css('width', initialWidth / 2 + 'px');
      $parentContainer.css('height', initialHeight = 'px');
      
      // Gestionnaire d'événement pour le survol de la souris
      $resume.hover(
        function() {
          // Supprime la classe "reduced" et modifie la largeur au survol de la souris
          $resume.removeClass('reduced').css('width', initialWidth + 'px').addClass("overlay");
        },
        function() {
          // Ajoute à nouveau la classe "reduced" et restaure la largeur lorsque la souris quitte
          $resume.addClass('reduced').removeClass("overlay").css('width', initialWidth / 2 + 'px');
        }
      );
    });
  }
  