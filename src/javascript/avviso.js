setTimeout(function() {
    var avviso = document.getElementById('avviso');
    if (avviso) {
        avviso.style.opacity = '0';
        setTimeout(function() { 
            avviso.remove(); 
        }, 500); // mezzo secondo per la dissolvenza
    }
}, 4000);