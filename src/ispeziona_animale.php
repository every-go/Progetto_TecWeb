<?php
require_once "connectionDB.php";
require_once "utils/imageHandler.php";
require_once "utils/validator.php";
require_once "utils/animalFormHandler.php";

function pulisciInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

use DB\DBAccess;

include "menu.php";

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
    foreach ($campiFormSemplici as $campo) {
        $valori[$campo] = pulisciInput($_POST[$campo] ?? "");
    }

    $errori = checkForm($valori);
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
        if ($isModifica) {
            $db = new DBAccess();

            if ($db->openDBConnection()) {
                $oldImagepath = $db->getAnimalById($id)["immagine"];
                if ($hasNewImage) {
                    $newImagePath = $imgHandler->save($_FILES["immagine"]);
                    if ($newImagePath !== false) {
                        $valori["immagine"] = $newImagePath;
                        
                        $imgHandler->delete($oldImagepath);
                    } else {
                        $errori[
                            campiDatiAnimale::$files["immagine"][
                                "errPlaceholder"
                            ]
                        ] = "Errore nel salvataggio dell'immagine.";
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
                    header("Location: animale.php?id=" . urlencode($id));
                    exit();
                } else {
                    $errori["erroreGenerale"] =
                        "Errore durante l'aggiornamento dell'animale.";
                }
                $db->closeConnection();
            } else {
                include __DIR__ . "/500.php";
                exit();
            }
        } else {
            $db = new DBAccess();

            if ($db->openDBConnection()) {
                if ($hasNewImage) {
                    $newImagePath = $imgHandler->save($_FILES["immagine"]);
                    if ($newImagePath !== false) {
                        $valori["immagine"] = $newImagePath;
                    } else {
                        $errori[
                            campiDatiAnimale::$files["immagine"][
                                "errPlaceholder"
                            ]
                        ] = "Errore nel salvataggio dell'immagine.";
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

                if ($nuovoId) {
                    header("Location: animale.php?id=" . urlencode($nuovoId));
                    exit();
                } else {
                    $errori["erroreGenerale"] =
                        "Errore durante l'inserimento dell'animale.";
                }
            } else {
                include __DIR__ . "/500.php";
                exit();
            }
        }
    } else {
        if ($imgError !== null) {
            $errori[
                campiDatiAnimale::$files["immagine"]["errPlaceholder"]
            ] = $imgError;
        }
    }
}

$content = createForm($action, $id);

$content = populateForm($valori, $content);

$htmlImg = $dbImagePath
    ? '<img src="' .
        $dbImagePath .
        '" alt="" class="img-preview">'
    : "";
$content = str_replace("[immagineCorrente]", $htmlImg, $content);

$content = replaceErrorPlaceholders($content, $errori);

$content = addAriaInvalid($content, $errori);

$content = cleanForm($content);

$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

echo $content;
?>
