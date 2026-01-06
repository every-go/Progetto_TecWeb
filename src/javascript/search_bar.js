const searchInput = document.getElementById('search-input');
let timeout = null;

searchInput.addEventListener('input', function() {
    const query = this.value.trim();
            
    clearTimeout(timeout);
            
    // 800ms prima di mostrare i risultati o tornare alla pagina default
    timeout = setTimeout(() => {
        if (query.length === 0) {
            // barra di ricerca vuota => torna pagina default
            window.location.href = 'animali.php';
        } else if (query.length >= 2) {
            window.location.href = `animali.php?search=${encodeURIComponent(query)}`;
        }
    }, 800);
});