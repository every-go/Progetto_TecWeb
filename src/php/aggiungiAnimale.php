<?php
session_start();

    include "menu.php";
    $content = file_get_contents("../html/login.html");
    $content = str_replace("[listaMenu]", $listaMenu, $content);
    $content = str_replace("[listaFooter]", $listaFooter, $content);
    
    if(isset($_SESSION['is_logged_in'])&&$_SESSION['is_logged_in'] === true 
                && isset($_SESSION['username'])&& $_SESSION['username']==="admin"){
        if(
            isset($_POST['aggiungiAnimale']) &&
            isset($_POST['id']
            // TODO:
            // aggiungere tutti gli isset dei campi
            ) 
            ) {
            $_SESSION['redirect_after_login'] = 'animale.php?id=';
            header("Location: login.php");
            exit();
        }
    }

    echo $content;

?>