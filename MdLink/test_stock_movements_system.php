<?php
// Test Stock Movements with full system integration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up a test session to bypass authentication
$_SESSION['userId'] = 1;
$_SESSION['userRole'] = 'super_admin';

// Set the title parameter
$_GET['title'] = 'Stock Movements';

// Include the main placeholder page
include 'placeholder.php';
?>