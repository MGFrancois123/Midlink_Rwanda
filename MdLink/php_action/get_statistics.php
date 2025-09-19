<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get pharmacy count
    $pharmacy_query = "SELECT COUNT(*) as count FROM pharmacies";
    $pharmacy_result = $connect->query($pharmacy_query);
    $pharmacy_count = $pharmacy_result ? $pharmacy_result->fetch_assoc()['count'] : 0;
    
    // Get admin count
    $admin_query = "SELECT COUNT(*) as count FROM admin_users";
    $admin_result = $connect->query($admin_query);
    $admin_count = $admin_result ? $admin_result->fetch_assoc()['count'] : 0;
    
    // Get medicine count
    $medicine_query = "SELECT COUNT(*) as count FROM medicines";
    $medicine_result = $connect->query($medicine_query);
    $medicine_count = $medicine_result ? $medicine_result->fetch_assoc()['count'] : 0;
    
    // Get system status (always active for now)
    $system_status = "Active";
    
    echo json_encode([
        'success' => true,
        'pharmacy_count' => (int)$pharmacy_count,
        'admin_count' => (int)$admin_count,
        'medicine_count' => (int)$medicine_count,
        'system_status' => $system_status
    ]);
    
} catch (Exception $e) {
    error_log("Statistics error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to get statistics: ' . $e->getMessage()
    ]);
}
?>
