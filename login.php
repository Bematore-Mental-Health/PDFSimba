<?php
include './DashboardBackend/session.php';
include './DashboardBackend/db_connection.php';

$errorMsg = "";
$showVerificationModal = false;
$verificationError = "";

// SMTP Configuration
$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;
$smtpUsername = 'systempdfsimba@gmail.com';
$smtpPassword = 'srwvpiti ksogqlnv';
$fromEmail = 'systempdfsimba@gmail.com';
$fromName = 'PDFSimba';

function sendEmail($toEmail, $subject, $body) {
    global $smtpHost, $smtpPort, $smtpUsername, $smtpPassword, $fromEmail, $fromName;
    
    // Create email headers
    $headers = [
        'From' => "$fromName <$fromEmail>",
        'To' => $toEmail,
        'Subject' => $subject,
        'Date' => date('r'),
        'MIME-Version' => '1.0',
        'Content-type' => 'text/plain; charset=utf-8'
    ];
    
    // Format headers
    $headersString = '';
    foreach ($headers as $key => $value) {
        $headersString .= "$key: $value\r\n";
    }
    
    // Open socket connection
    $socket = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 30);
    if (!$socket) {
        error_log("SMTP connection failed: $errstr ($errno)");
        return false;
    }
    
    // SMTP Conversation
    $welcome = fread($socket, 4096);
    fwrite($socket, "EHLO localhost\r\n");
    $ehloResponse = fread($socket, 4096);
    
    fwrite($socket, "STARTTLS\r\n");
    $starttlsResponse = fread($socket, 4096);
    
    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
        fclose($socket);
        error_log("TLS handshake failed");
        return false;
    }
    
    fwrite($socket, "EHLO localhost\r\n");
    fread($socket, 4096);
    
    fwrite($socket, "AUTH LOGIN\r\n");
    fread($socket, 4096);
    
    fwrite($socket, base64_encode($smtpUsername) . "\r\n");
    fread($socket, 4096);
    
    fwrite($socket, base64_encode($smtpPassword) . "\r\n");
    $authResponse = fread($socket, 4096);
    
    if (strpos($authResponse, '235') === false) {
        fclose($socket);
        error_log("SMTP authentication failed");
        return false;
    }
    
    fwrite($socket, "MAIL FROM: <$fromEmail>\r\n");
    fread($socket, 4096);
    
    fwrite($socket, "RCPT TO: <$toEmail>\r\n");
    fread($socket, 4096);
    
    fwrite($socket, "DATA\r\n");
    fread($socket, 4096);
    
    fwrite($socket, "$headersString\r\n$body\r\n.\r\n");
    $sendResponse = fread($socket, 4096);
    
    fwrite($socket, "QUIT\r\n");
    fclose($socket);
    
    return strpos($sendResponse, '250') !== false;
}

