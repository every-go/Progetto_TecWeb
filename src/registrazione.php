<?php
session_start();
require_once "connectionDB.php";
require_once "utils" . DIRECTORY_SEPARATOR . "AvvisiHelper.php";
require_once "utils" . DIRECTORY_SEPARATOR . "UrlHelper.php";

use DB\DBAccess;

include "menu.php";

$content = file_get_contents("html/registrazione.html");
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

$username = "";

$fields = [
    "username" => "",
    "password" => "",
    "confirm_password" => ""
];

$errorMessages = [
    'username' => '',
    'password' => '',
    'confirm_password' => '',
    'usernamenotvalid' => ''
];

function validateUsername(string $value): bool
{
    return (bool) preg_match('/^[a-zA-Z ]{4,24}$/', $value);
}

function validatePassword(string $value): bool
{
    if ($value === "#Flavio4") {
        return false;
    }

    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\|`~])[A-Za-z\d!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\|`~]{8,24}$/';
    return (bool) preg_match($regex, $value);
}

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}

if ((!isset($_SESSION["is_logged_in"]) || empty($_SESSION["is_logged_in"])) && isset($_POST["register"])) {

    // sanitizzazione input
    foreach ($fields as $key => &$value) {
        $value = sanitize($_POST[$key] ?? '');
    }

    $username = $fields["username"];
    $password = $fields["password"];
    $confirm_password = $fields["confirm_password"];

    // username
    if (!validateUsername($username)) {
        $errorMessages["username"] =
            'Lo <span lang="en">username</span> deve avere minimo 4 caratteri e massimo 24. 
                Può contenere solo lettere maiuscole, minuscole e spazi senza numeri o caratteri speciali.';
    }

    // password
    if (!validatePassword($password)) {
        $errorMessages["password"] =
            '<span lang="en">Password</span> non valida. 
                Deve contenere almeno 1 lettera maiuscola, 
                1 lettera minuscola, 
                1 numero e 
                almeno un carattere speciale, 
                minimo 8, massimo 24 caratteri.';
    }

    // conferma password
    if ($confirm_password === '') {
        $errorMessages["confirm_password"] =
            'Confermare la <span lang="en">password</span>.';
    } elseif ($password !== $confirm_password) {
        $errorMessages["confirm_password"] =
            'Le <span lang="en">password</span> non coincidono.';
    }

    // controllo DB solo se NON ci sono errori lato client
    if ($errorMessages['username'] === '' && $errorMessages['password'] === '' && $errorMessages['confirm_password'] === '') {
        $db = new DBAccess();

        if ($db->openDBConnection()) {

            if ($db->checkUsernameEsistente($username)) {

                $errorMessages["usernamenotvalid"] =
                    '<span lang="en">Username</span> già esistente.';

                $db->closeConnection();
            } else {

                $res = $db->registerUtente($username, $password);
                $db->closeConnection();

                if ($res) {

                    $redirectPage = 'index.php';
                    if (isset($_GET['redirect'])) {
                        $redirectPage = UrlHelper::getSafeRedirect(
                            urldecode($_GET['redirect']),
                            'index.php'
                        );
                    }

                    header("Location: login.php?redirect=" . urlencode($redirectPage));
                    exit();
                }
            }
        } else {
            include __DIR__ . "/500.php";
            http_response_code(500);
            exit();
        }
    }
}

$autofocus = [];
$focusTarget = null;

// password ha priorità
if ($errorMessages['password'] !== '' || $errorMessages['confirm_password'] !== '') {
    $focusTarget = 'password';
} else {
    foreach (array_keys($fields) as $campo) {
        if ($errorMessages[$campo] !== '') {
            $focusTarget = $campo;
            break;
        }
    }
}

foreach (array_keys($fields) as $campo) {
    $autofocus[$campo] = ($campo === $focusTarget) ? 'autofocus' : '';
}

$redirectParam = '';
if (isset($_GET['redirect'])) {
    $redirectParam = 'redirect=' . urlencode($_GET['redirect']);
}

$content = str_replace("[PAGINA_PROVENIENZA]", $redirectParam, $content);
$content = str_replace("[USERNAME_VALUE]", $username, $content);
$content = str_replace("[errUsername]", $errorMessages["username"], $content);
$content = str_replace("[errPassword]", $errorMessages["password"], $content);
$content = str_replace("[errConfirmPassword]", $errorMessages["confirm_password"], $content);
$content = str_replace("[ERROR_MESSAGE]", $errorMessages["usernamenotvalid"], $content);
$content = str_replace("[autofocusUsername]", $autofocus["username"], $content);
$content = str_replace("[autofocusPassword]", $autofocus["password"], $content);

echo $content;
?>