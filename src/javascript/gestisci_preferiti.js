// Roba provvisoria che mi ha fatto gemini, n on l'ho testato
// // Funzione chiamata dal click del bottone
// function togglePreferiti(idAnimale, btnElement) {
    
//     // 1. Chiamata al server (Fetch)
//     fetch('api/toggle_favorite.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json'
//         },
//         body: JSON.stringify({ id_animale: idAnimale })
//     })
//     .then(response => response.json()) // Converto la risposta in oggetto JS
//     .then(data => {
        
//         if (data.success) {
//             // 2. Aggiorno la UI in base alla risposta
//             if (data.action === 'added') {
//                 // Aggiungo classe CSS "attivo" (es. cuore pieno)
//                 btnElement.classList.add('is-favorite');
//                 btnElement.innerHTML = '❤️ Rimuovi dai preferiti'; // O cambia solo icona
//             } else {
//                 // Rimuovo classe
//                 btnElement.classList.remove('is-favorite');
//                 btnElement.innerHTML = '🤍 Aggiungi ai preferiti';
//             }
//         } else {
//             // Gestione errori (es. utente non loggato)
//             if (data.error === 'Devi essere loggato') {
//                 alert("Per aggiungere ai preferiti devi fare il login!");
//                 window.location.href = 'login.php';
//             } else {
//                 console.error('Errore:', data.error);
//             }
//         }
//     })
//     .catch(error => console.error('Errore di rete:', error));
// }