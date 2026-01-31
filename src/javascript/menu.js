document.addEventListener('DOMContentLoaded', function() {
    const menu = document.getElementById("menu");
    const hamburger = document.getElementById("hamburger");
    
    if (!menu || !hamburger) return;
    
    const links = menu.querySelector("ul");
    let aperto = false;

    function openMenu() {
        aperto = true;
        menu.classList.add("aperto");
        hamburger.setAttribute("aria-label", "Chiudi menù");
        if (links) links.removeAttribute("inert");
    }

    function closeMenu() {
        aperto = false;
        menu.classList.remove("aperto");
        hamburger.setAttribute("aria-label", "Apri menù");
        if (window.innerWidth <= 731 && links) {
            links.setAttribute("inert", "");
        }
    }

    function toggleMenu(ev) {
        if (ev) {
            ev.preventDefault();
            ev.stopPropagation();
        }
        aperto ? closeMenu() : openMenu();
    }

    hamburger.ontouchend = toggleMenu;
    hamburger.onclick = toggleMenu;

    document.ontouchend = (ev) => {
        if (!aperto) return;
        if (!menu.contains(ev.target) && !hamburger.contains(ev.target)) {
            closeMenu();
        }
    };

    document.onclick = (ev) => {
        if (!aperto) return;
        if (!menu.contains(ev.target) && !hamburger.contains(ev.target)) {
            closeMenu();
        }
    };

    function aggiornaMenu() {
        if (window.innerWidth <= 731) {
            if (!aperto && links) {
                links.setAttribute("inert", "");
            }
        } else {
            if (links) links.removeAttribute("inert");
            if (aperto) closeMenu();
        }
    }

    aggiornaMenu();
    window.addEventListener("resize", aggiornaMenu);
});