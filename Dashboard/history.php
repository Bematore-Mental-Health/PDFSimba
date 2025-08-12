<?php
include '../DashboardBackend/session.php';

// Redirect to login page if session is not set
if (!isUserLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Check if SQLite3 is available
if (!class_exists('SQLite3')) {
    die("SQLite3 extension is not enabled. Please enable it in php.ini");
}

try {
    $dbPath = __DIR__ . '/Backend/conversions.db';
    
    // Verify database file exists
    if (!file_exists($dbPath)) {
        throw new Exception("Database file not found at: " . $dbPath);
    }

    $db = new SQLite3($dbPath);
    $db->enableExceptions(true); 

    // Get user_id from session
    $user_id = $_SESSION['user_id'] ?? null;
    
    if (!$user_id) {
        die("You must be logged in to view conversion history");
    }

    // Get all conversions for this user, including parent-child relationships
    $query = '
        SELECT 
            c.*,
            p.original_filename as parent_original_filename,
            p.converted_filename as parent_converted_filename
        FROM conversions c
        LEFT JOIN conversions p ON c.parent_conversion_id = p.conversion_id
        WHERE c.user_id = :user_id 
        ORDER BY c.timestamp DESC
    ';
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':user_id', $user_id, SQLITE3_TEXT);
    
    $result = $stmt->execute();
    $conversions = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $conversions[] = $row;
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Function to force file download
function downloadFile($filePath) {
    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
    return false;
}

// Handle download request if filename parameter exists
if (isset($_GET['download']) && !empty($_GET['download'])) {
    $filename = basename($_GET['download']);
    $filePath = __DIR__ . '/CONVERTED/' . $filename;
    
    // Verify the file belongs to the user
    $stmt = $db->prepare('SELECT 1 FROM conversions WHERE user_id = :user_id AND converted_filename = :filename');
    $stmt->bindValue(':user_id', $user_id, SQLITE3_TEXT);
    $stmt->bindValue(':filename', $filename, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if ($result->fetchArray()) {
        if (file_exists($filePath)) {
            downloadFile($filePath);
        } else {
            die("File not found at: " . $filePath);
        }
    } else {
        die("Access denied or file not found in database");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDFSimba | Conversion History</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="../Media/book3.png" type="image/x-icon">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <style>
        .badge.completed { background-color: #28a745; }
        .badge.failed { background-color: #dc3545; }
        .badge.uploaded { background-color: #17a2b8; }
        .badge.processed { background-color: #6c757d; }
        .conversion-chain {
            border-left: 3px solid #dee2e6;
            padding-left: 15px;
            margin-left: 10px;
        }
        .conversion-item {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .conversion-item:hover {
            background-color: #e9ecef;
        }
        .conversion-type {
            font-weight: bold;
        }
        .conversion-details {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>

<?php 
include 'header-sidebar.php';
?>
    <div class="container mt-4">
        <h2>Conversion History</h2>
        <div class="alert alert-warning mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i> Note: Converted documents will be automatically deleted after 48 hours
        </div>
        
        <!-- Download Unavailable Modal -->
        <div class="modal fade" id="downloadErrorModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Download Unavailable</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>This document is no longer available as it has exceeded the 48-hour retention period.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($conversions)): ?>
            <div class="alert alert-info">No conversion history found</div>
        <?php else: ?>
            <div class="conversion-list">
                <?php 
                // Group conversions by parent-child relationships
                $groupedConversions = [];
                foreach ($conversions as $conv) {
                    if (empty($conv['parent_conversion_id'])) {
                        // This is a parent conversion
                        $groupedConversions[$conv['conversion_id']] = [
                            'parent' => $conv,
                            'children' => []
                        ];
                    }
                }
                
                // Add children to their parents
                foreach ($conversions as $conv) {
                    if (!empty($conv['parent_conversion_id']) && isset($groupedConversions[$conv['parent_conversion_id']])) {
                        $groupedConversions[$conv['parent_conversion_id']]['children'][] = $conv;
                    }
                }
                
                // Display the grouped conversions
                foreach ($groupedConversions as $group): 
                    $parent = $group['parent'];
                    $children = $group['children'];
                ?>
                    <div class="conversion-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="conversion-type">
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $parent['conversion_type']))) ?>
                                </span>
                                <span class="badge <?= $parent['status'] ?> ms-2">
                                    <?= ucfirst($parent['status']) ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                <?= htmlspecialchars($parent['timestamp']) ?>
                            </small>
                        </div>
                        <div class="conversion-details mt-2">
                            <div>
                                <strong>Original:</strong> <?= htmlspecialchars($parent['original_filename']) ?>
                            </div>
                            <?php if (!empty($parent['converted_filename'])): ?>
                                <div>
                                    <strong>Converted:</strong> 
                                    <?php 
                                    $filePath = __DIR__ . '/CONVERTED/' . $parent['converted_filename'];
                                    $fileExists = file_exists($filePath);
                                    ?>
                                    <?php if ($fileExists): ?>
                                        <a href="?download=<?= urlencode($parent['converted_filename']) ?>" class="btn btn-sm btn-success ms-2">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary ms-2" onclick="showUnavailableModal()">
                                            <i class="bi bi-download"></i> Download
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($children)): ?>
                            <div class="conversion-chain mt-3">
                                <?php foreach ($children as $child): ?>
                                    <div class="conversion-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="conversion-type">
                                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $child['conversion_type']))) ?>
                                                </span>
                                                <span class="badge <?= $child['status'] ?> ms-2">
                                                    <?= ucfirst($child['status']) ?>
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($child['timestamp']) ?>
                                            </small>
                                        </div>
                                        <div class="conversion-details mt-2">
                                            <?php if (!empty($child['converted_filename'])): ?>
                                                <div>
                                                    <strong>Result:</strong> 
                                                    <?php 
                                                    $filePath = __DIR__ . '/CONVERTED/' . $child['converted_filename'];
                                                    $fileExists = file_exists($filePath);
                                                    ?>
                                                    <?php if ($fileExists): ?>
                                                        <a href="?download=<?= urlencode($child['converted_filename']) ?>" class="btn btn-sm btn-success ms-2">
                                                            <i class="bi bi-download"></i> Download
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-secondary ms-2" onclick="showUnavailableModal()">
                                                            <i class="bi bi-download"></i> Download
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="./JS/toggle.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showUnavailableModal() {
            const modal = new bootstrap.Modal(document.getElementById('downloadErrorModal'));
            modal.show();
        }
    </script>
</body>
</html>