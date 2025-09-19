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

// Get staff_id from GET parameter
$staff_id = isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : 0;

// Validate staff_id
if ($staff_id <= 0) {
    error_log("Invalid staff_id: $staff_id");
    echo json_encode(['success' => false, 'message' => 'Invalid staff ID']);
    exit();
}

try {
    // Get staff details with pharmacy information
    $query = "SELECT ms.*, p.name as pharmacy_name 
              FROM medical_staff ms 
              LEFT JOIN pharmacies p ON ms.pharmacy_id = p.pharmacy_id 
              WHERE ms.staff_id = ?";
    
    $stmt = $connect->prepare($query);
    
    if (!$stmt) {
        error_log("Failed to prepare query: " . $connect->error);
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare query']);
        exit();
    }
    
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        error_log("Staff not found with ID: $staff_id");
        echo json_encode(['success' => false, 'message' => 'Staff member not found']);
        exit();
    }
    
    $staff = $result->fetch_assoc();
    
    // Log the successful retrieval
    error_log("Staff details retrieved successfully: ID $staff_id, Name: " . $staff['full_name']);
    
    echo json_encode([
        'success' => true,
        'data' => $staff
    ]);
    
} catch (Exception $e) {
    error_log("Error getting staff details: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
