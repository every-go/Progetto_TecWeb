<?php
session_start();
require_once "utils/animalFormHandler.php";
require_once "." . DIRECTORY_SEPARATOR . "connectionDB.php";

use DB\DBAccess;

if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] !== true) {
    $_SESSION['messaggio_errore'] = "Devi effettuare il <span lang=\"en\">login</span> per adottare un animale";

    // Memorizza la pagina corrente per il reindirizzamento dopo il login
    $paginaTarget = "./adotta.php?" . createHttpQuery($_GET, ['pagina', 'search', 'id'], false);
    header("Location: login.php?redirect=" . urlencode($paginaTarget));
    exit();
}

if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
    $_SESSION['messaggio_errore'] = "Gli amministratori non possono adottare animali";
    $parametriQuery = createHttpQuery($_GET, ['pagina', 'search', 'id'], false);
    header("Location: ./animale.php?" . $parametriQuery);
    exit();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    include __DIR__ . "/404.php";
    http_response_code(404);
    exit();
}

$animalId = intval($_GET["id"]);
$username = $_SESSION["username"];

$db = new DBAccess();
$connessioneOK = $db->openDBConnection();

if ($connessioneOK) {
    $animalData = $db->getAnimalById($animalId);

    if (!$animalData) {
        $db->closeConnection();
        include __DIR__ . "/404.php";
        http_response_code(404);
        exit();
    }

    if ($animalData["adottato"] == 1) {
        $db->closeConnection();
        $_SESSION['messaggio_errore'] = "Questo animale è già stato adottato";
        $_GET['id'] = $animalId;
        $parametriQuery = createHttpQuery($_GET, ['pagina', 'search', 'id'], false);
        header("Location: ./animale.php?" . $parametriQuery);
        exit();
    }

    $adozioneRiuscita = $db->adottaAnimale($username, $animalId);
    $db->closeConnection();

    if ($adozioneRiuscita) {
        $_SESSION['messaggio_adozione'] = "Complimenti! Hai adottato " . htmlspecialchars($animalData["nome"]) . " con successo!";
        $_GET['id'] = $animalId;
        $parametriQuery = createHttpQuery($_GET, ['pagina', 'search', 'id'], false);
        header("Location: ./animale.php?" . $parametriQuery);
        exit();
    } else {
        $_SESSION['messaggio_errore'] = "Si è verificato un errore durante l'adozione. Riprova più tardi.";
        $_GET['id'] = $animalId;
        $parametriQuery = createHttpQuery($_GET, ['pagina', 'search', 'id'], false);
        header("Location: ./animale.php?" . $parametriQuery);
        exit();
    }
} else {
    include __DIR__ . "/500.php";
    http_response_code(500);
    exit();
}
?>