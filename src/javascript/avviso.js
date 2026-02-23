setTimeout(function () {
    let avviso = document.getElementById('avviso');
    if (avviso) {
        avviso.classList.add('opacitaAvviso');
        setTimeout(function () {
            avviso.remove();
        }, 500);
    }
}, 4000);