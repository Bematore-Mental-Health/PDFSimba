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
    <!-- Left Panel -->
    <div class="left-panel">
  <div style="position: relative; z-index: 1;">
    <h2>Create your<br>Account</h2>
    <p class="mt-3">Convert, manage, and secure<br> your documents with ease!</p>
  </div>
</div>


    <!-- Right Panel -->
    <div class="right-panel">
      <h3>Sign Up</h3>
      <form>
        <div class="row g-3">
          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="First name" required>
          </div>
          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Last name" required>
          </div>
          <div class="col-12">
            <input type="email" class="form-control" placeholder="Email address" required>
          </div>
          <div class="col-12">
            <input type="password" class="form-control" placeholder="Password" required>
          </div>
          <div class="col-12 d-flex align-items-center">
            <input class="form-check-input me-2" type="checkbox" required>
            <label class="form-check-label">
              <a href="#">Accept Terms & Conditions</a>
            </label>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-join">Join us →</button>
          </div>
          <div class="col-12 text-center">
            <p class="mt-3">Already have an account? <a href="./login.php">Log in</a></p>
        </div>
      </form>
    </div>
  </div>

</body>
</html>
