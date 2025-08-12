<?php
include '../DashboardBackend/session.php';
include '../DashboardBackend/db_connection.php';

// Redirect to login page if session is not set
if (!isUserLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Initialize variables
$successMsg = '';
$errorMsg = '';
$userDetails = [];

// Get current user details including password hash
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $userDetails = $result->fetch_assoc();
} else {
    $errorMsg = "User not found.";
}
$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    
    // Basic validation
    if (empty($firstName) || empty($lastName) || empty($email)) {
        $errorMsg = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } else {
        // Check if email is already taken by another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errorMsg = "Email is already registered by another user.";
        } else {
            // Verify current password if trying to change password
            if (!empty($newPassword)) {
                if (!password_verify($currentPassword, $userDetails['password'])) {
                    $errorMsg = "Current password is incorrect.";
                }
            }
            
            // Proceed with update if no errors
            if (empty($errorMsg)) {
                // Prepare update query
                if (!empty($newPassword)) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $firstName, $lastName, $email, $hashedPassword, $userId);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $firstName, $lastName, $email, $userId);
                }
                
                if ($stmt->execute()) {
                    $successMsg = "Profile updated successfully!";
                    // Update session variables
                    $_SESSION['first_name'] = $firstName;
                    $_SESSION['last_name'] = $lastName;
                    $_SESSION['email'] = $email;
                    // Refresh user details
                    $userDetails['first_name'] = $firstName;
                    $userDetails['last_name'] = $lastName;
                    $userDetails['email'] = $email;
                    
                    // If password changed, redirect to login after 3 seconds
                    if (!empty($newPassword)) {
                        $successMsg .= " Please login again with your new password.";
                        echo '<meta http-equiv="refresh" content="3;url=../login.php?password_changed=1">';
                    }
                } else {
                    $errorMsg = "Error updating profile. Please try again.";
                }
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDFSimba | My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="shortcut icon" href="../Media/book3.png" type="image/x-icon">
  <link rel="stylesheet" href="../CSS/dashboard.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #555;
            margin-right: 20px;
        }
        .profile-detail {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
        }
        .detail-value {
            font-size: 18px;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        .password-field {
            position: relative;
        }
    </style>
</head>
<body>

    <?php 
include 'header-sidebar.php';
?>
    <div class="container">
        <div class="profile-container">
            <div class="d-flex align-items-center mb-4">
                <div class="profile-avatar">
                    <?php 
                        $initials = substr($userDetails['first_name'], 0, 1) . substr($userDetails['last_name'], 0, 1);
                        echo strtoupper($initials);
                    ?>
                </div>
                <div>
                    <h2>My Profile</h2>
                    <p class="text-muted mb-0">Member since <?php echo date('F Y', strtotime($_SESSION['created_at'] ?? 'now')); ?></p>
                </div>
            </div>

            <?php if ($successMsg): ?>
                <div class="alert alert-success"><?php echo $successMsg; ?></div>
            <?php endif; ?>
            
            <?php if ($errorMsg): ?>
                <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
            <?php endif; ?>

            <div class="profile-details">
                <div class="profile-detail">
                    <div class="detail-label">First Name</div>
                    <div class="detail-value"><?php echo htmlspecialchars($userDetails['first_name']); ?></div>
                </div>
                <div class="profile-detail">
                    <div class="detail-label">Last Name</div>
                    <div class="detail-value"><?php echo htmlspecialchars($userDetails['last_name']); ?></div>
                </div>
                <div class="profile-detail">
                    <div class="detail-label">Email Address</div>
                    <div class="detail-value"><?php echo htmlspecialchars($userDetails['email']); ?></div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    Edit Profile
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?php echo htmlspecialchars($userDetails['first_name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?php echo htmlspecialchars($userDetails['last_name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($userDetails['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3 password-field">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <span class="password-toggle" onclick="togglePassword('current_password')">👁️</span>
                            <div class="form-text">Required to verify your identity</div>
                        </div>
                        
                        <div class="mb-3 password-field">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <span class="password-toggle" onclick="togglePassword('new_password')">👁️</span>
                            <div class="form-text">Leave blank to keep current password</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(id) {
            const input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
            } else {
                input.type = "password";
            }
        }

        // Show modal if there was an error during submission
        <?php if ($errorMsg && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
            document.addEventListener('DOMContentLoaded', function() {
                var editModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
                editModal.show();
            });
        <?php endif; ?>
    </script>
</body>
</html>