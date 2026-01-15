const menu = document.getElementById("menu");
const links = menu.querySelector("ul")

hamburger.addEventListener("click", (ev) => {
    aperto = menu.classList.toggle("aperto");
    if (aperto) {
        hamburger.setAttribute("aria-label", "Chiudi menù");
    } else {
        hamburger.setAttribute("aria-label", "Apri menù");
    }
    links.toggleAttribute("inert", !aperto);
});

function aggiornaMenu() {
    if (window.innerWidth <= 731) {
        if (!menu.classList.contains("aperto")) {
            links.setAttribute("inert", "");
        }
    }
    else {
        links.removeAttribute("inert");
    }
}

aggiornaMenu()
window.addEventListener("resize", aggiornaMenu)