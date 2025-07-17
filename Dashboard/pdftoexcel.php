<?php
include '../DashboardBackend/session.php';

if (!isUserLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Define the backend URL constant
define('BACKEND_URL', 'http://localhost:5001');

if (!class_exists('SQLite3')) {
    die("SQLite3 extension is not enabled");
}

try {
    $dbPath = __DIR__ . '/Backend/conversions.db';
    if (!file_exists($dbPath)) {
        throw new Exception("Database not found");
    }

    $db = new SQLite3($dbPath);
    $db->enableExceptions(true); 

    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) die("Login required");

    // Get documents from last 48 hours
    $fortyEightHoursAgo = date('Y-m-d H:i:s', strtotime('-48 hours'));
    $stmt = $db->prepare('SELECT * FROM conversions 
                         WHERE user_id = :user_id 
                         AND conversion_type = "pdf_to_excel" 
                         AND status != "failed" 
                         AND timestamp > :fortyEightHoursAgo 
                         ORDER BY timestamp DESC');
    $stmt->bindValue(':user_id', $user_id, SQLITE3_TEXT);
    $stmt->bindValue(':fortyEightHoursAgo', $fortyEightHoursAgo, SQLITE3_TEXT);
    
    $result = $stmt->execute();
    $documents = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        // Only include if we have a converted filename
        if (!empty($row['converted_filename'])) {
            $documents[] = $row;
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
    <title>PDFSimba | PDF to Excel Conversions</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="../Media/book3.png" type="image/x-icon">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <style>
        .document-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .document-icon {
            font-size: 3rem;
            color: #198754; /* Excel green color */
        }
        .document-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .badge.completed { background-color: #28a745; }
        .badge.failed { background-color: #dc3545; }
        .badge.uploaded { background-color: #17a2b8; }
        .expiry-notice {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>

<?php include 'header-sidebar.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>PDF to Excel Conversions</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pdfToExcelModal">
            <i class="bi bi-file-earmark-pdf"></i> Convert New PDF
        </button>
    </div>

    <div class="alert alert-warning expiry-notice mb-4">
        <i class="bi bi-clock-history"></i> Note: Documents are automatically deleted after 48 hours
    </div>

    <?php if (empty($documents)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No converted Excel files available.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php foreach ($documents as $doc): ?>
                <div class="col">
                    <div class="card document-card">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-excel-fill document-icon mb-3"></i>
                            <h5 class="card-title document-name" title="<?= htmlspecialchars($doc['original_filename']) ?>">
                                <?= htmlspecialchars($doc['original_filename']) ?>
                            </h5>
                            <p class="card-text text-muted small">
                                <?= date('M d, Y H:i', strtotime($doc['timestamp'])) ?>
                            </p>
                            <span class="badge <?= $doc['status'] ?> mb-2">
                                <?= ucfirst($doc['status']) ?>
                            </span>
                            <div class="d-grid gap-2">
                                <!-- Download link -->
                                <a href="<?= BACKEND_URL ?>/download/<?= htmlspecialchars($doc['converted_filename']) ?>" 
                                   class="btn btn-sm btn-outline-primary" download>
                                    <i class="bi bi-download"></i> Download Excel
                                </a>
                                <!-- Edit link -->
                                <a href="<?= BACKEND_URL ?>/excel-editor/<?= htmlspecialchars($doc['converted_filename']) ?>?conversion_id=<?= $doc['conversion_id'] ?>&user_id=<?= $user_id ?>" 
                                   class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-pencil"></i> Edit Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- PDF to Excel Conversion Modal -->
<div class="modal fade" id="pdfToExcelModal" tabindex="-1" aria-labelledby="pdfToExcelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="pdfToExcelForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Convert PDF to Excel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">  
          <div class="mb-3">
            <input type="file" class="form-control" name="pdf_file" accept=".pdf" required />
          </div>
          <div id="pdfToExcelLoading" style="display: none;">
            <div class="spinner-border text-primary" role="status"></div>
            <span class="ms-2">Converting PDF to Excel...</span>
          </div>
          <div id="pdfToExcelResult" style="display: none;">
            <div class="alert alert-success">
              Conversion successful! The file will appear in your list above.
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Convert to Excel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="./JS/toggle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Use the PHP-defined constant in JavaScript
const BACKEND_URL = "<?= BACKEND_URL ?>";

// PDF to Excel Conversion Form
const pdfToExcelForm = document.getElementById("pdfToExcelForm");
if (pdfToExcelForm) {
    pdfToExcelForm.addEventListener("submit", function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const loadingDiv = document.getElementById("pdfToExcelLoading");
        const resultDiv = document.getElementById("pdfToExcelResult");
        const currentUserId = document.getElementById("currentUserId").value;
        
        // Show loading, hide result
        loadingDiv.style.display = "block";
        resultDiv.style.display = "none";
        
        // Disable submit button
        submitButton.disabled = true;

        fetch(`${BACKEND_URL}/convert-pdf-to-excel`, {
            method: "POST",
            body: formData,
        })
        .then((res) => res.json())
        .then((data) => {
            if (data.error) {
                // Record failed conversion
                if (currentUserId) {
                    fetch(`${BACKEND_URL}/record-conversion`, {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            user_id: currentUserId,
                            conversion_type: 'pdf_to_excel',
                            original_filename: formData.get('pdf_file').name,
                            file_size: formData.get('pdf_file').size,
                            status: 'failed',
                            error_message: data.error,
                            conversion_id: data.conversion_id || null
                        })
                    });
                }
                
                throw new Error(data.error);
            }

            // Record successful conversion
            if (currentUserId) {
                fetch(`${BACKEND_URL}/record-conversion`, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: currentUserId,
                        conversion_type: 'pdf_to_excel',
                        original_filename: data.original_filename,
                        converted_filename: data.excel_filename,
                        file_size: formData.get('pdf_file').size,
                        status: 'completed',
                        conversion_id: data.conversion_id || null
                    })
                });
            }

            // Show success message
            loadingDiv.style.display = "none";
            resultDiv.style.display = "block";
            
            // Close modal after delay and reload
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('pdfToExcelModal')).hide();
                window.location.reload();
            }, 2000);
        })
        .catch((err) => {
            // Record failed conversion
            if (currentUserId) {
                fetch(`${BACKEND_URL}/record-conversion`, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: currentUserId,
                        conversion_type: 'pdf_to_excel',
                        original_filename: formData.get('pdf_file')?.name || 'unknown',
                        file_size: formData.get('pdf_file')?.size || 0,
                        status: 'failed',
                        error_message: err.message
                    })
                });
            }
            
            alert("Conversion failed: " + err.message);
        })
        .finally(() => {
            // Reset button state
            submitButton.disabled = false;
            loadingDiv.style.display = "none";
        });
    });
}
</script>
</body>
</html>