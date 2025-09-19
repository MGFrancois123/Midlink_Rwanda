<?php 

// Fix: Clean up the duplicated and malformed session_start logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'connect.php';
// single DB connection via connect.php (mdlink)

//echo $_SESSION['userId'];

// Global error handling to avoid blank pages/spinners
// Show friendly message on fatal errors and log details to PHP error log
ini_set('display_errors', 0);
error_reporting(E_ALL);
set_error_handler(function($severity, $message, $file, $line){
    error_log("[MDLink PHP Warning] $message in $file:$line");
    return false; // use normal PHP handling as well
});
register_shutdown_function(function(){
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log('[MDLink PHP Fatal] '.$e['message'].' in '.$e['file'].':'.$e['line']);
        if (!headers_sent()) {
            http_response_code(500);
        }
        // Suppress global UI error banner; only log the fatal error above.
        // Optionally, hide preloader quietly without showing a message.
        echo '<script>var p=document.querySelector(".preloader"); if(p){p.style.display="none";}</script>';
    }
});

// Normalize sessions: prefer adminId; if only userId exists, mirror it
if (isset($_SESSION['userId']) && !isset($_SESSION['adminId'])) {
    $_SESSION['adminId'] = (int) $_SESSION['userId'];
}

// Establish a role in session when adminId exists but role not yet set
if (isset($_SESSION['adminId']) && $_SESSION['adminId'] && empty($_SESSION['userRole'])) {
    $uid = (int) $_SESSION['adminId'];
    $role = null;
    // Prefer mdlink admin_users by id (admin)
    if (isset($connect) && $connect) {
        $qa = @$connect->query("SELECT role FROM admin_users WHERE admin_id = ".$uid." LIMIT 1");
        if ($qa && $qa->num_rows === 1) {
            $row = $qa->fetch_assoc();
            $role = strtolower(trim((string)$row['role']));
        }
    }
    if (!$role) {
        // Fallback mapping by id if role column not present
        // id 1 -> super_admin, 2 -> pharmacy_admin, 3 -> finance_admin, others -> user
        if ($uid === 1) $role = 'super_admin';
        elseif ($uid === 2) $role = 'pharmacy_admin';
        elseif ($uid === 3) $role = 'finance_admin';
        else $role = 'user';
    }
    $_SESSION['userRole'] = $role;
}

// Set pharmacy context for pharmacy admins
if (isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'pharmacy_admin' && !isset($_SESSION['pharmacy_id'])) {
    // Default to Ineza Pharmacy for pharmacy admins
    $_SESSION['pharmacy_id'] = 8;
    $_SESSION['pharmacy_name'] = 'Ineza Pharmacy';
}

// Fallback for testing - set pharmacy context if not set
if (!isset($_SESSION['pharmacy_id'])) {
    $_SESSION['pharmacy_id'] = 8;
    $_SESSION['pharmacy_name'] = 'Ineza Pharmacy';
}

// Allow public access to specific pages (no login required)
$currentScript = basename($_SERVER['PHP_SELF']);
$publicScripts = array(
	'login.php',
	'forgot_password.php',
	'reset_password.php'
);

if (!isset($_SESSION['adminId']) || !$_SESSION['adminId']) {
	if (!in_array($currentScript, $publicScripts, true)) {
		header('location:./login.php');	
		exit;
	}
}

?>