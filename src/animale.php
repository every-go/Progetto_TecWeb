<?php
include "menu.php";
require_once "." . DIRECTORY_SEPARATOR . "connectionDB.php";
use DB\DBAccess;
$content = file_get_contents("html/animale.html");
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

$breadcrumb = "";

if (
    isset($_SESSION["provenienza"]) &&
    $_SESSION["provenienza"] === "Area Personale"
) {
    if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])) {
        $breadcrumb .=
            ' <a href="area_personale.php?pagina=' .
            $_GET["pagina"] .
            '"> Area Personale: pagina-' .
            $_GET["pagina"] .
            " </a>";
    } else {
        $breadcrumb .= ' <a href="area_personale.php"> Area Personale </a>';
    }
} else {
    $paramQueryProvenienza = [];
    if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])) {
        $paramQueryProvenienza[] = "pagina=" . $_GET["pagina"];
    }

    if (isset($_GET["search"]) && !empty(trim($_GET["search"]))) {
        $paramQueryProvenienza[] = "search=" . $_GET["search"];
    }

    $breadcrumb .= ' <a href="animali.php';

    if (!empty($paramQueryProvenienza)) {
        $breadcrumb .= "?" . implode("&", $paramQueryProvenienza);
    }

    $breadcrumb .= '"> Animali </a>';
}
$content = str_replace("[breadcrumb]", $breadcrumb, $content);
$pulsanti = "";
$pulsantePreferiti = "";
$sezioneAdottami = "";
$animalData = "";
$adottami = "";
$aiutoadozione = "";
$warningAdozione = "";

if (isset($_GET["id"])) {
    $db = new DBAccess();
    $connessioneOK = $db->openDBConnection();

    if ($connessioneOK) {
        $animalData = $db->getAnimalById($_GET["id"]);
        $isFavorite = false;
        if (isset($_SESSION["username"])) {
            $isFavorite = $db->checkPreferito(
                $_SESSION["username"],
                intval($_GET["id"])
            );
        }

        $db->closeConnection();
        if ($animalData) {
            $content = str_replace("[NOME]", $animalData["nome"], $content);
            $content = str_replace(
                "[ETA]",
                $animalData["eta"] .
                    ($animalData["eta"] == 1 ? " anno" : " anni"),
                $content
            );
            $content = str_replace("[LUOGO]", $animalData["luogo"], $content);
            $content = str_replace(
                "[CARATTERE]",
                $animalData["carattere"],
                $content
            );
            if ($animalData["immagine"]) {
                $content = str_replace(
                    "[PATH_IMMAGINE]",
                    $animalData["immagine"],
                    $content
                );
            } else {
                $content = str_replace("[PATH_IMMAGINE]", "", $content);
            }
            $content = str_replace(
                "[ALT_IMMAGINE]",
                $animalData["alt"],
                $content
            );
            $content = str_replace(
                "[DESCRIZIONE]",
                $animalData["descrizione"],
                $content
            );

            $bisogni = $animalData["bisogni"];
            // Inserisce <p> davanti a ogni nuovo punto numerato
            $bisogni = preg_replace(
                "/(^|\s)(\d+\))\s/",
                '<p>$2 ',
                trim($bisogni)
            );
            
            $arrayBisogni = array_filter(explode(";", $bisogni),function($value){
                return trim($value)!=="";
            });
            $bisogni="<ul><li>".implode("</li><li>",$arrayBisogni)."</li></ul>";

            $content = str_replace("[BISOGNI]", $bisogni, $content);

            $content = str_replace("[STORIA]", $animalData["storia"], $content);

            if ($isFavorite) {
                $pulsantePreferiti .=
                    '<button id="bottone-preferiti" aria-label="Rimuovi dai preferiti" data-id="' .
                    intval($_GET["id"]) .
                    '">
                        <img src="./images/png/heart_filled.png" class="print-only" alt="Icona preferiti" id="icona-preferiti">
                    </button>';
            } elseif (
                $animalData["adottato"] === 0 &&
                (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin")
            ) {
                $pulsantePreferiti .=
                '<button id="bottone-preferiti" aria-label="Aggiungi ai preferiti" data-id="' .
                intval($_GET["id"]) .
                '">
                    <img src="./images/png/heart.png" alt="Icona preferiti" id="icona-preferiti">
                </button>';

            }
        } else {
            include __DIR__ . "/404.php";
            http_response_code(404);
            exit();
        }
    } else {
        include __DIR__ . "/500.php";
        http_response_code(500);
        exit();
    }

    if (
        isset($_SESSION["is_logged_in"]) &&
        $_SESSION["is_logged_in"] === true &&
        isset($_SESSION["role"]) &&
        $_SESSION["role"] === "admin"
    ) {
        $pulsanti .=
            '<div class="admin-buttons">
                    <a role="button" href="ispeziona_animale.php?id=' .
            $_GET["id"] .
            '" class="btn-modifica">Modifica</a>
                    <a role="button" href="elimina.php?id=' .
            $_GET["id"] .
            '" class="btn-elimina"> Elimina </a>
                    </div>';
    } elseif ($animalData["adottato"] == 0) {
        $aiutoadozione =
            '<a class="aiuti" href="#adotta"> Vai alla adozione </a>';
        $adottami = '<aside id="adotta">
            <h2>Ti piace? Adotta!</h2>
            <p>Ogni essere, reale o straordinario, cerca un luogo in cui sentirsi al sicuro.
                Che la sua natura sia comune o rara, ciò che può offrire è semplice: compagnia, presenza e un legame
                sincero. Se qualcosa ha acceso la tua curiosità, forse è il primo passo verso un incontro
                speciale. Scopri un po’ di più… potrebbe essere il compagno che stavi cercando.</p>
            <form action="adotta.php?id=' . intval($_GET['id']) . '" method="post"> 
                <button type="submit">Adotta!</button>
            </form>
            </aside>';
    } else {
        $warningAdozione =
            '<a class="aiuti" href="#adotta"> ' .
            $animalData["nome"] .
            " è già stato adottato </a>";
        $adottami = '<aside id="adotta">
            <h2>Già adottato!</h2>
            <p>Questo animale ha già trovato una casa amorevole. Grazie per il tuo interesse nell\'adottare e
            per il tuo sostegno alla nostra missione di trovare famiglie per tutti i nostri amici a quattro zampe.</p>
            </aside>';
    }
} else {
    include __DIR__ . "/404.php";
    http_response_code(404);
    exit();
}

$messaggioAdozione = '';
if (isset($_SESSION['messaggio_errore'])) {
    $messaggioAdozione = "<p class='error-adozione' aria-live='assertive' role='alert'>" . $_SESSION['messaggio_errore'] . " </p>";
    unset($_SESSION['messaggio_errore']);
} elseif (isset($_SESSION['messaggio_adozione'])) {
    $messaggioAdozione = "<p class='confirm-adozione' aria-live='assertive' role='alert'>" . $_SESSION['messaggio_adozione'] . " </p>";
    unset($_SESSION['messaggio_adozione']);
}
$content = str_replace('[adottaMessage]', $messaggioAdozione, $content);


$content = str_replace("[pulsanti]", $pulsanti, $content);
$content = str_replace("[PULSANTE_PREFERITI]", $pulsantePreferiti, $content);
$content = str_replace("[aiutoAdozione]", $aiutoadozione, $content);
$content = str_replace("[WARNING_ADOZIONE]", $warningAdozione, $content);
$content = str_replace("[adottami]", $adottami, $content);

echo $content;
?>
