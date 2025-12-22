<?php 
    session_start();
    require_once 'connectionDB.php';
    use DB\DBAccess;

    if ($_SESSION['role'] !== 'admin') {
        header('Location: home.php');
        exit();
    }

    $db = new DBAccess();
    $connessioneOk = $db->openDBConnection();

    if ($connessioneOk) {
        $id = $_GET["id"];
        $res = $db->eliminaAnimale($id);
        if ($res) {
            header('Location: animali.php');
            exit();
        } else {
            include dirname(__DIR__) . "/html/500.html";
            http_response_code(500);
            exit();
        }
    } else {
        include dirname(__DIR__) . "/html/500.html";
        http_response_code(500);
        exit();
    }

?>