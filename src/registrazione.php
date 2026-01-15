<?php
session_start();
require_once "connectionDB.php";
use DB\DBAccess;

include "menu.php";
$content = file_get_contents("html/registrazione.html");
$content = str_replace("[listaMenu]", $listaMenu, $content);
$content = str_replace("[listaFooter]", $listaFooter, $content);

// campi del form
$fields = [
    "username" => "",
    "password" => "",
    "confirm_password" => ""
];

// array errori
$errorMessages = [];

// funzioni di validazione
function validateUsername($value) {
    return preg_match('/^[a-zA-Z]{8,24}$/', $value);
}

function validatePassword($value) {
    if ($value === "#Flavio4") {
        return false;
    }
    
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\|`~])[A-Za-z\d!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\|`~]{8,24}$/';
    
    return preg_match($regex, $value) === 1;
}

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}

// gestione submit
if (isset($_POST["register"])) {
    foreach ($fields as $key => &$value) {
        $value = sanitize($_POST[$key] ?? '');
    }

    // validazioni
    if ($fields["username"] === "") {
        $errorMessages["username"] = "Inserire lo <span lang='en'>username</span>";
    } elseif (!validateUsername($fields["username"])) {
        $errorMessages["username"] = "Username non valido";
    }

    if ($fields["password"] === "") {
        $errorMessages["password"] = "Inserire la <span lang='en'>password</span>";
    } elseif (!validatePassword($fields["password"])) {
        $errorMessages["password"] = "Password non valida";
    }

    if ($fields["confirm_password"] === "") {
        $errorMessages["confirm_password"] = "Confermare la <span lang='en'>password</span>";
    } elseif ($fields["confirm_password"] !== $fields["password"]) {
        $errorMessages["confirm_password"] = "Le password non coincidono";
    }

    // se tutto valido, controllo DB
    if (empty($errorMessages)) {
        $db = new DBAccess();
        if ($db->openDBConnection()) {
            if ($db->checkUsernameEsistente($fields["username"])) {
                $errorMessages["username"] = "Username già esistente";
            } else {
                $res = $db->registerUtente($fields["username"], $fields["password"]);
                $db->closeConnection();
                if ($res) {
                    $_SESSION['messaggio_utente'] = 'Registrazione avvenuta con successo';
                    $parametersQuery=isset($_GET['redirect']) ? '?redirect='.($_GET['redirect']) : '';
                    header("Location: login.php".$parametersQuery);
                    exit();
                } else {
                    $errorMessages["username"] = "Lo username è già esistente";
                }
            }
        } else {
            include __DIR__ . "/500.php";
            http_response_code(500);
            exit();
        }
    }
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

// autofocus dinamico: primo campo con errore
$autofocus = [];
$focusAssigned = false;
foreach (array_keys($fields) as $campo) {
    if (!$focusAssigned && isset($errorMessages[$campo])) {
        $autofocus[$campo] = "autofocus";
        $focusAssigned = true;
    } else {
        $autofocus[$campo] = "";
    }
}

// messaggi sessione
$adottaMessage = '';
if (isset($_SESSION['messaggio_errore'])) {
    $adottaMessage = "<p class='error-login' aria-live='assertive' role='alert'>" .
                     htmlspecialchars($_SESSION['messaggio_errore'], ENT_QUOTES, "UTF-8") .
                     "</p>";
    unset($_SESSION['messaggio_errore']);
}
if(isset($_GET['redirect'])){
    $redirectPage=htmlspecialchars('redirect='.$_GET['redirect'], ENT_QUOTES, "UTF-8");
}
else{
    $redirectPage='';
}
$content = str_replace("[PAGINA_PROVENIENZA]",$redirectPage , $content);
// sostituzioni template
$content = str_replace("[ERROR_MESSAGE]", $messaggiHTML, $content);
$content = str_replace("[USERNAME_VALUE]", $fields["username"], $content);
$content = str_replace("[autofocusUsername]", $autofocus["username"], $content);
$content = str_replace("[autofocusPassword]", $autofocus["password"], $content);
$content = str_replace("[autofocusConfirmPassword]", $autofocus["confirm_password"], $content);
$content = str_replace("[adottaMessage]", $adottaMessage, $content);

echo $content;
?>