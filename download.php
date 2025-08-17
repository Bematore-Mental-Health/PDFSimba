<?php
include './DashboardBackend/session.php';
include './DashboardBackend/db_connection.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Login required']);
    exit();
}

$file = $_GET['file'] ?? '';
$filePath = realpath('./converted/' . basename($file));

if (!$file || !file_exists($filePath)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'File not found']);
    exit();
}

// Record download in database
$stmt = $conn->prepare("INSERT INTO user_downloads (user_id, file_name, downloaded_at) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $_SESSION['user_id'], basename($filePath));
$stmt->execute();
$stmt->close();

// Set headers for download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
flush(); // Flush system output buffer
readfile($filePath);
exit();
?>