<?php

require_once "utils/animalFormHandler.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$currentPage = basename($_SERVER["PHP_SELF"]);

$pages = [
    "index.php" => ["text" => "Home", "lang" => "en"],
    "animali.php" => ["text" => "Animali"],
    "chiSiamo.php" => ["text" => "Chi siamo"],
];

$pagesFooter = [
    "index.php" => ["text" => "Home", "lang" => "en"],
    "animali.php" => ["text" => "Animali"],
    "chiSiamo.php" => ["text" => "Chi siamo"],
];

if (
    isset($_SESSION["is_logged_in"]) &&
    $_SESSION["is_logged_in"] === true &&
    isset($_SESSION["role"]) &&
    $_SESSION["role"] === "user"
) {
    $pages["area_personale.php"] = ["text" => "Area Personale"];
    $pages["logout.php"] = ["text" => "Logout", "lang" => "en"];
    $pagesFooter["area_personale.php"] = ["text" => "Area Personale"];
    $pagesFooter["logout.php"] = ["text" => "Logout", "lang" => "en"];
} elseif (
    isset($_SESSION["is_logged_in"]) &&
    $_SESSION["is_logged_in"] === true &&
    isset($_SESSION["role"]) &&
    $_SESSION["role"] === "admin"
) {
    $pages["logout.php"] = ["text" => "Logout", "lang" => "en"];
    $pagesFooter["logout.php"] = ["text" => "Logout", "lang" => "en"];
} else {
    // Memorizza la pagina corrente per il reindirizzamento dopo il login
    $pagina_corrente = basename($_SERVER['REQUEST_URI']);
    if(!isset($_GET['redirect'])){
        $_GET['redirect']=$pagina_corrente;
    }
    // $_GET['redirect']="/".$pagina_corrente;
    $pages["login.php?".createHttpQuery($_GET,['redirect'])] = ["text" => "Login", "lang" => "en"];
    $pagesFooter["login.php?".createHttpQuery($_GET,['redirect'])] = ["text" => "Login", "lang" => "en"];
    // $pagesFooter["login.php?redirect=" . urlencode($pagina_corrente)] = ["text" => "Login", "lang" => "en"];

}

$pagesFooter["crediti.php"] = ["text" => "Crediti"];

$listaMenu = "<ul>";
foreach ($pages as $page => $data) {
    $isCurrentPage = false;

    if (strtok($page, '?') === $currentPage) {
        $isCurrentPage = true;
    }

    if ($isCurrentPage) {
        $listaMenu .=
            '<li id="currentlink" aria-current="page"' .
            (!empty($data["lang"]) ? ' lang="' . $data["lang"] . '"' : "") .
            ">" .
            $data["text"] .
            "</li>";
    } else {
        $listaMenu .=
            '<li><a href="' .
            $page .
            '"' .
            (!empty($data["lang"]) ? ' lang="' . $data["lang"] . '"' : "") .
            ">" .
            $data["text"] .
            "</a></li>";
    }
}
$listaMenu .= "</ul>";

$listaFooter = "<ul>";
foreach ($pagesFooter as $page => $data) {
    $isCurrentPage = false;

    if ($page === $currentPage) {
        $isCurrentPage = true;
    }

    if ($isCurrentPage) {
        $listaFooter .=
            '<li aria-current="page"' .
            (!empty($data["lang"]) ? ' lang="' . $data["lang"] . '"' : "") .
            ">" .
            $data["text"] .
            "</li>";
    } else {
        $listaFooter .=
            '<li><a href="' .
            $page .
            '"' .
            (!empty($data["lang"]) ? ' lang="' . $data["lang"] . '"' : "") .
            ">" .
            $data["text"] .
            "</a></li>";
    }
}
$listaFooter .= "</ul>";
