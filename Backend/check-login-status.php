<?php
// check-login-status.php
include './DashboardBackend/session.php';
include './DashboardBackend/db_connection.php';


// check-login-status.php
session_start();
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$response = [
    'isLoggedIn' => isset($_SESSION['user_id']),
    'userId' => $_SESSION['user_id'] ?? null
];

echo json_encode($response);
?>