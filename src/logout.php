<?php
session_start();
session_unset();    // Rimuove tutte le variabili di sessione
session_destroy();  // Distrugge la sessione sul server

session_abort();

session_start();
$_SESSION['messaggio_utente'] = '<span lang="en">Logout </span>avvenuto con successo';
// Rimanda alla home
header("Location: index.php");
exit;
?>