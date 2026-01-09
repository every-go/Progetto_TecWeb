<?php 
include "menu.php";
$content = file_get_contents("html/index.html");
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);
echo $content;
?>