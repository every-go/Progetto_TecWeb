<?php
// api/toggle_favorite.php
require_once __DIR__ . '/../connectionDB.php';
use DB\DBAccess;

session_start();

// 1. Diciamo al browser che risponderemo in JSON
header('Content-Type: application/json');

// 2. Controllo Autenticazione
if (!isset($_SESSION["username"])) {
    echo json_encode(['success' => false, 'error' => 'Devi essere autenticato']);
    exit();
}

// 3. Recupero i dati inviati da JS (JSON Payload)
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!isset($input['id_animale'])&& !is_numeric($input['id_animale'])) {
    echo json_encode(['success' => false, 'error' => 'ID mancante o malformattato']);
    exit();
}

$idAnimale = intval($input['id_animale']);
$idUtente = $_SESSION['username'];

// 4. Logica DB
$db = new DBAccess();
$connOk = $db->openDBConnection();

if ($connOk) {
    try {
        // Controllo se esiste già
        // NOTA: Dovrai implementare questi metodi in DBAccess o fare la query qui
        $isFavorite = $db->checkPreferito($idUtente, $idAnimale);

        if ($isFavorite) {
            // Se c'è già, lo rimuovo
            $db->rimuoviPreferito($idUtente, $idAnimale);
            $status = 'removed';
        } else {
            // Se non c'è, lo aggiungo
            $db->aggiungiPreferito($idUtente, $idAnimale);
            $status = 'added';
        }

        echo json_encode(['success' => true, 'action' => $status]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Errore DB']);
    } finally {
        $db->closeConnection();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Connessione DB fallita']);
}
?>