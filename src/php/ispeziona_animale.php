// ATTENZIONE la pagina ispeziona_animale ha due segnaposti anche nel title, controlla
// PS cancella questo commento quando hai completato

<?php 
    include "menu.php";
    $content = file_get_contents("../html/ispeziona_animale.html");
    $content = str_replace("[listaMenu]", $listaMenu, $content);
    $content = str_replace("[listaFooter]", $listaFooter, $content);
    echo $content;
?>