<?php


require_once "." . DIRECTORY_SEPARATOR . "connectionDB.php";
use DB\DBAccess;

include "menu.php";
require_once "utils/AvvisiHelper.php";
require_once "utils/singleCardBuilder.php";
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

$messaggio= AvvisiHelper::getFormattedHtml();

$content = str_replace("[H2_PREFERITI]", $h2Preferiti, $content);
$content = str_replace("[LISTA_PREFERITI]", $listaPreferiti, $content);
$content = str_replace("[H2_ADOTTATI]", $h2Adottati, $content);
$content = str_replace("[LISTA_ADOTTATI]", $listaAdottati, $content);
$content = str_replace("[NO_ANIMALI]", $noAnimali, $content);
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);
$content = str_replace("[messaggio]", $messaggio, $content);

echo $content;
?>
