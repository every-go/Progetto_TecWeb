<?php
require_once 'connectionDB.php';
use DB\DBAccess;

function pulisciInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

include "menu.php";
$content = file_get_contents("../html/ispeziona_animale.html");

// Controllo accesso admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== 'admin') {
    header('Location: home.php');
    exit();
}

// Inizializzazione variabili
$formValido = true;
$imageLoaded = false;
$animaleCorrente = null;
$imagePath = '../images/jpg/cane_bello.jpg';

$campi = ['nome', 'alt', 'sesso', 'tipologia', 'luogo', 'eta', 'taglia', 'carattere', 'descrizione', 'bisogni', 'storia'];
$errori = [];

foreach ($campi as $campo) {
    $$campo = '';
    $errori[$campo] = '';
}

// ==================== ELABORAZIONE POST ====================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validazione campi di testo
    $campiTesto = ['nome', 'luogo', 'descrizione', 'bisogni', 'storia', 'alt'];
    foreach ($campiTesto as $campo) {
        $valore = $_POST[$campo] ?? '';
        if (strlen($valore) == 0) {
            $errori[$campo] = '<p>' . ucfirst($campo) . ' non inserito</p>';
            $formValido = false;
        } else if (strlen(trim($valore)) == 0) {
            $errori[$campo] = '<p>' . ucfirst($campo) . ' non può contenere solo spazi</p>';
            $formValido = false;
        }
        $$campo = pulisciInput($valore);
    }
    
    // Validazione età
    $eta = $_POST["eta"] ?? '';
    if (strlen($eta) == 0) {
        $errori['eta'] = '<p>Età non inserita</p>';
        $formValido = false;
    } else if (!filter_var($eta, FILTER_VALIDATE_INT) || $eta < 0) {
        $errori['eta'] = '<p>Età deve essere un numero naturale</p>';
        $formValido = false;
    }
    $eta = pulisciInput($eta);
    
    // Campi select/radio (già validati lato client)
    $sesso = $_POST["sesso"] ?? '';
    $tipologia = $_POST["tipo_animale"] ?? '';
    $taglia = $_POST["taglia"] ?? '';
    $carattere = $_POST["carattere"] ?? '';
    
    // Recupera dati animale se in modifica
    if (isset($_GET["id"])) {
        $id = intval($_GET["id"]);
        $db = new DBAccess();
        $db->openDBConnection();
        $animaleCorrente = $db->getAnimalById($id);
        $db->closeConnection();
        
        if ($animaleCorrente) {
            $imagePath = $animaleCorrente['immagine'];
        }
    }
    
    // Gestione upload immagine
    $uploadDir = __DIR__ . '/../images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imageLoaded = true;
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $maxSize = 1 * 1024 * 1024;
        
        $fileTmpPath = $_FILES['immagine']['tmp_name'];
        $fileName = basename($_FILES['immagine']['name']);
        $fileSize = $_FILES['immagine']['size'];
        
        if ($fileSize > $maxSize) {
            $errori['immagine'] = "<p>Dimensione troppo grande. Max 1MB.</p>";
            $formValido = false;
        } else {
            $fileType = mime_content_type($fileTmpPath);
            if (!in_array($fileType, $allowedTypes)) {
                $errori['immagine'] = "<p>Formato non consentito. Usa JPG, PNG o WEBP.</p>";
                $formValido = false;
            } else {
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = uniqid('animal_', true) . '.' . $fileExtension;
                $imagePath = '../images/' . $newFileName;
            }
        }

    }
    
    // ==================== SALVATAGGIO ====================
    if ($formValido) {
        // Sposta immagine se caricata
        if ($imageLoaded && !empty($newFileName)) {
            $destPath = $uploadDir . $newFileName;
            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                $errori['immagine'] = "<p>Errore nel caricamento.</p>";
                $formValido = false;
            } else {
                // Elimina vecchia immagine
                if (isset($_GET["id"]) && $animaleCorrente && 
                    $animaleCorrente['immagine'] !== '../images/jpg/cane_bello.jpg') {
                    $oldImagePath = __DIR__ . '/' . $animaleCorrente['immagine'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }
        }
        
        if ($formValido) {
            $db = new DBAccess();
            $db->openDBConnection();
            
            if (!isset($_GET["id"])) {
                // INSERIMENTO
                $id = $db->inserisciAnimale($nome, $alt, $imagePath, $sesso, $tipologia, 
                                           $luogo, $eta, $taglia, $carattere, 
                                           $descrizione, $bisogni, $storia);
                $db->closeConnection();
                
                if ($id) {
                    header("Location: animale.php?id=" . $id);
                    exit();
                } else {
                    if ($imageLoaded && isset($destPath) && file_exists($destPath)) {
                        unlink($destPath);
                    }
                    header('Location: 500.php');
                    exit();
                }
            } else {
                // MODIFICA
                $id = intval($_GET['id']);
                $res = $db->aggiornaAnimale($id, $nome, $alt, $imagePath, $sesso, $tipologia, 
                                           $luogo, $eta, $taglia, $carattere, 
                                           $descrizione, $bisogni, $storia);
                $db->closeConnection();
                
                if ($res) {
                    header('Location: animale.php?id=' . $id);
                    exit();
                } else {
                    if ($imageLoaded && isset($destPath) && file_exists($destPath)) {
                        unlink($destPath);
                    }
                    header('Location: 500.php');
                    exit();
                }
            }
        }
    }
    
    // ==================== GESTIONE ERRORI ====================
    if (!$formValido) {
        $isModifica = isset($_GET["id"]);
        $azione = $isModifica ? 'Modifica' : 'Aggiungi';
        $actionForm = $isModifica ? 'gestisceAnimale.php?id=' . intval($_GET["id"]) : 'gestisceAnimale.php';
        
        // Recupera immagine se non già fatto
        if ($isModifica && !$animaleCorrente) {
            $id = intval($_GET['id']);
            $db = new DBAccess();
            $db->openDBConnection();
            $animaleCorrente = $db->getAnimalById($id);
            $db->closeConnection();
            if ($animaleCorrente) {
                $imagePath = $animaleCorrente['immagine'];
            }
        }
        
        // Sostituzioni base
        $content = str_replace("[pulsanteAzione]", $azione, $content);
        $content = str_replace("[Azione]", $azione, $content);
        $content = str_replace("[actionForm]", $actionForm, $content);
        
        // Mostra immagine corrente se in modifica
        if ($isModifica) {
            $content = str_replace("[Nome]", ' ' . $animaleCorrente["nome"], $content);
            $immagineCorrente = '<img src="' . htmlspecialchars($imagePath) . '">';
            $content = str_replace("[immagineCorrente]", $immagineCorrente, $content);
            
        } else {
            $content = str_replace("[Nome]", ' animale', $content);
            $content = str_replace("[immagineCorrente]", '', $content);
        }
        
        // Valori campi
        $content = str_replace("[Ispezione]", $azione, $content);

        $content = str_replace("[nomeAnimale]", htmlspecialchars($nome), $content);
        $content = str_replace("[etaAnimale]", htmlspecialchars($eta), $content);
        $content = str_replace("[luogoAnimale]", htmlspecialchars($luogo), $content);
        $content = str_replace("[altImmagine]", htmlspecialchars($alt), $content);
        $content = str_replace("[descrizione]", htmlspecialchars($descrizione), $content);
        $content = str_replace("[bisogni]", htmlspecialchars($bisogni), $content);
        $content = str_replace("[storia]", htmlspecialchars($storia), $content);
        
        $valoriSelezionati = [
            'sesso' => $sesso,
            'tipologia' => $tipologia,
            'taglia' => $taglia,
            'carattere' => $carattere
        ];

        $radioButtons = [
            'sesso' => ['Maschio' => 'checkMaschio', 'Femmina' => 'checkFemmina'],
            'tipologia' => ['Reale' => 'checkReale', 'Fantasy' => 'checkFantasy'],
            'taglia' => ['Piccola' => 'checkTagliaP', 'Media' => 'checkTagliaM', 'Grande' => 'checkTagliaG'],
            'carattere' => ['Facile' => 'checkCarF', 'Moderato' => 'checkCarM', 'Difficile' => 'checkCarD']
        ];

        foreach ($radioButtons as $campo => $opzioni) { // $campo = sesso, $opzioni =  ['Maschio' => 'checkMaschio', 'Femmina' => 'checkFemmina']
            $valoreSelezionato = $valoriSelezionati[$campo]; // ciò che l'utente ha mandato
            
            foreach ($opzioni as $valore => $placeholder) { //$valore = Maschio, $placeholder = 'checkMaschio'
                $isChecked = ($valoreSelezionato === $valore) ? 'checked' : ''; //confronta valoreSelezionato dall'utente con Maschio e Femmina, se corrispondo allora 'checked'
                $content = str_replace("[$placeholder]", $isChecked, $content); //rimpiazza il placeholder
            }
        }
        
        // Errori
        $content = str_replace("[nomeErr]", $errori['nome'], $content);
        $content = str_replace("[etaErr]", $errori['eta'], $content);
        $content = str_replace("[imageErr]", $errori['immagine'] ?? '', $content);
        $content = str_replace("[altErr]", $errori['alt'], $content);
        $content = str_replace("[fileErr]", $imageLoaded ? 'Ricarica immagine' : '', $content);
        $content = str_replace("[luogoErr]", $errori['luogo'], $content);
        $content = str_replace("[descrizioneErr]", $errori['descrizione'], $content);
        $content = str_replace("[bisogniErr]", $errori['bisogni'], $content);
        $content = str_replace("[storiaErr]", $errori['storia'], $content);
    }
}

// Menu e footer
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);
echo $content;
?>