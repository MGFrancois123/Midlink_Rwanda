<?php  
// Prevent any output before headers
ob_start();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log logout activity before destroying session
if (isset($_SESSION['adminId']) && isset($_SESSION['username'])) {
    // Try to include activity logger with error handling
    $activity_logger_paths = [
        '../activity_logger.php',
        './activity_logger.php',
        dirname(__FILE__) . '/../activity_logger.php',
        dirname(__FILE__) . '/activity_logger.php'
    ];
    
    foreach ($activity_logger_paths as $path) {
        if (file_exists($path)) {
            try {
                require_once $path;
                logLogout($_SESSION['adminId'], $_SESSION['username']);
                break;
            } catch (Exception $e) {
                error_log("Logout logging error: " . $e->getMessage());
            }
        }
    }
}

// Store redirect info before destroying session
$redirect_url = '../login.php';

// Clear all session variables
$_SESSION = array();

// Delete the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clean output buffer
ob_end_clean();

// Use PHP header redirect
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location: " . $redirect_url);
exit();
?>