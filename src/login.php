<?php
session_start();
require_once "connectionDB.php";
use DB\DBAccess;

include "menu.php";



// Verifica se un URL è sicuro per il redirect (interno al sito).
function isSafeRedirect($url) {
    // controllo redirect vuoto
    if (empty(trim($url))) {
        return false;
    }

    // // controllo URL assoluto
    // if ($url[0] !== '/') {
    //     return false;
    // }

    // Controllo Protocollo: Evitiamo schemi come "http://" o "//"
    if (substr($url, 0, 2) === '//') {
        return false;
    }

    // 4. Controllo  ritorni a capo o caratteri nulli
    if (preg_match('/[\r\n]/', $url)) {
        return false;
    }

    return true;
}



$content = file_get_contents("html/login.html");
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

$username = "";
$errorMessages = [];

// pagina di redirect dopo login
$redirectPage=isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
if(!isSafeRedirect($redirectPage)){
    $redirectPage='index.php';
}


// funzioni di validazione
function validateUsername($username) {
    return preg_match('/^[a-zA-Z_]{4,24}$/', $username);
}

function validatePassword($password) {
    return strlen($password) >= 4;
}

function sanitizeUsername($username) {
    return htmlspecialchars(trim($username), ENT_QUOTES, "UTF-8");
}



// login solo se non già loggato
if (empty($_SESSION["is_logged_in"])) {

    if (isset($_POST["login"])) {
        $username = sanitizeUsername($_POST["username"] ?? '');
        $password = $_POST["password"] ?? '';

        // validazione
        if ($username === "") {
            $errorMessages['username'] = "Inserire lo <span lang='en'>username</span>";
        } elseif (!validateUsername($username)) {
            $errorMessages['username'] = "Username non valido";
        }

        if ($password === "") {
            $errorMessages['password'] = "Inserire la <span lang='en'>password</span>";
        } elseif (!validatePassword($password)) {
            $errorMessages['password'] = "Password non valida";
        }

        // se validi lato server, controllo DB
        if (empty($errorMessages)) {
            $db = new DBAccess();
            if ($db->openDBConnection()) {
                $res_login = $db->loginUtente($username, $password);
                $db->closeConnection();

                if ($res_login) {
                    $_SESSION['messaggio_utente'] = '<span lang="en">Login</span>&nbsp; avvenuto con successo';
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
        // messaggi statici di sistema → non escapare
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
if(isset($_GET['redirect'])){
    $redirectPage=htmlspecialchars('redirect='.$_GET['redirect'], ENT_QUOTES, "UTF-8");
}
else{
    $redirectPage='';
}
$content = str_replace("[PAGINA_PROVENIENZA]",$redirectPage , $content);
$content = str_replace("[ERROR_MESSAGE]", $messaggiHTML, $content);
$content = str_replace("[USERNAME_VALUE]", $username, $content);
$content = str_replace("[autofocusUsername]", $autofocus['username'], $content);
$content = str_replace("[autofocusPassword]", $autofocus['password'], $content);
$content = str_replace('[adottaMessage]', $adottaMessage, $content);

echo $content;
?>
