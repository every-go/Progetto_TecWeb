<?php
session_start();
session_unset();
session_destroy();

session_abort();

session_start();
$_SESSION['messaggio_utente'] = '<span lang="en">  Logout&nbsp;</span>avvenuto con successo';
header("Location: index.php");
exit;
?>