<?php
session_start();

// Store user session
function setUserSession($userId, $firstName, $lastName, $email) {
    // Clear any admin session data first
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['logged_in_as_admin']);
    
    // Set user session
    $_SESSION['user_id'] = $userId;
    $_SESSION['first_name'] = $firstName;
    $_SESSION['last_name'] = $lastName;
    $_SESSION['user_email'] = $email;
    $_SESSION['logged_in_as_user'] = true;
}

// Store admin session
function setAdminSession($adminId, $name, $email) {
    // Clear any user session data first
    unset($_SESSION['user_id']);
    unset($_SESSION['first_name']);
    unset($_SESSION['last_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['logged_in_as_user']);
    
    // Set admin session
    $_SESSION['admin_id'] = $adminId;
    $_SESSION['admin_name'] = $name;
    $_SESSION['admin_email'] = $email;
    $_SESSION['logged_in_as_admin'] = true;
}

// Check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['logged_in_as_user']);
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['logged_in_as_admin']);
}

// Get current role
function getCurrentRole() {
    if (isset($_SESSION['logged_in_as_admin'])) {
        return 'admin';
    } elseif (isset($_SESSION['logged_in_as_user'])) {
        return 'user';
    }
    return null;
}
?>