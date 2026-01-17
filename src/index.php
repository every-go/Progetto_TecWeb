<?php 
include "menu.php";
require_once "utils/AvvisiHelper.php";
$messaggio = AvvisiHelper::getFormattedHtml();

$content = file_get_contents("html/index.html");
$content = str_replace("[messaggio]", $messaggio, $content);
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);
echo $content;
?>