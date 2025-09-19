<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['medicine_id'])) {
    $medicine_id = (int)$_POST['medicine_id'];
    
    // Validate medicine_id
    if ($medicine_id <= 0) {
        header('Location: ../product.php?error=Invalid medicine ID');
        exit;
    }
    
    try {
        // First, check if the medicine exists
        $check_sql = "SELECT name FROM medicines WHERE medicine_id = ?";
        $check_stmt = $connect->prepare($check_sql);
        $check_stmt->bind_param("i", $medicine_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            header('Location: ../product.php?error=Medicine not found');
            exit;
        }
        
        $medicine_name = $result->fetch_assoc()['name'];
        
        // Delete the medicine
        $delete_sql = "DELETE FROM medicines WHERE medicine_id = ?";
        $delete_stmt = $connect->prepare($delete_sql);
        $delete_stmt->bind_param("i", $medicine_id);
        
        if ($delete_stmt->execute()) {
            // Log the delete action (optional - skip if audit_logs table doesn't exist)
            try {
                $log_sql = "INSERT INTO audit_logs (admin_id, table_name, record_id, action, new_data, action_time) 
                           VALUES (?, 'medicines', ?, 'DELETE', ?, NOW())";
                $log_stmt = $connect->prepare($log_sql);
                $log_data = json_encode([
                    'deleted_medicine_name' => $medicine_name,
                    'deleted_medicine_id' => $medicine_id
                ]);
                $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 1;
                $log_stmt->bind_param("iis", $admin_id, $medicine_id, $log_data);
                $log_stmt->execute();
            } catch (Exception $log_error) {
                // Log error but don't stop the delete
                error_log("Audit log error: " . $log_error->getMessage());
            }
            
            // Success - redirect to product list
            // Log activity
            require_once '../activity_logger.php';
            logDelete($_SESSION['adminId'], 'medicines', $medicine_id, "Deleted medicine with ID: {$medicine_id}");
            
            header('Location: ../product.php?success=Medicine deleted successfully');
            exit;
        } else {
            throw new Exception("Delete failed: " . $delete_stmt->error);
        }
        
    } catch (Exception $e) {
        error_log("Medicine delete error: " . $e->getMessage());
        header('Location: ../product.php?error=Failed to delete medicine: ' . $e->getMessage());
        exit;
    }
} else {
    // Not a POST request or missing medicine_id
    header('Location: ../product.php?error=Invalid request');
    exit;
}
?>
