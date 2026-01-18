<?php
session_start();
require_once "connectionDB.php";
require_once "utils".DIRECTORY_SEPARATOR."AvvisiHelper.php";
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
    return preg_match('/^[a-zA-Z]{4,24}$/', $value);
}

function validatePassword($value) {
    if ($value === "#Flavio4") {
        return false;
    }
    
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\|`~])[A-Za-z\d!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\|`~]{8,24}$/';
    
    return preg_match($regex, $value);
}

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}


// registrazione solo se non già loggato
if (empty($_SESSION["is_logged_in"])) {
    // gestione submit
    if (isset($_POST["register"])) {
        foreach ($fields as $key => &$value) {
            $value = sanitize($_POST[$key] ?? '');
        }

        // validazioni
        if (!validateUsername($fields["username"])) {
            $errorMessages["username"] = '<div id="errUsername" class="errorSuggestion" role="alert">Lo <span lang="en">username</span> deve avere minimo 4 caratteri e massimo 24. Può contenere solo lettere maiuscole o minuscole.</div>';
        }

        if (!validatePassword($fields["password"])) {
            $errorMessages["password"] = '<div id="errPassword" class="errorSuggestion" role="alert"><span lang="en">Password</span> non valida. Deve contenere almeno 1 lettera maiuscola, 1 lettera minuscola, 1 numero e almeno un carattere speciale, minimo 8, massimo 24 caratteri.</div>';
        }

        if ($fields["confirm_password"] === "") {
            $errorMessages["confirm_password"] = '<div id="errConfirmPassword" class="errorSuggestion" role="alert">Confermare la <span lang="en">password</span></div>';
        } elseif ($fields["confirm_password"] !== $fields["password"]) {
            $errorMessages["confirm_password"] = '<div id="errConfirmPassword" class="errorSuggestion" role="alert">Le <span lang="en">password</span> non coincidono</div>';
        }

        // se tutto valido, controllo DB
        if (empty($errorMessages)) {
            $db = new DBAccess();
            if ($db->openDBConnection()) {
                if ($db->checkUsernameEsistente($fields["username"])) {
                    $errorMessages["usernamenotvalid"] = '<div class="errorSuggestion" role="alert"><span lang="en">Username</span> già esistente.';
                } 
                else {
                    $res = $db->registerUtente($fields["username"], $fields["password"]);
                    $db->closeConnection();
                    if ($res) {
                        // $_SESSION['messaggio_utente'] = 'Registrazione avvenuta con successo';
                        AvvisiHelper::set('<span>Registrazione</span> &nbsp; avvenuta con successo', 'success');
                        
                        // $parametersQuery=isset($_GET['redirect']) ? '?redirect='.($_GET['redirect']) : '';
                        $redirectPage = 'index.php';
                        
                        if (isset($_GET['redirect'])) {
                            $redirectPage = $_GET['redirect'];
                            $redirectPage = UrlHelper::getSafeRedirect(urldecode($redirectPage), 'index.php');
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
}

// autofocus dinamico: primo campo con errore
$autofocus = [];

// decisione unica del target
$focusTarget = null;

if (isset($errorMessages['password']) || isset($errorMessages['confirm_password'])) {
    $focusTarget = 'password';
} else {
    foreach (array_keys($fields) as $campo) {
        if (isset($errorMessages[$campo])) {
            $focusTarget = $campo;
            break;
        }
    }
}

foreach (array_keys($fields) as $campo) {
    $autofocus[$campo] = ($campo === $focusTarget) ? 'autofocus' : '';
}

// messaggi sessione

if(isset($_GET['redirect'])){
    $redirectPage=htmlspecialchars('redirect='.urlencode($_GET['redirect']), ENT_QUOTES, "UTF-8");
}
else{
    $redirectPage='';
}
$content = str_replace("[PAGINA_PROVENIENZA]",$redirectPage , $content);
$content = str_replace("[errUsername]", $errorMessages["username"], $content);
$content = str_replace("[errPassword]", $errorMessages["password"], $content);
$content = str_replace("[errConfirmPassword]", $errorMessages["confirm_password"], $content);
$content = str_replace("[ERROR_MESSAGE]", $errorMessages["usernamenotvalid"], $content);
$content = str_replace("[USERNAME_VALUE]", $fields["username"], $content);
$content = str_replace("[autofocusUsername]", $autofocus["username"], $content);
$content = str_replace("[autofocusPassword]", $autofocus["password"], $content);

echo $content;
?>