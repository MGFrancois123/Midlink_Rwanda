<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'data' => null);

try {
    // Check if user is logged in and has super_admin role
    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'super_admin') {
        throw new Exception('Access denied. Only Super Administrators can delete users.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get user ID from POST data
        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        
        if ($user_id <= 0) {
            throw new Exception('Invalid user ID provided.');
        }

        // Check if user exists
        $check_sql = "SELECT admin_id, username FROM admin_users WHERE admin_id = ?";
        $check_stmt = $connect->prepare($check_sql);
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception('User not found.');
        }
        
        $user_data = $check_result->fetch_assoc();
        $username = $user_data['username'];
        $check_stmt->close();

        // Prevent deletion of the current user
        if ($user_id == $_SESSION['adminId']) {
            throw new Exception('You cannot delete your own account.');
        }

        // Start transaction
        $connect->autocommit(false);

        try {
            // Delete user
            $delete_sql = "DELETE FROM admin_users WHERE admin_id = ?";
            $delete_stmt = $connect->prepare($delete_sql);
            $delete_stmt->bind_param("i", $user_id);

            if (!$delete_stmt->execute()) {
                throw new Exception('Failed to delete user: ' . $delete_stmt->error);
            }

            $delete_stmt->close();

            // Log the user deletion
            $log_sql = "INSERT INTO audit_logs (admin_id, table_name, record_id, action, old_data, action_time) VALUES (?, 'admin_users', ?, 'DELETE', ?, NOW())";
            $log_stmt = $connect->prepare($log_sql);
            
            if ($log_stmt) {
                $log_data = json_encode([
                    'username' => $username,
                    'admin_id' => $user_id
                ]);
                $log_stmt->bind_param("iis", $_SESSION['adminId'], $user_id, $log_data);
                $log_stmt->execute();
                $log_stmt->close();
            }

            // Commit transaction
            $connect->commit();
            $connect->autocommit(true);

            $response['success'] = true;
            $response['message'] = 'User deleted successfully!';
            $response['data'] = array(
                'user_id' => $user_id,
                'username' => $username
            );

        } catch (Exception $e) {
            // Rollback transaction
            $connect->rollback();
            $connect->autocommit(true);
            throw $e;
        }

    } else {
        throw new Exception('Invalid request method');
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Close database connection
$connect->close();

// Return JSON response
echo json_encode($response);
?>
