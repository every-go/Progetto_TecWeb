<?php
session_start();
session_unset();
session_destroy();

session_abort();

session_start();
require_once __DIR__ . '/utils/UrlHelper.php';
require_once __DIR__ . '/utils/AvvisiHelper.php';
// $_SESSION['messaggio_utente'] = '<span lang="en">  Logout&nbsp;</span>avvenuto con successo';
AvvisiHelper::set('<span lang="en">  Logout&nbsp;</span>avvenuto con successo', 'success');

$redirectUrl = 'index.php';
if (isset($_GET['redirect'])) {
    require_once __DIR__ . '/utils/UrlHelper.php';
    $safeRedirect = UrlHelper::getSafeRedirect($_GET['redirect'], 'index.php');
    $redirectUrl = ltrim($safeRedirect, '/'); // Rimuovi lo slash iniziale per redirect relativi
}


header("Location: " . $redirectUrl);
exit;
?>