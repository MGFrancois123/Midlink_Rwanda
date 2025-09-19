<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

header('Content-Type: application/json');

try {
    // Get filter parameters
    $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
    $activity_type = isset($_GET['activity_type']) ? $_GET['activity_type'] : null;
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : null;
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : null;
    
    // Build the query
    $query = "SELECT 
                al.log_id,
                al.admin_id as user_id,
                au.username,
                'admin' as role,
                al.action,
                al.table_name,
                al.record_id,
                al.description,
                al.ip_address,
                al.user_agent,
                al.action_time,
                al.old_data,
                al.new_data
              FROM audit_logs al
              LEFT JOIN admin_users au ON al.admin_id = au.admin_id";
    
    $where_conditions = [];
    $params = [];
    $param_types = "";
    
    if ($user_id) {
        $where_conditions[] = "al.admin_id = ?";
        $params[] = $user_id;
        $param_types .= "i";
    }
    
    if ($activity_type) {
        $where_conditions[] = "al.action = ?";
        $params[] = $activity_type;
        $param_types .= "s";
    }
    
    if ($date_from) {
        $where_conditions[] = "DATE(al.action_time) >= ?";
        $params[] = $date_from;
        $param_types .= "s";
    }
    
    if ($date_to) {
        $where_conditions[] = "DATE(al.action_time) <= ?";
        $params[] = $date_to;
        $param_types .= "s";
    }
    
    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    // Add pagination parameters
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page'] - 1) : 0;
    $limit = isset($_GET['limit']) ? min(100, max(10, (int)$_GET['limit'])) : 25;
    $offset = $page * $limit;
    
    $query .= " ORDER BY al.action_time DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $param_types .= "ii";
    
    $stmt = $connect->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    // Get total count for pagination
    $count_query = "SELECT COUNT(*) as total FROM audit_logs al LEFT JOIN admin_users au ON al.admin_id = au.admin_id";
    if (!empty($where_conditions)) {
        $count_query .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    $count_stmt = $connect->prepare($count_query);
    if (!empty($params)) {
        // Remove the limit and offset parameters for count query
        $count_params = array_slice($params, 0, -2);
        $count_types = substr($param_types, 0, -2);
        if (!empty($count_params)) {
            $count_stmt->bind_param($count_types, ...$count_params);
        }
    }
    $count_stmt->execute();
    $total_result = $count_stmt->get_result()->fetch_assoc();
    $total_records = $total_result['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $activities,
        'total' => $total_records,
        'page' => $page + 1,
        'limit' => $limit,
        'total_pages' => ceil($total_records / $limit)
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_user_activity.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch user activity data',
        'error' => $e->getMessage()
    ]);
}
?>
