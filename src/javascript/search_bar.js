const searchInput = document.getElementById('search-input');
let timeout = null;

searchInput.addEventListener('input', function() {
    const query = this.value.trim();
            
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        if (query.length === 0) {
            window.location.href = 'animali.php';
        }
    }, 800);
});