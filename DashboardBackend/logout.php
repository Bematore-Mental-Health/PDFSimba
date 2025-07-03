<?php
include 'session.php';

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
header("Location: ../login.php");
exit();
?>
