<?php
    session_start();

    $currentPage = basename($_SERVER['PHP_SELF']);

    // pagine del menu 
    $pages = [
        'home.php' => ['text' => 'Home', 'lang' => 'en'],
        'animali.php' => ['text' => 'Animali'],
        'chiSiamo.php' => ['text' => 'Chi siamo'],
        
    ];

    // pagine del footer
    $pagesFooter = [
        'home.php' => ['text' => 'Home', 'lang' => 'en'],
        'animali.php' => ['text' => 'Animali'],
        'chiSiamo.php' => ['text' => 'Chi siamo'],
    ];

    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true && isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
        $pages['preferiti.php'] = ['text' => 'Preferiti'];
        $pages['logout.php'] = ['text' => 'Logout', 'lang' => 'en'];
        $pagesFooter['preferiti.php'] = ['text' => 'Preferiti'];

    } else if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $pages['logout.php'] = ['text' => 'Logout', 'lang' => 'en'];
    } else {
        $pages['login.php'] = ['text' => 'Login', 'lang' => 'en'];
    }

    // aggiunge crediti per ultimo 
    $pagesFooter['crediti.php'] = ['text' => 'Crediti'];

    $listaMenu = '<ul>';
    foreach($pages as $page => $data) {
        $isCurrentPage = false;

        if ($page === $currentPage) {
            $isCurrentPage = true;
        }

        if ($isCurrentPage) {
            $listaMenu .= '<li id="currentlink" aria-current="page" ' .
            (!empty($data['lang']) ? 'lang="' . $data["lang"] . '"' : '') .
            '>' . $data["text"] . '</li>';
        } else {
            $listaMenu .= '<li><a href="' . $page . '"' .
            (!empty($data['lang']) ? 'lang="' . $data["lang"] . '"' : '') .
            '>' . $data["text"] . '</a></li>';
        }
    }
    $listaMenu .= '</ul>';

    $listaFooter = '<ul>';
    foreach($pagesFooter as $page => $data) {
        $isCurrentPage = false;

        if ($page === $currentPage) {
            $isCurrentPage = true;
        }

        if ($isCurrentPage) {
            $listaFooter .= '<li aria-current="page"' .
            (!empty($data['lang']) ? 'lang="' . $data["lang"] . '"' : '') .
            '>' . $data["text"] . '</li>';
        } else {
            $listaFooter .= '<li><a href="' . $page . '"' .
            (!empty($data['lang']) ? 'lang="' . $data["lang"] . '"' : '') .
            '>' . $data["text"] . '</a></li>';
        }
    }
    $listaFooter .= '</ul>';


?>