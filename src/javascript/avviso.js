var durata = 4000; // 4 secondi
var intervallo = 333; // aggiornamento ogni 50ms

var avviso = document.getElementById('avviso');
var progressBar = avviso ? avviso.querySelector('.progress-bar') : null;

if (avviso && progressBar) {
    var tempoPassato = 0;

    var timer = setInterval(function () {
        tempoPassato += intervallo;
        var percentuale = 100 - (tempoPassato / durata) * 100;
        progressBar.style.width = percentuale + '%';

        if (tempoPassato >= durata) {
            clearInterval(timer);

            avviso.style.opacity = '0';
            setTimeout(function () {
                avviso.remove();
            }, 500); // dissolvenza
        }
    }, intervallo);
}