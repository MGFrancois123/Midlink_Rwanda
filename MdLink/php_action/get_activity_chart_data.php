<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

header('Content-Type: application/json');

try {
    // Get activity data for the last 30 days
    $query = "
        SELECT 
            DATE(action_time) as activity_date,
            COUNT(*) as activity_count
        FROM audit_logs 
        WHERE action_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(action_time)
        ORDER BY activity_date ASC
    ";
    
    $result = $connect->query($query);
    
    $labels = [];
    $activities = [];
    
    // Generate all dates for the last 30 days
    $start_date = date('Y-m-d', strtotime('-29 days'));
    $end_date = date('Y-m-d');
    
    $current_date = $start_date;
    while ($current_date <= $end_date) {
        $labels[] = date('M j', strtotime($current_date));
        $activities[] = 0; // Default to 0
        $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
    }
    
    // Fill in actual data
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $date_index = array_search($row['activity_date'], array_column($labels, null));
            if ($date_index !== false) {
                $activities[$date_index] = (int)$row['activity_count'];
            }
        }
    }
    
    // Get activity breakdown by type for the last 30 days
    $breakdown_query = "
        SELECT 
            action,
            COUNT(*) as count
        FROM audit_logs 
        WHERE action_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY action
        ORDER BY count DESC
    ";
    
    $breakdown_result = $connect->query($breakdown_query);
    $activity_breakdown = [];
    
    if ($breakdown_result) {
        while ($row = $breakdown_result->fetch_assoc()) {
            $activity_breakdown[] = [
                'action' => $row['action'],
                'count' => (int)$row['count']
            ];
        }
    }
    
    // Get hourly activity for today
    $hourly_query = "
        SELECT 
            HOUR(action_time) as hour,
            COUNT(*) as count
        FROM audit_logs 
        WHERE DATE(action_time) = CURDATE()
        GROUP BY HOUR(action_time)
        ORDER BY hour ASC
    ";
    
    $hourly_result = $connect->query($hourly_query);
    $hourly_data = [];
    
    // Initialize all hours with 0
    for ($i = 0; $i < 24; $i++) {
        $hourly_data[$i] = 0;
    }
    
    if ($hourly_result) {
        while ($row = $hourly_result->fetch_assoc()) {
            $hourly_data[(int)$row['hour']] = (int)$row['count'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'labels' => $labels,
            'activities' => $activities,
            'breakdown' => $activity_breakdown,
            'hourly' => array_values($hourly_data)
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_activity_chart_data.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch chart data',
        'error' => $e->getMessage()
    ]);
}
?>
