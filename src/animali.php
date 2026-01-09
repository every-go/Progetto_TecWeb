<?php
require_once "." . DIRECTORY_SEPARATOR . "connectionDB.php";
require_once "." .
    DIRECTORY_SEPARATOR .
    "utils" .
    DIRECTORY_SEPARATOR .
    "pagination.php";
use DB\DBAccess;

function singleCardBuilder($animale)
{
    $animaleCard = file_get_contents("html/animalCardTemplate.html");
    $animaleCard = str_replace("[ID_ANIMALE]", $animale["id"], $animaleCard);
    $animaleCard = str_replace(
        "[NOME_ANIMALE]",
        $animale["nome"],
        $animaleCard
    );
    $animaleCard = str_replace("[ALT_IMMAGINE]", $animale["alt"], $animaleCard);
    $animaleCard = str_replace(
        "[PATH_IMMAGINE]",
        $animale["immagine"],
        $animaleCard
    );
    $animaleCard = str_replace(
        "[TIPO]",
        $animale["tipo_animale"],
        $animaleCard
    );
    $animaleCard = str_replace("[LUOGO]", $animale["luogo"], $animaleCard);

    if ($animale["eta"] == 1) {
        $animale["eta"] = "1 anno";
    } else {
        $animale["eta"] = $animale["eta"] . " anni";
    }
    $animaleCard = str_replace("[ETA_ANIMALE]", $animale["eta"], $animaleCard);
    $animaleCard = str_replace("[TAGLIA]", $animale["taglia"], $animaleCard);
    $animaleCard = str_replace(
        "[CARATTERE]",
        $animale["carattere"],
        $animaleCard
    );

    // gestione provenienza per tornare alla pagina corretta dopo la visualizzazione del singolo animale
    $paramQueryProvenienza = "";
    if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])) {
        $paramQueryProvenienza .= "&pagina=" . $_GET["pagina"];
    }

    if (isset($_GET["search"]) && !empty(trim($_GET["search"]))) {
        $paramQueryProvenienza .= "&search=" . $_GET["search"];
    }

    $animaleCard = str_replace(
        "[PAGINA_PROVENIENZA]",
        $paramQueryProvenienza,
        $animaleCard
    );

    return $animaleCard;
}

include "menu.php";
$content = file_get_contents("html/animali.html");
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

// setto la provenienza per la breadcrum
$_SESSION["provenienza"] = "Animali";

$animaliPerPagina = 1;

$paginaCorrente = isset($_GET["pagina"]) ? (int) $_GET["pagina"] : 1;
if ($paginaCorrente < 1) {
    $paginaCorrente = 1;
}

$stringaAnimali = "";
$listaAnimali = "";

// ========== GESTIONE RICERCA ==========
// Controlla se è stata effettuata una ricerca
$isSearch = isset($_GET["search"]) && !empty(trim($_GET["search"]));

// Prepara il valore di ricerca per il campo di input
$searchTerm = $isSearch ? trim($_GET["search"]) : "";

// Inserisci il valore di ricerca nel campo di input
$searchValue = $isSearch ? htmlspecialchars($searchTerm) : "";

// Sostituisci il placeholder nel contenuto
$content = str_replace("[SEARCH_VALUE]", $searchValue, $content);

$db = new DBAccess();
$connessioneOK = $db->openDBConnection();

