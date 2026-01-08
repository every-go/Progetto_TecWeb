<?php
require_once 'connectionDB.php';
require_once 'utils/imageHandler.php';
require_once 'utils/validator.php';
require_once 'utils/animalFormHandler.php';


function pulisciInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

use DB\DBAccess;

include "menu.php";


// 1. CONTROLLO ACCESSO
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== 'admin') {
    header('Location: index.php');
    exit();
}


// 2. CONFIGURAZIONE E SETUP INIZIALE
$uploadDirSystem = 'images/uploads'; // Dove salvare fisicamente
$uploadDirWeb = 'images/uploads/'; // Cosa scrivere nel DB

// Inizializzo l'handler immagini
$imgHandler = new ImageHandler($uploadDirSystem, $uploadDirWeb);

// Determino azione e ID
$id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : null;
$action = ($id !== null) ? 'Modifica' : 'Aggiungi';
$isModifica = ($action === 'Modifica');

// Preparo le chiavi per l'inizializzazione
$campiFormSemplici = array_merge(
    array_keys(campiDatiAnimale::$testi), 
    array_keys(campiDatiAnimale::$radio)
);

// Inizializzo variabili base
$valori = array_fill_keys($campiFormSemplici, '');
$errori = []; 
$dbImagePath = '../images/jpg/cane_bello.jpg'; // Default
$oldImagepath=null;
// ============================================================
// 3. LOGICA PRINCIPALE (IL "CERVELLO")
// ============================================================

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // ==================== METODO GET (PRIMA APERTURA) ====================
    
    if ($isModifica) {
        $db = new DBAccess();
        
        if ($db->openDBConnection()) {
            $datiDB = $db->getAnimalById($id);
            $db->closeConnection();
            
            if ($datiDB) {
                // Ho trovato l'animale: popolo i valori
                $valori = $datiDB;
                $dbImagePath = $datiDB['immagine'];
            } else {
                // ID non trovato -> 404
                include __DIR__ . "/404.php";
                exit();
            }
        } else {
            // Errore connessione DB
            include __DIR__ . "/500.php";
            exit();
        }
    }
    // Se è "Aggiungi", $valori resta vuoto come inizializzato sopra.

} else {
    foreach ($campiFormSemplici as $campo) {
        $valori[$campo] = pulisciInput($_POST[$campo] ?? '');
    }

    // Validazione campi
    $errori=checkForm($valori);
    $imgError = null;
    $hasNewImage = false;
    if(isset($_FILES['immagine'] )&& $_FILES['immagine']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imgError = $imgHandler->validate($_FILES['immagine'] ?? null);
            if($imgError===null)$hasNewImage=true;
    }

    
    if(empty($errori)&& $imgError===null){

        if($isModifica){
            // inizio la modifica e salvo l'immagine se c'è cancellando la vecchia, altrimenti mantengo quella esistente
            $db = new DBAccess();

            if ($db->openDBConnection()) {

                $oldImagepath = $db->getAnimalById($id)['immagine'];
                if($hasNewImage){
                    $newImagePath = $imgHandler->save($_FILES['immagine']);
                    if ($newImagePath !== false) {
                        $valori['immagine'] = $newImagePath;
                        // Elimino la vecchia immagine
                        $imgHandler->delete($oldImagepath);
                    } else {
                        $errori[campiDatiAnimale::$files['immagine']['errPlaceholder']] = "Errore nel salvataggio dell'immagine.";
                        $valori['immagine'] =$oldImagepath; // Mantieni l'immagine esistente
                    }
                } else {
                    $valori['immagine'] = $oldImagepath; // Mantieni l'immagine esistente
                }
                $esitoOperazione=$db->aggiornaAnimale(
                    $id,
                    $valori['nome'],
                    $valori['alt'],
                    $valori['immagine'],
                    $valori['sesso'],
                    $valori['tipo_animale'],
                    $valori['luogo'],
                    $valori['eta'],
                    $valori['taglia'],
                    $valori['carattere'],
                    $valori['descrizione'],
                    $valori['bisogni'],
                    $valori['storia']
                );
                $db->closeConnection();

                if($esitoOperazione){
                    // Redirect alla pagina di dettaglio
                    header('Location: animale.php?id=' . urlencode($id));
                    exit();
                }
                else{
                    $errori['erroreGenerale']="Errore durante l'aggiornamento dell'animale.";
                }
                $db->closeConnection();
            }
            else{
            // Errore connessione DB
            include __DIR__ . "/500.php";
            exit();
        }
        }
        else{
            // inizio l'inserimento e salvo l'immagine se c'è
            $db = new DBAccess();

            if ($db->openDBConnection()) {

                if($hasNewImage){
                    $newImagePath = $imgHandler->save($_FILES['immagine']);
                    if ($newImagePath !== false) {
                        $valori['immagine'] = $newImagePath;
                    } else {
                        $errori[campiDatiAnimale::$files['immagine']['errPlaceholder']] = "Errore nel salvataggio dell'immagine.";
                    }
                } else {
                    $valori['immagine'] = null; // Nessuna immagine caricata
                }
                $nuovoId=$db->inserisciAnimale(
                    $valori['nome'],
                    $valori['alt'],
                    $valori['immagine'],
                    $valori['sesso'],
                    $valori['tipo_animale'],
                    $valori['luogo'],
                    $valori['eta'],
                    $valori['taglia'],
                    $valori['carattere'],
                    $valori['descrizione'],
                    $valori['bisogni'],
                    $valori['storia']
                );
                $db->closeConnection();

                if($nuovoId){
                    // Redirect alla pagina di dettaglio
                    header('Location: animale.php?id=' . urlencode($nuovoId));
                    exit();
                }
                else{
                    $errori['erroreGenerale']="Errore durante l'inserimento dell'animale.";
                }
            }
            else{
            // Errore connessione DB
            include __DIR__ . "/500.php";
            exit();
            }
        }

    }
    else{
        // Ci sono errori: rimango nel form e li mostro
        if($imgError!==null){
            $errori[campiDatiAnimale::$files['immagine']['errPlaceholder']]=$imgError;
        }
        
    }

}

// ============================================================
// 4. RENDERING UNICO (VIEW)
// ============================================================

// Se siamo qui è perché:
// 1. È una GET (prima apertura)
// 2. È una POST fallita (ci sono $errori)

// 1. Creo HTML base
$content = createForm($action, $id); 

// 2. Popolo i campi con $valori
$content = populateForm($valori, $content); 

// 3. Gestisco l'immagine corrente nel template
// Se c'è un'immagine (dal DB o default), crea il tag IMG, altrimenti stringa vuota
$htmlImg = ($dbImagePath) ? '<img src="'.$dbImagePath.'" alt="Immagine attuale" class="img-preview">' : '';
$content = str_replace('[immagineCorrente]', $htmlImg, $content);

// 4. Stampo gli errori (Se l'array $errori è vuoto, replaceErrorPlaceholders pulirà tutto)
$content = replaceErrorPlaceholders($content, $errori);

// aggiunge aria-invalid ai camppi che hanno errori
$content = addAriaInvalid($content, $errori);

// 5. Pulizia finale (Placeholder orfani)
$content = cleanForm($content);

// 6. Header e Footer
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

echo $content;
?>