<?php
// session_start();
require_once 'connectionDB.php';
use DB\DBAccess;

function pulisciInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
    include "menu.php";
    $content = file_get_contents("../html/ispeziona_animale.html");
    $content = str_replace("[listaMenu]", $listaMenu, $content);
    $content = str_replace("[listaFooter]", $listaFooter, $content);
    
    if(isset($_SESSION['is_logged_in'])&&$_SESSION['is_logged_in'] === true 
                && isset($_SESSION['username'])&& $_SESSION['username']==="admin"){
        if(
            isset($_POST['aggiungiAnimale']) &&
            $nome=pulisciInput(isset($_POST['nome'])) &&
            $alt=pulisciInput(isset($_POST['alt'])) &&
            $immagine=pulisciInput(isset($_POST['immagine'])) &&
            $sesso=pulisciInput(isset($_POST['sesso'])) &&
            $tipo_animale=pulisciInput(isset($_POST['tipo_animale'])) &&
            $luogo=pulisciInput(isset($_POST['luogo'])) &&
            $eta=pulisciInput(isset($_POST['eta'])) &&
            $taglia=pulisciInput(isset($_POST['taglia'])) &&
            $carattere=pulisciInput(isset($_POST['carattere'])) &&
            $descrizione=pulisciInput(isset($_POST['descrizione'])) &&
            $bisogni=pulisciInput(isset($_POST['bisogni'])) &&
            $storia=pulisciInput(isset($_POST['storia']))
            ) {

    $db = new DBAccess();
    $db->openDBConnection();
    $id = $db->inserisciAnimale(
        $nome,
        $alt,
        $immagine,
        $sesso,
        $tipo_animale,
        $luogo,
        $eta,
        $taglia,
        $carattere,
        $descrizione,
        $bisogni,
        $storia
    );
    $db->closeConnection();
    if ($id) {
        // Inserimento riuscito, $id contiene l'ID del nuovo animale    
        header("Location: animale.php?id=".$id);
        exit();
    } 
    else {
        // Gestione dell'errore di inserimento
        $content = str_replace("[NOME_ANIMALE]", $nome, $content);
        $content = str_replace("[ETA_ANIMALE]", $eta, $content);
        $content = str_replace("[ALT_ANIMALE]", $alt, $content);
        $content = str_replace("[IMMAGINE_ANIMALE]", $immagine, $content);
        $content = str_replace("[CHECK_SESSO_M]", ($sesso == "Maschio") ? "checked" : "", $content);
        $content = str_replace("[CHECK_SESSO_F]", ($sesso == "Femmina") ? "checked" : "", $content);
        $content = str_replace("[CHECK_TIPO_REALE]", ($tipo_animale == "Reale") ? "checked" : "", $content);
        $content = str_replace("[CHECK_TIPO_FANTASY]", ($tipo_animale == "Fantasy") ? "checked" : "", $content);
        $content = str_replace("[LUOGO_ANIMALE]", $luogo, $content);
        $content = str_replace("[CHECK_TAGLIA_P]", ($taglia == "Piccola") ? "checked" : "", $content);
        $content = str_replace("[CHECK_TAGLIA_M]", ($taglia == "Media") ? "checked" : "", $content);
        $content = str_replace("[CHECK_TAGLIA_G]", ($taglia == "Grande") ? "checked" : "", $content);
        $content = str_replace("[CHECK_CAR_F]", ($carattere == "Facile") ? "checked" : "", $content);
        $content = str_replace("[CHECK_CAR_M]", ($carattere == "Moderato") ? "checked" : "", $content);
        $content = str_replace("[CHECK_CAR_D]", ($carattere == "Difficile") ? "checked" : "", $content);
        $content = str_replace("[DESCRIZIONE_ANIMALE]", ($descrizione), $content);
        $content = str_replace("[BISOGNI_ANIMALE]", ($bisogni), 	$content);
        $content = str_replace("[STORIA_ANIMALE]", ($storia), $content);
        $content = str_replace("[AZIONE]", "aggiungiAnimale", $content);
        $content = str_replace("[PULSANTE_AZIONE]", "Aggiungi Animale", $content);
    }
}
else{
    // Se non sono stati inviati tutti i dati necessari, mostra il form vuoto
    $content = str_replace("[NOME_ANIMALE]", "", $content);
    $content = str_replace("[ETA_ANIMALE]", "", $content);
    $content = str_replace("[ALT_ANIMALE]", "", $content);
    $content = str_replace("[IMMAGINE_ANIMALE]", "", $content);
    $content = str_replace("[CHECK_SESSO_M]", "", $content);
    $content = str_replace("[CHECK_SESSO_F]", "", $content);
    $content = str_replace("[CHECK_TIPO_REALE]", "", $content);
    $content = str_replace("[CHECK_TIPO_FANTASY]", "", $content);
    $content = str_replace("[LUOGO_ANIMALE]", "", $content);
    $content = str_replace("[CHECK_TAGLIA_P]", "", $content);
    $content = str_replace("[CHECK_TAGLIA_M]", "", $content);
    $content = str_replace("[CHECK_TAGLIA_G]", "", $content);
    $content = str_replace("[CHECK_CAR_F]", "", $content);
    $content = str_replace("[CHECK_CAR_M]", "", $content);
    $content = str_replace("[CHECK_CAR_D]", "", $content);
    $content = str_replace("[DESCRIZIONE_ANIMALE]", "", $content);
    $content = str_replace("[BISOGNI_ANIMALE]", "", $content);
    $content = str_replace("[STORIA_ANIMALE]", "", $content);
    $content = str_replace("[AZIONE]", "aggiungiAnimale", $content);
    $content = str_replace("[PULSANTE_AZIONE]", "Aggiungi Animale", $content);
}
} 
else {
        // Non sei loggato come admin, reindirizza o mostra un messaggio di errore
        header("Location: home.php");
        exit();
    }

    echo $content;

?>