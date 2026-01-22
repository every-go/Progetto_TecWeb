<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'logged_in' => isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true
]);
?>