<?php
include '../DashboardBackend/session.php';
include '../DashboardBackend/db_connection.php';

// Check super admin privileges
if (!isAdminLoggedIn() || $_SESSION['admin_id'] != 1) {
    header('Location: login.php'); // Redirect to admin login
    exit();
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new admin
    if (isset($_POST['create_admin'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        // Validate inputs
        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = "All fields are required";
        } else {
            // Check if email exists
            $checkStmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkStmt->store_result();
            
            if ($checkStmt->num_rows > 0) {
                $_SESSION['error'] = "Email already exists";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO admins (name, email, password_hash) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $hashedPassword);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Admin created successfully";
                } else {
                    $_SESSION['error'] = "Error creating admin: " . $stmt->error;
                }
                $stmt->close();
            }
            $checkStmt->close();
        }
        header("Location: admins.php");
        exit();
    }
    
    // Delete admin
    if (isset($_POST['delete_admin'])) {
        $adminId = $_POST['admin_id'];
        
        // Prevent deleting super admin (ID 1)
        if ($adminId == 1) {
            $_SESSION['error'] = "Cannot delete super admin";
        } else {
            $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->bind_param("i", $adminId);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Admin deleted successfully";
            } else {
                $_SESSION['error'] = "Error deleting admin: " . $stmt->error;
            }
            $stmt->close();
        }
        header("Location: admins.php");
        exit();
    }
    
    // Update admin
    if (isset($_POST['update_admin'])) {
        $adminId = $_POST['admin_id'];
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        // Check if email exists for another admin
        $checkStmt = $conn->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
        $checkStmt->bind_param("si", $email, $adminId);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $_SESSION['error'] = "Email already exists for another admin";
        } else {
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admins SET name = ?, email = ?, password_hash = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $email, $hashedPassword, $adminId);
            } else {
                $stmt = $conn->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $name, $email, $adminId);
            }
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "Admin updated successfully";
            } else {
                $_SESSION['error'] = "Error updating admin: " . $stmt->error;
            }
            $stmt->close();
        }
        $checkStmt->close();
        header("Location: admins.php");
        exit();
    }
}

// Get all admins
$admins = [];
$result = $conn->query("SELECT * FROM admins ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
    $result->free();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDFSimba | Admin Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="../Media/book3.png" type="image/x-icon">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <style>
        .table-container { margin: 20px; overflow-x: auto; }
        .super-admin-badge { background-color: #6f42c1; color: white; }
        .admin-badge { background-color: #17a2b8; color: white; }
        .action-btns { white-space: nowrap; }
    </style>
</head>
<body>

<?php include 'header-sidebar.php'; ?>

<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Admin Management</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdminModal">
            <i class="bi bi-plus-circle"></i> Create New Admin
        </button>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?= htmlspecialchars($admin['id']) ?></td>
                    <td><?= htmlspecialchars($admin['name']) ?></td>
                    <td><?= htmlspecialchars($admin['email']) ?></td>
                    <td>
                        <span class="badge <?= $admin['id'] == 1 ? 'super-admin-badge' : 'admin-badge' ?>">
                            <?= $admin['id'] == 1 ? 'Super Admin' : 'Admin' ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y H:i', strtotime($admin['created_at'])) ?></td>
                    <td class="action-btns">
                        <!-- View Button -->
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                data-bs-target="#viewAdminModal" 
                                data-admin-id="<?= $admin['id'] ?>"
                                data-name="<?= htmlspecialchars($admin['name']) ?>"
                                data-email="<?= htmlspecialchars($admin['email']) ?>"
                                data-created="<?= date('M d, Y H:i', strtotime($admin['created_at'])) ?>">
                            <i class="bi bi-eye"></i> View
                        </button>
                        
                        <!-- Edit Button -->
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                data-bs-target="#editAdminModal" 
                                data-admin-id="<?= $admin['id'] ?>"
                                data-name="<?= htmlspecialchars($admin['name']) ?>"
                                data-email="<?= htmlspecialchars($admin['email']) ?>">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        
                        <!-- Delete Button (hidden for super admin) -->
                        <?php if ($admin['id'] != 1): ?>
                            <form method="post" style="display: inline-block;">
                                <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                <button type="submit" name="delete_admin" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this admin?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Create Admin Modal -->
<div class="modal fade" id="createAdminModal" tabindex="-1" aria-labelledby="createAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Create New Admin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="createName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="createName" name="name" required>
          </div>
          <div class="mb-3">
            <label for="createEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="createEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="createPassword" class="form-label">Password</label>
            <input type="password" class="form-control" id="createPassword" name="password" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="create_admin" class="btn btn-primary">Create Admin</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Admin Modal -->
<div class="modal fade" id="viewAdminModal" tabindex="-1" aria-labelledby="viewAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Admin Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Name:</label>
            <p id="viewAdminName" class="form-control-static"></p>
        </div>
        <div class="mb-3">
            <label class="form-label">Email:</label>
            <p id="viewAdminEmail" class="form-control-static"></p>
        </div>
        <div class="mb-3">
            <label class="form-label">Created:</label>
            <p id="viewAdminCreated" class="form-control-static"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Edit Admin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="admin_id" id="editAdminId">
          <div class="mb-3">
            <label for="editAdminName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="editAdminName" name="name" required>
          </div>
          <div class="mb-3">
            <label for="editAdminEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editAdminEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="editAdminPassword" class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" class="form-control" id="editAdminPassword" name="password">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="update_admin" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="./JS/toggle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// View Admin Modal
document.getElementById('viewAdminModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const modal = this;
    
    modal.querySelector('#viewAdminName').textContent = button.getAttribute('data-name');
    modal.querySelector('#viewAdminEmail').textContent = button.getAttribute('data-email');
    modal.querySelector('#viewAdminCreated').textContent = button.getAttribute('data-created');
});

// Edit Admin Modal
document.getElementById('editAdminModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const modal = this;
    
    modal.querySelector('#editAdminId').value = button.getAttribute('data-admin-id');
    modal.querySelector('#editAdminName').value = button.getAttribute('data-name');
    modal.querySelector('#editAdminEmail').value = button.getAttribute('data-email');
});
</script>
</body>
</html>