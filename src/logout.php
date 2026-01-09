<?php
session_start();
session_unset();    // Rimuove tutte le variabili di sessione
session_destroy();  // Distrugge la sessione sul server

// Rimanda alla home
header("Location: index.php");
exit;
?>