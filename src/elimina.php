<?php
session_start();
require_once "connectionDB.php";
use DB\DBAccess;

if ($_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}

$db = new DBAccess();
$connessioneOk = $db->openDBConnection();

if ($connessioneOk) {
    $id = $_GET["id"];
    $res = $db->eliminaAnimale($id);
    if ($res) {
        header("Location: animali.php");
        exit();
    } else {
        include __DIR__ . "/500.php";
        http_response_code(500);
        exit();
    }
} else {
    include __DIR__ . "/500.php";
    http_response_code(500);
    exit();
}

?>