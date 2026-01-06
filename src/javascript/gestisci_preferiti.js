// Funzione che prepara la pagina
// Non viene eseguita subito, ma solo quando la chiamiamo noi
function caricaGestoriEventi() {
    var btn = document.getElementById('bottone-preferiti');

    if (btn) {
        btn.onclick = function() {
            // CORREZIONE 1: Leggiamo data-id, non id
            var idAnimale = this.getAttribute('data-id');
            togglePreferiti(idAnimale);
        };
    }
}


// Roba provvisoria che mi ha fatto gemini, non l'ho testato
// Funzione chiamata dal click del bottone
function togglePreferiti(idAnimale) {
    // Recupero l'immagine dentro il bottone per cambiarla dopo
    var icona = document.getElementById('icona-preferiti'); 
    // Oppure meglio: var icona = btnElement.querySelector('img');

    fetch('./api/modifica_preferiti.php', { // Controlla che il nome del file sia giusto!
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_animale: idAnimale })
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.success) {
            // CORREZIONE 2: Cambio src e alt in base alla risposta
            if (data.action === 'added') {
                // Ora è preferito -> Cuore Pieno
                icona.src = "../images/png/heart_filled.png";
                icona.alt = "Rimuovi dai preferiti";
            } else {
                // Rimosso -> Cuore Vuoto
                icona.src = "../images/png/heart.png";
                icona.alt = "Aggiungi ai preferiti";
            }
        } else {
            // Gestione errori
            if (data.error === 'Devi essere autenticato') {
                alert("Devi fare il login per usare i preferiti!");
                // window.location.href = 'login.php';
            } else {
                console.error('Errore:', data.error);
                alert("Errore: " + data.error);
            }
        }
    })
    .catch(error => console.error('Errore di rete:', error));
}