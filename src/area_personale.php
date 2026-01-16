<?php


function singleCardBuilder($animale)
{
    $animaleCard = file_get_contents("html/animalCardTemplate.html");
    $animaleCard = str_replace("[ID_ANIMALE]", $animale["id"], $animaleCard);
    $animaleCard = str_replace(
        "[NOME_ANIMALE]",
        $animale["nome"],
        $animaleCard
    );
    $animaleCard = str_replace(
        "[SESSO_ANIMALE]",
        "<img src='./images/png/".strtolower($animale["sesso"]).".png' class = 'sesso' style='' alt='Sesso: ".$animale["sesso"]."'>",
        $animaleCard );
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

require_once "." . DIRECTORY_SEPARATOR . "connectionDB.php";
use DB\DBAccess;

include "menu.php";
$content = file_get_contents("html/area_personale.html");

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "user") {
    header("Location: 401.php");
    exit();
}

$_SESSION["provenienza"] = "Area Personale";
$stringaPreferiti = "";
$h2Preferiti = "";
$listaPreferiti = "";
$h2Adottati = "";
$listaAdottati = "";
$db = new DBAccess();
$connessioneOK = $db->openDBConnection();

if ($connessioneOK) {
    $h2Preferiti = "";
    $h2Adottati = "";
    $noAnimali = "";
    $stringaPreferiti = $db->getPreferiti($_SESSION["username"]);
    $stringaAdottati = $db->getAdottati($_SESSION["username"]);
    $db->closeConnection();

    if (!empty($stringaPreferiti)) {
        $h2Preferiti =
            "<h2 id = 'preferiti-content'>I tuoi animali preferiti </h2>";
        $listaPreferiti = '<ul class="animali">';
        foreach ($stringaPreferiti as $stringaAnimale) {
            $listaPreferiti .= singleCardBuilder($stringaAnimale);
        }
        $listaPreferiti .= "</ul>";
    }

    if (!empty($stringaAdottati)) {
        $h2Adottati = "<h2 id='adottati-content'>I tuoi animali adottati</h2>";
        $listaAdottati = '<ul class="animali">';
        foreach ($stringaAdottati as $stringaAnimale) {
            $listaAdottati .= singleCardBuilder($stringaAnimale);
        }
        $listaAdottati .= "</ul>";
    }

    if(empty($stringaAdottati) && empty($stringaPreferiti)){
        $noAnimali = "<p> Non hai animali né preferiti né adottati </p>"
            . "<p>Vai nella sezione <a href='animali.php'>animali</a> per vedere tutti i nostri animali!";
    }

} else {
    $listaPreferiti =
        "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
}

$content = str_replace("[H2_PREFERITI]", $h2Preferiti, $content);
$content = str_replace("[LISTA_PREFERITI]", $listaPreferiti, $content);
$content = str_replace("[H2_ADOTTATI]", $h2Adottati, $content);
$content = str_replace("[LISTA_ADOTTATI]", $listaAdottati, $content);
$content = str_replace("[NO_ANIMALI]", $noAnimali, $content);
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);
echo $content;
?>