function detectSuspiciousLogin($userId) {
    global $conn;
    
    // Check for multiple failed attempts only
    $failedStmt = $conn->prepare("SELECT COUNT(*) FROM failed_logins WHERE email = (SELECT email FROM users WHERE id = ?) AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $failedStmt->bind_param("i", $userId);
    $failedStmt->execute();
    $failedStmt->bind_result($failedCount);
    $failedStmt->fetch();
    $failedStmt->close();
    
    return ($failedCount >= 3);
}

// For modal requests
if (isset($_GET['modal'])) {
    // Handle AJAX login for modal
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
            echo '<div class="alert alert-danger mb-3">Please fill in both fields</div>';
            exit();
        }
        
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, temp_password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows !== 1) {
            // Log failed attempt
            $failStmt = $conn->prepare("INSERT INTO failed_logins (email, ip_address) VALUES (?, ?)");
            $failStmt->bind_param("ss", $email, $_SERVER['REMOTE_ADDR']);
            $failStmt->execute();
            $failStmt->close();
            
            echo '<div class="alert alert-danger mb-3">Invalid email or password</div>';
            exit();
        }
        
        $user = $result->fetch_assoc();
        
        if (!password_verify($password, $user['password'])) {
            // Log failed attempt
            $failStmt = $conn->prepare("INSERT INTO failed_logins (email, ip_address) VALUES (?, ?)");
            $failStmt->bind_param("ss", $email, $_SERVER['REMOTE_ADDR']);
            $failStmt->execute();
            $failStmt->close();
            
            echo '<div class="alert alert-danger mb-3">Invalid email or password</div>';
            exit();
        }
        
        // Check for suspicious login
        if (detectSuspiciousLogin($user['id'])) {
            // Generate verification code
            $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiryTime = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store verification code
            $verifyStmt = $conn->prepare("UPDATE users SET verification_code = ?, verification_code_expiry = ? WHERE id = ?");
            $verifyStmt->bind_param("ssi", $verificationCode, $expiryTime, $user['id']);
            $verifyStmt->execute();
            $verifyStmt->close();
            
            // Send verification email
            $subject = "PDFSimba - Login Verification Code";
            $body = "Hello {$user['first_name']},\n\n";
            $body .= "We detected multiple failed login attempts for your account.\n\n";
            $body .= "Your verification code is: $verificationCode\n\n";
            $body .= "This code will expire in 1 hour.\n\n";
            $body .= "If you didn't attempt to login, please contact support immediately.\n\n";
            $body .= "Thank you,\nThe PDFSimba Team";
            
            if (sendEmail($email, $subject, $body)) {
                $_SESSION['verify_email'] = $email;
                echo '<div class="alert alert-info mb-3">Verification code sent to your email</div>';
                exit();
            } else {
                echo '<div class="alert alert-danger mb-3">Failed to send verification email. Please try again.</div>';
                exit();
            }
        }
        
        // Check if this is a temporary password
        if ($user['temp_password'] == 1) {
            $_SESSION['temp_password_warning'] = true;
        }
        
        // Login successful
        setUserSession($user['id'], $user['first_name'], $user['last_name'], $user['email']);
        
        // Log this login
        $ip = $_SERVER['REMOTE_ADDR'];
        $logStmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address) VALUES (?, ?)");
        $logStmt->bind_param("is", $user['id'], $ip);
        $logStmt->execute();
        $logStmt->close();
        
        // Return success message
        echo '<script>window.parent.postMessage("login_success", window.location.origin);</script>';
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle password reset request
    if (isset($_POST['request_reset'])) {
        $email = trim($_POST['reset_email']);
        
        if (empty($email)) {
            header("Location: login.php?reset_error=Please enter your email address.");
            exit();
        } else {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id, first_name FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $tempPassword = bin2hex(random_bytes(4)); // 8-character temporary password
                $hashedTempPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
                
                // Update user with temporary password
                $updateStmt = $conn->prepare("UPDATE users SET password = ?, temp_password = 1 WHERE id = ?");
                $updateStmt->bind_param("si", $hashedTempPassword, $user['id']);
                
                if ($updateStmt->execute()) {
                    // Send email with temporary password
                    $subject = "PDFSimba - Your Temporary Password";
                    $body = "Hello {$user['first_name']},\n\n";
                    $body .= "Your temporary password is: $tempPassword\n\n";
                    $body .= "Please use this to log in, then immediately go to your profile to set a new permanent password.\n\n";
                    $body .= "If you didn't request this password reset, please contact support immediately.\n\n";
                    $body .= "Thank you,\nThe PDFSimba Team";
                    
                    // Try sending email multiple times if needed
                    $maxAttempts = 3;
                    $emailSent = false;
                    
                    for ($i = 0; $i < $maxAttempts; $i++) {
                        if (sendEmail($email, $subject, $body)) {
                            $emailSent = true;
                            break;
                        }
                        sleep(1); // Wait 1 second before retrying
                    }
                    
                    if ($emailSent) {
                        // Clear any previous failed attempts
                        $clearStmt = $conn->prepare("DELETE FROM failed_logins WHERE email = ?");
                        $clearStmt->bind_param("s", $email);
                        $clearStmt->execute();
                        $clearStmt->close();
                        
                        header("Location: login.php?reset_success=1");
                        exit();
                    } else {
                        header("Location: login.php?reset_error=Failed to send email after multiple attempts. Please try again later.");
                        exit();
                    }
                } else {
                    header("Location: login.php?reset_error=Database error. Please try again.");
                    exit();
                }
                $updateStmt->close();
            } else {
                // Don't reveal if email exists or not for security
                header("Location: login.php?reset_success=1");
                exit();
            }
            $stmt->close();
        }
    }
    // Handle login verification
    elseif (isset($_POST['verify_login'])) {
        $enteredCode = trim($_POST['verification_code']);
        $email = $_SESSION['verify_email'];
        
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email FROM users WHERE email = ? AND verification_code = ? AND verification_code_expiry > NOW()");
        $stmt->bind_param("ss", $email, $enteredCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Clear verification code
            $updateStmt = $conn->prepare("UPDATE users SET verification_code = NULL, verification_code_expiry = NULL WHERE email = ?");
            $updateStmt->bind_param("s", $email);
            $updateStmt->execute();
            $updateStmt->close();
            
            // Set user session
            setUserSession($user['id'], $user['first_name'], $user['last_name'], $user['email']);
            
            // Log this login
            $ip = $_SERVER['REMOTE_ADDR'];
            $logStmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address) VALUES (?, ?)");
            $logStmt->bind_param("is", $user['id'], $ip);
            $logStmt->execute();
            $logStmt->close();
            
            // Clear verification session
            unset($_SESSION['verify_email']);
            
            if (isset($_GET['modal'])) {
                // For modal logins
                echo '<script>window.parent.postMessage("login_success", window.location.origin);</script>';
                exit();
            } else {
                // Redirect to dashboard
                header("Location: ./Dashboard/index.php");
                exit();
            }
        } else {
            // Check if code exists but expired
            $expiredStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND verification_code = ? AND verification_code_expiry <= NOW()");
            $expiredStmt->bind_param("ss", $email, $enteredCode);
            $expiredStmt->execute();
            $expiredStmt->store_result();
            
            if ($expiredStmt->num_rows > 0) {
                $verificationError = "Verification code has expired. Please request a new one.";
            } else {
                $verificationError = "Invalid verification code. Please try again.";
            }
            $expiredStmt->close();
            $showVerificationModal = true;
        }
        $stmt->close();
    }
    // Handle normal login
    elseif (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $errorMsg = "Please fill in both fields.";
        } else {
            $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, temp_password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    // Check if this is a temporary password
                    if ($user['temp_password'] == 1) {
                        $_SESSION['temp_password_warning'] = true;
                    }
                    
                    // Check for suspicious login (only based on failed attempts now)
                    if (detectSuspiciousLogin($user['id'])) {
                        // Generate verification code
                        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                        $expiryTime = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        
                        // Store verification code
                        $verifyStmt = $conn->prepare("UPDATE users SET verification_code = ?, verification_code_expiry = ? WHERE id = ?");
                        $verifyStmt->bind_param("ssi", $verificationCode, $expiryTime, $user['id']);
                        $verifyStmt->execute();
                        $verifyStmt->close();
                        
                        // Send verification email
                        $subject = "PDFSimba - Login Verification Code";
                        $body = "Hello {$user['first_name']},\n\n";
                        $body .= "We detected multiple failed login attempts for your account.\n\n";
                        $body .= "Your verification code is: $verificationCode\n\n";
                        $body .= "This code will expire in 1 hour.\n\n";
                        $body .= "If you didn't attempt to login, please contact support immediately.\n\n";
                        $body .= "Thank you,\nThe PDFSimba Team";
                        
                        if (sendEmail($email, $subject, $body)) {
                            $_SESSION['verify_email'] = $email;
                            $showVerificationModal = true;
                        } else {
                            $errorMsg = "Failed to send verification email. Please try again.";
                        }
                    } else {
                        // Normal login - no verification needed
                        setUserSession($user['id'], $user['first_name'], $user['last_name'], $user['email']);
                        
                        // Log this login
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $logStmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address) VALUES (?, ?)");
                        $logStmt->bind_param("is", $user['id'], $ip);
                        $logStmt->execute();
                        $logStmt->close();
                        
                        if (isset($_GET['modal'])) {
                            // For modal logins
                            echo '<script>window.parent.postMessage("login_success", window.location.origin);</script>';
                            exit();
                        } else {
                            // Redirect to dashboard
                            header("Location: ./Dashboard/index.php");
                            exit();
                        }
                    }
                } else {
                    $errorMsg = "Incorrect password.";
                    
                    // Log failed attempt
                    $failStmt = $conn->prepare("INSERT INTO failed_logins (email, ip_address) VALUES (?, ?)");
                    $failStmt->bind_param("ss", $email, $_SERVER['REMOTE_ADDR']);
                    $failStmt->execute();
                    $failStmt->close();
                }
            } else {
                $errorMsg = "No account found with that email.";
            }
            $stmt->close();
        }
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
  <link rel="icon" href="./Media/book3.png" type="image/x-icon">
  <link rel="stylesheet" href="./CSS/signup.css">
  <style>
    .reset-link {
      cursor: pointer;
      color: #0d6efd;
      text-decoration: underline;
    }
    .reset-link:hover {
      text-decoration: none;
    }
    .modal-backdrop {
      z-index: 1040 !important;
    }
  </style>
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

      <?php if (isset($_SESSION['temp_password_warning'])): ?>
        <div class="alert alert-warning">
          You've logged in with a temporary password. Please go to your profile to set a new permanent password.
        </div>
        <?php unset($_SESSION['temp_password_warning']); ?>
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

