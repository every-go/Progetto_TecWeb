<?php
session_start();
session_unset();
session_destroy();

session_abort();

session_start();
require_once __DIR__ . '/utils/UrlHelper.php';
require_once __DIR__ . '/utils/AvvisiHelper.php';
AvvisiHelper::set('<span aria-hidden="true">✓</span>&nbsp;<span lang="en">Logout</span>&nbsp;avvenuto con successo', 'success');

$redirectUrl = 'index.php';
if (isset($_GET['redirect'])) {
    require_once __DIR__ . '/utils/UrlHelper.php';
    $safeRedirect = UrlHelper::getSafeRedirect($_GET['redirect'], 'index.php');
    $redirectUrl = ltrim($safeRedirect, '/');
}


header("Location: " . $redirectUrl);
exit;
?>