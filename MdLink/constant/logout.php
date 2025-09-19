<?php  
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} //to ensure you are using same session

// Log logout activity before destroying session
if (isset($_SESSION['adminId']) && isset($_SESSION['username'])) {
    require_once '../activity_logger.php';
    logLogout($_SESSION['adminId'], $_SESSION['username']);
}

session_destroy(); //destroy the session
 
?>
<script>
window.location="../login.php";
</script>
<?php
//to redirect back to "index.php" after logging out
  exit;
?>