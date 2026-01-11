<?php 
include "menu.php";
$messaggio = "";
if (isset($_SESSION['messaggio_utente'])) {
    $messaggio = "<div id='avviso' role='aler'> <p>" . $_SESSION['messaggio_utente'] . "</p> <div class='progress-container'>
        <div class='progress-bar'></div>
    </div> </div>";
    unset($_SESSION['messaggio_utente']);
}
$content = file_get_contents("html/index.html");
$content = str_replace("[messaggio]", $messaggio, $content);
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);
echo $content;
?>