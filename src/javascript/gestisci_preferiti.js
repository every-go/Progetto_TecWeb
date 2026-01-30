// modal avviso
const modalAvviso = document.getElementById('modal-avviso');
const btnAccedi = document.getElementById('vai-accedi');
const btnAnnullaAvviso = document.getElementById('annulla-accedi');
let elementoPrecedenteAvviso = null;

function apriModalAvviso() {
    elementoPrecedenteAvviso = document.activeElement;
    modalAvviso.style.display = 'flex';
    document.body.classList.add('modal-open');
 
    setTimeout(() => {
        if (btnAccedi) {
            btnAccedi.focus();
        }
    }, 100);
    
    modalAvviso.addEventListener('keydown', gestisciTastieraAvviso);
    document.addEventListener('keydown', chiudiConEscapeAvviso);
}

function chiudiModalAvviso() {
    modalAvviso.style.display = 'none';
    document.body.classList.remove('modal-open');
    
    if (elementoPrecedenteAvviso) {
        elementoPrecedenteAvviso.focus();
    }
    
    modalAvviso.removeEventListener('keydown', gestisciTastieraAvviso);
    document.removeEventListener('keydown', chiudiConEscapeAvviso);
}

function gestisciTastieraAvviso(e) {
    if (e.key === 'Tab') {
        const elementiFocusabili = modalAvviso.querySelectorAll(
            'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled])'
        );
        
        const primoElemento = elementiFocusabili[0];
        const ultimoElemento = elementiFocusabili[elementiFocusabili.length - 1];
        
        if (e.shiftKey && document.activeElement === primoElemento) {
            e.preventDefault();
            ultimoElemento.focus();
        } 
        else if (!e.shiftKey && document.activeElement === ultimoElemento) {
            e.preventDefault();
            primoElemento.focus();
        }
    }
}

function chiudiConEscapeAvviso(e) {
    if (e.key === 'Escape') {
        chiudiModalAvviso();
    }
}

if (modalAvviso) {
    modalAvviso.addEventListener('click', function(e) {
        if (e.target === modalAvviso) {
            chiudiModalAvviso();
        }
    });
}

if (btnAccedi) {
    btnAccedi.addEventListener('click', function() {
        
        const path = window.location.pathname;
        
        const search = window.location.search;

        const relativeUrl = path + search;

        const redirectParam = encodeURIComponent(relativeUrl);

        window.location.href = './login.php?redirect=' + redirectParam;
    });
}
if (btnAnnullaAvviso) {
    btnAnnullaAvviso.addEventListener('click', function() {
        chiudiModalAvviso();
    });
}

// modal conferma
const modalConferma = document.getElementById('modal-conferma');
const btnConferma = document.getElementById('conferma-si');
const btnAnnullaConferma = document.getElementById('conferma-no');
let formDaInviare = null;
let elementoPrecedenteConferma = null;

document.querySelectorAll('.form-adozione').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch('./api/verifica_sessione.php')
            .then(response => response.json())
            .then(data => {
                if (!data.logged_in) { // se non loggato mostra modal di avviso
                    apriModalAvviso();
                } else { // se loggato mostra modal di conferma
                    formDaInviare = this;
                    apriModalConferma();
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert("Errore di connessione");
            });
    });
});

if (btnConferma) {
    btnConferma.addEventListener('click', () => {
        if (formDaInviare) {
            formDaInviare.submit();
        }
    });
}

if (btnAnnullaConferma) {
    btnAnnullaConferma.addEventListener('click', () => {
        chiudiModalConferma();
    });
}

function apriModalConferma() {
    elementoPrecedenteConferma = document.activeElement;
    modalConferma.style.display = 'flex';
    document.body.classList.add('modal-open');

    setTimeout(() => {
        if (btnConferma) {
            btnConferma.focus();
        }
    }, 100);
    
    modalConferma.addEventListener('keydown', gestisciTastieraConferma);
    document.addEventListener('keydown', chiudiConEscapeConferma);
}

function chiudiModalConferma() {
    modalConferma.style.display = 'none';
    document.body.classList.remove('modal-open');
    
    if (elementoPrecedenteConferma) {
        elementoPrecedenteConferma.focus();
    }
    
    modalConferma.removeEventListener('keydown', gestisciTastieraConferma);
    document.removeEventListener('keydown', chiudiConEscapeConferma);
    
    formDaInviare = null;
}

function gestisciTastieraConferma(e) {
    if (e.key === 'Tab') {
        const elementiFocusabili = modalConferma.querySelectorAll(
            'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled])'
        );
        
        const primoElemento = elementiFocusabili[0];
        const ultimoElemento = elementiFocusabili[elementiFocusabili.length - 1];
        
        if (e.shiftKey && document.activeElement === primoElemento) {
            e.preventDefault();
            ultimoElemento.focus();
        } 
        else if (!e.shiftKey && document.activeElement === ultimoElemento) {
            e.preventDefault();
            primoElemento.focus();
        }
    }
}

function chiudiConEscapeConferma(e) {
    if (e.key === 'Escape') {
        chiudiModalConferma();
    }
}

if (modalConferma) {
    modalConferma.addEventListener('click', function(e) {
        if (e.target === modalConferma) {
            chiudiModalConferma();
        }
    });
}


function caricaGestoriEventi() {
    var btn = document.getElementById('bottone-preferiti');

    if (btn) {
        btn.onclick = function() {
            var idAnimale = this.getAttribute('data-id');
            togglePreferiti(idAnimale);
        };
    }
}


function togglePreferiti(idAnimale) {
    var icona = document.getElementById('icona-preferiti'); 
    
    fetch('./api/modifica_preferiti.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_animale: idAnimale })
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.success) {
            if (data.action === 'added') {
                icona.src = "./images/png/heart_filled.png";
                icona.alt = "Rimuovi dai preferiti";
            } else {
                icona.src = "./images/png/heart.png";
                icona.alt = "Aggiungi ai preferiti";
            }
        } else {
            if (data.error === 'Devi essere autenticato') {
                apriModalAvviso();
            } else {
                console.error('Errore:', data.error);
                alert("Errore: " + data.error);
            }
        }
    })
    .catch(error => console.error('Errore di rete:', error));
}