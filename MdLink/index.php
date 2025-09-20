<?php
// Prevent redirect loops
if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
    exit();
}

// Check if already on login.php to prevent loops
if (strpos($_SERVER['REQUEST_URI'], 'login.php') !== false) {
    exit();
}

// Redirect to login page
header("Location: /login.php");
exit();
?>