// post.php - Fonction pour changer le style de 2 classes (display none to block et inversement)
function changeStyle(element1, element2) {
    document.getElementsByClassName(element1)[0].style.display = 'none';
    document.getElementsByClassName(element2)[0].style.display = 'block';
}

function displayFormProfile(element) {
    const displayElement = document.querySelector(element);
    const elements = document.querySelectorAll("[class*=display-]");
    elements.forEach(element => {
        element.style.display = 'none';
    });
    displayElement.style.display = 'flex';
}

function hideFormProfile(element) {
    const displayElement = document.querySelector(element);
    const elements = document.querySelectorAll("[class*=display-]");
    elements.forEach(element => {
        element.style.display = 'block';
    });
    displayElement.style.display = 'none';
}

// NAVBAR - Fonction pour ouvrir ou fermer le bandeau de la navbar
function toggleMenu() {
    const hamburgerButton = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-links');
    const navOverlay = document.querySelector('.overlay');
    if (!hamburgerButton.classList.contains('open')) {
        hamburgerButton.classList.add('open');
        navMenu.classList.add('open');
        navOverlay.style.zIndex = 1;
        navOverlay.style.display = 'block';
    } else {
        hamburgerButton.classList.remove('open');
        navMenu.classList.remove('open');
        navOverlay.style.zIndex = -1;
        navOverlay.style.display = 'none';
    }
}

// NAVBAR //

// Déclaration des élements
const navOverlay = document.querySelector('.overlay');
// Ecouteur d'évènement sur l'overlay
navOverlay.addEventListener('click', function (event) {
    toggleMenu();
});

//