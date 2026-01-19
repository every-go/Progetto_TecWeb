<?php
require_once "." . DIRECTORY_SEPARATOR . "connectionDB.php";
require_once "utils" . DIRECTORY_SEPARATOR . "singleCardBuilder.php";
require_once "." .
    DIRECTORY_SEPARATOR .
    "utils" .
    DIRECTORY_SEPARATOR .
    "pagination.php";
use DB\DBAccess;



include "menu.php";
require_once "utils/AvvisiHelper.php";
require_once "utils/DataSanitizer.php";

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

        $stringaAnimali = DataSanitizer::pulisciLista($stringaAnimali);
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
    
    $messaggioRisultati = "";
    $indicatorePagina = "";
    $baseURL = "animali.php?";
    if ($isSearch) {
        // Messaggio risultati ricerca
        $messaggioRisultati = '<p> Risultati per <strong>' . htmlspecialchars($searchTerm) . '</strong>: ';
        $messaggioRisultati .= $totaleAnimali . ' ' . ($totaleAnimali === 1 ? 'animale trovato' : 'animali trovati');
        $messaggioRisultati .= '</p>';
        
        if($totaleAnimali === 0){
            $listaAnimali = '<p> Nessun animale trovato. <a href="animali.php">Torna all\'elenco completo</a> </p>';
        }

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
    $parametriQuery = $_GET;
    $pulsanteAdd = '<a id="add-animal-btn" role="button" href="aggiungiAnimale.php?' . 
    htmlspecialchars(http_build_query($parametriQuery)) . 
    '">
    <span class="material-symbols-outlined" aria-hidden="true">add</span> Aggiungi </a>';
}
$messaggio= AvvisiHelper::getFormattedHtml();
$content = str_replace("[addButton]", $pulsanteAdd, $content);
$content = str_replace("[LISTA_ANIMALI]", $listaAnimali, $content);
$content = str_replace("[messaggio]", $messaggio, $content);
$content = str_replace("[risultatoRicerca]", $messaggioRisultati, $content);

echo $content;
?>
