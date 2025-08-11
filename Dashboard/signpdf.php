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

    // Get documents from last 48 hours that are either pending or uploaded
    $fortyEightHoursAgo = date('Y-m-d H:i:s', strtotime('-48 hours'));
    $stmt = $db->prepare('SELECT * FROM conversions 
                         WHERE user_id = :user_id 
                         AND (conversion_type = "document_signing" OR conversion_type = "document_signing_upload")
                         AND status IN ("uploaded", "pending", "processing")
                         AND timestamp > :fortyEightHoursAgo 
                         ORDER BY timestamp DESC');
    $stmt->bindValue(':user_id', $user_id, SQLITE3_TEXT);
    $stmt->bindValue(':fortyEightHoursAgo', $fortyEightHoursAgo, SQLITE3_TEXT);
    
    $result = $stmt->execute();
    $documents = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        // Only include if we have either converted_filename or original_filename
        if (!empty($row['converted_filename']) || !empty($row['original_filename'])) {
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
    <title>PDFSimba | Documents for Signing</title>
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
            color: #d63031;
        }
        .document-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .badge.completed { background-color: #28a745; }
        .badge.failed { background-color: #dc3545; }
        .badge.uploaded { background-color: #17a2b8; }
        .badge.pending { background-color: #ffc107; color: #000; }
        .badge.processing { background-color: #007bff; }
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
        <h2>Documents for Signing</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadSigningDocumentModal">
            <i class="bi bi-upload"></i> Upload New Document
        </button>
    </div>

    <div class="alert alert-warning expiry-notice mb-4">
        <i class="bi bi-clock-history"></i> Note: Documents are automatically deleted after 48 hours
    </div>

    <?php if (empty($documents)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No documents available for signing.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php foreach ($documents as $doc): 
                // Determine which file to use for signing
                $documentFile = !empty($doc['converted_filename']) ? $doc['converted_filename'] : $doc['original_filename'];
                $statusClass = strtolower($doc['status']);
            ?>
                <div class="col">
                    <div class="card document-card">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-pdf-fill document-icon mb-3"></i>
                            <h5 class="card-title document-name" title="<?= htmlspecialchars($doc['original_filename']) ?>">
                                <?= htmlspecialchars($doc['original_filename']) ?>
                            </h5>
                            <p class="card-text text-muted small">
                                <?= date('M d, Y H:i', strtotime($doc['timestamp'])) ?>
                            </p>
                            <span class="badge <?= $statusClass ?> mb-2">
                                <?= ucfirst($doc['status']) ?>
                            </span>
                            <div class="d-grid gap-2">
                                <button class="btn btn-sm btn-outline-primary sign-document-btn" 
                                        data-document-id="<?= htmlspecialchars($documentFile) ?>"
                                        data-conversion-id="<?= htmlspecialchars($doc['conversion_id']) ?>">
                                    <i class="bi bi-pen"></i> Sign Document
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadSigningDocumentModal" tabindex="-1" aria-labelledby="uploadSigningDocumentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="signingDocumentForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Upload Document for Signing</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="currentUserId" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">  
          <div class="mb-3">
            <input type="file" class="form-control" name="document_file" accept=".pdf, .doc, .docx" required />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Open for Signing</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="./JS/toggle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Update this to point to your backend server
const BACKEND_URL = "http://localhost:5001";

const signingDocumentForm = document.getElementById("signingDocumentForm");
if (signingDocumentForm) {
    signingDocumentForm.addEventListener("submit", function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const currentUserId = document.getElementById("currentUserId").value;
        
        // Store original button text
        const originalText = submitButton.textContent;
        
        // Change button state
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        fetch(`${BACKEND_URL}/upload-signing-document`, {
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
                            conversion_type: 'document_signing',
                            original_filename: formData.get('document_file').name,
                            file_size: formData.get('document_file').size,
                            status: 'failed',
                            error_message: data.error
                        })
                    });
                }
                
                alert("Error: " + data.error);
                return;
            }

            // Open the signing editor in a new tab with user_id and conversion_id
            const signingEditorUrl = `${BACKEND_URL}/sign-document/${data.document_id}?user_id=${currentUserId}&conversion_id=${data.conversion_id || ''}`;
            window.open(signingEditorUrl, "_blank");
            
            // Close the modal and reload the page to show new document
            bootstrap.Modal.getInstance(document.getElementById('uploadSigningDocumentModal')).hide();
            setTimeout(() => window.location.reload(), 1000);
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
                        conversion_type: 'document_signing',
                        original_filename: formData.get('document_file')?.name || 'unknown',
                        file_size: formData.get('document_file')?.size || 0,
                        status: 'failed',
                        error_message: err.message
                    })
                });
            }
            
            alert("Failed to upload document: " + err.message);
        })
        .finally(() => {
            // Reset button state
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    });
}

// Handle sign document buttons
document.querySelectorAll('.sign-document-btn').forEach(button => {
    button.addEventListener('click', function() {
        const documentId = this.getAttribute('data-document-id');
        const conversionId = this.getAttribute('data-conversion-id');
        const userId = document.getElementById('currentUserId').value;
        
        // Open the signing editor in a new tab
        const signingEditorUrl = `${BACKEND_URL}/sign-document/${documentId}?user_id=${userId}&conversion_id=${conversionId || ''}`;
        window.open(signingEditorUrl, "_blank");
    });
});
</script>
</body>
</html>