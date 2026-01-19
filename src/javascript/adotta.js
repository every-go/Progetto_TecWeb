const modal = document.getElementById('modal-conferma');
const bottoniAdotta = document.querySelectorAll('.bottone-adotta');
const btnConferma = document.getElementById('conferma-si');
const btnAnnulla = document.getElementById('conferma-no');
let formDaInviare = null;
let elementoPrecedente = null;

// Ascolta il submit dei form
document.querySelectorAll('.form-adozione').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Ferma l'invio
        formDaInviare = this;
        apriModal();
    });
});

// Conferma adozione
btnConferma.addEventListener('click', () => {
    if (formDaInviare) {
        formDaInviare.submit(); // Invia il form
    }
});

// Annulla
btnAnnulla.addEventListener('click', () => {
    chiudiModal();
});

// Funzione per aprire il modal
function apriModal() {
    // Salva l'elemento che aveva il focus
    elementoPrecedente = document.activeElement;
    
    // Mostra il modal
    modal.style.display = 'flex';
    
    // Nascondi il resto della pagina agli screen reader
    document.body.classList.add('modal-open');
    
    // Nascondi solo se main-content esiste
    const mainContent = document.getElementById('animale-main');
    if (mainContent) {
        mainContent.setAttribute('aria-hidden', 'true');
    }
    
    // Sposta il focus sul primo bottone
    setTimeout(() => {
        btnConferma.focus();
    }, 100);
    
    // Aggiungi event listeners
    modal.addEventListener('keydown', gestisciTastiera);
    document.addEventListener('keydown', chiudiConEscape);
}

// Funzione per chiudere il modal
function chiudiModal() {
    // Nascondi il modal
    modal.style.display = 'none';
    
    // Ripristina l'accessibilità della pagina
    document.body.classList.remove('modal-open');
    
    // Ripristina solo se main-content esiste
    const mainContent = document.getElementById('animale-main');
    if (mainContent) {
        mainContent.removeAttribute('aria-hidden');
    }
    
    // Ritorna il focus all'elemento precedente
    if (elementoPrecedente) {
        elementoPrecedente.focus();
    }
    
    // Rimuovi event listeners
    modal.removeEventListener('keydown', gestisciTastiera);
    document.removeEventListener('keydown', chiudiConEscape);
    
    formDaInviare = null;
}

// Focus trap: mantieni il focus dentro il modal
function gestisciTastiera(e) {
    if (e.key === 'Tab') {
        const elementiFocusabili = modal.querySelectorAll(
            'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled])'
        );
        
        const primoElemento = elementiFocusabili[0];
        const ultimoElemento = elementiFocusabili[elementiFocusabili.length - 1];
        
        // Tab + Shift: vai all'ultimo elemento
        if (e.shiftKey && document.activeElement === primoElemento) {
            e.preventDefault();
            ultimoElemento.focus();
        } 
        // Tab: vai al primo elemento
        else if (!e.shiftKey && document.activeElement === ultimoElemento) {
            e.preventDefault();
            primoElemento.focus();
        }
    }
}

// Chiudi con Escape
function chiudiConEscape(e) {
    if (e.key === 'Escape') {
        chiudiModal();
    }
}

// Chiudi cliccando fuori dal modal (opzionale)
modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        chiudiModal();
    }
});