if ($connessioneOK) {
    if ($isSearch) {
        // MODALITÀ RICERCA - recupera solo gli animali che corrispondono alla ricerca
        if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
            $stringaAnimali = $db->searchAnimali($searchTerm);
        } else {
            $stringaAnimali = $db->searchAnimaliAdottabili($searchTerm);
        }
        $db->closeConnection();
        // $totaleAnimali = count($stringaAnimali);

        // // Non usare paginazione per i risultati di ricerca
        // $animaliPaginaCorrente = $stringaAnimali;
    } else {
        // MODALITÀ NORMALE - con paginazione
        $stringaAnimali = $db->getList();
        $db->closeConnection();
        // $totaleAnimali = count($stringaAnimali);
        // $totalePagine = ceil($totaleAnimali / $animaliPerPagina);

        // if($paginaCorrente > $totalePagine && $totalePagine > 0) {
        //     $paginaCorrente = $totalePagine;
        // }

        // $offest = ($paginaCorrente - 1) * $animaliPerPagina;
        // $animaliPaginaCorrente = array_slice($stringaAnimali, $offest, $animaliPerPagina);
    }

    // Calcola il totale degli animali e delle pagine per entrambe le modalità
    $totaleAnimali = count($stringaAnimali);

    $totalePagine = ceil($totaleAnimali / $animaliPerPagina);

    if ($paginaCorrente > $totalePagine && $totalePagine > 0) {
        $paginaCorrente = $totalePagine;
    }

    $offest = ($paginaCorrente - 1) * $animaliPerPagina;
    $animaliPaginaCorrente = array_slice(
        $stringaAnimali,
        $offest,
        $animaliPerPagina
    );

    // Costruisci le card degli animali
    foreach ($animaliPaginaCorrente as $stringaAnimale) {
        $listaAnimali .= singleCardBuilder($stringaAnimale);
    }
    $listaAnimali = '<ul class="animali">' . $listaAnimali . "</ul>";

    // ========== GESTIONE MESSAGGI E PAGINAZIONE ==========

    $indicatorePagina = "";
    $baseURL = "animali.php?";
    if ($isSearch) {
        // Messaggio risultati ricerca
        $messaggioRisultati =
            'Risultati per "<strong>' .
            htmlspecialchars($searchTerm) .
            '</strong>": ';
        $messaggioRisultati .=
            $totaleAnimali .
            " " .
            ($totaleAnimali === 1 ? "animale trovato" : "animali trovati");
        if ($totaleAnimali === 0) {
            $listaAnimali =
                '<p> Nessun animale trovato. <a href="animali.php">Torna all\'elenco completo</a> </p>';
        }

        $content = str_replace(
            '<p id="animali-content"> Ecco tutti i nostri amici! </p>',
            '<p id="animali-content">' . $messaggioRisultati . "</p>",
            $content
        );
        $baseURL .= "search=" . urlencode($searchTerm) . "&";

        // // Nascondi i pulsanti di paginazione durante la ricerca
        // $content = str_replace('[indietroLink]', 'style="display:none"', $content);
        // $content = str_replace('[avantiLink]', 'style="display:none"', $content);
        // $content = str_replace('[indicatorePagina]', '', $content);
    }
    // else {
    //     // Modalità normale - gestisci la paginazione
    //     if ($paginaCorrente > 1) {
    //         $content = str_replace("[indietroLink]", 'href="animali.php?pagina=' . ($paginaCorrente - 1) .'"', $content);
    //     } else {
    //         $content = str_replace("[indietroLink]", 'class="disabled" aria-disabled="true"', $content);
    //     }

    //     if($paginaCorrente>1){
    //         $indicatorePagina.= '<li>'.'<a href="animali.php?pagina=1 aria-label="Prima pagina">1</a></li>';

    //     }

    //     if($paginaCorrente<$totalePagine){
    //         $indicatorePagina.= '<li>'.'<a href="animali.php?pagina='.$totalePagine.' aria-label="Ultima pagina">'.$totalePagine.'</a></li>';

    //     }

    // $indicatorePagina = '<span class="indicatore-pagina"> Pagina ' . $paginaCorrente . ' di ' . $totalePagine . '</span>';

    $content = str_replace(
        "[LISTA_PAGINE]",
        createPaginationLinks($paginaCorrente, $totalePagine, $baseURL),
        $content
    );

    // if ($paginaCorrente < $totalePagine) {
    //     $content = str_replace("[avantiLink]", 'href="animali.php?pagina=' . ($paginaCorrente + 1) . '"', $content);
    // } else {
    //     $content = str_replace("[avantiLink]", 'class="disabled" aria-disabled="true"', $content);
    // }
} else {
    $listaAnimali =
        "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
}

$pulsanteAdd = "";
if (
    isset($_SESSION["is_logged_in"]) &&
    $_SESSION["is_logged_in"] === true &&
    isset($_SESSION["role"]) &&
    $_SESSION["role"] === "admin"
) {
    $pulsanteAdd = '<a id="add-animal-btn" role="button" href="ispeziona_animale.php?">
            <span class="material-symbols-outlined" aria-hidden="true">add</span>
            Aggiungi </a>';
}

$content = str_replace("[addButton]", $pulsanteAdd, $content);
$content = str_replace("[LISTA_ANIMALI]", $listaAnimali, $content);

echo $content;
?>
