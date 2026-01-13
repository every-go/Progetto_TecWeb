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
                alert("Devi fare il login per usare i preferiti!");
            } else {
                console.error('Errore:', data.error);
                alert("Errore: " + data.error);
            }
        }
    })
    .catch(error => console.error('Errore di rete:', error));
}