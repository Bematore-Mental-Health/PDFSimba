<?php
// session.php

session_start();

// Store user session with all details
function setUserSession($userId, $firstName, $lastName, $email) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['first_name'] = $firstName;
    $_SESSION['last_name'] = $lastName;
    $_SESSION['user_email'] = $email;
}

// Check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
