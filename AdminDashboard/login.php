<?php
include '../DashboardBackend/db_connection.php';
include '../DashboardBackend/session.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // Prepare SQL to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, email, password_hash FROM admins WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if admin exists
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Verify password
          // After successful admin authentication
if (password_verify($password, $admin['password_hash'])) {
    // Set admin session (remove user session setting)
    setAdminSession($admin['id'], $admin['name'], $admin['email']);
    
    // Redirect to admin dashboard
    header("Location: index.php");
    exit();
} else {
    $error = "Invalid email or password";
}
        } else {
            $error = "Invalid email or password";
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PDFSimba | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../Media/book3.png" type="image/x-icon">
  <link rel="stylesheet" href="../CSS/signup.css">
</head>
<body>

  <div class="container-signup">
    <div class="left-panel">
      <div style="position: relative; z-index: 1;">
        <h2>Login Into Your<br>Account</h2>
        <p class="mt-3">Convert, manage, and secure<br> your documents with ease!</p>
      </div>
    </div>

    <div class="right-panel">
      <h3>PDFSimba Login</h3>
      
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="row g-3">
          <div class="col-12">
            <input type="email" name="email" class="form-control" placeholder="Email address" required>
          </div>
          <div class="col-12">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
          </div>
          <div class="col-12 text-end">
            <span class="reset-link" onclick="showResetModal()">Forgot password?</span>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-join">Log In →</button>
          </div>
          <div class="col-12 text-center">
            <p class="mt-3">Don't have an account? <a href="./signup.php">Sign up</a></p>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>