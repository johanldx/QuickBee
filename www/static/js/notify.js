document.addEventListener('DOMContentLoaded', function() {
    // Récupère toutes les divs avec la classe .notif
    var notifications = document.querySelectorAll('.notif');
  
    // Parcours chaque div et les masque après 5 secondes
    notifications.forEach(function(div) {
        setTimeout(function() {
            div.style.display = 'none';
        }, 3000);
    });
  });