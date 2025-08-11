<?php
include '../DashboardBackend/session.php';

if (!isUserLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

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

    // Get documents from last 48 hours with PDF editing conversions
    $fortyEightHoursAgo = date('Y-m-d H:i:s', strtotime('-48 hours'));
    $stmt = $db->prepare('SELECT * FROM conversions 
                         WHERE user_id = :user_id 
                         AND (conversion_type = "pdf_edit_upload" OR conversion_type = "pdf_edit_save")
                         AND status != "failed" 
                         AND timestamp > :fortyEightHoursAgo 
                         ORDER BY timestamp DESC');
    $stmt->bindValue(':user_id', $user_id, SQLITE3_TEXT);
    $stmt->bindValue(':fortyEightHoursAgo', $fortyEightHoursAgo, SQLITE3_TEXT);
    
    $result = $stmt->execute();
    $documents = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        // Only include if we have a valid filename
        if (!empty($row['converted_filename']) || !empty($row['original_filename'])) {
            $documents[] = [
                'id' => $row['id'],
                'conversion_id' => $row['conversion_id'],
                'original_filename' => $row['original_filename'],
                'converted_filename' => $row['converted_filename'],
                'status' => $row['status'],
                'timestamp' => $row['timestamp'],
                'conversion_type' => $row['conversion_type']
            ];
        }
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

$backend_url = "http://localhost:5001";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDFSimba | PDF Editing</title>
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
            color: #d63384;
        }
        .document-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .badge.completed { background-color: #28a745; }
        .badge.failed { background-color: #dc3545; }
        .badge.uploaded { background-color: #17a2b8; }
        .badge.ready_for_edit { background-color: #ffc107; color: #000; }
        .expiry-notice {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .action-buttons {
            margin-top: 10px;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

<?php include 'header-sidebar.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>PDF Documents for Editing</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editPdfModal">
            <i class="bi bi-upload"></i> Upload PDF for Editing
        </button>
    </div>

    <div class="alert alert-warning expiry-notice mb-4">
        <i class="bi bi-clock-history"></i> Note: Documents are automatically deleted after 48 hours
    </div>

   <?php if (empty($documents)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No PDF documents available for editing.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php foreach ($documents as $doc): 
                // Determine which file to use for each action
                $editFile = ($doc['conversion_type'] === 'pdf_edit_upload') ? $doc['converted_filename'] : $doc['original_filename'];
                $downloadFile = ($doc['conversion_type'] === 'pdf_edit_save') ? $doc['converted_filename'] : $doc['original_filename'];
                
                // Generate display name
                $displayName = ($doc['conversion_type'] === 'pdf_edit_save') ? 
                    "Edited_" . $doc['original_filename'] : 
                    $doc['original_filename'];
            ?>
                <div class="col">
                    <div class="card document-card">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-pdf-fill document-icon mb-3"></i>
                            <h5 class="card-title document-name" title="<?= htmlspecialchars($displayName) ?>">
                                <?= htmlspecialchars($displayName) ?>
                            </h5>
                            <p class="card-text text-muted small">
                                <?= date('M d, Y H:i', strtotime($doc['timestamp'])) ?>
                            </p>
                            <span class="badge <?= str_replace(' ', '_', $doc['status']) ?> mb-2">
                                <?= ucfirst(str_replace('_', ' ', $doc['status'])) ?>
                            </span>
                            <div class="d-grid gap-2 action-buttons">
                                <a href="<?= $backend_url ?>/download-pdf-file/<?= urlencode($downloadFile) ?>" 
                                   class="btn btn-sm btn-success">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <?php if ($doc['status'] === 'ready_for_edit' || $doc['status'] === 'uploaded'): ?>
                                    <a href="<?= $backend_url ?>/pdf-editor-view?file=<?= urlencode($editFile) ?>&conversion_id=<?= $doc['conversion_id'] ?>" 
                                       class="btn btn-sm btn-primary" target="_blank">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- PDF Editing Modal -->
<div class="modal fade" id="editPdfModal" tabindex="-1" aria-labelledby="editPdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="uploadEditPdfForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Upload PDF for Editing</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="currentUserId" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">  
          <div class="mb-3">
            <input type="file" class="form-control" name="pdfFile" accept=".pdf" required />
          </div>
          <div id="editPdfLoading" style="display: none;">
            <div class="spinner-border text-primary" role="status"></div>
            <span class="ms-2">Preparing PDF for editing...</span>
          </div>
          <div id="editPdfResult" style="display: none;">
            <div class="alert alert-success">
              PDF ready for editing! The editor will open in a new tab.
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Upload & Edit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="./JS/toggle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const BACKEND_URL = "http://localhost:5001";

// PDF Editing Form
document.getElementById("uploadEditPdfForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const loadingDiv = document.getElementById("editPdfLoading");
    const resultDiv = document.getElementById("editPdfResult");
    const currentUserId = document.getElementById("currentUserId").value;
    
    loadingDiv.style.display = "block";
    resultDiv.style.display = "none";
    submitButton.disabled = true;

    fetch(`${BACKEND_URL}/upload-edit-pdf`, {
        method: "POST",
        body: formData,
    })
    .then((res) => res.json())
    .then((data) => {
        if (data.error) {
            throw new Error(data.error);
        }

        if (!data.pdfFileName) {
            throw new Error("Server didn't return a valid filename");
        }

        // Open editor with the returned filename and conversion ID
        const editorUrl = `${BACKEND_URL}/pdf-editor-view?file=${encodeURIComponent(data.pdfFileName)}&conversion_id=${data.conversionId || ''}`;
        window.open(editorUrl, "_blank");
        
        loadingDiv.style.display = "none";
        resultDiv.style.display = "block";
        
        setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById('editPdfModal')).hide();
            window.location.reload();
        }, 2000);
    })
    .catch((err) => {
        alert("Failed to upload PDF: " + err.message);
    })
    .finally(() => {
        submitButton.disabled = false;
        loadingDiv.style.display = "none";
    });
});
</script>
</body>
</html>