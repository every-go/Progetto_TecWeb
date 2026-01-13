<?php
require_once __DIR__ . "/../connectionDB.php";
use DB\DBAccess;

session_start();

header("Content-Type: application/json");

if (!isset($_SESSION["username"])) {
    echo json_encode([
        "success" => false,
        "error" => "Devi essere autenticato",
    ]);
    exit();
}

$inputJSON = file_get_contents("php://input");
$input = json_decode($inputJSON, true);

if (!isset($input["id_animale"]) || !is_numeric($input["id_animale"])) {
    echo json_encode([
        "success" => false,
        "error" => "ID mancante o non valido",
    ]);
    exit();
}

$idAnimale = intval($input["id_animale"]);
$idUtente = $_SESSION["username"];

$db = new DBAccess();
$connOk = $db->openDBConnection();

if ($connOk) {
    try {
        $isFavorite = $db->checkPreferito($idUtente, $idAnimale);

        if ($isFavorite) {
            $db->rimuoviPreferito($idUtente, $idAnimale);
            $status = "removed";
        } else {
            $db->aggiungiPreferito($idUtente, $idAnimale);
            $status = "added";
        }

        echo json_encode(["success" => true, "action" => $status]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode([
            "success" => false,
            "error" => "Errore nel database",
        ]);
    } finally {
        $db->closeConnection();
    }
} else {
    echo json_encode(["success" => false, "error" => "Connessione DB fallita"]);
}
?>