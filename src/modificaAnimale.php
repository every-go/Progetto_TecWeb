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


$id =
    isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : null;
if($id === null){
    include __DIR__ . "/404.php";
    exit();
}

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
if (!isset($_POST['Modifica'])) {

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
else{
    
    foreach ($campiFormSemplici as $campo) {
        $valori[$campo] = pulisciInput($_POST[$campo] ?? "");
    }

    // CORREZIONE: Passa true come secondo parametro per indicare che è una modifica
    $errori = checkForm($valori, true);
    
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

    if (empty($errori) && $imgError === null) {
            
            $db = new DBAccess();

            if ($db->openDBConnection()) {
                
                $datiDB = $db->getAnimalById($id);
                
                if (!$datiDB) {
                    $db->closeConnection();
                    include __DIR__ . "/500.php";
                    exit();
                }
                
                $oldImagepath = $datiDB["immagine"];
                
                // Gestione immagine PRIMA dell'update
                if ($hasNewImage) {
                    $newImagePath = $imgHandler->save($_FILES["immagine"]);
                    if ($newImagePath !== false) {
                        $valori["immagine"] = $newImagePath;
                        $imgHandler->delete($oldImagepath);
                    } else {
                        $errori[campiDatiAnimale::$files["immagine"]["errPlaceholder"]] = 
                            "Errore nel salvataggio dell'immagine.";
                        $valori["immagine"] = $oldImagepath;
                    }
                } else {
                    $valori["immagine"] = $oldImagepath;
                }
                
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
                
                $db->closeConnection();

                if ($esitoOperazione) {
                    header("Location: animale.php?" . createHttpQuery($_GET, ['pagina', 'search', 'id'], false));
                    exit();
                } else {
                    $errori["erroreGenerale"] = "Errore durante l'aggiornamento dell'animale.";
                }
            } else {
                include __DIR__ . "/500.php";
                exit();
            }
    } else {
        if ($imgError !== null) {
            $errori[campiDatiAnimale::$files["immagine"]["errPlaceholder"]] = $imgError;
        }
    }

}

//creazione del form html per l'aggiunta di un animale
$content = createForm('Modifica',$id);


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

$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

echo $content;