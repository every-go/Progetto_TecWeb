<?php
include "menu.php";

//import delle funzioni e classi necessarie
require_once "connectionDB.php";
require_once "utils/imageHandler.php";
require_once "utils/validator.php";
require_once "utils/animalFormHandler.php";

//funzione per pulire l'input dell'utente
function pulisciInput($data)
{
    return htmlspecialchars(trim($data));
}

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: 401.php");
    exit();
}



use DB\DBAccess;

//configurazione dei percorsi di upload delle immagini
$uploadDirSystem = "images/uploads";
$uploadDirWeb = "images/uploads/";

$imgHandler = new ImageHandler($uploadDirSystem, $uploadDirWeb);



// Inizializzazione delle variabili per il form
$campiFormSemplici = array_merge(
    array_keys(campiDatiAnimale::$testi),
    array_keys(campiDatiAnimale::$radio)
);

$valori = array_fill_keys($campiFormSemplici, "");
$errori = [];
$dbImagePath = "";
$newImagePath = null;

//contorollo se il form è stato inviato
if (isset($_POST['Aggiungi'])) {
    //Pulizia e recupero dei dati inseriti nel form
    foreach ($campiFormSemplici as $campo) {
        $valori[$campo] = pulisciInput($_POST[$campo] ?? "");
    }

    //validazione dei dati del form
    $errori = checkForm($valori);

    // Gestione dell'immagine
    $imgError = null;
    $hasNewImage = false;
    // Controlla se è stata caricata una nuova immagine
    if (
        isset($_FILES["immagine"]) &&
        $_FILES["immagine"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {
        $imgError = $imgHandler->validate($_FILES["immagine"] ?? null);
        if ($imgError === null) {
            $hasNewImage = true;
        }
    }

    // Se non ci sono errori, procedi con l'inserimento nel database
    if (empty($errori) && $imgError === null) {
        // INSERIMENTO nuovo animale
        $db = new DBAccess();

        if ($db->openDBConnection()) {
            if ($hasNewImage) {
                $newImagePath = $imgHandler->save($_FILES["immagine"]);
                if ($newImagePath !== false) {
                    $valori["immagine"] = $newImagePath;
                } else {
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

            $db->closeConnection();


            if ($nuovoId !== false) {
                $_GET["id"] = $nuovoId;
                header("Location: animale.php?" . createHttpQuery($_GET, ['pagina', 'search', 'id'], false));
                exit();
            } else {
                $errori["erroreGenerale"] = "Errore durante l'inserimento dell'animale.";
            }
        }
    } else {
        if ($imgError !== null) {
            $errori[campiDatiAnimale::$files["immagine"]["errPlaceholder"]] = $imgError;
        }
    }
}

//creazione del form html per l'aggiunta di un animale
$content = createForm('Aggiungi');


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

// breadcrumb aggiungi animale
$content = str_replace("[ANIMALE_LINK_BREADCRUMB]", '', $content);
$content = str_replace("[BREADCRUMB_SEPARATOR]", '', $content);





$content = cleanForm($content);

$content = renderErrorsWithAria($content, $errori);

$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

echo $content;