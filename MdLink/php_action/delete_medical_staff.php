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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Check if request is POST and has staff_id
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($input['staff_id'])) {
    error_log("Invalid request method or missing staff_id");
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$staff_id = (int)$input['staff_id'];

// Validate staff_id
if ($staff_id <= 0) {
    error_log("Invalid staff_id: $staff_id");
    echo json_encode(['success' => false, 'message' => 'Invalid staff ID']);
    exit();
}

try {
    // First, check if the staff exists
    $check_query = "SELECT full_name FROM medical_staff WHERE staff_id = ?";
    $check_stmt = $connect->prepare($check_query);
    $check_stmt->bind_param("i", $staff_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        error_log("Staff not found with ID: $staff_id");
        echo json_encode(['success' => false, 'message' => 'Staff member not found']);
        exit();
    }
    
    $staff_name = $check_result->fetch_assoc()['full_name'];
    
    // Delete the staff member
    $delete_query = "DELETE FROM medical_staff WHERE staff_id = ?";
    $delete_stmt = $connect->prepare($delete_query);
    
    if (!$delete_stmt) {
        error_log("Failed to prepare delete query: " . $connect->error);
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare delete query']);
        exit();
    }
    
    $delete_stmt->bind_param("i", $staff_id);
    
    if ($delete_stmt->execute()) {
        // Log the delete action
        error_log("Medical staff deleted successfully: ID $staff_id, Name: $staff_name");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Medical staff deleted successfully!',
            'data' => [
                'staff_id' => $staff_id,
                'staff_name' => $staff_name
            ]
        ]);
    } else {
        error_log("Failed to execute delete query: " . $delete_stmt->error);
        echo json_encode(['success' => false, 'message' => 'Failed to delete medical staff: ' . $delete_stmt->error]);
    }
    
} catch (Exception $e) {
    error_log("Error deleting medical staff: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
