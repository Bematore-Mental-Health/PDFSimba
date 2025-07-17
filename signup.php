<?php
include './DashboardBackend/session.php';
include './DashboardBackend/db_connection.php';

$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $terms = isset($_POST['terms']) ? 1 : 0;

    if(empty($firstName) || empty($lastName) || empty($email) || empty($password) || !$terms){
        $errorMsg = "All fields are required and terms must be accepted.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0){
            $errorMsg = "Email is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

            if($stmt->execute()){
                // Redirect to login page after successful signup
                header("Location: login.php?signup=success");
                exit();
            } else {
                $errorMsg = "Something went wrong. Please try again.";
            }
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
  <title>PDFSimba | Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="./Media/book3.png" type="image/x-icon">
  <link rel="stylesheet" href="./CSS/signup.css">
</head>
<body>

  <div class="container-signup">
    <div class="left-panel">
      <div style="position: relative; z-index: 1;">
        <h2>Create your<br>Account</h2>
        <p class="mt-3">Convert, manage, and secure<br> your documents with ease!</p>
      </div>
    </div>

    <div class="right-panel">
      <h3>Sign Up</h3>

      <?php if($errorMsg): ?>
        <div class="alert alert-danger"><?= $errorMsg ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="row g-3">
          <div class="col-md-6">
            <input type="text" name="first_name" class="form-control" placeholder="First name" required>
          </div>
          <div class="col-md-6">
            <input type="text" name="last_name" class="form-control" placeholder="Last name" required>
          </div>
          <div class="col-12">
            <input type="email" name="email" class="form-control" placeholder="Email address" required>
          </div>
          <div class="col-12">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
          </div>
          <div class="col-12 d-flex align-items-center">
            <input class="form-check-input me-2" type="checkbox" name="terms" required>
            <label class="form-check-label">
              <a href="terms.php">Accept Terms & Conditions</a>
            </label>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-join">Join us →</button>
          </div>
          <div class="col-12 text-center">
            <p class="mt-3">Already have an account? <a href="./login.php">Log in</a></p>
          </div>
        </div>
      </form>
    </div>
  </div>

</body>
</html>
