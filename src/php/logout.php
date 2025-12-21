<?php
session_start();
session_unset();    // Rimuove tutte le variabili di sessione
session_destroy();  // Distrugge la sessione sul server

// Rimanda alla home o al login
header("Location: home.php");
exit;
?>