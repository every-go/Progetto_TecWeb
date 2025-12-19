//Qui non serve mettere ListaMenu e ListaFooter poiché idealmente i
//preferiti vengono solo visualizzati da chi ha fatto il login, questa pagina
//dovrebbe essere protetta dall'errore 401

<?php 
    include "menu.php";
    $content = file_get_contents("../html/preferiti.html");
    echo $content;
?>