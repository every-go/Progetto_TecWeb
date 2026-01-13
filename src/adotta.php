<?php
    session_start();
    require_once "." . DIRECTORY_SEPARATOR . "connectionDB.php";
    use DB\DBAccess;

    // controllo se l'utente è loggato
    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] !== true) {
        // reindirizzo al login con messaggio
        $_SESSION['messaggio_errore'] = "Devi effettuare il <span lang='en'>login</span> per adottare un animale";
        header("Location: login.php");
        exit();
    }

    // controllo se un admin sta adottando
    if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
        $_SESSION['messaggio_errore'] = "Gli amministratori non possono adottare animali";
        header("Location: animale.php?id=" . intval($_GET["id"]));
        exit();
    }

    // controllo sull'ID
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
        // controllo che l'animale non sia già adottato
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
            header("Location: animale.php?id=" . $animalId);
            exit();
        }
        
        $adozioneRiuscita = $db->adottaAnimale($username, $animalId);
        $db->closeConnection();
        
        if ($adozioneRiuscita) {
            $_SESSION['messaggio_adozione'] = "Complimenti! Hai adottato " . htmlspecialchars($animalData["nome"]) . " con successo!";
            header("Location: animale.php?id=" . $animalId);
            exit();
        } else {
            // errore nell'adozione
            $_SESSION['messaggio_errore'] = "Si è verificato un errore durante l'adozione. Riprova più tardi.";
            header("Location: animale.php?id=" . $animalId);
            exit();
        }
    } else {
        include __DIR__ . "/500.php";
        http_response_code(500);
        exit();
    }
?>