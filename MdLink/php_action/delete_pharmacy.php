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

// Log the request
error_log("Delete pharmacy request received: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pharmacy_id'])) {
    $pharmacy_id = (int)$_POST['pharmacy_id'];
    
    // Validate pharmacy_id
    if ($pharmacy_id <= 0) {
        error_log("Invalid pharmacy ID: $pharmacy_id");
        echo json_encode(['success' => false, 'message' => 'Invalid pharmacy ID']);
        exit;
    }
    
    try {
        // First, check if the pharmacy exists
        $check_sql = "SELECT name FROM pharmacies WHERE pharmacy_id = ?";
        $check_stmt = $connect->prepare($check_sql);
        $check_stmt->bind_param("i", $pharmacy_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            error_log("Pharmacy not found with ID: $pharmacy_id");
            echo json_encode(['success' => false, 'message' => 'Pharmacy not found']);
            exit;
        }
        
        $pharmacy_name = $result->fetch_assoc()['name'];
        
        // Start transaction
        $connect->autocommit(false);
        
        try {
            // Delete related medicines first (if any)
            $delete_medicines_sql = "DELETE FROM medicines WHERE pharmacy_id = ?";
            $delete_medicines_stmt = $connect->prepare($delete_medicines_sql);
            $delete_medicines_stmt->bind_param("i", $pharmacy_id);
            $delete_medicines_stmt->execute();
            
            // Delete related admin users (if any)
            $delete_admins_sql = "DELETE FROM admin_users WHERE pharmacy_id = ?";
            $delete_admins_stmt = $connect->prepare($delete_admins_sql);
            $delete_admins_stmt->bind_param("i", $pharmacy_id);
            $delete_admins_stmt->execute();
            
            // Delete the pharmacy
            $delete_pharmacy_sql = "DELETE FROM pharmacies WHERE pharmacy_id = ?";
            $delete_pharmacy_stmt = $connect->prepare($delete_pharmacy_sql);
            $delete_pharmacy_stmt->bind_param("i", $pharmacy_id);
            
            if ($delete_pharmacy_stmt->execute()) {
                // Log the delete action (optional - skip if audit_logs table doesn't exist)
                try {
                    $log_sql = "INSERT INTO audit_logs (admin_id, table_name, record_id, action, new_data, action_time) 
                               VALUES (?, 'pharmacies', ?, 'DELETE', ?, NOW())";
                    $log_stmt = $connect->prepare($log_sql);
                    $log_data = json_encode([
                        'deleted_pharmacy_name' => $pharmacy_name,
                        'deleted_pharmacy_id' => $pharmacy_id
                    ]);
                    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 1;
                    $log_stmt->bind_param("iis", $admin_id, $pharmacy_id, $log_data);
                    $log_stmt->execute();
                } catch (Exception $log_error) {
                    // Log error but don't stop the delete
                    error_log("Audit log error: " . $log_error->getMessage());
                }
                
                // Commit transaction
                $connect->commit();
                $connect->autocommit(true);
                
                // Success - return JSON response
                error_log("Pharmacy deleted successfully: ID $pharmacy_id, Name: $pharmacy_name");
                echo json_encode([
                    'success' => true, 
                    'message' => 'Pharmacy deleted successfully!',
                    'data' => [
                        'pharmacy_id' => $pharmacy_id,
                        'pharmacy_name' => $pharmacy_name
                    ]
                ]);
                exit;
            } else {
                throw new Exception("Delete failed: " . $delete_pharmacy_stmt->error);
            }
            
        } catch (Exception $e) {
            // Rollback transaction
            $connect->rollback();
            $connect->autocommit(true);
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log("Pharmacy delete error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to delete pharmacy: ' . $e->getMessage()]);
        exit;
    }
} else {
    // Not a POST request or missing pharmacy_id
    error_log("Invalid request method or missing pharmacy_id");
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}
?>
