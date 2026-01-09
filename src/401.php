<?php 
include "menu.php";
$content = file_get_contents("html/401.html");
$content = str_replace('[listaFooter]', $listaFooter, $content);
echo $content;
?>