<?php
require_once "connectionDB.php";

use DB\DBAccess;

function validateUsername($username)
{
    return preg_match('/^[a-zA-Z_]{3,20}$/', $username);
}

function validatePassword($password)
{
    return strlen($password) >= 4;
}

function sanitizeUsername($username)
{
    return htmlspecialchars(trim($username), ENT_QUOTES, "UTF-8");
}

include "menu.php";
$content = file_get_contents("html/registrazione.html");
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

$username = "";
$errorMessage = "";

if (
    isset($_POST["register"]) &&
    isset($_POST["username"]) &&
    isset($_POST["password"]) &&
    isset($_POST["confirm_password"])
) {

    $username = sanitizeUsername($_POST["username"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $errorMessage = "";

    if (!validateUsername($username) || !validatePassword($password)) {
        $errorMessage = "<p class='error-login' aria-live='assertive' role='alert'>
            Username o password non validi!</p>";
    } elseif ($password !== $confirmPassword) {
        $errorMessage = "<p class='error-login' aria-live='assertive' role='alert'>
            Le password non coincidono!</p>";
    } else {
        $db = new DBAccess();
        $connessioneOK = $db->openDBConnection();

        if ($connessioneOK) {

            if ($db->checkUsernameEsistente($username)) {
                $errorMessage = "<p class='error-login' aria-live='assertive' role='alert'>
                    Username già esistente!</p>";
            } else {
                $res_register = $db->registerUtente($username, $password);
                if ($res_register) {
                    $_SESSION['messaggio_utente'] = 'Registrazione avvenuta con successo';
                    $db->closeConnection();

                    // Memorizza la pagina corrente per il reindirizzamento dopo il login
                    $pagina_corrente = basename($_SERVER['REQUEST_URI']);

                    header("Location: login.php?redirect=" . urlencode($pagina_corrente));
                    exit();
                } else {
                    $errorMessage = "<p class='error-login' aria-live='assertive' role='alert'>
                        Errore durante la registrazione</p>";
                }
            }

            $db->closeConnection();
        } else {
            include __DIR__ . "/500.php";
            http_response_code(500);
            exit();
        }
    }
}

$content = str_replace("[ERROR_MESSAGE]", $errorMessage, $content);
$content = str_replace("[USERNAME_VALUE]", $username, $content);
$content = str_replace("[autofocus]", isset($_POST["username"]) ? "autofocus" : "", $content);

$adottaMessage = '';
if (isset($_SESSION['messaggio_errore'])) {
    $adottaMessage = "<p class='error-login' aria-live='assertive' role='alert'>" . $_SESSION['messaggio_errore'] . " </p>";
    unset($_SESSION['messaggio_errore']);
}
$content = str_replace('[adottaMessage]', $adottaMessage, $content);

echo $content;
