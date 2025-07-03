<?php
include './DashboardBackend/session.php';
include './DashboardBackend/db_connection.php';

$errorMsg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errorMsg = "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Successful login → store session
                setUserSession($user['id'], $user['first_name'], $user['last_name'], $user['email']);

                // Redirect to dashboard or homepage
                header("Location: ./Dashboard/index.php");
                exit();
            } else {
                $errorMsg = "Incorrect password.";
            }
        } else {
            $errorMsg = "No account found with that email.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PDFSimba | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="./Media/book3.png" type="image/x-icon">
  <link rel="stylesheet" href="./CSS/signup.css">
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

      <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?= $errorMsg ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="row g-3">

          <div class="col-12">
            <input type="email" name="email" class="form-control" placeholder="Email address" required>
          </div>
          <div class="col-12">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
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

</body>
</html>
