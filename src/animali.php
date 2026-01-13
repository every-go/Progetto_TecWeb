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
        $animale["immagine"] ? $animale["immagine"] : "./images/png/notfound.png",
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

$_SESSION["provenienza"] = "Animali";

$animaliPerPagina = 3;

$paginaCorrente = isset($_GET["pagina"]) ? (int) $_GET["pagina"] : 1;
if ($paginaCorrente < 1) {
    $paginaCorrente = 1;
}

$stringaAnimali = "";
$listaAnimali = "";

$isSearch = isset($_GET["search"]) && !empty(trim($_GET["search"]));

$searchTerm = $isSearch ? trim($_GET["search"]) : "";

$searchValue = $isSearch ? htmlspecialchars($searchTerm) : "";

$content = str_replace("[SEARCH_VALUE]", $searchValue, $content);

$db = new DBAccess();
$connessioneOK = $db->openDBConnection();
if ($connessioneOK) {
    if ($isSearch) {
        if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
            $stringaAnimali = $db->searchAnimali($searchTerm);
        } else {
            $stringaAnimali = $db->searchAnimaliAdottabili($searchTerm);
        }
        $db->closeConnection();
    } else {
        $stringaAnimali = $db->getList();
        $db->closeConnection();
    }

    if ($stringaAnimali === false) {
        include __DIR__ . "/500.php";
        http_response_code(500);
        exit();
    }

    if (empty($stringaAnimali)) {
        $totaleAnimali = 0;
        $totalePagine = 0;
        $listaAnimali = '<p>Nessun animale disponibile al momento.</p>';
    } else {
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

        foreach ($animaliPaginaCorrente as $stringaAnimale) {
            $listaAnimali .= singleCardBuilder($stringaAnimale);
        }
        $listaAnimali = '<ul class="animali">' . $listaAnimali . "</ul>";
    }
    
    $indicatorePagina = "";
    $baseURL = "animali.php?";
    if ($isSearch) {
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
    }

    $content = str_replace(
        "[LISTA_PAGINE]",
        createPaginationLinks($paginaCorrente, $totalePagine, $baseURL),
        $content
    );

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
