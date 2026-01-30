<?php
session_start();
require_once "connectionDB.php";
require_once "utils/UrlHelper.php";
require_once "utils/AvvisiHelper.php";
use DB\DBAccess;

include "menu.php";

$content = file_get_contents("html/login.html");
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

$username = "";

$fields = [
    "username" => "",
    "password" => ""
];
$errorMessages = [
    'username' => '',
    'password' => '',
    'usernameorpassword' => ''
];

// pagina di redirect dopo login

$redirectPage = 'index.php';
if (isset($_GET['redirect'])) {
    require_once __DIR__ . '/utils/UrlHelper.php';
    
    $redirectPage = $_GET['redirect'];
    $redirectPage = UrlHelper::getSafeRedirect(urldecode($redirectPage), 'index.php');
}

function validateUsername($username) {
    return preg_match('/^[A-Za-z ]{4,24}$/', $username);
}

function validatePassword($password) {
    return preg_match(
        '/^(user|admin|(?=(?:.*[a-z]))(?=(?:.*[A-Z]))(?=(?:.*\d))(?=(?:.*[!@#$%^&*()_\\-+=\\[\\]{};:\'",.<>\/?\\\\|`~]))[A-Za-z\d!@#$%^&*()_\\/\-+=\\[\\]{};:\'",.<>\/?\\\\|`~]{8,24})$/',
        $password
    );
}


function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}

// login solo se non già loggato
if (empty($_SESSION["is_logged_in"])) {

    if (isset($_POST["login"])) {

        $fields['username'] = isset($_POST['username']) ? sanitize($_POST['username']) : $fields['username'];
        $fields['password'] = isset($_POST['password']) ? sanitize($_POST['password']) : '';
        
        // aggiornamento per il template
        $username = $fields['username'];
        $password = $fields['password'];

        if(!validateUsername($fields["username"])){
            $errorMessages['username'] = 'Ricorda che lo <span lang="en">username</span> deve avere minimo 4 caratteri e massimo 24. Sono consentite solamente lettere maiuscole o minuscole e spazi, senza numeri o caratteri speciali.';
        }

        if(!validatePassword($fields["password"])){
            $errorMessages['password'] = 'Ricorda che la <span lang="en">password</span> è compresa fra 8 e 24 caratteri, ha almeno un numero, una lettera maiuscola, una minuscola e un carattere speciale.';
        }

        // se validi lato client, controllo DB
        if ( $errorMessages['username'] === '' && $errorMessages['password'] === '') {
            $db = new DBAccess();
            if ($db->openDBConnection()) {
                $res_login = $db->loginUtente($username, $password);
                $db->closeConnection();

                if ($res_login) {
                    AvvisiHelper::set('<span aria-hidden="true">✓</span>&nbsp;<span lang="en">Login</span>&nbsp; avvenuto con successo', 'success');
                    $_SESSION['is_logged_in'] = true;
                    header("Location:". $redirectPage);
                    exit();
                } else {
                    $errorMessages['usernameorpassword'] = '<span lang="en">Username</span> o <span lang="en">password</span> non validi!';
                }
            } else {
                include __DIR__ . "/500.php";
                http_response_code(500);
                exit();
            }
        }
    }

} else {

    header("Location: ".$redirectPage);
    exit();
}

$autofocus = ['username' => '', 'password' => ''];

if ($errorMessages['username'] !== '') {
    $autofocus['username'] = 'autofocus';
} elseif ($errorMessages['password'] !== '') {
    $autofocus['password'] = 'autofocus';
} elseif ($errorMessages['usernameorpassword'] !== '') {
    $autofocus['username'] = 'autofocus';
}


$adottaMessage = '';
if (isset($_SESSION['messaggio_errore'])) {
    $adottaMessage = "<p class='error-login' aria-live='assertive' role='alert'>" .
                    $_SESSION['messaggio_errore'] .
                     "</p>";
    unset($_SESSION['messaggio_errore']);
}


$content = str_replace("[PAGINA_PROVENIENZA]","redirect=".urlencode($redirectPage) , $content);
$content = str_replace('[adottaMessage]', $adottaMessage, $content);
$content = str_replace("[USERNAME_VALUE]", $username, $content);
$content = str_replace("[autofocusUsername]", $autofocus['username'], $content);
$content = str_replace("[errUsername]", $errorMessages['username'], $content);
$content = str_replace("[autofocusPassword]", $autofocus['password'], $content);
$content = str_replace("[errPassword]", $errorMessages['password'], $content);
$content = str_replace("[ERROR_MESSAGE]", $errorMessages['usernameorpassword'], $content);

echo $content;
?>
