//Barre de Recherche
function searchEvent() {
    let input = document.getElementById("searchBar").value.toLowerCase();
    let events = document.querySelectorAll(".event-card");

    events.forEach(event => {
        let title = event.querySelector("h3").textContent.toLowerCase();
        let location = event.querySelector("p:nth-of-type(2)").textContent.toLowerCase();
        let description = event.querySelector("p:nth-of-type(3)").textContent.toLowerCase();

        if (title.includes(input) || location.includes(input) || description.includes(input)) {
            event.style.display = "block"; // Affiche l'événement si trouvé
        } else {
            event.style.display = "none"; // Masque l'événement s'il ne correspond pas
        }
    });
}

// Bouton "Remonter en haut"
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.querySelector('.back-to-top');
    
    // Afficher ou masquer le bouton selon la position de défilement
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) { // Apparaît après 300px de défilement
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    });
    
    // Fonction pour remonter en haut
    backToTopButton.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth' // Défilement doux
        });
    });
});