<?php
include '../DashboardBackend/session.php';
include '../DashboardBackend/db_connection.php';

// Redirect to login page if session is not set
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get recent conversions (from SQLite)
$conversions = [];
try {
    $dbPath = __DIR__ . '../../Backend/conversions.db';
    if (file_exists($dbPath)) {
        $sqlite = new SQLite3($dbPath);
        $result = $sqlite->query('SELECT * FROM conversions ORDER BY timestamp DESC LIMIT 10');
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $conversions[] = $row;
        }
    }
} catch (Exception $e) {
    $conversionError = "Error loading conversions: " . $e->getMessage();
}

// Get recent users (from MySQL)
$users = [];
$userQuery = $conn->query("SELECT id, first_name, last_name, email, created_at FROM users ORDER BY created_at DESC LIMIT 10");
if ($userQuery) {
    while ($row = $userQuery->fetch_assoc()) {
        $users[] = $row;
    }
    $userQuery->free();
}

// Get all admins (from MySQL)
$admins = [];
$adminQuery = $conn->query("SELECT id, name, email, created_at FROM admins ORDER BY created_at DESC LIMIT 10");
if ($adminQuery) {
    while ($row = $adminQuery->fetch_assoc()) {
        $admins[] = $row;
    }
    $adminQuery->free();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PDFSimba | Admin Dashboard</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
  <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.worker.min.js';
  </script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="shortcut icon" href="../Media/book3.png" type="image/x-icon">
  <link rel="stylesheet" href="../CSS/dashboard.css">
  <style>
    .dashboard-card {
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      overflow: hidden;
    }
    .card-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #eee;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .card-title {
      margin: 0;
      font-size: 1.2rem;
      font-weight: 600;
    }
    .view-all-link {
      font-size: 0.9rem;
    }
    .table-responsive {
      overflow-x: auto;
    }
    .table {
      margin-bottom: 0;
    }
    .table th {
      white-space: nowrap;
      font-weight: 600;
      background-color: #f8f9fa;
    }
    .status-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .badge-completed { background-color: #d4edda; color: #155724; }
    .badge-failed { background-color: #f8d7da; color: #721c24; }
    .badge-pending { background-color: #fff3cd; color: #856404; }
    .badge-processing { background-color: #cce5ff; color: #004085; }

    /* Improved responsive table styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }
    
    /* Table optimizations for small screens */
    @media (max-width: 768px) {
        .dashboard-card {
            margin-bottom: 20px;
            padding: 0;
        }
        
        .table {
            min-width: 600px; /* Forces horizontal scroll on small screens */
        }
        
        .table th, .table td {
            padding: 8px 6px;
            font-size: 0.85rem;
            white-space: nowrap;
        }
        
        .card-title {
            font-size: 1.1rem;
        }
        
        /* Compact status badges */
        .status-badge {
            padding: 3px 6px;
            font-size: 0.7rem;
        }
        
        /* Hide less important columns on very small screens */
        @media (max-width: 480px) {
            .conversions-table th:nth-child(2),
            .conversions-table td:nth-child(2),
            .conversions-table th:nth-child(6),
            .conversions-table td:nth-child(6) {
                display: none;
            }
            
            .users-table th:nth-child(4),
            .users-table td:nth-child(4) {
                display: none;
            }
        }
    }

    /* Better table header on all screens */
    .table thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
    }
  </style>
</head>
<body>

<?php include 'header-sidebar.php'; ?>

<div class="container mt-4">
  <!-- Conversions Card -->
  <div class="dashboard-card">
    <div class="card-header">
      <h3 class="card-title">Recent Main Page Conversions</h3>
      <a href="mainpageconv.php" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
    </div>
<div class="table-responsive">
    <table class="table conversions-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Document</th>
            <th>Type</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($conversions as $conv): ?>
            <tr>
              <td><?= htmlspecialchars($conv['id'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars(substr($conv['user_id'] ?? 'Unknown', 0, 8)) ?></td>
              <td>
                <?= htmlspecialchars(substr($conv['original_filename'] ?? 'No file', 0, 20)) ?>
                <?php if (!empty($conv['converted_filename'])): ?>
                  <br><small>→ <?= htmlspecialchars(substr($conv['converted_filename'], 0, 20)) ?></small>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($conv['conversion_type'] ?? 'N/A') ?></td>
              <td>
                <span class="status-badge badge-<?= strtolower($conv['status'] ?? '') ?>">
                  <?= htmlspecialchars($conv['status'] ?? 'Unknown') ?>
                </span>
              </td>
              <td><?= date('M d, H:i', strtotime($conv['timestamp'])) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($conversions)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted py-3">No recent conversions found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Users Card -->
  <div class="dashboard-card">
    <div class="card-header">
      <h3 class="card-title">Recent Users</h3>
      <a href="users.php" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
    </div>
   <div class="table-responsive">
    <table class="table users-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= htmlspecialchars($user['id']) ?></td>
              <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
              <td><?= htmlspecialchars(substr($user['email'], 0, 20)) ?></td>
              <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="4" class="text-center text-muted py-3">No users found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Admins Card -->
  <div class="dashboard-card">
    <div class="card-header">
      <h3 class="card-title">Admin Users</h3>
      <a href="admins.php" class="view-all-link">View All <i class="bi bi-arrow-right"></i></a>
    </div>
 <div class="table-responsive">
    <table class="table admins-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($admins as $admin): ?>
            <tr>
              <td><?= htmlspecialchars($admin['id']) ?></td>
              <td><?= htmlspecialchars($admin['name']) ?></td>
              <td><?= htmlspecialchars($admin['email']) ?></td>
              <td><?= ($admin['id'] == 1) ? 'Super Admin' : 'Admin' ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($admins)): ?>
            <tr>
              <td colspan="4" class="text-center text-muted py-3">No admins found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="./JS/toggle.js"></script>
</body>
</html>