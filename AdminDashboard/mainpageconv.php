<?php
include '../DashboardBackend/session.php';
include '../DashboardBackend/db_connection.php'; // MySQL connection

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

if (!class_exists('SQLite3')) {
    die("SQLite3 extension is not enabled");
}

try {
    // SQLite database path
    $dbPath = __DIR__ . '../../Backend/conversions.db'; // Adjusted path
    if (!file_exists($dbPath)) {
        throw new Exception("Conversions database not found at: " . $dbPath);
    }

    // Connect to SQLite
    $sqlite = new SQLite3($dbPath);
    $sqlite->enableExceptions(true); 

    // Get all conversions from SQLite
    $result = $sqlite->query('SELECT * FROM conversions ORDER BY timestamp DESC');
    $conversions = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $conversions[] = $row;
    }

    // Get user details from MySQL for all user_ids found
    if (!empty($conversions)) {
        $userIds = array_unique(array_column($conversions, 'user_id'));
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email FROM users WHERE id IN ($placeholders)");
        
        // Bind parameters dynamically
        $types = str_repeat('s', count($userIds));
        $stmt->bind_param($types, ...$userIds);
        $stmt->execute();
        $userResult = $stmt->get_result();
        
        $users = [];
        while ($user = $userResult->fetch_assoc()) {
            $users[$user['id']] = $user;
        }
        
        // Merge user data with conversions
        foreach ($conversions as &$conv) {
            $conv['user_info'] = $users[$conv['user_id']] ?? null;
        }
    }

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDFSimba | Main Page Conversions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="../Media/book3.png" type="image/x-icon">
    <link rel="stylesheet" href="../CSS/dashboard.css">

    <style>
        .table-container { margin: 20px; overflow-x: auto; }
        .status-badge {
            padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;
        }
        .completed { background-color: #d4edda; color: #155724; }
        .failed { background-color: #f8d7da; color: #721c24; }
        .uploaded { background-color: #d1ecf1; color: #0c5460; }
        .pending { background-color: #fff3cd; color: #856404; }
        .processing { background-color: #cce5ff; color: #004085; }
    </style>
</head>
<body>

<?php include 'header-sidebar.php'; ?>

<div class="table-container">
    <h2>MainPage Document Conversions</h2>
    
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Email</th>
                <th>Document</th>
                <th>Type</th>
                <th>Status</th>
                <th>File Size</th>
                <th>Timestamp</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($conversions as $index => $conv): 
                $userInfo = $conv['user_info'] ?? null;
            ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <?= htmlspecialchars($userInfo['first_name'] ?? 'Unknown') ?> 
                        <?= htmlspecialchars($userInfo['last_name'] ?? 'User') ?>
                    </td>
                    <td><?= htmlspecialchars($userInfo['email'] ?? 'N/A') ?></td>
                    <td>
                        <?= htmlspecialchars($conv['original_filename'] ?? 'No filename') ?>
                        <?php if (!empty($conv['converted_filename'])): ?>
                            <br><small>→ <?= htmlspecialchars($conv['converted_filename']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($conv['conversion_type'] ?? 'N/A') ?></td>
                    <td>
                        <span class="status-badge <?= strtolower($conv['status'] ?? '') ?>">
                            <?= htmlspecialchars($conv['status'] ?? 'Unknown') ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($conv['file_size'])): ?>
                            <?= round($conv['file_size'] / 1024, 2) ?> KB
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($conv['timestamp']))) ?></td>
                    <td>
                        <?php if (!empty($conv['converted_filename']) && file_exists($conv['converted_filename'])): ?>
                            <a href="<?= htmlspecialchars($conv['converted_filename']) ?>" 
                               class="btn btn-sm btn-primary" download>
                                Download
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>