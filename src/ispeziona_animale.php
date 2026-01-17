<?php
file_put_contents(__DIR__ . '/test_scrittura.log', 'File PHP caricato: ' . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

require_once "connectionDB.php";
require_once "utils/imageHandler.php";
require_once "utils/validator.php";
require_once "utils/animalFormHandler.php";

function pulisciInput($data)
{
    return htmlspecialchars(trim($data));
}

use DB\DBAccess;

include "menu.php";
include "avvisi.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: 401.php");
    exit();
}

$uploadDirSystem = "images/uploads";
$uploadDirWeb = "images/uploads/";

$imgHandler = new ImageHandler($uploadDirSystem, $uploadDirWeb);


$id =
    isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : null;
$action = $id !== null ? "Modifica" : "Aggiungi";
$isModifica = $action === "Modifica";

$campiFormSemplici = array_merge(
    array_keys(campiDatiAnimale::$testi),
    array_keys(campiDatiAnimale::$radio)
);

$valori = array_fill_keys($campiFormSemplici, "");
$errori = [];
$dbImagePath = "";
$oldImagepath = null;

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    if ($isModifica) {
        $db = new DBAccess();

        if ($db->openDBConnection()) {
            $datiDB = $db->getAnimalById($id);
            $db->closeConnection();

            if ($datiDB) {
                $valori = $datiDB;
                $dbImagePath = $datiDB["immagine"] . ' aria-label="' . $datiDB["alt"] . '"';
            } else {
                include __DIR__ . "/404.php";
                exit();
            }
        } else {
            include __DIR__ . "/500.php";
            exit();
        }
    }
} else {
    // Log file
    $logFile = __DIR__ . "/log/debug.log";
    file_put_contents($logFile, "=== INIZIO DEBUG " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
    
    foreach ($campiFormSemplici as $campo) {
        $valori[$campo] = pulisciInput($_POST[$campo] ?? "");
    }

    file_put_contents($logFile, "Valori POST ricevuti: " . print_r($valori, true) . "\n", FILE_APPEND);

    $errori = checkForm($valori);
    file_put_contents($logFile, "Errori validazione: " . print_r($errori, true) . "\n", FILE_APPEND);
    
    $imgError = null;
    $hasNewImage = false;
    
    if (
        isset($_FILES["immagine"]) &&
        $_FILES["immagine"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {
        $imgError = $imgHandler->validate($_FILES["immagine"] ?? null);
        if ($imgError === null) {
            $hasNewImage = true;
        }
    }
    
    file_put_contents($logFile, "hasNewImage: " . ($hasNewImage ? 'SI' : 'NO') . "\n", FILE_APPEND);
    file_put_contents($logFile, "imgError: " . ($imgError ?? 'NULL') . "\n", FILE_APPEND);

    if (empty($errori) && $imgError === null) {
        file_put_contents($logFile, "Validazione OK, procedo con salvataggio\n", FILE_APPEND);
        
        if ($isModifica) {
            file_put_contents($logFile, "Modalità: MODIFICA (ID: $id)\n", FILE_APPEND);
            
            $db = new DBAccess();

            if ($db->openDBConnection()) {
                file_put_contents($logFile, "DB connesso\n", FILE_APPEND);
                
                $datiDB = $db->getAnimalById($id);
                
                if (!$datiDB) {
                    file_put_contents($logFile, "ERRORE: Animale non trovato\n", FILE_APPEND);
                    $db->closeConnection();
                    include __DIR__ . "/500.php";
                    exit();
                }
                
                file_put_contents($logFile, "Dati animale recuperati dal DB\n", FILE_APPEND);
                $oldImagepath = $datiDB["immagine"];
                file_put_contents($logFile, "Vecchio path immagine: $oldImagepath\n", FILE_APPEND);
                
                // Gestione immagine PRIMA dell'update
                if ($hasNewImage) {
                    file_put_contents($logFile, "Salvataggio nuova immagine...\n", FILE_APPEND);
                    $newImagePath = $imgHandler->save($_FILES["immagine"]);
                    if ($newImagePath !== false) {
                        $valori["immagine"] = $newImagePath;
                        $imgHandler->delete($oldImagepath);
                        file_put_contents($logFile, "Immagine salvata: $newImagePath\n", FILE_APPEND);
                    } else {
                        file_put_contents($logFile, "ERRORE: Salvataggio immagine fallito\n", FILE_APPEND);
                        $errori[campiDatiAnimale::$files["immagine"]["errPlaceholder"]] = 
                            "Errore nel salvataggio dell'immagine.";
                        $valori["immagine"] = $oldImagepath;
                    }
                } else {
                    file_put_contents($logFile, "Nessuna nuova immagine, mantengo: $oldImagepath\n", FILE_APPEND);
                    $valori["immagine"] = $oldImagepath;
                }
                
                file_put_contents($logFile, "Chiamata aggiornaAnimale con path: " . $valori["immagine"] . "\n", FILE_APPEND);
                
                $esitoOperazione = $db->aggiornaAnimale(
                    $id,
                    $valori["nome"],
                    $valori["alt"],
                    $valori["immagine"],
                    $valori["sesso"],
                    $valori["tipo_animale"],
                    $valori["luogo"],
                    $valori["eta"],
                    $valori["taglia"],
                    $valori["carattere"],
                    $valori["descrizione"],
                    $valori["bisogni"],
                    $valori["storia"]
                );
                
                file_put_contents($logFile, "Esito aggiornaAnimale: " . ($esitoOperazione ? 'SUCCESS' : 'FAIL') . "\n", FILE_APPEND);
                
                $db->closeConnection();

                if ($esitoOperazione) {
                    file_put_contents($logFile, "Redirect a animale.php\n", FILE_APPEND);
                    header("Location: animale.php?" . createHttpQuery($_GET, ['pagina', 'search', 'id'], false));
                    exit();
                } else {
                    file_put_contents($logFile, "ERRORE: Update fallito\n", FILE_APPEND);
                    $errori["erroreGenerale"] = "Errore durante l'aggiornamento dell'animale.";
                }
            } else {
                file_put_contents($logFile, "ERRORE: Connessione DB fallita\n", FILE_APPEND);
                include __DIR__ . "/500.php";
                exit();
            }
        } else {
            // INSERIMENTO nuovo animale
            file_put_contents($logFile, "Modalità: INSERIMENTO\n", FILE_APPEND);
            
            $db = new DBAccess();

            if ($db->openDBConnection()) {
                if ($hasNewImage) {
                    $newImagePath = $imgHandler->save($_FILES["immagine"]);
                    if ($newImagePath !== false) {
                        $valori["immagine"] = $newImagePath;
                        file_put_contents($logFile, "Immagine salvata: $newImagePath\n", FILE_APPEND);
                    } else {
                        file_put_contents($logFile, "ERRORE: Salvataggio immagine fallito\n", FILE_APPEND);
                        $errori[campiDatiAnimale::$files["immagine"]["errPlaceholder"]] = 
                            "Errore nel salvataggio dell'immagine.";
                    }
                } else {
                    $valori["immagine"] = null;
                }
                
                $nuovoId = $db->inserisciAnimale(
                    $valori["nome"],
                    $valori["alt"],
                    $valori["immagine"],
                    $valori["sesso"],
                    $valori["tipo_animale"],
                    $valori["luogo"],
                    $valori["eta"],
                    $valori["taglia"],
                    $valori["carattere"],
                    $valori["descrizione"],
                    $valori["bisogni"],
                    $valori["storia"]
                );
                
                file_put_contents($logFile, "Nuovo ID: " . ($nuovoId ? $nuovoId : 'NULL') . "\n", FILE_APPEND);
                
                $db->closeConnection();

                if ($nuovoId) {
                    $_GET["id"] = $nuovoId;
                    header("Location: animale.php?" . createHttpQuery($_GET, ['pagina', 'search', 'id'], false));
                    exit();
                } else {
                    $errori["erroreGenerale"] = "Errore durante l'inserimento dell'animale.";
                }
            } else {
                include __DIR__ . "/500.php";
                exit();
            }
        }
    } else {
        file_put_contents($logFile, "Validazione FALLITA - non procedo con salvataggio\n", FILE_APPEND);
        if ($imgError !== null) {
            $errori[campiDatiAnimale::$files["immagine"]["errPlaceholder"]] = $imgError;
        }
    }
    
    file_put_contents($logFile, "=== FINE DEBUG ===\n\n", FILE_APPEND);
}

$content = createForm($action, $id);

$content = populateForm($valori, $content);

$content = replaceErrorPlaceholders($content, $errori);

$content = addAriaInvalid($content, $errori);

// Gestione http query per sticky parameters nella per la navigazione
$parametriQuery = [];

//imposta breadcrumb della pagina di modifica, con Sticky Parameters
if (isset($_GET["pagina"])) $parametriQuery["pagina"] = $_GET["pagina"];
if (isset($_GET["search"])) $parametriQuery["search"] = $_GET["search"];


//breadcrumb lista animali
$content = str_replace("[ANIMALI_LINK_BREADCRUMB]", '<a href="animali.php?' .
    htmlspecialchars(http_build_query($parametriQuery)) .
    '">Animali</a>', $content);
if ($isModifica) {
    //breadcrumb modifica

    // breadcrumb dettaglio animale
    $parametriQuery["id"] = $id;
    $content = str_replace("[ANIMALE_LINK_BREADCRUMB]", '<a href="animale.php?' .
        htmlspecialchars(http_build_query($parametriQuery)) . '">' . $valori["nome"] . '</a>', $content);

    $content = str_replace("[BREADCRUMB_SEPARATOR]", '<span aria-hidden="true">&gt;&gt;</span> ', $content);
} else {
    // breadcrumb aggiungi animale
    $content = str_replace("[ANIMALE_LINK_BREADCRUMB]", '', $content);
    $content = str_replace("[BREADCRUMB_SEPARATOR]", '', $content);
}




$content = cleanForm($content);

$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

echo $content;
