<?php
// Temporary authentication bypass for testing Stock Movements
session_start();

// Set up test session
$_SESSION['userId'] = 1;
$_SESSION['userRole'] = 'super_admin';

// Redirect to Stock Movements page
header('Location: stock_movements.php');
exit;
?>