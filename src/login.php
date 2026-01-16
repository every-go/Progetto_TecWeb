<?php
session_start();
require_once "connectionDB.php";
require_once "utils/UrlHelper.php";
require_once "utils/AvvisiHelper.php";
use DB\DBAccess;

include "menu.php";
// include "avvisi.php";



$content = file_get_contents("html/login.html");
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

$username = "";
$errorMessages = [];

// pagina di redirect dopo login

$redirectPage = 'index.php';
if (isset($_GET['redirect'])) {
    require_once __DIR__ . '/utils/UrlHelper.php';
    
    $redirectPage = $_GET['redirect'];
    $redirectPage = UrlHelper::getSafeRedirect(urldecode($redirectPage), 'index.php');
    // $redirectPage = ltrim($redirectPage, '/'); // Rimuovi lo slash iniziale per redirect relativi
    
    }

// echo urldecode($redirectPage);
// funzioni di validazione
function validateUsername($username) {
    return preg_match('/^[A-Za-z]{8,24}$/', $username) || $username === "user" || $username === "admin";
}

function sanitizeUsername($username) {
    return trim($username);
}

// login solo se non già loggato
if (empty($_SESSION["is_logged_in"])) {

    if (isset($_POST["login"])) {
        $username = sanitizeUsername($_POST["username"] ?? '');
        $password = $_POST["password"] ?? '';

        if(!validateUsername($username)){
            $errorMessages['username'] = "<span lang='en'>Username</span> non valido";
        }

        // se validi lato client, controllo DB
        if (empty($errorMessages)) {
            $db = new DBAccess();
            if ($db->openDBConnection()) {
                $res_login = $db->loginUtente($username, $password);
                $db->closeConnection();

                if ($res_login) {
                    // $_SESSION['messaggio_utente'] = '<span lang="en">Login</span>&nbsp; avvenuto con successo';
                    AvvisiHelper::set('<span lang="en">Login</span>&nbsp; avvenuto con successo', 'success');
                    $_SESSION['is_logged_in'] = true;
                    header("Location:". $redirectPage);
                    exit();
                } else {
                    $errorMessages['username'] = "<span lang='en'>Username</span> o <span lang='en'>password</span> non validi!";
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

// costruzione HTML errori
$messaggiHTML = "";
if (!empty($errorMessages)) {
    $messaggiHTML .= "<ul class='error-login'>";
    foreach ($errorMessages as $msg) {
        $messaggiHTML .= "<li aria-live='assertive' role='alert'>$msg</li>";
    }
    $messaggiHTML .= "</ul>";
}

// gestione autofocus dinamico: solo il primo errore
$autofocus = ['username'=>'','password'=>''];
foreach (['username','password'] as $campo) {
    if (isset($errorMessages[$campo])) {
        $autofocus[$campo] = "autofocus";
        break;
    }
}

$adottaMessage = '';
if (isset($_SESSION['messaggio_errore'])) {
    $adottaMessage = "<p class='error-login' aria-live='assertive' role='alert'>" .
                    $_SESSION['messaggio_errore'] .
                     "</p>";
    unset($_SESSION['messaggio_errore']);
}

// sostituzioni template
// if(isset($_GET['redirect'])){
//     $redirectPage='redirect='.$_GET['redirect'];
// }
// else{
//     $redirectPage='';
// }
$content = str_replace("[PAGINA_PROVENIENZA]","redirect=".urlencode($redirectPage) , $content);
$content = str_replace("[ERROR_MESSAGE]", $messaggiHTML, $content);
$content = str_replace("[USERNAME_VALUE]", $username, $content);
$content = str_replace("[autofocusUsername]", $autofocus['username'], $content);
$content = str_replace("[autofocusPassword]", $autofocus['password'], $content);
$content = str_replace('[adottaMessage]', $adottaMessage, $content);
// $content = str_replace("[messaggio]", $messaggio, $content);

echo $content;
?>