<!-- Password Reset Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="resetSuccessMessage" class="alert alert-success" style="display: none;">
                    A temporary password has been sent to your email. Please check your inbox.
                    <br><br>
                    After logging in with the temporary password, go to your profile to set a new permanent password.
                </div>
                <div id="resetErrorMessage" class="alert alert-danger" style="display: none;"></div>
                <form method="post" id="resetForm">
                    <div class="mb-3">
                        <label for="reset_email" class="form-label">Enter your registered email address</label>
                        <input type="email" class="form-control" id="reset_email" name="reset_email" required>
                    </div>
                    <button type="submit" name="request_reset" class="btn btn-primary">Send Temporary Password</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-hidden="true" style="<?= $showVerificationModal ? 'display: block; background-color: rgba(0,0,0,0.5);' : 'display: none;' ?>">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Login Verification</h5>
        </div>
        <div class="modal-body">
          <?php if ($verificationError): ?>
            <div class="alert alert-danger"><?= $verificationError ?></div>
          <?php endif; ?>
          <p>We've sent a 6-digit verification code to <strong><?= htmlspecialchars($_SESSION['verify_email'] ?? '') ?></strong>.</p>
          <p class="text-muted small">This code will expire in 1 hour.</p>
          <form method="post">
            <div class="mb-3">
              <label for="verification_code" class="form-label">Verification Code</label>
              <input type="text" class="form-control" id="verification_code" name="verification_code" required maxlength="6" pattern="\d{6}" title="Please enter a 6-digit code">
            </div>
            <button type="submit" name="verify_login" class="btn btn-primary">Verify</button>
          </form>
        </div>
      </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle verification modal
        <?php if ($showVerificationModal): ?>
            var verifyModal = new bootstrap.Modal(document.getElementById('verificationModal'));
            verifyModal.show();
        <?php endif; ?>
        
        // Handle reset success/error from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('reset_success')) {
            document.getElementById('resetSuccessMessage').style.display = 'block';
            document.getElementById('resetForm').style.display = 'none';
            document.getElementById('resetErrorMessage').style.display = 'none';
            var resetModal = new bootstrap.Modal(document.getElementById('resetModal'));
            resetModal.show();
            
            // Clear the success parameter from URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
        if (urlParams.has('reset_error')) {
            document.getElementById('resetSuccessMessage').style.display = 'none';
            document.getElementById('resetForm').style.display = 'block';
            document.getElementById('resetErrorMessage').style.display = 'block';
            document.getElementById('resetErrorMessage').textContent = urlParams.get('reset_error');
            var resetModal = new bootstrap.Modal(document.getElementById('resetModal'));
            resetModal.show();
            
            // Clear the error parameter from URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    });

    function showResetModal() {
        var modal = new bootstrap.Modal(document.getElementById('resetModal'));
        modal.show();
    }
</script>

<?php if (isset($_GET['modal'])): ?>
<script>
// When login is successful in modal context
window.parent.postMessage('login_success', window.location.origin);

// Handle successful login
<?php if (isset($_SESSION['user_id']) && isset($_GET['modal'])): ?>
window.parent.postMessage('login_success', window.location.origin);
<?php endif; ?>
</script>
<?php endif; ?>
</body>
</html>