<?php
include '../DashboardBackend/session.php';
include '../DashboardBackend/db_connection.php';

// Check admin privileges (add your own admin check logic)
if (!isAdminLoggedIn()) {
    header('Location: login.php'); // Redirect to admin login
    exit();
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete user
    if (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $_SESSION['message'] = "User deleted successfully";
        } else {
            $_SESSION['error'] = "Error deleting user: " . $stmt->error;
        }
        $stmt->close();
        header("Location: users.php");
        exit();
    }
    
    // Update user
    if (isset($_POST['update_user'])) {
        $userId = $_POST['user_id'];
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $isVerified = isset($_POST['is_verified']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, is_verified = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sssii", $firstName, $lastName, $email, $isVerified, $userId);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "User updated successfully";
        } else {
            $_SESSION['error'] = "Error updating user: " . $stmt->error;
        }
        $stmt->close();
        header("Location: users.php");
        exit();
    }
}

// Get all users
$users = [];
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDFSimba | User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="../Media/book3.png" type="image/x-icon">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <style>
        .table-container { margin: 20px; overflow-x: auto; }
        .verified-badge { background-color: #d4edda; color: #155724; }
        .unverified-badge { background-color: #f8d7da; color: #721c24; }
        .action-btns { white-space: nowrap; }
    </style>
</head>
<body>

<?php include 'header-sidebar.php'; ?>

<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>User Management</h2>
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
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td>
                        <?= htmlspecialchars($user['first_name']) ?> 
                        <?= htmlspecialchars($user['last_name']) ?>
                    </td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <span class="badge <?= $user['is_verified'] ? 'verified-badge' : 'unverified-badge' ?>">
                            <?= $user['is_verified'] ? 'Verified' : 'Unverified' ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y H:i', strtotime($user['created_at'])) ?></td>
                    <td class="action-btns">
                        <!-- View Button -->
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                data-bs-target="#viewUserModal" 
                                data-user-id="<?= $user['id'] ?>"
                                data-first-name="<?= htmlspecialchars($user['first_name']) ?>"
                                data-last-name="<?= htmlspecialchars($user['last_name']) ?>"
                                data-email="<?= htmlspecialchars($user['email']) ?>"
                                data-verified="<?= $user['is_verified'] ?>"
                                data-created="<?= date('M d, Y H:i', strtotime($user['created_at'])) ?>">
                            <i class="bi bi-eye"></i> View
                        </button>
                        
                        <!-- Edit Button -->
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                data-bs-target="#editUserModal" 
                                data-user-id="<?= $user['id'] ?>"
                                data-first-name="<?= htmlspecialchars($user['first_name']) ?>"
                                data-last-name="<?= htmlspecialchars($user['last_name']) ?>"
                                data-email="<?= htmlspecialchars($user['email']) ?>"
                                data-verified="<?= $user['is_verified'] ?>">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        
                        <!-- Delete Button -->
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <button type="submit" name="delete_user" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">User Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Name:</label>
            <p id="viewUserName" class="form-control-static"></p>
        </div>
        <div class="mb-3">
            <label class="form-label">Email:</label>
            <p id="viewUserEmail" class="form-control-static"></p>
        </div>
        <div class="mb-3">
            <label class="form-label">Status:</label>
            <p id="viewUserStatus" class="form-control-static"></p>
        </div>
        <div class="mb-3">
            <label class="form-label">Created:</label>
            <p id="viewUserCreated" class="form-control-static"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="editUserId">
          <div class="mb-3">
            <label for="editFirstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
          </div>
          <div class="mb-3">
            <label for="editLastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="editLastName" name="last_name" required>
          </div>
          <div class="mb-3">
            <label for="editEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editEmail" name="email" required>
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="editIsVerified" name="is_verified">
            <label class="form-check-label" for="editIsVerified">Verified</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="update_user" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// View User Modal
document.getElementById('viewUserModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const modal = this;
    
    modal.querySelector('#viewUserName').textContent = 
        button.getAttribute('data-first-name') + ' ' + button.getAttribute('data-last-name');
    modal.querySelector('#viewUserEmail').textContent = button.getAttribute('data-email');
    modal.querySelector('#viewUserStatus').textContent = 
        button.getAttribute('data-verified') === '1' ? 'Verified' : 'Unverified';
    modal.querySelector('#viewUserCreated').textContent = button.getAttribute('data-created');
});

// Edit User Modal
document.getElementById('editUserModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const modal = this;
    
    modal.querySelector('#editUserId').value = button.getAttribute('data-user-id');
    modal.querySelector('#editFirstName').value = button.getAttribute('data-first-name');
    modal.querySelector('#editLastName').value = button.getAttribute('data-last-name');
    modal.querySelector('#editEmail').value = button.getAttribute('data-email');
    modal.querySelector('#editIsVerified').checked = button.getAttribute('data-verified') === '1';
});
</script>
</body>
</html>