<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

header('Content-Type: application/json');

try {
    $log_id = isset($_GET['log_id']) ? (int)$_GET['log_id'] : 0;
    
    if (!$log_id) {
        throw new Exception('Log ID is required');
    }
    
    $query = "SELECT 
                al.log_id,
                al.admin_id as user_id,
                au.username,
                'admin' as role,
                au.email,
                al.action,
                al.table_name,
                al.record_id,
                al.description,
                al.ip_address,
                al.user_agent,
                al.action_time,
                al.old_data,
                al.new_data,
                '' as session_id
              FROM audit_logs al
              LEFT JOIN admin_users au ON al.admin_id = au.admin_id
              WHERE al.log_id = ?";
    
    $stmt = $connect->prepare($query);
    $stmt->bind_param('i', $log_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Activity log not found');
    }
    
    $activity = $result->fetch_assoc();
    
    // Get related activities (same user, same day)
    $related_query = "SELECT 
                        al.log_id,
                        al.action,
                        al.table_name,
                        al.description,
                        al.action_time
                      FROM audit_logs al
                      WHERE al.admin_id = ? 
                      AND DATE(al.action_time) = DATE(?)
                      AND al.log_id != ?
                      ORDER BY al.action_time DESC
                      LIMIT 10";
    
    $related_stmt = $connect->prepare($related_query);
    $related_stmt->bind_param('isi', $activity['user_id'], $activity['action_time'], $log_id);
    $related_stmt->execute();
    $related_result = $related_stmt->get_result();
    
    $related_activities = [];
    while ($row = $related_result->fetch_assoc()) {
        $related_activities[] = $row;
    }
    
    $activity['related_activities'] = $related_activities;
    
    echo json_encode([
        'success' => true,
        'data' => $activity
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_activity_details.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch activity details',
        'error' => $e->getMessage()
    ]);
}
?>
