<?php
require_once './constant/connect.php';

echo "=== TESTING STATISTICS ===\n";

// Test the statistics query
$stats_query = "SELECT COUNT(*) as total_activities FROM audit_logs";
$result = $connect->query($stats_query);
$total = $result->fetch_assoc()['total_activities'];
echo "Total activities: $total\n";

$active_query = "SELECT COUNT(DISTINCT admin_id) as active_users FROM audit_logs WHERE action = 'LOGIN' AND action_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$result = $connect->query($active_query);
$active = $result->fetch_assoc()['active_users'];
echo "Active users (last 7 days): $active\n";

$today_query = "SELECT COUNT(*) as today_activities FROM audit_logs WHERE DATE(action_time) = CURDATE()";
$result = $connect->query($today_query);
$today = $result->fetch_assoc()['today_activities'];
echo "Today's activities: $today\n";

$logins_query = "SELECT COUNT(*) as system_logins FROM audit_logs WHERE action = 'LOGIN'";
$result = $connect->query($logins_query);
$logins = $result->fetch_assoc()['system_logins'];
echo "System logins: $logins\n";

echo "\n=== TESTING CHART DATA ===\n";

// Test chart data query
$chart_query = "SELECT DATE(action_time) as activity_date, COUNT(*) as activity_count FROM audit_logs WHERE action_time >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(action_time) ORDER BY activity_date ASC";
$result = $connect->query($chart_query);

echo "Chart data (last 30 days):\n";
while($row = $result->fetch_assoc()) {
    echo "- {$row['activity_date']}: {$row['activity_count']} activities\n";
}
?>
