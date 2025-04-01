// Fonction pour vérifier si un élément est visible dans la fenêtre
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.top <= (window.innerHeight || document.documentElement.clientHeight)
    );
}

// Fonction pour gérer l'apparition au scroll
function handleScroll() {
    const forum = document.querySelector('.forum');
    if (isElementInViewport(forum)) {
        forum.classList.add('visible');
        // On retire l'écouteur d'événement une fois l'animation déclenchée
        window.removeEventListener('scroll', handleScroll);
    }
}

// Ajout de l'écouteur d'événement pour le scroll
window.addEventListener('scroll', handleScroll);

// Vérification initiale au chargement de la page
handleScroll();
