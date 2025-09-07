<?php
include './DashboardBackend/session.php';
include './DashboardBackend/db_connection.php';

$errorMsg = "";
$showVerificationModal = false;
$verificationError = "";

// SMTP Configuration
$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;
$smtpUsername = 'pdfsimbasystem@gmail.com';
$smtpPassword = 'bklx zwxt acxm bsnk';
$fromEmail = 'pdfsimbasystem@gmail.com';
$fromName = 'PDFSimba';

// Create registration attempts table if it doesn't exist
$createTableSQL = "
CREATE TABLE IF NOT EXISTS registration_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('success', 'failed', 'blocked') NOT NULL,
    reason VARCHAR(255),
    is_spam BOOLEAN DEFAULT FALSE
)";
$conn->query($createTableSQL);

// Create index for better performance
$conn->query("CREATE INDEX IF NOT EXISTS idx_ip ON registration_attempts(ip_address)");
$conn->query("CREATE INDEX IF NOT EXISTS idx_time ON registration_attempts(attempt_time)");

// Function to log registration attempts
function logRegistrationAttempt($ip, $email, $firstName, $lastName, $status, $reason = '', $isSpam = false) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO registration_attempts (ip_address, email, first_name, last_name, status, reason, is_spam) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $ip, $email, $firstName, $lastName, $status, $reason, $isSpam);
    $stmt->execute();
    $stmt->close();
}

