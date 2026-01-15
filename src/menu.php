<?php

use Soap\Url;

require_once "utils/animalFormHandler.php";
require_once "utils/UrlHelper.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$pagina_corrente_con_query = basename($_SERVER['REQUEST_URI']);
$currentPage = basename($_SERVER['PHP_SELF']);






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
    $pagesFooter["area_personale.php"] = ["text" => "Area Personale"];
    // redirect, se siamo nella root il redirect crea problemi
    if ($currentPage === "index.php") {
        $urlLogout = "./logout.php";
    } else {
        $urlLogout = UrlHelper::appendRedirect('logout.php', ['redirect', "id", "pagina", "search"]);
    }
    $pages[$urlLogout] = ["text" => "Logout", "lang" => "en"];
    $pagesFooter[$urlLogout] = ["text" => "Logout", "lang" => "en"];
} elseif (
    isset($_SESSION["is_logged_in"]) &&
    $_SESSION["is_logged_in"] === true &&
    isset($_SESSION["role"]) &&
    $_SESSION["role"] === "admin"
) {


    // redirect, se siamo nella root il redirect crea problemi
    if ($currentPage === "index.php") {
        $urlLogout = "./logout.php";
    } else {
        $urlLogout = UrlHelper::appendRedirect('logout.php', ['redirect', "id", "pagina", "search"]);
    }
    $pages[$urlLogout] = ["text" => "Logout", "lang" => "en"];
    $pagesFooter[$urlLogout] = ["text" => "Logout", "lang" => "en"];
} else {
    // redirect, se siamo nella root il redirect crea problemi
    if ($currentPage === "index.php") {
        $urlLogin = "./login.php";
    } else {
        $urlLogin = UrlHelper::appendRedirect('login.php', ['redirect', "id", "pagina", "search"]);
    }
    $pages[$urlLogin] = ["text" => "Login", "lang" => "en"];
    $pagesFooter[$urlLogin] = ["text" => "Login", "lang" => "en"];
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
