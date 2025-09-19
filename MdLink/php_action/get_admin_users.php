<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

header('Content-Type: application/json');

try {
    $query = "SELECT admin_id, username, email, 'admin' as role, status, created_at FROM admin_users ORDER BY username ASC";
    $result = $connect->query($query);
    
    $users = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $users
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_admin_users.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch admin users',
        'error' => $e->getMessage()
    ]);
}
?>