// Rate limiting - track attempts by IP
function checkRateLimit() {
    global $conn;
    
    $maxAttempts = 5; // Maximum attempts per hour
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Check recent attempts from this IP
    $stmt = $conn->prepare("SELECT COUNT(*) FROM registration_attempts WHERE ip_address = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    return $count < $maxAttempts;
}

// Check if IP is blocked (too many failed attempts)
function isIpBlocked() {
    global $conn;
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $maxFailedAttempts = 10; // Block after 10 failed attempts
    
    // Check failed attempts in last 24 hours
    $stmt = $conn->prepare("SELECT COUNT(*) FROM registration_attempts WHERE ip_address = ? AND status = 'failed' AND attempt_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    return $count >= $maxFailedAttempts;
}

// Spam detection
function isSpamContent($text) {
    // Check for common spam patterns
    $spamPatterns = [
        '/🏆/u', // Trophy emoji
        '/Поздравялем/u', // Russian text
        '/https?:\/\/[^\s]+/i', // URLs
        '/wilberies/i', // Spam keyword
        '/акции/u', // Russian text
        '/бесплатные/u', // Russian text
        '/попытки/u', // Russian text
        '/\{.*\}|\*.*\*|_.*_/u', // Excessive special characters
    ];
    
    foreach ($spamPatterns as $pattern) {
        if (preg_match($pattern, $text)) {
            return true;
        }
    }
    
    // Check for excessive length (names shouldn't be too long)
    if (strlen($text) > 50) {
        return true;
    }
    
    return false;
}

function sendVerificationEmail($toEmail, $firstName, $verificationCode) {
    global $smtpHost, $smtpPort, $smtpUsername, $smtpPassword, $fromEmail, $fromName;
    
    $subject = "PDFSimba - Verify Your Email";
    $body = "Hello $firstName,\n\n";
    $body .= "Your verification code is: $verificationCode\n\n";
    $body .= "This code will expire in 1 hour.\n\n";
    $body .= "Enter this code to complete your registration.\n\n";
    $body .= "If you didn't request this, please ignore this email.";
    
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
    $socket = fsockopen($smtpHost, $smtpPort, $errno, $errstr, 30);
    
    if (!$socket) {
        return false;
    }
    
    // SMTP Conversation
    $welcome = fread($socket, 4096);
    fwrite($socket, "EHLO localhost\r\n");
    fread($socket, 4096);
    
    fwrite($socket, "STARTTLS\r\n");
    fread($socket, 4096);
    
    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
        fclose($socket);
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
        return false;
    }
    
    fwrite($socket, "MAIL FROM: <$fromEmail>\r\n");
    fread($socket, 4096);
    
    fwrite($socket, "RCPT TO: <$toEmail>\r\n");
    $rcptResponse = fread($socket, 4096);
    
    // Check if recipient is valid
    if (strpos($rcptResponse, '250') === false) {
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        return false;
    }
    
    fwrite($socket, "DATA\r\n");
    fread($socket, 4096);
    
    fwrite($socket, "$headersString\r\n$body\r\n.\r\n");
    $sendResponse = fread($socket, 4096);
    
    fwrite($socket, "QUIT\r\n");
    fclose($socket);
    
    return strpos($sendResponse, '250') !== false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip = $_SERVER['REMOTE_ADDR'];
    $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    // Handle verification code submission
    if (isset($_POST['verify_code'])) {
        $enteredCode = trim($_POST['verification_code']);
        $email = $_SESSION['temp_email'];
        
        // Verify code against database with expiry check
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND verification_code = ? AND verification_code_expiry > NOW()");
        $stmt->bind_param("ss", $email, $enteredCode);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Update user as verified
            $updateStmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_code = NULL, verification_code_expiry = NULL WHERE email = ?");
            $updateStmt->bind_param("s", $email);
            
            if ($updateStmt->execute()) {
                // Log successful verification
                logRegistrationAttempt($ip, $email, '', '', 'success', 'Email verified');
                
                // Clean up session
                unset($_SESSION['temp_email']);
                unset($_SESSION['verification_code']);
                
                // Redirect to login
                header("Location: login.php?signup=success");
                exit();
            } else {
                $verificationError = "Database update failed. Please try again.";
                logRegistrationAttempt($ip, $email, '', '', 'failed', 'Database update failed');
            }
            $updateStmt->close();
        } else {
            // Check if code exists but expired
            $expiredStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND verification_code = ? AND verification_code_expiry <= NOW()");
            $expiredStmt->bind_param("ss", $email, $enteredCode);
            $expiredStmt->execute();
            $expiredStmt->store_result();
            
            if ($expiredStmt->num_rows > 0) {
                $verificationError = "Verification code has expired. Please request a new one.";
                logRegistrationAttempt($ip, $email, '', '', 'failed', 'Expired verification code');
            } else {
                $verificationError = "Invalid verification code. Please try again.";
                logRegistrationAttempt($ip, $email, '', '', 'failed', 'Invalid verification code');
            }
            $expiredStmt->close();
            $showVerificationModal = true;
        }
        $stmt->close();
    } 
    // Handle initial signup
    else {
        $password = $_POST['password'];
        $terms = isset($_POST['terms']) ? 1 : 0;

        // Check if IP is blocked first
        if (isIpBlocked()) {
            $errorMsg = "Too many failed attempts from your network. Please try again later or contact support.";
            logRegistrationAttempt($ip, $email, $firstName, $lastName, 'blocked', 'IP blocked due to too many failed attempts', true);
        }
        // Check rate limiting
        elseif (!checkRateLimit()) {
            $errorMsg = "Too many registration attempts. Please try again later.";
            logRegistrationAttempt($ip, $email, $firstName, $lastName, 'blocked', 'Rate limit exceeded', true);
        }
        // Validate input
        elseif (empty($firstName) || empty($lastName) || empty($email) || empty($password) || !$terms) {
            $errorMsg = "All fields are required and terms must be accepted.";
            logRegistrationAttempt($ip, $email, $firstName, $lastName, 'failed', 'Missing required fields');
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg = "Invalid email format.";
            logRegistrationAttempt($ip, $email, $firstName, $lastName, 'failed', 'Invalid email format');
        }
        elseif (isSpamContent($firstName) || isSpamContent($lastName)) {
            // Log the attempt but don't reveal it's spam detection
            $errorMsg = "Invalid input detected. Please check your information.";
            logRegistrationAttempt($ip, $email, $firstName, $lastName, 'failed', 'Spam content detected', true);
        }
        else {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errorMsg = "Email is already registered.";
                logRegistrationAttempt($ip, $email, $firstName, $lastName, 'failed', 'Email already registered');
            } else {
                // Generate verification code
                $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Calculate expiry time (1 hour from now)
                $expiryTime = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Insert user with verification code and expiry time
                $insertStmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, created_at, verification_code, verification_code_expiry, is_verified, updated_at) VALUES (?, ?, ?, ?, NOW(), ?, ?, 0, NOW())");
                $insertStmt->bind_param("ssssss", $firstName, $lastName, $email, $hashedPassword, $verificationCode, $expiryTime);
                
                if ($insertStmt->execute()) {
                    // Store email in session for verification
                    $_SESSION['temp_email'] = $email;
                    
                    // Send verification email using SMTP
                    if (sendVerificationEmail($email, $firstName, $verificationCode)) {
                        $showVerificationModal = true;
                        logRegistrationAttempt($ip, $email, $firstName, $lastName, 'success', 'Verification email sent');
                    } else {
                        $errorMsg = "Failed to send verification email. Please try again.";
                        logRegistrationAttempt($ip, $email, $firstName, $lastName, 'failed', 'Failed to send verification email');
                        // Rollback the insertion
                        $conn->query("DELETE FROM users WHERE email = '$email'");
                    }
                } else {
                    $errorMsg = "Database error. Please try again.";
                    logRegistrationAttempt($ip, $email, $firstName, $lastName, 'failed', 'Database error during registration');
                }
                $insertStmt->close();
            }
            $stmt->close();
        }
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

  <!-- Verification Modal -->
  <div class="modal fade" id="verificationModal" tabindex="-1" aria-hidden="true" style="<?= $showVerificationModal ? 'display: block; background-color: rgba(0,0,0,0.5);' : 'display: none;' ?>">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Verify Your Email</h5>
        </div>
        <div class="modal-body">
          <?php if ($verificationError): ?>
            <div class="alert alert-danger"><?= $verificationError ?></div>
          <?php endif; ?>
          <p>We've sent a 6-digit verification code to <strong><?= htmlspecialchars($_SESSION['temp_email'] ?? '') ?></strong>.</p>
          <p class="text-muted small">This code will expire in 1 hour.</p>
          <form method="post">
            <div class="mb-3">
              <label for="verification_code" class="form-label">Verification Code</label>
              <input type="text" class="form-control" id="verification_code" name="verification_code" required maxlength="6" pattern="\d{6}" title="Please enter a 6-digit code">
            </div>
            <button type="submit" name="verify_code" class="btn btn-primary">Verify</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Show modal if verification is needed
    <?php if ($showVerificationModal): ?>
      document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('verificationModal'));
        modal.show();
      });
    <?php endif; ?>
  </script>
</body>
</html>