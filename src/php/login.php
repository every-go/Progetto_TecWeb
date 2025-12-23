<?php 
    require_once 'connectionDB.php';

    use DB\DBAccess;

    // solo lettere e lunghezza di 3 <= x <= 20 caratteri
    function validateUsername($username) {
        return preg_match('/^[a-zA-Z_]{3,20}$/', $username);
    }

    // password di almeno 4 caratteri (di più?)
    function validatePassword($password) {
        return strlen($password) >=4;
    }

    function sanitizeUsername($username) {
        return htmlspecialchars(trim($username), ENT_QUOTES, 'UTF-8');
    }
    //  session is already active in /var/www/html/php/menu.php on line 2
    // session_start();

 

    include "menu.php";
    $content = file_get_contents("../html/login.html");
    $content = str_replace("[listaMenu]", $listaMenu, $content);
    $content = str_replace("[listaFooter]", $listaFooter, $content);

    if(!isset($_SESSION['is_logged_in'])||$_SESSION['is_logged_in'] !== true){
        // form da fare meglio

        //POSSIBILE ANCHE METTERLO STATICAMENTE NEL HTML e tenere solo gli errori
        $loginFormTemplate='<form action="../php/login.php" method="post" id="main-content">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="[USERNAME_VALUE]" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" name="login" value="login">
        </form>';
        $content = str_replace("[LOGIN_FORM]", $loginFormTemplate, $content);

        if(isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password'])) {
            // echo("sono in login");
            $username = sanitizeUsername($_POST['username']);
            $password = $_POST['password'];
            if (validateUsername($username) && validatePassword($password)) {
                // echo("dati validi");
                $db = new DBAccess();
                $connessioneOK = $db->openDBConnection();
                
                if ($connessioneOK) {
                    // echo("connessione ok");
                    $res_login = $db->loginUtente($username, $password); 
                    $db->closeConnection();
                    if ($res_login) {
                        // echo("login ok");
                        // Login riuscito
                        header("Location: home.php");
                        exit();
                    } else {
                        // Login fallito
                        // echo("login fallito");
                        $errorMessage = "Username o password non validi.";
                        $content = str_replace("[ERROR_MESSAGE]", "<p class='error'>$errorMessage</p>", $content);
                        $content = str_replace("[USERNAME_VALUE]", $username, $content);
                    }
                } else {
                    // echo("connessione fallita");
                    include dirname(__DIR__) . "/html/500.html";
                    http_response_code(500);
                    exit();
                }
            } else { 
                    // echo("dati non validi");
                    $content = str_replace("[USERNAME_VALUE]", $username, $content);
                    $errorMessage = "Username o password non validi.";
                    $content = str_replace("[ERROR_MESSAGE]", "<p>$errorMessage</p>", $content);
            }
        } else {
            // echo("nessun dato inviato");
            // Non è stato inviato alcun modulo, nessun messaggio di errore
            $content = str_replace("[ERROR_MESSAGE]", "", $content);
        }
    } else {
        // Utente già loggato, reindirizza alla home

        // O COSI O TRAMITE header( ), DA DISCUTERE E DECIDERE
        $alreadyLoggesInMessage = '<p>Sei già loggato come '. $_SESSION['username'].' </p>
        <p><a href="home.php">Vai alla home</a></p>'.
        '<p><a href="logout.php">Logout</a></p>';
        $content = str_replace("[ERROR_MESSAGE]", "", $content);
        $content = str_replace("[LOGIN_FORM]", $alreadyLoggesInMessage, $content);
    }

    // replace del USERNAME_VALUE nel form
    $content = str_replace("[USERNAME_VALUE]", "", $content);
    echo $content;
?>