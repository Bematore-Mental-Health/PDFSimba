<?php
include './DashboardBackend/session.php';
include './DashboardBackend/db_connection.php';

header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate");
header("Access-Control-Allow-Origin: *"); // Adjust for production

$response = [
    'isLoggedIn' => false,
    'userId' => null,
    'status' => 'ok'
];

try {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['user_id'])) {
        $response['isLoggedIn'] = true;
        $response['userId'] = $_SESSION['user_id'];
    }
    
    echo json_encode($response);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'isLoggedIn' => false,
        'userId' => null
    ]);
    exit();
}